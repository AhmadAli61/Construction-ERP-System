{{-- resources/views/livewire/hr/payroll/payroll-history.blade.php --}}
<div>
    <div>
        <div>
            <div class="card shadow-sm border-0">
                <!-- Header with Red-Black Gradient Background -->
                <div class="card-header border-0 pt-4 pb-4"
                    style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-history text-white fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold text-white">Payroll History</h3>
                                <p class="text-white-50 small mb-0">View and manage all payroll batches</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    <!-- Summary Cards -->
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Total Batches</small>
                                            <h4 class="mb-0 fw-bold text-white">{{ $totalBatches }}</h4>
                                        </div>
                                        <i class="fas fa-folder-open fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm"
                                style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Total Gross</small>
                                            <h4 class="mb-0 fw-bold text-white">€ {{ number_format($totalGross, 2) }}
                                            </h4>
                                        </div>
                                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm"
                                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Total Net</small>
                                            <h4 class="mb-0 fw-bold text-white">€ {{ number_format($totalNet, 2) }}</h4>
                                        </div>
                                        <i class="fas fa-euro-sign fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm"
                                style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Workers Paid</small>
                                            <h4 class="mb-0 fw-bold text-white">{{ $totalWorkers }}</h4>
                                        </div>
                                        <i class="fas fa-users fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Search and Filters Section with Button Trigger -->
                    <div class="card-body">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-search me-2" style="color: #ff0000;"></i>
                                        Search & Filter Payroll Batches
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        wire:click="resetFilters">
                                        <i class="fas fa-undo-alt me-1"></i> Reset All Filters
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form wire:submit.prevent="performSearch">
                                    <div class="row g-3 pt-3">
                                        <!-- Global Search -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-search me-1" style="color: #ff0000;"></i>
                                                Global Search
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <input type="text" class="form-control"
                                                    placeholder="Search by year, month, worker name, email, phone..."
                                                    wire:model="tempSearch">
                                                @if ($tempSearch)
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        wire:click="clearSearch">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Search across year, month, worker name, email, phone
                                            </small>
                                        </div>

                                        <!-- Status Filter -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-chart-pie me-1" style="color: #ff0000;"></i>
                                                Status
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-flag-checkered"></i>
                                                </span>
                                                <select class="form-select" wire:model="tempStatusFilter">
                                                    <option value="">All Statuses</option>
                                                    <option value="draft">Draft</option>
                                                    <option value="finalized">Finalized</option>
                                                </select>
                                                @if ($tempStatusFilter)
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        wire:click="clearStatusFilter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Year Filter -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-calendar-year me-1" style="color: #ff0000;"></i>
                                                Year
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <select class="form-select" wire:model="tempYearFilter">
                                                    <option value="">All Years</option>
                                                    @foreach ($availableYears as $year)
                                                        <option value="{{ $year }}">{{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($tempYearFilter)
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        wire:click="clearYearFilter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
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
                                                <select class="form-select" wire:model="tempMonthFilter">
                                                    <option value="">All Months</option>
                                                    @foreach ($months as $num => $name)
                                                        <option value="{{ $num }}">{{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($tempMonthFilter)
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        wire:click="clearMonthFilter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Search and Clear Buttons -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold invisible">Actions</label>
                                            <div class="d-flex gap-2 w-100">
                                                <button type="submit" class="btn btn-primary flex-grow-1"
                                                    style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none;">
                                                    <i class="fas fa-search me-2"></i> Search
                                                </button>
                                                <button type="button" class="btn btn-secondary"
                                                    wire:click="resetFilters" title="Clear All Filters">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </form>

                                <!-- Active Filters Display -->
                                @if ($search || $statusFilter || $yearFilter || $monthFilter)
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <small class="text-muted me-2">
                                                <i class="fas fa-filter me-1"></i>Active filters:
                                            </small>
                                            @if ($search)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-search me-1"></i>
                                                    Search: {{ $search }}
                                                    <button type="button" class="btn-close btn-close-white ms-2"
                                                        style="font-size: 8px;" wire:click="clearSearch"></button>
                                                </span>
                                            @endif
                                            @if ($statusFilter)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-chart-pie me-1"></i>
                                                    Status: {{ ucfirst($statusFilter) }}
                                                    <button type="button" class="btn-close btn-close-white ms-2"
                                                        style="font-size: 8px;"
                                                        wire:click="clearStatusFilter"></button>
                                                </span>
                                            @endif
                                            @if ($yearFilter)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-calendar-year me-1"></i>
                                                    Year: {{ $yearFilter }}
                                                    <button type="button" class="btn-close btn-close-white ms-2"
                                                        style="font-size: 8px;" wire:click="clearYearFilter"></button>
                                                </span>
                                            @endif
                                            @if ($monthFilter)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    Month: {{ $months[$monthFilter] ?? $monthFilter }}
                                                    <button type="button" class="btn-close btn-close-white ms-2"
                                                        style="font-size: 8px;"
                                                        wire:click="clearMonthFilter"></button>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>



                    <!-- Payroll Batches Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-sec">
                                <tr>
                                    <th class="py-3">
                                        <i class="fas fa-hashtag me-2"></i> ID
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-calendar-alt me-2"></i> Period
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-calendar-day me-2"></i> Start Date
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-calendar-check me-2"></i> End Date
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-users me-2"></i> Workers
                                    </th>
                                    <th class="py-3 text-end">
                                        <i class="fas fa-chart-line me-2"></i> Total Gross
                                    </th>
                                    <th class="py-3 text-end">
                                        <i class="fas fa-euro-sign me-2"></i> Total Net
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-chart-pie me-2"></i> Status
                                    </th>
                                    <th class="py-3">
                                        <i class="fas fa-clock me-2"></i> Finalized Date
                                    </th>
                                    <th class="py-3 text-center">
                                        <i class="fas fa-cog me-2"></i> Actions
                                    </th>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
                                    <tr class="border-bottom">
                                        <td>
                                            <span class="badge bg-light text-dark px-3 py-2">
                                                <i class="fas fa-tag me-1"></i>
                                                #{{ $batch->id }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $batch->month_name }} {{ $batch->year }}</div>
                                        </td>
                                        <td>{{ $batch->period_start->format('M d, Y') }}</td>
                                        <td>{{ $batch->period_end->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info px-3 py-2">
                                                <i class="fas fa-user me-1"></i>
                                                {{ $batch->payrolls->first()->worker->name ?? 'No worker assigned' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold">
                                                € {{ number_format($batch->payrolls->sum('gross_amount'), 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">
                                                € {{ number_format($batch->payrolls->sum('net_amount'), 2) }}
                                            </strong>
                                        </td>
                                        <td>
                                            @if ($batch->status === 'finalized')
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Finalized
                                                </span>
                                            @else
                                                <span class="badge bg-warning px-3 py-2">
                                                    <i class="fas fa-edit me-1"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($batch->finalized_at)
                                                <div>
                                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                    <small>{{ $batch->finalized_at->format('M d, Y') }}</small>
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock text-muted me-1"></i>
                                                    <small>{{ $batch->finalized_at->format('H:i') }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button class="btn btn-sm btn-infor"
                                                    wire:click="viewPayrollDetails({{ $batch->id }})"
                                                    style="font-size: 12px; padding: 3px 6px; white-space: nowrap;">
                                                    <i class="fas fa-eye me-1"></i> Details
                                                </button>

                                                @if ($batch->status === 'draft')
                                                    <button class="btn btn-sm btn-success"
                                                        wire:click="finalizeBatch({{ $batch->id }})"
                                                        style="font-size: 12px; padding: 3px 6px; white-space: nowrap;">
                                                        <i class="fas fa-lock me-1"></i> Finalize
                                                    </button>
                                                @endif

                                                <!-- Delete Button -->
                                                <button class="btn btn-sm btn-danger"
                                                    wire:click="confirmDeleteBatch({{ $batch->id }})"
                                                    style="font-size: 12px; padding: 3px 6px; white-space: nowrap;">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                <p class="mb-0">No payroll batches found</p>
                                                <small>Generate your first payroll from the dashboard</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination with Info -->
                    @if ($batches->total() > 0)
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing {{ $batches->firstItem() ?? 0 }} to {{ $batches->lastItem() ?? 0 }}
                                of {{ $batches->total() }} batches
                            </div>
                            <div>
                                {{ $batches->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modern Payroll Details Modal -->
            @if ($selectedPayroll)
                <div class="modal-details-overlay">
                    <div class="modal-details-container">
                        <div class="details-modal-content">
                            <div class="details-modal-header">
                                <div class="details-modal-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="details-modal-title">
                                    <h5>Payroll Details</h5>
                                    <small>{{ $selectedPayroll->worker->name }}</small>
                                </div>
                                <button type="button" class="details-modal-close" wire:click="closePayrollModal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="details-modal-body">
                                <!-- Worker Info Cards -->
                                <div class="details-info-grid">
                                    <div class="details-info-card">
                                        <span class="details-info-label">
                                            <i class="fas fa-user me-1"></i> Worker Information
                                        </span>
                                        <div class="details-info-title">{{ $selectedPayroll->worker->name }}</div>
                                        <div class="details-info-text">
                                            <i class="fas fa-envelope"></i>
                                            <span>{{ $selectedPayroll->worker->email }}</span>
                                        </div>
                                        <div class="details-info-text">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>Rate: {{ ucfirst($selectedPayroll->rate_type) }} -
                                                €{{ number_format($selectedPayroll->rate_snapshot, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="details-info-card">
                                        <span class="details-info-label">
                                            <i class="fas fa-calendar-alt me-1"></i> Period
                                        </span>
                                        <div class="details-info-title">{{ $selectedPayroll->batch->month_name }}
                                            {{ $selectedPayroll->batch->year }}</div>
                                        <div class="details-info-text">
                                            <i class="fas fa-calendar-week"></i>
                                            <span>{{ $selectedPayroll->batch->period_start->format('M d, Y') }} -
                                                {{ $selectedPayroll->batch->period_end->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Earnings Breakdown -->
                                <div class="details-section-header">
                                    <i class="fas fa-chart-line"></i>
                                    <h6>Earnings Breakdown</h6>
                                </div>

                                <div class="details-stats-grid">
                                    <div class="details-stat-card"
                                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                        <span class="details-stat-label">Total Hours</span>
                                        <div class="details-stat-value">
                                            {{ number_format($selectedPayroll->total_hours, 2) }}</div>
                                        <span class="details-stat-sub">{{ $selectedPayroll->total_days }} days
                                            worked</span>
                                    </div>

                                    <div class="details-stat-card"
                                        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                        <span class="details-stat-label">Gross Amount</span>
                                        <div class="details-stat-value">€
                                            {{ number_format($selectedPayroll->gross_amount, 2) }}</div>
                                    </div>

                                    <div class="details-stat-card"
                                        style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%);">
                                        <span class="details-stat-label">Net Amount</span>
                                        <div class="details-stat-value">€
                                            {{ number_format($selectedPayroll->net_amount, 2) }}</div>
                                    </div>
                                </div>

                                <!-- Deductions & Info -->
                                <div class="details-deductions-grid">
                                    <div class="details-deduction-card"
                                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                        <div class="details-deduction-item">
                                            <span><i class="fas fa-hand-holding-usd"></i> Advance Deductions</span>
                                            <strong>€
                                                {{ number_format($selectedPayroll->advance_deduction, 2) }}</strong>
                                        </div>
                                        @if ($selectedPayroll->manual_adjustment != 0)
                                            <div class="details-deduction-item">
                                                <span><i class="fas fa-sliders-h"></i> Manual Adjustment</span>
                                                <strong>€
                                                    {{ number_format($selectedPayroll->manual_adjustment, 2) }}</strong>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="details-deduction-card"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <div class="details-deduction-item">
                                            <span><i class="fas fa-clock"></i> Rate Snapshot</span>
                                            <strong>€ {{ number_format($selectedPayroll->rate_snapshot, 2) }}
                                                ({{ ucfirst($selectedPayroll->rate_type) }})</strong>
                                        </div>
                                        <div class="details-deduction-item">
                                            <span><i class="fas fa-chart-line"></i> Overtime Multiplier</span>
                                            <strong>{{ $selectedPayroll->overtime_multiplier }}x</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Project Breakdown -->
                                @if ($selectedPayroll->projectBreakdowns->count() > 0)
                                    <div class="details-section-header">
                                        <i class="fas fa-project-diagram"></i>
                                        <h6>Project Breakdown</h6>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="details-table">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-project-diagram me-2"></i>Project</th>
                                                    <th class="text-center"><i
                                                            class="fas fa-calendar-day me-2"></i>Days</th>
                                                    <th class="text-center"><i class="fas fa-clock me-2"></i>Hours
                                                    </th>
                                                    <th class="text-end"><i class="fas fa-dollar-sign me-2"></i>Amount
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($selectedPayroll->projectBreakdowns as $breakdown)
                                                    <tr>
                                                        <td class="details-project-name">
                                                            {{ $breakdown->project->name }}</td>
                                                        <td class="text-center">{{ $breakdown->days }}</td>
                                                        <td class="text-center">
                                                            {{ number_format($breakdown->hours, 2) }}</td>
                                                        <td class="text-end details-amount">€
                                                            {{ number_format($breakdown->amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <!-- Notes -->
                                @if ($selectedPayroll->notes)
                                    <div class="details-notes">
                                        <i class="fas fa-sticky-note"></i>
                                        <div class="details-notes-content">
                                            <strong>Notes</strong>
                                            <p>{{ $selectedPayroll->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="details-modal-footer">
                                <button type="button" class="details-btn-secondary" wire:click="closePayrollModal">
                                    <i class="fas fa-times me-1"></i> Close
                                </button>
                                <button type="button" class="details-btn-primary"
                                    wire:click="generateInvoice({{ $selectedPayroll->id }})">
                                    <i class="fas fa-file-pdf me-1"></i> Generate Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Finalize Batch Confirmation Modal -->
            @if ($confirmingFinalize)
                <div class="modal-finalize-overlay">
                    <div class="modal-finalize-container">
                        <div class="finalize-modal-content">
                            <div class="finalize-modal-header">
                                <div class="finalize-modal-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="finalize-modal-title">
                                    <h5>Finalize Payroll Batch</h5>
                                    <small>This action cannot be undone</small>
                                </div>
                                <button type="button" class="finalize-modal-close"
                                    wire:click="$set('confirmingFinalize', false)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="finalize-modal-body">
                                <div class="finalize-alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div class="finalize-alert-warning-content">
                                        <strong>Warning!</strong>
                                        <p>This action cannot be undone.</p>
                                    </div>
                                </div>

                                <p class="finalize-message">
                                    Are you sure you want to finalize this payroll batch?
                                </p>

                                <div class="finalize-note">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Once finalized, you will not be able to make any changes to this payroll
                                        batch.</span>
                                </div>
                            </div>

                            <div class="finalize-modal-footer">
                                <button type="button" class="finalize-btn-cancel"
                                    wire:click="$set('confirmingFinalize', false)">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="button" class="finalize-btn-primary" wire:click="confirmFinalize">
                                    <i class="fas fa-lock"></i> Yes, Finalize Batch
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modern Delete Batch Confirmation Modal -->
            @if ($confirmingDelete)
                <div class="modal-delete-overlay">
                    <div class="modal-delete-container">
                        <div class="delete-modal-content">
                            <div class="delete-modal-header">
                                <div class="delete-modal-icon">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                                <div class="delete-modal-title">
                                    <h5>Delete Payroll Batch</h5>
                                    <small>This action cannot be undone</small>
                                </div>
                                <button type="button" class="delete-modal-close"
                                    wire:click="$set('confirmingDelete', false)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="delete-modal-body">
                                <div class="delete-alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div class="delete-alert-content">
                                        <strong>Warning!</strong>
                                        <p>This action cannot be undone.</p>
                                    </div>
                                </div>

                                <p class="delete-message">
                                    Are you sure you want to delete this payroll batch?
                                </p>

                                <div class="delete-list-title">
                                    <i class="fas fa-trash-alt me-1"></i> This will permanently delete:
                                </div>
                                <ul class="delete-items-list">
                                    <li><i class="fas fa-folder"></i> The entire payroll batch record</li>
                                    <li><i class="fas fa-users"></i> All associated payroll records for workers</li>
                                    <li><i class="fas fa-chart-pie"></i> All project breakdowns and calculations</li>
                                </ul>

                                @php
                                    $batchToDeleteObj = \App\Models\PayrollBatch::find($batchToDelete);
                                @endphp

                                @if ($batchToDeleteObj)
                                    <div class="delete-batch-info">
                                        <div class="delete-batch-header">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Batch Information</strong>
                                        </div>
                                        <div class="delete-batch-details">
                                            <div class="delete-batch-detail-item">
                                                <span class="delete-batch-detail-label">Batch ID:</span>
                                                <span
                                                    class="delete-batch-detail-value">#{{ $batchToDeleteObj->id }}</span>
                                            </div>
                                            <div class="delete-batch-detail-item">
                                                <span class="delete-batch-detail-label">Period:</span>
                                                <span
                                                    class="delete-batch-detail-value">{{ $batchToDeleteObj->month_name }}
                                                    {{ $batchToDeleteObj->year }}</span>
                                            </div>
                                            <div class="delete-batch-detail-item">
                                                <span class="delete-batch-detail-label">Status:</span>
                                                <span class="delete-batch-detail-value">
                                                    @if ($batchToDeleteObj->status === 'finalized')
                                                        <span style="color: #28a745;">Finalized</span>
                                                    @else
                                                        <span style="color: #ffc107;">Draft</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="delete-batch-detail-item">
                                                <span class="delete-batch-detail-label">Workers:</span>
                                                <span
                                                    class="delete-batch-detail-value">{{ $batchToDeleteObj->payrolls->count() }}
                                                    worker(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="delete-final-warning">
                                    <i class="fas fa-skull-crosswalk"></i>
                                    <p>Once deleted, you cannot recover this data!</p>
                                </div>
                            </div>

                            <div class="delete-modal-footer">
                                <button type="button" class="delete-btn-secondary"
                                    wire:click="$set('confirmingDelete', false)">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="button" class="delete-btn-danger" wire:click="deleteBatch">
                                    <i class="fas fa-trash-alt me-1"></i> Yes, Delete Permanently
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <style>
                /* Modern Delete Batch Modal Styles */
                .modal-delete-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(4px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                }

                .modal-delete-container {
                    position: relative;
                    width: 100%;
                    max-width: 500px;
                    margin: 0 20px;
                    animation: deleteModalSlideIn 0.3s ease;
                }

                @keyframes deleteModalSlideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-30px) scale(0.95);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }

                .delete-modal-content {
                    background: white;
                    border-radius: 1rem;
                    overflow: hidden;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
                }

                .delete-modal-header {
                    background: linear-gradient(135deg, #ea5455 70%, #ffffff 100%);
                    padding: 1.25rem 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    position: relative;
                }

                .delete-modal-icon {
                    width: 48px;
                    height: 48px;
                    background: rgba(255, 255, 255, 0.15);
                    border-radius: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    backdrop-filter: blur(10px);
                }

                .delete-modal-icon i {
                    font-size: 1.5rem;
                }

                .delete-modal-title {
                    flex: 1;
                    color: white;
                }

                .delete-modal-title h5 {
                    font-size: 1.1rem;
                    margin-bottom: 0.25rem;
                    font-weight: 700;
                    color: white;
                }

                .delete-modal-title small {
                    font-size: 0.7rem;
                    opacity: 0.85;
                    display: block;
                }

                .delete-modal-close {
                    background: rgba(0, 0, 0, 0.15);
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
                    padding: 0;
                }

                .delete-modal-close:hover {
                    background: rgba(0, 0, 0, 0.3);
                    transform: rotate(90deg);
                    color: white;
                }

                .delete-modal-body {
                    padding: 1.5rem;
                    max-height: 60vh;
                    overflow-y: auto;
                }

                /* Delete Alert Styles */
                .delete-alert-danger {
                    background: linear-gradient(135deg, #fff5f5 0%, #fee 100%);
                    padding: 1rem;
                    border-radius: 0.75rem;
                    border-left: 4px solid #ea5455;
                    margin-bottom: 1.25rem;
                    display: flex;
                    align-items: flex-start;
                    gap: 0.75rem;
                }

                .delete-alert-danger i {
                    color: #ea5455;
                    font-size: 1.25rem;
                    margin-top: 0.125rem;
                }

                .delete-alert-content {
                    flex: 1;
                }

                .delete-alert-content strong {
                    color: #ea5455;
                    display: block;
                    margin-bottom: 0.25rem;
                }

                .delete-alert-content p {
                    color: #ea5455;
                    margin: 0;
                    font-size: 0.875rem;
                }

                .delete-message {
                    margin-bottom: 1rem;
                    color: #333;
                    font-size: 0.95rem;
                }

                .delete-list-title {
                    color: #6c757d;
                    font-size: 0.8rem;
                    margin-bottom: 0.5rem;
                    font-weight: 600;
                }

                .delete-items-list {
                    margin: 0;
                    padding-left: 1.25rem;
                    margin-bottom: 1rem;
                }

                .delete-items-list li {
                    color: #6c757d;
                    font-size: 0.8rem;
                    margin-bottom: 0.25rem;
                }

                .delete-items-list li:last-child {
                    margin-bottom: 0;
                }

                .delete-items-list li i {
                    color: #ea5455;
                    font-size: 0.7rem;
                    margin-right: 0.5rem;
                }

                /* Batch Info Card */
                .delete-batch-info {
                    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
                    border-radius: 0.75rem;
                    padding: 1rem;
                    margin: 1rem 0;
                    border: 1px solid #ffc107;
                    position: relative;
                    overflow: hidden;
                }

                .delete-batch-info::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 3px;
                    background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
                }

                .delete-batch-header {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    margin-bottom: 0.75rem;
                    padding-bottom: 0.5rem;
                    border-bottom: 1px solid rgba(255, 193, 7, 0.3);
                }

                .delete-batch-header i {
                    color: #ffc107;
                    font-size: 1rem;
                }

                .delete-batch-header strong {
                    color: #856404;
                    font-size: 0.9rem;
                }

                .delete-batch-details {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0.5rem;
                }

                .delete-batch-detail-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    font-size: 0.8rem;
                }

                .delete-batch-detail-label {
                    color: #856404;
                    font-weight: 500;
                }

                .delete-batch-detail-value {
                    color: #856404;
                    font-weight: 600;
                }

                /* Final Warning */
                .delete-final-warning {
                    background: linear-gradient(135deg, #ea5455 0%, #ea5455 100%);
                    border-radius: 0.75rem;
                    padding: 0.875rem;
                    margin-top: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    animation: pulseWarning 1.5s infinite;
                }

                @keyframes pulseWarning {

                    0%,
                    100% {
                        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
                    }

                    50% {
                        box-shadow: 0 0 0 5px rgba(220, 53, 69, 0);
                    }
                }

                .delete-final-warning i {
                    color: white;
                    font-size: 1.25rem;
                }

                .delete-final-warning p {
                    margin: 0;
                    color: white;
                    font-weight: 600;
                    font-size: 0.85rem;
                }

                /* Modal Footer */
                .delete-modal-footer {
                    padding: 1rem 1.5rem;
                    background: #f8f9fa;
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.75rem;
                    border-top: 1px solid #e9ecef;
                }

                .delete-btn-secondary {
                    padding: 0.5rem 1.25rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: 1px solid #dee2e6;
                    background: white;
                    color: #6c757d;
                }

                .delete-btn-secondary:hover {
                    background: #f8f9fa;
                    border-color: #ea5455;
                    color: #ea5455;
                    transform: translateY(-1px);
                }

                .delete-btn-danger {
                    padding: 0.5rem 1.25rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: none;
                    background: linear-gradient(135deg, #ea5455 0%, #ea5455 100%);
                    color: white;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .delete-btn-danger:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
                    background: linear-gradient(135deg, #ea5455 0%, #ea5455 100%);
                }

                .delete-btn-danger:active {
                    transform: translateY(0);
                }

                /* Custom Scrollbar */
                .delete-modal-body::-webkit-scrollbar {
                    width: 6px;
                }

                .delete-modal-body::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 3px;
                }

                .delete-modal-body::-webkit-scrollbar-thumb {
                    background: #f1f1f1;
                    border-radius: 3px;
                }

                /* Responsive */
                @media (max-width: 640px) {
                    .modal-delete-container {
                        max-width: 95%;
                        margin: 0 10px;
                    }

                    .delete-modal-header {
                        padding: 1rem;
                    }

                    .delete-modal-icon {
                        width: 40px;
                        height: 40px;
                    }

                    .delete-modal-icon i {
                        font-size: 1.25rem;
                    }

                    .delete-modal-title h5 {
                        font-size: 1rem;
                    }

                    .delete-modal-body {
                        padding: 1.25rem;
                    }

                    .delete-batch-details {
                        grid-template-columns: 1fr;
                        gap: 0.35rem;
                    }

                    .delete-modal-footer {
                        padding: 0.875rem 1.25rem;
                        flex-direction: column;
                    }

                    .delete-btn-secondary,
                    .delete-btn-danger {
                        width: 100%;
                        justify-content: center;
                    }
                }

                /* Modern Payroll Details Modal Styles */
                .modal-details-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(4px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    overflow-y: auto;
                    padding: 20px;
                }

                .modal-details-container {
                    position: relative;
                    width: 100%;
                    max-width: 800px;
                    margin: auto;
                    animation: detailsModalSlideIn 0.3s ease;
                }

                @keyframes detailsModalSlideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-30px) scale(0.95);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }

                .details-modal-content {
                    background: white;
                    border-radius: 1rem;
                    overflow: hidden;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
                    max-height: 90vh;
                    display: flex;
                    flex-direction: column;
                }

                .details-modal-header {
                    background: linear-gradient(135deg, #00cfe8 70%, #ffffff 100%);
                    padding: 1.25rem 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    position: relative;
                }

                .details-modal-icon {
                    width: 48px;
                    height: 48px;
                    background: rgba(255, 255, 255, 0.15);
                    border-radius: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    backdrop-filter: blur(10px);
                }

                .details-modal-icon i {
                    font-size: 1.5rem;
                }

                .details-modal-title {
                    flex: 1;
                    color: white;
                }

                .details-modal-title h5 {
                    font-size: 1.1rem;
                    margin-bottom: 0.25rem;
                    font-weight: 700;
                    color: white;
                }

                .details-modal-title small {
                    font-size: 0.7rem;
                    opacity: 0.85;
                    display: block;
                }

                .details-modal-close {
                    background: rgba(0, 0, 0, 0.15);
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
                    padding: 0;
                }

                .details-modal-close:hover {
                    background: rgba(0, 0, 0, 0.3);
                    transform: rotate(90deg);
                    color: white;
                }

                .details-modal-body {
                    padding: 1.5rem;
                    overflow-y: auto;
                    flex: 1;
                }

                /* Worker Info Cards */
                .details-info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1rem;
                    margin-bottom: 1.5rem;
                }

                .details-info-card {
                    background: #f8f9fa;
                    border-radius: 0.75rem;
                    padding: 1rem;
                    transition: all 0.3s ease;
                    border: 1px solid #e9ecef;
                }

                .details-info-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }

                .details-info-label {
                    font-size: 0.65rem;
                    text-transform: uppercase;
                    color: #6c757d;
                    letter-spacing: 0.5px;
                    margin-bottom: 0.5rem;
                    display: block;
                }

                .details-info-title {
                    font-size: 1rem;
                    font-weight: 700;
                    margin-bottom: 0.25rem;
                    color: #333;
                }

                .details-info-text {
                    font-size: 0.75rem;
                    color: #6c757d;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    margin-top: 0.25rem;
                }

                .details-info-text i {
                    width: 14px;
                    color: #00cfe8;
                }

                /* Earnings Stats Grid */
                .details-stats-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 1rem;
                    margin-bottom: 1.5rem;
                }

                .details-stat-card {
                    border-radius: 0.75rem;
                    padding: 1rem;
                    text-align: center;
                    color: white;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }

                .details-stat-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.1);
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }

                .details-stat-card:hover::before {
                    transform: translateX(0);
                }

                .details-stat-card:hover {
                    transform: translateY(-3px);
                }

                .details-stat-label {
                    font-size: 0.7rem;
                    opacity: 0.9;
                    margin-bottom: 0.5rem;
                    display: block;
                }

                .details-stat-value {
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin-bottom: 0.25rem;
                }

                .details-stat-sub {
                    font-size: 0.65rem;
                    opacity: 0.8;
                }

                /* Deductions Grid */
                .details-deductions-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1rem;
                    margin-bottom: 1.5rem;
                }

                .details-deduction-card {
                    border-radius: 0.75rem;
                    padding: 1rem;
                    color: white;
                    transition: all 0.3s ease;
                }

                .details-deduction-card:hover {
                    transform: translateY(-2px);
                }

                .details-deduction-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 0.5rem;
                }

                .details-deduction-item:last-child {
                    margin-bottom: 0;
                }

                .details-deduction-item i {
                    margin-right: 0.5rem;
                }

                /* Section Headers */
                .details-section-header {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    margin-bottom: 1rem;
                    margin-top: 1.5rem;
                    padding-bottom: 0.5rem;
                    border-bottom: 2px solid #f0f0f0;
                }

                .details-section-header:first-of-type {
                    margin-top: 0;
                }

                .details-section-header i {
                    color: #00cfe8;
                    font-size: 1.1rem;
                }

                .details-section-header h6 {
                    font-weight: 700;
                    margin: 0;
                    color: #333;
                }

                /* Project Breakdown Table */
                .details-table {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0;
                }

                .details-table thead th {
                    background: #f8f9fa;
                    padding: 0.75rem;
                    font-size: 0.75rem;
                    font-weight: 600;
                    color: #6c757d;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    border-bottom: 2px solid #e9ecef;
                }

                .details-table tbody td {
                    padding: 0.75rem;
                    font-size: 0.85rem;
                    border-bottom: 1px solid #e9ecef;
                    vertical-align: middle;
                }

                .details-table tbody tr:hover {
                    background: #f8f9fa;
                }

                .details-project-name {
                    font-weight: 600;
                    color: #333;
                }

                .details-amount {
                    color: #28a745;
                    font-weight: 600;
                }

                /* Notes Section */
                .details-notes {
                    background: linear-gradient(135deg, #e7f3ff 0%, #d4eaff 100%);
                    border-radius: 0.75rem;
                    padding: 1rem;
                    margin-top: 1rem;
                    border-left: 4px solid #00cfe8;
                    display: flex;
                    gap: 0.75rem;
                }

                .details-notes i {
                    color: #00cfe8;
                    font-size: 1rem;
                    margin-top: 0.125rem;
                }

                .details-notes-content {
                    flex: 1;
                }

                .details-notes-content strong {
                    display: block;
                    margin-bottom: 0.25rem;
                    color: #333;
                    font-size: 0.85rem;
                }

                .details-notes-content p {
                    margin: 0;
                    font-size: 0.85rem;
                    color: #555;
                }

                /* Modal Footer */
                .details-modal-footer {
                    padding: 1rem 1.5rem;
                    background: #f8f9fa;
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.75rem;
                    border-top: 1px solid #e9ecef;
                }

                .details-btn-secondary {
                    padding: 0.5rem 1.25rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: 1px solid #dee2e6;
                    background: white;
                    color: #6c757d;
                }

                .details-btn-secondary:hover {
                    background: #f8f9fa;
                    border-color: #ff0000;
                    color: #ff0000;
                    transform: translateY(-1px);
                }

                .details-btn-primary {
                    padding: 0.5rem 1.25rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: none;
                    background: linear-gradient(135deg, #00cfe8 0%, #00cfe8 100%);
                    color: white;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .details-btn-primary:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(0, 119, 255, 0.3);
                }

                .details-btn-primary:active {
                    transform: translateY(0);
                }

                /* Custom Scrollbar for Modal Body */
                .details-modal-body::-webkit-scrollbar {
                    width: 6px;
                }

                .details-modal-body::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 3px;
                }

                .details-modal-body::-webkit-scrollbar-thumb {
                    background: #d6d6d6;
                    border-radius: 3px;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    .modal-details-container {
                        max-width: 95%;
                        margin: 0 auto;
                    }

                    .details-info-grid,
                    .details-stats-grid,
                    .details-deductions-grid {
                        grid-template-columns: 1fr;
                        gap: 0.75rem;
                    }

                    .details-modal-header {
                        padding: 1rem;
                    }

                    .details-modal-icon {
                        width: 40px;
                        height: 40px;
                    }

                    .details-modal-icon i {
                        font-size: 1.25rem;
                    }

                    .details-modal-title h5 {
                        font-size: 0.95rem;
                    }

                    .details-modal-body {
                        padding: 1rem;
                    }

                    .details-stat-value {
                        font-size: 1.25rem;
                    }

                    .details-table {
                        font-size: 0.75rem;
                    }

                    .details-table thead th,
                    .details-table tbody td {
                        padding: 0.5rem;
                    }

                    .details-modal-footer {
                        padding: 0.875rem 1rem;
                        flex-direction: column;
                    }

                    .details-btn-secondary,
                    .details-btn-primary {
                        width: 100%;
                        justify-content: center;
                    }
                }

                /* Modern Finalize Modal Specific Styles */
                .modal-finalize-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(4px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                }

                .modal-finalize-container {
                    position: relative;
                    width: 100%;
                    max-width: 450px;
                    margin: 0 20px;
                    animation: finalizeModalSlideIn 0.3s ease;
                }

                @keyframes finalizeModalSlideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-30px) scale(0.95);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }

                .finalize-modal-content {
                    background: white;
                    border-radius: 1rem;
                    overflow: hidden;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
                }

                .finalize-modal-header {
                    background: linear-gradient(135deg, #28c76f 70%, #ffffff 100%);
                    padding: 1.25rem 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    position: relative;
                }

                .finalize-modal-icon {
                    width: 48px;
                    height: 48px;
                    background: rgba(255, 255, 255, 0.15);
                    border-radius: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    backdrop-filter: blur(10px);
                }

                .finalize-modal-icon i {
                    font-size: 1.5rem;
                }

                .finalize-modal-title {
                    flex: 1;
                    color: white;
                }

                .finalize-modal-title h5 {
                    font-size: 1.1rem;
                    margin-bottom: 0.25rem;
                    color: white;
                    font-weight: 700;
                }

                .finalize-modal-title small {
                    font-size: 0.7rem;
                    opacity: 0.85;
                    display: block;
                }

                .finalize-modal-close {
                    background: rgba(0, 0, 0, 0.15);
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
                    padding: 0;
                }

                .finalize-modal-close:hover {
                    background: rgba(0, 0, 0, 0.3);
                    transform: rotate(90deg);
                    color: white;
                }

                .finalize-modal-body {
                    padding: 1.5rem;
                }

                .finalize-alert-warning {
                    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
                    padding: 1rem;
                    border-radius: 0.75rem;
                    border-left: 4px solid #ffc107;
                    margin-bottom: 1.25rem;
                    display: flex;
                    align-items: flex-start;
                    gap: 0.75rem;
                }

                .finalize-alert-warning i {
                    color: #ffc107;
                    font-size: 1.25rem;
                    margin-top: 0.125rem;
                }

                .finalize-alert-warning-content {
                    flex: 1;
                }

                .finalize-alert-warning-content strong {
                    color: #856404;
                    display: block;
                    margin-bottom: 0.25rem;
                }

                .finalize-alert-warning-content p {
                    color: #856404;
                    margin: 0;
                    font-size: 0.875rem;
                }

                .finalize-message {
                    margin-bottom: 0.75rem;
                    color: #333;
                    font-size: 0.95rem;
                }

                .finalize-note {
                    color: #6c757d;
                    font-size: 0.8rem;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem;
                    background: #f8f9fa;
                    border-radius: 0.5rem;
                    margin-top: 0.5rem;
                }

                .finalize-note i {
                    color: #ff0000;
                }

                .finalize-modal-footer {
                    padding: 1rem 1.5rem;
                    background: #f8f9fa;
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.75rem;
                    border-top: 1px solid #e9ecef;
                }

                .finalize-btn-cancel {
                    padding: 0.5rem 1.25rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: 1px solid #dee2e6;
                    background: white;
                    color: #6c757d;
                }

                .finalize-btn-cancel:hover {
                    background: #f8f9fa;
                    border-color: #ff0000;
                    color: #ff0000;
                    transform: translateY(-1px);
                }

                .finalize-btn-primary {
                    padding: 0.5rem 1.25rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.875rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    border: none;
                    background: linear-gradient(135deg, #28c76f 0%, #28c76f 100%);
                    color: white;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .finalize-btn-primary:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(16, 110, 0, 0.3);
                }

                .finalize-btn-primary:active {
                    transform: translateY(0);
                }

                /* Responsive */
                @media (max-width: 640px) {
                    .modal-finalize-container {
                        max-width: 95%;
                        margin: 0 10px;
                    }

                    .finalize-modal-header {
                        padding: 1rem;
                    }

                    .finalize-modal-icon {
                        width: 40px;
                        height: 40px;
                    }

                    .finalize-modal-icon i {
                        font-size: 1.25rem;
                    }

                    .finalize-modal-title h5 {
                        font-size: 1rem;
                    }

                    .finalize-modal-body {
                        padding: 1.25rem;
                    }

                    .finalize-modal-footer {
                        padding: 0.875rem 1.25rem;
                        flex-direction: column;
                    }

                    .finalize-btn-cancel,
                    .finalize-btn-primary {
                        width: 100%;
                        justify-content: center;
                    }
                }

                /* Custom styling for better visual appeal */
                .card {
                    border-radius: 1rem;
                    overflow: hidden;
                }

                /* Delete button styling */
                .btn-danger {
                    background: #dc3545;
                    border: none;
                    color: white;
                }

                .btn-danger:hover {
                    background: #c82333;
                    transform: translateY(-1px);
                    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
                }

                /* Modal danger header */
                .modal-header.bg-danger-gradient {
                    background: linear-gradient(135deg, #dc3545 0%, #000000 100%);
                }

                .table {
                    margin-bottom: 0;
                }

                .table> :not(caption)>*>* {
                    padding: 1rem 0.75rem;
                }

                .table-hover tbody tr:hover {
                    background-color: rgba(255, 0, 0, 0.02);
                    transition: all 0.2s ease;
                }

                .form-control,
                .form-select,
                .input-group-text {
                    border-radius: 0.5rem;
                    border: 1px solid #e0e0e0;
                    transition: all 0.2s ease;
                }

                .form-control:focus,
                .form-select:focus {
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

                .btn-infor {
                    background: #00cfe8;
                    border: none;
                    color: white;
                }

                .btn-infor:hover {
                    background: #00cfe8;
                    transform: translateY(-1px);
                    color: white;
                }

                .btn-info {
                    background: #17a2b8;
                    border: none;
                    color: white;
                }

                .btn-info:hover {
                    background: #138496;
                    transform: translateY(-1px);
                }

                .btn-success {
                    background: #28a745;
                    border: none;
                }

                .btn-success:hover {
                    background: #218838;
                    transform: translateY(-1px);
                }

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

                /* Status badges */
                .bg-success {
                    background-color: #28a745 !important;
                }

                .bg-warning {
                    background-color: #ffc107 !important;
                    color: #000;
                }

                .bg-info {
                    background-color: #17a2b8 !important;
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

                /* White text opacity */
                .text-white-50 {
                    color: rgba(255, 255, 255, 0.7) !important;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    .table-responsive {
                        border-radius: 0.5rem;
                    }

                    .d-flex.gap-1 {
                        flex-direction: column;
                        gap: 5px;
                    }

                    .d-flex.gap-1 .btn {
                        width: 100%;
                    }

                    .row.mb-4 {
                        flex-direction: column;
                        gap: 10px;
                    }

                    .col-md-2.text-end {
                        text-align: left !important;
                    }
                }
            </style>
        </div>
    </div>
</div>
