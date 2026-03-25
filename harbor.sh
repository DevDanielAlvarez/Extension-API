#!/usr/bin/env bash

set -e

echo "⚓ Harbor - Laravel bootstrap"

# 1. Verificar se é um projeto Laravel
if [[ ! -f artisan || ! -f composer.json || ! -f bootstrap/app.php ]]; then
  echo "❌ Este diretório não parece ser um projeto Laravel."
  exit 1
fi

# 2. Verificar Docker
if ! command -v docker >/dev/null 2>&1; then
  echo "❌ Docker não está instalado."
  echo "Instale o Docker antes de continuar."
  exit 1
fi

# 3. Verificar Docker em execução
if ! docker info >/dev/null 2>&1; then
  echo "❌ Docker não está em execução."
  echo "Inicie o Docker e tente novamente."
  exit 1
fi

# 4. Verificar se vendor existe
if [[ ! -d vendor ]]; then
  echo "📦 vendor/ não encontrado. Executando composer install via Docker..."

  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/app" \
    -w /app \
    composer:2 \
    composer install --ignore-platform-req=ext-intl --no-interaction --prefer-dist
else
  echo "✅ vendor/ já existe. Pulando composer install."
fi

# 5. Verificar Sail
if [[ ! -f vendor/bin/sail ]]; then
  echo "❌ Sail não encontrado após o composer install."
  echo "Verifique se o projeto possui laravel/sail como dependência."
  exit 1
fi

# 6. Subir ambiente com Sail
echo "🚀 Iniciando ambiente com Sail..."
./vendor/bin/sail up -d

echo "✅ Ambiente Laravel iniciado com sucesso."
echo "👉 Use ./vendor/bin/sail para trabalhar no projeto."