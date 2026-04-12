<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="Back API",
 *     version="1.0.0",
 *     description="Complete API documentation with examples for requests and responses."
 * )
 * @OA\Server(
 *     url="/api",
 *     description="Local API base path"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum Token",
 *     description="Use: Bearer {token}"
 * )
 *
 * @OA\Schema(
 *     schema="DocumentTypeEnum",
 *     type="string",
 *     enum={"CPF", "CNPJ"},
 *     example="CPF",
 *     description="Supported document types"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "document_type", "document_number", "is_adm", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="string", example="01jzs43c8az2m6p7q9v3k2x1yw"),
 *     @OA\Property(property="name", type="string", example="Maria Silva"),
 *     @OA\Property(property="document_type", ref="#/components/schemas/DocumentTypeEnum"),
 *     @OA\Property(property="document_number", type="string", example="12345678900"),
 *     @OA\Property(property="is_adm", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-08T18:20:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-08T18:20:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     required={"id", "name", "guard_name", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="string", example="01jzs4v0b7d6x2y3w9a8c5m1nq"),
 *     @OA\Property(property="name", type="string", example="admin"),
 *     @OA\Property(property="guard_name", type="string", example="web"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-08T18:20:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-04-08T18:20:00.000000Z")
 * )
 *
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"document_type", "document_number", "password"},
 *     @OA\Property(property="document_type", ref="#/components/schemas/DocumentTypeEnum"),
 *     @OA\Property(property="document_number", type="string", example="12345678900"),
 *     @OA\Property(property="password", type="string", format="password", example="Pass@1234")
 * )
 *
 * @OA\Schema(
 *     schema="CreateUserRequest",
 *     type="object",
 *     required={"name", "document_type", "document_number", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Maria Silva"),
 *     @OA\Property(property="document_type", ref="#/components/schemas/DocumentTypeEnum"),
 *     @OA\Property(property="document_number", type="string", maxLength=255, example="12345678900"),
 *     @OA\Property(property="password", type="string", format="password", example="Pass@1234"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="Pass@1234")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     type="object",
 *     required={"name", "document_type", "document_number"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Maria Oliveira"),
 *     @OA\Property(property="document_type", ref="#/components/schemas/DocumentTypeEnum"),
 *     @OA\Property(property="document_number", type="string", maxLength=255, example="12345678900"),
 *     @OA\Property(property="password", type="string", format="password", nullable=true, example="NewPass@1234")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     required={"message", "errors"},
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(property="document_number", type="array", @OA\Items(type="string", example="The document number has already been taken.")),
 *         @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field confirmation does not match."))
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedMessage",
 *     type="object",
 *     required={"message"},
 *     @OA\Property(property="message", type="string", example="Unauthenticated.")
 * )
 *
 * @OA\Schema(
 *     schema="AuthSuccessResponse",
 *     type="object",
 *     required={"message", "data"},
 *     @OA\Property(property="message", type="string", example="Login successfully"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         required={"user", "token", "token_type"},
 *         @OA\Property(property="user", ref="#/components/schemas/User"),
 *         @OA\Property(property="token", type="string", example="1|hV5k...sanctum_token..."),
 *         @OA\Property(property="token_type", type="string", example="Bearer")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedUsersResponse",
 *     type="object",
 *     required={"data", "links", "meta"},
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 *     @OA\Property(
 *         property="links",
 *         type="object",
 *         @OA\Property(property="first", type="string", nullable=true, example="http://localhost/api/users?page=1"),
 *         @OA\Property(property="last", type="string", nullable=true, example="http://localhost/api/users?page=3"),
 *         @OA\Property(property="prev", type="string", nullable=true, example=null),
 *         @OA\Property(property="next", type="string", nullable=true, example="http://localhost/api/users?page=2")
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=3),
 *         @OA\Property(property="path", type="string", example="http://localhost/api/users"),
 *         @OA\Property(property="per_page", type="integer", example=10),
 *         @OA\Property(property="to", type="integer", example=10),
 *         @OA\Property(property="total", type="integer", example=25)
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedRolesResponse",
 *     type="object",
 *     required={"data", "links", "meta"},
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Role")),
 *     @OA\Property(
 *         property="links",
 *         type="object",
 *         @OA\Property(property="first", type="string", nullable=true, example="http://localhost/api/users/01jzs43c8az2m6p7q9v3k2x1yw/roles?page=1"),
 *         @OA\Property(property="last", type="string", nullable=true, example="http://localhost/api/users/01jzs43c8az2m6p7q9v3k2x1yw/roles?page=1"),
 *         @OA\Property(property="prev", type="string", nullable=true, example=null),
 *         @OA\Property(property="next", type="string", nullable=true, example=null)
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=1),
 *         @OA\Property(property="path", type="string", example="http://localhost/api/users/01jzs43c8az2m6p7q9v3k2x1yw/roles"),
 *         @OA\Property(property="per_page", type="integer", example=10),
 *         @OA\Property(property="to", type="integer", example=1),
 *         @OA\Property(property="total", type="integer", example=1)
 *     )
 * )
 *
 * @OA\Response(
 *     response="UnauthorizedResponse",
 *     description="Unauthenticated",
 *     @OA\JsonContent(ref="#/components/schemas/UnauthorizedMessage")
 * )
 *
 * @OA\Response(
 *     response="ValidationErrorResponse",
 *     description="Validation error",
 *     @OA\JsonContent(ref="#/components/schemas/ValidationError")
 * )
 *
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Users", description="User controller endpoints")
 * @OA\Tag(name="Patients", description="Patient controller endpoints")
 * @OA\Tag(name="Responsibles", description="Responsible controller endpoints")
 * @OA\Tag(name="Medicines", description="Medicine controller endpoints")
 * @OA\Tag(name="Roles", description="Role controller endpoints")
 * @OA\Tag(name="Prescriptions", description="Prescription controller endpoints")
 * @OA\Tag(name="Prescription Schedules", description="Prescription schedule controller endpoints")
 * @OA\Tag(name="Permissions", description="Permission controller endpoints")
 *
 * @OA\Post(
 *     path="/auth/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     description="Creates a user and returns an access token.",
 *     operationId="authRegister",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created",
 *         @OA\JsonContent(ref="#/components/schemas/AuthSuccessResponse")
 *     ),
 *     @OA\Response(response=422, ref="#/components/responses/ValidationErrorResponse")
 * )
 *
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Auth"},
 *     summary="Authenticate with document and password",
 *     description="Returns a Sanctum Bearer token on success.",
 *     operationId="authLogin",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated",
 *         @OA\JsonContent(ref="#/components/schemas/AuthSuccessResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Invalid credentials")
 *         )
 *     ),
 *     @OA\Response(response=422, ref="#/components/responses/ValidationErrorResponse")
 * )
 *
 * @OA\Post(
 *     path="/auth/logout",
 *     tags={"Auth"},
 *     summary="Logout current token",
 *     description="Revokes the current access token.",
 *     operationId="authLogout",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logged out",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Logged out successfully")
 *         )
 *     ),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse")
 * )
 *
 * @OA\Get(
 *     path="/user",
 *     tags={"Auth"},
 *     summary="Get authenticated user",
 *     operationId="authUser",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated user",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse")
 * )
 *
 * @OA\Get(
 *     path="/users",
 *     tags={"Users"},
 *     summary="List users",
 *     description="Returns paginated users (10 items per page by default).",
 *     operationId="usersIndex",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", minimum=1, example=1)),
 *     @OA\Response(response=200, description="Users list", @OA\JsonContent(ref="#/components/schemas/PaginatedUsersResponse")),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse")
 * )
 *
 * @OA\Post(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Create user",
 *     description="Creates a user record.",
 *     operationId="usersStore",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")),
 *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=422, ref="#/components/responses/ValidationErrorResponse")
 * )
 *
 * @OA\Get(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Show user",
 *     operationId="usersShow",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User ULID", @OA\Schema(type="string", example="01jzs43c8az2m6p7q9v3k2x1yw")),
 *     @OA\Response(response=200, description="User found", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=404, description="User not found")
 * )
 *
 * @OA\Put(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Update user",
 *     operationId="usersUpdate",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User ULID", @OA\Schema(type="string", example="01jzs43c8az2m6p7q9v3k2x1yw")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")),
 *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=422, ref="#/components/responses/ValidationErrorResponse"),
 *     @OA\Response(response=404, description="User not found")
 * )
 *
 * @OA\Delete(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Delete user",
 *     operationId="usersDestroy",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User ULID", @OA\Schema(type="string", example="01jzs43c8az2m6p7q9v3k2x1yw")),
 *     @OA\Response(response=204, description="No content"),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=404, description="User not found")
 * )
 *
 * @OA\Get(
 *     path="/users/{user}/roles",
 *     tags={"Users"},
 *     summary="List user roles",
 *     operationId="usersRoles",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User ULID", @OA\Schema(type="string", example="01jzs43c8az2m6p7q9v3k2x1yw")),
 *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", minimum=1, example=1)),
 *     @OA\Response(response=200, description="User roles", @OA\JsonContent(ref="#/components/schemas/PaginatedRolesResponse")),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=404, description="User not found")
 * )
 *
 * @OA\Post(
 *     path="/users/{user}/roles/{role}",
 *     tags={"Users"},
 *     summary="Attach role to user",
 *     description="Adds a role without removing existing ones.",
 *     operationId="usersAttachRole",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User ULID", @OA\Schema(type="string", example="01jzs43c8az2m6p7q9v3k2x1yw")),
 *     @OA\Parameter(name="role", in="path", required=true, description="Role ULID", @OA\Schema(type="string", example="01jzs4v0b7d6x2y3w9a8c5m1nq")),
 *     @OA\Response(response=204, description="No content"),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=404, description="User or role not found")
 * )
 *
 * @OA\Delete(
 *     path="/users/{user}/roles/{role}",
 *     tags={"Users"},
 *     summary="Detach role from user",
 *     operationId="usersDetachRole",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User ULID", @OA\Schema(type="string", example="01jzs43c8az2m6p7q9v3k2x1yw")),
 *     @OA\Parameter(name="role", in="path", required=true, description="Role ULID", @OA\Schema(type="string", example="01jzs4v0b7d6x2y3w9a8c5m1nq")),
 *     @OA\Response(response=204, description="No content"),
 *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
 *     @OA\Response(response=404, description="User or role not found")
 * )
 * @OA\Get(path="/users/trashed", tags={"Users"}, summary="List soft-deleted users", operationId="usersTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/users/{user}/restore", tags={"Users"}, summary="Restore soft-deleted user", operationId="usersRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/users/{user}/force-delete", tags={"Users"}, summary="Force delete user", operationId="usersForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/patients", tags={"Patients"}, summary="List patients", operationId="patientsIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/patients", tags={"Patients"}, summary="Create patient", operationId="patientsStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/patients/{patient}", tags={"Patients"}, summary="Show patient", operationId="patientsShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/patients/{patient}", tags={"Patients"}, summary="Update patient", operationId="patientsUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/patients/{patient}", tags={"Patients"}, summary="Delete patient", operationId="patientsDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/patients/{patient}/responsibles", tags={"Patients"}, summary="List patient responsibles", operationId="patientsResponsibles", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/patients/{patient}/responsibles/{responsible}", tags={"Patients"}, summary="Attach responsible", operationId="patientsAttachResponsible", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Delete(path="/patients/{patient}/responsibles/{responsible}", tags={"Patients"}, summary="Detach responsible", operationId="patientsDetachResponsible", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/patients/{patient}/prescriptions", tags={"Patients"}, summary="List patient prescriptions", operationId="patientsPrescriptions", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/patients/{patient}/prescriptions", tags={"Patients"}, summary="Create patient prescription", operationId="patientsStorePrescription", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/patients/trashed", tags={"Patients"}, summary="List soft-deleted patients", operationId="patientsTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/patients/{patient}/restore", tags={"Patients"}, summary="Restore soft-deleted patient", operationId="patientsRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/patients/{patient}/force-delete", tags={"Patients"}, summary="Force delete patient", operationId="patientsForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/responsibles", tags={"Responsibles"}, summary="List responsibles", operationId="responsiblesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/responsibles", tags={"Responsibles"}, summary="Create responsible", operationId="responsiblesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/responsibles/{responsible}", tags={"Responsibles"}, summary="Show responsible", operationId="responsiblesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/responsibles/{responsible}", tags={"Responsibles"}, summary="Update responsible", operationId="responsiblesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/responsibles/{responsible}", tags={"Responsibles"}, summary="Delete responsible", operationId="responsiblesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/responsibles/{responsible}/patients", tags={"Responsibles"}, summary="List responsible patients", operationId="responsiblesPatients", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/responsibles/{responsible}/patients/{patient}", tags={"Responsibles"}, summary="Attach patient", operationId="responsiblesAttachPatient", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Delete(path="/responsibles/{responsible}/patients/{patient}", tags={"Responsibles"}, summary="Detach patient", operationId="responsiblesDetachPatient", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/responsibles/trashed", tags={"Responsibles"}, summary="List soft-deleted responsibles", operationId="responsiblesTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/responsibles/{responsible}/restore", tags={"Responsibles"}, summary="Restore soft-deleted responsible", operationId="responsiblesRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/responsibles/{responsible}/force-delete", tags={"Responsibles"}, summary="Force delete responsible", operationId="responsiblesForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/medicines", tags={"Medicines"}, summary="List medicines", operationId="medicinesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/medicines", tags={"Medicines"}, summary="Create medicine", operationId="medicinesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/medicines/{medicine}", tags={"Medicines"}, summary="Show medicine", operationId="medicinesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/medicines/{medicine}", tags={"Medicines"}, summary="Update medicine", operationId="medicinesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/medicines/{medicine}", tags={"Medicines"}, summary="Delete medicine", operationId="medicinesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/medicines/trashed", tags={"Medicines"}, summary="List soft-deleted medicines", operationId="medicinesTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/medicines/{medicine}/restore", tags={"Medicines"}, summary="Restore soft-deleted medicine", operationId="medicinesRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/medicines/{medicine}/force-delete", tags={"Medicines"}, summary="Force delete medicine", operationId="medicinesForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/roles", tags={"Roles"}, summary="List roles", operationId="rolesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/roles", tags={"Roles"}, summary="Create role", operationId="rolesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/roles/{role}", tags={"Roles"}, summary="Show role", operationId="rolesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/roles/{role}", tags={"Roles"}, summary="Update role", operationId="rolesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/roles/{role}", tags={"Roles"}, summary="Delete role", operationId="rolesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/roles/{role}/users", tags={"Roles"}, summary="List role users", operationId="rolesUsers", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/roles/{role}/users/{user}", tags={"Roles"}, summary="Attach user to role", operationId="rolesAttachUser", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Delete(path="/roles/{role}/users/{user}", tags={"Roles"}, summary="Detach user from role", operationId="rolesDetachUser", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/roles/{role}/permissions", tags={"Roles"}, summary="Permissions by screen", operationId="rolesPermissionsByScreen", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="screen", in="query", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/roles/{role}/permissions", tags={"Roles"}, summary="Sync permissions by screen", operationId="rolesSyncPermissions", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(required={"screen"}, @OA\Property(property="screen", type="string"), @OA\Property(property="permissions", type="array", @OA\Items(type="string")))), @OA\Response(response=204, description="No content"))
 * @OA\Post(path="/roles/{role}/permissions/activate-all", tags={"Roles"}, summary="Activate all permissions by screen", operationId="rolesActivateAllPermissions", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(required={"screen"}, @OA\Property(property="screen", type="string"))), @OA\Response(response=204, description="No content"))
 * @OA\Post(path="/roles/{role}/permissions/disable-all", tags={"Roles"}, summary="Disable all permissions by screen", operationId="rolesDisableAllPermissions", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(required={"screen"}, @OA\Property(property="screen", type="string"))), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/roles/trashed", tags={"Roles"}, summary="List soft-deleted roles", operationId="rolesTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/roles/{role}/restore", tags={"Roles"}, summary="Restore soft-deleted role", operationId="rolesRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/roles/{role}/force-delete", tags={"Roles"}, summary="Force delete role", operationId="rolesForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/prescriptions", tags={"Prescriptions"}, summary="List prescriptions", operationId="prescriptionsIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/prescriptions", tags={"Prescriptions"}, summary="Create prescription", operationId="prescriptionsStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/prescriptions/{prescription}", tags={"Prescriptions"}, summary="Show prescription", operationId="prescriptionsShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/prescriptions/{prescription}", tags={"Prescriptions"}, summary="Update prescription", operationId="prescriptionsUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/prescriptions/{prescription}", tags={"Prescriptions"}, summary="Delete prescription", operationId="prescriptionsDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/prescriptions/{prescription}/schedules", tags={"Prescriptions"}, summary="List prescription schedules", operationId="prescriptionsSchedules", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Get(path="/prescriptions/trashed", tags={"Prescriptions"}, summary="List soft-deleted prescriptions", operationId="prescriptionsTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/prescriptions/{prescription}/restore", tags={"Prescriptions"}, summary="Restore soft-deleted prescription", operationId="prescriptionsRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/prescriptions/{prescription}/force-delete", tags={"Prescriptions"}, summary="Force delete prescription", operationId="prescriptionsForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/prescription-schedules", tags={"Prescription Schedules"}, summary="List prescription schedules", operationId="prescriptionSchedulesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/prescription-schedules", tags={"Prescription Schedules"}, summary="Create prescription schedule", operationId="prescriptionSchedulesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/prescription-schedules/{prescription_schedule}", tags={"Prescription Schedules"}, summary="Show prescription schedule", operationId="prescriptionSchedulesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription_schedule", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/prescription-schedules/{prescription_schedule}", tags={"Prescription Schedules"}, summary="Update prescription schedule", operationId="prescriptionSchedulesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription_schedule", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/prescription-schedules/{prescription_schedule}", tags={"Prescription Schedules"}, summary="Delete prescription schedule", operationId="prescriptionSchedulesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription_schedule", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/prescription-schedules/trashed", tags={"Prescription Schedules"}, summary="List soft-deleted prescription schedules", operationId="prescriptionSchedulesTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/prescription-schedules/{prescriptionSchedule}/restore", tags={"Prescription Schedules"}, summary="Restore soft-deleted prescription schedule", operationId="prescriptionSchedulesRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescriptionSchedule", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/prescription-schedules/{prescriptionSchedule}/force-delete", tags={"Prescription Schedules"}, summary="Force delete prescription schedule", operationId="prescriptionSchedulesForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescriptionSchedule", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/permissions", tags={"Permissions"}, summary="List permissions", operationId="permissionsIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Get(path="/permissions/{permission}", tags={"Permissions"}, summary="Show permission", operationId="permissionsShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="permission", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Delete(path="/permissions/{permission}", tags={"Permissions"}, summary="Delete permission", operationId="permissionsDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="permission", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/permissions/trashed", tags={"Permissions"}, summary="List soft-deleted permissions", operationId="permissionsTrashed", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/permissions/{permission}/restore", tags={"Permissions"}, summary="Restore soft-deleted permission", operationId="permissionsRestore", security={{"bearerAuth":{}}}, @OA\Parameter(name="permission", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="Restored"))
 * @OA\Delete(path="/permissions/{permission}/force-delete", tags={"Permissions"}, summary="Force delete permission", operationId="permissionsForceDelete", security={{"bearerAuth":{}}}, @OA\Parameter(name="permission", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/permissions/screens", tags={"Permissions"}, summary="List permission screens", operationId="permissionsScreens", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Get(path="/permissions/grouped", tags={"Permissions"}, summary="List grouped permissions", operationId="permissionsGrouped", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 */
class ApiDocumentation
{
}
