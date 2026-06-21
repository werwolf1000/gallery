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

| Secret | Описание |
|--------|----------|
| `DEPLOY_HOST` | IP или домен сервера |
| `DEPLOY_USER` | SSH-пользователь |
| `DEPLOY_KEY` | Приватный SSH-ключ |
| `DEPLOY_PATH` | Путь на сервере, например `/var/www/gallery` |
| `DEPLOY_PORT` | SSH-порт (обычно `22`) |

**Variable** (Settings → Secrets and variables → Actions → Variables):

| Variable | Значение |
|----------|----------|
| `DEPLOY_ENABLED` | `true` — включить автодеплой при push в `main` |

Пока `DEPLOY_ENABLED` не задан, workflow **Deploy** выполняет только тесты сборки, SSH-шаг пропускается. Ручной деплой: Actions → Deploy → Run workflow.

## Стек

- PHP 8.4, Laravel 13, Sanctum
- Vue 3, Vue Router, Pinia, Vite
- MySQL 8.4, Nginx, Docker Compose

## Страницы

- `/` — главная
- `/apartments`, `/purchase`, `/rent`, `/izhs`, `/renovation` — каталоги
- `/home-staging` — хоумстейджинг
- `/admin` — админ-панель


## На сервере (один раз, вне GitHub)
1. Установить Docker и Git.
2. Клонировать репозиторий в DEPLOY_PATH:

```shell
git clone git@github.com:werwolf1000/gallery.git /var/www/gallery
```
3. Создать .env из .env.example и задать:

   - APP_KEY — php artisan key:generate
   - APP_URL — ваш домен
   - DB_PASSWORD, DB_ROOT_PASSWORD
   - SANCTUM_STATEFUL_DOMAINS — ваш домен (без http://)

4. Первый запуск:
```shell
cd /var/www/gallery
bash scripts/deploy.sh
```
