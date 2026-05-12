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
                        <h3 class="mb-0 fw-bold text-white">{{ $invoiceId ? 'Edit Invoice' : 'Create New Invoice' }}</h3>
                        <p class="text-white-50 small mb-0">{{ $invoiceId ? 'Update existing invoice details' : 'Generate a professional invoice for your client' }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.sales-invoices.list') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Invoices
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Invoice Information - Full Width First -->
            <div class="modern-card mb-4">
                <div class="modern-card-header bg-light">
                    <i class="fas fa-info-circle me-2" style="color: #ff0000;"></i>
                    <h6 class="mb-0 fw-bold">Invoice Information</h6>
                </div>
                <div class="modern-card-body">
                    <div class="row g-3">
                      <div class="col-md-4">
    <div class="modern-form-group">
        <label class="modern-label">
            <i class="fas fa-hashtag me-1"></i> Invoice Number <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-2">
            <input type="text" class="modern-input flex-grow-1 @error('invoice_number') is-invalid @enderror" 
                   wire:model="invoice_number" 
                   placeholder="Enter invoice number"
                   style="min-width: 0;">
            <button type="button" class="btn btn-outline-secondary" wire:click="generateInvoiceNumber" 
                    style="white-space: nowrap; border-radius: 0.75rem; border: 2px solid #e5e7eb; background: white;">
                <i class="fas fa-magic me-1"></i> Auto
            </button>
        </div>
        @error('invoice_number') 
            <small class="text-danger">{{ $message }}</small> 
        @else
            <small class="text-muted">Enter manually or click Auto</small>
        @enderror
    </div>
</div>
                        <div class="col-md-4">
                            <div class="modern-form-group">
                                <label class="modern-label">
                                    <i class="fas fa-calendar-alt me-1"></i> Invoice Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="modern-input" wire:model="invoice_date">
                                @error('invoice_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="modern-form-group">
                                <label class="modern-label">
                                    <i class="fas fa-flag-checkered me-1"></i> Payment Status <span class="text-danger">*</span>
                                </label>
                                <select class="modern-select" wire:model="payment_status">
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                    <option value="paid">Paid</option>
                                </select>
                                @error('payment_status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Provider - Full Width Card -->
            <div class="modern-card mb-4">
                <div class="modern-card-header bg-light">
                    <i class="fas fa-building me-2" style="color: #ff0000;"></i>
                    <h6 class="mb-0 fw-bold">Service Provider</h6>
                </div>
                <div class="modern-card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="modern-form-group">
                                <label class="modern-label">Company Name</label>
                                <input type="text" class="modern-input bg-sec" wire:model="company_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="modern-form-group">
                                <label class="modern-label">CIF/Tax ID</label>
                                <input type="text" class="modern-input" wire:model="company_cif">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="modern-form-group">
                                <label class="modern-label">Phone</label>
                                <input type="text" class="modern-input" wire:model="company_phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-form-group">
                                <label class="modern-label">Address</label>
                                <input type="text" class="modern-input bg-sec" wire:model="company_address" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-form-group">
                                <label class="modern-label">Email</label>
                                <input type="email" class="modern-input" wire:model="company_email">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project & Client Information -->
            <div class="modern-card mb-4">
                <div class="modern-card-header bg-light">
                    <i class="fas fa-user-circle me-2" style="color: #ff0000;"></i>
                    <h6 class="mb-0 fw-bold">Project & Client Information</h6>
                </div>
                <div class="modern-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="modern-form-group">
                                <label class="modern-label">
                                    <i class="fas fa-project-diagram me-1"></i> Select Project <span class="text-danger">*</span>
                                </label>
                                <select class="modern-select" wire:model.live="project_id">
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->name }} ({{ $project->project_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-form-group">
                                <label class="modern-label">
                                    <i class="fas fa-user me-1"></i> Client Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="modern-input" wire:model="client_name">
                                @error('client_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-form-group">
                                <label class="modern-label">Client Phone</label>
                                <input type="text" class="modern-input" wire:model="client_phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-form-group">
                                <label class="modern-label">Client Address</label>
                                <input type="text" class="modern-input" wire:model="client_address">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services & Pricing -->
            <div class="modern-card mb-4">
                <div class="modern-card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                        <h6 class="mb-0 fw-bold d-inline-block">Services & Pricing</h6>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="addItem">
                        <i class="fas fa-plus-circle me-1"></i> Add Item
                    </button>
                </div>
                <div class="modern-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-sec">
                                <tr>
                                    <th class="py-2 ps-3" style="width: 140px;">Service Type</th>
                                    <th class="py-2">Description</th>
                                    <th class="py-2" style="width: 80px;">Unit</th>
                                    <th class="py-2 text-center" style="width: 90px;">Quantity</th>
                                    <th class="py-2 text-center" style="width: 110px;">Unit Price</th>
                                    <th class="py-2 text-center" style="width: 110px;">Total</th>
                                    <th class="py-2 text-center pe-3" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                <tr class="border-bottom align-middle">
                                    <td class="ps-3">
                                        <select class="form-select form-select-sm" wire:model.live="items.{{ $index }}.service_type">
                                            @foreach($serviceTypes as $key => $data)
                                                <option value="{{ $key }}">{{ $key }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" wire:model="items.{{ $index }}.description" placeholder="Description">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" wire:model="items.{{ $index }}.unit" placeholder="Unit">
                                    </td>
                                    <td class="text-center">
                                        <input type="number" step="0.01" class="form-control form-control-sm text-center" wire:model.live="items.{{ $index }}.quantity" style="width: 80px; margin: 0 auto;">
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">€</span>
                                            <input type="number" step="0.01" class="form-control text-end" wire:model.live="items.{{ $index }}.unit_price">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-white px-2 py-1">
                                            € {{ number_format($item['total'] ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" wire:click="removeItem({{ $index }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-2x mb-1 opacity-50"></i>
                                            <p class="mb-0 small">No items added yet</p>
                                            <small>Click "Add Item" to start</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($items) > 0)
                            <tfoot class="bg-sec">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold py-2">Subtotal</td>
                                    <td class="text-center fw-bold py-2">€ {{ number_format($subtotal, 2) }}</td>
                                    <td class="py-2"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">VAT Rate (%):</td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm" style="max-width: 100px;">
                                            <input type="number" step="0.1" class="form-control form-control-sm text-end" wire:model.live="vat_percentage">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold">€ {{ number_format($vat_amount, 2) }}</td>
                                    <td class="py-2"></td>
                                </tr>
                                <tr style="background: linear-gradient(135deg, #fef2f2, #fee2e2);">
                                    <td colspan="5" class="text-end fw-bold py-2 fs-5">TOTAL AMOUNT</td>
                                    <td class="text-center py-2">
                                        <div class="bg-danger text-white rounded py-1 px-2">
                                            <strong class="fs-5">€ {{ number_format($total, 2) }}</strong>
                                        </div>
                                    </td>
                                    <td class="py-2"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Terms & Exclusions Row -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="modern-card">
                        <div class="modern-card-header bg-light">
                            <i class="fas fa-file-contract me-2" style="color: #ff0000;"></i>
                            <h6 class="mb-0 fw-bold">Terms & Conditions</h6>
                        </div>
                        <div class="modern-card-body">
                            <textarea class="modern-textarea" rows="6" wire:model="terms_conditions"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modern-card">
                        <div class="modern-card-header bg-light">
                            <i class="fas fa-ban me-2" style="color: #ff0000;"></i>
                            <h6 class="mb-0 fw-bold">Exclusions</h6>
                        </div>
                        <div class="modern-card-body">
                            <textarea class="modern-textarea" rows="6" wire:model="exclusions"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="modern-card mt-4">
                <div class="modern-card-header bg-light">
                    <i class="fas fa-sticky-note me-2" style="color: #ff0000;"></i>
                    <h6 class="mb-0 fw-bold">Additional Notes</h6>
                </div>
                <div class="modern-card-body">
                    <textarea class="modern-textarea" rows="3" wire:model="notes" placeholder="Any additional information..."></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                <a href="{{ route('admin.sales-invoices.list') }}" class="btn btn-light">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none;" wire:click="save">
                    <i class="fas fa-save me-1"></i>
                    {{ $invoiceId ? 'Update Invoice' : 'Save Invoice' }}
                </button>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }

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
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .btn-primary {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        .table {
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem;
        }

        .bg-sec {
            background-color: #f8f9fa !important;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        @media (max-width: 768px) {
            .modern-card-body {
                padding: 1rem;
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
            }
            
            .d-flex.gap-2 .btn {
                width: 100%;
            }
            
            .row.g-4 {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</div>