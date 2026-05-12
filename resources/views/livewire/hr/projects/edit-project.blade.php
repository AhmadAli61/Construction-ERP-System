<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Red-Black Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class=" me-3">
                        <i class="fas fa-tasks text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Manage Projects</h3>
                        <p class="text-white-50 small mb-0">View and manage all your construction projects</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('hr.project.add') }}" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add New Project
                    </a>
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

            <!-- Status Cards Section -->
            <div class="card-body pb-0">
                <div class="row g-3">
                    <div class="col-md-2 col-6">
                        <div class="card border-0 shadow-sm text-center status-card planning-card" style="cursor: pointer;" wire:click="filterByStatus('planning')">
                            <div class="card-body p-3">
                                <div class="status-icon mb-2">
                                    <i class="fas fa-calendar-alt fs-2" style="color: #17a2b8;"></i>
                                </div>
                                <h5 class="mb-1 fw-bold">{{ $statusCounts['planning'] ?? 0 }}</h5>
                                <p class="mb-0 small text-muted">Planning</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 col-6">
                        <div class="card border-0 shadow-sm text-center status-card ongoing-card" style="cursor: pointer;" wire:click="filterByStatus('ongoing')">
                            <div class="card-body p-3">
                                <div class="status-icon mb-2">
                                    <i class="fas fa-play-circle fs-2" style="color: #007bff;"></i>
                                </div>
                                <h5 class="mb-1 fw-bold">{{ $statusCounts['ongoing'] ?? 0 }}</h5>
                                <p class="mb-0 small text-muted">Ongoing</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 col-6">
                        <div class="card border-0 shadow-sm text-center status-card onhold-card" style="cursor: pointer;" wire:click="filterByStatus('on_hold')">
                            <div class="card-body p-3">
                                <div class="status-icon mb-2">
                                    <i class="fas fa-pause-circle fs-2" style="color: #ffc107;"></i>
                                </div>
                                <h5 class="mb-1 fw-bold">{{ $statusCounts['on_hold'] ?? 0 }}</h5>
                                <p class="mb-0 small text-muted">On Hold</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 col-6">
                        <div class="card border-0 shadow-sm text-center status-card completed-card" style="cursor: pointer;" wire:click="filterByStatus('completed')">
                            <div class="card-body p-3">
                                <div class="status-icon mb-2">
                                    <i class="fas fa-check-circle fs-2" style="color: #28a745;"></i>
                                </div>
                                <h5 class="mb-1 fw-bold">{{ $statusCounts['completed'] ?? 0 }}</h5>
                                <p class="mb-0 small text-muted">Completed</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 col-6">
                        <div class="card border-0 shadow-sm text-center status-card cancelled-card" style="cursor: pointer;" wire:click="filterByStatus('cancelled')">
                            <div class="card-body p-3">
                                <div class="status-icon mb-2">
                                    <i class="fas fa-times-circle fs-2" style="color: #dc3545;"></i>
                                </div>
                                <h5 class="mb-1 fw-bold">{{ $statusCounts['cancelled'] ?? 0 }}</h5>
                                <p class="mb-0 small text-muted">Cancelled</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 col-6">
                        <div class="card border-0 shadow-sm text-center status-card all-card" style="cursor: pointer;" wire:click="showAllProjects">
                            <div class="card-body p-3">
                                <div class="status-icon mb-2">
                                    <i class="fas fa-folder-open fs-2" style="color: #6c757d;"></i>
                                </div>
                                <h5 class="mb-1 fw-bold">{{ $totalProjectsCount }}</h5>
                                <p class="mb-0 small text-muted">All Projects</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Status Filter Indicator -->
            @if($statusFilter)
                <div class="px-4 pt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-2 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-filter me-2 text-primary"></i>
                            <span class="text-muted">Active Filter:</span>
                            <span class="badge bg-primary ms-2">
                                <i class="fas {{ $statusIcons[$statusFilter] ?? 'fa-chart-pie' }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $statusFilter)) }} Projects
                            </span>
                        </div>
                        <button wire:click="clearStatusFilter" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Clear Filter
                        </button>
                    </div>
                </div>
            @endif

            <!-- Search and Filters Section -->
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search me-1" style="color: #ff0000;"></i>
                            Search Projects
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Search by project name, code, quotation or client..."
                                   wire:model="tempSearch">
                            @if($tempSearch)
                                <button wire:click="clearSearch" class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Searches: Project Name, Code, Quotation Number, Client Name
                        </small>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-chart-pie me-1" style="color: #ff0000;"></i>
                            Filter by Status
                        </label>
                        <div class="input-group">
                            <select class="form-select" wire:model="tempStatusFilter">
                                <option value="">All Statuses</option>
                                <option value="planning">Planning</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @if($tempStatusFilter)
                                <button wire:click="clearStatusFilter" class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-eye me-1" style="color: #ff0000;"></i>
                            Items Per Page
                        </label>
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-semibold invisible" style="visibility: hidden;">
                            <i class="fas fa- me-1" style="color: #ff0000;"></i>
                            Actions
                        </label>
                        <div class="d-flex gap-2 w-100">
                            <button wire:click="performSearch" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-2"></i> Search
                            </button>
                            <button wire:click="resetFilters" class="btn btn-secondary" title="Reset All Filters">
                                <i class="fas fa-undo-alt me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                @if($search || $statusFilter)
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <small class="text-muted me-2">
                                <i class="fas fa-filter me-1"></i>Active filters:
                            </small>
                            @if($search)
                                <span class="badge bg-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Search: {{ $search }}
                                    <button wire:click="clearSearch" class="btn-close btn-close-white ms-2" style="font-size: 8px;"></button>
                                </span>
                            @endif
                            @if($statusFilter)
                                <span class="badge bg-info">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Status: {{ ucfirst(str_replace('_', ' ', $statusFilter)) }}
                                    <button wire:click="clearStatusFilter" class="btn-close btn-close-white ms-2" style="font-size: 8px;"></button>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Projects Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3"><i class="fas fa-barcode me-2"></i> Code</th>
                            <th class="py-3"><i class="fas fa-project-diagram me-2"></i> Project Name</th>
                            <th class="py-3"><i class="fas fa-user me-2"></i> Client</th>
                            <th class="py-3 text-end"><i class="fas fa-dollar-sign me-2"></i> Contract Value</th>
                            <th class="py-3 text-end"><i class="fas fa-calculator me-2"></i> Estimated Cost</th>
                            <th class="py-3"><i class="fas fa-chart-pie me-2"></i> Status</th>
                            <th class="py-3"><i class="fas fa-calendar-alt me-2"></i> Timeline</th>
                            <th class="py-3 text-center"><i class="fas fa-cog me-2"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr class="border-bottom">
                                <td class="fw-semibold">
                                    <span class="badge bg-light text-dark px-3 py-2">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ $project->project_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $project->name }}</div>
                                    @if($project->location)
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ Str::limit($project->location, 40) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div><i class="fas fa-user-circle me-1 text-muted"></i> {{ $project->client_name }}</div>
                                    @if($project->client_phone)
                                        <small class="text-muted"><i class="fas fa-phone me-1"></i> {{ $project->client_phone }}</small>
                                    @endif
                                    @if($project->client_email)
                                        <br><small class="text-muted"><i class="fas fa-envelope me-1"></i> {{ $project->client_email }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold text-success">
                                        € {{ number_format($project->contract_value, 2) }}
                                    </span>
                                    @if($project->vat_rate > 0)
                                        <br>
                                        <small class="text-muted">
                                            @if($project->vat_included)
                                                <i class="fas fa-check-circle text-success"></i> VAT {{ $project->vat_rate }}% Inc.
                                            @else
                                                <i class="fas fa-plus-circle text-warning"></i> +{{ $project->vat_rate }}% VAT
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="text-muted">
                                        € {{ number_format($project->estimated_cost, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'planning' => 'info',
                                            'ongoing' => 'primary',
                                            'on_hold' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusIcons = [
                                            'planning' => 'fa-calendar-alt',
                                            'ongoing' => 'fa-play-circle',
                                            'on_hold' => 'fa-pause-circle',
                                            'completed' => 'fa-check-circle',
                                            'cancelled' => 'fa-times-circle'
                                        ];
                                        $color = $statusColors[$project->status] ?? 'secondary';
                                        $icon = $statusIcons[$project->status] ?? 'fa-circle';
                                    @endphp
                                    <span class="badge bg-{{ $color }} px-3 py-2">
                                        <i class="fas {{ $icon }} me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <i class="fas fa-calendar-day text-muted me-1"></i>
                                        <small>{{ $project->start_date ?: 'Not set' }}</small>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-check text-muted me-1"></i>
                                        <small>{{ $project->end_date ?: 'Not set' }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button wire:click="viewDetails({{ $project->id }})" 
                                                type="button"
                                                class="btn btn-sm btn-info"
                                                style="font-size: 12px; padding: 3px 6px; white-space: nowrap;">
                                            <i class="fas fa-eye me-1"></i> Details
                                        </button>
                                        
                                        <button wire:click="openEditModal({{ $project->id }})" 
                                                type="button"
                                                class="btn btn-sm btn-success"
                                                style="font-size: 12px; padding: 3px 6px; white-space: nowrap;">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>

                                        <button wire:click="confirmDelete({{ $project->id }})" 
                                                type="button"
                                                class="btn btn-sm btn-danger"
                                                style="font-size: 12px; padding: 3px 6px; white-space: nowrap;">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">No projects found</p>
                                        <small>Try adjusting your search or filters</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination with Info -->
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} 
                    of {{ $projects->total() }} projects
                </div>
                <div>
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Project Modal with Budget Items -->
    <div class="modal-overlay @if($editingProject) modal-overlay-active @endif">
        <div class="modal-container @if($editingProject) modal-container-active @endif">
            <div class="modal-dialog-custom modal-xl">
                <div class="modal-content-custom">
                    <div class="modal-header-custom" style="background: linear-gradient(135deg, #28c76f 70%, #f8f8f8 100%);">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-edit me-2"></i> Edit Project
                        </h5>
                        <button type="button" class="btn-close-custom btn-close-white" wire:click="closeEditModal">×</button>
                    </div>
                    <form wire:submit.prevent="updateProject">
                        <div class="modal-body-custom">
                            <!-- Basic Information -->
                            <h6 class="fw-bold mb-3" style="color: #28c76f;">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('editForm.name') is-invalid @enderror" wire:model="editForm.name" required>
                                    @error('editForm.name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Project Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('editForm.project_code') is-invalid @enderror" wire:model="editForm.project_code" required>
                                    @error('editForm.project_code') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Quotation Number</label>
                                    <input type="text" class="form-control" wire:model="editForm.quotation_number">
                                </div>
                            </div>

                            <!-- Budget Items Section -->
                            <div class="mb-4 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <h6 class="fw-bold mb-0" style="color: #28c76f;">
                                        <i class="fas fa-list me-2"></i>Budget Items
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-success" wire:click="addBudgetItem">
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
                                                    <input type="text" class="form-control form-control-sm" wire:model="budgetItems.{{ $index }}.description" placeholder="Description">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" wire:model="budgetItems.{{ $index }}.unit" placeholder="Unit">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" class="form-control form-control-sm text-center" step="0.01" wire:model.live="budgetItems.{{ $index }}.quantity">
                                                </td>
                                                <td>
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
                                                    No budget items added yet.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        @if(count($budgetItems) > 0 && !empty(array_filter(array_column($budgetItems, 'description'))))
                                        <tfoot class="bg-light">
                                            @php
                                                $subtotal = collect($budgetItems)->sum('total');
                                                $vatRate = $editForm['vat_rate'] ?? 0;
                                                $vatAmount = $subtotal * ($vatRate / 100);
                                                $total = ($editForm['vat_included'] ?? false) ? $subtotal : $subtotal + $vatAmount;
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
                                                        <input type="number" class="form-control form-control-sm text-end" step="0.1" wire:model.live="editForm.vat_rate">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-bold">€ {{ number_format($vatAmount, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" wire:model.live="editForm.vat_included" id="editVatIncluded">
                                                        <label class="form-check-label small" for="editVatIncluded">
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
                                @if(($editForm['vat_rate'] ?? 0) > 0)
                                    <div class="mt-2">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <small>
                                                @if($editForm['vat_included'] ?? false)
                                                    <strong>✅ VAT Included:</strong> The prices shown already include <strong>{{ $editForm['vat_rate'] }}% VAT</strong>.
                                                @else
                                                    <strong>⚠️ VAT Added:</strong> <strong>{{ $editForm['vat_rate'] }}% VAT</strong> will be added to the subtotal.
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

                            <!-- Client Information -->
                            <h6 class="fw-bold mb-3 mt-3" style="color: #28c76f;">
                                <i class="fas fa-users me-2"></i>Client Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Client Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('editForm.client_name') is-invalid @enderror" wire:model="editForm.client_name" required>
                                    @error('editForm.client_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Client Phone</label>
                                    <input type="text" class="form-control" wire:model="editForm.client_phone">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Client Email</label>
                                    <input type="email" class="form-control @error('editForm.client_email') is-invalid @enderror" wire:model="editForm.client_email">
                                    @error('editForm.client_email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Client Address</label>
                                    <textarea class="form-control" rows="2" wire:model="editForm.client_address" placeholder="Client's full address"></textarea>
                                </div>
                            </div>

                            <!-- Location & Description -->
                            <h6 class="fw-bold mb-3 mt-3" style="color: #28c76f;">
                                <i class="fas fa-map-marker-alt me-2"></i>Location & Details
                            </h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Location</label>
                                    <textarea class="form-control" rows="2" wire:model="editForm.location" placeholder="Project address or location"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea class="form-control" rows="3" wire:model="editForm.description" placeholder="Project description, scope of work, etc."></textarea>
                                </div>
                            </div>

                            <!-- Financial Summary (Auto-calculated) -->
                            <h6 class="fw-bold mb-3 mt-3" style="color: #28c76f;">
                                <i class="fas fa-chart-line me-2"></i>Financial Summary
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Estimated Cost (Subtotal)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="text" class="form-control" value="{{ number_format(collect($budgetItems)->sum('total'), 2) }}" readonly style="background-color: #f8f9fa;">
                                    </div>
                                    <small class="text-muted">Auto-calculated from budget items</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Contract Value (Total with VAT)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="text" class="form-control" 
                                               value="{{ number_format(($editForm['vat_included'] ?? false) ? collect($budgetItems)->sum('total') : collect($budgetItems)->sum('total') * (1 + ($editForm['vat_rate'] ?? 0) / 100), 2) }}" 
                                               readonly style="background-color: #f8f9fa; font-weight: bold; color: #28a745;">
                                    </div>
                                    <small class="text-muted">
                                        @if(($editForm['vat_rate'] ?? 0) > 0)
                                            @if($editForm['vat_included'] ?? false)
                                                Includes {{ $editForm['vat_rate'] }}% VAT
                                            @else
                                                Excludes {{ $editForm['vat_rate'] }}% VAT
                                            @endif
                                        @else
                                            No VAT applied
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <!-- Timeline -->
                            <h6 class="fw-bold mb-3 mt-3" style="color: #28c76f;">
                                <i class="fas fa-calendar-alt me-2"></i>Project Timeline
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control" wire:model="editForm.start_date">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control" wire:model="editForm.end_date">
                                    @error('editForm.end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Valid Until</label>
                                    <input type="date" class="form-control" wire:model="editForm.valid_until">
                                </div>
                            </div>

                            <!-- Status -->
                            <h6 class="fw-bold mb-3 mt-3" style="color: #28c76f;">
                                <i class="fas fa-tasks me-2"></i>Project Status
                            </h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="editForm.status">
                                        <option value="planning">📋 Planning</option>
                                        <option value="ongoing">⚡ Ongoing</option>
                                        <option value="on_hold">⏸️ On Hold</option>
                                        <option value="completed">✅ Completed</option>
                                        <option value="cancelled">❌ Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer-custom">
                            <button type="button" class="btn btn-secondary" wire:click="closeEditModal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success" style="background: #28c76f; border: none;" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-save me-1"></i> Update Project
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay @if($confirmingDelete) modal-overlay-active @endif">
        <div class="modal-container @if($confirmingDelete) modal-container-active @endif">
            <div class="modal-dialog-custom">
                <div class="modal-content-custom">
                    <div class="modal-header-custom" style="background: linear-gradient(135deg, #dc3545 70%, #faf9f9 100%);">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-exclamation-triangle me-2"></i> Confirm Delete
                        </h5>
                        <button type="button" class="btn-close-custom btn-close-white" wire:click="cancelDelete">×</button>
                    </div>
                    <div class="modal-body-custom text-center">
                        <i class="fas fa-trash-alt text-danger fa-4x mb-3"></i>
                        <h5 class="fw-bold">Are you sure?</h5>
                        <p class="text-muted">You are about to delete the project <strong>{{ $deleteProjectName }}</strong>.</p>
                        <div class="alert alert-danger">
                            <i class="fas fa-info-circle me-2"></i>
                            This action cannot be undone. All associated data (workers, expenses, budget items) will be permanently removed.
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteProject" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-trash-alt me-1"></i> Yes, Delete Project
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-1"></i> Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Details Modal -->
    @if($showDetailsModal && $selectedProject)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8 70%, #d7d7d7 100%); color: white;">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-project-diagram fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-white fw-bold mb-0">{{ $selectedProject->name }}</h5>
                                <small class="opacity-75">Project Code: {{ $selectedProject->project_code }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeDetailsModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="container-fluid">
                            <!-- Summary Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-building fs-4 mb-2" style="color: #17a2b8;"></i>
                                            <div class="small text-muted">Client</div>
                                            <div class="fw-semibold">{{ $selectedProject->client_name }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-dollar-sign fs-4 mb-2" style="color: #17a2b8;"></i>
                                            <div class="small text-muted">Contract Value</div>
                                            <div class="fw-semibold text-success">€ {{ number_format($selectedProject->contract_value, 2) }}</div>
                                            @if($selectedProject->vat_rate > 0)
                                                <small class="text-muted">
                                                    @if($selectedProject->vat_included)
                                                        (VAT {{ $selectedProject->vat_rate }}% Inc.)
                                                    @else
                                                        (+{{ $selectedProject->vat_rate }}% VAT)
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-calculator fs-4 mb-2" style="color: #17a2b8;"></i>
                                            <div class="small text-muted">Estimated Cost</div>
                                            <div class="fw-semibold">€ {{ number_format($selectedProject->estimated_cost, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-chart-line fs-4 mb-2" style="color: #17a2b8;"></i>
                                            <div class="small text-muted">Status</div>
                                            <div class="fw-semibold">
                                                @php
                                                    $statusColors = [
                                                        'planning' => 'info',
                                                        'ongoing' => 'primary',
                                                        'on_hold' => 'warning',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $color = $statusColors[$selectedProject->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">
                                                    {{ ucfirst(str_replace('_', ' ', $selectedProject->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Items Table in Details Modal -->
                            @if($selectedProject->budgetItems && $selectedProject->budgetItems->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                        <i class="fas fa-list fs-5 me-2" style="color: #17a2b8;"></i>
                                        <h6 class="fw-bold mb-0">Budget Items</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Section</th>
                                                    <th>Description</th>
                                                    <th>Unit</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedProject->budgetItems as $item)
                                                <tr>
                                                    <td>{{ $item->section }}</td>
                                                    <td>{{ $item->description }}</td>
                                                    <td>{{ $item->unit ?: '-' }}</td>
                                                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                    <td class="text-end">€ {{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-end fw-bold">€ {{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="5" class="text-end fw-bold">Subtotal</td>
                                                    <td class="text-end fw-bold">€ {{ number_format($selectedProject->estimated_cost, 2) }}</td>
                                                </tr>
                                                @if($selectedProject->vat_rate > 0)
                                                <tr>
                                                    <td colspan="5" class="text-end">
                                                        VAT ({{ $selectedProject->vat_rate }}%)
                                                        @if($selectedProject->vat_included)
                                                            <span class="badge bg-success ms-2">Included</span>
                                                        @else
                                                            <span class="badge bg-warning ms-2">Added</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        € {{ number_format($selectedProject->estimated_cost * ($selectedProject->vat_rate / 100), 2) }}
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr class="table-success">
                                                    <td colspan="5" class="text-end fw-bold fs-5">Total</td>
                                                    <td class="text-end fw-bold fs-5 text-success">€ {{ number_format($selectedProject->contract_value, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Project Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                        <i class="fas fa-info-circle fs-5 me-2" style="color: #17a2b8;"></i>
                                        <h6 class="fw-bold mb-0">Project Information</h6>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Project Name</div>
                                        <div class="fw-semibold fs-5">{{ $selectedProject->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Quotation Number</div>
                                        <div class="fw-semibold">{{ $selectedProject->quotation_number ?: 'Not provided' }}</div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="info-card p-3 border rounded-3">
                                        <div class="small text-muted text-uppercase mb-1">Description</div>
                                        <div>{{ $selectedProject->description ?: 'No description provided' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                        <i class="fas fa-users fs-5 me-2" style="color: #17a2b8;"></i>
                                        <h6 class="fw-bold mb-0">Client Information</h6>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Client Name</div>
                                        <div class="fw-semibold">{{ $selectedProject->client_name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Client Phone</div>
                                        <div>{{ $selectedProject->client_phone ?: 'Not provided' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Client Email</div>
                                        <div>{{ $selectedProject->client_email ?: 'Not provided' }}</div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="info-card p-3 border rounded-3">
                                        <div class="small text-muted text-uppercase mb-1">Client Address</div>
                                        <div>{{ $selectedProject->client_address ?: 'Not provided' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                        <i class="fas fa-map-marker-alt fs-5 me-2" style="color: #17a2b8;"></i>
                                        <h6 class="fw-bold mb-0">Location</h6>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="info-card p-3 border rounded-3">
                                        <div class="small text-muted text-uppercase mb-1">Project Address</div>
                                        <div>{{ $selectedProject->location ?: 'No location specified' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                        <i class="fas fa-calendar-alt fs-5 me-2" style="color: #17a2b8;"></i>
                                        <h6 class="fw-bold mb-0">Project Timeline</h6>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Start Date</div>
                                        <div class="fw-semibold">
                                            <i class="fas fa-calendar-day me-2 text-muted"></i>
                                            {{ $selectedProject->start_date ? \Carbon\Carbon::parse($selectedProject->start_date)->format('d F, Y') : 'Not set' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">End Date</div>
                                        <div class="fw-semibold">
                                            <i class="fas fa-calendar-check me-2 text-muted"></i>
                                            {{ $selectedProject->end_date ? \Carbon\Carbon::parse($selectedProject->end_date)->format('d F, Y') : 'Not set' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="info-card p-3 border rounded-3 h-100">
                                        <div class="small text-muted text-uppercase mb-1">Valid Until</div>
                                        <div class="fw-semibold">
                                            <i class="fas fa-hourglass-end me-2 text-muted"></i>
                                            {{ $selectedProject->valid_until ? \Carbon\Carbon::parse($selectedProject->valid_until)->format('d F, Y') : 'Not set' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Information -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                        <i class="fas fa-clock fs-5 me-2" style="color: #17a2b8;"></i>
                                        <h6 class="fw-bold mb-0">System Information</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card p-3 border rounded-3">
                                        <div class="small text-muted text-uppercase mb-1">Created At</div>
                                        <div>
                                            <i class="fas fa-calendar-plus me-2 text-muted"></i>
                                            {{ $selectedProject->created_at ? \Carbon\Carbon::parse($selectedProject->created_at)->format('d F, Y h:i A') : 'Not available' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card p-3 border rounded-3">
                                        <div class="small text-muted text-uppercase mb-1">Last Updated</div>
                                        <div>
                                            <i class="fas fa-edit me-2 text-muted"></i>
                                            {{ $selectedProject->updated_at ? \Carbon\Carbon::parse($selectedProject->updated_at)->format('d F, Y h:i A') : 'Not available' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailsModal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                        <button type="button" class="btn btn-info" style="background: #17a2b8; border: none;" wire:click="openEditModal({{ $selectedProject->id }})">
                            <i class="fas fa-edit me-1"></i> Edit Project
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
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
        }
        
        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .badge {
            font-weight: 500;
            border-radius: 0.5rem;
        }
        
        .alert {
            border-radius: 0.75rem;
            border: none;
        }
        
        .bg-info {
            background-color: #17a2b8 !important;
        }
        
        .bg-primary {
            background-color: #007bff !important;
        }
        
        .bg-warning {
            background-color: #ffc107 !important;
            color: #000;
        }
        
        .bg-success {
            background-color: #28a745 !important;
        }
        
        .bg-danger {
            background-color: #dc3545 !important;
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
        
        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0);
            visibility: hidden;
            z-index: 1040;
            transition: all 0.2s ease-in-out;
        }
        
        .modal-overlay-active {
            background-color: rgba(0, 0, 0, 0.5);
            visibility: visible;
        }
        
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            visibility: hidden;
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.2s ease-in-out;
        }
        
        .modal-container-active {
            visibility: visible;
            opacity: 1;
            transform: scale(1);
        }
        
        .modal-dialog-custom {
            position: relative;
            width: auto;
            margin: 0.5rem;
            pointer-events: auto;
            max-width: 500px;
        }
        
        .modal-xl .modal-dialog-custom {
            max-width: 1200px;
        }
        
        .modal-content-custom {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 1rem;
            outline: 0;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
        
        .modal-header-custom {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            border-top-left-radius: calc(1rem - 1px);
            border-top-right-radius: calc(1rem - 1px);
        }
        
        .modal-header-custom .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
            font-size: 1.25rem;
            font-weight: 600;
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
        
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .btn-close-white {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }
        
        .btn-close-white:hover {
            opacity: 1;
        }
        
        .badge {
            transition: all 0.2s ease;
        }
        
        .badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .btn-close-custom {
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
            background: transparent;
            border: 0;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            opacity: 0.8;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        
        .btn-close-custom:hover {
            opacity: 1;
        }
        
        .modal-body-custom {
            position: relative;
            flex: 1 1 auto;
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-footer-custom {
            display: flex;
            flex-shrink: 0;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(1rem - 1px);
            border-bottom-left-radius: calc(1rem - 1px);
            gap: 0.5rem;
        }
        
        .status-card {
            transition: all 0.3s ease;
            border-radius: 1rem;
            background: white;
        }
        
        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
        }
        
        .planning-card:hover {
            background: linear-gradient(135deg, #e3f2fd, #fff);
            border-left: 4px solid #17a2b8;
        }
        
        .ongoing-card:hover {
            background: linear-gradient(135deg, #cce5ff, #fff);
            border-left: 4px solid #007bff;
        }
        
        .onhold-card:hover {
            background: linear-gradient(135deg, #fff3cd, #fff);
            border-left: 4px solid #ffc107;
        }
        
        .completed-card:hover {
            background: linear-gradient(135deg, #d4edda, #fff);
            border-left: 4px solid #28a745;
        }
        
        .cancelled-card:hover {
            background: linear-gradient(135deg, #f8d7da, #fff);
            border-left: 4px solid #dc3545;
        }
        
        .all-card:hover {
            background: linear-gradient(135deg, #e2e3e5, #fff);
            border-left: 4px solid #6c757d;
        }
        
        @media (min-width: 576px) {
            .modal-dialog-custom {
                max-width: 500px;
                margin: 1.75rem auto;
            }
            
            .modal-xl .modal-dialog-custom {
                max-width: 1200px;
            }
        }
        
        .modal-body-custom::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-body-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .modal-body-custom::-webkit-scrollbar-thumb {
            background: #e4e2e2;
            border-radius: 3px;
        }
        
        .modal-body-custom::-webkit-scrollbar-thumb:hover {
            background: #cc0000;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 0.5rem;
            }
            
            .btn-group {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-group .btn {
                margin: 0 !important;
            }
            
            .modal-body-custom {
                padding: 1rem;
            }
        }
        
        .info-card {
            background: #ffffff;
            transition: all 0.2s ease;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</div>