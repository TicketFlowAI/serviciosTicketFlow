<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'client',
            'technician',
            'super-admin',
            '1',
            '2',
            '3'
        ];

        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }

        // Create permissions
        $permissions = [
            'view-companies', 'create-companies', 'edit-companies', 'delete-companies', 'view-deleted-companies', 'restore-companies', 'view-company-users',
            'view-services', 'create-services', 'edit-services', 'delete-services', 'view-deleted-services', 'restore-services',
            'view-taxes', 'create-taxes', 'edit-taxes', 'delete-taxes', 'view-deleted-taxes', 'restore-taxes',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories', 'view-deleted-categories', 'restore-categories',
            'view-service-terms', 'create-service-terms', 'edit-service-terms', 'delete-service-terms', 'view-deleted-service-terms', 'restore-service-terms',
            'view-service-contracts', 'create-service-contracts', 'edit-service-contracts', 'delete-service-contracts', 'view-deleted-service-contracts', 'restore-service-contracts',
            'view-tickets', 'create-tickets', 'edit-tickets', 'delete-tickets', 'view-deleted-tickets', 'restore-tickets', 'close-tickets', 'reassign-tickets', 'open-tickets', 'mark-needs-human-interaction', 'view-ticket-history',
            'view-messages', 'create-messages', 'edit-messages', 'delete-messages',
            'view-users', 'create-users', 'edit-users', 'delete-users', 'view-authenticated-user', 'view-deleted-users', 'restore-users', 'view-users-by-role',
            'view-emails', 'create-emails', 'edit-emails', 'delete-emails', 'view-deleted-emails', 'restore-emails',
            'view-intervals', 'create-intervals', 'edit-intervals', 'delete-intervals', 'view-deleted-intervals', 'restore-intervals',
            'view-roles', 'edit-roles', 'view-permissions',
            'view-reports', 'view-technician-reports',
            'create-surveys', 'edit-surveys', 'delete-surveys',
            'view-survey-questions', 'create-survey-questions', 'edit-survey-questions', 'delete-survey-questions', 'view-deleted-survey-questions', 'restore-survey-questions', 'view-all-survey-questions',
            'disable-two-factor-authentication',
            'request-service-contracts',
            'cancel-service-contracts' 
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Assign permissions to roles
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $technicianRole = Role::where('name', 'technician')->first();
        $clientRole = Role::where('name', 'client')->first();

        $superAdminRole->givePermissionTo($permissions);

        $technicianPermissions = array_filter($permissions, function($permission) {
            return !str_contains($permission, 'delete') && !str_contains($permission, 'view-reports') && !str_contains($permission, 'delete-surveys') && !str_contains($permission, 'edit-surveys') && !str_contains($permission, 'create-survey-questions') && !str_contains($permission, 'edit-survey-questions') && !str_contains($permission, 'delete-survey-questions') && !str_contains($permission, 'view-deleted-survey-questions') && !str_contains($permission, 'restore-survey-questions') && !str_contains($permission, 'view-all-survey-questions') && !str_contains($permission, 'disable-two-factor-authentication');
        });

        $technicianPermissions[] = 'view-survey-questions';
        $technicianPermissions[] = 'request-service-contracts';
        $technicianPermissions[] = 'cancel-service-contracts'; 

        $technicianRole->givePermissionTo($technicianPermissions);

        $clientPermissions = [
            'view-companies', 'edit-companies',
            'view-service-contracts',
            'view-tickets', 'create-tickets', 'edit-tickets', 'close-tickets', 'open-tickets', 'mark-needs-human-interaction', 'view-ticket-history',
            'view-messages', 'create-messages',
            'view-users', 'create-users', 'edit-users', 'view-authenticated-user',
            'create-surveys','view-services',
            'view-survey-questions',
            'request-service-contracts',
            'cancel-service-contracts',
            'view-services'
        ];

        $clientRole->givePermissionTo($clientPermissions);
    }
}
