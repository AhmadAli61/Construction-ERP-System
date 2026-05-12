<?php

namespace App\Livewire\Hr\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\BudgetItem;
use Livewire\WithPagination;
use Carbon\Carbon;

class EditProject extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Actual filter properties (applied after search button click)
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Temporary properties for button search
    public $tempSearch = '';
    public $tempStatusFilter = '';

    // Delete confirmation properties
    public $confirmingDelete = false;
    public $deleteId = null;
    public $deleteProjectName = '';

    // Edit modal properties
    public $editingProject = false;
    public $editProjectId = null;
    public $editForm = [
        'name' => '',
        'project_code' => '',
        'quotation_number' => '',
        'client_name' => '',
        'client_phone' => '',
        'client_email' => '',
        'client_address' => '',
        'location' => '',
        'description' => '',
        'contract_value' => '',
        'estimated_cost' => '',
        'vat_rate' => 0,
        'vat_included' => false,
        'start_date' => '',
        'end_date' => '',
        'valid_until' => '',
        'status' => 'planning',
    ];

    // Budget items for editing
    public $budgetItems = [];

    // Details modal properties
    public $showDetailsModal = false;
    public $selectedProject = null;

    // Status counts and icons
    public $statusCounts = [
        'planning' => 0,
        'ongoing' => 0,
        'on_hold' => 0,
        'completed' => 0,
        'cancelled' => 0,
    ];
    public $totalProjectsCount = 0;

    public $statusIcons = [
        'planning' => 'fa-calendar-alt',
        'ongoing' => 'fa-play-circle',
        'on_hold' => 'fa-pause-circle',
        'completed' => 'fa-check-circle',
        'cancelled' => 'fa-times-circle'
    ];

    protected $rules = [
        'editForm.name' => 'required|string|max:255',
        'editForm.project_code' => 'required|string|max:50',
        'editForm.quotation_number' => 'nullable|string|max:50',
        'editForm.client_name' => 'required|string|max:255',
        'editForm.client_phone' => 'nullable|string|max:20',
        'editForm.client_email' => 'nullable|email|max:255',
        'editForm.client_address' => 'nullable|string',
        'editForm.location' => 'nullable|string',
        'editForm.description' => 'nullable|string',
        'editForm.contract_value' => 'nullable|numeric|min:0',
        'editForm.estimated_cost' => 'nullable|numeric|min:0',
        'editForm.vat_rate' => 'nullable|numeric|min:0|max:100',
        'editForm.vat_included' => 'boolean',
        'editForm.start_date' => 'nullable|date',
        'editForm.end_date' => 'nullable|date|after_or_equal:start_date',
        'editForm.valid_until' => 'nullable|date',
        'editForm.status' => 'required|in:planning,ongoing,on_hold,completed,cancelled',
    ];

    public function mount()
    {
        $this->loadStatusCounts();
    }

    public function loadStatusCounts()
    {
        $this->statusCounts = [
            'planning' => Project::where('status', 'planning')->count(),
            'ongoing' => Project::where('status', 'ongoing')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'cancelled' => Project::where('status', 'cancelled')->count(),
        ];
        $this->totalProjectsCount = Project::count();
    }

    public function filterByStatus($status)
    {
        $this->tempStatusFilter = $status;
        $this->statusFilter = $status;
        $this->search = $this->tempSearch;
        $this->resetPage();
    }

    public function showAllProjects()
    {
        $this->tempStatusFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function performSearch()
    {
        $this->search = $this->tempSearch;
        $this->statusFilter = $this->tempStatusFilter;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->tempSearch = '';
        $this->tempStatusFilter = '';
        $this->resetPage();
    }

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

    public function addBudgetItem()
    {
        $this->budgetItems[] = [
            'id' => null,
            'section' => 'General',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
    }

    public function removeBudgetItem($index)
    {
        if (isset($this->budgetItems[$index])) {
            if ($this->budgetItems[$index]['id']) {
                BudgetItem::find($this->budgetItems[$index]['id'])->delete();
            }
            array_splice($this->budgetItems, $index, 1);
            $this->recalculateTotals();
        }
    }

    public function updatedBudgetItems()
    {
        $this->recalculateTotals();
    }

    public function updatedEditFormVatRate()
    {
        $this->recalculateTotals();
    }

    public function updatedEditFormVatIncluded()
    {
        $this->recalculateTotals();
    }

    public function recalculateTotals()
    {
        // Calculate item totals
        foreach ($this->budgetItems as $index => $item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $this->budgetItems[$index]['total'] = $quantity * $unitPrice;
        }

        // Calculate subtotal
        $subtotal = collect($this->budgetItems)->sum('total');
        $vatRate = floatval($this->editForm['vat_rate'] ?? 0);
        $vatAmount = $subtotal * ($vatRate / 100);

        // Update estimated cost and contract value
        $this->editForm['estimated_cost'] = $subtotal;
        $this->editForm['contract_value'] = ($this->editForm['vat_included'] ?? false) ? $subtotal : $subtotal + $vatAmount;
    }

    public function openEditModal($id)
    {
        $project = Project::with('budgetItems')->findOrFail($id);

        $this->editProjectId = $project->id;
        $this->editForm = [
            'name' => $project->name,
            'project_code' => $project->project_code,
            'quotation_number' => $project->quotation_number,
            'client_name' => $project->client_name,
            'client_phone' => $project->client_phone,
            'client_email' => $project->client_email,
            'client_address' => $project->client_address,
            'location' => $project->location,
            'description' => $project->description,
            'contract_value' => $project->contract_value,
            'estimated_cost' => $project->estimated_cost,
            'vat_rate' => $project->vat_rate ?? 0,
            'vat_included' => $project->vat_included ?? false,
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'valid_until' => $project->valid_until,
            'status' => $project->status,
        ];

        // Load budget items
        $this->budgetItems = [];
        foreach ($project->budgetItems as $item) {
            $this->budgetItems[] = [
                'id' => $item->id,
                'section' => $item->section,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ];
        }

        if (empty($this->budgetItems)) {
            $this->addBudgetItem();
        }

        $this->editingProject = true;
    }

    public function viewDetails($id)
    {
        $this->selectedProject = Project::with('budgetItems')->findOrFail($id);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedProject = null;
    }

    public function updateProject()
    {
        // Validate unique project code except for current project
        $this->rules['editForm.project_code'] = 'required|string|max:50|unique:projects,project_code,' . $this->editProjectId;

        $this->validate();

        $this->recalculateTotals();

        $subtotal = collect($this->budgetItems)->sum('total');
        $vatRate = floatval($this->editForm['vat_rate'] ?? 0);
        $vatAmount = $subtotal * ($vatRate / 100);
        $totalWithVat = ($this->editForm['vat_included'] ?? false) ? $subtotal : $subtotal + $vatAmount;

        $project = Project::findOrFail($this->editProjectId);

        // Update project data
        $project->update([
            'name' => $this->editForm['name'],
            'project_code' => $this->editForm['project_code'],
            'quotation_number' => $this->editForm['quotation_number'],
            'client_name' => $this->editForm['client_name'],
            'client_phone' => $this->editForm['client_phone'],
            'client_email' => $this->editForm['client_email'],
            'client_address' => $this->editForm['client_address'],
            'location' => $this->editForm['location'],
            'description' => $this->editForm['description'],
            'contract_value' => $totalWithVat,
            'estimated_cost' => $subtotal,
            'budget_total' => $totalWithVat,
            'vat_rate' => $this->editForm['vat_rate'],
            'vat_included' => $this->editForm['vat_included'],
            'start_date' => $this->editForm['start_date'],
            'end_date' => $this->editForm['end_date'],
            'valid_until' => $this->editForm['valid_until'],
            'status' => $this->editForm['status'],
        ]);

        // Update budget items
        $existingItemIds = [];
        foreach ($this->budgetItems as $index => $item) {
            if (!empty($item['description'])) {
                $budgetItem = BudgetItem::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'project_id' => $project->id,
                        'section' => $item['section'],
                        'description' => $item['description'],
                        'unit' => $item['unit'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $item['total'],
                        'sort_order' => $index,
                    ]
                );
                if ($budgetItem->id) {
                    $existingItemIds[] = $budgetItem->id;
                }
            }
        }

        // Delete budget items that were removed
        BudgetItem::where('project_id', $project->id)
            ->whereNotIn('id', $existingItemIds)
            ->delete();

        $vatStatus = $this->editForm['vat_rate'] > 0
            ? ($this->editForm['vat_included'] ? 'VAT ' . $this->editForm['vat_rate'] . '% included' : 'VAT ' . $this->editForm['vat_rate'] . '% added')
            : 'No VAT';

        session()->flash('message', 'Project updated successfully with ' . $vatStatus . '.');

        $this->closeEditModal();
        $this->loadStatusCounts();
        $this->resetPage();
    }

    public function closeEditModal()
    {
        $this->editingProject = false;
        $this->editProjectId = null;
        $this->editForm = [
            'name' => '',
            'project_code' => '',
            'quotation_number' => '',
            'client_name' => '',
            'client_phone' => '',
            'client_email' => '',
            'client_address' => '',
            'location' => '',
            'description' => '',
            'contract_value' => '',
            'estimated_cost' => '',
            'vat_rate' => 0,
            'vat_included' => false,
            'start_date' => '',
            'end_date' => '',
            'valid_until' => '',
            'status' => 'planning',
        ];
        $this->budgetItems = [];
        $this->resetErrorBag();
    }

    public function confirmDelete($id)
    {
        $project = Project::find($id);
        if ($project) {
            $this->deleteId = $id;
            $this->deleteProjectName = $project->name;
            $this->confirmingDelete = true;
        }
    }

    public function deleteProject()
    {
        if ($this->deleteId) {
            $project = Project::find($this->deleteId);

            if ($project) {
                $project->workers()->detach();
                $project->expenses()->delete();
                $project->budgetItems()->delete();
                $project->delete();

                session()->flash('message', 'Project deleted successfully.');
                $this->loadStatusCounts();
            }

            $this->confirmingDelete = false;
            $this->deleteId = null;
            $this->deleteProjectName = '';
            $this->resetPage();
        }
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->deleteProjectName = '';
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Project::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('project_code', 'like', "%{$this->search}%")
                    ->orWhere('quotation_number', 'like', "%{$this->search}%")
                    ->orWhere('client_name', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        $projects = $query->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.hr.projects.edit-project', [
            'projects' => $projects
        ])->layout('layouts.hrmanagerdashboard');
    }
}
