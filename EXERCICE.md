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

**À vous de construire chaque prompt en RCTF complet** (Rôle / Contexte / Tâche / Format) —
c'est l'objet de l'exercice. Les specs ci-dessus vous donnent la matière ; à vous de les
formuler correctement pour Claude.

1. **Générer les entités** — construisez un prompt RCTF qui couvre :
   - Rôle : quelle stack, quel niveau d'expertise attendu ?
   - Contexte : quel projet, quel namespace, quelles conventions (strict_types, attributs
     Doctrine PHP 8, pas d'UUID Ramsey ici) ?
   - Tâche : les 3 éléments à générer (enum `CartStatus`, entités `Cart` et `CartItem`)
     selon les specs ci-dessus
   - Format : combien de fichiers, avec quoi dedans (getters, constructeur strict…) ?

2. **Générer `CartService`** — même logique RCTF, en précisant les 5 méthodes de la
   section "Service CartService" ci-dessus et la dépendance à injecter.

3. **Générer la migration** :
   ```bash
   php bin/console doctrine:migrations:diff
   ```

4. **Générer les tests unitaires** de `CartService` — construisez un prompt RCTF qui
   précise : le framework de test, la stratégie de mock pour `EntityManagerInterface`,
   les 5 méthodes à couvrir, et l'emplacement du fichier de test.
   *(Le Jour 2 vous présentera le Skill `/gen-test`, qui automatise ce même prompt.)*

**Après l'exercice** : le formateur vous montrera un exemple de prompt RCTF de référence
en debriefing — comparez-le au vôtre avant de continuer.

## Critère de succès

- `php bin/console doctrine:schema:validate` → OK
- `CartService::getTotal()` testé avec au moins 3 assertions
- PHPStan 0 erreur
