build:
	docker compose build --no-cache

up:
	docker compose --env-file .env.docker up -d

down:
	docker compose down

restart:
	docker compose --env-file .env.docker up -d --build

logs:
	docker compose logs -f

logs-app:
	docker compose logs -f app

logs-nginx:
	docker compose logs -f nginx

shell:
	docker compose exec app sh

artisan:
	docker compose exec app php artisan $(cmd)

migrate:
	docker compose exec app php artisan migrate --force

seed:
	docker compose exec app php artisan db:seed --force

cache-clear:
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

ps:
	docker compose ps
