<?php

namespace App\Livewire\Hr\Projects;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class ProjectAssignment extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $project_id;
    public $worker_id;
    public $assigned_date;
    
    // Search properties
    public $search = '';
    public $searchProject = '';
    public $searchWorker = '';
    public $searchStatus = '';
    public $searchDateFrom = '';
    public $searchDateTo = '';
    
    // Temporary properties for button search
    public $tempSearch = '';
    public $tempSearchProject = '';
    public $tempSearchWorker = '';
    public $tempSearchStatus = '';
    public $tempSearchDateFrom = '';
    public $tempSearchDateTo = '';
    
    // Release modal property
    public $releaseId = null;
    
    public function performSearch()
    {
        // Copy temporary values to actual search values
        $this->search = $this->tempSearch;
        $this->searchProject = $this->tempSearchProject;
        $this->searchWorker = $this->tempSearchWorker;
        $this->searchStatus = $this->tempSearchStatus;
        $this->searchDateFrom = $this->tempSearchDateFrom;
        $this->searchDateTo = $this->tempSearchDateTo;
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        // Reset all search properties
        $this->search = '';
        $this->searchProject = '';
        $this->searchWorker = '';
        $this->searchStatus = '';
        $this->searchDateFrom = '';
        $this->searchDateTo = '';
        
        // Reset temporary properties
        $this->tempSearch = '';
        $this->tempSearchProject = '';
        $this->tempSearchWorker = '';
        $this->tempSearchStatus = '';
        $this->tempSearchDateFrom = '';
        $this->tempSearchDateTo = '';
        
        $this->resetPage();
    }
    
    public function clearGlobalSearch()
    {
        $this->tempSearch = '';
        $this->search = '';
        $this->resetPage();
    }
    
    public function clearProjectFilter()
    {
        $this->tempSearchProject = '';
        $this->searchProject = '';
        $this->resetPage();
    }
    
    public function clearWorkerFilter()
    {
        $this->tempSearchWorker = '';
        $this->searchWorker = '';
        $this->resetPage();
    }
    
    public function clearStatusFilter()
    {
        $this->tempSearchStatus = '';
        $this->searchStatus = '';
        $this->resetPage();
    }
    
    public function clearDateFromFilter()
    {
        $this->tempSearchDateFrom = '';
        $this->searchDateFrom = '';
        $this->resetPage();
    }
    
    public function clearDateToFilter()
    {
        $this->tempSearchDateTo = '';
        $this->searchDateTo = '';
        $this->resetPage();
    }
    
    // Set the release ID when clicking the release button
    public function setReleaseId($id)
    {
        $this->releaseId = $id;
    }
    
    // Confirm and execute the release
    public function confirmRelease()
    {
        if ($this->releaseId) {
            DB::table('project_worker')
                ->where('id', $this->releaseId)
                ->update([
                    'status' => 'released',
                    'release_date' => now(),
                    'updated_at' => now(),
                ]);

            session()->flash('message', 'Worker released from project successfully.');
            $this->releaseId = null;
            
            // Dispatch event to close modal
            $this->dispatch('closeModal');
        }
    }

    public function assign()
    {
        $this->validate([
            'project_id' => 'required',
            'worker_id' => 'required',
        ]);

        $exists = DB::table('project_worker')
            ->where('project_id', $this->project_id)
            ->where('worker_id', $this->worker_id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            session()->flash('error', 'Worker already active assigned to this project.');
            return;
        }

        DB::table('project_worker')->insert([
            'project_id' => $this->project_id,
            'worker_id' => $this->worker_id,
            'assigned_date' => $this->assigned_date ?? now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('message', 'Worker assigned successfully.');

        $this->reset(['worker_id']);
    }

    // Remove the old release method since we're using confirmRelease now
    // public function release($id)
    // {
    //     DB::table('project_worker')
    //         ->where('id', $id)
    //         ->update([
    //             'status' => 'released',
    //             'release_date' => now(),
    //             'updated_at' => now(),
    //         ]);

    //     session()->flash('message', 'Worker released from project.');
    // }

    public function render()
    {
        $projects = Project::all();
        $workers = Worker::where('status', 'active')->get();

        $assignmentsQuery = DB::table('project_worker')
            ->join('projects', 'projects.id', '=', 'project_worker.project_id')
            ->join('workers', 'workers.id', '=', 'project_worker.worker_id')
            ->select(
                'project_worker.*',
                'projects.name as project_name',
                'projects.project_code',
                'workers.name as worker_name',
                'workers.email as worker_email',
                'workers.designation as worker_designation',
                'workers.phone as worker_phone'
            );
        
        // Apply global search (searches across all fields)
        if (!empty($this->search)) {
            $assignmentsQuery->where(function($query) {
                $query->where('projects.name', 'like', '%' . $this->search . '%')
                    ->orWhere('projects.project_code', 'like', '%' . $this->search . '%')
                    ->orWhere('workers.name', 'like', '%' . $this->search . '%')
                    ->orWhere('workers.email', 'like', '%' . $this->search . '%')
                    ->orWhere('workers.designation', 'like', '%' . $this->search . '%')
                    ->orWhere('workers.phone', 'like', '%' . $this->search . '%')
                    ->orWhere('project_worker.status', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply project-specific search
        if (!empty($this->searchProject)) {
            $assignmentsQuery->where(function($query) {
                $query->where('projects.name', 'like', '%' . $this->searchProject . '%')
                    ->orWhere('projects.project_code', 'like', '%' . $this->searchProject . '%');
            });
        }
        
        // Apply worker-specific search
        if (!empty($this->searchWorker)) {
            $assignmentsQuery->where(function($query) {
                $query->where('workers.name', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('workers.email', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('workers.designation', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('workers.phone', 'like', '%' . $this->searchWorker . '%');
            });
        }
        
        // Apply status filter
        if (!empty($this->searchStatus)) {
            $assignmentsQuery->where('project_worker.status', $this->searchStatus);
        }
        
        // Apply date range filter
        if (!empty($this->searchDateFrom)) {
            $assignmentsQuery->whereDate('project_worker.assigned_date', '>=', $this->searchDateFrom);
        }
        
        if (!empty($this->searchDateTo)) {
            $assignmentsQuery->whereDate('project_worker.assigned_date', '<=', $this->searchDateTo);
        }
        
        $assignments = $assignmentsQuery
            ->orderBy('project_worker.id', 'desc')
            ->paginate(10);
            
        return view('livewire.hr.projects.project-assignment', [
            'projects' => $projects,
            'workers' => $workers,
            'assignments' => $assignments,
        ])->layout('layouts.hrmanagerdashboard');
    }
}