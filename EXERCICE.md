# Exercice J3 — US04 : Refuser une demande de retour

## User Story

> En tant qu'**administrateur**, je veux pouvoir **refuser une demande de retour** en indiquant un motif, afin d'informer le client des raisons du refus.

---

## Contexte

Même machine d'état que l'US03 :

```
PENDING → APPROVED  (US03)
PENDING → REJECTED  (US04, avec motif obligatoire)
```

L'entité `ReturnRequest` doit stocker le motif de refus.

---

## Ce que vous allez construire

### Modification de l'entité `ReturnRequest`

Ajoutez un champ `rejectionReason` :

```php
// src/Return/Entity/ReturnRequest.php
class ReturnRequest
{
    // ... champs existants ...

    public ?string $rejectionReason = null;
}
```

### Méthode à implémenter

```php
// src/Return/Service/ReturnAdminService.php
/**
 * @throws \InvalidArgumentException si $reason est vide
 * @throws \LogicException si la demande n'est pas en statut PENDING
 */
public function rejectReturn(ReturnRequest $request, string $reason): void;
```

---

## Règles métier

| Condition | Comportement attendu |
|-----------|----------------------|
| `$reason` vide ou blank | Lève `\InvalidArgumentException("Rejection reason cannot be empty")` |
| `status !== PENDING` | Lève `\LogicException("Cannot reject a {$request->status->value} request")` |
| `status === PENDING` + raison valide | Passe à `REJECTED`, stocke `rejectionReason`, persist |

> **Ordre de validation :** vérifiez `$reason` en premier, avant de vérifier le statut.

---

## Étapes

### 1. Modifier l'entité

Ajoutez `public ?string $rejectionReason = null;` dans `ReturnRequest`.

### 2. Écrire les tests unitaires

Fichier : `tests/Unit/Return/Service/ReturnAdminServiceRejectTest.php`

Tests à écrire :

```php
public function test_reject_with_empty_reason_throws_invalid_argument(): void
// \InvalidArgumentException avant même de vérifier le statut

public function test_reject_with_blank_reason_throws_invalid_argument(): void
// "   " (espaces) doit aussi être rejeté — utilisez trim()

public function test_reject_approved_request_throws_logic_exception(): void
// \LogicException pour APPROVED (avec une raison valide)

public function test_reject_pending_request_sets_status_and_reason(): void
// Vérifier status === REJECTED et rejectionReason === 'Produit endommagé à la livraison'
```

### 3. Lancer les tests — vérifier qu'ils échouent

```bash
php bin/phpunit tests/Unit/Return/Service/ReturnAdminServiceRejectTest.php
```

### 4. Implémenter `rejectReturn()`

```php
public function rejectReturn(ReturnRequest $request, string $reason): void
{
    if (trim($reason) === '') {
        throw new \InvalidArgumentException('Rejection reason cannot be empty');
    }
    if ($request->status !== ReturnStatus::PENDING) {
        throw new \LogicException(
            "Cannot reject a {$request->status->value} request"
        );
    }
    $request->status = ReturnStatus::REJECTED;
    $request->rejectionReason = $reason;
    $this->entityManager->flush();
}
```

### 5. Relancer tous les tests Return

```bash
php bin/phpunit tests/Unit/Return/
```

### 6. Commit

```bash
git add src/Return/ tests/Unit/Return/
git commit -m "feat: ReturnAdminService::rejectReturn — transition PENDING → REJECTED avec motif"
```

---

## Prompts Claude suggérés

```
J'ai deux règles de validation à combiner dans rejectReturn() :
1. La raison ne peut pas être vide ou blank (trim)
2. Le statut doit être PENDING, sinon LogicException

Génère les tests PHPUnit qui vérifient les 4 cas :
- raison vide → InvalidArgumentException
- raison blank → InvalidArgumentException  
- statut non-PENDING + raison valide → LogicException
- statut PENDING + raison valide → REJECTED + rejectionReason stocké
```

```
Voici mes 4 tests qui échouent. Implémente rejectReturn() 
dans ReturnAdminService. Respecte l'ordre de validation :
raison d'abord, statut ensuite.
[coller les tests]
```

---

## Critères de succès

- [ ] `rejectionReason` ajouté à l'entité `ReturnRequest`
- [ ] 4 tests écrits avant l'implémentation
- [ ] Validation `trim()` sur la raison
- [ ] Ordre de validation respecté (raison avant statut)
- [ ] `status` et `rejectionReason` mis à jour ensemble
- [ ] Tous les tests Unit/Return/ passent (non-régression)

---

## Notion clé : Ordre de validation

L'ordre dans lequel vous validez les préconditions a de l'importance pour l'expérience
utilisateur : échouer vite sur les erreurs de saisie (raison vide) avant de vérifier
l'état métier (statut) réduit les allers-retours pour l'appelant.
