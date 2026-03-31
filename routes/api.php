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
});

Route::apiResource('users', UserController::class);
Route::apiResource('patients', PatientController::class);
Route::apiResource('responsibles', ResponsibleController::class);
Route::apiResource('medicines', MedicineController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('prescriptions', PrescriptionController::class);
Route::apiResource('prescription-schedules', PrescriptionSchedulesController::class);

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

Route::get('permissions/screens', [PermissionController::class, 'screens']);
Route::get('permissions/grouped', [PermissionController::class, 'grouped']);
Route::get('roles/{role}/permissions', [RoleController::class, 'permissionsByScreen']);
Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissionsByScreen']);
Route::post('roles/{role}/permissions/activate-all', [RoleController::class, 'activateAllPermissionsByScreen']);
Route::post('roles/{role}/permissions/disable-all', [RoleController::class, 'disableAllPermissionsByScreen']);

