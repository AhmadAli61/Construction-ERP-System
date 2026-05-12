<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payroll;
use App\Models\Worker;
use App\Models\MonthlyPayrollSummary;
use App\Models\Project;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollDashboard extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Actual filter properties (applied after search button click)
    public $selectedYear = null;
    public $selectedMonth = null;
    public $searchWorker = '';
    public $searchProject = '';
    public $minGrossAmount = '';
    public $maxGrossAmount = '';
    public $perPage = 10;

    // Temporary properties for button search
    public $tempSelectedYear = null;
    public $tempSelectedMonth = null;
    public $tempSearchWorker = '';
    public $tempSearchProject = '';
    public $tempMinGrossAmount = '';
    public $tempMaxGrossAmount = '';

    // Statistics
    public $totalPayrolls = 0;
    public $totalGrossAmount = 0;
    public $totalNetAmount = 0;
    public $totalWorkers = 0;
    public $totalAdvancesDeducted = 0;
    public $averagePayroll = 0;

    // Summary data for current month
    public $monthlySummary = null;
    public $availableYears = [];
    public $availableMonths = [];

    // Save summary modal
    public $showSaveModal = false;
    public $saveSummaryNotes = '';
    public $savingSummary = false;

    // Fetching status
    public $isFetching = false;
    public $fetchMessage = '';
    
    // Search flag
    public $isSearching = false;
    
    // View Payroll Modal
    public $showViewModal = false;
    public $selectedPayroll = null;

    public function mount()
    {
        $this->loadAvailableYears();
        $this->loadAvailableMonths();

        // Set default to current month
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;

        $this->loadStatistics();
        $this->loadMonthlySummary();
    }

    public function loadAvailableYears()
    {
        // Get years from payroll_batches through payrolls
        $years = DB::table('payrolls')
            ->join('payroll_batches', 'payrolls.payroll_batch_id', '=', 'payroll_batches.id')
            ->select('payroll_batches.year')
            ->distinct()
            ->orderBy('payroll_batches.year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no payrolls exist yet, add current and previous years
        if (empty($years)) {
            $currentYear = Carbon::now()->year;
            $years = [$currentYear, $currentYear - 1];
        }

        $this->availableYears = $years;
    }

    public function loadAvailableMonths()
    {
        $this->availableMonths = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->selectedYear = $this->tempSelectedYear;
        $this->selectedMonth = $this->tempSelectedMonth;
        $this->searchWorker = $this->tempSearchWorker;
        $this->searchProject = $this->tempSearchProject;
        $this->minGrossAmount = $this->tempMinGrossAmount;
        $this->maxGrossAmount = $this->tempMaxGrossAmount;
        $this->isSearching = true;
        
        $this->resetPage();
        $this->loadStatistics();
        $this->loadMonthlySummary();
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->tempSearchWorker = '';
        $this->tempSearchProject = '';
        $this->tempMinGrossAmount = '';
        $this->tempMaxGrossAmount = '';
        
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        $this->searchWorker = '';
        $this->searchProject = '';
        $this->minGrossAmount = '';
        $this->maxGrossAmount = '';
        $this->isSearching = false;
        
        $this->resetPage();
        $this->loadStatistics();
        $this->loadMonthlySummary();
    }
    
    // Clear individual filters
    public function clearSearchWorker()
    {
        $this->tempSearchWorker = '';
        $this->searchWorker = '';
        $this->resetPage();
        $this->loadStatistics();
    }
    
    public function clearSearchProject()
    {
        $this->tempSearchProject = '';
        $this->searchProject = '';
        $this->resetPage();
        $this->loadStatistics();
    }
    
    public function clearMinGrossAmount()
    {
        $this->tempMinGrossAmount = '';
        $this->minGrossAmount = '';
        $this->resetPage();
        $this->loadStatistics();
    }
    
    public function clearMaxGrossAmount()
    {
        $this->tempMaxGrossAmount = '';
        $this->maxGrossAmount = '';
        $this->resetPage();
        $this->loadStatistics();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /**
     * Fetch existing payrolls for the selected month/year
     */
    public function fetchPayrolls()
    {
        $this->isFetching = true;
        $this->fetchMessage = 'Fetching existing payroll records...';

        try {
            $this->loadStatistics();

            $count = $this->totalPayrolls;

            if ($count == 0) {
                $this->fetchMessage = 'No payroll records found for ' . $this->availableMonths[$this->selectedMonth] . ' ' . $this->selectedYear;
                session()->flash('warning', 'No payroll records found for ' . $this->availableMonths[$this->selectedMonth] . ' ' . $this->selectedYear);
            } else {
                $this->fetchMessage = "Found {$count} payroll records!";
                session()->flash('message', "Found {$count} payroll records for " .
                    $this->availableMonths[$this->selectedMonth] . ' ' . $this->selectedYear);
            }
        } catch (\Exception $e) {
            $this->fetchMessage = 'Error: ' . $e->getMessage();
            session()->flash('error', 'Error fetching payrolls: ' . $e->getMessage());
        } finally {
            $this->isFetching = false;
        }
    }

    /**
     * Calculate totals from existing payroll data
     */
    public function calculateTotals()
    {
        $this->loadStatistics();
        session()->flash('message', 'Totals recalculated successfully!');
    }

    public function loadStatistics()
    {
        if (!$this->selectedYear || !$this->selectedMonth) {
            $this->resetStats();
            return;
        }

        // Query payrolls directly through the payroll_batches relationship
        $payrollsQuery = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
            ->whereHas('batch', function ($query) {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            });

        // Apply worker search filter
        if ($this->searchWorker) {
            $payrollsQuery->whereHas('worker', function ($query) {
                $query->where('name', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('email', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('phone', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('designation', 'like', '%' . $this->searchWorker . '%');
            });
        }

        // Apply project search filter
        if ($this->searchProject) {
            $payrollsQuery->whereHas('projectBreakdowns.project', function ($query) {
                $query->where('name', 'like', '%' . $this->searchProject . '%')
                    ->orWhere('project_code', 'like', '%' . $this->searchProject . '%');
            });
        }

        // Apply gross amount range filters
        if ($this->minGrossAmount !== '' && is_numeric($this->minGrossAmount)) {
            $payrollsQuery->where('gross_amount', '>=', $this->minGrossAmount);
        }
        
        if ($this->maxGrossAmount !== '' && is_numeric($this->maxGrossAmount)) {
            $payrollsQuery->where('gross_amount', '<=', $this->maxGrossAmount);
        }

        $this->totalPayrolls = $payrollsQuery->count();
        $this->totalGrossAmount = $payrollsQuery->sum('gross_amount');
        $this->totalNetAmount = $payrollsQuery->sum('net_amount');
        $this->totalAdvancesDeducted = $payrollsQuery->sum('advance_deduction');
        $this->totalWorkers = $payrollsQuery->distinct('worker_id')->count('worker_id');

        $this->averagePayroll = $this->totalPayrolls > 0
            ? $this->totalNetAmount / $this->totalPayrolls
            : 0;
    }

    private function resetStats()
    {
        $this->totalPayrolls = 0;
        $this->totalGrossAmount = 0;
        $this->totalNetAmount = 0;
        $this->totalWorkers = 0;
        $this->totalAdvancesDeducted = 0;
        $this->averagePayroll = 0;
    }

    public function loadMonthlySummary()
    {
        if (!$this->selectedYear || !$this->selectedMonth) {
            $this->monthlySummary = null;
            return;
        }

        $this->monthlySummary = MonthlyPayrollSummary::where('year', $this->selectedYear)
            ->where('month', $this->selectedMonth)
            ->first();
    }

    public function saveMonthlySummary()
    {
        $this->validate([
            'saveSummaryNotes' => 'nullable|string|max:500',
        ]);

        $this->savingSummary = true;

        try {
            DB::beginTransaction();

            // Get all payrolls for the selected period with filters
            $payrollsQuery = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
                ->whereHas('batch', function ($query) {
                    $query->where('year', $this->selectedYear)
                        ->where('month', $this->selectedMonth);
                });

            if ($this->searchWorker) {
                $payrollsQuery->whereHas('worker', function ($query) {
                    $query->where('name', 'like', '%' . $this->searchWorker . '%');
                });
            }

            $payrolls = $payrollsQuery->get();

            if ($payrolls->isEmpty()) {
                throw new \Exception('No payroll data found for this period.');
            }

            // Calculate totals
            $totalGross = $payrolls->sum('gross_amount');
            $totalNet = $payrolls->sum('net_amount');
            $totalAdvances = $payrolls->sum('advance_deduction');
            $totalWorkers = $payrolls->unique('worker_id')->count();
            $totalPayrolls = $payrolls->count();

            // Create or update summary
            $summary = MonthlyPayrollSummary::updateOrCreate(
                [
                    'year' => $this->selectedYear,
                    'month' => $this->selectedMonth,
                ],
                [
                    'total_gross_amount' => $totalGross,
                    'total_net_amount' => $totalNet,
                    'total_advances_deducted' => $totalAdvances,
                    'total_workers' => $totalWorkers,
                    'total_payrolls' => $totalPayrolls,
                    'single_worker_payrolls' => $totalPayrolls,
                    'batch_payrolls' => 0,
                    'notes' => $this->saveSummaryNotes,
                    'saved_by' => auth()->id(),
                    'saved_at' => now(),
                ]
            );

            DB::commit();

            $this->monthlySummary = $summary;
            $this->showSaveModal = false;
            $this->saveSummaryNotes = '';

            session()->flash('message', 'Monthly summary saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving summary: ' . $e->getMessage());
        } finally {
            $this->savingSummary = false;
        }
    }

    // View Payroll Details
    public function viewPayrollDetails($payrollId)
    {
        $this->selectedPayroll = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
            ->find($payrollId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedPayroll = null;
    }

    public function exportToCSV()
    {
        if (!$this->selectedYear || !$this->selectedMonth) {
            session()->flash('error', 'Please select year and month first');
            return;
        }

        $payrollsQuery = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
            ->whereHas('batch', function ($query) {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            });

        if ($this->searchWorker) {
            $payrollsQuery->whereHas('worker', function ($query) {
                $query->where('name', 'like', '%' . $this->searchWorker . '%');
            });
        }

        if ($this->searchProject) {
            $payrollsQuery->whereHas('projectBreakdowns.project', function ($query) {
                $query->where('name', 'like', '%' . $this->searchProject . '%');
            });
        }

        $payrolls = $payrollsQuery->get();

        if ($payrolls->isEmpty()) {
            session()->flash('error', 'No payroll data to export');
            return;
        }

        $filename = "payroll_{$this->selectedYear}_{$this->selectedMonth}.csv";
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, [
            'Worker Name',
            'Worker Email',
            'Worker Phone',
            'Designation',
            'Rate Type',
            'Rate Snapshot',
            'Total Days',
            'Total Hours',
            'Gross Amount',
            'Advance Deduction',
            'Manual Adjustment',
            'Net Amount',
            'Overtime Multiplier Used',
            'Projects Worked',
            'Created At'
        ]);

        // Add data
        foreach ($payrolls as $payroll) {
            $projects = $payroll->projectBreakdowns->map(function ($breakdown) {
                return $breakdown->project->name . ' (' . number_format($breakdown->hours, 1) . ' hrs - €' . number_format($breakdown->amount, 2) . ')';
            })->implode('; ');

            fputcsv($handle, [
                $payroll->worker->name,
                $payroll->worker->email,
                $payroll->worker->phone ?? 'N/A',
                $payroll->worker->designation ?? 'N/A',
                $payroll->rate_type,
                $payroll->rate_snapshot,
                $payroll->total_days,
                $payroll->total_hours,
                $payroll->gross_amount,
                $payroll->advance_deduction,
                $payroll->manual_adjustment,
                $payroll->net_amount,
                $payroll->overtime_multiplier_used ?? 'N/A',
                $projects,
                $payroll->created_at->format('Y-m-d H:i:s')
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function render()
    {
        if (!$this->selectedYear || !$this->selectedMonth) {
            return view('livewire.admin.payroll-dashboard', [
                'payrolls' => collect(),
            ])->layout('layouts.admindashboard');
        }

        // Query payrolls directly through the payroll_batches relationship
        $payrollsQuery = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
            ->whereHas('batch', function ($query) {
                $query->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            });

        // Apply worker search filter
        if ($this->searchWorker) {
            $payrollsQuery->whereHas('worker', function ($query) {
                $query->where('name', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('email', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('phone', 'like', '%' . $this->searchWorker . '%')
                    ->orWhere('designation', 'like', '%' . $this->searchWorker . '%');
            });
        }

        // Apply project search filter
        if ($this->searchProject) {
            $payrollsQuery->whereHas('projectBreakdowns.project', function ($query) {
                $query->where('name', 'like', '%' . $this->searchProject . '%')
                    ->orWhere('project_code', 'like', '%' . $this->searchProject . '%');
            });
        }

        // Apply gross amount range filters
        if ($this->minGrossAmount !== '' && is_numeric($this->minGrossAmount)) {
            $payrollsQuery->where('gross_amount', '>=', $this->minGrossAmount);
        }
        
        if ($this->maxGrossAmount !== '' && is_numeric($this->maxGrossAmount)) {
            $payrollsQuery->where('gross_amount', '<=', $this->maxGrossAmount);
        }

        $payrolls = $payrollsQuery->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.payroll-dashboard', [
            'payrolls' => $payrolls,
            'hasPayrolls' => $payrolls->total() > 0,
        ])->layout('layouts.admindashboard');
    }
}