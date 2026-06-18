# Exercice J3 — US03 : Approuver une demande de retour

## User Story

> En tant qu'**administrateur**, je veux pouvoir **approuver une demande de retour** afin d'autoriser le client à renvoyer sa commande.

---

## Contexte

Rappel des états possibles :

```php
enum ReturnStatus: string {
    case PENDING   = 'pending';
    case APPROVED  = 'approved';
    case REJECTED  = 'rejected';
    case REFUNDED  = 'refunded';
}
```

Une demande ne peut être approuvée que si elle est en statut `PENDING`.

---

## Ce que vous allez construire

Une méthode `approveReturn()` dans `ReturnAdminService`.

### Signature attendue

```php
// src/Return/Service/ReturnAdminService.php
class ReturnAdminService
{
    /**
     * @throws \LogicException si la demande n'est pas en statut PENDING
     */
    public function approveReturn(ReturnRequest $request): void;
}
```

---

## Règles métier

| Condition | Comportement attendu |
|-----------|----------------------|
| `status === PENDING` | Passe à `APPROVED`, persist en base |
| `status !== PENDING` | Lève `\LogicException("Cannot approve a {$request->status->value} request")` |

---

## Étapes

### 1. Écrire les tests unitaires en premier (TDD)

Fichier : `tests/Unit/Return/Service/ReturnAdminServiceApproveTest.php`

Tests à écrire :

```php
public function test_approve_pending_request_sets_status_approved(): void
// Vérifier que $request->status === ReturnStatus::APPROVED après l'appel

public function test_approve_approved_request_throws_logic_exception(): void
// Vérifier que \LogicException est levée avec le bon message

public function test_approve_rejected_request_throws_logic_exception(): void
// Même chose pour REJECTED

public function test_approve_refunded_request_throws_logic_exception(): void
// Même chose pour REFUNDED
```

Utilisez `$this->expectException(\LogicException::class)` et
`$this->expectExceptionMessageMatches('/Cannot approve/')`.

### 2. Lancer les tests — vérifier qu'ils échouent

```bash
php bin/phpunit tests/Unit/Return/Service/ReturnAdminServiceApproveTest.php
```

### 3. Implémenter `approveReturn()` dans `ReturnAdminService`

```php
public function approveReturn(ReturnRequest $request): void
{
    if ($request->status !== ReturnStatus::PENDING) {
        throw new \LogicException(
            "Cannot approve a {$request->status->value} request"
        );
    }
    $request->status = ReturnStatus::APPROVED;
    $this->entityManager->flush();
}
```

> **Note :** Vous devrez injecter `EntityManagerInterface` dans le constructeur
> en plus du repository.

### 4. Relancer les tests

```bash
php bin/phpunit tests/Unit/Return/Service/ReturnAdminServiceApproveTest.php
```

Tous les tests doivent passer en vert.

### 5. Relancer la suite complète

```bash
php bin/phpunit tests/Unit/Return/
```

Vérifiez que les tests US02 passent toujours (non-régression).

### 6. Commit

```bash
git add src/Return/Service/ReturnAdminService.php tests/Unit/Return/
git commit -m "feat: ReturnAdminService::approveReturn — transition PENDING → APPROVED"
```

---

## Prompts Claude suggérés

```
J'ai cette règle métier :
- approveReturn() ne fonctionne que si status === PENDING
- sinon LogicException avec le message "Cannot approve a {status} request"

Génère les 4 tests PHPUnit qui couvrent toutes les transitions invalides
et le cas nominal. Utilise des objets ReturnRequest créés manuellement
(pas de mock pour l'entité).
```

```
Voici mes tests qui échouent :
[coller les tests]

Implémente approveReturn() dans ReturnAdminService. 
Injecte EntityManagerInterface pour le flush.
```

---

## Critères de succès

- [ ] 4 tests écrits avant l'implémentation
- [ ] Tous les tests passent
- [ ] `\LogicException` levée pour tout statut ≠ PENDING
- [ ] `flush()` appelé après le changement de statut
- [ ] Tests US02 toujours verts (non-régression)

---

## Notion clé : State Machine légère

Contrôler les transitions d'état directement dans le service
(plutôt que dans l'entité) est une approche pragmatique pour des workflows simples.
Pour des workflows complexes, on utilise le composant `symfony/workflow`.
