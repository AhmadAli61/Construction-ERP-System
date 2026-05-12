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
                        <h3 class="mb-0 fw-bold text-white">Sales Invoices</h3>
                        <p class="text-white-50 small mb-0">Manage and track all sales invoices</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.sales-invoices.create') }}" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i>
                        Create New Invoice
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

          <!-- Search and Filters Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-light border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-search me-2" style="color: #ff0000;"></i>
                Search & Filter Invoices
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                <i class="fas fa-undo-alt me-1"></i> Reset All Filters
            </button>
        </div>
    </div>
    <div class="card-body pt-3">
        <form wire:submit.prevent="performSearch">
            <div class="row g-3">
                <!-- Search Input -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search me-1" style="color: #ff0000;"></i>
                        Search Invoices
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0" 
                               placeholder="Search by invoice #, client name, email or phone..."
                               wire:model="tempSearch">
                        @if($tempSearch)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Search across invoice number, client name, email, phone
                    </small>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-pie me-1" style="color: #ff0000;"></i>
                        Payment Status
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-flag-checkered text-muted"></i>
                        </span>
                        <select class="form-select border-start-0" wire:model="tempStatusFilter">
                            <option value="">All Status</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                        </select>
                        @if($tempStatusFilter)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearStatusFilter">
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

                <!-- Search Actions - Fixed alignment -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold opacity-0" style="visibility: hidden;">
                        <i class="fas fa-eye me-1"></i>
                        Action
                    </label>
                    <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none; height: 38px;">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </div>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if($isSearching && ($search || $statusFilter))
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">
                        <i class="fas fa-filter me-1"></i>Active filters:
                    </small>
                    @if($search)
                        <span class="badge bg-primary">
                            <i class="fas fa-search me-1"></i>
                            Search: {{ $search }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearSearch"></button>
                        </span>
                    @endif
                    @if($statusFilter)
                        <span class="badge bg-info">
                            <i class="fas fa-chart-pie me-1"></i>
                            Status: {{ ucfirst($statusFilter) }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearStatusFilter"></button>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-primary">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Total Invoices</span>
                            <h3 class="dashboard-card-value">{{ number_format($totalCount) }}</h3>
                            <span class="dashboard-card-subtitle">All time</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-success">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Total Subtotal</span>
                            <h3 class="dashboard-card-value">€ {{ number_format($totalSubtotal, 2) }}</h3>
                            <span class="dashboard-card-subtitle">Before VAT</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-info">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Total VAT</span>
                            <h3 class="dashboard-card-value">€ {{ number_format($totalVat, 2) }}</h3>
                            <span class="dashboard-card-subtitle">Tax collected</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card dashboard-card-warning">
                        <div class="dashboard-card-icon">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <div class="dashboard-card-content">
                            <span class="dashboard-card-label">Total Amount</span>
                            <h3 class="dashboard-card-value">€ {{ number_format($totalAmount, 2) }}</h3>
                            <span class="dashboard-card-subtitle">Net payable</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                        Invoice Records
                    </h6>
                    <div class="text-muted small">
                        <i class="fas fa-chart-line me-1"></i>
                        Total: {{ $invoices->total() }} invoices
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-sec">
                                    <tr>
                                        <th class="py-3 ps-4" style="width: 100px;"><i class="fas fa-hashtag me-2" ></i> Invoice #</th>
                                        <th class="py-3" style="width: 100px;"><i class="fas fa-calendar-alt me-2" ></i> Date</th>
                                        <th class="py-3"><i class="fas fa-user me-2" ></i> Client</th>
                                        <th class="py-3"><i class="fas fa-project-diagram me-2" ></i> Project</th>
                                        <th class="py-3 text-end" style="width: 110px;"><i class="fas fa-chart-line me-2" ></i> Subtotal</th>
                                        <th class="py-3 text-end" style="width: 110px;"><i class="fas fa-percent me-2" ></i> VAT</th>
                                        <th class="py-3 text-end" style="width: 110px;"><i class="fas fa-euro-sign me-2" ></i> Total</th>
                                        <th class="py-3" style="width: 90px;"><i class="fas fa-chart-pie me-2" ></i> Status</th>
                                        <th class="py-3 text-center pe-4" style="width: 100px;"><i class="fas fa-cog me-2" ></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr class="border-bottom">
                                            <td class="ps-4">
                                                <span class="badge bg-light text-dark px-3 py-2">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $invoice->invoice_number }}
                                                </span>
                                                <br>
                                                <small class="text-muted">ID: #{{ $invoice->id }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $invoice->invoice_date->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $invoice->client_name }}</div>
                                                @if($invoice->client_phone)
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>{{ $invoice->client_phone }}
                                                    </small>
                                                @endif
                                                @if($invoice->client_email)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-envelope me-1"></i>{{ $invoice->client_email }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $invoice->project->name }}</div>
                                                <small class="text-muted">{{ $invoice->project->project_code }}</small>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-semibold">€ {{ number_format($invoice->subtotal, 2) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span>€ {{ number_format($invoice->vat_amount, 2) }}</span>
                                                <br>
                                                <small class="text-muted">({{ $invoice->vat_percentage }}%)</small>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-danger fs-6">€ {{ number_format($invoice->total, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($invoice->payment_status == 'paid')
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> Paid
                                                    </span>
                                                @elseif($invoice->payment_status == 'partial')
                                                    <span class="badge bg-warning text-dark px-3 py-2">
                                                        <i class="fas fa-adjust me-1"></i> Partial
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-2">
                                                        <i class="fas fa-times-circle me-1"></i> Unpaid
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center pe-4">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('admin.sales-invoices.edit', $invoice->id) }}" class="btn-action btn-edit" title="Edit Invoice">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button wire:click="delete({{ $invoice->id }})"
                                                            onclick="return confirm('Are you sure you want to delete this invoice?')"
                                                            class="btn-action btn-delete" title="Delete Invoice">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    <button class="btn-action btn-print" onclick="printInvoice({{ $invoice->id }})" title="Print Invoice">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No invoices found</p>
                                                    <small>Click "Create New Invoice" to create one</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($invoices->count() > 0)
                                    <tfoot class="bg-light">
                                        <tr class="fw-bold">
                                            <td colspan="4" class="py-3 ps-4">TOTAL</td>
                                            <td class="text-end py-3">€ {{ number_format($invoices->sum('subtotal'), 2) }}</td>
                                            <td class="text-end py-3">€ {{ number_format($invoices->sum('vat_amount'), 2) }}</td>
                                            <td class="text-end py-3 text-danger">€ {{ number_format($invoices->sum('total'), 2) }}</td>
                                            <td colspan="2" class="py-3 pe-4"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($invoices->total() > 0)
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} 
                                    of {{ $invoices->total() }} invoices
                                </div>
                                <div>
                                    {{ $invoices->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-database fa-3x mb-3 opacity-50"></i>
                                <h5 class="mb-2">No Invoices Found</h5>
                                <p class="mb-0">Click the "Create New Invoice" button to create your first invoice</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function printInvoice(id) {
            window.open('/admin/sales-invoices/print/' + id, '_blank');
        }
    </script>
    @endpush

    <style>
        /* Card Styles */
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
        
        /* Action Buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 2px;
        }
        
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        
        .btn-edit:hover {
            background: #138496;
            transform: translateY(-1px);
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        
        .btn-print {
            background: #28a745;
            color: white;
        }
        
        .btn-print:hover {
            background: #218838;
            transform: translateY(-1px);
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
        
        .dashboard-card-primary .dashboard-card-icon { background: linear-gradient(135deg, #ff0000, #cc0000); }
        .dashboard-card-success .dashboard-card-icon { background: linear-gradient(135deg, #28a745, #20c997); }
        .dashboard-card-info .dashboard-card-icon { background: linear-gradient(135deg, #17a2b8, #138496); }
        .dashboard-card-warning .dashboard-card-icon { background: linear-gradient(135deg, #ffc107, #e0a800); }
        
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
        
        /* Pagination */
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
        }
    </style>
</div>