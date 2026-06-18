# US01 — Initier une demande de retour

## User Story

> En tant que **client**, je veux pouvoir initier une demande de retour sur une commande livrée,
> afin d'obtenir un remboursement en cas de problème.

## Critères d'acceptance

- Une demande ne peut être initiée que sur une commande avec statut `delivered`
- Le client doit fournir une raison (min 10 caractères)
- La demande est créée avec statut `pending`
- Une commande ne peut avoir qu'une seule demande de retour en cours

## À implémenter

1. **Enum** `App\Return\Enum\ReturnStatus` : `pending`, `approved`, `rejected`
2. **Entity** `App\Return\Entity\ReturnRequest` :
   - id, order (ManyToOne), reason (text), status, requestedAt, resolvedAt (nullable)
3. **Service** `App\Return\Service\ReturnService::initiateReturn(Order $order, string $reason): ReturnRequest`
   - Valide statut commande (doit être `delivered`)
   - Valide longueur de la raison (>= 10 chars)
   - Crée et persiste ReturnRequest
4. **Migration** Doctrine

## Prompts suggérés

Utilisez `/analyze-feature` puis `/gen-code` avec le contexte FluxCommerce.

## Tests à écrire

```php
testCanInitiateReturnOnDeliveredOrder()
testCannotInitiateReturnOnNonDeliveredOrder()  // expects exception
testReasonTooShortThrows()                      // expects exception
```
