# FluxCommerce — Projet exemple de formation

ERP B2B fictif de gestion de commandes développé en **PHP 8.2 / Symfony 7.4 / Doctrine ORM**.

Créé pour la formation **"Claude au service du développeur Symfony"**.

## Stack technique

- PHP 8.2
- Symfony 7.4
- Doctrine ORM (PostgreSQL)
- PHPStan niveau 6
- PHP CS Fixer (@Symfony rules)
- PHPUnit 11

## Installation

```bash
composer install
cp .env .env.local
# Configurer DATABASE_URL dans .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Démarrage rapide avec Docker

**Prérequis** : Docker Desktop (Mac/Windows) ou Docker Engine + Compose v2 (Linux)

```bash
# 1. Cloner le dépôt
git clone https://github.com/abouillis/formation-claude-symfony.git
cd formation-claude-symfony

# 2. Démarrer les conteneurs (PHP 8.2 + Nginx + PostgreSQL 16)
make up          # ou : docker compose up -d

# 3. Installer les dépendances
make composer    # ou : docker compose exec php composer install

# 4. Lancer les migrations
make migrate     # ou : docker compose exec php php bin/console doctrine:migrations:migrate

# 5. Accéder à l'application
# → http://localhost:8080
```

**Commandes disponibles** (`make help` pour la liste complète) :

| Commande | Description |
|----------|-------------|
| `make up` | Démarrer les conteneurs |
| `make shell` | Shell dans le conteneur PHP |
| `make phpstan` | PHPStan niveau 6 |
| `make test` | Tests PHPUnit |
| `make reset` | Réinitialiser la BDD |

## Modules

| Module | Description |
|--------|-------------|
| Customer | Gestion des clients B2B, adresses, tiers de fidélité |
| Catalog | Produits, catégories, gestion du stock |
| Order | Commandes, lignes de commande, workflow de statuts |
| Billing | Factures, paiements |
| Reporting | Agrégats et rapports de ventes |

## Qualité

```bash
./vendor/bin/phpstan analyse          # Analyse statique (niveau 6)
./vendor/bin/php-cs-fixer fix --dry-run  # Vérification du style
php bin/phpunit                       # Tests
```

## Branches d'exercices

| Branche | Exercice |
|---------|----------|
| exercice/j2-credit-check | Créer CreditCheckService |
| exercice/j2-discount-refacto | Refactoriser DiscountResolver (Strategy Pattern) |
| exercice/j2-phpstan-errors | Corriger 5 erreurs PHPStan avec /fix-phpstan |
| exercice/j3-us01 | US-01 : Champ note sur commande |
| exercice/j3-us02 | US-02 : Calcul total TTC |
| exercice/j3-us03 | US-03 : Workflow statut commande |
| exercice/j3-us04 | US-04 : Génération facture automatique |
| exercice/j3-us05 | US-05 : Vérification crédit client |
| exercice/j3-us06 | US-06 : Système de remise automatique |

## Notes pédagogiques

Ce projet contient des **patterns legacy intentionnels** pour les exercices :
- `OrderStatusWorkflow` : switch/case à refactoriser en map de transitions
- `DiscountResolver` : switch/case à refactoriser en Strategy Pattern
- `ReportingService` : requêtes N+1 intentionnelles
- `OrderController` : fat controller avec logique métier
- `Money::fromFloat()` : bug de précision float intentionnel
