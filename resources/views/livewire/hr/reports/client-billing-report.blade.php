<div>
    <div>
        <div>
            <div class="card shadow-sm border-0">
                <!-- Header with Red-Black Gradient Background -->
                <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-file-invoice-dollar text-white fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold text-white">Client Billing Report</h3>
                                <p class="text-white-50 small mb-0">Track hours to bill companies based on Daily or Hourly rates</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="">
                    @if(session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('message') }}
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

                    <!-- Summary Cards -->
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Daily Basis Calls</small>
                                            <h4 class="mb-0 fw-bold text-white">{{ number_format($totalDailyCalls) }}</h4>
                                        </div>
                                        <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Hourly Basis Calls</small>
                                            <h4 class="mb-0 fw-bold text-white">{{ number_format($totalHourlyCalls) }}</h4>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Total Client Hours</small>
                                            <h4 class="mb-0 fw-bold text-white">{{ number_format($totalClientHours, 1) }}</h4>
                                        </div>
                                        <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="card-body py-3 text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-white-50">Projects / Workers</small>
                                            <h4 class="mb-0 fw-bold text-white">{{ $totalProjects }} / {{ $totalWorkers }}</h4>
                                        </div>
                                        <i class="fas fa-building fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filters Section -->
                    <div class="card-body">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-filter me-2" style="color: #ff0000;"></i>
                                        Filter Report
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                                        <i class="fas fa-undo-alt me-1"></i> Reset All Filters
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 pt-3">
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt me-1" style="color: #ff0000;"></i>
                                            Month
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-calendar-month"></i>
                                            </span>
                                            <select class="form-select" wire:model.live="tempMonth">
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}">{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-year me-1" style="color: #ff0000;"></i>
                                            Year
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <select class="form-select" wire:model.live="tempYear">
                                                @for($i = Carbon\Carbon::now()->year - 2; $i <= Carbon\Carbon::now()->year + 1; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-project-diagram me-1" style="color: #ff0000;"></i>
                                            Project
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <select class="form-select" wire:model.live="tempProjectId">
                                                <option value="">All Projects</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-user me-1" style="color: #ff0000;"></i>
                                            Worker
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-user-circle"></i>
                                            </span>
                                            <select class="form-select" wire:model.live="tempWorkerId">
                                                <option value="">All Workers</option>
                                                @foreach($workers as $worker)
                                                    <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-tag me-1" style="color: #ff0000;"></i>
                                            Billing Type
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-receipt"></i>
                                            </span>
                                            <select class="form-select" wire:model.live="tempBillingType">
                                                <option value="">All Types</option>
                                                <option value="daily">Daily (Fixed)</option>
                                                <option value="hourly">Hourly (Actual)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-primary" wire:click="applyFilters" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none;">
                                                <i class="fas fa-search me-2"></i> Search
                                            </button>
                                            <button type="button" class="btn btn-secondary" wire:click="clearAllTempFilters">
                                                <i class="fas fa-times me-2"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Filters Display -->
                                @if($selectedProjectId || $selectedWorkerId || $selectedBillingType || ($selectedMonth != $tempMonth) || ($selectedYear != $tempYear))
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <small class="text-muted me-2">
                                                <i class="fas fa-filter me-1"></i>Active filters:
                                            </small>
                                            @if($selectedMonth != $tempMonth)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    Month: {{ date('F', mktime(0,0,0,$selectedMonth,1)) }}
                                                </span>
                                            @endif
                                            @if($selectedYear != $tempYear)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-calendar-year me-1"></i>
                                                    Year: {{ $selectedYear }}
                                                </span>
                                            @endif
                                            @if($selectedProjectId)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-project-diagram me-1"></i>
                                                    Project: {{ $selectedProjectName }}
                                                </span>
                                            @endif
                                            @if($selectedWorkerId)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-user me-1"></i>
                                                    Worker: {{ $selectedWorkerName }}
                                                </span>
                                            @endif
                                            @if($selectedBillingType)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-tag me-1"></i>
                                                    Billing Type: {{ $selectedBillingType == 'daily' ? 'Daily (Fixed)' : 'Hourly (Actual)' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Report Table -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-sec">
                                    <tr>
                                        <th class="py-3"><i class="fas fa-calendar-day me-2"></i> Date</th>
                                        <th class="py-3"><i class="fas fa-project-diagram me-2"></i> Project</th>
                                        <th class="py-3"><i class="fas fa-user me-2"></i> Worker</th>
                                        <th class="py-3 text-center"><i class="fas fa-stopwatch me-2"></i> Worker Hours</th>
                                        <th class="py-3 text-center"><i class="fas fa-tag me-2"></i> Billing Type</th>
                                        <th class="py-3 text-center"><i class="fas fa-chart-line me-2"></i> Client Hours</th>
                                        <th class="py-3"><i class="fas fa-notes-medical me-2"></i> Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $attendance)
                                        <tr class="border-bottom">
                                            <td>
                                                <div class="fw-bold">{{ Carbon\Carbon::parse($attendance->date)->format('d M, Y') }}</div>
                                            </td>
                                            <td>
                                                <strong>{{ $attendance->project->name }}</strong><br>
                                                <small class="text-muted">{{ $attendance->project->project_code }}</small>
                                            </td>
                                            <td>
                                                {{ $attendance->worker->name }}<br>
                                                <small class="text-muted">{{ $attendance->worker->designation ?? 'No designation' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold">{{ number_format($attendance->hours_worked, 1) }} hrs</span>
                                                @if($attendance->overtime_hours > 0)
                                                    <br><small class="text-warning">+{{ number_format($attendance->overtime_hours, 1) }} OT</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($attendance->client_billing_type == 'daily')
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="fas fa-calendar-day me-1"></i> Daily (Fixed)
                                                    </span>
                                                @else
                                                    <span class="badge bg-info px-3 py-2">
                                                        <i class="fas fa-clock me-1"></i> Hourly
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <strong class="text-primary">{{ number_format($attendance->client_hours, 1) }} hrs</strong>
                                                @if($attendance->client_billing_type == 'daily')
                                                    <br><small class="text-muted">Fixed {{ $attendance->status == 'present' ? '9' : ($attendance->status == 'half_day' ? '4.5' : '0') }}h</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($attendance->notes, 40) ?: '-' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No attendance records found for this period</p>
                                                    <small>Try adjusting your filters</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td>TOTAL</td>
                                        <td colspan="2"></td>
                                        <td class="text-center">{{ number_format($attendances->sum('hours_worked'), 1) }} hrs</td>
                                        <td class="text-center">--</td>
                                        <td class="text-center text-danger">{{ number_format($attendances->sum('client_hours'), 1) }} hrs</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination with Info -->
                    @if($attendances->total() > 0)
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} 
                                of {{ $attendances->total() }} records
                            </div>
                            <div>
                                {{ $attendances->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom styling for better visual appeal */
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
        
        .btn-outline-secondary:hover {
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