# API

## Autenticacao
- Login: `POST /api/auth/login`
- Registro: `POST /api/auth/register`
- Logout: `POST /api/auth/logout` (requer token)

### Token
Use header:
`Authorization: Bearer <token>`

## Recursos Principais (CRUD)
Todos abaixo com `auth:sanctum`:
- `/api/users`
- `/api/patients`
- `/api/responsibles`
- `/api/medicines`
- `/api/roles`
- `/api/prescriptions`
- `/api/prescription-schedules`

Cada recurso possui rotas extras:
- `GET /trashed`
- `POST /{id}/restore`
- `DELETE /{id}/force-delete`

## Relacionamentos de Dominio (Rotas)
- Paciente x Responsavel
  - `POST /api/patients/{patient}/responsibles/{responsible}`
  - `DELETE /api/patients/{patient}/responsibles/{responsible}`
  - `GET /api/patients/{patient}/responsibles`
- Prescricoes por paciente
  - `GET /api/patients/{patient}/prescriptions`
  - `POST /api/patients/{patient}/prescriptions`
- Agendas por prescricao
  - `GET /api/prescriptions/{prescription}/schedules`
- Usuario x Papel
  - `GET /api/users/{user}/roles`
  - `POST /api/users/{user}/roles/{role}`
  - `DELETE /api/users/{user}/roles/{role}`
- Papel x Usuario
  - `GET /api/roles/{role}/users`
  - `POST /api/roles/{role}/users/{user}`
  - `DELETE /api/roles/{role}/users/{user}`
- Papel x Permissao por tela
  - `GET /api/roles/{role}/permissions?screen=...`
  - `PUT /api/roles/{role}/permissions`
  - `POST /api/roles/{role}/permissions/activate-all`
  - `POST /api/roles/{role}/permissions/disable-all`

## Paginacao
Listagens usam pagina padrao com tamanho 10 em diversos controllers (`paginate(10)`).

## Erros Comuns
- 401: nao autenticado em rotas protegidas.
- 404: registro nao encontrado (`findOrFail`).
- 422: falha de validacao.

## Referencia OpenAPI
- Anotacoes centrais: `app/OpenApi/ApiDocumentation.php`
- UI Swagger: `/api/documentation`
