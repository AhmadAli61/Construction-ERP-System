<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Worker;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\WorkerAdvance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkerProfile extends Component
{
    public $workers = [];
    public $selectedWorkerId = null;
    public $selectedWorker = null;

    // Actual filter properties (applied after search button click)
    public $selectedYear = null;
    public $selectedMonth = null;
    public $showAttendance = true;
    public $showPayrolls = true;
    public $showAdvances = true;

    // Temporary properties for button search
    public $tempSelectedWorkerId = null;
    public $tempSelectedYear = null;
    public $tempSelectedMonth = null;

    // Statistics
    public $totalHours = 0;
    public $totalOvertime = 0;
    public $totalDaysWorked = 0;
    public $totalEarnings = 0;
    public $totalAdvances = 0;
    public $pendingAdvances = 0;

    // Data collections
    public $attendanceRecords = [];
    public $monthlyAttendance = [];
    public $payrollHistory = [];
    public $advanceHistory = [];
    public $projectHistory = [];

    // Monthly summary for chart
    public $chartData = [];
    public $months = [];
    
    // Search flag
    public $isSearching = false;

    public function mount()
    {
        $this->loadWorkers();
        $this->loadMonths();

        // Set default values
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
    }

    public function loadWorkers()
    {
        $this->workers = Worker::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function loadMonths()
    {
        $this->months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->selectedWorkerId = $this->tempSelectedWorkerId;
        $this->selectedYear = $this->tempSelectedYear;
        $this->selectedMonth = $this->tempSelectedMonth;
        $this->isSearching = true;
        
        if ($this->selectedWorkerId) {
            $this->selectedWorker = Worker::with(['projects', 'attendances', 'payrolls', 'advances'])
                ->find($this->selectedWorkerId);
            $this->loadWorkerData();
        } else {
            $this->selectedWorker = null;
            $this->resetData();
        }
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->tempSelectedWorkerId = null;
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedMonth = Carbon::now()->month;
        
        $this->selectedWorkerId = null;
        $this->selectedWorker = null;
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        $this->isSearching = false;
        
        $this->resetData();
    }
    
    // Clear worker filter
    public function clearWorkerFilter()
    {
        $this->tempSelectedWorkerId = null;
        $this->selectedWorkerId = null;
        $this->selectedWorker = null;
        $this->isSearching = false;
        $this->resetData();
    }

    public function loadWorkerData()
    {
        if (!$this->selectedWorker) return;

        $this->loadAttendanceData();
        $this->loadPayrollHistory();
        $this->loadAdvanceHistory();
        $this->loadProjectHistory();
        $this->calculateStatistics();
        $this->prepareChartData();
    }

    public function loadAttendanceData()
    {
        $startDate = Carbon::create($this->selectedYear, 1, 1);
        $endDate = Carbon::create($this->selectedYear, 12, 31)->endOfDay();

        $attendances = Attendance::where('worker_id', $this->selectedWorker->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $this->attendanceRecords = $attendances;

        $this->monthlyAttendance = [];
        foreach ($attendances as $attendance) {
            $month = $attendance->date->month;
            if (!isset($this->monthlyAttendance[$month])) {
                $this->monthlyAttendance[$month] = [
                    'month' => $month,
                    'month_name' => $this->months[$month],
                    'total_hours' => 0,
                    'total_overtime' => 0,
                    'days_worked' => 0,
                    'regular_hours' => 0,
                ];
            }
            $this->monthlyAttendance[$month]['total_hours'] += $attendance->hours_worked;
            $this->monthlyAttendance[$month]['total_overtime'] += $attendance->overtime_hours;
            $this->monthlyAttendance[$month]['days_worked']++;
            $this->monthlyAttendance[$month]['regular_hours'] = $this->monthlyAttendance[$month]['total_hours'] - $this->monthlyAttendance[$month]['total_overtime'];
        }
    }

    public function loadPayrollHistory()
    {
        $payrolls = Payroll::with(['batch', 'projectBreakdowns.project'])
            ->where('worker_id', $this->selectedWorker->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->payrollHistory = [];
        foreach ($payrolls as $payroll) {
            $year = $payroll->batch->year ?? date('Y', strtotime($payroll->created_at));
            $month = $payroll->batch->month ?? date('m', strtotime($payroll->created_at));

            $this->payrollHistory[] = [
                'id' => $payroll->id,
                'year' => $year,
                'month' => $month,
                'month_name' => $this->months[$month] ?? 'Unknown',
                'total_hours' => $payroll->total_hours,
                'gross_amount' => $payroll->gross_amount,
                'advance_deduction' => $payroll->advance_deduction,
                'net_amount' => $payroll->net_amount,
                'created_at' => $payroll->created_at,
                'project_breakdown' => $payroll->projectBreakdowns,
            ];
        }
    }

    public function loadAdvanceHistory()
    {
        $advances = WorkerAdvance::where('worker_id', $this->selectedWorker->id)
            ->orderBy('advance_date', 'desc')
            ->get();

        $this->advanceHistory = $advances;
        $this->totalAdvances = $advances->sum('amount');
        $this->pendingAdvances = $advances->where('status', 'pending')->sum('remaining_amount');
    }

    public function loadProjectHistory()
    {
        $this->projectHistory = $this->selectedWorker->projects()
            ->withPivot('assigned_date', 'release_date', 'status')
            ->orderBy('pivot_assigned_date', 'desc')
            ->get();
    }

    public function calculateStatistics()
    {
        $attendances = Attendance::where('worker_id', $this->selectedWorker->id)
            ->whereYear('date', $this->selectedYear)
            ->get();

        $this->totalHours = $attendances->sum('hours_worked');
        $this->totalOvertime = $attendances->sum('overtime_hours');
        $this->totalDaysWorked = $attendances->count();

        $payrolls = Payroll::with('batch')
            ->where('worker_id', $this->selectedWorker->id)
            ->whereHas('batch', function ($query) {
                $query->where('year', $this->selectedYear);
            })
            ->get();

        $this->totalEarnings = $payrolls->sum('net_amount');
    }

    public function prepareChartData()
    {
        $this->chartData = [
            'labels' => [],
            'hours' => [],
            'overtime' => [],
            'earnings' => [],
        ];

        for ($month = 1; $month <= 12; $month++) {
            $monthData = $this->monthlyAttendance[$month] ?? null;
            $monthName = $this->months[$month];

            $this->chartData['labels'][] = substr($monthName, 0, 3);
            $this->chartData['hours'][] = $monthData ? round($monthData['total_hours'], 2) : 0;
            $this->chartData['overtime'][] = $monthData ? round($monthData['total_overtime'], 2) : 0;

            $payroll = collect($this->payrollHistory)->firstWhere('month', $month);
            $this->chartData['earnings'][] = $payroll ? $payroll['net_amount'] : 0;
        }
    }

    public function resetData()
    {
        $this->attendanceRecords = [];
        $this->monthlyAttendance = [];
        $this->payrollHistory = [];
        $this->advanceHistory = [];
        $this->projectHistory = [];
        $this->totalHours = 0;
        $this->totalOvertime = 0;
        $this->totalDaysWorked = 0;
        $this->totalEarnings = 0;
        $this->totalAdvances = 0;
        $this->pendingAdvances = 0;
        $this->chartData = [];
    }

    public function render()
    {
        return view('livewire.admin.worker-profile')->layout('layouts.admindashboard');
    }
}