# Glossario

## Termos de Dominio
- Paciente: pessoa assistida pela instituicao.
- Responsavel: pessoa vinculada ao paciente para suporte legal/familiar.
- Prescricao: orientacao de uso de medicamento para um paciente em um periodo.
- Agenda da Prescricao: detalhe recorrente de administracao (dia da semana, horario e quantidade).
- Medicamento: item farmaceutico usado na prescricao.
- Item de Estoque: item de enxoval do residente (ex.: travesseiro, edredom) ou insumo medico (ex.: agulha, seringa) com saldo controlado.
- Movimentacao de Estoque: registro de entrada, saida, ajuste de inventario ou devolucao de um item de estoque; ver `docs/09-controle-de-estoque.md`.
- Doacao de Item: registro publico (sem login) de alguem trazendo um item para um paciente especifico, pendente de confirmacao por um admin no painel.
- Medicamento do Paciente (`PatientMedicine`): saldo de um medicamento pertencente a um paciente especifico (nunca a clinica — a instituicao nao pode manter estoque proprio de medicamentos por exigencia legal). Um saldo por par (paciente, medicamento).
- Movimentacao de Medicamento do Paciente (`PatientMedicineMovement`): registro de entrada, saida, ajuste ou devolucao do saldo de um `PatientMedicine`, incluindo a baixa automatica gerada ao marcar uma dose como aplicada.

## Termos Tecnicos
- DTO: objeto de transporte de dados entre camadas.
- FormRequest: classe de validacao de entrada HTTP do Laravel.
- Resource: transformador de saida JSON da API.
- Policy: classe de autorizacao por acao de recurso.
- Soft Delete: exclusao logica com possibilidade de restauracao.
- Force Delete: exclusao fisica permanente.
- Screen: contexto funcional da permissao (enum `PermissionScreenEnum`).
