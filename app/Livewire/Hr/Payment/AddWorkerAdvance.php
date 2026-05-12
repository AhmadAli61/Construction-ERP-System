<?php

namespace App\Livewire\Hr\Payment;

use Livewire\Component;
use App\Models\WorkerAdvance;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;

class AddWorkerAdvance extends Component
{
    public $advanceId;
    public $worker_id;
    public $amount;
    public $advance_date;
    public $notes;
    public $status = 'pending';

    // For display purposes
    public $currentBalance = 0;
    public $totalAdvances = 0;
    public $totalDeducted = 0;
    public $advanceHistory = [];

    protected function rules()
    {
        return [
            'worker_id' => 'required|exists:workers,id',
            'amount' => 'required|numeric|min:0.01',
            'advance_date' => 'required|date',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $advance = WorkerAdvance::findOrFail($id);
            $this->advanceId = $advance->id;
            $this->worker_id = $advance->worker_id;
            $this->amount = $advance->amount;
            $this->advance_date = $advance->advance_date;
            $this->notes = $advance->notes;
            $this->status = $advance->status;
        }

        if ($this->worker_id) {
            $this->loadWorkerBalance();
        }
    }

    public function updatedWorkerId()
    {
        $this->loadWorkerBalance();
    }

    public function loadWorkerBalance()
    {
        if (!$this->worker_id) {
            $this->currentBalance = 0;
            $this->totalAdvances = 0;
            $this->totalDeducted = 0;
            $this->advanceHistory = [];
            return;
        }

        // Get all advances for this worker (including deductions)
        $allAdvances = WorkerAdvance::where('worker_id', $this->worker_id)
            ->orderBy('advance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $this->totalAdvances = $allAdvances->where('is_deduction', false)->sum('amount');
        $this->totalDeducted = $allAdvances->where('is_deduction', true)->sum('amount');

        // Get the latest running balance
        $latest = $allAdvances->first();
        $this->currentBalance = $latest ? $latest->running_balance : 0;

        // Get history for display (only non-deduction entries for simplicity)
        $this->advanceHistory = $allAdvances->where('is_deduction', false)->take(10);
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Get current balance before this transaction
            $currentBalance = $this->getCurrentBalance($this->worker_id);

            // Calculate new balance (add amount)
            $newBalance = $currentBalance + $this->amount;

            // Create new advance record
            $advance = WorkerAdvance::create([
                'worker_id' => $this->worker_id,
                'amount' => $this->amount,
                'is_deduction' => false,
                'advance_date' => $this->advance_date,
                'notes' => $this->notes,
                'status' => 'pending',
                'remaining_amount' => $this->amount, // Initially, remaining is full amount
                'running_balance' => $newBalance,
            ]);

            // Update running balance for ALL existing records? No, we only store balance per transaction

            DB::commit();

            session()->flash('message', 'Advance added successfully! New balance: €' . number_format($newBalance, 2));

            // Reset form
            $this->reset(['amount', 'notes']);
            $this->advance_date = now()->format('Y-m-d');
            $this->loadWorkerBalance();

            return redirect()->route('hr.advances.list');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error saving advance: ' . $e->getMessage());
        }
    }

    private function getCurrentBalance($workerId)
    {
        $lastAdvance = WorkerAdvance::where('worker_id', $workerId)
            ->orderBy('id', 'desc')
            ->first();

        return $lastAdvance ? $lastAdvance->running_balance : 0;
    }

    public function render()
    {
        return view('livewire.hr.payment.add-worker-advance', [
            'workers' => Worker::where('status', 'active')->get(),
        ])->layout('layouts.hrmanagerdashboard');
    }
}
