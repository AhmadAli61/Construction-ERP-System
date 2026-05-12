<?php
// app/Livewire/Admin/Dashboard.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Worker;
use App\Models\Project;
use App\Models\Attendance;
use App\Models\WorkerAdvance;
use App\Models\PayrollBatch;
use App\Models\Payroll;
use App\Models\SaleInvoice;
use App\Models\ProjectExpense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $selectedYear;
    public $selectedMonth;
    public $dateRange = 'current_month';
    
    // HR Stats
    public $workerStats = [];
    public $attendanceStats = [];
    public $payrollStats = [];
    public $projectStats = [];
    public $advanceStats = [];
    public $quickStats = [];
    public $medicalStatus = [];
    public $topPerformers = [];
    public $weeklyAttendance = [];
    public $attendanceTrend = [];
    public $departmentDistribution = [];
    public $recentActivities = [];
    public $upcomingDeadlines = [];
    public $medicalExpiringWorkers = [];
    public $workersWithHighAdvances = [];
    
    // Admin/Financial Stats
    public $financialStats = [];
    public $revenueData = [];
    public $expenseData = [];
    public $profitLossData = [];
    public $monthlyProfitLoss = [];
    public $topProjectsByRevenue = [];
    public $recentInvoices = [];
    public $recentExpenses = [];
    public $userStats = [];
    
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
            
            // Admin specific data
            $this->loadFinancialStats();
            $this->loadProfitLossData();
            $this->loadMonthlyProfitLoss();
            $this->loadTopProjectsByRevenue();
            $this->loadRecentInvoices();
            $this->loadRecentExpenses();
            $this->loadUserStats();
        } catch (\Exception $e) {
            \Log::error('Admin Dashboard loading error: ' . $e->getMessage());
            session()->flash('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }
    
    private function loadQuickStats()
    {
        // Total Workers
        $totalWorkers = Worker::count();
        $activeWorkers = Worker::where('status', 'active')->count();
        $inactiveWorkers = Worker::where('status', 'inactive')->count();

        // Active Projects
        $activeProjects = Project::where('status', 'ongoing')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $planningProjects = Project::where('status', 'planning')->count();
        $totalProjects = Project::count();

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
        
        // Total Users
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();

        $this->quickStats = [
            'total_workers' => $totalWorkers,
            'active_workers' => $activeWorkers,
            'inactive_workers' => $inactiveWorkers,
            'active_projects' => $activeProjects,
            'completed_projects' => $completedProjects,
            'planning_projects' => $planningProjects,
            'total_projects' => $totalProjects,
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
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
        ];
    }
    
    private function loadMedicalStatus()
    {
        $totalWorkers = Worker::where('status', 'active')->count();
        
        $validMedical = Worker::where('status', 'active')
            ->where('medical_expiry_date', '>=', Carbon::now())
            ->count();
        
        $expiringSoon = Worker::where('status', 'active')
            ->where('medical_expiry_date', '>=', Carbon::now())
            ->where('medical_expiry_date', '<=', Carbon::now()->addDays(30))
            ->count();

        $this->medicalStatus = [
            'total_workers' => $totalWorkers,
            'valid' => $validMedical,
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
        
        $weightedAttendance = $presentCount + ($halfDayCount * 0.5);
        $attendanceRate = $maxPossibleAttendance > 0 ? round(($weightedAttendance / $maxPossibleAttendance) * 100, 1) : 0;
        
        $totalHours = $attendances->sum('hours_worked');
        $totalOvertime = $attendances->sum('overtime_hours');
        
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::where('date', $today)->get();
        $todayPresent = $todayAttendance->where('status', 'present')->count();
        $todayHalfDay = $todayAttendance->where('status', 'half_day')->count();
        $todayAbsent = max(0, $totalWorkers - ($todayPresent + $todayHalfDay));
        
        $this->attendanceStats = [
            'attendance_rate' => $attendanceRate,
            'total_present' => $presentCount,
            'total_half_day' => $halfDayCount,
            'total_hours' => round($totalHours, 1),
            'total_overtime' => round($totalOvertime, 1),
            'today_present' => $todayPresent,
            'today_half_day' => $todayHalfDay,
            'today_absent' => $todayAbsent,
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
            
            $lastMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonth();
            $lastMonthPayrolls = Payroll::whereHas('batch', function($q) use ($lastMonth) {
                $q->where('year', $lastMonth->year)->where('month', $lastMonth->month);
            })->get();
            
            $lastMonthGross = $lastMonthPayrolls->sum('gross_amount');
            $grossGrowth = $lastMonthGross > 0 ? round((($totalGross - $lastMonthGross) / $lastMonthGross) * 100, 1) : 0;
            
            $this->payrollStats = [
                'total_gross' => $totalGross,
                'total_net' => $totalNet,
                'total_advance_deduction' => $totalAdvanceDeduction,
                'total_workers_paid' => $totalWorkersPaid,
                'gross_growth' => $grossGrowth,
            ];
        } catch (\Exception $e) {
            $this->payrollStats = [
                'total_gross' => 0,
                'total_net' => 0,
                'total_advance_deduction' => 0,
                'total_workers_paid' => 0,
                'gross_growth' => 0,
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
            $pendingBalance = WorkerAdvance::where('status', 'pending')->where('is_deduction', false)->sum('remaining_amount') ?? 0;
            
            $workersWithAdvances = WorkerAdvance::where('is_deduction', false)
                ->distinct('worker_id')
                ->count('worker_id');
            
            $this->advanceStats = [
                'total_advances_given' => $totalAdvances,
                'total_deducted' => $totalDeducted,
                'pending_balance' => $pendingBalance,
                'recovery_percentage' => $totalAdvances > 0 ? round(($totalDeducted / $totalAdvances) * 100, 1) : 0,
                'workers_with_advances' => $workersWithAdvances,
                'average_advance' => $workersWithAdvances > 0 ? round($totalAdvances / $workersWithAdvances, 2) : 0,
            ];
        } catch (\Exception $e) {
            $this->advanceStats = [
                'total_advances_given' => 0,
                'total_deducted' => 0,
                'pending_balance' => 0,
                'recovery_percentage' => 0,
                'workers_with_advances' => 0,
                'average_advance' => 0,
            ];
        }
    }
    
    private function loadFinancialStats()
    {
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        // Total Revenue from Sale Invoices
        $totalRevenue = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('total');
        $totalRevenueSubtotal = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('subtotal');
        $totalVatCollected = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('vat_amount');
        $invoicesCount = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])->count();
        
        // Paid vs Unpaid
        $paidInvoices = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total');
        $unpaidInvoices = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('payment_status', 'unpaid')
            ->sum('total');
        
        // Total Expenses from Project Expenses
        $totalExpenses = ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $expensesCount = ProjectExpense::whereBetween('expense_date', [$startDate, $endDate])->count();
        
        // Total Payroll for the month
        $totalPayroll = Payroll::whereHas('batch', function($q) {
            $q->where('year', $this->selectedYear)->where('month', $this->selectedMonth);
        })->sum('net_amount');
        
        // Calculate Profit/Loss
        $totalCost = $totalExpenses + $totalPayroll;
        $netProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        $this->financialStats = [
            'total_revenue' => $totalRevenue,
            'total_revenue_subtotal' => $totalRevenueSubtotal,
            'total_vat_collected' => $totalVatCollected,
            'invoices_count' => $invoicesCount,
            'paid_invoices' => $paidInvoices,
            'unpaid_invoices' => $unpaidInvoices,
            'total_expenses' => $totalExpenses,
            'expenses_count' => $expensesCount,
            'total_payroll' => $totalPayroll,
            'total_cost' => $totalCost,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
        ];
    }
    
    private function loadProfitLossData()
    {
        // Get last 6 months of data for chart
        $this->profitLossData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = SaleInvoice::whereBetween('invoice_date', [$monthStart, $monthEnd])->sum('total');
            $expenses = ProjectExpense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
            $payroll = Payroll::whereHas('batch', function($q) use ($date) {
                $q->where('year', $date->year)->where('month', $date->month);
            })->sum('net_amount');
            
            $totalCost = $expenses + $payroll;
            $profit = $revenue - $totalCost;
            
            $this->profitLossData[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'payroll' => $payroll,
                'total_cost' => $totalCost,
                'profit' => $profit,
            ];
        }
    }
    
    private function loadMonthlyProfitLoss()
    {
        // Get full year data
        $this->monthlyProfitLoss = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($this->selectedYear, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($this->selectedYear, $month, 1)->endOfMonth();
            
            $revenue = SaleInvoice::whereBetween('invoice_date', [$monthStart, $monthEnd])->sum('total');
            $expenses = ProjectExpense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
            $payroll = Payroll::whereHas('batch', function($q) use ($month) {
                $q->where('year', $this->selectedYear)->where('month', $month);
            })->sum('net_amount');
            
            $totalCost = $expenses + $payroll;
            $profit = $revenue - $totalCost;
            
            $this->monthlyProfitLoss[$month] = [
                'month_name' => Carbon::create()->month($month)->format('F'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'payroll' => $payroll,
                'total_cost' => $totalCost,
                'profit' => $profit,
            ];
        }
    }
    
    private function loadTopProjectsByRevenue()
    {
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        
        $this->topProjectsByRevenue = SaleInvoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->select('project_id', DB::raw('SUM(total) as total_revenue'), DB::raw('COUNT(*) as invoice_count'))
            ->with('project')
            ->groupBy('project_id')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'project_name' => $item->project->name ?? 'Unknown',
                    'project_code' => $item->project->project_code ?? '',
                    'total_revenue' => $item->total_revenue,
                    'invoice_count' => $item->invoice_count,
                ];
            });
    }
    
    private function loadRecentInvoices()
    {
        $this->recentInvoices = SaleInvoice::with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'client_name' => $invoice->client_name,
                    'project_name' => $invoice->project->name ?? 'Unknown',
                    'total' => $invoice->total,
                    'payment_status' => $invoice->payment_status,
                ];
            });
    }
    
    private function loadRecentExpenses()
    {
        $this->recentExpenses = ProjectExpense::with(['project', 'category'])
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function($expense) {
                return [
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'expense_date' => $expense->expense_date,
                    'project_name' => $expense->project->name ?? 'Unknown',
                    'category_name' => $expense->category->name ?? 'Other',
                ];
            });
    }
    
    private function loadUserStats()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        
        // Users by role (if roles exist)
        $usersByRole = [];
        if (\Schema::hasTable('roles')) {
            $usersByRole = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->select('roles.name as role_name', DB::raw('COUNT(*) as count'))
                ->groupBy('roles.name')
                ->get()
                ->toArray();
        }
        
        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'created_at' => $user->created_at,
                ];
            });
        
        $this->userStats = [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
            'users_by_role' => $usersByRole,
            'recent_users' => $recentUsers,
        ];
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
                    'days_left' => Carbon::parse($worker->medical_expiry_date)->diffInDays(now()),
                ];
            }
        } catch (\Exception $e) {}
        
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
                    'days_left' => Carbon::parse($project->end_date)->diffInDays(now()),
                ];
            }
        } catch (\Exception $e) {}
        
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
        } catch (\Exception $e) {}
        
        try {
            $recentInvoices = SaleInvoice::orderBy('created_at', 'desc')->limit(2)->get();
            foreach ($recentInvoices as $invoice) {
                $activities[] = [
                    'type' => 'invoice',
                    'title' => 'New Invoice Created',
                    'description' => "{$invoice->invoice_number} - €" . number_format($invoice->total, 2),
                    'time' => $invoice->created_at->diffForHumans(),
                ];
            }
        } catch (\Exception $e) {}
        
        try {
            $recentExpenses = ProjectExpense::orderBy('created_at', 'desc')->limit(2)->get();
            foreach ($recentExpenses as $expense) {
                $activities[] = [
                    'type' => 'expense',
                    'title' => 'New Expense Recorded',
                    'description' => "{$expense->description} - €" . number_format($expense->amount, 2),
                    'time' => $expense->created_at->diffForHumans(),
                ];
            }
        } catch (\Exception $e) {}
        
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
        
        return view('livewire.admin.dashboard', [
            'months' => $months,
            'years' => $years,
        ])->layout('layouts.admindashboard');
    }
}