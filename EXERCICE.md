# Exercice — Implémenter CreditCheckService avec TDD assisté

## Contexte

FluxCommerce est un ERP B2B. Chaque client a une **limite de crédit** (`creditLimit`).
Avant de confirmer une commande, le système doit vérifier que le montant ne dépasse pas
cette limite.

Le service `CreditCheckService` est créé mais non implémenté (lève `RuntimeException`).
**10 tests sont déjà écrits et échouent** — votre mission est de les faire passer.

## Règles métier

| Condition | Résultat `canPlaceOrder` |
|---|---|
| Client `Suspended` ou `Blacklisted` | `false` toujours |
| `creditLimit = 0` (sans limite) | `true` toujours |
| `orderTotal <= creditLimit` | `true` |
| `orderTotal > creditLimit` | `false` |
| Devises incompatibles | `throw LogicException` |

## Étapes suggérées

1. Lancer les tests pour voir les 10 échecs :
   ```bash
   php bin/phpunit tests/Unit/Customer/Service/CreditCheckServiceTest.php
   ```

2. Utiliser `/gen-code` pour demander à Claude d'implémenter le service :
   ```
   Rôle : Dev Symfony 7 senior, ERP B2B FluxCommerce.
   Contexte : CreditCheckService stub dans src/Customer/Service/.
   Tâche : Implémenter canPlaceOrder() et availableCredit() selon les règles métier
           documentées dans le stub. Les tests sont dans tests/Unit/Customer/Service/.
   Format : Code PHP 8.3 strict_types, readonly si possible, exceptions typées.
   ```

3. Vérifier que les 10 tests passent + PHPStan 0 erreur

## Fichiers concernés

- `src/Customer/Service/CreditCheckService.php` — à implémenter
- `tests/Unit/Customer/Service/CreditCheckServiceTest.php` — tests déjà écrits

## Critère de succès

```
OK (10 tests, 10 assertions)
[OK] No errors  ← PHPStan
```
