# Regras de Autorizacao

## Modelo de Seguranca
- Autenticacao API via Sanctum (`auth:sanctum`).
- Autorizacao por Policies + Role/Permission.
- Permissao definida por par (`screen`, `name`).

## Como a Permissao e Avaliada
Implementacao principal em `app/Policies/Traits/CheckPermissionTrait.php`.

Passos:
1. Se usuario autenticado tem `is_adm = true`, retorna `true` imediatamente.
2. Monta chave de cache local por request: `screen:action`.
3. Busca permissao no banco (`permissions`) por nome da acao e tela.
4. Chama `User::hasPermission(Permission)` para verificar se algum papel do usuario contem a permissao.

## Policies por Recurso
Exemplos:
- `PatientPolicy` usa `PermissionScreenEnum::PATIENTS_SCREEN`.
- `PrescriptionPolicy` usa `PermissionScreenEnum::PRESCRIPTIONS_SCREEN`.
- `RolePolicy` usa `PermissionScreenEnum::ROLES_SCREEN`.
- `StockItemPolicy`, `StockMovementPolicy` e `StockDonationPolicy` usam `PermissionScreenEnum::STOCK_SCREEN` (uma unica tela cobre cadastro de itens, movimentacoes e validacao de doacoes).
- As rotas `GET/POST /doacoes` (formulario publico de doacao) ficam fora de `auth:sanctum` propositalmente — qualquer visitante pode registrar uma doacao pendente; a confirmacao exige login no painel Filament.

Acoes avaliadas normalmente:
- `listar`, `exibir`, `criar`, `atualizar`, `deletar`
- `restaurar`, `forcar deletar`, `reordenar`
- variantes em massa quando aplicavel

## Gestao de Permissoes por Tela
`RoleController` possui endpoints para:
- listar permissoes disponiveis e selecionadas por tela
- sincronizar permissoes da tela alvo
- ativar/desativar todas na tela alvo

Importante: no sync, permissoes de outras telas sao preservadas.

## Observacoes Operacionais
- APIs sem token em `api/*` retornam `401` JSON.
- Fluxo web sem autenticacao redireciona para login Filament.
- Comportamento definido em `bootstrap/app.php`.
- Login e cadastro de `User` sao restritos a CPF (`document_type` fixado no backend, nao e mais input do cliente).
