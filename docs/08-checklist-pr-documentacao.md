# Checklist de PR para Documentacao

Use este checklist em todo PR:

- [ ] Mudou endpoint, payload ou response? Atualizar `docs/04-api.md`.
- [ ] Mudou regra de negocio de fluxo? Atualizar `docs/03-fluxos-de-negocio.md`.
- [ ] Mudou papel/permissao/policy? Atualizar `docs/05-regras-de-autorizacao.md`.
- [ ] Mudou entidades/conceitos? Atualizar `docs/06-glossario.md` e `docs/01-visao-geral.md`.
- [ ] Mudou estrutura de camadas/padroes? Atualizar `docs/02-arquitetura.md`.
- [ ] Incluiu ou atualizou diagrama Mermaid quando o fluxo ficou mais complexo.
- [ ] Validou que nao ha contradicao entre docs e codigo atual.

## Criterio de Pronto
PR so deve ser considerado completo quando as alteracoes funcionais estiverem refletidas em `docs/`.
