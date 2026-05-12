<div>
    @php
    use Carbon\Carbon;
    use App\Models\Worker;
    @endphp

    <div class="card shadow-sm border-0">
        <!-- Header - PRESERVED ORIGINAL -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-user-clock text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Single Worker Payroll</h3>
                        <p class="text-white-50 small mb-0">Generate payroll for daily or hourly workers with calendar view & rate override</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="debugAttendance" class="btn btn-light btn-sm">
                        <i class="fas fa-bug me-2"></i> Debug
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body pt-4" style="background: white;">
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

            @if(session()->has('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Left Column: Controls -->
                <div class="col-md-4">
                    <div class="modern-card h-100">
                        <div class="modern-card-header bg-light">
                            <i class="fas fa-sliders-h me-2" style="color: #ff0000;"></i>
                            <h6 class="mb-0 fw-bold">Payroll Controls</h6>
                        </div>
                        <div class="modern-card-body">
                            <!-- Worker Selection -->
                            <div class="modern-form-group mb-4">
                                <label class="modern-label">
                                    <i class="fas fa-user me-1"></i> Select Worker <span class="text-danger">*</span>
                                </label>
                                <select class="modern-select" wire:model.live="selectedWorker">
                                    <option value="">Choose a worker...</option>
                                    @foreach($workers as $worker)
                                        <option value="{{ $worker->id }}">
                                            {{ $worker->name }} -
                                            <span class="badge {{ $worker->rate_type === 'daily' ? 'bg-info' : 'bg-success' }}">
                                                {{ ucfirst($worker->rate_type) }}
                                            </span>
                                            Rate: €{{ number_format($worker->rate, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Period Selection -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="modern-label">Month</label>
                                    <select class="modern-select" wire:model.live="selectedMonth">
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="modern-label">Year</label>
                                    <select class="modern-select" wire:model.live="selectedYear">
                                        @for($i = Carbon::now()->year - 2; $i <= Carbon::now()->year + 1; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- Payroll Type Override Section -->
                            @if($selectedWorker && $earningsData)
                                <div class="override-card-modern mb-4">
                                    <div class="override-card-header">
                                        <i class="fas fa-exchange-alt me-2" style="color: #ff0000;"></i>
                                        <h6 class="mb-0 fw-bold">Payroll Type Override</h6>
                                    </div>
                                    <div class="override-card-body">
                                        <div class="mb-3">
                                            <label class="modern-label">Calculation Type</label>
                                            <select class="modern-select" wire:model.live="payrollType">
                                                <option value="default">Default ({{ ucfirst($earningsData['worker_rate_type']) }})</option>
                                                <option value="daily">Force Daily Rate Calculation</option>
                                                <option value="hourly">Force Hourly Rate Calculation</option>
                                            </select>
                                            <small class="text-muted d-block mt-1">
                                                @if($payrollType !== 'default')
                                                    <i class="fas fa-info-circle me-1" style="color: #ff0000;"></i>
                                                    Overriding from {{ ucfirst($earningsData['worker_rate_type']) }} to {{ ucfirst($payrollType) }}
                                                @endif
                                            </small>
                                        </div>

                                        @if($showOverrideWarning)
                                            <div class="alert-modern alert-warning-modern mb-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <small>Override active! The payroll will be calculated using <strong>{{ ucfirst($payrollType) }}</strong> rates.</small>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="modern-label">
                                                Override Rate (Optional)
                                                <small class="text-muted">Leave empty to use worker's rate</small>
                                            </label>
                                            <div class="modern-input-group">
                                                <span class="modern-input-group-text">€</span>
                                                <input type="number" step="0.01" class="modern-input"
                                                       wire:model.live="overrideRate"
                                                       placeholder="Custom rate">
                                                <span class="modern-input-group-text">
                                                    / {{ $payrollType === 'daily' ? 'day' : ($payrollType === 'hourly' ? 'hour' : 'unit') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Overtime Multiplier -->
                            @if($earningsData && ($payrollType === 'hourly' || ($payrollType === 'default' && $earningsData['worker_rate_type'] === 'hourly')))
                                <div class="modern-form-group mb-4">
                                    <label class="modern-label">
                                        <i class="fas fa-clock me-1"></i> Overtime Multiplier
                                    </label>
                                    <div class="modern-input-group">
                                        <input type="number" step="0.1" min="1" max="3"
                                               class="modern-input" wire:model.live="overtimeMultiplier"
                                               placeholder="Default 1.5x">
                                        <span class="modern-input-group-text">x</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Notes -->
                            <div class="modern-form-group mb-4">
                                <label class="modern-label">
                                    <i class="fas fa-sticky-note me-1"></i> Notes
                                </label>
                                <textarea class="modern-textarea" rows="2" wire:model="customNotes"
                                          placeholder="Add notes for this payroll..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <button class="modern-btn-primary" wire:click="generatePayroll"
                                        wire:loading.attr="disabled" @if(!$selectedWorker) disabled @endif>
                                    <span wire:loading.remove><i class="fas fa-calculator me-2"></i> Generate Payroll</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i> Generating...</span>
                                </button>

                                @if($existingPayroll && $existingPayroll->status === 'draft')
                                    <button class="modern-btn-warning" wire:click="updatePayroll">
                                        <i class="fas fa-edit me-2"></i> Update Payroll
                                    </button>
                                @endif
                            </div>

                            @if($existingPayroll)
                                <div class="info-card-modern mt-4">
                                    <i class="fas fa-info-circle me-2" style="color: #ff0000;"></i>
                                    <strong>Existing Payroll:</strong> {{ ucfirst($existingPayroll->status) }}
                                    @if($existingPayroll->payrolls->first())
                                        <br><small>Net: €{{ number_format($existingPayroll->payrolls->first()->net_amount, 2) }}</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Calendar View -->
                <div class="col-md-8">
                    <div class="modern-card h-100">
                        <div class="modern-card-header bg-light">
                            <i class="fas fa-calendar-alt me-2" style="color: #ff0000;"></i>
                            <h6 class="mb-0 fw-bold">Attendance Calendar - {{ $months[$selectedMonth] }} {{ $selectedYear }}</h6>
                        </div>
                        <div class="modern-card-body p-0">
                            @if($selectedWorker)
                                @include('livewire.hr.payroll.partials.calendar-view')
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                                    <p class="mb-0">Select a worker to view attendance calendar</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Width Summary Card Below -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="modern-card">
                        <div class="modern-card-header bg-light">
                            <i class="fas fa-chart-line me-2" style="color: #ff0000;"></i>
                            <h6 class="mb-0 fw-bold">Payroll Summary - {{ $months[$selectedMonth] }} {{ $selectedYear }}</h6>
                        </div>
                        <div class="modern-card-body">
                            @if($selectedWorker)
                                @include('livewire.hr.payroll.partials.summary-view')
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-user-circle fa-4x text-muted mb-3"></i>
                                    <p class="mb-0">Select a worker to view payroll summary</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern CSS Styles (White background, Red/Black accents) -->
    <style>
        /* Modern Card Styles */
        .modern-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .modern-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

     .modern-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f0f0f0;
    display: flex;           /* ← Add this */
    align-items: center;     /* ← Add this */
    gap: 0.5rem;            /* ← Add this (optional, for spacing) */
}
/* Make all card headers display inline */
.modern-card-header,
.override-card-header,
.card-header-modern {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.modern-card-header i,
.modern-card-header h6,
.override-card-header i,
.override-card-header h6,
.card-header-modern i,
.card-header-modern h6 {
    display: inline-block !important;
    margin: 0 !important;
}
        .modern-card-body {
            padding: 1.25rem;
        }

        /* Form Elements */
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

        .modern-select, .modern-input, .modern-textarea {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: white;
        }

        .modern-select:focus, .modern-input:focus, .modern-textarea:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
            outline: none;
        }

        .modern-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .modern-input-group {
            display: flex;
            align-items: stretch;
        }

        .modern-input-group .modern-input {
            border-radius: 0;
        }

        .modern-input-group .modern-input:first-child {
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
        }

        .modern-input-group .modern-input:last-child {
            border-top-right-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
        }

        .modern-input-group-text {
            display: flex;
            align-items: center;
            padding: 0.625rem 0.875rem;
            background: #f8f9fa;
            border: 2px solid #e5e7eb;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
        }

        /* Buttons */
        .modern-btn-primary {
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            border: none;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: white;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
        }

        .modern-btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        .modern-btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .modern-btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            border: none;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            color: #333;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
        }

        .modern-btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        /* Override Card */
        .override-card-modern {
            border: 0.5px solid #ff0000;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .override-card-header {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #ffcccc;
        }

        .override-card-body {
            padding: 1rem;
        }

        /* Alert Modern */
        .alert-modern {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .alert-warning-modern {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 3px solid #ffc107;
            color: #856404;
        }

        /* Info Card */
        .info-card-modern {
            background: #f8f9fa;
            border-left: 3px solid #ff0000;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }

        /* Focus Styles */
        .modern-select:focus, 
        .modern-input:focus, 
        .modern-textarea:focus,
        button:focus {
            border-color: #ff0000 !important;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1) !important;
            outline: none !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-card-body {
                padding: 1rem;
            }
        }
         .modal-modern-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    /* Modal Container */
    .modal-modern-container {
        position: relative;
        width: 100%;
        max-width: 650px;
        margin: 0 20px;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modal Content */
    .modern-modal-content {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Modal Header */
    .modern-modal-header {
        background: linear-gradient(135deg, #28c76f 70%, #ffffff 100%);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .modern-modal-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .modern-modal-icon i {
        font-size: 1.5rem;
    }

    .modern-modal-title {
        flex: 1;
        color: white;
    }

    .modern-modal-title h5 {
        font-size: 1.125rem;
        margin-bottom: 0.25rem;
    }

    .modern-modal-title small {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .modern-modal-close {
        background: rgba(0, 0, 0, 0.2);
        border: none;
        width: 36px;
        height: 36px;
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
    }

    /* Modal Body */
    .modern-modal-body {
        padding: 1.5rem;
    }

    /* Period Info */
    .period-info-modern {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 3px solid #28c76f;
        font-size: 0.875rem;
    }

    /* Stats Grid - 2x2 layout */
    .modal-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .modal-stat-card {
        background: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .modal-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .modal-stat-card.highlight {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
        border: 1px solid #ffcccc;
    }

    .modal-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-stat-icon i {
        font-size: 1.25rem;
    }

    .modal-stat-info {
        flex: 1;
    }

    .modal-stat-label {
        display: block;
        font-size: 0.7rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .modal-stat-value {
        display: block;
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
    }

    .modal-stat-value.net {
        color: #ff0000;
        font-size: 1.25rem;
    }

    /* Modal Footer */
    .modern-modal-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        border-top: 1px solid #f0f0f0;
    }

    .modal-btn-primary,
    .modal-btn-secondary {
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
    }

    .modal-btn-primary {
        background: linear-gradient(135deg, #28c76f 0%, #28c76f 100%);
        color: white;
    }

    .modal-btn-primary:hover {
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
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

    /* Responsive */
    @media (max-width: 640px) {
        .modal-modern-container {
            max-width: 90%;
            margin: 0 15px;
        }
        
        .modal-stats-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .modern-modal-body {
            padding: 1rem;
        }
        
        .modern-modal-footer {
            flex-direction: column;
        }
        
        .modal-btn-primary,
        .modal-btn-secondary {
            justify-content: center;
        }
        
        .modern-modal-header {
            padding: 1rem;
        }
        
        .modern-modal-icon {
            width: 40px;
            height: 40px;
        }
        
        .modern-modal-icon i {
            font-size: 1.25rem;
        }
    }
    </style>

    @if($generatedBatch && $showConfirmation)
        <div class="modal-modern-overlay">
            <div class="modal-modern-container">
                <div class="modern-modal-content">
                    <!-- Modal Header -->
                    <div class="modern-modal-header">
                        <div class="modern-modal-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="modern-modal-title">
                            <h5 class="mb-0 text-white fw-bold">Payroll Generated!</h5>
                            <small>Batch ID: #{{ $generatedBatch->id }}</small>
                        </div>
                        <button type="button" class="modern-modal-close" wire:click="$set('showConfirmation', false)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modern-modal-body">
                        @if($generatedBatch->payrolls->first())
                            @php $payroll = $generatedBatch->payrolls->first(); @endphp
                            
                            <!-- Period Info -->
                            <div class="period-info-modern">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <strong>Period:</strong> {{ Carbon::parse($generatedBatch->period_start)->format('M d, Y') }} - {{ Carbon::parse($generatedBatch->period_end)->format('M d, Y') }}
                            </div>

                            <!-- Stats Grid - Only using available data from original -->
                            <div class="modal-stats-grid">
                                <div class="modal-stat-card">
                                    <div class="modal-stat-icon" style="background: #e3f2fd;">
                                        <i class="fas fa-clock" style="color: #1976d2;"></i>
                                    </div>
                                    <div class="modal-stat-info">
                                        <span class="modal-stat-label">Total Hours</span>
                                        <strong class="modal-stat-value">{{ number_format($payroll->total_hours, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="modal-stat-card">
                                    <div class="modal-stat-icon" style="background: #e8f5e9;">
                                        <i class="fas fa-chart-line" style="color: #388e3c;"></i>
                                    </div>
                                    <div class="modal-stat-info">
                                        <span class="modal-stat-label">Gross Amount</span>
                                        <strong class="modal-stat-value">€{{ number_format($payroll->gross_amount, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="modal-stat-card">
                                    <div class="modal-stat-icon" style="background: #fff3e0;">
                                        <i class="fas fa-hand-holding-usd" style="color: #f57c00;"></i>
                                    </div>
                                    <div class="modal-stat-info">
                                        <span class="modal-stat-label">Advance Deduction</span>
                                        <strong class="modal-stat-value">€{{ number_format($payroll->advance_deduction, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="modal-stat-card highlight">
                                    <div class="modal-stat-icon" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);">
                                        <i class="fas fa-wallet" style="color: white;"></i>
                                    </div>
                                    <div class="modal-stat-info">
                                        <span class="modal-stat-label">Net Payable</span>
                                        <strong class="modal-stat-value net">€{{ number_format($payroll->net_amount, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="modern-modal-footer">
                        <button class="modal-btn-secondary" wire:click="$set('showConfirmation', false)">
                            Close
                        </button>
                        <a href="{{ route('payroll.history') }}" class="modal-btn-primary">
                            View All Payrolls
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>