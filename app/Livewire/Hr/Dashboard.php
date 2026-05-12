<?php
// app/Livewire/Hr/Dashboard.php

namespace App\Livewire\Hr;

use Livewire\Component;
use App\Models\Worker;
use App\Models\Project;
use App\Models\Attendance;
use App\Models\WorkerAdvance;
use App\Models\PayrollBatch;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $dateRange = 'current_month';
    
    public $workerStats = [];
    public $attendanceStats = [];
    public $payrollStats = [];
    public $projectStats = [];
    public $advanceStats = [];
    public $recentActivities = [];
    public $attendanceTrend = [];
    public $departmentDistribution = [];
    public $upcomingDeadlines = [];
    public $medicalExpiringWorkers = [];
    public $workersWithHighAdvances = [];
    public $topPerformers = [];
    public $weeklyAttendance = [];
    public $quickStats = [];
    public $medicalStatus = [];
    
    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->loadDashboardData();
    }
    
    public function updatedDateRange()
    {
        if ($this->dateRange === 'current_month') {
            $this->selectedMonth = Carbon::now()->month;
            $this->selectedYear = Carbon::now()->year;
        } elseif ($this->dateRange === 'previous_month') {
            $date = Carbon::now()->subMonth();
            $this->selectedMonth = $date->month;
            $this->selectedYear = $date->year;
        }
        $this->loadDashboardData();
    }
    
    public function updatedSelectedMonth()
    {
        $this->loadDashboardData();
    }
    
    public function updatedSelectedYear()
    {
        $this->loadDashboardData();
    }
    
    public function loadDashboardData()
    {
        try {
            $this->loadQuickStats();
            $this->loadMedicalStatus();
            $this->loadWorkerStats();
            $this->loadAttendanceStats();
            $this->loadPayrollStats();
            $this->loadProjectStats();
            $this->loadAdvanceStats();
            $this->loadRecentActivities();
            $this->loadAttendanceTrend();
            $this->loadDepartmentDistribution();
            $this->loadUpcomingDeadlines();
            $this->loadAlerts();
            $this->loadTopPerformers();
            $this->loadWeeklyAttendance();
        } catch (\Exception $e) {
            \Log::error('Dashboard loading error: ' . $e->getMessage());
            session()->flash('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }
    
    private function loadQuickStats()
    {
        // Total Workers
        $totalWorkers = Worker::count();
        $activeWorkers = Worker::where('status', 'active')->count();
        $inactiveWorkers = Worker::where('status', 'inactive')->count();
        $terminatedWorkers = Worker::where('status', 'terminated')->count();

        // Active Projects
        $activeProjects = Project::where('status', 'ongoing')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $planningProjects = Project::where('status', 'planning')->count();

        // Current Month Attendance
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        $currentMonthAttendance = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->count();
        
        $totalAttendanceDays = Attendance::whereBetween('date', [$startDate, $endDate])->count();
        
        $attendanceRate = $totalAttendanceDays > 0 
            ? round(($currentMonthAttendance / $totalAttendanceDays) * 100, 1) 
            : 0;

        // Pending Advances
        $totalPendingAdvances = WorkerAdvance::where('status', 'pending')
            ->where('remaining_amount', '>', 0)
            ->sum('remaining_amount');
        
        $workersWithAdvances = WorkerAdvance::where('status', 'pending')
            ->where('remaining_amount', '>', 0)
            ->distinct('worker_id')
            ->count('worker_id');

        // Payroll Stats for current month
        $currentMonthPayroll = PayrollBatch::where('year', $this->selectedYear)
            ->where('month', $this->selectedMonth)
            ->first();
        
        $totalPayrollAmount = 0;
        $payrollWorkersCount = 0;
        if ($currentMonthPayroll) {
            $totalPayrollAmount = $currentMonthPayroll->payrolls()->sum('net_amount');
            $payrollWorkersCount = $currentMonthPayroll->payrolls()->count();
        }

        // Today's attendance
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('date', $today)->get();
        $todayPresent = $todayAttendance->where('status', 'present')->count();
        $todayHalfDay = $todayAttendance->where('status', 'half_day')->count();
        $todayAbsent = max(0, $activeWorkers - ($todayPresent + $todayHalfDay));

        $this->quickStats = [
            'total_workers' => $totalWorkers,
            'active_workers' => $activeWorkers,
            'inactive_workers' => $inactiveWorkers,
            'terminated_workers' => $terminatedWorkers,
            'active_projects' => $activeProjects,
            'completed_projects' => $completedProjects,
            'planning_projects' => $planningProjects,
            'attendance_rate' => $attendanceRate,
            'attendance_days' => $currentMonthAttendance,
            'total_attendance_days' => $totalAttendanceDays,
            'pending_advances_total' => $totalPendingAdvances,
            'workers_with_advances' => $workersWithAdvances,
            'total_payroll_amount' => $totalPayrollAmount,
            'payroll_workers_count' => $payrollWorkersCount,
            'today_present' => $todayPresent,
            'today_half_day' => $todayHalfDay,
            'today_absent' => $todayAbsent,
        ];
    }
    
    private function loadMedicalStatus()
    {
        $totalWorkers = Worker::where('status', 'active')->count();
        
        $validMedical = Worker::where('status', 'active')
            ->where('medical_expiry_date', '>=', Carbon::now())
            ->count();
        
        $expiredMedical = Worker::where('status', 'active')
            ->where('medical_expiry_date', '<', Carbon::now())
            ->whereNotNull('medical_expiry_date')
            ->count();
        
        $noMedical = Worker::where('status', 'active')
            ->whereNull('medical_expiry_date')
            ->count();
        
        $expiringSoon = Worker::where('status', 'active')
            ->where('medical_expiry_date', '>=', Carbon::now())
            ->where('medical_expiry_date', '<=', Carbon::now()->addDays(30))
            ->count();

        $this->medicalStatus = [
            'total_workers' => $totalWorkers,
            'valid' => $validMedical,
            'expired' => $expiredMedical,
            'not_provided' => $noMedical,
            'expiring_soon' => $expiringSoon,
            'valid_percentage' => $totalWorkers > 0 ? round(($validMedical / $totalWorkers) * 100, 1) : 0,
        ];
    }
    
    private function loadWorkerStats()
    {
        $totalWorkers = Worker::count();
        $activeWorkers = Worker::where('status', 'active')->count();
        $inactiveWorkers = Worker::where('status', 'inactive')->count();
        $terminatedWorkers = Worker::where('status', 'terminated')->count();
        
        $joinedThisMonth = Worker::whereYear('date_of_joining', $this->selectedYear)
            ->whereMonth('date_of_joining', $this->selectedMonth)
            ->count();
        
        $departmentCount = Worker::whereNotNull('department')
            ->distinct('department')
            ->count('department');
        
        $validMedical = Worker::whereNotNull('medical_expiry_date')
            ->where('medical_expiry_date', '>=', now())
            ->count();
        
        $this->workerStats = [
            'total' => $totalWorkers,
            'active' => $activeWorkers,
            'inactive' => $inactiveWorkers,
            'terminated' => $terminatedWorkers,
            'active_percentage' => $totalWorkers > 0 ? round(($activeWorkers / $totalWorkers) * 100, 1) : 0,
            'joined_this_month' => $joinedThisMonth,
            'department_count' => $departmentCount,
            'valid_medical' => $validMedical,
            'medical_percentage' => $totalWorkers > 0 ? round(($validMedical / $totalWorkers) * 100, 1) : 0,
        ];
    }
    
    private function loadAttendanceStats()
    {
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get();
        
        $totalDays = $startDate->daysInMonth;
        $totalWorkers = Worker::where('status', 'active')->count();
        $maxPossibleAttendance = $totalWorkers * $totalDays;
        
        $presentCount = $attendances->where('status', 'present')->count();
        $halfDayCount = $attendances->where('status', 'half_day')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        
        $weightedAttendance = $presentCount + ($halfDayCount * 0.5);
        $attendanceRate = $maxPossibleAttendance > 0 ? round(($weightedAttendance / $maxPossibleAttendance) * 100, 1) : 0;
        
        $totalHours = $attendances->sum('hours_worked');
        $totalOvertime = $attendances->sum('overtime_hours');
        $avgHoursPerDay = $attendances->count() > 0 ? round($totalHours / $attendances->count(), 1) : 0;
        
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('date', $today)->get();
        $todayPresent = $todayAttendance->where('status', 'present')->count();
        $todayHalfDay = $todayAttendance->where('status', 'half_day')->count();
        $todayAbsent = max(0, $totalWorkers - ($todayPresent + $todayHalfDay));
        
        $this->attendanceStats = [
            'attendance_rate' => $attendanceRate,
            'total_present' => $presentCount,
            'total_half_day' => $halfDayCount,
            'total_absent' => $absentCount,
            'total_hours' => round($totalHours, 1),
            'total_overtime' => round($totalOvertime, 1),
            'avg_hours_per_day' => $avgHoursPerDay,
            'today_present' => $todayPresent,
            'today_half_day' => $todayHalfDay,
            'today_absent' => $todayAbsent,
            'total_working_days' => $totalDays,
            'days_with_data' => $attendances->pluck('date')->unique()->count(),
            'present' => $todayPresent,
            'half_day' => $todayHalfDay,
            'absent' => $todayAbsent,
        ];
    }
    
    private function loadPayrollStats()
    {
        try {
            $currentBatch = PayrollBatch::where('year', $this->selectedYear)
                ->where('month', $this->selectedMonth)
                ->first();
            
            $totalPayrolls = Payroll::whereHas('batch', function($q) {
                $q->where('year', $this->selectedYear)->where('month', $this->selectedMonth);
            })->get();
            
            $totalGross = $totalPayrolls->sum('gross_amount');
            $totalNet = $totalPayrolls->sum('net_amount');
            $totalAdvanceDeduction = $totalPayrolls->sum('advance_deduction');
            $totalWorkersPaid = $totalPayrolls->count();
            
            $ytdPayrolls = Payroll::whereHas('batch', function($q) {
                $q->where('year', $this->selectedYear);
            })->get();
            
            $ytdGross = $ytdPayrolls->sum('gross_amount');
            $ytdNet = $ytdPayrolls->sum('net_amount');
            
            $lastMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonth();
            $lastMonthPayrolls = Payroll::whereHas('batch', function($q) use ($lastMonth) {
                $q->where('year', $lastMonth->year)->where('month', $lastMonth->month);
            })->get();
            
            $lastMonthGross = $lastMonthPayrolls->sum('gross_amount');
            $grossGrowth = $lastMonthGross > 0 ? round((($totalGross - $lastMonthGross) / $lastMonthGross) * 100, 1) : 0;
            
            $isFinalized = $currentBatch && $currentBatch->status === 'finalized';
            
            $this->payrollStats = [
                'current_batch_exists' => !is_null($currentBatch),
                'is_finalized' => $isFinalized,
                'total_gross' => $totalGross,
                'total_net' => $totalNet,
                'total_advance_deduction' => $totalAdvanceDeduction,
                'total_workers_paid' => $totalWorkersPaid,
                'ytd_gross' => $ytdGross,
                'ytd_net' => $ytdNet,
                'gross_growth' => $grossGrowth,
                'average_salary' => $totalWorkersPaid > 0 ? round($totalNet / $totalWorkersPaid, 2) : 0,
            ];
        } catch (\Exception $e) {
            $this->payrollStats = [
                'current_batch_exists' => false,
                'is_finalized' => false,
                'total_gross' => 0,
                'total_net' => 0,
                'total_advance_deduction' => 0,
                'total_workers_paid' => 0,
                'ytd_gross' => 0,
                'ytd_net' => 0,
                'gross_growth' => 0,
                'average_salary' => 0,
            ];
        }
    }
    
    private function loadProjectStats()
    {
        $activeProjects = Project::where('status', 'ongoing')->count();
        $planningProjects = Project::where('status', 'planning')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $onHoldProjects = Project::where('status', 'on_hold')->count();
        
        $totalProjects = Project::count();
        $totalContractValue = Project::sum('contract_value') ?? 0;
        
        $endingSoon = Project::where('status', 'ongoing')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays(30))
            ->count();
        
        $this->projectStats = [
            'total' => $totalProjects,
            'active' => $activeProjects,
            'planning' => $planningProjects,
            'completed' => $completedProjects,
            'on_hold' => $onHoldProjects,
            'active_percentage' => $totalProjects > 0 ? round(($activeProjects / $totalProjects) * 100, 1) : 0,
            'total_contract_value' => $totalContractValue,
            'ending_soon' => $endingSoon,
        ];
    }
    
    private function loadAdvanceStats()
    {
        try {
            $totalAdvances = WorkerAdvance::where('is_deduction', false)->sum('amount') ?? 0;
            $totalDeducted = WorkerAdvance::where('is_deduction', true)->sum('amount') ?? 0;
            $pendingAdvances = WorkerAdvance::where('status', 'pending')->where('is_deduction', false)->sum('remaining_amount') ?? 0;
            
            $workersWithAdvances = WorkerAdvance::where('is_deduction', false)
                ->distinct('worker_id')
                ->count('worker_id');
            
            $activeWorkers = Worker::where('status', 'active')->count();
            $workersWithAdvancePercentage = $activeWorkers > 0 ? round(($workersWithAdvances / $activeWorkers) * 100, 1) : 0;
            $avgAdvance = $workersWithAdvances > 0 ? round($totalAdvances / $workersWithAdvances, 2) : 0;
            
            $this->advanceStats = [
                'total_advances_given' => $totalAdvances,
                'total_deducted' => $totalDeducted,
                'pending_balance' => $pendingAdvances,
                'recovery_percentage' => $totalAdvances > 0 ? round(($totalDeducted / $totalAdvances) * 100, 1) : 0,
                'workers_with_advances' => $workersWithAdvances,
                'workers_with_advance_percentage' => $workersWithAdvancePercentage,
                'average_advance' => $avgAdvance,
                'pending_count' => WorkerAdvance::where('status', 'pending')->where('is_deduction', false)->count(),
            ];
        } catch (\Exception $e) {
            $this->advanceStats = [
                'total_advances_given' => 0,
                'total_deducted' => 0,
                'pending_balance' => 0,
                'recovery_percentage' => 0,
                'workers_with_advances' => 0,
                'workers_with_advance_percentage' => 0,
                'average_advance' => 0,
                'pending_count' => 0,
            ];
        }
    }
    
    private function loadAttendanceTrend()
    {
        $trendData = [];
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->select('date', DB::raw('COUNT(*) as total'), 
                DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status = 'half_day' THEN 1 ELSE 0 END) as half_day"),
                DB::raw("SUM(hours_worked) as hours"))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        foreach ($attendances as $att) {
            $trendData[] = [
                'date' => Carbon::parse($att->date)->format('d M'),
                'present' => $att->present,
                'half_day' => $att->half_day,
                'absent' => $att->total - ($att->present + $att->half_day),
                'hours' => round($att->hours, 1),
            ];
        }
        
        $this->attendanceTrend = $trendData;
    }
    
    private function loadWeeklyAttendance()
    {
        $weeklyData = [];
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        $weeks = ceil($startDate->diffInDays($endDate) / 7);
        
        for ($i = 0; $i < min($weeks, 4); $i++) {
            $weekStart = $startDate->copy()->addWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            if ($weekStart > $endDate) break;
            
            $attendances = Attendance::whereBetween('date', [$weekStart, $weekEnd])->get();
            $weeklyData[] = [
                'week' => 'Week ' . ($i + 1),
                'present' => $attendances->where('status', 'present')->count(),
                'hours' => round($attendances->sum('hours_worked'), 1),
            ];
        }
        
        $this->weeklyAttendance = $weeklyData;
    }
    
    private function loadDepartmentDistribution()
    {
        $this->departmentDistribution = Worker::where('status', 'active')
            ->whereNotNull('department')
            ->select('department', DB::raw('COUNT(*) as count'))
            ->groupBy('department')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
    
    private function loadTopPerformers()
    {
        try {
            $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
            
            $this->topPerformers = Attendance::whereBetween('date', [$startDate, $endDate])
                ->with('worker')
                ->select('worker_id', 
                    DB::raw('COUNT(*) as attendance_count'),
                    DB::raw('SUM(hours_worked) as total_hours'),
                    DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_days'))
                ->groupBy('worker_id')
                ->orderBy('present_days', 'desc')
                ->orderBy('total_hours', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->worker ? $item->worker->name : 'Unknown',
                        'designation' => $item->worker && $item->worker->designation ? $item->worker->designation : 'Worker',
                        'present_days' => $item->present_days,
                        'total_hours' => round($item->total_hours, 1),
                    ];
                });
        } catch (\Exception $e) {
            $this->topPerformers = collect();
        }
    }
    
    private function loadUpcomingDeadlines()
    {
        $deadlines = [];
        
        try {
            $medicalExpiring = Worker::whereNotNull('medical_expiry_date')
                ->where('medical_expiry_date', '>=', now())
                ->where('medical_expiry_date', '<=', now()->addDays(30))
                ->orderBy('medical_expiry_date')
                ->limit(3)
                ->get();
            
            foreach ($medicalExpiring as $worker) {
                $deadlines[] = [
                    'type' => 'Medical',
                    'title' => $worker->name,
                    'date' => $worker->medical_expiry_date,
                    'days_left' => Carbon::parse($worker->medical_expiry_date)->diffInDays(now()),
                ];
            }
        } catch (\Exception $e) {
            // Skip if error
        }
        
        try {
            $projectsEnding = Project::where('status', 'ongoing')
                ->whereNotNull('end_date')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(30))
                ->orderBy('end_date')
                ->limit(3)
                ->get();
            
            foreach ($projectsEnding as $project) {
                $deadlines[] = [
                    'type' => 'Project',
                    'title' => $project->name,
                    'date' => $project->end_date,
                    'days_left' => Carbon::parse($project->end_date)->diffInDays(now()),
                ];
            }
        } catch (\Exception $e) {
            // Skip if error
        }
        
        usort($deadlines, function($a, $b) {
            return $a['days_left'] - $b['days_left'];
        });
        
        $this->upcomingDeadlines = array_slice($deadlines, 0, 4);
    }
    
    private function loadAlerts()
    {
        try {
            $this->medicalExpiringWorkers = Worker::whereNotNull('medical_expiry_date')
                ->where('medical_expiry_date', '>=', now())
                ->where('medical_expiry_date', '<=', now()->addDays(30))
                ->orderBy('medical_expiry_date')
                ->get()
                ->map(function($worker) {
                    return [
                        'id' => $worker->id,
                        'name' => $worker->name,
                        'days_left' => Carbon::parse($worker->medical_expiry_date)->diffInDays(now()),
                    ];
                });
        } catch (\Exception $e) {
            $this->medicalExpiringWorkers = collect();
        }
        
        try {
            $this->workersWithHighAdvances = Worker::where('status', 'active')
                ->with(['advances' => function($q) {
                    $q->where('is_deduction', false)->where('status', 'pending');
                }])
                ->get()
                ->filter(function($worker) {
                    $pendingBalance = $worker->advances->sum('remaining_amount');
                    $monthlyEstimate = 0;
                    if ($worker->rate_type === 'monthly') {
                        $monthlyEstimate = $worker->rate ?? 0;
                    } elseif ($worker->rate_type === 'daily') {
                        $monthlyEstimate = ($worker->rate ?? 0) * 22;
                    } else {
                        $monthlyEstimate = ($worker->rate ?? 0) * 9 * 22;
                    }
                    return $pendingBalance > 0 && $monthlyEstimate > 0 && ($pendingBalance / $monthlyEstimate) > 0.5;
                })
                ->take(3)
                ->map(function($worker) {
                    return [
                        'id' => $worker->id,
                        'name' => $worker->name,
                        'pending_balance' => $worker->advances->sum('remaining_amount'),
                    ];
                });
        } catch (\Exception $e) {
            $this->workersWithHighAdvances = collect();
        }
    }
    
    private function loadRecentActivities()
    {
        $activities = [];
        
        try {
            $recentWorkers = Worker::orderBy('created_at', 'desc')->limit(2)->get();
            foreach ($recentWorkers as $worker) {
                $activities[] = [
                    'type' => 'worker',
                    'title' => 'New Worker Joined',
                    'description' => "{$worker->name} - " . ($worker->designation ?? 'Worker'),
                    'time' => $worker->created_at->diffForHumans(),
                ];
            }
        } catch (\Exception $e) {
            // Skip if error
        }
        
        try {
            $recentAttendance = Attendance::with('worker')
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();
            foreach ($recentAttendance as $att) {
                if ($att->worker) {
                    $activities[] = [
                        'type' => 'attendance',
                        'title' => 'Attendance Marked',
                        'description' => "{$att->worker->name} - " . ucfirst($att->status),
                        'time' => $att->created_at->diffForHumans(),
                    ];
                }
            }
        } catch (\Exception $e) {
            // Skip if error
        }
        
        try {
            $recentPayrolls = PayrollBatch::orderBy('created_at', 'desc')->limit(1)->get();
            foreach ($recentPayrolls as $payroll) {
                $activities[] = [
                    'type' => 'payroll',
                    'title' => 'Payroll Generated',
                    'description' => Carbon::create()->month($payroll->month)->format('F') . " {$payroll->year}",
                    'time' => $payroll->created_at->diffForHumans(),
                ];
            }
        } catch (\Exception $e) {
            // Skip if error
        }
        
        $this->recentActivities = array_slice($activities, 0, 5);
    }
    
    public function goToPage($route)
    {
        return redirect()->to($route);
    }
    
    public function render()
    {
        $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
        
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 1);
        
        return view('livewire.hr.dashboard', [
            'months' => $months,
            'years' => $years,
        ])->layout('layouts.hrmanagerdashboard');
    }
}