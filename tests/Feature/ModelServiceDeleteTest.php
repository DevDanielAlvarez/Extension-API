<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Prescription;
use App\Models\PrescriptionSchedule;
use App\Models\Responsible;
use App\Models\Role;
use App\Models\User;
use App\Services\Medicine\MedicineService;
use App\Services\Patient\PatientService;
use App\Services\Permission\PermissionService;
use App\Services\Prescription\PrescriptionService;
use App\Services\PrescriptionSchedule\PrescriptionScheduleService;
use App\Services\Responsible\ResponsibleService;
use App\Services\Role\RoleService;
use App\Services\User\UserService;

describe('Model service delete', function () {
    it('soft deletes user through UserService', function () {
        $user = User::factory()->create();

        UserService::find($user->id)->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    });

    it('soft deletes patient through PatientService', function () {
        $patient = Patient::factory()->create();

        PatientService::find($patient->id)->delete();

        $this->assertSoftDeleted('patients', ['id' => $patient->id]);
    });

    it('soft deletes responsible through ResponsibleService', function () {
        $responsible = Responsible::factory()->create();

        ResponsibleService::find($responsible->id)->delete();

        $this->assertSoftDeleted('responsibles', ['id' => $responsible->id]);
    });

    it('soft deletes medicine through MedicineService', function () {
        $medicine = Medicine::factory()->create();

        MedicineService::find($medicine->id)->delete();

        $this->assertSoftDeleted('medicines', ['id' => $medicine->id]);
    });

    it('soft deletes role through RoleService', function () {
        $role = Role::factory()->create();

        RoleService::find($role->id)->delete();

        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    });

    it('soft deletes permission through PermissionService', function () {
        $permission = Permission::factory()->create();

        PermissionService::find($permission->id)->delete();

        $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
    });

    it('soft deletes prescription through PrescriptionService', function () {
        $prescription = Prescription::factory()->create();

        PrescriptionService::find($prescription->id)->delete();

        $this->assertSoftDeleted('prescriptions', ['id' => $prescription->id]);
    });

    it('soft deletes prescription schedule through PrescriptionScheduleService', function () {
        $schedule = PrescriptionSchedule::factory()->create();

        PrescriptionScheduleService::find($schedule->id)->delete();

        $this->assertSoftDeleted('prescription_schedules', ['id' => $schedule->id]);
    });
});
