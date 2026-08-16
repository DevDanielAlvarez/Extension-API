---
name: app-context
description: Detalha o contexto completo do projeto Extension-API (stack, entidades, arquitetura, fluxos de negocio, autorizacao, API, convencoes e setup) direto no chat, cruzando docs/ e ESPECIFICACOES.md com o codigo real para apontar o que esta atualizado e o que esta desatualizado. Use quando o usuario pedir "contexto do projeto", "explique o projeto", "panorama do sistema", "app-context", "onboarding" ou "me da uma visao geral" neste projeto. Nao gera nem edita arquivos — a saida e apenas a resposta no chat.
---

# App Context — Panorama Completo do Projeto

## Objetivo

Dar ao usuario (ou a uma sessao nova de IA) uma visao completa e confiavel do projeto `Extension-API` em uma unica resposta no chat, sem precisar abrir `docs/` manualmente. A skill nao produz nem atualiza arquivos — o entregavel e a mensagem no chat.

## Contexto do domino

O projeto ja tem documentacao extensa e madura em `docs/01` a `docs/09` e um documento consolidado em `ESPECIFICACOES.md` (gerado a partir de `docs/`). Essa documentacao é a fonte primaria, mas pode ficar desatualizada em relacao ao codigo (o proprio projeto reconhece isso em `docs/07-playbook-ia-documentacao.md` e `docs/08-checklist-pr-documentacao.md`, que existem justamente para manter docs e codigo sincronizados). O valor desta skill esta em **cruzar a documentacao com o codigo/git real antes de responder**, nao em apenas parafrasear `ESPECIFICACOES.md`.

## Processo

1. **Leia primeiro as fontes consolidadas**, na ordem:
   - `ESPECIFICACOES.md` (visao consolidada: stack, arquitetura, schema, enums, API, fluxos, autorizacao, glossario, setup)
   - `docs/01-visao-geral.md` a `docs/06-glossario.md` (mesmo conteudo, granular — use se precisar de mais detalhe em uma secao especifica)
   - `README.md` (setup/execucao do projeto)

2. **Verifique pontos volateis contra o codigo real** antes de apresentar como fato, especialmente:
   - Rotas: `routes/api.php` e `routes/web.php` (ou pasta `routes/`) vs. `docs/04-api.md`
   - Entidades/schema: `database/migrations/*` e `app/Models/*` vs. secao 3 do `ESPECIFICACOES.md`
   - Autorizacao: `app/Policies/*.php`, `PermissionScreenEnum` vs. `docs/05-regras-de-autorizacao.md`
   - Enums de dominio: `app/Enums/*` (ou equivalente) vs. secao 4 do `ESPECIFICACOES.md`
   - Mudancas recentes ainda nao documentadas: rode `git log --oneline -20` e compare com o que os docs descrevem — funcionalidades novas (ex.: dashboard, quick-create, restricoes de login) podem ja estar no codigo e ainda nao em `docs/`.

3. **Nunca apresente doc desatualizado como se fosse o estado atual do codigo.** Se encontrar divergencia entre `docs/`/`ESPECIFICACOES.md` e o codigo, sinalize explicitamente as duas versoes (ex.: "docs descrevem X, mas o codigo em `arquivo.php` hoje faz Y") em vez de escolher uma silenciosamente. Se nao for possivel confirmar algo no codigo, marque como "A confirmar" (mesmo termo usado no playbook do projeto).

4. **Monte a resposta no chat** cobrindo, na medida do que for relevante ao pedido do usuario:
   1. Objetivo do projeto e stack principal (Laravel, Sanctum, Filament, Swagger, etc.)
   2. Entidades centrais e relacionamentos (Patient, Prescription, StockItem, Role/Permission etc.)
   3. Arquitetura e camadas (Controller → FormRequest → DTO → Service → Model → Resource) e onde cada uma vive em `app/`
   4. Principais fluxos de negocio (cadastro de paciente, prescricao, movimentacao de estoque, permissoes por tela)
   5. Modelo de autorizacao (Policies, `PermissionScreenEnum`, `CheckPermissionTrait`)
   6. Superficie de API (grupos de endpoints, autenticacao, paginacao, erros comuns)
   7. Convencoes de codigo observadas (ULID, `timestamps()` + `softDeletes()`, transacoes em escrita composta)
   8. Setup/execucao (Docker/Sail vs. sem Docker) — resuma, sem repetir o `README.md` inteiro
   9. Pontos em aberto / divergencias encontradas entre docs e codigo, e funcionalidades recentes ainda nao documentadas

   Ajuste a profundidade ao pedido: se o usuario pedir contexto geral, cubra tudo em nivel de resumo; se pedir foco em uma area (ex.: "contexto de autorizacao"), aprofunde so essa secao e resuma o resto em 1-2 linhas.

5. **Cite arquivos reais** (`app/Models/Patient.php`, `routes/api.php`, etc.) para cada afirmacao central, seguindo o mesmo padrao de rastreabilidade usado em `docs/07-playbook-ia-documentacao.md`.

## O que evitar

- Nao crie, edite ou proponha editar arquivos em `docs/` ou `ESPECIFICACOES.md` — esta skill e somente leitura/apresentacao no chat. Se o usuario quiser um documento persistido, isso e um pedido separado.
- Nao apresente todo o conteudo de `ESPECIFICACOES.md` literalmente; sintetize e destaque o que importa para o pedido do usuario.
- Nao invente comportamento que nao esta nem nos docs nem no codigo — marque como "A confirmar".
- Nao ignore o codigo achando que a documentacao ja esta correta; o projeto tem historico de docs sendo atualizadas junto com features (ver `git log`), mas isso nao garante 100% de sincronia no momento da consulta.
