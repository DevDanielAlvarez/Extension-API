#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# O bind mount traz arquivos do host; alinhamos o uid/gid do www-data ao dono do
# projeto para que arquivos criados pela aplicação continuem editáveis fora do container.
HOST_UID="$(stat -c '%u' /var/www/html)"
HOST_GID="$(stat -c '%g' /var/www/html)"

if [[ "${HOST_UID}" != "0" && "$(id -u www-data)" != "${HOST_UID}" ]]; then
  groupmod -o -g "${HOST_GID}" www-data
  usermod -o -u "${HOST_UID}" -g "${HOST_GID}" www-data
fi

mkdir -p \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache \
  database

chown -R www-data:www-data storage bootstrap/cache
# X maiúsculo preserva o bit de execução só em diretórios, evitando sujar o diff do git.
chmod -R u+rwX,g+rwX storage bootstrap/cache

LOCK_HASH_FILE="vendor/.composer-lock.sha1"
CURRENT_LOCK_HASH="$(sha1sum composer.lock | cut -d' ' -f1)"

if [[ ! -f vendor/autoload.php || "$(cat "${LOCK_HASH_FILE}" 2>/dev/null || true)" != "${CURRENT_LOCK_HASH}" ]]; then
  echo "📦 Dependências PHP desatualizadas. Rodando composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
  echo "${CURRENT_LOCK_HASH}" > "${LOCK_HASH_FILE}"
fi

# Aceita apenas chave no formato base64:..., evitando linha corrompida por reinícios.
read_app_key_from_env_file() {
  local raw
  raw="$(grep -E '^APP_KEY=' .env 2>/dev/null | tail -n1 | cut -d= -f2- || true)"

  if [[ "${raw}" =~ ^base64:[A-Za-z0-9+/=]+$ ]]; then
    printf '%s' "${raw}"
  fi
}

FILE_APP_KEY="$(read_app_key_from_env_file)"

if [[ -n "${FILE_APP_KEY}" ]]; then
  export APP_KEY="${FILE_APP_KEY}"
else
  echo "🔑 APP_KEY ausente. Gerando chave da aplicação..."

  if grep -qE '^APP_KEY=' .env 2>/dev/null; then
    sed -i 's/^APP_KEY=.*/APP_KEY=/' .env
  else
    echo 'APP_KEY=' >> .env
  fi

  php artisan key:generate --force --no-interaction
  export APP_KEY="$(read_app_key_from_env_file)"
fi

DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
[[ "${DB_FILE}" = /* ]] || DB_FILE="/var/www/html/${DB_FILE}"

DB_IS_NEW=0

if [[ ! -f "${DB_FILE}" ]]; then
  echo "🗃️  Criando banco SQLite em ${DB_FILE}..."
  mkdir -p "$(dirname "${DB_FILE}")"
  touch "${DB_FILE}"
  DB_IS_NEW=1
fi

chown www-data:www-data "${DB_FILE}" "$(dirname "${DB_FILE}")"
chmod ug+rw "${DB_FILE}"

echo "🗄️  Executando migrations..."
php artisan migrate --force --no-interaction

if [[ "${DB_IS_NEW}" == "1" ]]; then
  echo "🌱 Banco novo. Executando seeders..."
  php artisan db:seed --force --no-interaction
fi

php artisan storage:link --no-interaction 2>/dev/null || true
php artisan l5-swagger:generate --no-interaction 2>/dev/null || true
php artisan filament:upgrade --no-interaction 2>/dev/null || true

echo "🚀 Aplicação pronta em http://localhost:${APP_PORT:-80}"

exec "$@"
