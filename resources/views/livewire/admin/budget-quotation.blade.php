<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Red-Black Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4"
            style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-file-invoice-dollar text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Budget Quotations</h3>
                        <p class="text-white-50 small mb-0">Create and manage professional project quotations</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light" wire:click="$set('showForm', true)">
                        <i class="fas fa-plus-circle me-2"></i> New Quotation
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Modern Search and Filters Section -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-search me-2" style="color: #ff0000;"></i>
                            Search & Filter Quotations
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                            <i class="fas fa-undo-alt me-1"></i> Reset All Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="performSearch">
                        <div class="row g-3 pt-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-search me-1" style="color: #ff0000;"></i>
                                    Search Quotations
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0"
                                        placeholder="Search by project name, code, client or quotation number..."
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
                                    Search across project name, code, client name, quotation number
                                </small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-chart-pie me-1" style="color: #ff0000;"></i>
                                    Project Status
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-flag-checkered text-muted"></i>
                                    </span>
                                    <select class="form-select border-start-0" wire:model="tempStatusFilter">
                                        <option value="">All Status</option>
                                        <option value="planning">Planning</option>
                                        <option value="ongoing">Ongoing</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    @if ($tempStatusFilter)
                                        <button type="button" class="btn btn-outline-secondary"
                                            wire:click="clearStatusFilter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

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
                                <button type="submit" class="btn btn-primary w-100"
                                    style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none; height: 38px;">
                                    <i class="fas fa-search me-2"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($isSearching && ($search || $statusFilter))
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
                                            style="font-size: 8px;" wire:click="clearStatusFilter"></button>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quotations List -->
            <div class="card border-0 shadow-sm">
                <div
                    class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                        All Quotations
                    </h6>
                    <div class="text-muted small">
                        <i class="fas fa-chart-line me-1"></i>
                        Total: {{ $projects->total() }} quotations
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-sec">
                                    <tr>
                                        <th class="py-3 ps-4" style="width: 100px;">
                                            <i class="fas fa-hashtag me-2"></i> Quotation
                                        </th>
                                        <th class="py-3" style="width: 200px;">
                                            <i class="fas fa-project-diagram me-2"></i> Project
                                        </th>
                                        <th class="py-3" style="width: 200px;">
                                            <i class="fas fa-user me-2"></i> Client
                                        </th>
                                        <th class="py-3 text-end" style="width: 120px;">
                                            <i class="fas fa-euro-sign me-2"></i> Total
                                        </th>
                                        <th class="py-3" style="width: 110px;">
                                            <i class="fas fa-calendar me-2"></i> Date
                                        </th>
                                        <th class="py-3" style="width: 110px;">
                                            <i class="fas fa-calendar-check me-2"></i> Valid Until
                                        </th>
                                        <th class="py-3" style="width: 100px;">
                                            <i class="fas fa-chart-pie me-2"></i> Status
                                        </th>
                                        <th class="py-3 text-center pe-4" style="width: 100px;">
                                            <i class="fas fa-cog me-2"></i> Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                        @php $total = $project->budget_total ?? 0; @endphp
                                        <tr class="border-bottom">
                                            <td class="ps-4">
                                                <span class="badge bg-light text-dark px-3 py-2">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $project->quotation_number ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $project->name }}</div>
                                                @if ($project->project_code)
                                                    <small class="text-muted">{{ $project->project_code }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $project->client_name }}</div>
                                                @if ($project->client_phone)
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>{{ $project->client_phone }}
                                                    </small>
                                                @endif
                                                @if ($project->client_email)
                                                    <br><small class="text-muted">
                                                        <i
                                                            class="fas fa-envelope me-1"></i>{{ $project->client_email }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">€
                                                    {{ number_format($total, 2) }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $project->created_at->format('M d, Y') }}</div>
                                                <small
                                                    class="text-muted">{{ $project->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                @if ($project->valid_until)
                                                    <span class="badge px-3 py-2 fw-bold text-danger">
                                                        {{ \Carbon\Carbon::parse($project->valid_until)->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'planning' => 'info',
                                                        'ongoing' => 'primary',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger',
                                                    ];
                                                    $color = $statusColors[$project->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} px-3 py-2">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </td>

                                            <td class="text-center pe-4">
                                                <div class="d-flex gap-1 justify-content-center">

                                                    <button class="btn btn-sm btn-success"
                                                        wire:click="editProject({{ $project->id }})"
                                                        style="font-size: 12px; padding: 3px 6px;">
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </button>

                                                    <button class="btn btn-sm btn-secondarys"
                                                        wire:click="printQuotation({{ $project->id }})"
                                                        style="font-size: 12px; padding: 3px 6px;">
                                                        <i class="fas fa-print me-1"></i> Print
                                                    </button>

                                                </div>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No quotations found</p>
                                                    <small>Click "New Quotation" to create one</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($projects->total() > 0)
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }}
                                    of {{ $projects->total() }} quotations
                                </div>
                                <div>
                                    {{ $projects->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-database fa-3x mb-3 opacity-50"></i>
                                <h5 class="mb-2">No Quotations Found</h5>
                                <p class="mb-0">Click the "New Quotation" button to create your first quotation</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quotation Form Modal - Enhanced Modern Layout -->
    @if ($showForm)
        <div class="modal-modern-overlay" wire:click.self="$set('showForm', false)">
            <div class="modal-modern-container" style="max-width: 1400px;">
                <div class="modern-modal-content">
                    <div class="modern-modal-header">
                        <div class="modern-modal-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="modern-modal-title">
                            <h5 class="mb-0 fw-bold text-white">
                                {{ $isEditing ? 'Edit Quotation' : 'Create New Quotation' }}
                            </h5>
                            <small>Fill in the details below to generate a professional quotation</small>
                        </div>
                        <button type="button" class="modern-modal-close" wire:click="resetForm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modern-modal-body">
                        <form wire:submit.prevent="saveQuotation">
                            <!-- Progress Steps -->
                            <div class="progress-steps mb-4">
                                <div class="step active">
                                    <div class="step-number">1</div>
                                    <div class="step-label">Company & Details</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-label">Client Info</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-label">Project Info</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step">
                                    <div class="step-number">4</div>
                                    <div class="step-label">Budget Items</div>
                                </div>
                            </div>

                            <!-- Company & Quotation Details Section -->
                            <div class="form-modern-section mb-4">
                                <div class="section-title">
                                    <div class="title-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h6 class="mb-0">Company & Quotation Details</h6>
                                </div>
                                <div class="section-content">
                                    <div class="row g-4">
                                        <div class="col-md-7">
                                            <div class="company-card">
                                                <div class="company-header">
                                                    <i class="fas fa-store"></i>
                                                    <span>Company Information</span>
                                                </div>
                                                <div class="company-body">
                                                    <div class="company-name">{{ $company['name'] }}</div>
                                                    <div class="company-details">
                                                        <div class="detail-item">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <span>{{ $company['address'] }}</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <i class="fas fa-id-card"></i>
                                                            <span>{{ $company['tax_id'] }}</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <i class="fas fa-phone"></i>
                                                            <span>{{ $company['phone'] }}</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <i class="fas fa-envelope"></i>
                                                            <span>{{ $company['email'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="quotation-details-card">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control modern-input"
                                                        wire:model="quotation_number" readonly
                                                        placeholder="Quotation Number">
                                                    <label class="text-danger"><i
                                                            class="fas fa-tag me-2 text-danger"></i>Quotation
                                                        Number</label>
                                                </div>
                                                <div class="form-floating">
                                                    <input type="date" class="form-control modern-input"
                                                        wire:model="valid_until" placeholder="Valid Until">
                                                    <label class="text-danger"><i
                                                            class="fas fa-calendar-alt me-2 text-danger"></i>Valid
                                                        Until <span class="text-danger">*</span></label>
                                                </div>
                                                @error('valid_until')
                                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Information Section -->
                            <div class="form-modern-section mb-4">
                                <div class="section-title">
                                    <div class="title-icon">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <h6 class="mb-0">Client Information</h6>
                                </div>
                                <div class="section-content">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control modern-input @error('client_name') is-invalid @enderror"
                                                    wire:model="client_name" placeholder="Client Name">
                                                <label><i class="fas fa-user me-2"></i>Client Name <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            @error('client_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control modern-input"
                                                    wire:model="client_phone" placeholder="Phone Number">
                                                <label><i class="fas fa-phone me-2"></i>Phone Number</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control modern-input"
                                                    wire:model="client_email" placeholder="Email Address">
                                                <label><i class="fas fa-envelope me-2"></i>Email Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control modern-input"
                                                    wire:model="client_address" placeholder="Address">
                                                <label><i class="fas fa-location-dot me-2"></i>Address</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Information Section -->
                            <div class="form-modern-section mb-4">
                                <div class="section-title">
                                    <div class="title-icon">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <h6 class="mb-0">Project Information</h6>
                                </div>
                                <div class="section-content">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control modern-input @error('name') is-invalid @enderror"
                                                    wire:model="name" placeholder="Project Name">
                                                <label><i class="fas fa-tasks me-2"></i>Project Name <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control modern-input @error('project_code') is-invalid @enderror"
                                                    wire:model="project_code" placeholder="Project Code">
                                                <label><i class="fas fa-barcode me-2"></i>Project Code <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            @error('project_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control modern-input"
                                                    wire:model="location" placeholder="Location">
                                                <label><i class="fas fa-map-marker-alt me-2"></i>Location</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <textarea class="form-control modern-input" wire:model="description" placeholder="Project Description"
                                                    style="height: 100px;"></textarea>
                                                <label><i class="fas fa-align-left me-2"></i>Project
                                                    Description</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Items Section -->
                            <div class="form-modern-section mb-4">
                                <div class="section-title d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="title-icon me-2">
                                            <i class="fas fa-list"></i>
                                        </div>
                                        <h6 class="mb-0">Budget Items</h6>
                                    </div>
                                    <button type="button" class="btn-add-item" wire:click="addBudgetItem">
                                        <i class="fas fa-plus-circle me-1"></i> Add Item
                                    </button>
                                </div>
                                <div class="section-content p-0">
                                    <div class="table-responsive">
                                        <table class="budget-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%">Section</th>
                                                    <th style="width: 30%">Description</th>
                                                    <th style="width: 10%">Unit</th>
                                                    <th style="width: 10%">Quantity</th>
                                                    <th style="width: 15%">Unit Price</th>
                                                    <th style="width: 15%">Total</th>
                                                    <th style="width: 5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($budgetItems as $index => $item)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="budget-input"
                                                                wire:model="budgetItems.{{ $index }}.section"
                                                                placeholder="Section">
                                                            @error('budgetItems.{{ $index }}.section')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="text" class="budget-input"
                                                                wire:model="budgetItems.{{ $index }}.description"
                                                                placeholder="Description">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="budget-input"
                                                                wire:model="budgetItems.{{ $index }}.unit"
                                                                placeholder="Unit">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="budget-input text-center"
                                                                step="0.01"
                                                                wire:model.live="budgetItems.{{ $index }}.quantity"
                                                                wire:change="recalculateItemTotals">
                                                        </td>
                                                        <td>
                                                            <div class="price-input">
                                                                <span class="currency">€</span>
                                                                <input type="number" class="budget-input"
                                                                    step="0.01"
                                                                    wire:model.live="budgetItems.{{ $index }}.unit_price"
                                                                    wire:change="recalculateItemTotals">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="total-amount">
                                                                € {{ number_format($item['total'] ?? 0, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="remove-item"
                                                                wire:click="removeBudgetItem({{ $index }})">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if (count($budgetItems) == 0)
                                                    <tr>
                                                        <td colspan="7" class="empty-state">
                                                            <i class="fas fa-inbox"></i>
                                                            <p>No items added yet</p>
                                                            <small>Click "Add Item" to start</small>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            @if (count($budgetItems) > 0)
                                                <tfoot>
                                                    @php
                                                        $subtotal = collect($budgetItems)->sum('total');
                                                        $vatAmount = $subtotal * ($vat_rate / 100);
                                                        $total = $vat_included ? $subtotal : $subtotal + $vatAmount;
                                                    @endphp
                                                    <tr class="summary-row">
                                                        <td colspan="5" class="text-end fw-bold">SUBTOTAL</td>
                                                        <td class="fw-bold">€ {{ number_format($subtotal, 2) }}</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="summary-row">
                                                        <td colspan="4" class="text-end fw-bold">VAT Rate</td>
                                                        <td colspan="2">
                                                            <div class="vat-control">
                                                                <input type="number" class="vat-input"
                                                                    step="0.1" wire:model="vat_rate">
                                                                <span class="vat-percent">%</span>
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="summary-row">
                                                        <td colspan="5" class="text-end fw-bold">VAT Amount</td>
                                                        <td class="fw-bold">€ {{ number_format($vatAmount, 2) }}</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="summary-row">
                                                        <td colspan="4" class="text-end">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    wire:model="vat_included" id="vatIncluded">
                                                                <label class="form-check-label" for="vatIncluded">
                                                                    VAT Included in Prices
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td colspan="2">
                                                            <div class="total-box">
                                                                <span>TOTAL</span>
                                                                <strong>€ {{ number_format($total, 2) }}</strong>
                                                            </div>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="modal-actions">
                                <button type="button" class="btn-cancel" wire:click="resetForm">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </button>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-save me-2"></i>
                                    {{ $isEditing ? 'Update Quotation' : 'Create Quotation' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

  <!-- Print Modal - Professional Minimal Design -->
@if ($printMode && $projectId)
    <div class="modal-modern-overlay" wire:click.self="$set('printMode', false)">
        <div class="modal-modern-container" style="max-width: 1000px;">
            <div class="modern-modal-content">
                <div class="modern-modal-header" style="background: linear-gradient(135deg, #1e293b 70%, #ffffff 100%);">
                    <div class="modern-modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 18L18 18" />
                            <path d="M6 14L18 14" />
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <path d="M8 8h8" />
                            <path d="M8 12h8" />
                        </svg>
                    </div>
                    <div class="modern-modal-title">
                        <h5 class="mb-0 fw-bold text-white">Quotation Preview</h5>
                        <small class="text-white-50">Review before printing</small>
                    </div>
                    <button type="button" class="modern-modal-close" wire:click="$set('printMode', false)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <div class="modern-modal-body p-0" id="print-area">
                    <div class="quotation-minimal" style="max-width: 1000px; margin: 0 auto; background: white; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">

                        <!-- Top Red Line -->
                        <div style="height: 4px; background: #dc2626;"></div>

                        <!-- Header -->
                        <div style="padding: 30px 35px 20px 35px; border-bottom: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                                <div>
                                    <img src="{{ asset('build/assets/img/OBREROEUSKA 2.png') }}" alt="Logo" style="max-height: 100px; width: auto;">
                                </div>
                                <div style="text-align: right;">
                                    <div style="color: #dc2626; font-weight: 600; font-size: 13px; letter-spacing: 2px; margin-bottom: 5px;">
                                        PRESUPUESTO
                                    </div>
                                    <div style="font-size: 24px; font-weight: 700; color: #0f172a;">
                                        {{ $quotation_number }}
                                    </div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Fecha de emisión: {{ now()->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div style="padding: 30px 35px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; border-bottom: 1px solid #e2e8f0; background: #ffffff;">

                            <!-- LEFT COLUMN -->
                            <div>
                                <!-- Company Info Block -->
                                <div style="margin-bottom: 28px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                        <div style="width: 28px; height: 28px; background: #dc2626; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                                <line x1="8" y1="6" x2="16" y2="6"></line>
                                                <line x1="8" y1="10" x2="16" y2="10"></line>
                                                <line x1="8" y1="14" x2="12" y2="14"></line>
                                            </svg>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #dc2626;">
                                            DATOS DE LA EMPRESA
                                        </div>
                                    </div>
                                    <div style="padding-left: 36px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 15px; margin-bottom: 6px;">
                                            {{ $company['name'] }}
                                        </div>
                                        <div style="font-size: 12px; color: #475569; line-height: 1.5; margin-bottom: 8px;">
                                            {{ $company['address'] }}
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px;">
                                            <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                    <polyline points="22,6 12,13 2,6"></polyline>
                                                </svg>
                                                <span><span style="font-weight: 600;">CIF/NIF:</span> {{ $company['tax_id'] }}</span>
                                            </div>
                                            <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                                    <line x1="12" y1="18" x2="12" y2="18"></line>
                                                </svg>
                                                <span><span style="font-weight: 600;">Teléfono:</span> {{ $company['phone'] }}</span>
                                            </div>
                                            <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                    <polyline points="22,6 12,13 2,6"></polyline>
                                                </svg>
                                                <span><span style="font-weight: 600;">Email:</span> {{ $company['email'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Client Info Block -->
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                        <div style="width: 28px; height: 28px; background: #dc2626; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #dc2626;">
                                            DATOS DEL CLIENTE
                                        </div>
                                    </div>
                                    <div style="padding-left: 36px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 15px; margin-bottom: 6px;">
                                            {{ $client_name }}
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                                            @if ($client_phone)
                                                <div style="font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                                        <line x1="12" y1="18" x2="12" y2="18"></line>
                                                    </svg>
                                                    {{ $client_phone }}
                                                </div>
                                            @endif
                                            @if ($client_email)
                                                <div style="font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                        <polyline points="22,6 12,13 2,6"></polyline>
                                                    </svg>
                                                    {{ $client_email }}
                                                </div>
                                            @endif
                                        </div>
                                        @if ($client_address)
                                            <div style="font-size: 12px; color: #475569; margin-top: 8px; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                {{ $client_address }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT COLUMN -->
                            <div>
                                <!-- Project Info Block -->
                                <div style="margin-bottom: 28px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                        <div style="width: 28px; height: 28px; background: #dc2626; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #dc2626;">
                                            DETALLES DEL PROYECTO
                                        </div>
                                    </div>
                                    <div style="padding-left: 36px;">
                                        <div style="font-weight: 700; color: #0f172a; font-size: 15px; margin-bottom: 4px;">
                                            {{ $name }}
                                        </div>
                                        <div style="font-size: 12px; color: #475569; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 4v16h16V4H4zm2 4h12v2H6V8zm0 4h12v2H6v-2zm0 4h8v2H6v-2z"></path>
                                            </svg>
                                            <span><span style="font-weight: 600;">Código de proyecto:</span> {{ $project_code }}</span>
                                        </div>
                                        @if ($location)
                                            <div style="font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                {{ $location }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Validity & Payment Block -->
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                                        <div style="width: 28px; height: 28px; background: #dc2626; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                                <line x1="12" y1="14" x2="12" y2="14"></line>
                                                <line x1="16" y1="14" x2="16" y2="14"></line>
                                                <line x1="8" y1="14" x2="8" y2="14"></line>
                                            </svg>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #dc2626;">
                                            VALIDEZ Y CONDICIONES
                                        </div>
                                    </div>
                                    <div style="padding-left: 36px;">
                                        <div style="display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
                                            <div style="font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                <span><span style="font-weight: 600;">Válido hasta:</span></span>
                                                <span style="color: #dc2626; font-weight: 600;">{{ \Carbon\Carbon::parse($valid_until)->format('d/m/Y') }}</span>
                                            </div>
                                            <div style="width: 1px; height: 12px; background: #e2e8f0;"></div>
                                            <div style="font-size: 12px; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <path d="M12 6v6l4 2"></path>
                                                </svg>
                                                <span><span style="font-weight: 600;">Forma de pago:</span> Contra aceptación</span>
                                            </div>
                                        </div>
                                        <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border-left: 3px solid #dc2626;">
                                            <div style="font-size: 11px; color: #475569; display: flex; align-items: center; gap: 8px;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                                <span>Este presupuesto tiene una validez de 30 días desde la fecha de emisión.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description (if any) -->
                        @if ($description)
                            <div style="padding: 20px 35px; border-bottom: 1px solid #e2e8f0;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #dc2626;">
                                        DESCRIPCIÓN DEL PROYECTO
                                    </div>
                                </div>
                                <div style="font-size: 13px; color: #475569; line-height: 1.5; padding-left: 22px;">
                                    {{ $description }}
                                </div>
                            </div>
                        @endif

                        <!-- Budget Table - LEFT ALIGNED NOW -->
                       <!-- Budget Table - LEFT ALIGNED WITH ADJUSTED COLUMN WIDTHS -->
<div style="padding: 25px 35px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path>
            <rect x="3" y="12" width="18" height="8" rx="2"></rect>
            <line x1="7" y1="12" x2="7" y2="16"></line>
            <line x1="17" y1="12" x2="17" y2="16"></line>
        </svg>
        <div style="font-size: 13px; font-weight: 700; color: #0f172a; letter-spacing: 0.5px;">
            DESGLOSE DEL PRESUPUESTO
        </div>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
        <thead>
            <tr style="border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 10px 8px 10px 0; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; width: 12%;">
                    SECCIÓN
                </th>
                <th style="padding: 10px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; width: 28%;">
                    DESCRIPCIÓN
                </th>
                <th style="padding: 10px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; width: 10%;">
                    UNIDAD
                </th>
                <th style="padding: 10px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; width: 12%;">
                    CANTIDAD
                </th>
                <th style="padding: 10px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; width: 18%;">
                    PRECIO UNIT.
                </th>
                <th style="padding: 10px 0 10px 8px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; width: 20%;">
                    TOTAL
                </th>
             </tr>
        </thead>
        <tbody>
            @php $hasItems = false; @endphp
            @foreach ($budgetItems as $item)
                @if ($item['description'])
                    @php $hasItems = true; @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 8px 10px 0; font-size: 13px; color: #334155; text-align: left; word-wrap: break-word;">
                            {{ $item['section'] }}
                         </td>
                        <td style="padding: 10px 8px; font-size: 13px; color: #334155; text-align: left; word-wrap: break-word;">
                            {{ $item['description'] }}
                         </td>
                        <td style="padding: 10px 8px; font-size: 12px; color: #64748b; text-align: left;">
                            {{ $item['unit'] ?: '-' }}
                         </td>
                        <td style="padding: 10px 8px; font-size: 13px; color: #334155; text-align: left;">
                            {{ number_format($item['quantity'], 2) }}
                         </td>
                        <td style="padding: 10px 8px; font-size: 13px; color: #334155; text-align: left;">
                            € {{ number_format($item['unit_price'], 2) }}
                         </td>
                        <td style="padding: 10px 0 10px 8px; font-size: 13px; font-weight: 500; color: #0f172a; text-align: left;">
                            € {{ number_format($item['total'], 2) }}
                         </td>
                     </tr>
                @endif
            @endforeach
            @if (!$hasItems)
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #94a3b8;">
                        No hay partidas añadidas
                     </td>
                 </tr>
            @endif
        </tbody>
        @if ($hasItems)
            <tfoot>
                @php
                    $subtotal = collect($budgetItems)->sum('total');
                    $vatAmount = $subtotal * ($vat_rate / 100);
                    $total = $vat_included ? $subtotal : $subtotal + $vatAmount;
                @endphp
                <tr style="border-top: 1px solid #e2e8f0;">
                    <td colspan="5" style="padding: 12px 8px 8px 0; text-align: right; font-size: 12px; color: #64748b;">
                        SUBTOTAL
                     </td>
                    <td style="padding: 12px 0 8px 8px; text-align: left; font-size: 13px; font-weight: 500;">
                        € {{ number_format($subtotal, 2) }}
                     </td>
                 </tr>
                @if ($vat_rate > 0)
                    <tr>
                        <td colspan="5" style="padding: 5px 8px 5px 0; text-align: right; font-size: 12px; color: #64748b;">
                            IVA ({{ $vat_rate }}%)
                         </td>
                        <td style="padding: 5px 0 5px 8px; text-align: left; font-size: 13px;">
                            € {{ number_format($vatAmount, 2) }}
                         </td>
                     </tr>
                @endif
                <tr style="border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    <td colspan="5" style="padding: 12px 8px 12px 0; text-align: right; font-weight: 700; font-size: 14px; color: #0f172a;">
                        TOTAL
                     </td>
                    <td style="padding: 12px 0 12px 8px; text-align: left; font-weight: 800; font-size: 18px; color: #dc2626;">
                        € {{ number_format($total, 2) }}
                     </td>
                 </tr>
            </tfoot>
        @endif
     </table>
    @if ($vat_included && $vat_rate > 0)
        <div style="margin-top: 8px; text-align: right; font-size: 10px; color: #94a3b8;">
            * El IVA está incluido en los precios
        </div>
    @endif
</div>

                        <!-- Footer -->
                        <div style="padding: 20px 35px; border-top: 1px solid #e2e8f0; text-align: center; background: #fafafa;">
                            <p style="margin: 0; font-size: 11px; color: #64748b;">
                                {{ $company['name'] }} | {{ $company['phone'] }} | {{ $company['email'] }}
                            </p>
                            <p style="margin: 5px 0 0 0; font-size: 10px; color: #94a3b8;">
                                Gracias por confiar en nosotros
                            </p>
                        </div>

                        <!-- Bottom Red Line -->
                        <div style="height: 3px; background: #dc2626;"></div>
                    </div>
                </div>

                <div class="modern-modal-footer" style="background: #f8f9fa; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="modal-btn-secondary" wire:click="$set('printMode', false)" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #e5e7eb; background: white; color: #6c757d; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; margin-right: 8px;">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Close
                    </button>
                    <button type="button" class="modal-btn-primary" onclick="printQuotationDocument()" style="padding: 10px 20px; border-radius: 8px; border: none; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; margin-right: 8px;">
                            <path d="M6 18L18 18" />
                            <path d="M6 14L18 14" />
                            <rect x="4" y="4" width="16" height="16" rx="2" />
                            <path d="M8 8h8" />
                            <path d="M8 12h8" />
                        </svg>
                        Print / Save as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hover effects for buttons */
        .modal-btn-secondary:hover {
            background: #f8f9fa;
            border-color: #dc2626;
            color: #dc2626;
            transform: translateY(-1px);
        }
        
        .modal-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0 !important;
                background: white;
                overflow: visible;
            }

            .modal-modern-overlay {
                background: white !important;
                backdrop-filter: none !important;
            }

            .modern-modal-header,
            .modern-modal-footer {
                display: none !important;
            }

            .quotation-minimal {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            @page {
                size: A4;
                margin: 1.2cm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <script>
        function printQuotationDocument() {
            // Close the modal first
            Livewire.dispatch('closePrintModal');
            
            // Wait a bit for the modal to close
            setTimeout(() => {
                // Create a new window for printing
                const printWindow = window.open('', '_blank');
                
                // Get the quotation content from the current page
                const quotationContent = document.querySelector('#print-area .quotation-minimal').cloneNode(true);
                
                // Create the print HTML
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Quotation {{ $quotation_number }}</title>
                        <style>
                            * {
                                margin: 0;
                                padding: 0;
                                box-sizing: border-box;
                            }
                            
                            body {
                                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                padding: 20px;
                                background: white;
                            }
                            
                            .quotation-minimal {
                                max-width: 1000px;
                                margin: 0 auto;
                                background: white;
                            }
                            
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin: 20px 0;
                            }
                            
                            th, td {
                                padding: 10px;
                                text-align: left;
                                border-bottom: 1px solid #e2e8f0;
                            }
                            
                            th {
                                background: #f8fafc;
                                font-weight: 600;
                            }
                            
                            @page {
                                size: A4;
                                margin: 1.5cm;
                            }
                            
                            @media print {
                                body {
                                    padding: 0;
                                }
                                .no-print {
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${quotationContent.outerHTML}
                        <div class="no-print" style="text-align: center; margin-top: 20px; padding: 20px;">
                            <button onclick="window.print()" style="padding: 12px 24px; background: #dc2626; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; margin-right: 10px;">
                                🖨️ Print / Save as PDF
                            </button>
                            <button onclick="window.close()" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                                ✕ Close
                            </button>
                        </div>
                        <script>
                            window.onload = function() {
                                window.print();
                            };
                            window.onafterprint = function() {
                                setTimeout(function() {
                                    window.close();
                                }, 500);
                            };
                        <\/script>
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
            }, 200);
        }
    </script>
@endif

    <style>
        /* Modern Form Styles */
        .progress-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            padding: 20px 30px;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            border-color: #ff0000;
            color: white;
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .step.active .step-label {
            color: #ff0000;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e2e8f0;
        }

        /* Section Cards */
        .form-modern-section {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            background: #f8fafc;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .title-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #ff0000, #cc0000);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .section-content {
            padding: 1.5rem;
        }

        /* Modern Form Floating Labels */
        .modern-input {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
        }

        .form-floating>label {
            padding-left: 1rem;
            color: #64748b;
        }

        /* Company Card */
        .company-card {
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
            border: 1px solid #fee2e2;
            border-radius: 1rem;
            overflow: hidden;
        }

        .company-header {
            background: #ff0000;
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-body {
            padding: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
        }

        .company-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #475569;
        }

        .detail-item i {
            width: 20px;
            color: #ff0000;
        }

        /* Quotation Details Card */
        .quotation-details-card {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1.5rem;
            height: 100%;
        }

        /* Budget Table */
        .budget-table {
            width: 100%;
            border-collapse: collapse;
        }

        .budget-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .budget-table th {
            padding: 12px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .budget-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .budget-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .budget-input:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.1);
        }

        .price-input {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .currency {
            background: #f1f5f9;
            padding: 8px 10px;
            border-radius: 0.5rem;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .price-input .budget-input {
            flex: 1;
        }

        .total-amount {
            font-weight: 600;
            color: #0f172a;
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 0.5rem;
            display: inline-block;
        }

        .remove-item {
            background: #fee2e2;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            color: #dc2626;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-item:hover {
            background: #dc2626;
            color: white;
            transform: scale(1.05);
        }

        .empty-state {
            text-align: center;
            padding: 60px !important;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state p {
            margin-bottom: 5px;
            font-weight: 500;
        }

        /* VAT Controls */
        .vat-control {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
        }

        .vat-input {
            width: 80px;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            text-align: right;
        }

        .vat-percent {
            font-weight: 600;
            color: #475569;
        }

        .summary-row {
            border-top: 1px solid #e2e8f0;
        }

        .summary-row td {
            padding: 12px;
            font-size: 14px;
        }

        .total-box {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            padding: 12px 20px;
            border-radius: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            gap: 15px;
        }

        .total-box span {
            font-size: 14px;
            opacity: 0.9;
        }

        .total-box strong {
            font-size: 20px;
            font-weight: 800;
        }

        /* Action Buttons */
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 1rem;
        }

        .btn-cancel,
        .btn-submit {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-cancel {
            background: white;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-cancel:hover {
            background: #f8fafc;
            border-color: #ff0000;
            color: #ff0000;
            transform: translateY(-2px);
        }

        .btn-submit {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 0, 0, 0.4);
        }

        .btn-add-item {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 0.5rem;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .progress-steps {
                padding: 15px;
            }

            .step-label {
                font-size: 10px;
            }

            .step-number {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .section-content {
                padding: 1rem;
            }

            .budget-table {
                font-size: 12px;
            }

            .modal-actions {
                flex-direction: column;
            }

            .btn-cancel,
            .btn-submit {
                width: 100%;
            }
        }

        .card {
            border-radius: 1rem;
            overflow: hidden;
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

        .btn-primary {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

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

        .btn-print {
            background: #28a745;
            color: white;
        }

        .btn-print:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .badge {
            font-weight: 500;
            border-radius: 0.5rem;
        }

        .btn-secondarys {
            background-color: black;
            color: white;
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
        }

        .bg-sec {
            background-color: #f8f9fa !important;
        }

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

        .info-card {
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .info-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(220, 38, 38, 0.1);
            border-radius: 8px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .form-section-card {
            border: 1px solid #e0e0e0;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-section-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .form-section-header {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .form-section-body {
            padding: 1rem;
        }

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

            margin-bottom: 0.25rem;
            color: white;
        }

        .modern-modal-title small {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        .modern-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modern-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modern-modal-body {
            padding: 1.25rem;
            max-height: 80vh;
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

            .modal-modern-container {
                max-width: 95%;
                margin: 0 10px;
            }

            .info-card {
                margin-bottom: 1rem;
            }

            .form-section-body {
                padding: 0.75rem;
            }
        }
    </style>
  <script>
    function printQuotationDocument() {
        // Close the modal first
        Livewire.dispatch('closePrintModal');
        
        // Wait a bit for the modal to close
        setTimeout(() => {
            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            
            // Get the quotation content from the current page
            const quotationContent = document.querySelector('#print-area .quotation-minimal').cloneNode(true);
            
            // Create the print HTML
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Quotation {{ $quotation_number }}</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        
                        body {
                            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                            padding: 20px;
                            background: white;
                        }
                        
                        .quotation-minimal {
                            max-width: 1000px;
                            margin: 0 auto;
                            background: white;
                        }
                        
                        .print-header {
                            text-align: center;
                            margin-bottom: 20px;
                            padding-bottom: 10px;
                            border-bottom: 2px solid #dc2626;
                        }
                        
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                        }
                        
                        th, td {
                            padding: 10px;
                            text-align: left;
                            border-bottom: 1px solid #e2e8f0;
                        }
                        
                        th {
                            background: #f8fafc;
                            font-weight: 600;
                        }
                        
                        .text-right {
                            text-align: right;
                        }
                        
                        .total-row {
                            font-weight: bold;
                            border-top: 2px solid #e2e8f0;
                        }
                        
                        @page {
                            size: A4;
                            margin: 1.5cm;
                        }
                        
                        @media print {
                            body {
                                padding: 0;
                            }
                            .no-print {
                                display: none;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${quotationContent.outerHTML}
                    <div class="no-print" style="text-align: center; margin-top: 20px; padding: 10px;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Print / Save as PDF
                        </button>
                        <button onclick="window.close()" style="padding: 10px 20px; margin-left: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Close
                        </button>
                    </div>
                    <script>
                        // Auto-trigger print dialog when window loads
                        window.onload = function() {
                            window.print();
                        };
                        
                        // Close window after printing or cancel
                        window.onafterprint = function() {
                            setTimeout(function() {
                                window.close();
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            
            printWindow.document.close();
        }, 200);
    }
</script>
</div>
