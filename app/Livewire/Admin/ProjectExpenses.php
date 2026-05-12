<?php
// app/Livewire/Admin/ProjectExpenses.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ProjectExpenses extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selectedProject = null;
    public $projects = [];
    public $categories = [];

    // Form fields
    public $expenseId = null;
    public $expense_type = 'project'; // NEW: project or company
    public $project_id = null;
    public $category_id = null;
    public $description = '';
    public $amount = null;
    public $expense_date = null;
    public $invoice_number = ''; // NEW
    public $notes = '';

    // Delete confirmation properties
    public $deleteId = null;
    public $deleteDescription = '';

    // Actual filter properties
    public $filterProject = null;
    public $filterCategory = null;
    public $filterDateFrom = null;
    public $filterDateTo = null;
    public $filterExpenseType = null; // NEW
    public $search = '';
    public $perPage = 10;

    // Temporary properties for button search
    public $tempFilterProject = null;
    public $tempFilterCategory = null;
    public $tempFilterDateFrom = null;
    public $tempFilterDateTo = null;
    public $tempFilterExpenseType = null; // NEW
    public $tempSearch = '';

    // Statistics
    public $totalExpenses = 0;
    public $totalCompanyExpenses = 0; // NEW
    public $totalProjectExpenses = 0; // NEW
    public $averageExpense = 0;
    public $totalTransactions = 0;
    public $activeProjectsCount = 0;
    public $categoriesUsedCount = 0;
    public $totalByCategory;
    public $projectTotal = 0;
    
    public $isSearching = false;

    public function mount()
    {
        $this->projects = Project::orderBy('name')->get();
        $this->categories = ExpenseCategory::active()->orderBy('sort_order')->get();
        $this->expense_date = Carbon::now()->format('Y-m-d');
        $this->totalByCategory = collect();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->dispatch('open-modal');
    }

    public function performSearch()
    {
        $this->filterProject = $this->tempFilterProject;
        $this->filterCategory = $this->tempFilterCategory;
        $this->filterDateFrom = $this->tempFilterDateFrom;
        $this->filterDateTo = $this->tempFilterDateTo;
        $this->filterExpenseType = $this->tempFilterExpenseType; // NEW
        $this->search = $this->tempSearch;
        $this->isSearching = true;
        
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function resetFilters()
    {
        $this->tempFilterProject = null;
        $this->tempFilterCategory = null;
        $this->tempFilterDateFrom = null;
        $this->tempFilterDateTo = null;
        $this->tempFilterExpenseType = null; // NEW
        $this->tempSearch = '';
        
        $this->filterProject = null;
        $this->filterCategory = null;
        $this->filterDateFrom = null;
        $this->filterDateTo = null;
        $this->filterExpenseType = null; // NEW
        $this->search = '';
        $this->isSearching = false;
        
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function clearProjectFilter()
    {
        $this->tempFilterProject = null;
        $this->filterProject = null;
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function clearCategoryFilter()
    {
        $this->tempFilterCategory = null;
        $this->filterCategory = null;
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function clearExpenseTypeFilter() // NEW
    {
        $this->tempFilterExpenseType = null;
        $this->filterExpenseType = null;
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function clearDateFromFilter()
    {
        $this->tempFilterDateFrom = null;
        $this->filterDateFrom = null;
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function clearDateToFilter()
    {
        $this->tempFilterDateTo = null;
        $this->filterDateTo = null;
        $this->resetPage();
        $this->calculateStats();
    }
    
    public function clearSearch()
    {
        $this->tempSearch = '';
        $this->search = '';
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedExpenseType() // NEW: Clear project when switching to company
    {
        if ($this->expense_type === 'company') {
            $this->project_id = null;
        }
    }

    public function calculateStats()
    {
        $query = ProjectExpense::query();

        if ($this->filterProject) {
            $query->where('project_id', $this->filterProject);
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->filterExpenseType) { // NEW
            $query->where('expense_type', $this->filterExpenseType);
        }

        if ($this->filterDateFrom) {
            $query->where('expense_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('expense_date', '<=', $this->filterDateTo);
        }

        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        $this->totalExpenses = $query->sum('amount');
        $this->totalTransactions = $query->count();
        
        // NEW: Separate totals
        $this->totalProjectExpenses = (clone $query)->where('expense_type', 'project')->sum('amount');
        $this->totalCompanyExpenses = (clone $query)->where('expense_type', 'company')->sum('amount');
        
        $this->averageExpense = $this->totalTransactions > 0 
            ? $this->totalExpenses / $this->totalTransactions 
            : 0;

        $activeProjectsQuery = clone $query;
        $this->activeProjectsCount = $activeProjectsQuery->distinct('project_id')->count('project_id');

        $categoriesUsedQuery = clone $query;
        $this->categoriesUsedCount = $categoriesUsedQuery->distinct('category_id')->count('category_id');

        $this->totalByCategory = $query->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        if (!$this->totalByCategory) {
            $this->totalByCategory = collect();
        }
    }

    public function selectProject($projectId)
    {
        $this->selectedProject = Project::with('expenses.category')->find($projectId);
        $this->projectTotal = $this->selectedProject ? $this->selectedProject->expenses->sum('amount') : 0;
        $this->calculateStats();
    }

    public function editExpense($id)
    {
        $expense = ProjectExpense::find($id);
        if ($expense) {
            $this->expenseId = $expense->id;
            $this->expense_type = $expense->expense_type; // NEW
            $this->project_id = $expense->project_id;
            $this->category_id = $expense->category_id;
            $this->description = $expense->description;
            $this->amount = $expense->amount;
            $this->expense_date = $expense->expense_date->format('Y-m-d');
            $this->invoice_number = $expense->invoice_number; // NEW
            $this->notes = $expense->notes;

            $this->dispatch('open-modal');
        }
    }

    public function saveExpense()
    {
        $rules = [
            'expense_type' => 'required|in:project,company',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|min:3',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
        ];
        
        // Add project validation only if expense type is project
        if ($this->expense_type === 'project') {
            $rules['project_id'] = 'required|exists:projects,id';
        }
        
        $this->validate($rules);
        
        // Check duplicate invoice number manually
        if ($this->invoice_number) {
            $exists = ProjectExpense::where('invoice_number', $this->invoice_number)
                ->when($this->expenseId, function($query) {
                    $query->where('id', '!=', $this->expenseId);
                })
                ->exists();
            
            if ($exists) {
                $this->addError('invoice_number', 'This invoice number already exists!');
                return;
            }
        }

        $data = [
            'expense_type' => $this->expense_type,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
            'invoice_number' => $this->invoice_number ?: null,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ];
        
        // Only add project_id if expense_type is project
        if ($this->expense_type === 'project') {
            $data['project_id'] = $this->project_id;
        } else {
            $data['project_id'] = null;
        }

        ProjectExpense::updateOrCreate(
            ['id' => $this->expenseId],
            $data
        );

        session()->flash('message', $this->expenseId ? 'Expense updated successfully!' : 'Expense added successfully!');

        $this->resetForm();
        $this->calculateStats();

        if ($this->selectedProject && $this->project_id && $this->selectedProject->id == $this->project_id) {
            $this->selectProject($this->project_id);
        }

        $this->dispatch('close-modal');
    }

    public function confirmDelete($id)
    {
        $expense = ProjectExpense::find($id);
        if ($expense) {
            $this->deleteId = $expense->id;
            $this->deleteDescription = $expense->description;
            $this->dispatch('open-delete-modal');
        }
    }

    public function deleteExpense()
    {
        if ($this->deleteId) {
            $expense = ProjectExpense::find($this->deleteId);
            if ($expense) {
                $projectId = $expense->project_id;
                $expense->delete();

                session()->flash('message', 'Expense deleted successfully!');
                $this->calculateStats();

                if ($this->selectedProject && $this->selectedProject->id == $projectId) {
                    $this->selectProject($projectId);
                }
            }
        }

        $this->closeDeleteModal();
    }

    public function closeDeleteModal()
    {
        $this->deleteId = null;
        $this->deleteDescription = '';
        $this->dispatch('close-delete-modal');
    }

    public function resetForm()
    {
        $this->expenseId = null;
        $this->expense_type = 'project';
        $this->project_id = null;
        $this->category_id = null;
        $this->description = '';
        $this->amount = null;
        $this->expense_date = Carbon::now()->format('Y-m-d');
        $this->invoice_number = '';
        $this->notes = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $expensesQuery = ProjectExpense::with(['project', 'category', 'createdBy'])
            ->when($this->filterProject, function ($query) {
                $query->where('project_id', $this->filterProject);
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterExpenseType, function ($query) {
                $query->where('expense_type', $this->filterExpenseType);
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->where('expense_date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->where('expense_date', '<=', $this->filterDateTo);
            })
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('expense_date', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.project-expenses', [
            'expenses' => $expensesQuery,
        ])->layout('layouts.admindashboard');
    }
}