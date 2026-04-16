# Visao Geral do Projeto

## Objetivo
API Laravel para gestao de pacientes, responsaveis, prescricoes e horarios de medicacao, com autenticacao via Sanctum e administracao via Filament.

## Stack Principal
- Backend: Laravel 12 (PHP)
- Autenticacao API: Laravel Sanctum
- Painel administrativo: Filament
- Documentacao API: L5 Swagger/OpenAPI
- Persistencia: Eloquent ORM com Soft Deletes

## Entidades Centrais
- User: usuario autenticado, pode ser administrador (`is_adm`) e pode ter varios papeis.
- Role: papel funcional (ex.: enfermagem, farmacia), ligado a usuarios e permissoes.
- Permission: acao por tela (screen + name), usada por policies.
- Patient: paciente com dados pessoais e relacao com prescricoes e responsaveis.
- Responsible: responsavel legal/familiar ligado a pacientes.
- Prescription: prescricao para um paciente e medicamento, com periodo de vigencia.
- PrescriptionSchedule: agenda da prescricao por dia da semana, horario e quantidade.
- Medicine: medicamento usado na prescricao.

## Como Ler o Sistema em 30 Minutos
1. Ler rotas API em `routes/api.php` para entender pontos de entrada.
2. Ler controllers de dominio:
   - `app/Http/Controllers/PatientController.php`
   - `app/Http/Controllers/PrescriptionController.php`
   - `app/Http/Controllers/PrescriptionSchedulesController.php`
   - `app/Http/Controllers/RoleController.php`
3. Ler services correspondentes em `app/Services/*` para operacoes CRUD.
4. Ler modelos para regras de cascata e relacionamentos:
   - `app/Models/Patient.php`
   - `app/Models/Prescription.php`
   - `app/Models/PrescriptionSchedule.php`
5. Ler autorizacao em policies e trait:
   - `app/Policies/*.php`
   - `app/Policies/Traits/CheckPermissionTrait.php`

## Convencoes de Fluxo
- Entrada: Controller + FormRequest (ou validacao inline em poucos casos).
- Transporte de dados: DTOs (`app/DTO/*`).
- Persistencia: Services com `create`, `find`, `update`, `delete`.
- Saida: API Resources (`app/Http/Resources/*`).
- Operacoes criticas: `DB::transaction(...)` quando ha escrita composta.

## Comportamento de Erro de Autenticacao
- Requisicoes em `api/*` sem autenticacao recebem `401` JSON (`Unauthenticated.`).
- Requisicoes web nao autenticadas redirecionam para login do Filament.
- Configuracao em `bootstrap/app.php`.
