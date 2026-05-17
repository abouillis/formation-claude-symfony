# FluxCommerce — CLAUDE.md

## Stack technique
- PHP 8.2 / Symfony 7.4 / Doctrine ORM / PostgreSQL
- PHP CS Fixer (règles: @Symfony) — config dans .php-cs-fixer.php
- PHPStan niveau 6 — config dans phpstan.neon
- Tests : PHPUnit 11 (unit + functional WebTestCase)

## Modules
- Customer : clients B2B, adresses, tiers (src/Customer/)
- Catalog : produits, catégories (src/Catalog/)
- Order : commandes, lignes, workflow statuts (src/Order/)
- Billing : factures, paiements (src/Billing/)
- Shared : ValueObjects partagés (src/Shared/)
- Reporting : agrégats (src/Reporting/)

## Features PHP 8 à utiliser
- Enums backed (string ou int) pour tous les statuts et types
- readonly classes pour les Value Objects
- Named arguments pour les constructeurs complexes
- Match expressions (jamais de switch)
- Attributes Symfony (#[Route], #[ORM\Entity], etc.)
- Constructor property promotion

## Conventions de nommage
- Services : pas de suffixe "Service" sauf si ambigu (OrderCalculator, pas OrderCalculatorService)
- Entités : PascalCase sans suffixe
- Repositories : suffixe Repository
- ValueObjects : dans src/Shared/ValueObject/ — readonly class
- Enums : dans src/*/Enum/ — backed enum string ou int

## Anti-patterns à bannir
- NE PAS retourner array non typé — utiliser @return array<Type>
- NE PAS mettre de logique métier dans les controllers
- NE PAS utiliser EntityManager directement dans les controllers
- NE PAS utiliser switch — utiliser match ou Strategy Pattern
- NE PAS faire de calculs financiers en float — utiliser Money (centimes int)

## Commandes utiles
- php bin/console doctrine:migrations:migrate
- ./vendor/bin/phpstan analyse
- ./vendor/bin/php-cs-fixer fix
- php bin/phpunit
