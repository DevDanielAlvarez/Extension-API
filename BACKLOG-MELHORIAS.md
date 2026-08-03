# Backlog de Melhorias — UX e Login

Lista de ajustes a fazer, levantados a partir do código atual. Organizado por frente de trabalho, com o problema, o porquê e os arquivos envolvidos.

> **Status:** implementado em 2026-08-03 (ver plano em `.claude/plans` da sessão). Itens não marcados foram avaliados e conscientemente adiados — o motivo está anotado ao lado.

---

## 1. Login somente com CPF

**Problema:** hoje o login (API e painel) aceita `document_type` como `CPF` **ou** `CNPJ`, porque o campo é genérico e compartilhado com `Patient`/`Responsible`. Isso deixa a tela de login mais confusa do que precisa (usuário tem que escolher um tipo de documento antes de digitar o número).

**Arquivos envolvidos:**
- `app/Http/Requests/LoginFormRequest.php` — valida `document_type` com `new Enum(DocumentTypeEnum::class)` (aceita CPF e CNPJ).
- `app/Http/Controllers/AuthController.php` (`login`) — busca o usuário por `document_type` + `document_number` vindos do request.
- `app/Filament/Auth/CustomLogin.php` — formulário do painel tem um `Select` de `document_type` antes do campo de número.
- `app/Http/Requests/User/CreateUserFormRequest.php` e `app/Filament/Auth/CustomRegistration.php` — cadastro de usuário também aceita CNPJ hoje.

**O que fazer:**
- [x] `CustomLogin.php`: removido o `Select::make('document_type')` da tela; ficou só `document_number` (CPF) e `password`. `document_type` fixado em `DocumentTypeEnum::CPF` internamente no `authenticate()`.
- [x] `LoginFormRequest.php`: removida a regra de `document_type`; o backend sempre assume CPF.
- [x] `AuthController::login`: query sempre filtra `document_type = CPF`.
- [x] Restringido também o **cadastro/edição** de usuário (`CreateUserFormRequest`, `UpdateUserFormRequest`, `CustomRegistration`, `UserForm.php` do painel, `UserController`) a CPF — inclui o CRUD admin de usuários, não só o self-registro.
- [x] `AuthControllerTest.php` e `UserControllerTest.php` atualizados; adicionado teste garantindo que um usuário CNPJ (dado legado) não consegue logar.
- [x] Swagger (`app/OpenApi/ApiDocumentation.php`) atualizado — `document_type` removido dos schemas de Login/Register/Update.
- [x] Decisão tomada: `Patient` e `Responsible` continuam aceitando CPF ou CNPJ normalmente; a coluna `document_type` de `users` foi mantida no schema (não removida), só deixou de ser input do usuário — evita quebrar consistência com o restante do banco.

---

## 2. Deixar o sistema mais intuitivo e fácil de usar

**Problema geral:** o painel Filament funciona, mas tem atrito de navegação e pontos onde falta contexto/feedback pro usuário final (provavelmente equipe de enfermagem, não devs).

**O que fazer:**
- [x] **Agrupar a navegação lateral**: `getNavigationGroup()` adicionado em todos os Resources — "Cuidado ao Paciente" (Pacientes, Prescrições, Responsáveis, Medicamentos) e "Administração do Sistema" (Usuários, Papéis).
- [ ] **Padronizar rótulos em pt-BR** em todo o painel — não feito nesta rodada; é um trabalho de varredura grande e contínuo, mais adequado a ir sendo corrigido conforme cada tela é tocada do que numa mudança única.
- [x] **Textos de ajuda (`helperText`)**: adicionados em `content_unit`/`route_of_administration` (`MedicineForm.php`), `end_date` (`PrescriptionForm.php` e o formulário embutido em `PrescriptionsRelationManager.php`), e `nursing_report` (`PatientForm.php`).
- [x] **Notificação de sucesso** adicionada na `CreateAction` de `PrescriptionsRelationManager.php` (era a exceção sem notificação).
- [x] **Confirmação amigável** em `ForceDeleteAction` com `modalHeading`/`modalDescription` explicando que é uma exclusão permanente, aplicado em todas as Tables principais (Patients, Medicines, Prescriptions, Responsibles, Users, Roles) e em `PrescriptionSchedulesRelationManager`.
- [x] **Busca global** configurada: `PatientResource` agora busca por nome **e** documento (`getGloballySearchableAttributes`) e mostra o documento no resultado; `MedicineResource` já cobria nome via `recordTitleAttribute` padrão do Filament.
- [ ] **Simplificar seleção de paciente/medicamento** — revisão não feita nesta rodada; os `Select` relevantes já usavam `searchable()`/`preload()` de forma consistente onde foram checados, sem achado de inconsistência que valesse mudança.
- [x] **Onboarding/vazio amigável**: `emptyStateHeading`/`emptyStateDescription` adicionados nas Tables de Pacientes, Medicamentos, Prescrições, Responsáveis, Usuários e Papéis.
- [x] **Responsividade**: colunas de timestamp já usavam `toggleable(isToggledHiddenByDefault: true)` de forma consistente nas tabelas principais; nada adicional necessário além do que já existia.

---

## 3. Melhorar "Medicamentos para aplicar hoje" no Dashboard

**Problema:** o widget `TodayMedicationsTable` (`app/Filament/Widgets/TodayMedicationsTable.php`) era só uma **listagem estática** dos horários de hoje — não existia campo no banco pra marcar uma dose como aplicada, atrasada ou pulada.

**Lacuna estrutural (pré-requisito):**
- [x] Nova tabela `medication_administrations` (`prescription_schedule_id`, `scheduled_date`, `applied_at` nullable, `applied_by_user_id` nullable, unique por ocorrência). Sem Service/DTO dedicados — o status é calculado em runtime (não guardado como enum) a partir de `applied_at` vs horário atual, então não precisa de um `status` persistido nem de um job para mantê-lo atualizado.
- [x] Model `MedicationAdministration` + relação `PrescriptionSchedule::medicationAdministrations()`.

**Melhorias no widget:**
- [x] Ação por linha "Marcar como aplicado" (grava `applied_at`/`applied_by_user_id`) e ação "Desfazer" (reverte, com confirmação).
- [x] Badge de status colorido: pendente (cinza), atrasado (vermelho), aplicado (verde).
- [x] Ordenação: itens ainda não aplicados sempre no topo (`orderByRaw('administration_applied_at is null desc')`), com `time` como critério secundário — mantém a coluna `time` clicável/ordenável pelo usuário.
- [ ] **Agrupar por paciente** (`Table::groups()`) — avaliado e adiado: o suporte do Filament a agrupamento por relacionamento aninhado (`prescription.patient.name`) não pôde ser validado com segurança sem rodar a UI extensivamente; o filtro por paciente (abaixo) cobre a mesma necessidade com menos risco.
- [x] Filtro por paciente e por medicamento na tabela.
- [x] `pollingInterval` de 30s mantido.

---

## 4. Botão de cadastro rápido

**Problema:** já existia um bom fluxo de criação "aninhada" (prescrição + horários na aba do paciente, responsável direto ao vincular), mas não havia atalho no Dashboard.

**Decisão confirmada com o usuário:** cadastro rápido cobre **Paciente + Prescrição + Medicamento** (não só Prescrição).

**O que fazer:**
- [x] `App\Filament\Support\QuickCreateActions` — classe com as 3 ações reutilizáveis (Paciente, Prescrição com repeater de horários, Medicamento), reaproveitando os `Schema`s e Services já existentes.
- [x] Header actions no Dashboard (`getHeaderActions()`) com os 3 atalhos.
- [x] Header action "Nova Prescrição" também dentro do próprio widget `TodayMedicationsTable`.
- [x] Notificação de sucesso em todas as 3 ações.
- [ ] Refresh automático de widgets **fora** do widget de origem — limitação assumida: os botões do Dashboard não empurram atualização imediata para os outros widgets (aparecem em até 30s via polling existente). Só a ação dentro do próprio `TodayMedicationsTable` atualiza na hora. Não valia a complexidade de um barramento de eventos entre widgets para esse ganho.

---

## Ordem de execução (como foi feito)

1. Login só com CPF.
2. Estrutura de aplicação de dose (`medication_administrations`).
3. Dashboard de hoje mais acionável.
4. Botões de cadastro rápido.
5. Polimento geral de UX.
