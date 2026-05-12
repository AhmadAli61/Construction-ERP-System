<?php

namespace App\Livewire\Hr;

use Livewire\Component;
use App\Models\Worker;

class AddWorker extends Component
{
    public $workerId;
    public $first_name;  // New property
    public $last_name;   // New property
    public $name;        // Keep this for backward compatibility
    public $email, $phone, $alternate_phone, $address, $city, $state, $zip, $country;
    public $emergency_contact_name, $emergency_contact_phone;
    public $dob, $national_id, $blood_group, $department, $designation, $date_of_joining;
    public $rate = 0;
    public $rate_type = 'hourly';
    public $status = 'active';
    
    // New fields for ID type
    public $id_type = '';
    public $national_id_input = '';
    
    // New fields for medical information
    public $medical_issue_date;
    public $medical_expiry_date;

    public function mount($workerId = null)
    {
        $this->workerId = $workerId;
        
        if ($workerId && $workerId != 0) {
            $worker = Worker::findOrFail($workerId);
            
            // Split the name into first and last name
            $nameParts = explode(' ', $worker->name, 2);
            $this->first_name = $nameParts[0] ?? '';
            $this->last_name = $nameParts[1] ?? '';
            
            // Populate all fields from the worker model
            $this->name = $worker->name;
            $this->email = $worker->email;
            $this->phone = $worker->phone;
            $this->alternate_phone = $worker->alternate_phone;
            $this->address = $worker->address;
            $this->city = $worker->city;
            $this->state = $worker->state;
            $this->zip = $worker->zip;
            $this->country = $worker->country;
            $this->emergency_contact_name = $worker->emergency_contact_name;
            $this->emergency_contact_phone = $worker->emergency_contact_phone;
            $this->dob = $worker->dob;
            $this->blood_group = $worker->blood_group;
            $this->medical_issue_date = $worker->medical_issue_date;
            $this->medical_expiry_date = $worker->medical_expiry_date;
            $this->rate = $worker->rate ?? 0;
            $this->rate_type = $worker->rate_type ?? 'hourly';
            $this->department = $worker->department;
            $this->designation = $worker->designation;
            $this->date_of_joining = $worker->date_of_joining;
            $this->status = $worker->status ?? 'active';
            
            // Parse the existing national_id to populate the dropdown and input
            $this->parseNationalIdForEdit($worker->national_id);
        }
    }
    
    /**
     * Parse the stored national_id format to populate the form fields for editing
     * Format stored: "N.I.E : (ID number)" or "D.N.I : (ID number)" or "Passport : (ID number)"
     */
    private function parseNationalIdForEdit($storedValue)
    {
        if (empty($storedValue)) {
            $this->id_type = '';
            $this->national_id_input = '';
            return;
        }
        
        // Check for N.I.E pattern
        if (strpos($storedValue, 'N.I.E :') === 0) {
            $this->id_type = 'N.I.E';
            $this->national_id_input = trim(str_replace('N.I.E :', '', $storedValue));
        } 
        // Check for D.N.I pattern
        elseif (strpos($storedValue, 'D.N.I :') === 0) {
            $this->id_type = 'D.N.I';
            $this->national_id_input = trim(str_replace('D.N.I :', '', $storedValue));
        }
        // Check for Passport pattern
        elseif (strpos($storedValue, 'Passport :') === 0) {
            $this->id_type = 'Passport';
            $this->national_id_input = trim(str_replace('Passport :', '', $storedValue));
        }
        else {
            // If no pattern matches, treat as legacy data
            $this->id_type = '';
            $this->national_id_input = $storedValue;
        }
    }
    
    /**
     * Format the national ID based on selected type
     * Returns format like: "N.I.E : (ID)" or "D.N.I : (ID)" or "Passport : (ID)"
     * If no ID type selected or no ID entered, returns null
     */
    private function formatNationalId()
    {
        // If no ID type selected or no ID number entered, return null
        if (empty($this->id_type) || empty($this->national_id_input)) {
            return null;
        }
        
        // Format based on selected type
        return $this->id_type . ' : ' . $this->national_id_input;
    }

    protected function rules()
    {
        $uniqueEmail = 'required|email|unique:workers,email';
        if ($this->workerId && $this->workerId != 0) {
            $uniqueEmail .= ',' . $this->workerId;
        }

        return [
            'first_name' => 'required|string|max:255',  // New validation
            'last_name' => 'required|string|max:255',   // New validation
            'email' => $uniqueEmail,
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'id_type' => 'required|string|in:N.I.E,D.N.I,Passport',
            'national_id_input' => 'required|string|max:50',
            'blood_group' => 'nullable|string|max:5',
            'medical_issue_date' => 'nullable|date',
            'medical_expiry_date' => 'nullable|date|after_or_equal:medical_issue_date',
            'rate' => 'required|numeric|min:0',
            'rate_type' => 'required|in:hourly,daily,monthly',
            'department' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:50',
            'date_of_joining' => 'nullable|date',
            'status' => 'required|string|in:active,inactive,terminated',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'first_name' => 'First Name',  // New attribute
            'last_name' => 'Last Name',    // New attribute
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'id_type' => 'ID Type',
            'national_id_input' => 'ID Number',
            'medical_issue_date' => 'Medical Issue Date',
            'medical_expiry_date' => 'Medical Expiry Date',
            'rate' => 'Rate Amount',
            'rate_type' => 'Rate Type',
            'status' => 'Employment Status',
        ];
    }

    protected function messages()
    {
        return [
            'first_name.required' => 'Please enter the worker\'s first name.',
            'last_name.required' => 'Please enter the worker\'s last name.',
            'email.required' => 'Please enter the worker\'s email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Please enter the worker\'s phone number.',
            'id_type.required' => 'Please select an ID type.',
            'national_id_input.required' => 'Please enter the ID number.',
            'medical_expiry_date.after_or_equal' => 'Medical expiry date must be after or equal to the issue date.',
            'rate.required' => 'Please enter the rate amount.',
            'rate.numeric' => 'Please enter a valid numeric amount.',
            'rate.min' => 'Rate amount cannot be negative.',
            'rate_type.required' => 'Please select a rate type.',
            'status.required' => 'Please select employment status.',
        ];
    }

    public function save()
    {
        $this->validate();
        
        // Combine first and last name for database storage
        $this->name = trim($this->first_name . ' ' . $this->last_name);
        
        // Format the national_id for storage
        $formattedNationalId = $this->formatNationalId();

        // Prepare data for saving
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'alternate_phone' => $this->alternate_phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'dob' => $this->dob,
            'national_id' => $formattedNationalId,
            'blood_group' => $this->blood_group,
            'medical_issue_date' => $this->medical_issue_date,
            'medical_expiry_date' => $this->medical_expiry_date,
            'rate' => $this->rate,
            'rate_type' => $this->rate_type,
            'department' => $this->department,
            'designation' => $this->designation,
            'date_of_joining' => $this->date_of_joining,
            'status' => $this->status,
        ];

        try {
            Worker::updateOrCreate(
                ['id' => $this->workerId && $this->workerId != 0 ? $this->workerId : null],
                $data
            );

            session()->flash('message', 'Worker saved successfully.');
            return redirect()->route('hr.workers.list');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving worker: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.hr.add-worker')->layout('layouts.hrmanagerdashboard');
    }
}