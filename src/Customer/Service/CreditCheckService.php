<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Shared\ValueObject\Money;

/**
 * @todo EXERCICE J2 : Implémenter la vérification de limite de crédit
 *
 * Ce service doit vérifier qu'un client B2B a suffisamment de crédit disponible
 * avant qu'une commande soit confirmée.
 *
 * Règles métier à implémenter :
 * - Un client avec statut Suspended ou Blacklisted est toujours refusé
 * - Un client avec creditLimit = 0 est considéré comme "sans limite" (toujours OK)
 * - Sinon, le total de la commande ne doit pas dépasser creditLimit
 */
class CreditCheckService
{
    /**
     * Vérifie si le client peut passer une commande du montant donné.
     *
     * @throws \LogicException si les devises sont incompatibles
     */
    public function canPlaceOrder(Customer $customer, Money $orderTotal): bool
    {
        // TODO : implémenter
        throw new \RuntimeException('Non implémenté — exercice J2');
    }

    /**
     * Retourne le crédit disponible restant pour le client.
     * Retourne null si le client n'a pas de limite de crédit.
     */
    public function availableCredit(Customer $customer): ?Money
    {
        // TODO : implémenter
        throw new \RuntimeException('Non implémenté — exercice J2');
    }
}
