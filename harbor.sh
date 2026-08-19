#!/usr/bin/env bash

set -euo pipefail

echo "⚓ Harbor - bootstrap Docker"

if [[ ! -f artisan || ! -f composer.json || ! -f bootstrap/app.php ]]; then
  echo "❌ Este diretório não parece ser um projeto Laravel."
  exit 1
fi

if ! command -v docker >/dev/null 2>&1; then
  echo "❌ Docker não está instalado."
  exit 1
fi

if ! docker info >/dev/null 2>&1; then
  echo "❌ Docker não está em execução."
  exit 1
fi

if [[ ! -f .env ]]; then
  echo "📝 Criando .env a partir de .env.example..."
  cp .env.example .env
fi

echo "🚀 Subindo ambiente com Docker Compose..."
docker compose up -d --build

echo "✅ Ambiente iniciado."
echo "👉 API: http://localhost"
echo "👉 Use: docker compose exec app php artisan <comando>"
