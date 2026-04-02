<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="Back API",
 *     version="1.0.0",
 *     description="Swagger documentation for all API controllers."
 * )
 * @OA\Server(
 *     url="/api",
 *     description="API base path"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
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
 *     summary="Register user",
 *     operationId="authRegister",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","document_type","document_number","password","password_confirmation"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="document_type", type="string"),
 *             @OA\Property(property="document_number", type="string"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="password_confirmation", type="string")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Created"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Post(
 *     path="/auth/login",
 *     tags={"Auth"},
 *     summary="Login",
 *     operationId="authLogin",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"document_type","document_number","password"},
 *             @OA\Property(property="document_type", type="string"),
 *             @OA\Property(property="document_number", type="string"),
 *             @OA\Property(property="password", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Authenticated"),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 *
 * @OA\Get(
 *     path="/user",
 *     tags={"Auth"},
 *     summary="Authenticated user",
 *     operationId="authUser",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="OK"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(path="/users", tags={"Users"}, summary="List users", operationId="usersIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Create user",
 *     operationId="usersStore",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
 *     @OA\Response(response=201, description="Created")
 * )
 * @OA\Get(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Show user",
 *     operationId="usersShow",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="OK")
 * )
 * @OA\Put(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Update user",
 *     operationId="usersUpdate",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
 *     @OA\Response(response=200, description="Updated")
 * )
 * @OA\Delete(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Delete user",
 *     operationId="usersDestroy",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=204, description="No content")
 * )
 * @OA\Get(
 *     path="/users/{user}/roles",
 *     tags={"Users"},
 *     summary="List user roles",
 *     operationId="usersRoles",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="OK")
 * )
 * @OA\Post(
 *     path="/users/{user}/roles/{role}",
 *     tags={"Users"},
 *     summary="Attach role to user",
 *     operationId="usersAttachRole",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=204, description="No content")
 * )
 * @OA\Delete(
 *     path="/users/{user}/roles/{role}",
 *     tags={"Users"},
 *     summary="Detach role from user",
 *     operationId="usersDetachRole",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Parameter(name="role", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=204, description="No content")
 * )
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
 *
 * @OA\Get(path="/responsibles", tags={"Responsibles"}, summary="List responsibles", operationId="responsiblesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/responsibles", tags={"Responsibles"}, summary="Create responsible", operationId="responsiblesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/responsibles/{responsible}", tags={"Responsibles"}, summary="Show responsible", operationId="responsiblesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/responsibles/{responsible}", tags={"Responsibles"}, summary="Update responsible", operationId="responsiblesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/responsibles/{responsible}", tags={"Responsibles"}, summary="Delete responsible", operationId="responsiblesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/responsibles/{responsible}/patients", tags={"Responsibles"}, summary="List responsible patients", operationId="responsiblesPatients", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/responsibles/{responsible}/patients/{patient}", tags={"Responsibles"}, summary="Attach patient", operationId="responsiblesAttachPatient", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Delete(path="/responsibles/{responsible}/patients/{patient}", tags={"Responsibles"}, summary="Detach patient", operationId="responsiblesDetachPatient", security={{"bearerAuth":{}}}, @OA\Parameter(name="responsible", in="path", required=true, @OA\Schema(type="string")), @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/medicines", tags={"Medicines"}, summary="List medicines", operationId="medicinesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/medicines", tags={"Medicines"}, summary="Create medicine", operationId="medicinesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/medicines/{medicine}", tags={"Medicines"}, summary="Show medicine", operationId="medicinesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/medicines/{medicine}", tags={"Medicines"}, summary="Update medicine", operationId="medicinesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/medicines/{medicine}", tags={"Medicines"}, summary="Delete medicine", operationId="medicinesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="medicine", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
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
 *
 * @OA\Get(path="/prescriptions", tags={"Prescriptions"}, summary="List prescriptions", operationId="prescriptionsIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/prescriptions", tags={"Prescriptions"}, summary="Create prescription", operationId="prescriptionsStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/prescriptions/{prescription}", tags={"Prescriptions"}, summary="Show prescription", operationId="prescriptionsShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/prescriptions/{prescription}", tags={"Prescriptions"}, summary="Update prescription", operationId="prescriptionsUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/prescriptions/{prescription}", tags={"Prescriptions"}, summary="Delete prescription", operationId="prescriptionsDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 * @OA\Get(path="/prescriptions/{prescription}/schedules", tags={"Prescriptions"}, summary="List prescription schedules", operationId="prescriptionsSchedules", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 *
 * @OA\Get(path="/prescription-schedules", tags={"Prescription Schedules"}, summary="List prescription schedules", operationId="prescriptionSchedulesIndex", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Post(path="/prescription-schedules", tags={"Prescription Schedules"}, summary="Create prescription schedule", operationId="prescriptionSchedulesStore", security={{"bearerAuth":{}}}, @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=201, description="Created"))
 * @OA\Get(path="/prescription-schedules/{prescription_schedule}", tags={"Prescription Schedules"}, summary="Show prescription schedule", operationId="prescriptionSchedulesShow", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription_schedule", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=200, description="OK"))
 * @OA\Put(path="/prescription-schedules/{prescription_schedule}", tags={"Prescription Schedules"}, summary="Update prescription schedule", operationId="prescriptionSchedulesUpdate", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription_schedule", in="path", required=true, @OA\Schema(type="string")), @OA\RequestBody(required=true, @OA\JsonContent(type="object")), @OA\Response(response=200, description="Updated"))
 * @OA\Delete(path="/prescription-schedules/{prescription_schedule}", tags={"Prescription Schedules"}, summary="Delete prescription schedule", operationId="prescriptionSchedulesDestroy", security={{"bearerAuth":{}}}, @OA\Parameter(name="prescription_schedule", in="path", required=true, @OA\Schema(type="string")), @OA\Response(response=204, description="No content"))
 *
 * @OA\Get(path="/permissions/screens", tags={"Permissions"}, summary="List permission screens", operationId="permissionsScreens", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 * @OA\Get(path="/permissions/grouped", tags={"Permissions"}, summary="List grouped permissions", operationId="permissionsGrouped", security={{"bearerAuth":{}}}, @OA\Response(response=200, description="OK"))
 */
class ApiDocumentation
{
}
