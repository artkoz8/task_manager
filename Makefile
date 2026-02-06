# Zmienne
DC = docker compose -f .docker/docker-compose.yaml
APP_EXEC = $(DC) exec -t php-fpm
PHP = $(APP_EXEC) php
COMPOSER = $(APP_EXEC) composer
CONSOLE = $(APP_EXEC) bin/console

.PHONY: help build up down shell test artisan migrate seed install setup run_jobs create-author

help: ## Wyświetla pomoc
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# DOCKER ######################################################################
build: ## Buduje obrazy
	$(DC) build

up: ## Uruchamia kontenery
	$(DC) up -d

down: ## Zatrzymuje kontenery
	$(DC) down

shell: ## Wchodzi do kontenera PHP
	$(APP_EXEC) bash

# COMPOSER #####################################################################
composer_install: ## Generuje parę kluczy secret
	$(COMPOSER) install

# SYMFONY ######################################################################
generate_secret_keys: ## Generuje parę kluczy secret
	$(CONSOLE) secrets:generate-keys

cache_clear: ## Czyszczenie cache aplikacji
	rm -rf var/cache/*
	$(CONSOLE) cache:clear

graphQL_dump_schema: cache_clear ## Wykonaj dump schem dla graphQL
	$(CONSOLE) graphql:dump-schema
