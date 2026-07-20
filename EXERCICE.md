# Exercice — Générer Entity Cart + CartService (Jour 1, Module 3)

## Contexte

FluxCommerce doit gérer un **panier** avant qu'une commande soit créée.
Le panier est temporaire : un client y ajoute des produits, puis valide en commande.

Cette feature n'existe pas encore dans le projet — vous allez la générer avec Claude.

## Specs fonctionnelles

### Entity `Cart`
| Propriété | Type | Contrainte |
|---|---|---|
| id | int | PK auto |
| customer | Customer | ManyToOne, NOT NULL |
| status | CartStatus (enum) | `open` / `checked_out` / `abandoned` |
| createdAt | DateTimeImmutable | set au construct |
| checkedOutAt | DateTimeImmutable | nullable |
| items | Collection\<CartItem\> | OneToMany, cascade persist/remove |

### Entity `CartItem`
| Propriété | Type | Contrainte |
|---|---|---|
| id | int | PK auto |
| cart | Cart | ManyToOne, NOT NULL |
| product | Product | ManyToOne, NOT NULL |
| quantity | int | >= 1 |
| unitPriceAmount | int | snapshot du prix au moment d'ajout |
| unitPriceCurrency | string(3) | snapshot |

### Service `CartService`
```php
addItem(Cart $cart, Product $product, int $quantity): CartItem
removeItem(Cart $cart, CartItem $item): void
getTotal(Cart $cart): Money       // somme unitPrice * qty de tous les items
clear(Cart $cart): void           // vide les items
checkout(Cart $cart): Order       // crée une Order à partir du panier, passe status à checked_out
```

## Étapes suggérées

1. Générer les entités avec Claude (prompt RCTF) :
   ```
   Rôle : Dev Symfony 7.2 / PHP 8.3 / Doctrine ORM 3 senior, ERP B2B.
   Contexte : Projet FluxCommerce — namespace App\Order, conventions UUID Ramsey non utilisées ici
              (auto-increment classique), declare(strict_types=1), attributs Doctrine PHP 8.
   Tâche : Génère CartStatus (enum string), Entity Cart et Entity CartItem
           selon les specs dans EXERCICE.md. Namespace App\Order\{Entity,Enum}.
   Format : 3 fichiers PHP complets avec tous les getters + constructeur strict.
   ```

2. Générer `CartService` :
   ```
   [même rôle/contexte]
   Tâche : Génère CartService (App\Order\Service\CartService) avec les 5 méthodes
           définies dans EXERCICE.md. La méthode checkout() crée une Order Doctrine.
   Format : Service PHP injectant EntityManagerInterface, sans setters superflus.
   ```

3. Générer la migration :
   ```bash
   php bin/console doctrine:migrations:diff
   ```

4. Générer les tests unitaires de `CartService` (prompt RCTF) :
   ```
   Rôle : Dev Symfony 7.2 / PHP 8.3 / PHPUnit 11 senior, TDD.
   Contexte : CartService (App\Order\Service\CartService) vient d'être généré,
              voir son code ci-dessus/dans le fichier.
   Tâche : Génère les tests unitaires pour les 5 méthodes de CartService
           (addItem, removeItem, getTotal, clear, checkout).
           Mock EntityManagerInterface.
   Format : tests/Unit/Order/Service/CartServiceTest.php, use statements inclus.
   ```
   *(Le Jour 2 vous présentera le Skill `/gen-test`, qui automatise ce même prompt.)*

## Critère de succès

- `php bin/console doctrine:schema:validate` → OK
- `CartService::getTotal()` testé avec au moins 3 assertions
- PHPStan 0 erreur
