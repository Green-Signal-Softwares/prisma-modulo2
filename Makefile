# =============================================================================
#  PRISMA — Makefile
#  Atalhos para operações Docker mais comuns
# =============================================================================

.PHONY: up down build logs shell artisan migrate fresh reset status

## Sobe todos os serviços (modo dev com Vite + Mailpit)
up:
	docker compose --profile dev up -d

## Sobe apenas os serviços de produção (sem Vite HMR)
up-prod:
	docker compose up -d

## Para todos os serviços
down:
	docker compose down

## Rebuild completo das imagens
build:
	docker compose build --no-cache

## Rebuild e sobe
rebuild: build up

## Logs em tempo real dos serviços principais
logs:
	docker compose logs -f app queue reverb scheduler

## Abre o shell do container app
shell:
	docker compose exec app bash

## Abre o shell do container MySQL
db-shell:
	docker compose exec mysql mysql -u${DB_USERNAME:-prisma} -p${DB_PASSWORD:-secret} ${DB_DATABASE:-prisma}

## Roda artisan (ex: make artisan CMD="migrate")
artisan:
	docker compose exec app php artisan $(CMD)

## Rodar migrações
migrate:
	docker compose exec app php artisan migrate

## Reset do banco (cuidado em produção!)
fresh:
	docker compose exec app php artisan migrate:fresh --seed

## Remove tudo incluindo volumes (DESTRUTIVO)
reset:
	docker compose down -v --remove-orphans
	docker compose --profile dev up -d --build

## Status dos containers
status:
	docker compose ps
