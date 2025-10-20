<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], 
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $ketua = User::firstOrCreate(
            ['email' => 'ketua@example.com'],
            [
                'name' => 'Ketua Tim',
                'password' => bcrypt('password'),
                'role' => 'ketua_tim',
            ]
        );

        $anggota = User::firstOrCreate(
            ['email' => 'anggota@example.com'],
            [
                'name' => 'Anggota User',
                'password' => bcrypt('password'),
                'role' => 'anggota',
            ]
        );

        $projects = [
            ['name' => 'Proyek Web App', 'description' => 'Bangun aplikasi web', 'status' => 'In Progress', 'created_by' => $admin->id],
            ['name' => 'Proyek Mobile', 'description' => 'Aplikasi mobile', 'status' => 'Planning', 'created_by' => $ketua->id],
            ['name' => 'Proyek Database', 'description' => 'Optimasi DB', 'status' => 'Completed', 'created_by' => $admin->id],
            ['name' => 'Proyek UI/UX', 'description' => 'Desain interface', 'status' => 'In Progress', 'created_by' => $ketua->id],
            ['name' => 'Proyek Testing', 'description' => 'QA testing', 'status' => 'On Hold', 'created_by' => $admin->id],
        ];

        foreach ($projects as $data) {
            Project::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        $tasks = [
            ['name' => 'Desain Database', 'status' => 'Completed', 'due_date' => '2024-01-15', 'project_id' => 1, 'assigned_to' => $anggota->id],
            ['name' => 'Implementasi API', 'status' => 'In Progress', 'due_date' => '2024-02-01', 'project_id' => 1, 'assigned_to' => $ketua->id],
            ['name' => 'Testing Unit', 'status' => 'Pending', 'due_date' => '2024-01-20', 'project_id' => 3, 'assigned_to' => $anggota->id],
            ['name' => 'Deploy Server', 'status' => 'Completed', 'due_date' => '2024-01-10', 'project_id' => 3, 'assigned_to' => $admin->id],
            ['name' => 'Review Code', 'status' => 'In Progress', 'due_date' => '2024-02-05', 'project_id' => 2, 'assigned_to' => $ketua->id],
            ['name' => 'Buat Wireframe', 'status' => 'Pending', 'due_date' => '2024-01-25', 'project_id' => 4, 'assigned_to' => $anggota->id],
            ['name' => 'Optimasi Query', 'status' => 'Completed', 'due_date' => '2024-01-12', 'project_id' => 3, 'assigned_to' => $admin->id],
            ['name' => 'Integrasi Frontend', 'status' => 'In Progress', 'due_date' => '2024-02-10', 'project_id' => 1, 'assigned_to' => $anggota->id],
            ['name' => 'Dokumentasi', 'status' => 'Pending', 'due_date' => '2024-02-15', 'project_id' => 5, 'assigned_to' => $ketua->id],
            ['name' => 'Bug Fix', 'status' => 'Completed', 'due_date' => '2024-01-18', 'project_id' => 4, 'assigned_to' => $admin->id],
        ];

        foreach ($tasks as $data) {
            Task::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        echo "Seeding completed: " . User::count() . " users, " . Project::count() . " projects, " . Task::count() . " tasks.\n";
    }
}