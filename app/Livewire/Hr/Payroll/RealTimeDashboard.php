<?php

namespace App\Livewire\Hr\Payroll;

use Livewire\Component;
use App\Models\Worker;
use App\Models\PayrollBatch;
use App\Services\PayrollService;
use Carbon\Carbon;

class RealTimeDashboard extends Component
{
    public $selectedWorker = null;
    public $workers = [];
    public $earningsData = null;
    public $overtimeMultiplier = 1.5;
    public $currentBatch = null;
    public $payrollBatches = [];
    
    public function mount()
    {
        $this->workers = Worker::where('status', 'active')->get();
        $this->loadPayrollBatches();
        $this->loadCurrentBatch();
    }

    public function loadPayrollBatches()
    {
        $this->payrollBatches = PayrollBatch::with('payrolls.worker')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function loadCurrentBatch()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $this->currentBatch = PayrollBatch::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();
    }

    public function updatedSelectedWorker()
    {
        if ($this->selectedWorker) {
            $worker = Worker::find($this->selectedWorker);
            $service = new PayrollService();
            $service->setOvertimeMultiplier($this->overtimeMultiplier);
            $this->earningsData = $service->getRealTimeEarnings($worker);
        } else {
            $this->earningsData = null;
        }
    }

    public function updatedOvertimeMultiplier()
    {
        if ($this->selectedWorker) {
            $this->updatedSelectedWorker();
        }
    }

    public function generatePayroll()
    {
        $this->validate([
            'overtimeMultiplier' => 'required|numeric|min:1|max:3',
        ]);

        try {
            $service = new PayrollService();
            $batch = $service->generatePayrollBatch(
                Carbon::now()->year,
                Carbon::now()->month,
                $this->overtimeMultiplier
            );

            session()->flash('message', 'Payroll generated successfully! Batch #' . $batch->id);
            $this->loadPayrollBatches();
            $this->loadCurrentBatch();
            $this->earningsData = null;
            $this->selectedWorker = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    public function finalizeBatch($batchId)
    {
        $batch = PayrollBatch::find($batchId);
        if ($batch && $batch->status === 'draft') {
            $batch->update([
                'status' => 'finalized',
                'finalized_at' => now(),
            ]);
            session()->flash('message', 'Payroll batch finalized successfully!');
            $this->loadPayrollBatches();
            $this->loadCurrentBatch();
        }
    }

    public function render()
    {
        return view('livewire.hr.payroll.real-time-dashboard')->layout('layouts.hrmanagerdashboard');
    }
}
