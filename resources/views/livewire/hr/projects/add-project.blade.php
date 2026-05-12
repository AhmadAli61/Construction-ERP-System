{{-- resources/views/livewire/hr/projects/add-project.blade.php --}}
<div>
<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Red-Black Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-building text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Add New Project</h3>
                        <p class="text-white-50 small mb-0">Create a complete project with budget quotation and VAT</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hr.projects.list') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Back to Projects
                    </a>
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

            <form wire:submit.prevent="save">
                <!-- Budget Items Section -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-1 border-bottom">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                            Budget Items & Quotation
                        </h6>
                        <button type="button" class="btn btn-danger btn-sm" wire:click="addBudgetItem">
                            <i class="fas fa-plus-circle me-1"></i> Add Item
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 120px;">Section</th>
                                    <th>Description</th>
                                    <th style="width: 80px;">Unit</th>
                                    <th style="width: 80px;" class="text-center">Qty</th>
                                    <th style="width: 100px;" class="text-center">Unit Price (€)</th>
                                    <th style="width: 100px;" class="text-center">Total (€)</th>
                                    <th style="width: 40px;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budgetItems as $index => $item)
                                <tr>
                                    <td>
                                        <select class="form-select form-select-sm" wire:model="budgetItems.{{ $index }}.section">
                                            <option>General</option>
                                            <option>Masonry</option>
                                            <option>Electrical</option>
                                            <option>Plumbing</option>
                                            <option>Finishing</option>
                                            <option>Labor</option>
                                            <option>Materials</option>
                                            <option>Equipment</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" wire:model="budgetItems.{{ $index }}.description" placeholder="Item description">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" wire:model="budgetItems.{{ $index }}.unit" placeholder="Unit">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm text-center" step="0.01" wire:model.live="budgetItems.{{ $index }}.quantity">
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">€</span>
                                            <input type="number" class="form-control text-end" step="0.01" wire:model.live="budgetItems.{{ $index }}.unit_price">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-dark px-2 py-1">
                                            € {{ number_format($item['total'] ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" wire:click="removeBudgetItem({{ $index }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No budget items added yet. Click "Add Item" to start.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($budgetItems) > 0 && !empty(array_filter(array_column($budgetItems, 'description'))))
                            <tfoot class="bg-light">
                                @php
                                    $subtotal = collect($budgetItems)->sum('total');
                                    $vatAmount = $subtotal * ($vat_rate / 100);
                                    $total = $vat_included ? $subtotal : $subtotal + $vatAmount;
                                @endphp
                                <tr>
                                    <td colspan="5" class="text-end fw-bold py-2">SUBTOTAL</td>
                                    <td class="text-center fw-bold py-2">€ {{ number_format($subtotal, 2) }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">VAT Rate (%):</td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm" style="max-width: 100px;">
                                            <input type="number" class="form-control form-control-sm text-end" step="0.1" wire:model.live="vat_rate">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold">€ {{ number_format($vatAmount, 2) }}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" wire:model.live="vat_included" id="vatIncluded">
                                            <label class="form-check-label small" for="vatIncluded">
                                                VAT Included in Prices
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">TOTAL:</td>
                                    <td class="text-center">
                                        <div class="bg-danger text-white rounded py-1 px-2">
                                            <strong>€ {{ number_format($total, 2) }}</strong>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    
                    <!-- VAT Status Display -->
                    @if($vat_rate > 0)
                        <div class="mt-2">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>
                                    @if($vat_included)
                                        <strong>✅ VAT Included:</strong> The prices shown already include <strong>{{ $vat_rate }}% VAT</strong>.
                                    @else
                                        <strong>⚠️ VAT Added:</strong> <strong>{{ $vat_rate }}% VAT</strong> will be added to the subtotal.
                                    @endif
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="mt-2">
                            <div class="alert alert-secondary mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <small><strong>ℹ️ No VAT:</strong> No VAT is applied to this quotation.</small>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quotation Details Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-file-invoice-dollar me-2" style="color: #ff0000;"></i>
                        Quotation Details
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1" style="color: #ff0000;"></i>
                                Quotation Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-hashtag"></i>
                                </span>
                                <input type="text" class="form-control" wire:model="quotation_number" readonly style="background-color: #f8f9fa;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-check me-1" style="color: #ff0000;"></i>
                                Valid Until
                            </label>
                            <input type="date" class="form-control" wire:model="valid_until">
                            @error('valid_until') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

               <!-- Project Information Section -->
<div class="mb-4">
    <h6 class="fw-bold mb-3 pb-1 border-bottom">
        <i class="fas fa-info-circle me-2" style="color: #ff0000;"></i>
        Project Information
    </h6>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-project-diagram me-1" style="color: #ff0000;"></i>
                Project Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   wire:model="name"
                   placeholder="Enter project name">
            @error('name') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-barcode me-1" style="color: #ff0000;"></i>
                Project Code
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <input type="text" 
                       class="form-control @error('project_code') is-invalid @enderror" 
                       wire:model="project_code"
                       readonly
                       style="background-color: #f8f9fa; font-family: monospace; font-weight: bold;">
                <button type="button" 
                        class="btn btn-outline-secondary" 
                        wire:click="regenerateProjectCode"
                        title="Regenerate project code">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Project code is automatically generated in format: P-YEAR-SEQUENCE (e.g., P-2026-001)
            </small>
            @error('project_code') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>
    </div>
</div>

                <!-- Client Information Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-users me-2" style="color: #ff0000;"></i>
                        Client Information
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1" style="color: #ff0000;"></i>
                                Client Name
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('client_name') is-invalid @enderror" 
                                       wire:model="client_name"
                                       placeholder="Enter client name">
                            </div>
                            @error('client_name') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone me-1" style="color: #ff0000;"></i>
                                Client Phone
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="text" class="form-control" wire:model="client_phone" placeholder="Enter phone number">
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1" style="color: #ff0000;"></i>
                                Client Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" wire:model="client_email" placeholder="Enter email address">
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-location-dot me-1" style="color: #ff0000;"></i>
                                Client Address
                            </label>
                            <textarea class="form-control" rows="2" wire:model="client_address" placeholder="Enter client's full address"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary (Auto-calculated) -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-chart-line me-2" style="color: #ff0000;"></i>
                        Financial Summary
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calculator me-1" style="color: #ff0000;"></i>
                                Estimated Cost (Subtotal)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">€</span>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ number_format(collect($budgetItems)->sum('total'), 2) }}"
                                       readonly
                                       style="background-color: #f8f9fa; font-weight: bold;">
                            </div>
                            <small class="text-muted">Total of all budget items before VAT</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-file-invoice-dollar me-1" style="color: #ff0000;"></i>
                                Contract Value (Final Total)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">€</span>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ number_format($vat_included ? collect($budgetItems)->sum('total') : collect($budgetItems)->sum('total') * (1 + $vat_rate / 100), 2) }}"
                                       readonly
                                       style="background-color: #f8f9fa; font-weight: bold; color: #28a745;">
                            </div>
                            <small class="text-muted">
                                @if($vat_rate > 0)
                                    @if($vat_included)
                                        ✅ Includes {{ $vat_rate }}% VAT (VAT amount: € {{ number_format(collect($budgetItems)->sum('total') * $vat_rate / 100, 2) }})
                                    @else
                                        ⚠️ Excludes {{ $vat_rate }}% VAT (Add € {{ number_format(collect($budgetItems)->sum('total') * $vat_rate / 100, 2) }} VAT)
                                    @endif
                                @else
                                    ℹ️ No VAT applied
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Timeline Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-calendar-alt me-2" style="color: #ff0000;"></i>
                        Project Timeline
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-day me-1" style="color: #ff0000;"></i>
                                Start Date
                            </label>
                            <input type="date" class="form-control" wire:model="start_date">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-check me-1" style="color: #ff0000;"></i>
                                End Date
                            </label>
                            <input type="date" class="form-control" wire:model="end_date">
                            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <!-- Status & Location Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-chart-pie me-2" style="color: #ff0000;"></i>
                        Project Status & Location
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-flag-checkered me-1" style="color: #ff0000;"></i>
                                Status
                            </label>
                            <select class="form-select" wire:model="status">
                                <option value="planning">📋 Planning</option>
                                <option value="ongoing">⚡ Ongoing</option>
                                <option value="on_hold">⏸️ On Hold</option>
                                <option value="completed">✅ Completed</option>
                                <option value="cancelled">❌ Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt me-1" style="color: #ff0000;"></i>
                                Project Location
                            </label>
                            <textarea class="form-control" rows="2" wire:model="location" placeholder="Enter project location address"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-file-alt me-2" style="color: #ff0000;"></i>
                        Project Description
                    </h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1" style="color: #ff0000;"></i>
                                Description
                            </label>
                            <textarea class="form-control" rows="4" wire:model="description" placeholder="Enter detailed project description, scope, deliverables, etc."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="text-end pt-3 border-top">
                    <a href="{{ route('hr.projects.list') }}" class="btn btn-light me-2">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-save-project" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-2"></i> Create Project with Quotation
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i> Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom styling for better visual appeal */
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
    
    .input-group:focus-within .form-control,
    .input-group:focus-within .input-group-text {
        border-color: #ff0000;
        box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1);
    }
    
    .input-group:focus-within .form-control {
        border-left-color: #ff0000;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #ff0000;
        border-right-color: #ff0000;
    }
    
    .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
        transition: all 0.2s ease;
    }
    
    .btn {
        border-radius: 0.5rem;
        padding: 0.5rem 1.25rem;
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
    
    .btn-save-project {
        background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-save-project:hover {
        background: linear-gradient(135deg, #cc0000 0%, #990000 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
    }
    
    .btn-save-project:active {
        transform: translateY(0);
    }
    
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    
    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-1px);
    }
    
    .border-bottom {
        border-bottom: 2px solid #f0f0f0 !important;
    }
    
    .alert {
        border-radius: 0.75rem;
        border: none;
    }
    
    label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1);
    }
    
    .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table > :not(caption) > * > * {
        padding: 0.5rem;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .badge {
        font-weight: 500;
        border-radius: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
        }
    }
</style>
</div>