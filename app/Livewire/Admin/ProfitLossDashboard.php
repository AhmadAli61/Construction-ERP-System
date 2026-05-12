<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Project;
use App\Models\Payroll;
use App\Models\ProjectExpense;
use App\Models\SaleInvoice;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitLossDashboard extends Component
{
    // Actual filter properties (applied after search button click)
    public $selectedYear = null;
    public $selectedMonth = null;
    public $selectedProjectId = null;
    public $viewType = 'monthly';
    public $customStartDate = null;
    public $customEndDate = null;
    public $selectedCategoryId = null;
    public $selectedStatus = null;

    // Temporary properties for button search
    public $tempSelectedYear = null;
    public $tempSelectedMonth = null;
    public $tempSelectedProjectId = null;
    public $tempViewType = 'monthly';
    public $tempCustomStartDate = null;
    public $tempCustomEndDate = null;
    public $tempSelectedCategoryId = null;
    public $tempSelectedStatus = null;

    public $availableYears = [];
    public $months = [];
    public $projects = [];
    public $categories = [];
    public $statuses = ['paid', 'unpaid', 'partial', 'all'];

    // Statistics
    public $totalRevenue = 0;
    public $totalRevenueSubtotal = 0;
    public $totalVatCollected = 0;
    public $totalPayroll = 0;
    public $totalOtherExpenses = 0;
    public $totalExpenses = 0;
    public $grossProfit = 0;
    public $netProfit = 0;
    public $profitMargin = 0;
    public $totalInvoicesCount = 0;
    public $totalExpensesCount = 0;

    // Breakdown
    public $monthlyData = [];
    public $projectProfitability = [];
    public $expensesByCategory = [];
    public $revenueByProject = [];
    public $chartData = [];

    // Date range for current view
    public $currentStartDate = null;
    public $currentEndDate = null;
    public $periodLabel = '';

    // Search flag
    public $isSearching = false;

    public function mount()
    {
        $this->loadAvailableYears();
        $this->loadMonths();
        $this->loadProjects();
        $this->loadCategories();

        // Set default temp values
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->tempViewType = 'monthly';
        $this->tempSelectedStatus = 'all';

        // Set actual values
        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        $this->viewType = 'monthly';
        $this->selectedStatus = 'all';

        $this->loadData();
    }

    public function loadAvailableYears()
    {
        $yearsFromInvoices = SaleInvoice::selectRaw('YEAR(invoice_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $yearsFromExpenses = ProjectExpense::selectRaw('YEAR(expense_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $yearsFromPayrolls = DB::table('payroll_batches')
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $allYears = array_unique(array_merge($yearsFromInvoices, $yearsFromExpenses, $yearsFromPayrolls));

        if (empty($allYears)) {
            $allYears = [Carbon::now()->year, Carbon::now()->year - 1];
        }

        sort($allYears);
        $this->availableYears = array_reverse($allYears);
    }

    public function loadProjects()
    {
        $this->projects = Project::orderBy('name')->get();
    }

    public function loadCategories()
    {
        $this->categories = ExpenseCategory::active()->orderBy('name')->get();
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
        $this->selectedMonth = $this->tempSelectedMonth;
        $this->selectedProjectId = $this->tempSelectedProjectId;
        $this->viewType = $this->tempViewType;
        $this->customStartDate = $this->tempCustomStartDate;
        $this->customEndDate = $this->tempCustomEndDate;
        $this->selectedCategoryId = $this->tempSelectedCategoryId;
        $this->selectedStatus = $this->tempSelectedStatus;
        $this->isSearching = true;

        $this->loadData();
    }

    // Reset all filters
    public function resetFilters()
    {
        $this->tempSelectedYear = Carbon::now()->year;
        $this->tempSelectedMonth = Carbon::now()->month;
        $this->tempSelectedProjectId = null;
        $this->tempViewType = 'monthly';
        $this->tempCustomStartDate = null;
        $this->tempCustomEndDate = null;
        $this->tempSelectedCategoryId = null;
        $this->tempSelectedStatus = 'all';

        $this->selectedYear = Carbon::now()->year;
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedProjectId = null;
        $this->viewType = 'monthly';
        $this->customStartDate = null;
        $this->customEndDate = null;
        $this->selectedCategoryId = null;
        $this->selectedStatus = 'all';
        $this->isSearching = false;

        $this->loadData();
    }

    // Clear individual filters
    public function clearProjectFilter()
    {
        $this->tempSelectedProjectId = null;
        $this->selectedProjectId = null;
        $this->loadData();
    }

    public function clearCategoryFilter()
    {
        $this->tempSelectedCategoryId = null;
        $this->selectedCategoryId = null;
        $this->loadData();
    }

    public function updatedViewType()
    {
        if ($this->tempViewType === 'custom') {
            $this->tempCustomStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->tempCustomEndDate = Carbon::now()->format('Y-m-d');
        }
    }

    public function loadData()
    {
        // Set date range based on view type
        $this->setDateRange();

        // Get Revenue from Sale Invoices
        $this->loadRevenueData();

        // Get Payroll Expenses
        $this->loadPayrollData();

        // Get Other Expenses
        $this->loadOtherExpenses();

        // Calculate totals
        $this->calculateTotals();

        // Load breakdowns
        $this->loadExpensesByCategory();
        $this->loadRevenueByProject();
        $this->loadProjectProfitability();

        if ($this->viewType === 'yearly') {
            $this->loadMonthlyBreakdown();
        }
    }

    public function setDateRange()
    {
        switch ($this->viewType) {
            case 'monthly':
                $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
                $endDate = $startDate->copy()->endOfMonth();
                $this->currentStartDate = $startDate;
                $this->currentEndDate = $endDate;
                $this->periodLabel = $this->months[$this->selectedMonth] . ' ' . $this->selectedYear;
                break;

            case 'quarterly':
                $quarter = ceil($this->selectedMonth / 3);
                $startDate = Carbon::create($this->selectedYear, ($quarter - 1) * 3 + 1, 1);
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                $this->currentStartDate = $startDate;
                $this->currentEndDate = $endDate;
                $this->periodLabel = 'Q' . $quarter . ' ' . $this->selectedYear;
                break;

            case 'yearly':
                $startDate = Carbon::create($this->selectedYear, 1, 1);
                $endDate = Carbon::create($this->selectedYear, 12, 31)->endOfDay();
                $this->currentStartDate = $startDate;
                $this->currentEndDate = $endDate;
                $this->periodLabel = 'Year ' . $this->selectedYear;
                break;

            case 'custom':
                if ($this->customStartDate && $this->customEndDate) {
                    $this->currentStartDate = Carbon::parse($this->customStartDate);
                    $this->currentEndDate = Carbon::parse($this->customEndDate)->endOfDay();
                    $this->periodLabel = $this->currentStartDate->format('d M Y') . ' - ' . $this->currentEndDate->format('d M Y');
                }
                break;

            case 'project':
                $this->currentStartDate = null;
                $this->currentEndDate = null;
                $this->periodLabel = 'All Time - Project Analysis';
                break;
        }
    }

    public function loadRevenueData()
    {
        $query = SaleInvoice::query();

        if ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
            $query->whereBetween('invoice_date', [$this->currentStartDate, $this->currentEndDate]);
        }

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        if ($this->selectedStatus !== 'all') {
            $query->where('payment_status', $this->selectedStatus);
        }

        $invoices = $query->get();

        $this->totalRevenue = $invoices->sum('total');
        $this->totalRevenueSubtotal = $invoices->sum('subtotal');
        $this->totalVatCollected = $invoices->sum('vat_amount');
        $this->totalInvoicesCount = $invoices->count();
    }

    public function loadPayrollData()
    {
        if ($this->viewType === 'project') {
            $this->totalPayroll = 0;
            return;
        }

        $query = Payroll::query();

        // Apply date filters based on view type
        if ($this->viewType === 'monthly') {
            $query->whereHas('batch', function ($q) {
                $q->where('year', $this->selectedYear)
                    ->where('month', $this->selectedMonth);
            });
        } elseif ($this->viewType === 'quarterly' && $this->currentStartDate && $this->currentEndDate) {
            $startMonth = $this->currentStartDate->month;
            $endMonth = $this->currentEndDate->month;
            $query->whereHas('batch', function ($q) use ($startMonth, $endMonth) {
                $q->where('year', $this->selectedYear)
                    ->whereBetween('month', [$startMonth, $endMonth]);
            });
        } elseif ($this->viewType === 'yearly') {
            $query->whereHas('batch', function ($q) {
                $q->where('year', $this->selectedYear);
            });
        } elseif ($this->viewType === 'custom' && $this->currentStartDate && $this->currentEndDate) {
            $startMonth = $this->currentStartDate->month;
            $endMonth = $this->currentEndDate->month;
            $startYear = $this->currentStartDate->year;
            $endYear = $this->currentEndDate->year;

            if ($startYear === $endYear) {
                $query->whereHas('batch', function ($q) use ($startYear, $startMonth, $endMonth) {
                    $q->where('year', $startYear)
                        ->whereBetween('month', [$startMonth, $endMonth]);
                });
            } else {
                $this->totalPayroll = 0;
                return;
            }
        }

        // **CRITICAL FIX: Apply project filter to payroll**
        if ($this->selectedProjectId) {
            $query->whereHas('projectBreakdowns', function ($q) {
                $q->where('project_id', $this->selectedProjectId);
            });
        }

        $this->totalPayroll = $query->sum('net_amount');
    }

    public function loadOtherExpenses()
    {
        $query = ProjectExpense::query();

        if ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
            $query->whereBetween('expense_date', [$this->currentStartDate, $this->currentEndDate]);
        }

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        $this->totalOtherExpenses = $query->sum('amount');
        $this->totalExpensesCount = $query->count();
    }

    public function loadExpensesByCategory()
    {
        $query = ProjectExpense::select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id');

        if ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
            $query->whereBetween('expense_date', [$this->currentStartDate, $this->currentEndDate]);
        }

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        $this->expensesByCategory = $query->with('category')->get();
    }

    public function loadRevenueByProject()
    {
        $query = SaleInvoice::select(
            'project_id',
            DB::raw('SUM(total) as total_revenue'),
            DB::raw('SUM(subtotal) as total_subtotal'),
            DB::raw('SUM(vat_amount) as total_vat'),
            DB::raw('COUNT(*) as invoice_count')
        )
            ->groupBy('project_id');

        if ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
            $query->whereBetween('invoice_date', [$this->currentStartDate, $this->currentEndDate]);
        }

        if ($this->selectedProjectId) {
            $query->where('project_id', $this->selectedProjectId);
        }

        if ($this->selectedStatus !== 'all') {
            $query->where('payment_status', $this->selectedStatus);
        }

        $revenueData = $query->with('project')->get();

        $this->revenueByProject = [];
        foreach ($revenueData as $data) {
            $this->revenueByProject[] = [
                'project_id' => $data->project_id,
                'project_name' => $data->project->name ?? 'Unknown',
                'project_code' => $data->project->project_code ?? '',
                'total_revenue' => $data->total_revenue,
                'total_subtotal' => $data->total_subtotal,
                'total_vat' => $data->total_vat,
                'invoice_count' => $data->invoice_count,
            ];
        }
    }

    public function loadProjectProfitability()
    {
        $projects = $this->selectedProjectId
            ? Project::where('id', $this->selectedProjectId)->get()
            : Project::all();

        $this->projectProfitability = [];

        foreach ($projects as $project) {
            // Get revenue from sale invoices
            $invoiceQuery = SaleInvoice::where('project_id', $project->id);
            if ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
                $invoiceQuery->whereBetween('invoice_date', [$this->currentStartDate, $this->currentEndDate]);
            }
            if ($this->selectedStatus !== 'all') {
                $invoiceQuery->where('payment_status', $this->selectedStatus);
            }
            $revenue = $invoiceQuery->sum('total');
            $invoiceCount = $invoiceQuery->count();

            // Get labor cost from payroll with proper filtering
            $laborCost = 0;
            $payrollQuery = DB::table('payroll_project_breakdowns')
                ->join('payrolls', 'payroll_project_breakdowns.payroll_id', '=', 'payrolls.id')
                ->join('payroll_batches', 'payrolls.payroll_batch_id', '=', 'payroll_batches.id')
                ->where('payroll_project_breakdowns.project_id', $project->id);

            // Apply date filters to payroll
            if ($this->viewType === 'monthly') {
                $payrollQuery->where('payroll_batches.year', $this->selectedYear)
                    ->where('payroll_batches.month', $this->selectedMonth);
            } elseif ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
                $payrollQuery->whereBetween('payroll_batches.created_at', [$this->currentStartDate, $this->currentEndDate]);
            }

            $laborCost = $payrollQuery->sum('payroll_project_breakdowns.amount');

            // Get other expenses
            $expenseQuery = ProjectExpense::where('project_id', $project->id);
            if ($this->viewType !== 'project' && $this->currentStartDate && $this->currentEndDate) {
                $expenseQuery->whereBetween('expense_date', [$this->currentStartDate, $this->currentEndDate]);
            }
            if ($this->selectedCategoryId) {
                $expenseQuery->where('category_id', $this->selectedCategoryId);
            }
            $otherExpenses = $expenseQuery->sum('amount');

            $totalCost = $laborCost + $otherExpenses;
            $profit = $revenue - $totalCost;
            $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

            // Only show projects with activity
            if ($revenue > 0 || $otherExpenses > 0 || $laborCost > 0) {
                $this->projectProfitability[] = [
                    'project' => $project,
                    'revenue' => $revenue,
                    'invoice_count' => $invoiceCount,
                    'labor_cost' => $laborCost,
                    'other_expenses' => $otherExpenses,
                    'total_cost' => $totalCost,
                    'profit' => $profit,
                    'margin' => $margin,
                    'status' => $project->status,
                ];
            }
        }

        // Sort by profit descending
        usort($this->projectProfitability, function ($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
    }

    public function loadMonthlyBreakdown()
    {
        $this->monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($this->selectedYear, $month, 1);
            $monthEnd = $monthStart->copy()->endOfMonth();

            // Monthly revenue from invoices
            $revenueQuery = SaleInvoice::whereBetween('invoice_date', [$monthStart, $monthEnd]);
            if ($this->selectedProjectId) {
                $revenueQuery->where('project_id', $this->selectedProjectId);
            }
            if ($this->selectedStatus !== 'all') {
                $revenueQuery->where('payment_status', $this->selectedStatus);
            }
            $monthlyRevenue = $revenueQuery->sum('total');

            // Monthly payroll with project filter
            $payrollQuery = Payroll::whereHas('batch', function ($q) use ($month) {
                $q->where('year', $this->selectedYear)
                    ->where('month', $month);
            });

            if ($this->selectedProjectId) {
                $payrollQuery->whereHas('projectBreakdowns', function ($q) {
                    $q->where('project_id', $this->selectedProjectId);
                });
            }

            $monthlyPayroll = $payrollQuery->sum('net_amount');

            // Monthly other expenses
            $expenseQuery = ProjectExpense::whereBetween('expense_date', [$monthStart, $monthEnd]);
            if ($this->selectedProjectId) {
                $expenseQuery->where('project_id', $this->selectedProjectId);
            }
            if ($this->selectedCategoryId) {
                $expenseQuery->where('category_id', $this->selectedCategoryId);
            }
            $monthlyExpenses = $expenseQuery->sum('amount');

            $monthlyTotalExpenses = $monthlyPayroll + $monthlyExpenses;
            $monthlyProfit = $monthlyRevenue - $monthlyTotalExpenses;

            $this->monthlyData[$month] = [
                'month' => $month,
                'month_name' => $this->months[$month],
                'revenue' => $monthlyRevenue,
                'payroll' => $monthlyPayroll,
                'other_expenses' => $monthlyExpenses,
                'total_expenses' => $monthlyTotalExpenses,
                'profit' => $monthlyProfit,
                'margin' => $monthlyRevenue > 0 ? ($monthlyProfit / $monthlyRevenue) * 100 : 0,
                'invoice_count' => $revenueQuery->count(),
            ];
        }

        // Prepare chart data
        $this->chartData = [
            'months' => array_values(array_column($this->monthlyData, 'month_name')),
            'revenue' => array_values(array_column($this->monthlyData, 'revenue')),
            'expenses' => array_values(array_column($this->monthlyData, 'total_expenses')),
            'profit' => array_values(array_column($this->monthlyData, 'profit')),
        ];
    }

    public function calculateTotals()
    {
        $this->totalExpenses = $this->totalPayroll + $this->totalOtherExpenses;
        $this->grossProfit = $this->totalRevenue - $this->totalPayroll;
        $this->netProfit = $this->totalRevenue - $this->totalExpenses;
        $this->profitMargin = $this->totalRevenue > 0 ? ($this->netProfit / $this->totalRevenue) * 100 : 0;
    }

    public function render()
    {
        return view('livewire.admin.profit-loss-dashboard')->layout('layouts.admindashboard');
    }
}
