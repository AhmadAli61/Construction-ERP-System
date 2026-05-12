<?php

namespace App\Livewire\Hr;

use Livewire\Component;
use App\Models\Worker;
use Livewire\WithPagination;

class EditWorker extends Component
{
    use WithPagination;

    // Search properties (actual filters applied after button click)
    public $search = '';
    public $statusFilter = '';
    public $departmentFilter = '';
    
    // Temporary properties for button search
    public $tempSearch = '';
    public $tempStatusFilter = '';
    public $tempDepartmentFilter = '';
    
    public $confirmingDelete = false;
    public $deleteId = null;
    
    // For details modal
    public $showDetailsModal = false;
    public $selectedWorker = null;

    protected $paginationTheme = 'bootstrap';
    
    protected $queryString = ['search', 'statusFilter', 'departmentFilter'];

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->search = $this->tempSearch;
        $this->statusFilter = $this->tempStatusFilter;
        $this->departmentFilter = $this->tempDepartmentFilter;
        $this->resetPage();
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->departmentFilter = '';
        $this->tempSearch = '';
        $this->tempStatusFilter = '';
        $this->tempDepartmentFilter = '';
        $this->resetPage();
    }
    
    // Clear individual filters
    public function clearSearch()
    {
        $this->tempSearch = '';
        $this->search = '';
        $this->resetPage();
    }
    
    public function clearStatusFilter()
    {
        $this->tempStatusFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }
    
    public function clearDepartmentFilter()
    {
        $this->tempDepartmentFilter = '';
        $this->departmentFilter = '';
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deleteId = $id;
    }

    public function deleteWorker()
    {
        if ($this->deleteId) {
            $worker = Worker::find($this->deleteId);
            if ($worker) {
                $worker->delete();
                session()->flash('message', 'Worker deleted successfully.');
            }
            $this->confirmingDelete = false;
            $this->deleteId = null;
        }
    }
    
    public function viewDetails($id)
    {
        $this->selectedWorker = Worker::find($id);
        $this->showDetailsModal = true;
    }
    
    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedWorker = null;
    }

    public function render()
    {
        $workers = Worker::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%')
                      ->orWhere('alternate_phone', 'like', '%' . $this->search . '%')
                      ->orWhere('national_id', 'like', '%' . $this->search . '%')
                      ->orWhere('department', 'like', '%' . $this->search . '%')
                      ->orWhere('designation', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->departmentFilter, function ($query) {
                $query->where('department', $this->departmentFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Get unique departments for filter dropdown
        $departments = Worker::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->toArray();

        return view('livewire.hr.edit-worker', [
            'workers' => $workers,
            'departments' => $departments,
        ])->layout('layouts.hrmanagerdashboard');
    }
}