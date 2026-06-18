# Exercice J3 — US05 : Émettre un remboursement automatique

## User Story

> En tant que **système**, je veux **déclencher automatiquement un remboursement** lorsqu'une demande de retour est approuvée, afin de rembourser le client sans intervention manuelle.

---

## Contexte

La chaîne des états jusqu'ici :

```
PENDING → APPROVED → REFUNDED
             ↑
         (US03 — vous avez implémenté ça)
             ↓
         refundReturn() → REFUNDED  (cette US)
```

Un remboursement correspond au montant total de la commande originale.

---

## Ce que vous allez construire

### Interface du gateway de paiement

```php
// src/Payment/Gateway/PaymentGatewayInterface.php
interface PaymentGatewayInterface
{
    /**
     * Émet un remboursement pour le montant donné.
     * @throws \RuntimeException si le gateway est indisponible
     */
    public function refund(string $transactionId, int $amountCents, string $currency): void;
}
```

### Méthode à implémenter

```php
// src/Return/Service/ReturnAdminService.php
/**
 * @throws \LogicException si la demande n'est pas en statut APPROVED
 * @throws \RuntimeException si le gateway de paiement échoue (re-propagé)
 */
public function refundReturn(ReturnRequest $request): void;
```

La méthode doit :
1. Vérifier que `status === APPROVED`
2. Appeler le gateway avec `transactionId`, `amountCents` et `currency` depuis la commande
3. Passer le statut à `REFUNDED`
4. Persister

---

## Données de la commande

Pour cet exercice, supposez que `Order` expose :

```php
$request->order->paymentTransactionId; // string, ex: "txn_abc123"
$request->order->totalAmountCents;     // int, ex: 15000 (= 150,00 €)
$request->order->currency;             // string, ex: "EUR"
```

---

## Étapes

### 1. Créer l'interface `PaymentGatewayInterface`

Fichier : `src/Payment/Gateway/PaymentGatewayInterface.php`

### 2. Écrire les tests unitaires

Fichier : `tests/Unit/Return/Service/ReturnAdminServiceRefundTest.php`

Tests à écrire :

```php
public function test_refund_non_approved_request_throws_logic_exception(): void
// PENDING → LogicException

public function test_refund_approved_request_calls_gateway_and_sets_refunded(): void
// Vérifie que refund() est appelé avec les bons arguments
// Vérifie que status === REFUNDED après l'appel

public function test_refund_propagates_gateway_exception(): void
// Le gateway lève RuntimeException → elle remonte sans modification
// Vérifier que status n'a PAS changé (toujours APPROVED)
```

### 3. Lancer les tests — vérifier qu'ils échouent

```bash
php bin/phpunit tests/Unit/Return/Service/ReturnAdminServiceRefundTest.php
```

### 4. Injecter `PaymentGatewayInterface` dans `ReturnAdminService`

```php
public function __construct(
    private ReturnRequestRepositoryInterface $repository,
    private EntityManagerInterface $entityManager,
    private PaymentGatewayInterface $paymentGateway,
) {}
```

### 5. Implémenter `refundReturn()`

```php
public function refundReturn(ReturnRequest $request): void
{
    if ($request->status !== ReturnStatus::APPROVED) {
        throw new \LogicException(
            "Cannot refund a {$request->status->value} request"
        );
    }
    $this->paymentGateway->refund(
        $request->order->paymentTransactionId,
        $request->order->totalAmountCents,
        $request->order->currency,
    );
    $request->status = ReturnStatus::REFUNDED;
    $this->entityManager->flush();
}
```

> **Attention :** N'attrapez PAS l'exception du gateway — laissez-la remonter.
> Si le gateway échoue, le statut doit rester `APPROVED` (pas de flush).

### 6. Relancer tous les tests Return

```bash
php bin/phpunit tests/Unit/Return/
```

### 7. Commit

```bash
git add src/ tests/Unit/Return/
git commit -m "feat: ReturnAdminService::refundReturn — appel gateway + transition APPROVED → REFUNDED"
```

---

## Prompts Claude suggérés

```
J'ai cette interface gateway :

interface PaymentGatewayInterface {
    public function refund(string $transactionId, int $amountCents, string $currency): void;
}

J'ai besoin de tester refundReturn() qui :
1. Vérifie status === APPROVED sinon LogicException
2. Appelle $gateway->refund() avec les données de la commande
3. Passe status à REFUNDED et flush

Génère 3 tests PHPUnit avec createMock(PaymentGatewayInterface::class).
Le test de gateway qui échoue doit vérifier que le status reste APPROVED.
```

```
Mon test de non-régression échoue après l'ajout du PaymentGateway
dans le constructeur. Comment adapter les tests existants 
qui ne passaient pas de gateway ?
[coller l'erreur]
```

---

## Critères de succès

- [ ] `PaymentGatewayInterface` créée avec la bonne signature
- [ ] 3 tests écrits avant l'implémentation
- [ ] Gateway appelé avec les bons arguments (`transactionId`, `amountCents`, `currency`)
- [ ] Exception du gateway re-propagée sans modifier le statut
- [ ] Tous les tests Unit/Return/ passent

---

## Notion clé : Transaction atomique

Le `flush()` doit être appelé **après** le gateway, jamais avant.
Si le gateway échoue, la base de données ne doit pas changer.
C'est le principe du "don't commit until you're sure it worked".
