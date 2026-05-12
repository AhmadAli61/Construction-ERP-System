<?php

namespace App\Livewire\Hr\Payroll;

use Livewire\Component;
use App\Models\PayrollBatch;
use App\Models\Payroll;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PayrollHistory extends Component
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
    
    // Additional filters
    public $yearFilter = '';
    public $tempYearFilter = '';
    public $monthFilter = '';
    public $tempMonthFilter = '';
    
    public $selectedBatch = null;
    public $selectedPayroll = null;
    public $confirmingFinalize = false;
    public $batchToFinalize = null;
    public $confirmingDelete = false;
    public $batchToDelete = null;

    protected $queryString = ['search', 'statusFilter', 'perPage', 'yearFilter', 'monthFilter'];

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->search = $this->tempSearch;
        $this->statusFilter = $this->tempStatusFilter;
        $this->yearFilter = $this->tempYearFilter;
        $this->monthFilter = $this->tempMonthFilter;
        $this->resetPage();
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->tempSearch = '';
        $this->tempStatusFilter = '';
        $this->tempYearFilter = '';
        $this->tempMonthFilter = '';
        
        $this->search = '';
        $this->statusFilter = '';
        $this->yearFilter = '';
        $this->monthFilter = '';
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
    
    public function clearYearFilter()
    {
        $this->tempYearFilter = '';
        $this->yearFilter = '';
        $this->resetPage();
    }
    
    public function clearMonthFilter()
    {
        $this->tempMonthFilter = '';
        $this->monthFilter = '';
        $this->resetPage();
    }
    
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function viewPayrollDetails($payrollId)
    {
        $this->selectedPayroll = Payroll::with(['worker', 'projectBreakdowns.project', 'batch'])
            ->find($payrollId);
    }

    public function finalizeBatch($batchId)
    {
        $this->batchToFinalize = $batchId;
        $this->confirmingFinalize = true;
    }

    public function confirmFinalize()
    {
        if ($this->batchToFinalize) {
            $batch = PayrollBatch::find($this->batchToFinalize);
            if ($batch && $batch->status === 'draft') {
                $batch->update([
                    'status' => 'finalized',
                    'finalized_at' => now(),
                ]);
                session()->flash('message', 'Payroll batch #' . $batch->id . ' has been finalized successfully.');
            }
        }
        
        $this->confirmingFinalize = false;
        $this->batchToFinalize = null;
    }

    public function confirmDeleteBatch($batchId)
    {
        $this->batchToDelete = $batchId;
        $this->confirmingDelete = true;
    }

    public function deleteBatch()
    {
        if ($this->batchToDelete) {
            try {
                DB::beginTransaction();
                
                $batch = PayrollBatch::with('payrolls')->find($this->batchToDelete);
                
                if (!$batch) {
                    session()->flash('error', 'Payroll batch not found.');
                    DB::rollBack();
                    $this->confirmingDelete = false;
                    $this->batchToDelete = null;
                    return;
                }

                // Check if batch is finalized - prevent deletion if needed (optional)
                if ($batch->status === 'finalized') {
                    session()->flash('error', 'Cannot delete a finalized payroll batch. Please unfinalize it first if needed.');
                    DB::rollBack();
                    $this->confirmingDelete = false;
                    $this->batchToDelete = null;
                    return;
                }

                // Delete all associated payrolls and their project breakdowns
                foreach ($batch->payrolls as $payroll) {
                    // Delete project breakdowns first
                    $payroll->projectBreakdowns()->delete();
                    // Delete the payroll
                    $payroll->delete();
                }
                
                // Delete the batch
                $batch->delete();
                
                DB::commit();
                
                session()->flash('message', 'Payroll batch #' . $this->batchToDelete . ' and all associated payrolls have been deleted successfully.');
                
                // Reset pagination to avoid empty page
                if ($this->getBatchesQuery()->count() <= ($this->perPage * ($this->getPage() - 1))) {
                    $this->resetPage();
                }
                
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'Error deleting payroll batch: ' . $e->getMessage());
            }
        }
        
        $this->confirmingDelete = false;
        $this->batchToDelete = null;
    }

    protected function getBatchesQuery()
    {
        return PayrollBatch::with(['payrolls.worker'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->when($this->search, function ($query) {
                $query->where('year', 'like', '%' . $this->search . '%')
                    ->orWhere('month', 'like', '%' . $this->search . '%')
                    ->orWhereHas('payrolls.worker', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->yearFilter, function ($query) {
                $query->where('year', $this->yearFilter);
            })
            ->when($this->monthFilter, function ($query) {
                $query->where('month', $this->monthFilter);
            });
    }
    public function closePayrollModal()
{
    $this->selectedPayroll = null;
}

    protected function getPage()
    {
        return request()->get('page', 1);
    }

    public function generateInvoice($payrollId)
    {
        $payroll = Payroll::with(['worker', 'projectBreakdowns.project', 'batch'])
            ->find($payrollId);

        session()->flash('message', 'Invoice generated for payroll #' . $payrollId);
    }

    public function render()
    {
        // Calculate summary statistics
        $totalBatches = PayrollBatch::count();
        $totalGross = PayrollBatch::with('payrolls')->get()->sum(function ($batch) {
            return $batch->payrolls->sum('gross_amount');
        });
        $totalNet = PayrollBatch::with('payrolls')->get()->sum(function ($batch) {
            return $batch->payrolls->sum('net_amount');
        });
        $totalWorkers = PayrollBatch::with('payrolls')->get()->sum(function ($batch) {
            return $batch->payrolls->count();
        });
        
        // Get available years and months for filters
        $availableYears = PayrollBatch::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $batches = $this->getBatchesQuery()->paginate($this->perPage);

        return view('livewire.hr.payroll.payroll-history', [
            'batches' => $batches,
            'totalBatches' => $totalBatches,
            'totalGross' => $totalGross,
            'totalNet' => $totalNet,
            'totalWorkers' => $totalWorkers,
            'availableYears' => $availableYears,
            'months' => $months,
        ])->layout('layouts.hrmanagerdashboard');
    }
}