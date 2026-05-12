{{-- resources/views/livewire/hr/payment/add-worker-advance.blade.php --}}
<div>
    <div class="card shadow-sm border-0">
        <!-- Header -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-money-bill-wave text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">{{ $advanceId ? 'Edit Worker Advance' : 'Add New Worker Advance' }}</h3>
                        <p class="text-white-50 small mb-0">{{ $advanceId ? 'Update advance details below' : 'Record a new worker advance payment' }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hr.advances.list') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Left Column: Advance Form -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-plus-circle me-2" style="color: #ff0000;"></i>
                                New Advance Entry
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <form wire:submit.prevent="save">
                                <!-- Worker Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user-tie me-1"></i>
                                        Select Worker
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('worker_id') is-invalid @enderror" wire:model.live="worker_id">
                                        <option value="">Choose a worker...</option>
                                        @foreach($workers as $worker)
                                            <option value="{{ $worker->id }}">
                                                {{ $worker->name }} 
                                                @if($worker->designation) ({{ $worker->designation }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('worker_id') 
                                        <small class="text-danger d-block mt-1">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </small> 
                                    @enderror
                                </div>

                             <!-- Amount -->
<div class="mb-4">
    <label class="form-label fw-semibold">
        <i class="fas fa-euro-sign me-1"></i>
        Advance Amount
        <span class="text-danger">*</span>
    </label>
    <input type="number" 
           step="0.01" 
           class="form-control @error('amount') is-invalid @enderror" 
           wire:model="amount"
           placeholder="0.00">
    @error('amount') 
        <small class="text-danger d-block mt-1">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
        </small> 
    @enderror
    <small class="text-muted d-block mt-1">
        <i class="fas fa-info-circle me-1"></i>
        This amount will be ADDED to the worker's current balance
    </small>
</div>

                                <!-- Advance Date -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Advance Date
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('advance_date') is-invalid @enderror" 
                                           wire:model="advance_date"
                                           max="{{ now()->format('Y-m-d') }}">
                                    @error('advance_date') 
                                        <small class="text-danger d-block mt-1">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </small> 
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        Reason / Remarks
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              wire:model="notes"
                                              rows="3"
                                              placeholder="Enter reason for advance, e.g., Emergency, Festival, Medical, etc..."></textarea>
                                    @error('notes') 
                                        <small class="text-danger d-block mt-1">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </small> 
                                    @enderror
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('hr.advances.list') }}" class="btn btn-light">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                                        <span wire:loading.remove>
                                            <i class="fas fa-plus-circle me-2"></i> Add Advance
                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin"></i> Adding...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Worker Balance & History -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-chart-line me-2" style="color: #ff0000;"></i>
                                Worker Advance Account
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            @if($worker_id)
                                <!-- Balance Cards -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-gradient-primary text-white">
                                            <div class="card-body text-center py-3">
                                                <small class="text-white-50">Current Balance</small>
                                                <h3 class="mb-0 fw-bold text-white">€ {{ number_format($currentBalance, 2) }}</h3>
                                                <small class="text-white-50">Total pending to repay</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-gradient-info text-white">
                                            <div class="card-body text-center py-3">
                                                <small class="text-white-50">Total Advances Given</small>
                                                <h3 class="mb-0 fw-bold text-white">€ {{ number_format($totalAdvances, 2) }}</h3>
                                                <small class="text-white-50">Lifetime advances</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Advance History -->
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-history me-2 text-danger"></i>
                                        Advance History
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th class="text-end">Amount</th>
                                                    <th>Reason</th>
                                                    <th class="text-end">Balance After</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($advanceHistory as $advance)
                                                    <tr>
                                                        <td>
                                                            <small>{{ \Carbon\Carbon::parse($advance->advance_date)->format('M d, Y') }}</small>
                                                        </td>
                                                        <td class="text-end text-success fw-semibold">
                                                            + € {{ number_format($advance->amount, 2) }}
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">{{ Str::limit($advance->notes ?? 'No reason', 30) }}</small>
                                                        </td>
                                                        <td class="text-end">
                                                            <small>€ {{ number_format($advance->running_balance, 2) }}</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-3 text-muted">
                                                            No advance history for this worker
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Each advance adds to the worker's balance. Deductions during payroll will reduce the balance.
                                    </small>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                                    <p class="mb-0">Select a worker to view advance account</p>
                                    <small class="text-muted">Choose a worker from the left form</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 1rem;
            overflow: hidden;
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
        
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            padding: 0.5rem 1.25rem;
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
        
        .btn-danger {
            background: #ff0000;
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: #000000;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        @media (max-width: 768px) {
            .d-flex.gap-2 {
                flex-direction: column;
            }
            .d-flex.gap-2 .btn {
                width: 100%;
            }
        }
    </style>
</div>