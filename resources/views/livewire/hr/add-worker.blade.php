{{-- resources/views/livewire/hr/workers/add-worker.blade.php --}}
<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Red-Black Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-user-tie text-white fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-white">{{ $workerId ? 'Edit Worker' : 'Add New Worker' }}</h3>
                    <p class="text-white-50 small mb-0">{{ $workerId ? 'Update worker information below' : 'Fill in the worker details below' }}</p>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
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

            <form wire:submit.prevent="save">
                <!-- Basic Information Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-info-circle me-2" style="color: #000000;"></i>
                        Basic Information
                    </h6>
                     <div class="row">
                        <!-- First Name Field -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1" style="color: #000000;"></i>
                                First Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   wire:model.defer="first_name"
                                   placeholder="Enter first name">
                            @error('first_name') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <!-- Last Name Field -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1" style="color: #000000;"></i>
                                Last Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   wire:model.defer="last_name"
                                   placeholder="Enter last name">
                            @error('last_name') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                     
                    </div>
                
                </div>

               <!-- Contact Information Section -->
<div class="mb-4">
    <h6 class="fw-bold mb-3 pb-1 border-bottom">
        <i class="fas fa-phone-alt me-2" style="color: #000000;"></i>
        Contact Information
    </h6>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-envelope me-1" style="color: #000000;"></i>
                Email Address
                <span class="text-danger">*</span>
            </label>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   wire:model.defer="email"
                   placeholder="Enter email address">
            @error('email') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-phone me-1" style="color: #000000;"></i>
                Phone Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   wire:model.defer="phone"
                   placeholder="Enter phone number">
            @error('phone') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-phone-alt me-1" style="color: #000000;"></i>
                Alternate Phone
            </label>
            <input type="text" 
                   class="form-control" 
                   wire:model.defer="alternate_phone"
                   placeholder="Enter alternate phone number">
        </div>
    </div>
</div>

                <!-- Address Information Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-map-marker-alt me-2" style="color: #000000;"></i>
                        Address Information
                    </h6>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-home me-1" style="color: #000000;"></i>
                                Street Address
                            </label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   wire:model.defer="address"
                                   placeholder="Enter street address">
                            @error('address') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-city me-1" style="color: #000000;"></i>
                                City
                            </label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   wire:model.defer="city"
                                   placeholder="Enter city">
                            @error('city') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-globe me-1" style="color: #000000;"></i>
                                State/Province
                            </label>
                            <input type="text" 
                                   class="form-control @error('state') is-invalid @enderror" 
                                   wire:model.defer="state"
                                   placeholder="Enter state">
                            @error('state') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-mail-bulk me-1" style="color: #000000;"></i>
                                ZIP/Postal Code
                            </label>
                            <input type="text" 
                                   class="form-control @error('zip') is-invalid @enderror" 
                                   wire:model.defer="zip"
                                   placeholder="Enter ZIP code">
                            @error('zip') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-flag me-1" style="color: #000000;"></i>
                                Country
                            </label>
                            <input type="text" 
                                   class="form-control @error('country') is-invalid @enderror" 
                                   wire:model.defer="country"
                                   placeholder="Enter country">
                            @error('country') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-ambulance me-2" style="color: #000000;"></i>
                        Emergency Contact
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1" style="color: #000000;"></i>
                                Contact Person Name
                            </label>
                            <input type="text" 
                                   class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                   wire:model.defer="emergency_contact_name"
                                   placeholder="Enter emergency contact name">
                            @error('emergency_contact_name') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone me-1" style="color: #000000;"></i>
                                Contact Phone Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                   wire:model.defer="emergency_contact_phone"
                                   placeholder="Enter emergency contact phone">
                            @error('emergency_contact_phone') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Personal Details Section -->
<div class="mb-4">
    <h6 class="fw-bold mb-3 pb-1 border-bottom">
        <i class="fas fa-id-card me-2" style="color: #000000;"></i>
        Personal Details
    </h6>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-calendar-alt me-1" style="color: #000000;"></i>
                Date of Birth
            </label>
            <input type="date" 
                   class="form-control @error('dob') is-invalid @enderror" 
                   wire:model.defer="dob">
            @error('dob') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>

        <!-- ID Type Dropdown -->
        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-id-card me-1" style="color: #000000;"></i>
                ID Type
                <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('id_type') is-invalid @enderror" wire:model.defer="id_type">
                <option value="">Select ID Type</option>
                <option value="N.I.E">N.I.E</option>
                <option value="D.N.I">D.N.I</option>
                <option value="Passport">Passport</option>
            </select>
            @error('id_type') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>

        <!-- National ID Field -->
        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-id-card me-1" style="color: #000000;"></i>
                ID Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('national_id_input') is-invalid @enderror" 
                   wire:model.defer="national_id_input"
                   placeholder="Enter ID number">
            @error('national_id_input') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold">
                <i class="fas fa-tint me-1" style="color: #000000;"></i>
                Blood Group
            </label>
            <select class="form-select @error('blood_group') is-invalid @enderror" wire:model.defer="blood_group">
                <option value="">Select Blood Group</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
            @error('blood_group') 
                <small class="text-danger d-block mt-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small> 
            @enderror
        </div>
    </div>
</div>

                <!-- Medical Information Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-notes-medical me-2" style="color: #000000;"></i>
                        Medical Information
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-plus me-1" style="color: #000000;"></i>
                                Medical Issue Date
                            </label>
                            <input type="date" 
                                   class="form-control @error('medical_issue_date') is-invalid @enderror" 
                                   wire:model.defer="medical_issue_date">
                            <small class="text-muted">Date when medical certificate was issued</small>
                            @error('medical_issue_date') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-times me-1" style="color: #000000;"></i>
                                Medical Expiry Date
                            </label>
                            <input type="date" 
                                   class="form-control @error('medical_expiry_date') is-invalid @enderror" 
                                   wire:model.defer="medical_expiry_date">
                            <small class="text-muted">Date when medical certificate expires</small>
                            @error('medical_expiry_date') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info border-0 bg-light">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>Medical certificate is valid from issue date to expiry date. Please ensure the expiry date is after the issue date.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Details Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3 pb-1 border-bottom">
                        <i class="fas fa-briefcase me-2" style="color: #000000;"></i>
                        Employment Details
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-building me-1" style="color: #000000;"></i>
                                Department
                            </label>
                            <input type="text" 
                                   class="form-control @error('department') is-invalid @enderror" 
                                   wire:model.defer="department"
                                   placeholder="Enter department name">
                            @error('department') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user-tag me-1" style="color: #000000;"></i>
                                Designation
                            </label>
                            <input type="text" 
                                   class="form-control @error('designation') is-invalid @enderror" 
                                   wire:model.defer="designation"
                                   placeholder="Enter job title/designation">
                            @error('designation') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-clock me-1" style="color: #000000;"></i>
                                Rate Type
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('rate_type') is-invalid @enderror" wire:model.defer="rate_type">
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            @error('rate_type') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-1" style="color: #000000;"></i>
                                Rate Amount
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">€</span>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('rate') is-invalid @enderror" 
                                       wire:model.defer="rate"
                                       placeholder="0.00">
                            </div>
                            @error('rate') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-check me-1" style="color: #000000;"></i>
                                Date of Joining
                            </label>
                            <input type="date" 
                                   class="form-control @error('date_of_joining') is-invalid @enderror" 
                                   wire:model.defer="date_of_joining">
                            @error('date_of_joining') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-toggle-on me-1" style="color: #000000;"></i>
                                Employment Status
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model.defer="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="terminated">Terminated</option>
                            </select>
                            @error('status') 
                                <small class="text-danger d-block mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="text-end pt-3 border-top">
                    <a href="{{ route('hr.workers.list') }}" class="btn btn-light me-2">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-save-worker" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-2"></i> {{ $workerId ? 'Update Worker' : 'Save Worker' }}
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i> {{ $workerId ? 'Updating...' : 'Saving...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Custom styling for better visual appeal */
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1);
            outline: none;
        }
        
        /* Fix for input-group focus styling */
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
        
        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
        }
        
        /* Save Worker Button Styling - Red with Black hover */
        .btn-save-worker {
            background: #ff0000;
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-save-worker:hover {
            background: #000000;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .btn-save-worker:active {
            transform: translateY(0);
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
        
        /* Error styling */
        .is-invalid {
            border-color: #dc3545;
        }
        
        .is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1);
        }
        
        /* White text opacity for description */
        .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        /* Background opacity for icon circle */
        .bg-opacity-20 {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Medical info alert styling */
        .alert-info.bg-light {
            background-color: #f8f9fa !important;
            border-left: 4px solid #ff0000;
        }
    </style>
</div>