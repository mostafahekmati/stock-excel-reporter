APP_SERVICE=app
DC=docker compose

.PHONY: help build up down restart logs ps shell artisan composer init link-art fresh mysql

help:
	@echo ""
	@echo "Available commands:"
	@echo "  make build              Build images"
	@echo "  make up                 Start containers (detached) + ensure 'art' command"
	@echo "  make down               Stop containers"
	@echo "  make restart            Recreate containers + ensure 'art' command"
	@echo "  make logs               Follow logs"
	@echo "  make ps                 Show running containers"
	@echo "  make shell              Enter app container shell"
	@echo "  make artisan c=          Run artisan from host (example: make artisan c='migrate')"
	@echo "  make composer c=         Run composer from host (example: make composer c='install')"
	@echo "  make init               First-time setup (up + install + key + migrate + art)"
	@echo "  make fresh              Reset DB and run migrations (DANGER: wipes DB)"
	@echo "  make mysql              Open MySQL client"
	@echo ""

build:
	$(DC) build

up:
	$(DC) up -d --build
	$(MAKE) link-art

down:
	$(DC) down

restart:
	$(DC) down
	$(DC) up -d --build
	$(MAKE) link-art

logs:
	$(DC) logs -f

ps:
	$(DC) ps

shell:
	$(DC) exec $(APP_SERVICE) bash

# Create/refresh the "art" command inside the container (symlink in PATH)
link-art:
	$(DC) exec $(APP_SERVICE) sh -lc 'if [ -f /var/www/art ]; then ln -sf /var/www/art /usr/local/bin/art; else echo "Missing /var/www/art. Create it in project root first."; exit 1; fi'

artisan:
	$(DC) exec $(APP_SERVICE) php artisan $(c)

composer:
	$(DC) exec $(APP_SERVICE) composer $(c)

init: up
	$(DC) exec $(APP_SERVICE) composer install
	$(DC) exec $(APP_SERVICE) php artisan key:generate
	$(DC) exec $(APP_SERVICE) php artisan migrate
	$(MAKE) link-art

fresh:
	$(DC) exec $(APP_SERVICE) php artisan migrate:fresh --seed

mysql:
	$(DC) exec mysql mysql -ularavel -plaravel laravel
