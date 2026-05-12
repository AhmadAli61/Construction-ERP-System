<div>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Real-Time Payroll Dashboard
                        </h4>
                        <small>View real-time earnings and generate payroll instantly</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Column: Controls -->
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-sliders-h me-2"></i>
                            Payroll Controls
                        </h5>

                        <!-- Worker Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>
                                Select Worker
                            </label>
                            <select class="form-select" wire:model.live="selectedWorker">
                                <option value="">Choose a worker...</option>
                                @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}">
                                        {{ $worker->name }} - {{ ucfirst($worker->rate_type) }}: €{{ number_format($worker->rate, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Overtime Multiplier -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-clock me-1"></i>
                                Overtime Multiplier
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       step="0.1"
                                       min="1"
                                       max="3"
                                       wire:model.live="overtimeMultiplier">
                                <span class="input-group-text">x</span>
                            </div>
                            <small class="text-muted">Example: 1.5 = Time and a half, 2 = Double time</small>
                        </div>

                        <!-- Generate Payroll Button -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg" 
                                    wire:click="generatePayroll"
                                    wire:loading.attr="disabled">
                                <i class="fas fa-calculator me-2"></i>
                                <span wire:loading.remove>Generate Payroll for {{ date('F Y') }}</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Generating...
                                </span>
                            </button>
                        </div>

                        <!-- Current Batch Status -->
                        @if($currentBatch)
                            <div class="alert alert-info mt-4 mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Current Batch:</strong> {{ $currentBatch->month_name }} {{ $currentBatch->year }}
                                        <br>
                                        <small>Status: {{ ucfirst($currentBatch->status) }}</small>
                                    </div>
                                    @if($currentBatch->status === 'draft')
                                        <button class="btn btn-success btn-sm" 
                                                wire:click="finalizeBatch({{ $currentBatch->id }})">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Finalize Batch
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Real-Time Earnings -->
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-chart-line me-2"></i>
                            Real-Time Earnings
                        </h5>

                        @if($selectedWorker && $earningsData)
                            <div class="mb-4">
                                <h6 class="text-muted">From {{ $earningsData['from_date']->format('M d, Y') }} to {{ $earningsData['to_date']->format('M d, Y') }}</h6>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body text-center">
                                            <small class="text-muted">Total Hours Worked</small>
                                            <h3 class="mb-0">{{ number_format($earningsData['total_hours'], 2) }}</h3>
                                            <small>{{ $earningsData['regular_hours'] }} regular + {{ $earningsData['overtime_hours'] }} overtime</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body text-center">
                                            <small class="text-muted">Attendance Records</small>
                                            <h3 class="mb-0">{{ $earningsData['attendance_count'] }}</h3>
                                            <small>Days worked</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-gradient-success text-white">
                                        <div class="card-body">
                                            <small class="text-white-50">Regular Pay</small>
                                            <h4 class="mb-0">€ {{ number_format($earningsData['regular_pay'], 2) }}</h4>
                                            <small>{{ number_format($earningsData['regular_hours'], 2) }} hrs × €{{ number_format($earningsData['gross_earnings'] / ($earningsData['total_hours'] ?: 1), 2) }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-gradient-warning text-white">
                                        <div class="card-body">
                                            <small class="text-white-50">Overtime Pay</small>
                                            <h4 class="mb-0">€ {{ number_format($earningsData['overtime_pay'], 2) }}</h4>
                                            <small>{{ number_format($earningsData['overtime_hours'], 2) }} hrs × €{{ number_format($earningsData['gross_earnings'] / ($earningsData['total_hours'] ?: 1), 2) }} × {{ $overtimeMultiplier }}x</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-gradient-info text-white">
                                        <div class="card-body">
                                            <small class="text-white-50">Gross Earnings</small>
                                            <h4 class="mb-0">€ {{ number_format($earningsData['gross_earnings'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-gradient-danger text-white">
                                        <div class="card-body">
                                            <small class="text-white-50">Pending Advances</small>
                                            <h4 class="mb-0">€ {{ number_format($earningsData['pending_advances'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 bg-gradient-primary text-white">
                                <div class="card-body text-center">
                                    <small class="text-white-50">Net Payable (After Advances)</small>
                                    <h3 class="mb-0">€ {{ number_format($earningsData['net_earnings'], 2) }}</h3>
                                </div>
                            </div>

                            <!-- Project Breakdown -->
                            @if($earningsData['attendance_count'] > 0)
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-project-diagram me-2"></i>
                                        Project Breakdown
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Project</th>
                                                    <th>Days</th>
                                                    <th>Hours</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($earningsData['attendance'] ?? [] as $attendance)
                                                    <tr>
                                                        <td>{{ $attendance->project->name }}</td>
                                                        <td>1</td>
                                                        <td>{{ number_format($attendance->hours_worked, 2) }}</td>
                                                        <td class="text-end">€ {{ number_format($attendance->hours_worked * $earningsData['gross_earnings'] / ($earningsData['total_hours'] ?: 1), 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @elseif($selectedWorker)
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                <p>No attendance records found for this period</p>
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-user-circle fa-3x mb-3"></i>
                                <p>Select a worker to view real-time earnings</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payroll Batches -->
        @if($payrollBatches->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Recent Payroll Batches
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Period</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Workers</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payrollBatches->take(5) as $batch)
                                            <tr>
                                                <td>
                                                    <strong>{{ $batch->month_name }} {{ $batch->year }}</strong>
                                                </td>
                                                <td>{{ $batch->period_start->format('M d, Y') }}</td>
                                                <td>{{ $batch->period_end->format('M d, Y') }}</td>
                                                <td>{{ $batch->payrolls->count() }}</td>
                                                <td>€ {{ number_format($batch->payrolls->sum('net_amount'), 2) }}</td>
                                                <td>
                                                    @if($batch->status === 'finalized')
                                                        <span class="badge bg-success">Finalized</span>
                                                    @else
                                                        <span class="badge bg-warning">Draft</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('payroll.history', ['batch' => $batch->id]) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .bg-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        .card {
            border-radius: 1rem;
        }
        .btn {
            border-radius: 0.5rem;
        }
    </style>
</div>