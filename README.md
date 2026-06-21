# Gallery of Spaces

Сайт «Галерея Пространств» — Laravel 13 + Vue 3 SPA + MySQL (Docker).

## Быстрый старт (локально)

```bash
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan migrate --seed
docker compose exec php npm install
docker compose exec php npm run build
```

Откройте http://127.0.0.1:8880

| Роль | Email | Пароль |
|------|-------|--------|
| Администратор | admin@gallery.local | password |
| Пользователь | alex@example.com | password |

## GitHub и автодеплой

- **CI** — проверка сборки на каждый push/PR (`.github/workflows/ci.yml`)
- **Deploy** — автоматический деплой на VPS при push в `main` (`.github/workflows/deploy.yml`)

Подробная инструкция: [docs/DEPLOY.md](docs/DEPLOY.md)

### Секреты GitHub Actions

`DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_KEY`, `DEPLOY_PATH`, `DEPLOY_PORT`

## Стек

- PHP 8.4, Laravel 13, Sanctum
- Vue 3, Vue Router, Pinia, Vite
- MySQL 8.4, Nginx, Docker Compose

## Страницы

- `/` — главная
- `/apartments`, `/purchase`, `/rent`, `/izhs`, `/renovation` — каталоги
- `/home-staging` — хоумстейджинг
- `/admin` — админ-панель
