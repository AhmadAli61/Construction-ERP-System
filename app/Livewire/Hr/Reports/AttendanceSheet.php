<?php

namespace App\Livewire\Hr\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\Worker;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSheet extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Actual filter properties (applied after search button click)
    public $selectedWorkerId = null;
    public $selectedProjectId = null;
    public $selectedMonth = null;
    public $selectedYear = null;

    // Temporary properties for button search
    public $tempSelectedWorkerId = null;
    public $tempSelectedProjectId = null;
    public $tempSelectedMonth = null;
    public $tempSelectedYear = null;

    // View mode
    public $viewMode = 'calendar';

    // Stats
    public $totalPresent = 0;
    public $totalAbsent = 0;
    public $totalHalfDay = 0;
    public $totalHoursWorked = 0;
    public $totalOvertime = 0;
    public $attendanceRate = 0;
    public $averageHoursPerDay = 0;
    public $totalWorkingDays = 0;
    public $totalPossibleDays = 0;
    public $totalActualAttendance = 0;
    public $totalProjectsWorked = 0;

    // Workers list
    public $workers = [];

    // Search flag
    public $isSearching = false;

    // Tooltip state
    public $showTooltip = false;
    public $tooltipContent = '';
    public $tooltipX = 0;
    public $tooltipY = 0;

    public function mount()
    {
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->tempSelectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->loadWorkers();
    }

    public function loadWorkers()
    {
        $query = Worker::where('workers.status', 'active');

        if ($this->tempSelectedProjectId) {
            $query->whereHas('projects', function ($q) {
                $q->where('project_id', $this->tempSelectedProjectId)
                    ->where('project_worker.status', 'active')
                    ->where(function ($sub) {
                        $sub->whereNull('project_worker.release_date')
                            ->orWhere('project_worker.release_date', '>=', now()->format('Y-m-d'));
                    });
            });
        }

        $this->workers = $query->orderBy('name')->get();
    }

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->selectedWorkerId = $this->tempSelectedWorkerId;
        $this->selectedProjectId = $this->tempSelectedProjectId;
        $this->selectedMonth = $this->tempSelectedMonth;
        $this->selectedYear = $this->tempSelectedYear;
        $this->isSearching = true;

        $this->resetPage();
        $this->calculateStats();
    }

    // Reset all filters
    public function resetFilters()
    {
        $this->tempSelectedWorkerId = null;
        $this->tempSelectedProjectId = null;
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->tempSelectedYear = Carbon::now()->year;

        $this->selectedWorkerId = null;
        $this->selectedProjectId = null;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;

        $this->isSearching = false;
        $this->loadWorkers();
        $this->resetPage();

        // Reset stats
        $this->totalPresent = 0;
        $this->totalAbsent = 0;
        $this->totalHalfDay = 0;
        $this->totalHoursWorked = 0;
        $this->totalOvertime = 0;
        $this->attendanceRate = 0;
        $this->averageHoursPerDay = 0;
        $this->totalWorkingDays = 0;
        $this->totalPossibleDays = 0;
        $this->totalActualAttendance = 0;
        $this->totalProjectsWorked = 0;
    }

    // Clear individual filters
    public function clearWorkerFilter()
    {
        $this->tempSelectedWorkerId = null;
        $this->selectedWorkerId = null;
        $this->isSearching = false;
        $this->resetPage();
    }

    public function clearProjectFilter()
    {
        $this->tempSelectedProjectId = null;
        $this->selectedProjectId = null;
        $this->loadWorkers();
        $this->resetPage();
        if ($this->selectedWorkerId) {
            $this->calculateStats();
        }
    }

    public function updatedTempSelectedProjectId()
    {
        $this->loadWorkers();
        $this->tempSelectedWorkerId = null;
    }

    public function updatedSelectedViewMode()
    {
        // Just update view mode without affecting search
    }

    public function calculateStats()
    {
        if (!$this->selectedWorkerId || !$this->isSearching) {
            return;
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        $totalDaysInMonth = $startDate->daysInMonth;

        $query = Attendance::where('worker_id', $this->selectedWorkerId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        $attendances = $query->get();

        $this->totalPresent = $attendances->where('status', 'present')->count();
        $this->totalAbsent = $attendances->where('status', 'absent')->count();
        $this->totalHalfDay = $attendances->where('status', 'half_day')->count();
        $this->totalHoursWorked = $attendances->sum('hours_worked');
        $this->totalOvertime = $attendances->sum('overtime_hours');

        $this->totalWorkingDays = $attendances->count();
        $this->totalPossibleDays = $totalDaysInMonth;

        // Calculate weighted attendance (present = 1, half_day = 0.5)
        $this->totalActualAttendance = $this->totalPresent + ($this->totalHalfDay * 0.5);

        // Attendance rate based on actual attendance out of total possible days
        if ($this->totalPossibleDays > 0) {
            $this->attendanceRate = round(($this->totalActualAttendance / $this->totalPossibleDays) * 100, 2);
        } else {
            $this->attendanceRate = 0;
        }

        // Average hours per working day
        if ($this->totalWorkingDays > 0) {
            $this->averageHoursPerDay = round($this->totalHoursWorked / $this->totalWorkingDays, 2);
        } else {
            $this->averageHoursPerDay = 0;
        }

        // Count projects worked on this month
        $this->totalProjectsWorked = $attendances->pluck('project_id')->unique()->count();
    }

    public function getAttendanceDataProperty()
    {
        if (!$this->selectedWorkerId || !$this->isSearching) {
            return collect();
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $query = Attendance::with(['project'])
            ->where('worker_id', $this->selectedWorkerId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        $attendances = $query->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $calendar = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $attendance = $attendances->get($dateKey);

            $calendar[] = [
                'date' => $currentDate->copy(),
                'day' => $currentDate->day,
                'dayName' => $currentDate->format('D'),
                'isWeekend' => $currentDate->isWeekend(),
                'attendance' => $attendance,
                'status' => $attendance ? $attendance->status : 'not_recorded',
                'project' => $attendance ? $attendance->project->name : null,
                'project_code' => $attendance ? $attendance->project->project_code : null,
                'hours_worked' => $attendance ? $attendance->hours_worked : 0,
                'overtime' => $attendance ? $attendance->overtime_hours : 0,
                'check_in' => $attendance ? $attendance->check_in : null,
                'check_out' => $attendance ? $attendance->check_out : null,
                'notes' => $attendance ? $attendance->notes : null,
            ];

            $currentDate->addDay();
        }

        return collect($calendar);
    }

    public function getMonthStatsProperty()
    {
        if (!$this->selectedWorkerId || !$this->isSearching) {
            return [
                'labels' => [],
                'present' => [],
                'absent' => [],
                'half_day' => [],
                'hours' => [],
            ];
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $query = Attendance::where('worker_id', $this->selectedWorkerId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        $stats = $query->select(
            DB::raw('DATE(date) as date'),
            DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
            DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
            DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
            DB::raw('SUM(hours_worked) as hours')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $stats->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d M');
            })->toArray(),
            'present' => $stats->pluck('present')->toArray(),
            'absent' => $stats->pluck('absent')->toArray(),
            'half_day' => $stats->pluck('half_day')->toArray(),
            'hours' => $stats->pluck('hours')->toArray(),
        ];
    }

    public function getWorkerInfoProperty()
    {
        if (!$this->selectedWorkerId || !$this->isSearching) {
            return null;
        }

        $worker = Worker::find($this->selectedWorkerId);
        if ($worker) {
            $worker->total_attendance = Attendance::where('worker_id', $this->selectedWorkerId)->count();
            $worker->total_hours = Attendance::where('worker_id', $this->selectedWorkerId)->sum('hours_worked');

            $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

            $worker->monthly_projects = Attendance::where('worker_id', $this->selectedWorkerId)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('project')
                ->get()
                ->groupBy('project_id')
                ->map(function ($attendances, $projectId) {
                    $project = $attendances->first()->project;
                    $project->days_worked = $attendances->count();
                    return $project;
                })
                ->values();
        }
        return $worker;
    }

    public function exportToExcel()
    {
        session()->flash('info', 'Excel export feature coming soon!');
    }

    public function render()
    {
        $projects = Project::orderBy('name')->get();

        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 1);

        $months = [
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

        return view('livewire.hr.reports.attendance-sheet', [
            'projects' => $projects,
            'years' => $years,
            'months' => $months,
            'attendanceData' => $this->attendance_data,
            'workerInfo' => $this->worker_info,
            'monthStats' => $this->month_stats,
        ])->layout('layouts.hrmanagerdashboard');
    }
}