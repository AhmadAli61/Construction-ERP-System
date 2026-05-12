{{-- resources/views/livewire/admin/payroll-dashboard.blade.php --}}
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
                        <h3 class="mb-0 fw-bold text-white">Admin Payroll Dashboard</h3>
                        <p class="text-white-50 small mb-0">View, generate, and manage monthly payroll summaries</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="calculateTotals" class="btn btn-light">
                        <i class="fas fa-sync-alt me-2"></i>
                        Refresh Totals
                    </button>
                    <button wire:click="exportToCSV" class="btn btn-light">
                        <i class="fas fa-file-excel me-2"></i>
                        Export to CSV
                    </button>
                    @if($hasPayrolls && !$monthlySummary)
                        <button wire:click="$set('showSaveModal', true)" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Save Monthly Summary
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            <!-- Alerts -->
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session()->has('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

           <!-- Search and Filters Section with Button Trigger -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-search me-2" style="color: #ff0000;"></i>
                Search & Filter Payroll Data
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                <i class="fas fa-undo-alt me-1"></i> Reset All Filters
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="performSearch">
            <div class="row g-3 pt-3">
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

                <!-- Month Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar-alt me-1" style="color: #ff0000;"></i>
                        Month
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar-month"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedMonth">
                            @foreach($availableMonths as $monthNum => $monthName)
                                <option value="{{ $monthNum }}">{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Worker Search -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-user me-1" style="color: #ff0000;"></i>
                        Worker Name/Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" wire:model="tempSearchWorker" 
                               placeholder="Search by name, email, phone...">
                        @if($tempSearchWorker)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearSearchWorker">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Project Search -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-project-diagram me-1" style="color: #ff0000;"></i>
                        Project Name
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-building"></i>
                        </span>
                        <input type="text" class="form-control" wire:model="tempSearchProject" 
                               placeholder="Search by project name or code...">
                        @if($tempSearchProject)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearSearchProject">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Min Gross Amount -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-line me-1" style="color: #ff0000;"></i>
                        Min Gross (€)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">€</span>
                        <input type="number" step="100" class="form-control" wire:model="tempMinGrossAmount" 
                               placeholder="Min amount">
                        @if($tempMinGrossAmount)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearMinGrossAmount">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Max Gross Amount -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-line me-1" style="color: #ff0000;"></i>
                        Max Gross (€)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">€</span>
                        <input type="number" step="100" class="form-control" wire:model="tempMaxGrossAmount" 
                               placeholder="Max amount">
                        @if($tempMaxGrossAmount)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearMaxGrossAmount">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Items Per Page -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-eye me-1" style="color: #ff0000;"></i>
                        Per Page
                    </label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>

                <!-- Search Actions - Moved to new row and aligned to right -->
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none; min-width: 150px;">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if($searchWorker || $searchProject || $minGrossAmount || $maxGrossAmount)
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">
                        <i class="fas fa-filter me-1"></i>Active filters:
                    </small>
                    @if($searchWorker)
                        <span class="badge bg-primary">
                            <i class="fas fa-user me-1"></i>
                            Worker: {{ $searchWorker }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearSearchWorker"></button>
                        </span>
                    @endif
                    @if($searchProject)
                        <span class="badge bg-success">
                            <i class="fas fa-project-diagram me-1"></i>
                            Project: {{ $searchProject }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearSearchProject"></button>
                        </span>
                    @endif
                    @if($minGrossAmount)
                        <span class="badge bg-info">
                            <i class="fas fa-chart-line me-1"></i>
                            Min Gross: €{{ number_format($minGrossAmount, 2) }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearMinGrossAmount"></button>
                        </span>
                    @endif
                    @if($maxGrossAmount)
                        <span class="badge bg-warning">
                            <i class="fas fa-chart-line me-1"></i>
                            Max Gross: €{{ number_format($maxGrossAmount, 2) }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearMaxGrossAmount"></button>
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Fetch Status Message -->
        @if($isFetching || $fetchMessage)
            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                {{ $fetchMessage ?: 'Processing...' }}
            </div>
        @endif
    </div>
</div>

            <!-- Statistics Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body py-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50">Total Payrolls</small>
                                    <h4 class="mb-0 fw-bold text-white">{{ number_format($totalPayrolls) }}</h4>
                                    <small class="text-white-50">Single worker payrolls</small>
                                </div>
                                <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="card-body py-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50">Total Workers</small>
                                    <h4 class="mb-0 fw-bold text-white">{{ number_format($totalWorkers) }}</h4>
                                    <small class="text-white-50">Unique workers</small>
                                </div>
                                <i class="fas fa-users fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="card-body py-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50">Total Gross</small>
                                    <h4 class="mb-0 fw-bold text-white">€ {{ number_format($totalGrossAmount, 2) }}</h4>
                                    <small class="text-white-50">Before deductions</small>
                                </div>
                                <i class="fas fa-chart-line fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="card-body py-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-white-50">Total Net</small>
                                    <h4 class="mb-0 fw-bold text-white">€ {{ number_format($totalNetAmount, 2) }}</h4>
                                    <small class="text-white-50">After advances</small>
                                </div>
                                <i class="fas fa-euro-sign fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row -->
            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Average Payroll</small>
                                    <h4 class="mb-0 fw-bold">€ {{ number_format($averagePayroll, 2) }}</h4>
                                    <small>Per worker</small>
                                </div>
                                <i class="fas fa-chart-simple fa-2x text-muted opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Total Advances Deducted</small>
                                    <h4 class="mb-0 fw-bold">€ {{ number_format($totalAdvancesDeducted, 2) }}</h4>
                                    <small>Deducted from payrolls</small>
                                </div>
                                <i class="fas fa-hand-holding-usd fa-2x text-muted opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">Summary Status</small>
                                    <h4 class="mb-0 mt-3">
                                        @if($monthlySummary)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> Saved
                                            </span>
                                            <br>
                                            <small class="text-muted">By: {{ $monthlySummary->savedBy->name ?? 'System' }}</small>
                                        @else
                                            <span class="badge bg-warning px-3 py-2">
                                                <i class="fas fa-clock me-1"></i> Not Saved
                                            </span>
                                        @endif
                                    </h4>
                                    @if($monthlySummary && $monthlySummary->saved_at)
                                        <small>Saved: {{ $monthlySummary->saved_at->format('M d, Y H:i') }}</small>
                                    @endif
                                </div>
                                <i class="fas fa-save fa-2x text-muted opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payrolls Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                        Payroll Records for {{ $availableMonths[$selectedMonth] ?? '' }} {{ $selectedYear }}
                        @if(!$hasPayrolls)
                            <small class="text-muted">(Click "Fetch/Generate" to create payroll data)</small>
                        @endif
                    </h6>
                    <div>
                        <button wire:click="fetchPayrolls" class="btn btn-save-payroll btn-sm">
                            <i class="fas fa-download me-2"></i>
                            Fetch/Generate
                        </button>
                    </div>
                </div>
                <div class="">
                    @if(!$hasPayrolls && !$isFetching)
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-database fa-3x mb-3 opacity-50"></i>
                                <h5 class="mb-2">No Payroll Data Found</h5>
                                <p class="mb-3">Click the "Fetch/Generate" button above to generate payroll data for {{ $availableMonths[$selectedMonth] ?? '' }} {{ $selectedYear }}</p>
                                <button wire:click="fetchPayrolls" class="btn btn-save-payroll">
                                    <i class="fas fa-download me-2"></i>
                                    Generate Payrolls Now
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive mt-2">
                            <table class="table table-hover align-middle">
                                <thead class="bg-sec">
                                    <tr>
                                        <th class="py-3" style="white-space: nowrap;">
                                            <i class="fas fa-hashtag me-2" ></i> ID
                                        </th>
                                        <th class="py-3" style="white-space: nowrap;">
                                            <i class="fas fa-user me-2" ></i> Worker
                                        </th>
                                        <th class="py-3" style="white-space: nowrap;">
                                            <i class="fas fa-dollar-sign me-2" ></i> Rate
                                        </th>
                                        <th class="py-3 text-center" style="white-space: nowrap;">
                                            <i class="fas fa-clock me-2" ></i> Days/Hours
                                        </th>
                                        <th class="py-3 text-end" style="white-space: nowrap;">
                                            <i class="fas fa-chart-line me-2" ></i> Gross Amount
                                        </th>
                                        <th class="py-3 text-end" style="white-space: nowrap;">
                                            <i class="fas fa-hand-holding-usd me-2" ></i> Advances
                                        </th>
                                        <th class="py-3 text-end" style="white-space: nowrap;">
                                            <i class="fas fa-euro-sign me-2" ></i> Net Amount
                                        </th>
                                        <th class="py-3" style="white-space: nowrap;">
                                            <i class="fas fa-project-diagram me-2" ></i> Projects
                                        </th>
                                        <th class="py-3" style="white-space: nowrap;">
                                            <i class="fas fa-calendar-alt me-2" ></i> Created At
                                        </th>
                                        <th class="py-3 text-center" style="white-space: nowrap;">
                                            <i class="fas fa-cog me-2" ></i> Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrolls as $payroll)
                                        <tr class="border-bottom">
                                            <td>
                                                <span class="badge bg-light text-dark px-3 py-2">
                                                    <i class="fas fa-tag me-1"></i>
                                                    #{{ $payroll->id }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $payroll->worker->name }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ $payroll->worker->email }}
                                                </small>
                                                @if($payroll->worker->phone)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>
                                                        {{ $payroll->worker->phone }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info px-3 py-2">
                                                    {{ ucfirst($payroll->rate_type) }}
                                                </span>
                                                <br>
                                                <small>€{{ number_format($payroll->rate_snapshot, 2) }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-semibold">{{ $payroll->total_days }} days</div>
                                                <small class="text-muted">{{ number_format($payroll->total_hours, 2) }} hrs</small>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-semibold">€ {{ number_format($payroll->gross_amount, 2) }}</span>
                                            </td>
                                            <td class="text-end text-danger">
                                                -€ {{ number_format($payroll->advance_deduction, 2) }}
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">
                                                    € {{ number_format($payroll->net_amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @foreach($payroll->projectBreakdowns->take(2) as $breakdown)
                                                    <div class="small">
                                                        <i class="fas fa-project-diagram text-muted me-1"></i>
                                                        {{ $breakdown->project->name }} ({{ number_format($breakdown->hours, 1) }} hrs)
                                                    </div>
                                                @endforeach
                                                @if($payroll->projectBreakdowns->count() > 2)
                                                    <small class="text-muted">+{{ $payroll->projectBreakdowns->count() - 2 }} more</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-calendar-day text-muted me-1"></i>
                                                    <small>{{ $payroll->created_at->format('M d, Y') }}</small>
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock text-muted me-1"></i>
                                                    <small>{{ $payroll->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info" 
                                                        wire:click="viewPayrollDetails({{ $payroll->id }})"
                                                        style="font-size: 12px; padding: 3px 6px;">
                                                    <i class="fas fa-eye me-1"></i> Details
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No payroll records found for this period</p>
                                                    <small>Try adjusting your search filters</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination with Info -->
                        @if($payrolls->total() > 0)
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Showing {{ $payrolls->firstItem() ?? 0 }} to {{ $payrolls->lastItem() ?? 0 }} 
                                    of {{ $payrolls->total() }} payrolls
                                </div>
                                <div>
                                    {{ $payrolls->links() }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- View Payroll Details Modal -->
    @if($showViewModal && $selectedPayroll)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content" style="border-radius: 1rem;">
                    <div class="modal-header" style="background: linear-gradient(135deg, #00cfe8 70%, #ffffff 100%); border-radius: 1rem 1rem 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-file-invoice-dollar fa-2x text-white"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-white fw-bold mb-0">Payroll Details</h5>
                                <small class="text-white-50">{{ $selectedPayroll->worker->name }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-black" wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Period Info -->
                        <div class="period-info-modern mb-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <i class="fas fa-calendar-alt me-2 text-danger"></i>
                                    <strong>Period:</strong> {{ $selectedPayroll->batch->month_name ?? $availableMonths[$selectedPayroll->batch->month] }} {{ $selectedPayroll->batch->year }}
                                    <br>
                                    <small class="text-muted">{{ $selectedPayroll->batch->period_start->format('M d, Y') }} - {{ $selectedPayroll->batch->period_end->format('M d, Y') }}</small>
                                </div>
                                <span class="badge {{ $selectedPayroll->batch->status === 'finalized' ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                                    {{ ucfirst($selectedPayroll->batch->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Worker Info -->
                        <div class="worker-info-modern mb-4">
                            <div class="worker-avatar">
                                <i class="fas fa-user-circle fa-2x"></i>
                            </div>
                            <div class="worker-details">
                                <h6 class="mb-0 fw-bold">{{ $selectedPayroll->worker->name }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>{{ $selectedPayroll->worker->email }}
                                </small>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-phone me-1"></i>{{ $selectedPayroll->worker->phone ?? 'N/A' }}
                                </small>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="stat-card-mini" style="background: linear-gradient(135deg, #6c176e 0%, #930095 100%);">
                                    <div class="stat-card-mini-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <span class="stat-label">Total Hours</span>
                                        <h4 class="stat-value-mini mb-0 text-white">{{ number_format($selectedPayroll->total_hours, 2) }}</h4>
                                        <small>{{ $selectedPayroll->total_days }} days worked</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <div class="stat-card-mini-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <span class="stat-label">Gross Amount</span>
                                        <h4 class="stat-value-mini mb-0 text-white">€ {{ number_format($selectedPayroll->gross_amount, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-mini" style="background: linear-gradient(135deg, #674300 0%, #a26b03 100%);">
                                    <div class="stat-card-mini-icon">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                    <div class="stat-card-mini-content">
                                        <span class="stat-label">Net Amount</span>
                                        <h4 class="stat-value-mini mb-0 text-white">€ {{ number_format($selectedPayroll->net_amount, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rate & Overtime Info -->
                        <div class="info-grid mb-4">
                            <div class="info-item">
                                <span><i class="fas fa-dollar-sign me-2 text-muted"></i>Rate Type:</span>
                                <strong class="badge bg-info">{{ ucfirst($selectedPayroll->rate_type) }}</strong>
                            </div>
                            <div class="info-item">
                                <span><i class="fas fa-tag me-2 text-muted"></i>Rate Snapshot:</span>
                                <strong>€{{ number_format($selectedPayroll->rate_snapshot, 2) }}</strong>
                            </div>
                            <div class="info-item">
                                <span><i class="fas fa-chart-line me-2 text-muted"></i>Overtime Multiplier:</span>
                                <strong>{{ $selectedPayroll->overtime_multiplier_used ?? 1.5 }}x</strong>
                            </div>
                            @if($selectedPayroll->advance_deduction > 0)
                            <div class="info-item">
                                <span><i class="fas fa-hand-holding-usd me-2 text-muted"></i>Advance Deduction:</span>
                                <strong class="text-danger">-€{{ number_format($selectedPayroll->advance_deduction, 2) }}</strong>
                            </div>
                            @endif
                            @if($selectedPayroll->manual_adjustment != 0)
                            <div class="info-item">
                                <span><i class="fas fa-sliders-h me-2 text-muted"></i>Manual Adjustment:</span>
                                <strong class="{{ $selectedPayroll->manual_adjustment > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $selectedPayroll->manual_adjustment > 0 ? '+' : '' }}€{{ number_format($selectedPayroll->manual_adjustment, 2) }}
                                </strong>
                            </div>
                            @endif
                        </div>

                        <!-- Project Breakdown -->
                        @if($selectedPayroll->projectBreakdowns->count() > 0)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light border-0">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-project-diagram me-2 text-info"></i>
                                        Project Breakdown
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-sec">
                                                <tr>
                                                    <th><i class="fas fa-project-diagram me-2"></i>Project</th>
                                                    <th class="text-center"><i class="fas fa-calendar-day me-2"></i>Days</th>
                                                    <th class="text-center"><i class="fas fa-clock me-2"></i>Hours</th>
                                                    <th class="text-end"><i class="fas fa-dollar-sign me-2"></i>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedPayroll->projectBreakdowns as $breakdown)
                                                    <tr class="border-bottom">
                                                        <td class="fw-semibold">
                                                            <i class="fas fa-building me-2 text-secondary"></i>
                                                            {{ $breakdown->project->name }}
                                                        </td>
                                                        <td class="text-center">{{ $breakdown->days }}</td>
                                                        <td class="text-center">{{ number_format($breakdown->hours, 2) }}</td>
                                                        <td class="text-end text-success fw-semibold">€ {{ number_format($breakdown->amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr class="fw-bold">
                                                    <td>TOTAL</td>
                                                    <td class="text-center">{{ $selectedPayroll->projectBreakdowns->sum('days') }}</td>
                                                    <td class="text-center">{{ number_format($selectedPayroll->total_hours, 2) }}</td>
                                                    <td class="text-end text-danger">€ {{ number_format($selectedPayroll->gross_amount, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        @if($selectedPayroll->notes)
                            <div class="alert alert-info">
                                <i class="fas fa-sticky-note me-2"></i>
                                <strong>Notes:</strong> {{ $selectedPayroll->notes }}
                            </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="text-muted small mt-3">
                            <i class="fas fa-calendar-plus me-1"></i>
                            Created: {{ $selectedPayroll->created_at->format('M d, Y h:i A') }}
                            @if($selectedPayroll->updated_at != $selectedPayroll->created_at)
                                <span class="ms-3">
                                    <i class="fas fa-edit me-1"></i>
                                    Updated: {{ $selectedPayroll->updated_at->format('M d, Y h:i A') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeViewModal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                        <button type="button" class="btn btn-save-payroll" wire:click="exportToCSV">
                            <i class="fas fa-file-pdf me-1"></i>
                            Generate Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

  <!-- Save Monthly Summary Modal - Modern Design -->
@if($showSaveModal)
    <div class="modal-modern-overlay" wire:click.self="$set('showSaveModal', false)">
        <div class="modal-modern-container" style="max-width: 500px;">
            <div class="modern-modal-content">
                <div class="modern-modal-header" style="background: linear-gradient(135deg, #28a745 70%, #ffffff 100%);">
                    <div class="modern-modal-icon" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-save"></i>
                    </div>
                    <div class="modern-modal-title">
                        <h5 class="mb-0 fw-bold text-white">Save Monthly Summary</h5>
                        <small>Save payroll summary for future reference</small>
                    </div>
                    <button type="button" class="modern-modal-close" wire:click="$set('showSaveModal', false)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modern-modal-body">
                    <!-- Period Summary Card -->
                    <div class="summary-info-card mb-4">
                        <div class="summary-info-header">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Period Summary
                        </div>
                        <div class="summary-info-body">
                            <div class="summary-row">
                                <span class="summary-label">Period:</span>
                                <span class="summary-value fw-bold">{{ $availableMonths[$selectedMonth] ?? '' }} {{ $selectedYear }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Workers:</span>
                                <span class="summary-value">{{ number_format($totalWorkers) }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Payrolls:</span>
                                <span class="summary-value">{{ number_format($totalPayrolls) }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Net Amount:</span>
                                <span class="summary-value text-success fw-bold">€ {{ number_format($totalNetAmount, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Gross Amount:</span>
                                <span class="summary-value">€ {{ number_format($totalGrossAmount, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Advances:</span>
                                <span class="summary-value text-danger">€ {{ number_format($totalAdvancesDeducted, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="modern-form-group mb-4">
                        <label class="modern-label">
                            <i class="fas fa-sticky-note me-1" style="color: #28a745;"></i>
                            Notes (Optional)
                        </label>
                        <textarea class="modern-textarea" rows="4" wire:model="saveSummaryNotes" 
                                  placeholder="Add any notes about this month's payroll..."></textarea>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            These notes will be saved with the summary for future reference.
                        </small>
                    </div>

                    <!-- Warning Alert -->
                    <div class="alert-modern alert-warning-modern mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> This will save the current payroll summary to the database for future reference and profit/loss calculations. This action can be performed multiple times and will update the existing summary.
                    </div>
                </div>

                <div class="modern-modal-footer">
                    <button class="modal-btn-secondary" wire:click="$set('showSaveModal', false)">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button class="modal-btn-primary" wire:click="saveMonthlySummary" wire:loading.attr="disabled" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-2"></i> Save Summary
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

    <style>
        /* Modern Modal Styles - Add to existing styles */
.modal-modern-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-modern-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: 0 20px;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-modal-content {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modern-modal-header {
    background: linear-gradient(135deg, #ff0000 0%, #000000 100%);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modern-modal-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.modern-modal-icon i {
    font-size: 1.25rem;
}

.modern-modal-title {
    flex: 1;
    color: white;
}

.modern-modal-title h5 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.modern-modal-title small {
    font-size: 0.7rem;
    opacity: 0.8;
}

.modern-modal-close {
    background: rgba(0, 0, 0, 0.2);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modern-modal-close:hover {
    background: rgba(0, 0, 0, 0.3);
    transform: rotate(90deg);
    color: white;
}

.modern-modal-body {
    padding: 1.25rem;
    max-height: 60vh;
    overflow-y: auto;
}

.modern-modal-footer {
    padding: 0.75rem 1.25rem;
    background: #f8f9fa;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    border-top: 1px solid #f0f0f0;
}

.modal-btn-primary,
.modal-btn-secondary {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.8rem;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
}

.modal-btn-primary {
    background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
    color: white;
}

.modal-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 0, 0, 0.3);
}

.modal-btn-secondary {
    background: white;
    color: #6c757d;
    border: 1px solid #e5e7eb;
}

.modal-btn-secondary:hover {
    background: #f8f9fa;
    border-color: #ff0000;
    color: #ff0000;
}

/* Summary Info Card */
.summary-info-card {
    background: #f8f9fa;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.summary-info-header {
    background: #e9ecef;
    padding: 0.75rem 1rem;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

.summary-info-body {
    padding: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.summary-value {
    font-size: 0.875rem;
    font-weight: 500;
    color: #333;
}

/* Modern Form Elements */
.modern-form-group {
    margin-bottom: 1rem;
}

.modern-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    color: #333;
    font-size: 0.875rem;
}

.modern-textarea {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    background: white;
    resize: vertical;
    min-height: 100px;
}

.modern-textarea:focus {
    border-color: #ff0000;
    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
    outline: none;
}

/* Alert Modern */
.alert-modern {
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.alert-warning-modern {
    background: #fff3cd;
    border-left: 3px solid #ffc107;
    color: #856404;
}

/* Custom scrollbar for modal body */
.modern-modal-body::-webkit-scrollbar {
    width: 6px;
}

.modern-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.modern-modal-body::-webkit-scrollbar-thumb {
    background: #f1f1f1;
    border-radius: 3px;
}

.modern-modal-body::-webkit-scrollbar-thumb:hover {
    background: #f1f1f1;
}

/* Responsive */
@media (max-width: 640px) {
    .modal-modern-container {
        max-width: 95%;
        margin: 0 10px;
    }
    
    .summary-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
        /* Custom styling */
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }
        
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
        
        .btn-save-payroll {
            background: #ff0000;
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-save-payroll:hover {
            background: #000000;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .btn-success {
            background: #28a745;
            border: none;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .btn-infor {
            background: #00cfe8;
            border: none;
            color: white;
        }
        
        .btn-infor:hover {
            background: #00cfe8;
            transform: translateY(-1px);
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
        
        .badge {
            font-weight: 500;
            border-radius: 0.5rem;
        }
        
        .alert {
            border-radius: 0.75rem;
            border: none;
        }
        
        /* Pagination styling */
        .pagination {
            margin-bottom: 0;
        }
        
        .page-link {
            border-radius: 0.5rem;
            margin: 0 2px;
            color: #ff0000;
            border: 1px solid #e0e0e0;
        }
        
        .page-link:hover {
            background-color: #ff0000;
            border-color: #ff0000;
            color: white;
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, #ff0000, #000000);
            border-color: #ff0000;
        }
        
        /* Modal styling */
        .modal.show {
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            border-radius: 1rem;
            border: none;
        }
        
        .modal-header {
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        .bg-sec {
            background-color: #f8f9fa !important;
        }
        /* Stat cards mini */
        .stat-card-mini {
            border-radius: 1rem;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
        }
        
        .stat-card-mini-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stat-card-mini-content {
            flex: 1;
        }
        
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
        }
        
        .stat-value-mini {
            font-size: 24px;
            font-weight: 700;
        }
        
        /* Worker info */
        .worker-info-modern {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid #f0f0f0;
        }
        
        .worker-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .period-info-modern {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid #00cfe8;
            font-size: 0.875rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-close-white {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }
        
        .btn-close-white:hover {
            opacity: 1;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 0.5rem;
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
            }
            
            .d-flex.gap-2 .btn {
                width: 100%;
            }
            
            .row.g-3 {
                flex-direction: column;
                gap: 10px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card-mini {
                flex-direction: column;
                text-align: center;
            }
            
            .stat-value-mini {
                font-size: 20px;
            }
        }
    </style>
</div>