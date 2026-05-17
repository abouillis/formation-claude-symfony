.PHONY: help up down build shell composer phpstan phpcs test logs reset

help: ## Afficher l'aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Démarrer les conteneurs
	docker compose up -d

down: ## Arrêter les conteneurs
	docker compose down

build: ## Rebuilder les images
	docker compose build --no-cache

shell: ## Ouvrir un shell dans le conteneur PHP
	docker compose exec php sh

composer: ## Installer les dépendances Composer
	docker compose exec php composer install

migrate: ## Lancer les migrations Doctrine
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

fixtures: ## Charger les fixtures (si disponibles)
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

phpstan: ## Lancer PHPStan niveau 6
	docker compose exec php vendor/bin/phpstan analyse --level=6

phpcs: ## Vérifier le style de code (dry-run)
	docker compose exec php vendor/bin/php-cs-fixer fix --dry-run --diff

phpcs-fix: ## Corriger le style de code
	docker compose exec php vendor/bin/php-cs-fixer fix

test: ## Lancer les tests PHPUnit
	docker compose exec php php bin/phpunit

test-ci: ## Lancer les tests dans un conteneur dédié (CI)
	docker compose -f docker-compose.test.yml up --abort-on-container-exit --exit-code-from php

logs: ## Afficher les logs
	docker compose logs -f

reset: ## Réinitialiser la base de données
	docker compose exec php php bin/console doctrine:database:drop --force --if-exists
	docker compose exec php php bin/console doctrine:database:create
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

cache-clear: ## Vider le cache Symfony
	docker compose exec php php bin/console cache:clear
