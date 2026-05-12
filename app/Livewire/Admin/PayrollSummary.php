<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Payroll;
use App\Models\MonthlyPayrollSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollSummary extends Component
{
    // Actual filter properties (applied after search button click)
    public $selectedYear = null;
    public $selectedStartMonth = null;
    public $selectedEndMonth = null;
    public $viewType = 'yearly'; // yearly, quarterly, monthly

    // Temporary properties for button search
    public $tempSelectedYear = null;
    public $tempSelectedStartMonth = null;
    public $tempSelectedEndMonth = null;
    public $tempViewType = 'yearly';

    // Statistics
    public $totalGrossAmount = 0;
    public $totalNetAmount = 0;
    public $totalAdvancesDeducted = 0;
    public $totalWorkers = 0;
    public $totalPayrolls = 0;
    public $averageMonthlyPayout = 0;

    public $availableYears = [];
    public $months = [];

    // Summary data
    public $monthlySummaries = [];
    public $yearlySummaries = [];

    // Selected summary for details
    public $selectedSummary = null;
    public $showDetailsModal = false;
    public $detailedPayrolls = [];
    
    // Search flag
    public $isSearching = false;

    public function mount()
    {
        $this->loadAvailableYears();
        $this->loadMonths();

        // Set defaults
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedStartMonth = 1;
        $this->tempSelectedEndMonth = Carbon::now()->month;
        $this->tempViewType = 'yearly';
        
        $this->selectedYear = Carbon::now()->year;
        $this->selectedStartMonth = 1;
        $this->selectedEndMonth = Carbon::now()->month;
        $this->viewType = 'yearly';

        $this->loadData();
    }

    public function loadAvailableYears()
    {
        $yearsFromSummaries = MonthlyPayrollSummary::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $yearsFromPayrolls = DB::table('payrolls')
            ->join('payroll_batches', 'payrolls.payroll_batch_id', '=', 'payroll_batches.id')
            ->select('payroll_batches.year')
            ->distinct()
            ->orderBy('payroll_batches.year', 'desc')
            ->pluck('year')
            ->toArray();

        $allYears = array_unique(array_merge($yearsFromSummaries, $yearsFromPayrolls));

        if (empty($allYears)) {
            $currentYear = Carbon::now()->year;
            $allYears = [$currentYear, $currentYear - 1];
        }

        $this->availableYears = $allYears;
    }

    public function loadMonths()
    {
        $this->months = [
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
        $this->selectedStartMonth = $this->tempSelectedStartMonth;
        $this->selectedEndMonth = $this->tempSelectedEndMonth;
        $this->viewType = $this->tempViewType;
        $this->isSearching = true;
        
        $this->loadData();
    }
    
    // Reset all filters
    public function resetFilters()
    {
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedStartMonth = 1;
        $this->tempSelectedEndMonth = Carbon::now()->month;
        $this->tempViewType = 'yearly';
        
        $this->selectedYear = Carbon::now()->year;
        $this->selectedStartMonth = 1;
        $this->selectedEndMonth = Carbon::now()->month;
        $this->viewType = 'yearly';
        $this->isSearching = false;
        
        $this->loadData();
    }

    public function refreshData()
    {
        $this->loadData();
        session()->flash('message', 'Data refreshed successfully!');
    }

    public function loadData()
    {
        if ($this->viewType === 'yearly') {
            $this->loadYearlyData();
        } elseif ($this->viewType === 'quarterly') {
            $this->loadQuarterlyData();
        } else {
            $this->loadMonthlyData();
        }

        $this->loadTotals();
    }

    public function loadYearlyData()
    {
        $this->yearlySummaries = MonthlyPayrollSummary::where('year', $this->selectedYear)
            ->orderBy('month')
            ->get();

        if ($this->yearlySummaries->isEmpty()) {
            $this->calculateYearlyFromPayrolls();
        }
    }

    public function calculateYearlyFromPayrolls()
    {
        $yearlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $payrolls = Payroll::with(['worker', 'batch'])
                ->whereHas('batch', function ($query) use ($month) {
                    $query->where('year', $this->selectedYear)
                        ->where('month', $month);
                })
                ->get();

            if ($payrolls->isNotEmpty()) {
                $yearlyData[] = (object)[
                    'year' => $this->selectedYear,
                    'month' => $month,
                    'total_gross_amount' => $payrolls->sum('gross_amount'),
                    'total_net_amount' => $payrolls->sum('net_amount'),
                    'total_advances_deducted' => $payrolls->sum('advance_deduction'),
                    'total_workers' => $payrolls->unique('worker_id')->count(),
                    'total_payrolls' => $payrolls->count(),
                ];
            }
        }

        $this->yearlySummaries = collect($yearlyData);
    }

    public function loadQuarterlyData()
    {
        $quarters = [
            1 => ['months' => [1, 2, 3], 'name' => 'Q1 (Jan-Mar)'],
            2 => ['months' => [4, 5, 6], 'name' => 'Q2 (Apr-Jun)'],
            3 => ['months' => [7, 8, 9], 'name' => 'Q3 (Jul-Sep)'],
            4 => ['months' => [10, 11, 12], 'name' => 'Q4 (Oct-Dec)'],
        ];

        $quarterlyData = [];

        foreach ($quarters as $quarterNum => $quarter) {
            $summaries = MonthlyPayrollSummary::where('year', $this->selectedYear)
                ->whereIn('month', $quarter['months'])
                ->get();

            if ($summaries->isEmpty()) {
                $totalGross = 0;
                $totalNet = 0;
                $totalAdvances = 0;
                $totalWorkers = 0;
                $totalPayrolls = 0;

                foreach ($quarter['months'] as $month) {
                    $payrolls = Payroll::with(['worker', 'batch'])
                        ->whereHas('batch', function ($query) use ($month) {
                            $query->where('year', $this->selectedYear)
                                ->where('month', $month);
                        })
                        ->get();

                    if ($payrolls->isNotEmpty()) {
                        $totalGross += $payrolls->sum('gross_amount');
                        $totalNet += $payrolls->sum('net_amount');
                        $totalAdvances += $payrolls->sum('advance_deduction');
                        $totalWorkers += $payrolls->unique('worker_id')->count();
                        $totalPayrolls += $payrolls->count();
                    }
                }

                if ($totalGross > 0) {
                    $quarterlyData[] = (object)[
                        'quarter' => $quarterNum,
                        'quarter_name' => $quarter['name'],
                        'total_gross_amount' => $totalGross,
                        'total_net_amount' => $totalNet,
                        'total_advances_deducted' => $totalAdvances,
                        'total_workers' => $totalWorkers,
                        'total_payrolls' => $totalPayrolls,
                    ];
                }
            } else {
                $quarterlyData[] = (object)[
                    'quarter' => $quarterNum,
                    'quarter_name' => $quarter['name'],
                    'total_gross_amount' => $summaries->sum('total_gross_amount'),
                    'total_net_amount' => $summaries->sum('total_net_amount'),
                    'total_advances_deducted' => $summaries->sum('total_advances_deducted'),
                    'total_workers' => $summaries->sum('total_workers'),
                    'total_payrolls' => $summaries->sum('total_payrolls'),
                ];
            }
        }

        $this->yearlySummaries = collect($quarterlyData);
    }

    public function loadMonthlyData()
    {
        $this->monthlySummaries = MonthlyPayrollSummary::where('year', $this->selectedYear)
            ->whereBetween('month', [$this->selectedStartMonth, $this->selectedEndMonth])
            ->orderBy('month')
            ->get();

        if ($this->monthlySummaries->isEmpty()) {
            $monthlyData = [];

            for ($month = $this->selectedStartMonth; $month <= $this->selectedEndMonth; $month++) {
                $payrolls = Payroll::with(['worker', 'batch'])
                    ->whereHas('batch', function ($query) use ($month) {
                        $query->where('year', $this->selectedYear)
                            ->where('month', $month);
                    })
                    ->get();

                if ($payrolls->isNotEmpty()) {
                    $monthlyData[] = (object)[
                        'year' => $this->selectedYear,
                        'month' => $month,
                        'total_gross_amount' => $payrolls->sum('gross_amount'),
                        'total_net_amount' => $payrolls->sum('net_amount'),
                        'total_advances_deducted' => $payrolls->sum('advance_deduction'),
                        'total_workers' => $payrolls->unique('worker_id')->count(),
                        'total_payrolls' => $payrolls->count(),
                    ];
                }
            }

            $this->monthlySummaries = collect($monthlyData);
        }
    }

    public function loadTotals()
    {
        if ($this->viewType === 'yearly') {
            $this->totalGrossAmount = $this->yearlySummaries->sum('total_gross_amount');
            $this->totalNetAmount = $this->yearlySummaries->sum('total_net_amount');
            $this->totalAdvancesDeducted = $this->yearlySummaries->sum('total_advances_deducted');
            $this->totalWorkers = $this->yearlySummaries->sum('total_workers');
            $this->totalPayrolls = $this->yearlySummaries->sum('total_payrolls');
            $monthsWithData = $this->yearlySummaries->count();
            $this->averageMonthlyPayout = $monthsWithData > 0 ? $this->totalNetAmount / $monthsWithData : 0;
        } elseif ($this->viewType === 'quarterly') {
            $this->totalGrossAmount = $this->yearlySummaries->sum('total_gross_amount');
            $this->totalNetAmount = $this->yearlySummaries->sum('total_net_amount');
            $this->totalAdvancesDeducted = $this->yearlySummaries->sum('total_advances_deducted');
            $this->totalWorkers = $this->yearlySummaries->sum('total_workers');
            $this->totalPayrolls = $this->yearlySummaries->sum('total_payrolls');
            $quartersWithData = $this->yearlySummaries->count();
            $this->averageMonthlyPayout = $quartersWithData > 0 ? $this->totalNetAmount / $quartersWithData : 0;
        } else {
            $this->totalGrossAmount = $this->monthlySummaries->sum('total_gross_amount');
            $this->totalNetAmount = $this->monthlySummaries->sum('total_net_amount');
            $this->totalAdvancesDeducted = $this->monthlySummaries->sum('total_advances_deducted');
            $this->totalWorkers = $this->monthlySummaries->sum('total_workers');
            $this->totalPayrolls = $this->monthlySummaries->sum('total_payrolls');
            $monthsWithData = $this->monthlySummaries->count();
            $this->averageMonthlyPayout = $monthsWithData > 0 ? $this->totalNetAmount / $monthsWithData : 0;
        }
    }

    public function viewDetails($summaryData)
    {
        $this->selectedSummary = $summaryData;
        
        if ($this->viewType === 'quarterly') {
            $quarterMonths = [];
            if (strpos($summaryData['quarter_name'], 'Q1') !== false) $quarterMonths = [1,2,3];
            elseif (strpos($summaryData['quarter_name'], 'Q2') !== false) $quarterMonths = [4,5,6];
            elseif (strpos($summaryData['quarter_name'], 'Q3') !== false) $quarterMonths = [7,8,9];
            elseif (strpos($summaryData['quarter_name'], 'Q4') !== false) $quarterMonths = [10,11,12];
            
            $this->detailedPayrolls = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
                ->whereHas('batch', function ($query) use ($quarterMonths) {
                    $query->where('year', $this->selectedYear)
                        ->whereIn('month', $quarterMonths);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $month = $summaryData['month'] ?? null;
            $year = $summaryData['year'] ?? $this->selectedYear;
            
            $this->detailedPayrolls = Payroll::with(['worker', 'batch', 'projectBreakdowns.project'])
                ->whereHas('batch', function ($query) use ($month, $year) {
                    $query->where('year', $year)
                        ->where('month', $month);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedSummary = null;
        $this->detailedPayrolls = [];
    }

    public function render()
    {
        $summaries = $this->viewType == 'monthly' ? $this->monthlySummaries : $this->yearlySummaries;
        
        return view('livewire.admin.payroll-summary', [
            'summaries' => $summaries,
        ])->layout('layouts.admindashboard');
    }
}