@php
    use Carbon\Carbon;
@endphp

<div>
    @if($earningsData)
        <!-- Worker Info Banner -->
        <div class="worker-banner-modern mb-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-3">
                        <div class="worker-avatar">
                            <i class="fas fa-user-tie fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $earningsData['worker']->name }}</h4>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge-modern badge-info">
                                    <i class="fas fa-tag me-1"></i>
                                    {{ ucfirst($earningsData['worker_rate_type']) }}
                                </span>
                                @if($earningsData['worker_rate_type'] != $earningsData['calculation_type'])
                                    <span class="badge-modern badge-warning">
                                        <i class="fas fa-exchange-alt me-1"></i>
                                        Paying as: {{ ucfirst($earningsData['calculation_type']) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    @if($payrollType !== 'default')
                        <span class="badge-modern badge-warning">
                            <i class="fas fa-exchange-alt me-1"></i>
                            Override Active: {{ ucfirst($payrollType) }}
                        </span>
                    @endif
                    @if($overrideRate)
                        <span class="badge-modern badge-primary ms-2">
                            <i class="fas fa-tag me-1"></i>
                            Custom Rate: €{{ number_format($overrideRate, 2) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #7367f0 0%, #7367f0 100%);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <small class="text-muted">Days Worked</small>
                        <h3 class="mb-0 fw-bold">{{ $earningsData['attendance_count'] }}</h3>
                        @if($earningsData['calculation_type'] === 'daily')
                            <small>{{ $earningsData['full_days'] }} full, {{ $earningsData['half_days'] }} half</small>
                        @else
                            <small>{{ number_format($earningsData['total_hours'], 1) }} total hours</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #00cfe8 0%, #00cfe8 100%);">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <small class="text-muted">{{ $earningsData['rate_label'] }}</small>
                        <h3 class="mb-0 fw-bold">€{{ number_format($earningsData['calculated_rate'], 2) }}<small class="fs-6">/{{ $earningsData['rate_unit'] }}</small></h3>
                        @if($earningsData['calculation_type'] === 'daily')
                            <small>Half day: €{{ number_format($earningsData['calculated_rate'] / 2, 2) }}</small>
                        @else
                            <small>Daily (9h): €{{ number_format($earningsData['calculated_rate'] * 9, 2) }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #28a745 0%, #28a745 100%);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <small class="text-muted">Gross Earnings</small>
                        <h3 class="mb-0 fw-bold text-success">€{{ number_format($earningsData['gross_earnings'], 2) }}</h3>
                        <small>Before deductions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ffc107 0%, #ffc107 100%);">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-content">
                        <small class="text-muted">Pending Advances</small>
                        <h3 class="mb-0 fw-bold">€{{ number_format($earningsData['total_pending_advances'], 2) }}</h3>
                        @if($deductAdvance && $advanceDeductionAmount > 0)
                            <small class="text-success">Deducting: €{{ number_format($advanceDeductionAmount, 2) }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculation Section -->
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <i class="fas fa-calculator me-2" style="color: #ff0000;"></i>
                <h6 class="mb-0 fw-semibold">Payroll Calculation - {{ ucfirst($earningsData['calculation_type']) }} Rate Method</h6>
            </div>
            <div class="modern-card-body">
                @if($earningsData['calculation_type'] === 'daily')
                    <div class="calculation-formula mb-3">
                        <div class="alert-info-modern">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Formula:</strong> (Full Days × Daily Rate) + (Half Days × Half Day Rate)
                        </div>
                    </div>
                    <div class="calculation-items">
                        <div class="calculation-item">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>Full Days:</strong>
                                    <span class="badge-success-modern ms-2">{{ $earningsData['full_days'] }} days</span>
                                </div>
                                <div class="text-end">
                                    × €{{ number_format($earningsData['calculated_rate'], 2) }}
                                    = <strong class="text-success">€{{ number_format($earningsData['full_days'] * $earningsData['calculated_rate'], 2) }}</strong>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>Half Days:</strong>
                                    <span class="badge-warning-modern ms-2">{{ $earningsData['half_days'] }} days</span>
                                </div>
                                <div class="text-end">
                                    × €{{ number_format($earningsData['calculated_rate'] / 2, 2) }}
                                    = <strong class="text-success">€{{ number_format($earningsData['half_days'] * ($earningsData['calculated_rate'] / 2), 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="calculation-formula mb-3">
                        <div class="alert-info-modern">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Formula:</strong> (Regular Hours × Hourly Rate) + (Overtime Hours × Hourly Rate × Multiplier)
                        </div>
                    </div>
                    <div class="calculation-items">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>Regular Hours:</strong>
                                <span class="badge-success-modern ms-2">{{ number_format($earningsData['regular_hours'], 2) }} hrs</span>
                            </div>
                            <div class="text-end">
                                × €{{ number_format($earningsData['calculated_rate'], 2) }}
                                = <strong class="text-success">€{{ number_format($earningsData['regular_pay'], 2) }}</strong>
                            </div>
                        </div>
                        @if($earningsData['overtime_hours'] > 0)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>Overtime Hours:</strong>
                                    <span class="badge-warning-modern ms-2">{{ number_format($earningsData['overtime_hours'], 2) }} hrs</span>
                                </div>
                                <div class="text-end">
                                    × €{{ number_format($earningsData['calculated_rate'], 2) }}
                                    × {{ $earningsData['overtime_multiplier_used'] }}x
                                    = <strong class="text-success">€{{ number_format($earningsData['overtime_pay'], 2) }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="calculation-total mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="fs-5">Gross Earnings:</strong>
                        <strong class="fs-4 text-success">€{{ number_format($earningsData['gross_earnings'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advance Deduction Section -->
        @if($earningsData['total_pending_advances'] > 0)
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <i class="fas fa-hand-holding-usd me-2" style="color: #ff0000;"></i>
                <h6 class="mb-0 fw-semibold">Advance Deduction</h6>
            </div>
            <div class="modern-card-body">
                <div class="alert-info-modern mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Total Pending Advances:</strong> 
                            €{{ number_format($earningsData['total_pending_advances'], 2) }}
                        </span>
                        <span class="text-muted">
                            {{ count($earningsData['pending_advances_list']) }} pending advance(s)
                        </span>
                    </div>
                </div>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input modern-switch" type="checkbox" 
                           id="deductAdvanceSwitch" 
                           wire:model.live="deductAdvance">
                    <label class="form-check-label fw-semibold ms-2" for="deductAdvanceSwitch">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        Deduct Advance Amount from Payroll
                    </label>
                </div>
                
                @if($deductAdvance)
                <div class="row g-3 align-items-stretch">
    
    <!-- Left Column -->
    <div class="col-md-6 d-flex flex-column">
        <label class="modern-label">
            <i class="fas fa-euro-sign me-1"></i>
            Advance Amount to Deduct
        </label>

        <div class="modern-input-group flex-grow-1">
            <span class="modern-input-group-text">€</span>
            <input type="number" 
                   step="0.01" 
                   class="modern-input" 
                   wire:model.live="advanceDeductionAmount"
                   min="0"
                   max="{{ $earningsData['total_pending_advances'] }}">
            <button class="btn-max" 
                    wire:click="$set('advanceDeductionAmount', {{ $earningsData['total_pending_advances'] }})">
                Max
            </button>
        </div>

        <small class="text-muted mt-1">
            Maximum: €{{ number_format($earningsData['total_pending_advances'], 2) }}
        </small>
    </div>

    <!-- Right Column -->
    <div class="col-md-6 d-flex flex-column">
        <!-- Invisible label to match height -->
        <label class="modern-label invisible">Hidden</label>

<div class="alert-warning-modern d-flex align-items-center h-100">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <small>
                This will deduct €{{ number_format($advanceDeductionAmount, 2) }} 
                from the payroll and mark the corresponding advances as paid.
            </small>
        </div>
    </div>

</div>

                
                @if($advanceDeductionAmount > 0)
                <div class="advances-list mt-3">
                    <small class="text-muted">Advances to be deducted:</small>
                    <div class="mt-2">
                        @php
                            $remaining = $advanceDeductionAmount;
                        @endphp
                        @foreach($earningsData['pending_advances_list'] as $advance)
                            @if($remaining <= 0) @break @endif
                            @php
                                $deductFromThis = min($advance->remaining_amount, $remaining);
                                $remaining -= $deductFromThis;
                            @endphp
                            <div class="advance-item">
                                <i class="fas fa-receipt text-warning me-2"></i>
                                <strong>€{{ number_format($deductFromThis, 2) }}</strong> - 
                                Advance from {{ Carbon::parse($advance->advance_date)->format('M d, Y') }}
                                @if($advance->notes) <small class="text-muted">({{ Str::limit($advance->notes, 40) }})</small> @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>
        @endif

        <!-- Net Payable Card -->
        <div class="net-payable-card mb-4">
            <div class="net-payable-content">
                <div class="net-payable-label">
                    <i class="fas fa-wallet me-2"></i>
                    Net Payable (After All Deductions)
                </div>
                <div class="net-payable-amount">
                    € {{ number_format($earningsData['net_earnings'], 2) }}
                </div>
                <div class="net-payable-note">
                    This amount will be paid to the worker
                    @if($deductAdvance && $advanceDeductionAmount > 0)
                        <br><small>✓ Advance deduction of €{{ number_format($advanceDeductionAmount, 2) }} applied</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Manual Adjustment -->
        <div class="modern-card mb-4">
            <div class="modern-card-header">
                <i class="fas fa-sliders-h me-2" style="color: #ff0000;"></i>
                <h6 class="mb-0 fw-semibold">Manual Adjustment</h6>
            </div>
            <div class="modern-card-body">
                <div class="modern-input-group">
                    <span class="modern-input-group-text">€</span>
                    <input type="number" step="0.01" class="modern-input" 
                           wire:model="manualAdjustment" 
                           placeholder="0.00">
                    <span class="modern-input-group-text text-muted">Adjust net amount</span>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    Positive values increase net pay, negative values decrease it
                </small>
            </div>
        </div>

        <!-- Project Breakdown -->
        @if(count($earningsData['project_breakdown']) > 0)
            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <i class="fas fa-project-diagram me-2" style="color: #ff0000;"></i>
                    <h6 class="mb-0 fw-semibold">Project Cost Distribution</h6>
                </div>
                <div class="modern-card-body p-0">
                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th class="text-center">Days</th>
                                    <th class="text-center">Hours</th>
                                    <th class="text-end">Amount (€)</th>
                                    <th class="text-center">Allocation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($earningsData['project_breakdown'] as $breakdown)
                                    @php
                                        $percentage = $earningsData['gross_earnings'] > 0
                                            ? ($breakdown['amount'] / $earningsData['gross_earnings']) * 100
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">
                                            <i class="fas fa-building me-2 text-secondary"></i>
                                            {{ $breakdown['project']->name }}
                                        </td>
                                        <td class="text-center">{{ $breakdown['days'] }}</td>
                                        <td class="text-center">{{ number_format($breakdown['hours'], 1) }}</td>
                                        <td class="text-end text-success fw-semibold">
                                            €{{ number_format($breakdown['amount'], 2) }}
                                        </td>
                                        <td class="text-center">
                                            <div class="progress-bar-modern">
                                                <div class="progress-fill" style="width: {{ $percentage }}%; background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);"></div>
                                                <span class="progress-label">{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-footer">
                                    <td class="fw-bold">TOTAL</td>
                                    <td class="text-center fw-bold">{{ collect($earningsData['project_breakdown'])->sum('days') }}</td>
                                    <td class="text-center fw-bold">{{ number_format($earningsData['total_hours'], 1) }}</td>
                                    <td class="text-end fw-bold" style="color: #ff0000;">€{{ number_format($earningsData['gross_earnings'], 2) }}</td>
                                    <td class="text-center fw-bold">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Advances List with Remove Option -->
        @if($earningsData['total_pending_advances'] > 0 && count($earningsData['pending_advances_list']) > 0 && $existingPayroll && $existingPayroll->payrolls->first())
            <div class="modern-card">
                <div class="modern-card-header">
                    <i class="fas fa-hand-holding-usd me-2" style="color: #ff0000;"></i>
                    <h6 class="mb-0 fw-semibold">Pending Advances in Payroll</h6>
                </div>
                <div class="modern-card-body">
                    <small class="text-muted d-block mb-3">Click remove to exclude advance from payroll</small>
                    @foreach($earningsData['pending_advances_list'] as $advance)
                        <div class="advance-remove-item">
                            <div class="advance-info">
                                <strong>€ {{ number_format($advance->remaining_amount, 2) }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ Carbon::parse($advance->advance_date)->format('M d, Y') }}
                                    @if($advance->notes) - {{ $advance->notes }} @endif
                                </small>
                            </div>
                            <button class="btn-remove"
                                    wire:click="removeAdvance({{ $advance->id }})"
                                    onclick="return confirm('Remove this advance from payroll?')">
                                <i class="fas fa-trash-alt me-1"></i> Remove
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
            <p class="mb-0">No attendance records found for this period</p>
            <small class="text-muted">Please check if attendance has been recorded for this worker</small>
        </div>
    @endif
</div>

<style>
/* Summary View Modern Styles */
.worker-banner-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 1rem;
    padding: 1.25rem;
    border: 1px solid #f0f0f0;
    border-left: 2px solid #ff0000;
}

.worker-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.badge-modern {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-info {
    background: #e3f2fd;
    color: #1976d2;
}

.badge-warning {
    background: #fff3cd;
    color: #856404;
}

.badge-primary {
    background: #d1ecf1;
    color: #0c5460;
}

.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.stat-content {
    flex: 1;
}

.modern-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
}

.modern-card-header {
    background: #fafafa;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f0f0f0;
}

.modern-card-body {
    padding: 1.25rem;
}

.alert-info-modern {
    background: #e7f3ff;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    color: #004085;
    border-left: 3px solid #17a2b8;
}

.alert-warning-modern {
    background: #fff3cd;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    color: #856404;
    border-left: 3px solid #ffc107;
}

.badge-success-modern {
    background: #d4edda;
    color: #155724;
    padding: 0.125rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.badge-warning-modern {
    background: #fff3cd;
    color: #856404;
    padding: 0.125rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.modern-switch {
    width: 3rem;
    height: 1.5rem;
}

.btn-max {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.375rem 0.75rem;
    border-radius: 0 0.5rem 0.5rem 0;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.3s ease;
}

.btn-max:hover {
    background: #5a6268;
}

.advance-item {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
}

.net-payable-card {
    background: linear-gradient(135deg, #ff0000 0%, #000000 100%);
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.net-payable-content {
    padding: 1.5rem;
    text-align: center;
    color: white;
}

.net-payable-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.net-payable-amount {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.net-payable-note {
    font-size: 0.75rem;
    opacity: 0.8;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table th,
.modern-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e9ecef;
}

.modern-table thead th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
}

.modern-table tfoot td {
    background: #f8f9fa;
    font-weight: 600;
}

.progress-bar-modern {
    position: relative;
    display: inline-block;
    width: 80px;
    background: #e9ecef;
    border-radius: 0.5rem;
    overflow: hidden;
    height: 20px;
}

.progress-fill {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    transition: width 0.3s ease;
}

.progress-label {
    position: relative;
    z-index: 1;
    font-size: 0.6875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #333;
}

.advance-remove-item {
    background: #fff3cd;
    border-left: 3px solid #ffc107;
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-remove {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.3s ease;
}

.btn-remove:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.modern-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.875rem;
    color: #333;
}

.modern-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.modern-input:focus {
    border-color: #ff0000;
    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
    outline: none;
}

.modern-input-group {
    display: flex;
    align-items: stretch;
}

.modern-input-group .modern-input {
    border-radius: 0;
}

.modern-input-group .modern-input:first-child {
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
}

.modern-input-group .modern-input:last-child {
    border-top-right-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.modern-input-group-text {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: #f8f9fa;
    border: 2px solid #e5e7eb;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-card {
        padding: 0.75rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
    }
    
    .net-payable-amount {
        font-size: 1.75rem;
    }
    
    .worker-avatar {
        width: 45px;
        height: 45px;
    }
}
</style>