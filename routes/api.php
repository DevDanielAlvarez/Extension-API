<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionSchedulesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ResponsibleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('users/trashed', [UserController::class, 'trashed']);
    Route::post('users/{user}/restore', [UserController::class, 'restore']);
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete']);
    Route::apiResource('users', UserController::class);

    Route::get('patients/trashed', [PatientController::class, 'trashed']);
    Route::post('patients/{patient}/restore', [PatientController::class, 'restore']);
    Route::delete('patients/{patient}/force-delete', [PatientController::class, 'forceDelete']);
    Route::apiResource('patients', PatientController::class);

    Route::get('responsibles/trashed', [ResponsibleController::class, 'trashed']);
    Route::post('responsibles/{responsible}/restore', [ResponsibleController::class, 'restore']);
    Route::delete('responsibles/{responsible}/force-delete', [ResponsibleController::class, 'forceDelete']);
    Route::apiResource('responsibles', ResponsibleController::class);

    Route::get('medicines/trashed', [MedicineController::class, 'trashed']);
    Route::post('medicines/{medicine}/restore', [MedicineController::class, 'restore']);
    Route::delete('medicines/{medicine}/force-delete', [MedicineController::class, 'forceDelete']);
    Route::apiResource('medicines', MedicineController::class);

    Route::get('roles/trashed', [RoleController::class, 'trashed']);
    Route::post('roles/{role}/restore', [RoleController::class, 'restore']);
    Route::delete('roles/{role}/force-delete', [RoleController::class, 'forceDelete']);
    Route::apiResource('roles', RoleController::class);

    Route::get('prescriptions/trashed', [PrescriptionController::class, 'trashed']);
    Route::post('prescriptions/{prescription}/restore', [PrescriptionController::class, 'restore']);
    Route::delete('prescriptions/{prescription}/force-delete', [PrescriptionController::class, 'forceDelete']);
    Route::apiResource('prescriptions', PrescriptionController::class);

    Route::get('prescription-schedules/trashed', [PrescriptionSchedulesController::class, 'trashed']);
    Route::post('prescription-schedules/{prescriptionSchedule}/restore', [PrescriptionSchedulesController::class, 'restore']);
    Route::delete('prescription-schedules/{prescriptionSchedule}/force-delete', [PrescriptionSchedulesController::class, 'forceDelete']);
    Route::apiResource('prescription-schedules', PrescriptionSchedulesController::class);

    Route::get('permissions', [PermissionController::class, 'index']);
    Route::get('permissions/trashed', [PermissionController::class, 'trashed']);
    Route::get('permissions/screens', [PermissionController::class, 'screens']);
    Route::get('permissions/grouped', [PermissionController::class, 'grouped']);
    Route::get('permissions/{permission}', [PermissionController::class, 'show']);
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy']);
    Route::post('permissions/{permission}/restore', [PermissionController::class, 'restore']);
    Route::delete('permissions/{permission}/force-delete', [PermissionController::class, 'forceDelete']);

    Route::post('patients/{patient}/responsibles/{responsible}', [PatientController::class, 'attachResponsible']);
    Route::post('responsibles/{responsible}/patients/{patient}', [ResponsibleController::class, 'attachPatient']);
    Route::delete('patients/{patient}/responsibles/{responsible}', [PatientController::class, 'detachResponsible']);
    Route::delete('responsibles/{responsible}/patients/{patient}', [ResponsibleController::class, 'detachPatient']);

    Route::get('patients/{patient}/responsibles', [PatientController::class, 'responsibles']);
    Route::get('responsibles/{responsible}/patients', [ResponsibleController::class, 'patients']);

    Route::get('patients/{patient}/prescriptions', [PatientController::class, 'prescriptions']);
    Route::post('patients/{patient}/prescriptions', [PatientController::class, 'storePrescription']);
    Route::get('prescriptions/{prescription}/schedules', [PrescriptionController::class, 'schedules']);

    Route::get('users/{user}/roles', [UserController::class, 'roles']);
    Route::post('users/{user}/roles/{role}', [UserController::class, 'attachRole']);
    Route::delete('users/{user}/roles/{role}', [UserController::class, 'detachRole']);

    Route::get('roles/{role}/users', [RoleController::class, 'users']);
    Route::post('roles/{role}/users/{user}', [RoleController::class, 'attachUser']);
    Route::delete('roles/{role}/users/{user}', [RoleController::class, 'detachUser']);

    Route::get('roles/{role}/permissions', [RoleController::class, 'permissionsByScreen']);
    Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissionsByScreen']);
    Route::post('roles/{role}/permissions/activate-all', [RoleController::class, 'activateAllPermissionsByScreen']);
    Route::post('roles/{role}/permissions/disable-all', [RoleController::class, 'disableAllPermissionsByScreen']);
});

