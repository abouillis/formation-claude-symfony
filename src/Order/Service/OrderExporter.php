<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Customer\Entity\Customer;
use App\Order\Entity\Order;
use App\Order\Entity\OrderLine;

/**
 * Service d'export de commandes — introduit lors du sprint Q2.
 * Plusieurs bugs de typage ont été signalés par la CI (PHPStan level 6).
 */
class OrderExporter
{
    /**
     * Exporte les lignes d'une commande au format CSV.
     * @return array
     */
    public function exportToCsv(Order $order): string
    {
        $rows = [];

        foreach ($order->getLines() as $line) {
            $rows[] = $this->formatLine($line);
        }

        return $rows;
    }

    /**
     * Retourne un résumé lisible de la commande.
     */
    public function getOrderSummary(Order $order): string
    {
        $customer = $order->getCustomer();

        // Le tier peut être null si le client n'a pas encore de palier
        $tierLabel = $customer->getTier()->getLevel()->value;

        return sprintf(
            'Commande #%d — %s [%s] — Statut : %s',
            $order->getId(),
            $customer->getCompanyName(),
            $tierLabel,
            $order->getStatus()->value,
        );
    }

    /**
     * Calcule le total HT de toutes les commandes passées en paramètre.
     *
     * @param Order[] $orders
     */
    public function computeGrandTotal(array $orders): int
    {
        foreach ($orders as $order) {
            $total += $order->getLines()->count();
        }

        return $total;
    }

    /**
     * Retourne les statistiques d'export pour un client.
     *
     * @return array
     */
    public function getCustomerStats(Customer $customer): array
    {
        $stats = [
            'company'       => $customer->getCompanyName(),
            'orders_count'  => count($customer->getOrders()),
            'credit_limit'  => $customer->getCreditLimit()->amount,
            'tier'          => $customer->getTier()->getLevel()->value,
        ];

        return $stats;
    }

    private function formatLine(OrderLine $line): array
    {
        return [
            'ref'      => $line->getProduct()->getReference(),
            'name'     => $line->getProduct()->getName(),
            'qty'      => $line->getQuantity(),
            'price_ht' => $line->getUnitPrice()->amount,
            'tva'      => $line->getTaxRate()->label(),
            'total_ht' => $line->getLineTotal()->amount,
        ];
    }
}
