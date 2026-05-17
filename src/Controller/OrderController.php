<?php

declare(strict_types=1);

namespace App\Controller;

use App\Billing\Entity\Invoice;
use App\Order\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @todo EXERCICE REFACTORING : Ce controller contient de la logique métier
 * À extraire dans des services dédiés (OrderCreator, InvoiceGenerator, etc.)
 */
#[Route('/orders')]
class OrderController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    #[Route('/', name: 'order_list')]
    public function list(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findAll();
        return $this->json(['orders' => array_map(fn($o) => ['id' => $o->getId(), 'status' => $o->getStatus()], $orders)]);
    }

    #[Route('/{id}/confirm', name: 'order_confirm', methods: ['POST'])]
    public function confirm(int $id, Request $request): Response
    {
        $order = $this->em->find(Order::class, $id);

        if (null === $order) {
            return $this->json(['error' => 'Commande introuvable'], 404);
        }

        // LOGIQUE MÉTIER DANS LE CONTROLLER — legacy intentionnel
        if ($order->getStatus()->value !== 'pending') {
            return $this->json(['error' => 'Seules les commandes en attente peuvent être confirmées'], 400);
        }

        $order->confirm();

        // Génération facture inline — devrait être dans InvoiceGenerator
        $invoiceNumber = 'FC-'.date('Y').'-'.str_pad((string) rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $dueAt = new \DateTimeImmutable('+30 days');

        // BUG INTENTIONNEL : Address détachée (exercice debugging)
        $invoice = new Invoice($order, $invoiceNumber, $dueAt);
        $this->em->persist($invoice);
        $this->em->flush();

        return $this->json(['message' => 'Commande confirmée', 'invoice' => $invoiceNumber]);
    }
}
