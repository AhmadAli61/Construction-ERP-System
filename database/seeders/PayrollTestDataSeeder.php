<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Worker;
use App\Models\Project;
use App\Models\Attendance;
use App\Models\WorkerAdvance;
use Carbon\Carbon;

class PayrollTestDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample workers if none exist
        if (Worker::count() == 0) {
            $workers = [
                [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '1234567890',
                    'rate_type' => 'hourly',
                    'rate' => 25.00,
                    'designation' => 'Electrician',
                    'department' => 'Electrical',
                    'status' => 'active',
                    'date_of_joining' => '2024-01-01',
                ],
                [
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'phone' => '0987654321',
                    'rate_type' => 'daily',
                    'rate' => 200.00,
                    'designation' => 'Carpenter',
                    'department' => 'Woodwork',
                    'status' => 'active',
                    'date_of_joining' => '2024-01-15',
                ],
                [
                    'name' => 'Mike Johnson',
                    'email' => 'mike@example.com',
                    'phone' => '1122334455',
                    'rate_type' => 'monthly',
                    'rate' => 3500.00,
                    'designation' => 'Site Supervisor',
                    'department' => 'Management',
                    'status' => 'active',
                    'date_of_joining' => '2024-02-01',
                ],
            ];

            foreach ($workers as $workerData) {
                Worker::create($workerData);
            }
        }

        // Create sample projects
        if (Project::count() == 0) {
            $projects = [
                [
                    'name' => 'Downtown Office Building',
                    'project_code' => 'PRJ-001',
                    'client_name' => 'ABC Corporation',
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
                    'status' => 'ongoing',
                ],
                [
                    'name' => 'Residential Complex',
                    'project_code' => 'PRJ-002',
                    'client_name' => 'XYZ Developers',
                    'start_date' => '2024-02-01',
                    'end_date' => '2025-01-31',
                    'status' => 'ongoing',
                ],
            ];

            foreach ($projects as $projectData) {
                Project::create($projectData);
            }
        }

        // Create sample attendances for current month
        $workers = Worker::all();
        $projects = Project::all();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        foreach ($workers as $worker) {
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                if ($date->isWeekday() && rand(1, 10) > 2) { // 80% attendance
                    $hoursWorked = rand(7, 9);
                    $overtime = $hoursWorked > 8 ? $hoursWorked - 8 : 0;

                    Attendance::create([
                        'project_id' => $projects->random()->id,
                        'worker_id' => $worker->id,
                        'date' => $date->copy(),
                        'check_in' => '08:00:00',
                        'check_out' => sprintf('%02d:00:00', 8 + $hoursWorked),
                        'hours_worked' => $hoursWorked,
                        'overtime_hours' => $overtime,
                        'status' => 'present',
                        'payroll_generated' => false,
                    ]);
                }
            }
        }

        // Create sample advances
        foreach ($workers as $worker) {
            if (rand(1, 3) == 1) { // 33% chance of advance
                WorkerAdvance::create([
                    'worker_id' => $worker->id,
                    'amount' => rand(100, 500),
                    'remaining_amount' => rand(100, 500),
                    'advance_date' => Carbon::now()->subDays(rand(1, 15)),
                    'notes' => 'Salary advance',
                    'status' => 'pending',
                ]);
            }
        }
    }
}
