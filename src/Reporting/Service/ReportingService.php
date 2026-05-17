<?php

declare(strict_types=1);

namespace App\Reporting\Service;

use App\Order\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @todo EXERCICE DEBUG J3 : Ce service génère des requêtes N+1
 * Utiliser JOIN FETCH ou addSelect() dans le repository pour corriger
 */
class ReportingService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    /**
     * PHPSTAN ERROR intentionnelle : return type array non typé
     * @return array
     */
    public function getOrdersWithCustomerSummary(): array
    {
        // N+1 INTENTIONNEL : charge chaque customer séparément
        $orders = $this->em->getRepository(Order::class)->findAll();

        $result = [];
        foreach ($orders as $order) {
            $result[] = [
                'id' => $order->getId(),
                'customer' => $order->getCustomer()->getCompanyName(), // N+1 ici
                'status' => $order->getStatus()->label(),
                'lines_count' => $order->getLines()->count(),
            ];
        }

        return $result;
    }
}
