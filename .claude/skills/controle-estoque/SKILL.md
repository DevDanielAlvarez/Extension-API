---
name: controle-estoque
description: Cria ou atualiza a especificação de requisitos do controle de estoque desta clínica/casa de repouso (itens de enxoval do residente como travesseiro e edredom; insumos médicos como agulha e seringa). Use quando o usuário pedir para "documentar controle de estoque", "especificar estoque", "cadastro de itens/insumos", "estoque de residentes" ou "estoque médico" neste projeto. A funcionalidade ainda não existe no código — este skill produz uma especificação de requisitos, não documentação de código existente.
---

# Controle de Estoque — Especificação de Requisitos

## Contexto do domínio

O projeto (`Extension-API`) é uma API Laravel para uma clínica que funciona como **casa de repouso para idosos**. No modelo de dados atual (ver `ESPECIFICACOES.md`), os idosos são a entidade `Patient`. Esta skill trata de uma funcionalidade **ainda não implementada**: controle de estoque de itens usados na instituição, que se dividem em pelo menos duas naturezas distintas:

- **Itens de enxoval/uso pessoal do residente** — ex.: travesseiro, edredom, lençol, toalha. Tipicamente atribuídos/emprestados a um `Patient` específico, sem necessidade de controle de lote/validade.
- **Insumos médicos/clínicos** — ex.: agulha, seringa, luvas, gaze, álcool. Consumidos pela equipe médica/enfermagem no cuidado ao paciente, muitas vezes com necessidade de controle de lote e validade (relevante para rastreabilidade e compliance sanitário).

Trate essas duas naturezas como categorias de um mesmo cadastro de itens de estoque, não como dois módulos separados, a menos que o usuário diga o contrário — isso evita duplicar CRUD e Policy.

**Importante:** como a funcionalidade não existe no código, este skill produz uma **especificação de requisitos** (o que deve ser construído), não uma documentação descritiva de comportamento real. Nunca escreva como se algo já estivesse implementado. Marque decisões em aberto como **"A decidir"** (não confundir com "A confirmar" do playbook em `docs/07-playbook-ia-documentacao.md`, que é para lacunas de documentação sobre código já existente).

## Processo

1. **Confirme o escopo antes de escrever**, se ainda não estiver claro na conversa. Perguntas que normalmente importam para casas de repouso:
   - Itens de enxoval são atribuídos a um paciente específico (com controle de devolução) ou só contados como estoque geral por quarto/ala?
   - Insumos médicos precisam de controle de lote (`batch`) e validade (`expiry_date`)? (Normalmente sim, por rastreabilidade sanitária.)
   - Deve haver estoque mínimo com alerta de reposição?
   - Quem pode movimentar estoque — todos os papéis (`Role`) ou só alguns (ex.: enfermagem para insumos, administrativo para enxoval)?
   - Movimentações de saída devem poder ser vinculadas a um paciente (ex.: seringa usada no paciente X) ou só ao usuário que registrou?
   - Compras/fornecedores entram no escopo agora ou ficam para depois?
   Não invente respostas para essas perguntas — se o usuário não respondeu, registre como "A decidir" no documento em vez de assumir.

2. **Modele os dados seguindo as convenções já estabelecidas no projeto** (ver `ESPECIFICACOES.md` seções 2–4):
   - Chave primária ULID, `timestamps()` + `softDeletes()` em entidades de negócio.
   - Separação Controller → FormRequest → DTO → Service → Model → Resource.
   - Autorização por tela via `PermissionScreenEnum` (propor um novo valor, ex. `stock_screen`) + Policy dedicada, seguindo o padrão de `PatientPolicy`/`MedicinePolicy`.
   - Categorização por enum (ex. `StockItemCategoryEnum`: `RESIDENT_SUPPLY`, `MEDICAL_SUPPLY`), em vez de tabelas separadas por categoria — replica o padrão já usado em `ContentUnitEnum`/`RouteOfAdministrationEnum`.
   - Um log de movimentações (entrada/saída/ajuste) semelhante ao padrão já usado em `medication_administrations`: tabela operacional própria, referenciando o item, quantidade, usuário responsável e, quando aplicável, o paciente.
   - Proponha nomes de tabela/model em inglês (consistente com o restante do schema: `patients`, `medicines`, `prescriptions`), mesmo com o texto da doc em português.

3. **Escreva/atualize o documento de especificação** em `docs/09-controle-de-estoque.md`, no mesmo estilo dos demais arquivos em `docs/` (tabelas de schema, enums, regras, mermaid). Estrutura sugerida:
   1. Objetivo e escopo (o que entra e o que fica de fora nesta fase)
   2. Perfis/papéis envolvidos
   3. Entidades e schema proposto (tabelas markdown, como em `ESPECIFICACOES.md` seção 3)
   4. Enums propostos
   5. Regras de negócio propostas (ex.: não permitir saída maior que saldo; alerta de estoque mínimo; item de enxoval emprestado exige devolução para voltar ao saldo)
   6. Fluxos propostos (com `sequenceDiagram` Mermaid, mesmo padrão de `ESPECIFICACOES.md` seção 6)
   7. Autorização (nova entrada em `PermissionScreenEnum` e regras de Policy)
   8. Perguntas em aberto / decisões pendentes (lista explícita de "A decidir")
   9. Critérios de aceite para a primeira versão implementável

4. **Depois de escrever o novo doc, siga o checklist do próprio projeto** (`docs/08-checklist-pr-documentacao.md`):
   - Novo conceito/entidade → atualizar `docs/06-glossario.md` e `docs/01-visao-geral.md`.
   - Novo fluxo → refletir em `docs/03-fluxos-de-negocio.md` (ou linkar para o novo arquivo).
   - Nova permissão/tela → atualizar `docs/05-regras-de-autorizacao.md`.
   - Se `ESPECIFICACOES.md` for regenerado a partir de `docs/`, sinalize ao usuário que ele precisa ser atualizado também (é um documento consolidado, gerado a partir dos arquivos em `docs/`).

5. **Não implemente código nesta skill.** O entregável é o documento de especificação. Se o usuário pedir para implementar depois, isso é um passo separado (migrations, models, services etc.) que deve partir do documento já validado.

## O que evitar

- Não misture terminologia: o residente idoso é `Patient` no sistema atual — use esse termo ao referenciar o schema, mas pode usar "residente"/"idoso" no texto explicativo em português.
- Não crie dois CRUDs paralelos (um para enxoval, outro para insumos) sem necessidade explícita do usuário — prefira um cadastro único de itens com categoria.
- Não assuma controle de lote/validade só para insumos médicos sem confirmar — pergunte, mas essa é a suposição padrão razoável caso o usuário não tenha preferência.
- Não declare a funcionalidade como "implementada" ou "existente" em nenhum lugar do documento.
