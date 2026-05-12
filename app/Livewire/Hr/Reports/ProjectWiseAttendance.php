<?php

namespace App\Livewire\Hr\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectWiseAttendance extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Actual filter properties (applied after search button click)
    public $selectedProjectId = null;
    public $selectedMonth = null;
    public $selectedYear = null;
    public $selectedWorkerId = null;

    // Temporary properties for button search
    public $tempProjectId = null;
    public $tempMonth = null;
    public $tempYear = null;
    public $tempWorkerId = null;

    // View mode
    public $viewMode = 'calendar';

    // Search flag
    public $isSearching = false;

    // Stats
    public $totalPresent = 0;
    public $totalAbsent = 0;
    public $totalHalfDay = 0;
    public $totalHoursWorked = 0;
    public $totalOvertime = 0;
    public $attendanceRate = 0;
    public $averageHoursPerDay = 0;
    public $totalWorkers = 0;
    public $totalWorkingDays = 0;
    public $totalPresentWorkers = 0;
    public $totalAbsentWorkers = 0;
    public $totalPossibleAttendance = 0;
    public $totalActualAttendance = 0;

    // Workers list for the project
    public $workers = [];
    public $selectedWorkerDetails = null;

    public function mount()
    {
        $this->tempMonth = Carbon::now()->month;
        $this->tempYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->selectedProjectId = $this->tempProjectId;
        $this->selectedMonth = $this->tempMonth;
        $this->selectedYear = $this->tempYear;
        $this->selectedWorkerId = $this->tempWorkerId;
        $this->isSearching = true;
        
        $this->loadWorkers();
        $this->loadSelectedWorkerDetails();
        $this->calculateStats();
        $this->resetPage();
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->tempProjectId = null;
        $this->tempMonth = Carbon::now()->month;
        $this->tempYear = Carbon::now()->year;
        $this->tempWorkerId = null;
        
        $this->selectedProjectId = null;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->selectedWorkerId = null;
        
        $this->isSearching = false;
        $this->workers = collect();
        $this->selectedWorkerDetails = null;
        
        $this->resetPage();
        
        // Reset stats
        $this->totalPresent = 0;
        $this->totalAbsent = 0;
        $this->totalHalfDay = 0;
        $this->totalHoursWorked = 0;
        $this->totalOvertime = 0;
        $this->attendanceRate = 0;
        $this->averageHoursPerDay = 0;
        $this->totalWorkers = 0;
        $this->totalWorkingDays = 0;
        $this->totalPresentWorkers = 0;
        $this->totalAbsentWorkers = 0;
        $this->totalPossibleAttendance = 0;
        $this->totalActualAttendance = 0;
    }
    
    // Clear individual filters
    public function clearProjectFilter()
    {
        $this->tempProjectId = null;
        $this->selectedProjectId = null;
        $this->workers = collect();
        $this->selectedWorkerId = null;
        $this->tempWorkerId = null;
        $this->isSearching = false;
        $this->resetPage();
    }
    
    public function clearWorkerFilter()
    {
        $this->tempWorkerId = null;
        $this->selectedWorkerId = null;
        $this->selectedWorkerDetails = null;
        if ($this->selectedProjectId) {
            $this->calculateStats();
        }
        $this->resetPage();
    }
    
    public function updatedTempProjectId()
    {
        // When project changes, clear worker filter
        $this->tempWorkerId = null;
        $this->loadWorkersForTemp();
    }
    
    public function loadWorkersForTemp()
    {
        if ($this->tempProjectId) {
            $this->workers = Worker::whereHas('projects', function ($query) {
                $query->where('project_id', $this->tempProjectId)
                    ->where('project_worker.status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('project_worker.release_date')
                            ->orWhere('project_worker.release_date', '>=', now()->format('Y-m-d'));
                    });
            })->orderBy('name')->get();
        } else {
            $this->workers = collect();
        }
    }

    public function loadWorkers()
    {
        if ($this->selectedProjectId) {
            $this->workers = Worker::whereHas('projects', function ($query) {
                $query->where('project_id', $this->selectedProjectId)
                    ->where('project_worker.status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('project_worker.release_date')
                            ->orWhere('project_worker.release_date', '>=', now()->format('Y-m-d'));
                    });
            })->orderBy('name')->get();
        } else {
            $this->workers = collect();
        }
    }

    public function loadSelectedWorkerDetails()
    {
        if ($this->selectedWorkerId && $this->selectedProjectId && $this->isSearching) {
            $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
            
            $this->selectedWorkerDetails = Worker::with(['attendances' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->where('project_id', $this->selectedProjectId);
            }])->find($this->selectedWorkerId);
        }
    }

    public function calculateStats()
    {
        if (!$this->selectedProjectId || !$this->isSearching) {
            return;
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();
        $totalDaysInMonth = $startDate->daysInMonth;

        $this->totalWorkers = Worker::whereHas('projects', function ($query) {
            $query->where('project_id', $this->selectedProjectId)
                ->where('project_worker.status', 'active')
                ->where(function ($q) {
                    $q->whereNull('project_worker.release_date')
                        ->orWhere('project_worker.release_date', '>=', now()->format('Y-m-d'));
                });
        })->count();

        $query = Attendance::where('project_id', $this->selectedProjectId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($this->selectedWorkerId) {
            $query->where('worker_id', $this->selectedWorkerId);
        }

        $attendances = $query->get();

        $this->totalPresent = $attendances->where('status', 'present')->count();
        $this->totalAbsent = $attendances->where('status', 'absent')->count();
        $this->totalHalfDay = $attendances->where('status', 'half_day')->count();
        $this->totalHoursWorked = $attendances->sum('hours_worked');
        $this->totalOvertime = $attendances->sum('overtime_hours');
        
        $this->totalWorkingDays = $attendances->pluck('date')->unique()->count();
        $this->totalPossibleAttendance = $this->totalWorkers * $totalDaysInMonth;
        $this->totalActualAttendance = $this->totalPresent + ($this->totalHalfDay * 0.5);

        if ($this->totalPossibleAttendance > 0) {
            $this->attendanceRate = round(($this->totalActualAttendance / $this->totalPossibleAttendance) * 100, 2);
        } else {
            $this->attendanceRate = 0;
        }

        if ($this->totalWorkingDays > 0) {
            $this->averageHoursPerDay = round($this->totalHoursWorked / $this->totalWorkingDays, 2);
        } else {
            $this->averageHoursPerDay = 0;
        }

        $this->totalPresentWorkers = $attendances->where('status', 'present')->pluck('worker_id')->unique()->count();
        $this->totalAbsentWorkers = $this->totalWorkers - $this->totalPresentWorkers;
    }

    public function getAttendanceDataProperty()
    {
        if (!$this->selectedProjectId || !$this->isSearching) {
            return collect();
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $query = Attendance::with(['worker', 'project'])
            ->where('project_id', $this->selectedProjectId)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($this->selectedWorkerId) {
            $query->where('worker_id', $this->selectedWorkerId);
        }

        return $query->orderBy('date', 'desc')
            ->orderBy('worker_id')
            ->get();
    }

    public function getDailySummaryProperty()
    {
        if (!$this->selectedProjectId || !$this->isSearching) {
            return collect();
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $query = Attendance::where('project_id', $this->selectedProjectId)
            ->whereBetween('date', [$startDate, $endDate]);
            
        if ($this->selectedWorkerId) {
            $query->where('worker_id', $this->selectedWorkerId);
        }

        return $query->select(
                'date',
                DB::raw('COUNT(*) as total_workers'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
                DB::raw('SUM(hours_worked) as total_hours'),
                DB::raw('SUM(overtime_hours) as total_overtime')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getCalendarDataProperty()
    {
        if (!$this->selectedProjectId || !$this->isSearching) {
            return collect();
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $dailyData = $this->daily_summary->keyBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m-d');
        });

        $calendar = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dayData = $dailyData->get($dateKey);
            
            $calendar[] = [
                'date' => $currentDate->copy(),
                'day' => $currentDate->day,
                'dayName' => $currentDate->format('D'),
                'isWeekend' => $currentDate->isWeekend(),
                'present' => $dayData ? $dayData->present : 0,
                'absent' => $dayData ? $dayData->absent : 0,
                'half_day' => $dayData ? $dayData->half_day : 0,
                'total_workers' => $dayData ? $dayData->total_workers : 0,
                'total_hours' => $dayData ? $dayData->total_hours : 0,
                'total_overtime' => $dayData ? $dayData->total_overtime : 0,
                'has_data' => $dayData ? true : false,
            ];
            
            $currentDate->addDay();
        }
        
        return collect($calendar);
    }

    public function getWorkerSummaryProperty()
    {
        if (!$this->selectedProjectId || !$this->isSearching) {
            return collect();
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $query = Attendance::where('project_id', $this->selectedProjectId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('worker');
            
        if ($this->selectedWorkerId) {
            $query->where('worker_id', $this->selectedWorkerId);
        }

        return $query->select(
                'worker_id',
                DB::raw('COUNT(*) as total_days'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day'),
                DB::raw('SUM(hours_worked) as total_hours'),
                DB::raw('SUM(overtime_hours) as total_overtime')
            )
            ->groupBy('worker_id')
            ->orderBy('present', 'desc')
            ->get();
    }

    public function getProjectInfoProperty()
    {
        if (!$this->selectedProjectId || !$this->isSearching) {
            return null;
        }
        
        return Project::find($this->selectedProjectId);
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
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return view('livewire.hr.reports.project-wise-attendance', [
            'projects' => $projects,
            'years' => $years,
            'months' => $months,
            'attendanceData' => $this->attendance_data,
            'dailySummary' => $this->daily_summary,
            'calendarData' => $this->calendar_data,
            'workerSummary' => $this->worker_summary,
            'projectInfo' => $this->project_info,
        ])->layout('layouts.hrmanagerdashboard');
    }
}