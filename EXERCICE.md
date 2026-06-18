# Exercice J3 — US02 : Lister les demandes de retour (vue admin)

## User Story

> En tant qu'**administrateur**, je veux pouvoir **lister toutes les demandes de retour en attente** afin de les traiter dans les meilleurs délais.

---

## Contexte

Vous avez accès à l'entité `ReturnRequest` créée en US01 (déjà commitée sur `exercice/j3-us01`).

```php
// src/Return/Entity/ReturnRequest.php  (disponible sur j3-us01)
class ReturnRequest {
    public int $id;
    public Order $order;
    public string $reason;
    public ReturnStatus $status;  // PENDING | APPROVED | REJECTED | REFUNDED
    public \DateTimeImmutable $createdAt;
}
```

---

## Ce que vous allez construire

Un **repository** Doctrine et un **service de listing** qui filtrent et trient les demandes en attente.

### Interface attendue

```php
// src/Return/Repository/ReturnRequestRepository.php
interface ReturnRequestRepositoryInterface
{
    /** @return ReturnRequest[] */
    public function findPending(): array;

    /** @return ReturnRequest[] */
    public function findByStatus(ReturnStatus $status): array;
}
```

```php
// src/Return/Service/ReturnAdminService.php
class ReturnAdminService
{
    public function __construct(
        private ReturnRequestRepositoryInterface $repository
    ) {}

    /**
     * Retourne les demandes en attente, triées par date croissante.
     * @return ReturnRequest[]
     */
    public function getPendingRequests(): array;
}
```

---

## Règles métier

- Seules les demandes avec `status = PENDING` sont remontées
- Tri par `createdAt ASC` (les plus anciennes en premier, priorité de traitement)
- Si aucune demande en attente : retourner un tableau vide (ne pas lever d'exception)

---

## Étapes

### 1. Créer l'interface du repository

Fichier : `src/Return/Repository/ReturnRequestRepositoryInterface.php`

Déclarez les deux méthodes `findPending()` et `findByStatus()`.

### 2. Écrire les tests unitaires (avec mock)

Fichier : `tests/Unit/Return/Service/ReturnAdminServiceTest.php`

Tests à écrire (la classe `ReturnAdminService` n'existe pas encore — TDD) :

```php
public function test_getPendingRequests_returns_empty_when_none_pending(): void
public function test_getPendingRequests_returns_only_pending_requests(): void
public function test_getPendingRequests_are_sorted_by_date_ascending(): void
```

Utilisez `$this->createMock(ReturnRequestRepositoryInterface::class)`.

### 3. Lancer les tests — vérifier qu'ils échouent

```bash
php bin/phpunit tests/Unit/Return/Service/ReturnAdminServiceTest.php
```

Attendu : `Error: Class "App\Return\Service\ReturnAdminService" not found`

### 4. Implémenter `ReturnAdminService`

Fichier : `src/Return/Service/ReturnAdminService.php`

La méthode `getPendingRequests()` doit :
1. Appeler `$this->repository->findPending()`
2. Trier par `createdAt` ASC
3. Retourner le tableau résultant

### 5. Relancer les tests — vérifier qu'ils passent

```bash
php bin/phpunit tests/Unit/Return/Service/ReturnAdminServiceTest.php
```

### 6. Implémenter le repository Doctrine

Fichier : `src/Return/Repository/ReturnRequestRepository.php`

```php
class ReturnRequestRepository extends ServiceEntityRepository
    implements ReturnRequestRepositoryInterface
{
    public function findPending(): array
    {
        return $this->findByStatus(ReturnStatus::PENDING);
    }

    public function findByStatus(ReturnStatus $status): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
```

### 7. Commit

```bash
git add src/Return/ tests/Unit/Return/
git commit -m "feat: ReturnAdminService — listing des demandes en attente"
```

---

## Prompts Claude suggérés

```
J'ai cette interface :

interface ReturnRequestRepositoryInterface {
    public function findPending(): array;
    public function findByStatus(ReturnStatus $status): array;
}

Génère les tests unitaires pour ReturnAdminService::getPendingRequests() 
en mockant le repository. Couvre : liste vide, filtrage PENDING seulement, 
tri par date ASC.
```

```
Voici mes tests qui échouent. Implémente ReturnAdminService pour les faire passer.
Le service doit déléguer au repository et trier par createdAt ASC.
[coller le contenu du fichier de test]
```

---

## Critères de succès

- [ ] `ReturnRequestRepositoryInterface` déclarée avec les 2 méthodes
- [ ] 3 tests unitaires écrits **avant** l'implémentation
- [ ] Tous les tests passent
- [ ] Tri ASC vérifié par un test dédié
- [ ] Repository Doctrine implémente l'interface

---

## Notion clé : Interface + Mock = testabilité

En injectant une **interface** dans le service (et non la classe concrète Doctrine),
vous pouvez tester `ReturnAdminService` sans base de données.
C'est le principe du **Dependency Inversion** (le D de SOLID).
