# Развёртывание на GitHub и автодеплой

Проект настроен для CI/CD через GitHub Actions: проверка сборки на каждый PR и автоматический деплой на сервер при push в `main`.

## 1. Создайте репозиторий на GitHub

```bash
cd /home/werwolf/projects/gallery
git init
git add .
git commit -m "Initial commit: Gallery of Spaces Laravel + Vue SPA"
git branch -M main
git remote add origin git@github.com:YOUR_USERNAME/gallery.git
git push -u origin main
```

Замените `YOUR_USERNAME/gallery` на ваш репозиторий.

## 2. Подготовьте сервер (VPS)

Требования: Ubuntu 22.04+, Docker и Docker Compose, Git, SSH-доступ.

```bash
# На сервере
sudo apt update && sudo apt install -y docker.io docker-compose-plugin git
sudo usermod -aG docker $USER
# перелогиньтесь

sudo mkdir -p /var/www/gallery
sudo chown $USER:$USER /var/www/gallery
cd /var/www/gallery
git clone git@github.com:YOUR_USERNAME/gallery.git .
cp .env.example .env
nano .env   # задайте APP_KEY, APP_URL, пароли БД, SANCTUM_STATEFUL_DOMAINS
```

Сгенерируйте ключ приложения (локально или на сервере):

```bash
docker compose -f docker-compose.prod.yml run --rm php php artisan key:generate
```

Первый запуск вручную:

```bash
bash scripts/deploy.sh
```

## 3. Настройте секреты GitHub

В репозитории: **Settings → Secrets and variables → Actions → New repository secret**

| Secret | Описание |
|--------|----------|
| `DEPLOY_HOST` | IP или домен сервера |
| `DEPLOY_USER` | SSH-пользователь (например `deploy`) |
| `DEPLOY_KEY` | Приватный SSH-ключ (содержимое `~/.ssh/id_ed25519`) |
| `DEPLOY_PATH` | Путь на сервере, например `/var/www/gallery` |
| `DEPLOY_PORT` | SSH-порт (обычно `22`) |

### SSH-ключ для деплоя

На своей машине:

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/gallery_deploy -N ""
cat ~/.ssh/gallery_deploy.pub >> ~/.ssh/authorized_keys   # на сервере
# Приватный ключ ~/.ssh/gallery_deploy → секрет DEPLOY_KEY в GitHub
```

## 4. Как работает деплой

При push в `main`:

1. Workflow **CI** (`ci.yml`) — проверяет composer, npm build, миграции на SQLite.
2. Workflow **Deploy** (`deploy.yml`) — по SSH заходит на сервер и выполняет `scripts/deploy.sh`:
   - `git pull`
   - `docker compose -f docker-compose.prod.yml up -d --build`
   - `composer install --no-dev`
   - `npm ci && npm run build`
   - `php artisan migrate --force` и кеширование конфигов

## 5. Локальная разработка

```bash
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan migrate --seed
docker compose exec php npm install
docker compose exec php npm run dev   # или npm run build
```

Сайт: http://127.0.0.1:8880

## Демо-аккаунты (после seed)

| Роль | Email | Пароль |
|------|-------|--------|
| Администратор | admin@gallery.local | password |
| Пользователь | alex@example.com | password |
