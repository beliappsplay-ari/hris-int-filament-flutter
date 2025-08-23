<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimesheetPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Permission data with guard_name for Spatie Laravel Permission package
            $permissions = [
                [
                    'name' => 'view_timesheet',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'view_all_timesheets',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'export_timesheet',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($permissions as $permission) {
                // Check if permission already exists
                $exists = DB::table('permissions')
                    ->where('name', $permission['name'])
                    ->where('guard_name', $permission['guard_name'])
                    ->exists();
                
                if (!$exists) {
                    DB::table('permissions')->insert($permission);
                    $this->command->info("✅ Created permission: {$permission['name']}");
                } else {
                    $this->command->info("📋 Permission already exists: {$permission['name']}");
                }
            }

            $this->command->info('🎉 Timesheet permissions seeding completed successfully!');
            $this->command->info('📝 Note: These permissions use guard_name "web" for Spatie Laravel Permission package');

        } catch (\Exception $e) {
            $this->command->error('❌ Error seeding timesheet permissions: ' . $e->getMessage());
        }
    }
}