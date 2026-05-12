<?php

namespace App\Livewire\Hr\Payment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Worker;
use App\Models\WorkerAdvance;
use Illuminate\Support\Facades\DB;

class WorkerAdvanceList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Actual filter properties (applied after search button click)
    public $search = '';
    public $perPage = 10;

    // Temporary properties for button search
    public $tempSearch = '';

    // Status filter
    public $statusFilter = '';
    public $tempStatusFilter = '';

    // Sort by
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    // Edit modal properties
    public $showEditModal = false;
    public $editAdvanceId = null;
    public $editWorkerId = null;
    public $editWorkerName = '';
    public $editAmount = '';
    public $editAdvanceDate = '';
    public $editNotes = '';
    public $editRemainingAmount = '';
    public $editStatus = '';

    // For showing worker details
    public $selectedWorkerId = null;
    public $showWorkerDetails = false;
    public $workerName = '';
    public $workerDesignation = '';
    public $workerEmail = '';
    public $workerPhone = '';

    // Worker advance summary
    public $totalTaken = 0;
    public $totalPaid = 0;
    public $remainingBalance = 0;

    // Lists for modal
    public $advancesGiven = [];
    public $deductionsMade = [];

    protected $queryString = ['search', 'statusFilter', 'sortBy', 'sortDirection', 'perPage'];

    // Validation rules for edit
    protected function editRules()
    {
        return [
            'editAmount' => 'required|numeric|min:0.01',
            'editAdvanceDate' => 'required|date',
            'editStatus' => 'required|in:pending,paid',
            'editNotes' => 'nullable|string',
            'editRemainingAmount' => 'nullable|numeric|min:0',
        ];
    }

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->search = $this->tempSearch;
        $this->statusFilter = $this->tempStatusFilter;
        $this->resetPage();
    }

    // Reset all filters
    public function resetFilters()
    {
        $this->tempSearch = '';
        $this->tempStatusFilter = '';
        $this->search = '';
        $this->statusFilter = '';
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
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

    // Sort by column
    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // Open edit modal for an advance - FIRST close worker details, then open edit
    public function editAdvance($advanceId)
    {
        $advance = WorkerAdvance::findOrFail($advanceId);

        $this->editAdvanceId = $advance->id;
        $this->editWorkerId = $advance->worker_id;
        $this->editWorkerName = $advance->worker->name ?? 'Unknown';
        $this->editAmount = $advance->amount;
        $this->editAdvanceDate = $advance->advance_date->format('Y-m-d');
        $this->editNotes = $advance->notes;
        $this->editRemainingAmount = $advance->remaining_amount;
        $this->editStatus = $advance->status;

        // Close worker details modal first
        $this->showWorkerDetails = false;

        // Then open edit modal
        $this->showEditModal = true;
    }

    // Update advance
    public function updateAdvance()
    {
        $this->validate($this->editRules());

        DB::beginTransaction();

        try {
            $advance = WorkerAdvance::findOrFail($this->editAdvanceId);

            // Calculate new remaining amount if status changed
            $remainingAmount = $this->editRemainingAmount;
            if ($this->editStatus === 'paid') {
                $remainingAmount = 0;
            } elseif (!$remainingAmount && $remainingAmount !== 0) {
                $remainingAmount = $this->editAmount;
            }

            // Get the difference in amount to update running balance
            $oldAmount = $advance->amount;
            $amountDifference = $this->editAmount - $oldAmount;

            // Update the advance
            $advance->update([
                'amount' => $this->editAmount,
                'advance_date' => $this->editAdvanceDate,
                'notes' => $this->editNotes,
                'remaining_amount' => $remainingAmount,
                'status' => $this->editStatus,
            ]);

            // If amount changed, update running_balance for this and all subsequent records
            if ($amountDifference != 0) {
                $this->updateRunningBalances($advance->worker_id, $advance->id, $amountDifference);
            }

            DB::commit();

            session()->flash('message', 'Advance updated successfully!');
            $this->showEditModal = false;
            $this->resetEditForm();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error updating advance: ' . $e->getMessage());
        }
    }

    // Update running balances for all records after the edited one
    private function updateRunningBalances($workerId, $advanceId, $amountDifference)
    {
        $laterAdvances = WorkerAdvance::where('worker_id', $workerId)
            ->where('id', '>', $advanceId)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($laterAdvances as $adv) {
            $newBalance = $adv->running_balance + $amountDifference;
            $adv->update(['running_balance' => $newBalance]);
        }
    }

    // Reset edit form
    public function resetEditForm()
    {
        $this->editAdvanceId = null;
        $this->editWorkerId = null;
        $this->editWorkerName = '';
        $this->editAmount = '';
        $this->editAdvanceDate = '';
        $this->editNotes = '';
        $this->editRemainingAmount = '';
        $this->editStatus = '';
    }

    // Close edit modal and refresh data
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetEditForm();

        // Refresh the worker details if it was open
        if ($this->selectedWorkerId) {
            $this->viewWorkerDetails($this->selectedWorkerId);
        }
    }

    // Close worker details
    public function closeWorkerDetails()
    {
        $this->showWorkerDetails = false;
        $this->selectedWorkerId = null;
    }

    public function viewWorkerDetails($workerId)
    {
        $this->selectedWorkerId = $workerId;

        // Get worker details
        $worker = Worker::find($workerId);
        if ($worker) {
            $this->workerName = $worker->name;
            $this->workerDesignation = $worker->designation ?? 'Not assigned';
            $this->workerEmail = $worker->email ?? 'N/A';
            $this->workerPhone = $worker->phone ?? 'N/A';
        }

        // Get all advances given (is_deduction = false)
        $this->advancesGiven = WorkerAdvance::where('worker_id', $workerId)
            ->where('is_deduction', false)
            ->orderBy('advance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all deductions made (is_deduction = true)
        $this->deductionsMade = WorkerAdvance::where('worker_id', $workerId)
            ->where('is_deduction', true)
            ->orderBy('advance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('deductedInPayroll')
            ->get();

        // Calculate totals
        $this->totalTaken = $this->advancesGiven->sum('amount');
        $this->totalPaid = $this->deductionsMade->sum('amount');

        // Get current running balance
        $lastAdvance = WorkerAdvance::where('worker_id', $workerId)
            ->orderBy('id', 'desc')
            ->first();
        $this->remainingBalance = $lastAdvance ? $lastAdvance->running_balance : 0;

        $this->showWorkerDetails = true;
    }

    public function render()
    {
        // Get unique workers with their advance summary
        $workersQuery = Worker::where('status', 'active');

        // Apply search filter
        if (!empty($this->search)) {
            $workersQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('designation', 'like', '%' . $this->search . '%')
                    ->orWhere('department', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter (workers with advances only)
        if ($this->statusFilter === 'has_advances') {
            $workersQuery->whereHas('advances', function ($query) {
                $query->where('is_deduction', false);
            });
        } elseif ($this->statusFilter === 'has_balance') {
            $workersQuery->whereHas('advances', function ($query) {
                $query->where('is_deduction', false)
                    ->where('running_balance', '>', 0);
            });
        } elseif ($this->statusFilter === 'cleared') {
            $workersQuery->whereHas('advances', function ($query) {
                $query->where('is_deduction', false)
                    ->where('running_balance', 0);
            });
        }

        // Apply sorting
        $workersQuery->orderBy($this->sortBy, $this->sortDirection);

        $workers = $workersQuery->paginate($this->perPage);

        // For each worker, calculate advance summary
        $workerSummaries = [];
        foreach ($workers as $worker) {
            // Get all advances given (not deductions)
            $advancesGiven = WorkerAdvance::where('worker_id', $worker->id)
                ->where('is_deduction', false)
                ->get();

            // Get all deductions
            $deductions = WorkerAdvance::where('worker_id', $worker->id)
                ->where('is_deduction', true)
                ->get();

            // Get current balance
            $lastAdvance = WorkerAdvance::where('worker_id', $worker->id)
                ->orderBy('id', 'desc')
                ->first();

            $totalTaken = $advancesGiven->sum('amount');
            $totalPaid = $deductions->sum('amount');
            $currentBalance = $lastAdvance ? $lastAdvance->running_balance : 0;

            $workerSummaries[] = (object) [
                'worker' => $worker,
                'total_taken' => $totalTaken,
                'total_paid' => $totalPaid,
                'current_balance' => $currentBalance,
                'has_advances' => $advancesGiven->count() > 0,
                'last_advance_date' => $advancesGiven->isNotEmpty() ? $advancesGiven->first()->advance_date : null,
            ];
        }

        // Calculate overall statistics
        $allAdvancesGiven = WorkerAdvance::where('is_deduction', false)->sum('amount');
        $allDeductions = WorkerAdvance::where('is_deduction', true)->sum('amount');
        $workersWithBalance = WorkerAdvance::select('worker_id', DB::raw('MAX(id) as last_id'))
            ->where('is_deduction', false)
            ->groupBy('worker_id')
            ->get();

        $totalOutstanding = 0;
        foreach ($workersWithBalance as $item) {
            $lastAdvance = WorkerAdvance::find($item->last_id);
            if ($lastAdvance) {
                $totalOutstanding += $lastAdvance->running_balance;
            }
        }

        $totalWorkers = Worker::where('status', 'active')->count();
        $workersWithAdvances = WorkerAdvance::where('is_deduction', false)->distinct('worker_id')->count('worker_id');

        return view('livewire.hr.payment.worker-advance-list', [
            'workerSummaries' => $workerSummaries,
            'workers' => $workers,
            'totalGiven' => $allAdvancesGiven,
            'totalPaidOverall' => $allDeductions,
            'totalOutstanding' => $totalOutstanding,
            'totalWorkers' => $totalWorkers,
            'workersWithAdvances' => $workersWithAdvances,
        ])->layout('layouts.hrmanagerdashboard');
    }
}
