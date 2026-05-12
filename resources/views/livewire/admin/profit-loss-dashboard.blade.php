<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Red-Black Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-chart-line text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Profit & Loss Dashboard</h3>
                        <p class="text-white-50 small mb-0">Real P&L based on Sale Invoices vs Actual Expenses</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search and Filters Section with Button Trigger -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-light border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-search me-2" style="color: #ff0000;"></i>
                Search & Filter P&L Data
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                <i class="fas fa-undo-alt me-1"></i> Reset All Filters
            </button>
        </div>
    </div>
    <div class="card-body pt-3">
        <form wire:submit.prevent="performSearch">
            <div class="row g-3">
                <!-- View Type -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-pie me-1" style="color: #ff0000;"></i>
                        View Type
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-chart-simple"></i>
                        </span>
                        <select class="form-select" wire:model="tempViewType">
                            <option value="monthly">Monthly View</option>
                            <option value="quarterly">Quarterly View</option>
                            <option value="yearly">Yearly View</option>
                            <option value="custom">Custom Range</option>
                            <option value="project">Project Analysis</option>
                        </select>
                    </div>
                </div>

                <!-- Year Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar me-1" style="color: #ff0000;"></i>
                        Year
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedYear">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Period/Month Filter -->
                @if($tempViewType == 'monthly' || $tempViewType == 'quarterly')
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1" style="color: #ff0000;"></i>
                            Period
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-calendar-week"></i>
                            </span>
                            @if($tempViewType == 'monthly')
                                <select class="form-select" wire:model="tempSelectedMonth">
                                    @foreach($months as $num => $name)
                                        <option value="{{ $num }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="form-select" wire:model="tempSelectedMonth">
                                    <option value="1">Q1 (Jan-Mar)</option>
                                    <option value="4">Q2 (Apr-Jun)</option>
                                    <option value="7">Q3 (Jul-Sep)</option>
                                    <option value="10">Q4 (Oct-Dec)</option>
                                </select>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Custom Date Range -->
                @if($tempViewType == 'custom')
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1" style="color: #ff0000;"></i>
                            Start Date
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-calendar-day"></i>
                            </span>
                            <input type="date" class="form-control" wire:model="tempCustomStartDate">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1" style="color: #ff0000;"></i>
                            End Date
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <input type="date" class="form-control" wire:model="tempCustomEndDate">
                        </div>
                    </div>
                @endif

                <!-- Project Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-project-diagram me-1" style="color: #ff0000;"></i>
                        Filter by Project
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-building"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedProjectId">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @if($tempSelectedProjectId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearProjectFilter">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Expense Category Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-tags me-1" style="color: #ff0000;"></i>
                        Expense Category
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-tag"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedCategoryId">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @if($tempSelectedCategoryId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearCategoryFilter">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Invoice Status Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-pie me-1" style="color: #ff0000;"></i>
                        Invoice Status
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-flag-checkered"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedStatus">
                            <option value="all">All Status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                </div>

                <!-- Search Actions - Now on its own row aligned to the right -->
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none;">
                            <i class="fas fa-search me-2"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if($isSearching && ($selectedProjectId || $selectedCategoryId || ($selectedStatus && $selectedStatus != 'all')))
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">
                        <i class="fas fa-filter me-1"></i>Active filters:
                    </small>
                    @if($selectedProjectId)
                        <span class="badge bg-primary">
                            <i class="fas fa-project-diagram me-1"></i>
                            Project: {{ $projects->firstWhere('id', $selectedProjectId)?->name }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearProjectFilter"></button>
                        </span>
                    @endif
                    @if($selectedCategoryId)
                        <span class="badge bg-success">
                            <i class="fas fa-tag me-1"></i>
                            Category: {{ $categories->firstWhere('id', $selectedCategoryId)?->name }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearCategoryFilter"></button>
                        </span>
                    @endif
                    @if($selectedStatus && $selectedStatus != 'all')
                        <span class="badge bg-info">
                            <i class="fas fa-flag-checkered me-1"></i>
                            Status: {{ ucfirst($selectedStatus) }}
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Period Summary -->
        <div class="mt-3 pt-2">
            <div class="alert alert-info mb-0" style="background: #e7f3ff; border: none;">
                <i class="fas fa-calendar-alt me-2"></i>
                <strong>Period:</strong> {{ $periodLabel }}
                @if($selectedProjectId)
                    <span class="ms-3"><i class="fas fa-project-diagram me-1"></i> Project Filter Active</span>
                @endif
                @if($selectedCategoryId)
                    <span class="ms-3"><i class="fas fa-tag me-1"></i> Category Filter Active</span>
                @endif
            </div>
        </div>
    </div>
</div>
            <!-- Main KPI Cards Row -->
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-success">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Total Revenue</span>
                            <h3 class="dashboard-card-value">€ {{ number_format($totalRevenue, 2) }}</h3>
                            <span class="dashboard-card-subtitle">{{ $totalInvoicesCount }} Invoices</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-danger">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Total Expenses</span>
                            <h3 class="dashboard-card-value">€ {{ number_format($totalExpenses, 2) }}</h3>
                            <span class="dashboard-card-subtitle">{{ $totalExpensesCount }} Transactions</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-info">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">VAT Collected (IVA)</span>
                            <h3 class="dashboard-card-value">€ {{ number_format($totalVatCollected, 2) }}</h3>
                            <span class="dashboard-card-subtitle">21% on subtotal</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-primary">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-chart-simple"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Net Profit</span>
                            <h3 class="dashboard-card-value @if($netProfit < 0) text-danger @else text-white @endif">
                                € {{ number_format($netProfit, 2) }}
                            </h3>
                            <span class="dashboard-card-subtitle">Margin: {{ number_format($profitMargin, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown & Profit Analysis -->
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-chart-pie me-2" style="color: #ff0000;"></i>
                                Expense Breakdown
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="expense-card text-center">
                                        <div class="expense-card-icon" style="background: linear-gradient(135deg, #dc3545, #b02a37);">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h4 class="expense-amount text-danger mt-2 mb-1">€ {{ number_format($totalPayroll, 2) }}</h4>
                                        <small class="text-muted">Payroll</small>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $totalExpenses > 0 ? ($totalPayroll / $totalExpenses) * 100 : 0 }}%;"></div>
                                        </div>
                                        <small>{{ $totalExpenses > 0 ? number_format(($totalPayroll / $totalExpenses) * 100, 1) : 0 }}% of total</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="expense-card text-center">
                                        <div class="expense-card-icon" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <h4 class="expense-amount text-warning mt-2 mb-1">€ {{ number_format($totalOtherExpenses, 2) }}</h4>
                                        <small class="text-muted">Other Expenses</small>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: {{ $totalExpenses > 0 ? ($totalOtherExpenses / $totalExpenses) * 100 : 0 }}%;"></div>
                                        </div>
                                        <small>{{ $totalExpenses > 0 ? number_format(($totalOtherExpenses / $totalExpenses) * 100, 1) : 0 }}% of total</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-top">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Revenue Breakdown:</span>
                                    <span>Subtotal: € {{ number_format($totalRevenueSubtotal, 2) }} | VAT: € {{ number_format($totalVatCollected, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-chart-line me-2" style="color: #ff0000;"></i>
                                Profit Analysis
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row g-3 text-center">
                                <div class="col-6">
                                    <div class="profit-card">
                                        <div class="profit-card-icon" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                                            <i class="fas fa-chart-simple"></i>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Gross Profit</small>
                                        <h4 class="profit-amount text-info">€ {{ number_format($grossProfit, 2) }}</h4>
                                        <small>Revenue - Payroll</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="profit-card">
                                        <div class="profit-card-icon" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Net Profit</small>
                                        <h4 class="profit-amount @if($netProfit < 0) text-danger @else text-success @endif">
                                            € {{ number_format($netProfit, 2) }}
                                        </h4>
                                        <small>Revenue - All Expenses</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <div class="alert @if($profitMargin > 20) alert-success @elseif($profitMargin > 10) alert-info @elseif($profitMargin > 0) alert-warning @else alert-danger @endif mb-0">
                                    <i class="fas fa-chart-line me-2"></i>
                                    <strong>Profit Margin: {{ number_format($profitMargin, 1) }}%</strong>
                                    @if($profitMargin > 20)
                                        <br><small>Excellent! Your business is very profitable.</small>
                                    @elseif($profitMargin > 10)
                                        <br><small>Good profit margin. Room for improvement.</small>
                                    @elseif($profitMargin > 0)
                                        <br><small>Low profit margin. Consider reducing costs.</small>
                                    @else
                                        <br><small>Negative profit. Need to review expenses urgently!</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expenses by Category -->
            @if($expensesByCategory && $expensesByCategory->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-chart-pie me-2" style="color: #ff0000;"></i>
                            Expenses by Category
                        </h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-2">
                            @foreach($expensesByCategory as $expense)
                                @php
                                    $percentage = $totalOtherExpenses > 0 ? ($expense->total / $totalOtherExpenses) * 100 : 0;
                                @endphp
                                <div class="col-md-4 col-lg-3">
                                    <div class="category-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="{{ $expense->category->icon }} me-2" style="color: {{ $expense->category->color }}"></i>
                                                <span class="fw-semibold">{{ $expense->category->name }}</span>
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-bold">€ {{ number_format($expense->total, 2) }}</span>
                                                <br>
                                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-light mt-3 mb-0">
                            <strong>Total Other Expenses:</strong> € {{ number_format($totalOtherExpenses, 2) }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Yearly Chart Table (Only for Yearly View) -->
            @if($viewType == 'yearly' && !empty($chartData['months']))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-chart-bar me-2" style="color: #ff0000;"></i>
                            Monthly Performance - {{ $selectedYear }}
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Month</th>
                                        <th class="text-end">Revenue (€)</th>
                                        <th class="text-end">Payroll (€)</th>
                                        <th class="text-end">Other Expenses (€)</th>
                                        <th class="text-end">Total Expenses (€)</th>
                                        <th class="text-end">Profit (€)</th>
                                        <th class="text-end pe-4">Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyData as $month)
                                        <tr>
                                            <td class="ps-4"><strong>{{ $month['month_name'] }}</strong></td>
                                            <td class="text-end text-success">€ {{ number_format($month['revenue'], 2) }}</td>
                                            <td class="text-end text-danger">€ {{ number_format($month['payroll'], 2) }}</td>
                                            <td class="text-end text-warning">€ {{ number_format($month['other_expenses'], 2) }}</td>
                                            <td class="text-end">€ {{ number_format($month['total_expenses'], 2) }}</td>
                                            <td class="text-end fw-bold {{ $month['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                € {{ number_format($month['profit'], 2) }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="badge {{ $month['margin'] >= 20 ? 'bg-success' : ($month['margin'] >= 10 ? 'bg-info' : ($month['margin'] >= 0 ? 'bg-warning' : 'bg-danger')) }}">
                                                    {{ number_format($month['margin'], 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="fw-bold">
                                        <td class="ps-4">TOTAL</td>
                                        <td class="text-end">€ {{ number_format(collect($monthlyData)->sum('revenue'), 2) }}</td>
                                        <td class="text-end">€ {{ number_format(collect($monthlyData)->sum('payroll'), 2) }}</td>
                                        <td class="text-end">€ {{ number_format(collect($monthlyData)->sum('other_expenses'), 2) }}</td>
                                        <td class="text-end">€ {{ number_format(collect($monthlyData)->sum('total_expenses'), 2) }}</td>
                                        <td class="text-end">€ {{ number_format($netProfit, 2) }}</td>
                                        <td class="text-end pe-4">{{ number_format($profitMargin, 1) }}%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Project Profitability Table -->
            @if(count($projectProfitability) > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-chart-line me-2" style="color: #ff0000;"></i>
                            Project Profitability Analysis
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-sec">
                                    <tr>
    <th class="ps-4">
        <i class="fas fa-project-diagram me-2" ></i>
        Project
    </th>
    <th>
        <i class="fas fa-chart-line me-2" ></i>
        Status
    </th>
    <th class="text-end">
        <i class="fas fa-euro-sign me-2" ></i>
        Revenue (€)
    </th>
    <th class="text-end">
        <i class="fas fa-file-invoice me-2" ></i>
        Invoices
    </th>
    <th class="text-end">
        <i class="fas fa-users me-2" ></i>
        Labor Cost (€)
    </th>
    <th class="text-end">
        <i class="fas fa-receipt me-2" ></i>
        Other Exp (€)
    </th>
    <th class="text-end">
        <i class="fas fa-calculator me-2" ></i>
        Total Cost (€)
    </th>
    <th class="text-end">
        <i class="fas fa-chart-simple me-2" ></i>
        Profit (€)
    </th>
    <th class="text-end pe-4">
        <i class="fas fa-percent me-2" ></i>
        Margin
    </th>
</tr>
                                </thead>
                                <tbody>
                                    @foreach($projectProfitability as $project)
                                        <tr>
                                            <td class="ps-4">
                                                <strong>{{ $project['project']->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $project['project']->project_code ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if($project['status'] == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($project['status'] == 'ongoing')
                                                    <span class="badge bg-primary">Ongoing</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($project['status']) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-success">€ {{ number_format($project['revenue'], 2) }}</td>
                                            <td class="text-end">{{ $project['invoice_count'] }}</td>
                                            <td class="text-end text-black">€ {{ number_format($project['labor_cost'], 2) }}</td>
                                            <td class="text-end text-black">€ {{ number_format($project['other_expenses'], 2) }}</td>
                                            <td class="text-end">€ {{ number_format($project['total_cost'], 2) }}</td>
                                            <td class="text-end fw-bold {{ $project['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                € {{ number_format($project['profit'], 2) }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <div class="progress" style="width: 80px; height: 6px;">
                                                        <div class="progress-bar {{ $project['margin'] >= 0 ? 'bg-success' : 'bg-danger' }}"
                                                             style="width: {{ min(abs($project['margin']), 100) }}%;"></div>
                                                    </div>
                                                    <span>{{ number_format($project['margin'], 1) }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="fw-bold">
                                        <td colspan="2" class="ps-4">TOTAL</td>
                                        <td class="text-end">€ {{ number_format(collect($projectProfitability)->sum('revenue'), 2) }}</td>
                                        <td class="text-end">{{ collect($projectProfitability)->sum('invoice_count') }}</td>
                                        <td class="text-end">€ {{ number_format(collect($projectProfitability)->sum('labor_cost'), 2) }}</td>
                                        <td class="text-end">€ {{ number_format(collect($projectProfitability)->sum('other_expenses'), 2) }}</td>
                                        <td class="text-end">€ {{ number_format(collect($projectProfitability)->sum('total_cost'), 2) }}</td>
                                        <td class="text-end @if($netProfit < 0) text-danger @else text-success @endif">
                                            € {{ number_format($netProfit, 2) }}
                                        </td>
                                        <td class="text-end pe-4">{{ number_format($profitMargin, 1) }}%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5>No Data Available</h5>
                        <p class="text-muted">No transactions found for the selected filters and period.</p>
                        <button wire:click="resetFilters" class="btn btn-danger btn-sm">
                            <i class="fas fa-undo-alt me-2"></i> Reset Filters
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .dashboard-card-icon {
            width: 55px;
            height: 55px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .dashboard-card-success .dashboard-card-icon { background: linear-gradient(135deg, #28a745, #1e7e34); }
        .dashboard-card-danger .dashboard-card-icon { background: linear-gradient(135deg, #dc3545, #b02a37); }
        .dashboard-card-info .dashboard-card-icon { background: linear-gradient(135deg, #17a2b8, #138496); }
        .dashboard-card-primary .dashboard-card-icon { background: linear-gradient(135deg, #ff0000, #cc0000); }

        .dashboard-card-content {
            flex: 1;
        }

        .dashboard-card-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0.5px;
        }

        .dashboard-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0.25rem 0;
            color: #2c3e50;
        }

        .dashboard-card-subtitle {
            font-size: 0.7rem;
            color: #6c757d;
        }

        /* Expense Cards */
        .expense-card {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.2s ease;
        }

        .expense-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .expense-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto;
        }

        .expense-amount {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Profit Cards */
        .profit-card {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.2s ease;
        }

        .profit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .profit-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto;
        }

        .profit-amount {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Category Items */
        .category-item {
            background: #f8f9fa;
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .category-item:hover {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* Form Controls */
        .form-control, .form-select, .input-group-text {
            border-radius: 0.5rem;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1);
            outline: none;
        }

        .input-group:focus-within .form-control,
        .input-group:focus-within .input-group-text {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1);
        }

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-light {
            background: white;
            color: #000;
            border: 1px solid #e0e0e0;
        }

        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        .btn-outline-secondary:hover {
            background: #f8f9fa;
            border-color: #ff0000;
            color: #ff0000;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            border-radius: 0.5rem;
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
        }

        .bg-sec {
            background-color: #f8f9fa !important;
        }

        /* Table */
        .table {
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        /* Active Filters */
        .btn-close-white {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .btn-close-white:hover {
            opacity: 1;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 0.5rem;
            }

            .dashboard-card {
                flex-direction: column;
                text-align: center;
            }

            .dashboard-card-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .dashboard-card-value {
                font-size: 1.25rem;
            }

            .expense-card-icon, .profit-card-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .expense-amount, .profit-amount {
                font-size: 1.25rem;
            }
        }
    </style>
</div>