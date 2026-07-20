# Corrigé formateur — Exercice Cart + CartService (Jour 1, Module 3)

> ⚠️ **Réservé au formateur.** Ce fichier contient les prompts RCTF de référence.
> Ne pas distribuer avant l'exercice — l'objectif est que les participants
> construisent leur propre prompt à partir des specs dans `EXERCICE.md`.
> À montrer uniquement lors du debriefing (voir script formateur, Module 3,
> "Exercice — Entity Cart + CartService", 22 min → 7 min de debriefing).

## Prompt 1 — Entités (`CartStatus`, `Cart`, `CartItem`)

```
Rôle : Tu es développeur Symfony 7.2 / PHP 8.3 / Doctrine ORM 3 senior,
spécialisé ERP B2B.

Contexte : Projet FluxCommerce, namespace App\Order. Conventions du projet :
declare(strict_types=1) en tête de chaque fichier, attributs Doctrine natifs
PHP 8 (pas d'annotations), pas d'UUID Ramsey ici — auto-increment classique
sur les id. Les entités du projet utilisent des constructeurs stricts
(propriétés obligatoires passées en paramètre, pas de setters superflus).

Tâche : Génère 3 fichiers PHP :
1. Enum CartStatus (string backed) avec les valeurs open, checked_out, abandoned
2. Entity Cart avec :
   - id (PK auto)
   - customer: Customer (ManyToOne, NOT NULL)
   - status: CartStatus (défaut: open)
   - createdAt: DateTimeImmutable (initialisé dans le constructeur)
   - checkedOutAt: DateTimeImmutable nullable
   - items: Collection<CartItem> (OneToMany, mappedBy: cart, cascade: persist+remove,
     initialisée en ArrayCollection dans le constructeur)
3. Entity CartItem avec :
   - id (PK auto)
   - cart: Cart (ManyToOne, NOT NULL, inversedBy: items)
   - product: Product (ManyToOne, NOT NULL)
   - quantity: int (>= 1)
   - unitPriceAmount: int (snapshot du prix au moment de l'ajout, en centimes)
   - unitPriceCurrency: string(3) (snapshot, ex: EUR)

Format : namespace App\Order\Entity pour Cart/CartItem, App\Order\Enum pour
CartStatus. Chaque entité avec tous les getters, sans setters sauf si
nécessaire à la logique métier. Attributs Doctrine complets (#[ORM\Entity],
#[ORM\Table], #[ORM\Column]...).
```

## Prompt 2 — `CartService`

```
Rôle : Tu es développeur Symfony 7.2 / PHP 8.3 / Doctrine ORM 3 senior,
spécialisé ERP B2B.

Contexte : Projet FluxCommerce, namespace App\Order. Les entités Cart,
CartItem et l'enum CartStatus existent déjà (voir ci-dessus). Le service
doit persister ses changements via Doctrine.

Tâche : Génère CartService avec les 5 méthodes suivantes :
- addItem(Cart $cart, Product $product, int $quantity): CartItem
  → crée un CartItem avec un snapshot du prix courant du Product,
    l'ajoute à $cart->items, persist + flush
- removeItem(Cart $cart, CartItem $item): void
  → retire l'item de la collection, remove + flush
- getTotal(Cart $cart): Money
  → somme (unitPriceAmount * quantity) de tous les items, retourne un Money
- clear(Cart $cart): void
  → vide la collection items, flush
- checkout(Cart $cart): Order
  → crée une Order Doctrine à partir du panier, passe $cart->status à
    checked_out, fixe checkedOutAt à maintenant, flush

Format : Service PHP dans App\Order\Service\CartService, injectant
EntityManagerInterface via constructeur (readonly property), sans setters
superflus. PHPStan niveau 6 strict (types explicites partout).
```

## Prompt 3 — Tests unitaires de `CartService`

```
Rôle : Tu es développeur Symfony 7.2 / PHP 8.3 / PHPUnit 11 senior, TDD.

Contexte : CartService (App\Order\Service\CartService) vient d'être généré
(voir code ci-dessus). Il dépend de EntityManagerInterface, injecté par
constructeur.

Tâche : Génère les tests unitaires pour les 5 méthodes de CartService :
- addItem : vérifie que persist() et flush() sont appelés, que le CartItem
  retourné a le bon snapshot de prix
- removeItem : vérifie que remove() et flush() sont appelés, que l'item
  n'est plus dans la collection
- getTotal : au moins 3 assertions — panier vide (0), 1 item, plusieurs items
- clear : vérifie que la collection est vide après appel, flush() appelé
- checkout : vérifie que le status passe à checked_out, que checkedOutAt
  est renseigné, qu'une Order est bien créée et retournée

Format : tests/Unit/Order/Service/CartServiceTest.php. Utilise
createMock(EntityManagerInterface::class). Inclus tous les use statements.
```

## Étape 3 — Migration

Pas de prompt nécessaire, commande CLI directe :

```bash
php bin/console doctrine:migrations:diff
```

## Points de vigilance pendant le debriefing

- **Relation OneToMany mal configurée** (pattern d'erreur fréquent cité dans le
  script) : `mappedBy` / `inversedBy` oubliés, ou `ArrayCollection` non
  initialisée dans le constructeur.
- Comparer le prompt du participant au prompt de référence : a-t-il précisé
  le namespace ? Les conventions du projet (strict_types, pas d'UUID) ? Le
  format de sortie attendu ?
- Si le groupe a écrit des prompts très courts (« génère Cart et CartItem »),
  faire le lien avec le Module 2 (les 7 règles d'or, RCTF) : plus le contexte
  est précis, moins il y a de corrections à faire après coup.
