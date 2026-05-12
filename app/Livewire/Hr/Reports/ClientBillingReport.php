<?php

namespace App\Livewire\Hr\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\Worker;
use Carbon\Carbon;

class ClientBillingReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Active filters (applied when search button is clicked)
    public $selectedMonth = null;
    public $selectedYear = null;
    public $selectedProjectId = null;
    public $selectedWorkerId = null;
    public $selectedBillingType = null;

    // Temporary filters (bound to form inputs)
    public $tempMonth = null;
    public $tempYear = null;
    public $tempProjectId = null;
    public $tempWorkerId = null;
    public $tempBillingType = null;

    // Summary totals
    public $totalDailyCalls = 0;
    public $totalHourlyCalls = 0;
    public $totalClientHours = 0;
    public $totalProjects = 0;
    public $totalWorkers = 0;

    // For displaying selected filter names
    public $selectedProjectName = '';
    public $selectedWorkerName = '';

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        
        // Initialize temp filters with same values
        $this->tempMonth = $this->selectedMonth;
        $this->tempYear = $this->selectedYear;
        $this->tempProjectId = null;
        $this->tempWorkerId = null;
        $this->tempBillingType = null;
    }

    public function applyFilters()
    {
        // Apply the temporary filter values to the active filters
        $this->selectedMonth = $this->tempMonth;
        $this->selectedYear = $this->tempYear;
        $this->selectedProjectId = $this->tempProjectId;
        $this->selectedWorkerId = $this->tempWorkerId;
        $this->selectedBillingType = $this->tempBillingType;
        
        // Get selected names for display
        if ($this->selectedProjectId) {
            $project = Project::find($this->selectedProjectId);
            $this->selectedProjectName = $project ? $project->name : '';
        } else {
            $this->selectedProjectName = '';
        }
        
        if ($this->selectedWorkerId) {
            $worker = Worker::find($this->selectedWorkerId);
            $this->selectedWorkerName = $worker ? $worker->name : '';
        } else {
            $this->selectedWorkerName = '';
        }
        
        // Reset pagination when filters are applied
        $this->resetPage();
    }

    public function clearAllTempFilters()
    {
        // Clear temporary filters but keep month and year with current values
        $this->tempProjectId = null;
        $this->tempWorkerId = null;
        $this->tempBillingType = null;
        
        // Optionally reset month/year to current values
        $this->tempMonth = Carbon::now()->month;
        $this->tempYear = Carbon::now()->year;
    }

    public function resetFilters()
    {
        // Reset all active filters to default
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->selectedProjectId = null;
        $this->selectedWorkerId = null;
        $this->selectedBillingType = null;
        
        // Reset temporary filters to match
        $this->tempMonth = $this->selectedMonth;
        $this->tempYear = $this->selectedYear;
        $this->tempProjectId = null;
        $this->tempWorkerId = null;
        $this->tempBillingType = null;
        
        // Clear selected names
        $this->selectedProjectName = '';
        $this->selectedWorkerName = '';
        
        // Reset pagination
        $this->resetPage();
    }

    public function render()
    {
        // Build query using active filters
        $query = Attendance::with(['worker', 'project'])
            ->whereYear('date', $this->selectedYear)
            ->whereMonth('date', $this->selectedMonth);

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        if ($this->selectedWorkerId) {
            $query->where('worker_id', $this->selectedWorkerId);
        }

        if ($this->selectedBillingType) {
            $query->where('client_billing_type', $this->selectedBillingType);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        // Calculate summary using the same query
        $this->totalDailyCalls = $query->clone()->where('client_billing_type', 'daily')->count();
        $this->totalHourlyCalls = $query->clone()->where('client_billing_type', 'hourly')->count();
        $this->totalClientHours = $query->clone()->sum('client_hours');
        $this->totalProjects = $query->clone()->distinct('project_id')->count('project_id');
        $this->totalWorkers = $query->clone()->distinct('worker_id')->count('worker_id');

        // Get projects and workers for filters
        $projects = Project::orderBy('name')->get();
        $workers = Worker::orderBy('name')->get();

        return view('livewire.hr.reports.client-billing-report', [
            'attendances' => $attendances,
            'projects' => $projects,
            'workers' => $workers,
        ])->layout('layouts.hrmanagerdashboard');
    }
}