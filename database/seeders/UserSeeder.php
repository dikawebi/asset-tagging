<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Cached Roles & Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Permission (Contoh: untuk akses aset dan brand)
        $permissions = [
            'view_assets', 'create_assets', 'edit_assets', 'delete_assets',
            'view_brands', 'create_brands', 'edit_brands', 'delete_brands'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Buat Role Admin dan assign semua permission
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 4. Buat Role Staff (User biasa)
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->givePermissionTo(['view_assets', 'view_brands']);

        // 5. Buat User Admin
        $admin = User::firstOrCreate(
            ['email' => 'yogi.windraya@borneoprima.com'],
            [
                'name' => 'Yogi Windraya',
                'password' => Hash::make('administrator'),
            ]
        );
        $admin->assignRole($adminRole);

        $admin = User::firstOrCreate(
            ['email' => 'ali.abidin@borneoprima.com'],
            [
                'name' => 'Ali Abidin',
                'password' => Hash::make('administrator'),
            ]
        );
        $admin->assignRole($adminRole);

        $admin = User::firstOrCreate(
            ['email' => 'andika.kuswidyarto@borneoprima.com'],
            [
                'name' => 'Andika Kuswidyarto',
                'password' => Hash::make('administrator'),
            ]
        );
        $admin->assignRole($adminRole);

        $admin = User::firstOrCreate(
            ['email' => 'rio.hidayat@borneoprima.com'],
            [
                'name' => 'Rio Hidayat',
                'password' => Hash::make('administrator'),
            ]
        );
        $admin->assignRole($adminRole);

        // 6. Buat User Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@email.com'],
            [
                'name' => 'Staff',
                'password' => Hash::make('staff'),
            ]
        );
        $staff->assignRole($staffRole);
    }
}
