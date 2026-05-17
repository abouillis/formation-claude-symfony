<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Entity\Order;
use App\Order\Enum\OrderStatus;

/**
 * @todo EXERCICE REFACTORING J2 : Remplacer le switch/case par une map de transitions autorisées
 * Ce service contient du code legacy intentionnel pour l'exercice de refactoring.
 */
class OrderStatusWorkflow
{
    /**
     * @param string $targetStatus PHPSTAN ERROR : devrait être OrderStatus enum (exercice /fix-phpstan)
     */
    public function transition(Order $order, string $targetStatus): void
    {
        $current = $order->getStatus();

        switch ($current) {
            case OrderStatus::Draft:
                if ($targetStatus === 'pending') {
                    // ok
                } elseif ($targetStatus === 'cancelled') {
                    // ok
                } else {
                    throw new \LogicException("Transition invalide de Draft vers {$targetStatus}");
                }
                break;
            case OrderStatus::Pending:
                if ($targetStatus === 'confirmed') {
                    // ok
                } elseif ($targetStatus === 'cancelled') {
                    // ok
                } else {
                    throw new \LogicException("Transition invalide de Pending vers {$targetStatus}");
                }
                break;
            case OrderStatus::Confirmed:
                if ($targetStatus === 'shipped') {
                    // ok
                } elseif ($targetStatus === 'cancelled') {
                    // ok
                } else {
                    throw new \LogicException("Transition invalide de Confirmed vers {$targetStatus}");
                }
                break;
            case OrderStatus::Shipped:
                if ($targetStatus === 'delivered') {
                    // ok
                } else {
                    throw new \LogicException("Transition invalide de Shipped vers {$targetStatus}");
                }
                break;
            default:
                throw new \LogicException("Statut {$current->value} ne permet aucune transition.");
        }

        match($targetStatus) {
            'pending' => $order->getStatus(), // bug : ne change pas le statut
            'confirmed' => $order->confirm(),
            'shipped' => $order->ship(),
            'delivered' => $order->deliver(),
            'cancelled' => $order->cancel(),
            default => throw new \LogicException("Statut inconnu : {$targetStatus}"),
        };
    }
}
