# Back API

API Laravel com autenticação Sanctum, painel Filament e documentação Swagger.

## Requisitos

### Com Docker
- Docker
- Docker Compose (plugin docker compose)

### Sem Docker
- PHP 8.5+
- Composer 2+
- Node.js 20+ e npm
- MariaDB 11+ (ou MySQL compatível)

## 1) Rodar com Docker (Laravel Sail)

### Linux

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### Windows (PowerShell)

```powershell
Copy-Item .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### Enderecos (Docker)
- API: http://localhost
- Swagger: http://localhost/api/documentation
- Filament: http://localhost/admin

Observacao: a porta pode variar conforme APP_PORT no .env.

## 2) Rodar sem Docker

### Linux

```bash
cp .env.example .env
composer install
npm install

php artisan key:generate

# configure DB_* no .env para seu MariaDB/MySQL local
php artisan migrate:fresh --seed

php artisan serve
npm run dev
```

### Windows (PowerShell)

```powershell
Copy-Item .env.example .env
composer install
npm install

php artisan key:generate

# configure DB_* no .env para seu MariaDB/MySQL local
php artisan migrate:fresh --seed

php artisan serve
npm run dev
```

### Enderecos (sem Docker)
- API: http://127.0.0.1:8000
- Swagger: http://127.0.0.1:8000/api/documentation
- Filament: http://127.0.0.1:8000/admin

## Comandos uteis

### Docker

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail artisan l5-swagger:generate
./vendor/bin/sail down
```

### Sem Docker

```bash
php artisan test
php artisan l5-swagger:generate
```

## Solucao de problemas

- Erro de permissao em storage/bootstrap cache:

```bash
chmod -R 775 storage bootstrap/cache
```

- Se alterar migrations/modelos de soft delete, rode novamente:

```bash
php artisan migrate:fresh --seed
```

- Logs da aplicacao:

```bash
tail -f storage/logs/laravel.log
```
