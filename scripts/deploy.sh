#!/usr/bin/env bash
set -euo pipefail

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.prod.yml}"
PROJECT_DIR="${DEPLOY_PATH:-$(cd "$(dirname "$0")/.." && pwd)}"

cd "$PROJECT_DIR"

echo "==> Pull latest code"
git fetch origin
git reset --hard "origin/${GITHUB_REF_NAME:-main}"

echo "==> Start containers"
docker compose -f "$COMPOSE_FILE" up -d --build

echo "==> Install PHP dependencies"
docker compose -f "$COMPOSE_FILE" exec -T php composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

echo "==> Install JS dependencies and build assets"
docker compose -f "$COMPOSE_FILE" exec -T php npm ci
docker compose -f "$COMPOSE_FILE" exec -T php npm run build

echo "==> Laravel setup"
docker compose -f "$COMPOSE_FILE" exec -T php php artisan migrate --force
docker compose -f "$COMPOSE_FILE" exec -T php php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T php php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T php php artisan view:cache

echo "==> Deploy finished successfully"
