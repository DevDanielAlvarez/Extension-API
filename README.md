# Back API

API Laravel com autenticação Sanctum, painel Filament e documentação Swagger.

## Requisitos

Docker e o plugin `docker compose`. Nada além disso — PHP, Composer, Node e banco rodam todos dentro do container.

## Setup

```bash
cp .env.example .env
docker compose up -d
```

É só isso. Na primeira subida o container cuida sozinho de:

1. gerar a `APP_KEY`
2. criar o banco SQLite em `database/database.sqlite`
3. rodar as migrations
4. rodar os seeders (apenas quando o banco é novo)
5. gerar a documentação Swagger

Acompanhe o progresso com `docker compose logs -f app`. A aplicação está pronta quando aparecer `🚀 Aplicação pronta`.

### Endereços

- API: http://localhost:8080
- Swagger: http://localhost:8080/api/documentation
- Filament: http://localhost:8080/admin

Para usar outra porta, altere `APP_PORT` (e `APP_URL`) no `.env` e rode `docker compose up -d` novamente.

## Banco de dados

O projeto usa **SQLite**, num arquivo em `database/database.sqlite`. Ele fica no seu diretório de trabalho, então persiste entre `docker compose down` e `up`.

Para começar do zero, apague o arquivo e suba de novo — as migrations e os seeders rodam automaticamente:

```bash
docker compose down
rm -f database/database.sqlite
docker compose up -d
```

## Comandos úteis

```bash
# logs
docker compose logs -f app

# Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan test
docker compose exec app php artisan l5-swagger:generate

# shell no container
docker compose exec app bash

# parar
docker compose down
```

## Solução de problemas

**A porta já está em uso.** Se algo no host já ocupa a `8080`, mude `APP_PORT` e `APP_URL` no `.env` e suba novamente.

**Alterei o `composer.json`/`composer.lock`.** O container detecta a mudança pelo hash do lock e reinstala as dependências sozinho no próximo `docker compose up -d`.

**Alterei arquivos do frontend.** Os assets são compilados durante o build da imagem, então reconstrua:

```bash
docker compose up -d --build
```

**Quero recomeçar do absoluto zero** (imagem, volumes e banco):

```bash
docker compose down -v
rm -f database/database.sqlite
docker compose up -d --build
```

## Documentação de fluxo e arquitetura

- `docs/01-visao-geral.md`
- `docs/02-arquitetura.md`
- `docs/03-fluxos-de-negocio.md`
- `docs/04-api.md`
- `docs/05-regras-de-autorizacao.md`
- `docs/06-glossario.md`
- `docs/07-playbook-ia-documentacao.md`
- `docs/08-checklist-pr-documentacao.md`

Sugestão de leitura rápida:

1. Visão geral
2. Arquitetura
3. Fluxos de negócio
4. API e autorização
