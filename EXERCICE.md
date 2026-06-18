# Exercice — Refactoring avec Strategy Pattern assisté par Claude

## Contexte

Le `DiscountResolver` actuel utilise un `switch` sur le niveau de fidélité client.
À chaque nouveau palier, il faut modifier cette classe — violation du principe Open/Closed.

Le ticket demande de refactoriser en **Strategy Pattern** pour rendre l'ajout de paliers
extensible sans modifier le code existant.

## Objectif

Remplacer le `switch` dans `DiscountResolver` par un Strategy Pattern :

```
DiscountStrategyInterface
├── BronzeDiscountStrategy   (0%)
├── SilverDiscountStrategy   (3%)
├── GoldDiscountStrategy     (7%)
└── PlatinumDiscountStrategy (12%)
```

`DiscountResolver` injecte un `iterable` de stratégies et délègue.

## Étapes suggérées

1. Analyser le code existant avec Claude (`/analyze-feature`)
2. Générer `DiscountStrategyInterface` + les 4 implémentations (`/gen-code`)
3. Réécrire `DiscountResolver` pour injecter et itérer les stratégies
4. Générer les tests unitaires (`/gen-test`)
5. Vérifier PHPStan 0 erreur + tests verts

## Critère de succès

- `DiscountResolver` ne contient plus de `switch` ni de `if/elseif` sur le tier
- Ajouter un palier = créer une nouvelle classe, sans toucher à `DiscountResolver`
- PHPStan level 6 : 0 erreur
- Tests unitaires : chaque stratégie testée isolément
