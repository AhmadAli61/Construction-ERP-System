<?php

namespace App\Livewire\Hr\Projects;

use App\Models\Project;
use App\Models\BudgetItem;
use Livewire\Component;
use Carbon\Carbon;

class AddProject extends Component
{
    // Project fields
    public $name;
    public $project_code;
    public $client_name;
    public $client_phone;
    public $client_email;
    public $client_address;
    public $location;
    public $description;
    public $start_date;
    public $end_date;
    public $status = 'planning';

    // Quotation and VAT fields
    public $quotation_number;
    public $valid_until;
    public $vat_rate = 0;
    public $vat_included = false;

    // Budget items
    public $budgetItems = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'project_code' => 'required|string|unique:projects,project_code',
        'client_name' => 'required|string|max:255',
        'client_phone' => 'nullable|string|max:20',
        'client_email' => 'nullable|email|max:255',
        'client_address' => 'nullable|string',
        'location' => 'nullable|string',
        'description' => 'nullable|string',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:planning,ongoing,on_hold,completed,cancelled',
        'quotation_number' => 'nullable|string|max:50',
        'valid_until' => 'nullable|date',
        'vat_rate' => 'nullable|numeric|min:0|max:100',
        'vat_included' => 'boolean',
    ];

    public function mount()
    {
        // Generate a default quotation number
        $this->quotation_number = 'Q-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $this->valid_until = Carbon::now()->addDays(45)->format('Y-m-d');
        $this->addBudgetItem();
        
        // Generate the project code automatically
        $this->generateProjectCode();
    }

    /**
     * Generate project code in format: P-YYYY-XXX
     * where XXX is sequential number for the year
     */
    protected function generateProjectCode()
    {
        $currentYear = Carbon::now()->year;
        
        // Find the latest project code for the current year
        $latestProject = Project::where('project_code', 'like', "P-{$currentYear}-%")
            ->orderBy('project_code', 'desc')
            ->first();
        
        if ($latestProject) {
            // Extract the number from the latest code
            preg_match('/P-' . $currentYear . '-(\d+)/', $latestProject->project_code, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newNumber = $lastNumber + 1;
        } else {
            // No projects found for this year, start from 1
            $newNumber = 1;
        }
        
        // Format the code with leading zeros (3 digits)
        $this->project_code = sprintf("P-%d-%03d", $currentYear, $newNumber);
    }

    /**
     * Regenerate project code - useful if you want to refresh it
     */
    public function regenerateProjectCode()
    {
        $this->generateProjectCode();
        session()->flash('info', 'Project code regenerated successfully.');
    }

    public function addBudgetItem()
    {
        $this->budgetItems[] = [
            'section' => 'General',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
    }

    public function removeBudgetItem($index)
    {
        if (isset($this->budgetItems[$index])) {
            array_splice($this->budgetItems, $index, 1);
            $this->recalculateTotals();
        }
    }

    public function updatedBudgetItems()
    {
        $this->recalculateTotals();
    }

    public function updatedVatRate()
    {
        $this->recalculateTotals();
    }

    public function updatedVatIncluded()
    {
        $this->recalculateTotals();
    }

    public function recalculateTotals()
    {
        foreach ($this->budgetItems as $index => $item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $this->budgetItems[$index]['total'] = $quantity * $unitPrice;
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->budgetItems)->sum('total');
    }

    public function getVatAmountProperty()
    {
        return $this->getSubtotalProperty() * ($this->vat_rate / 100);
    }

    public function getTotalWithVatProperty()
    {
        $subtotal = $this->getSubtotalProperty();
        if ($this->vat_included) {
            return $subtotal;
        }
        return $subtotal + ($subtotal * ($this->vat_rate / 100));
    }

    public function save()
    {
        // First validate the basic project data
        $this->validate();

        // Recalculate totals to ensure everything is up to date
        $this->recalculateTotals();

        $subtotal = collect($this->budgetItems)->sum('total');
        $vatAmount = $subtotal * ($this->vat_rate / 100);
        $totalWithVat = $this->vat_included ? $subtotal : $subtotal + $vatAmount;

        // Create the project with all fields including VAT
        $project = Project::create([
            'name' => $this->name,
            'project_code' => $this->project_code,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'client_email' => $this->client_email,
            'client_address' => $this->client_address,
            'location' => $this->location,
            'description' => $this->description,
            'contract_value' => $totalWithVat,
            'estimated_cost' => $subtotal,
            'budget_total' => $totalWithVat,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'quotation_number' => $this->quotation_number,
            'valid_until' => $this->valid_until,
            'vat_rate' => $this->vat_rate,
            'vat_included' => $this->vat_included,
        ]);

        // Save budget items
        foreach ($this->budgetItems as $index => $item) {
            if (!empty($item['description'])) {
                BudgetItem::create([
                    'project_id' => $project->id,
                    'section' => $item['section'],
                    'description' => $item['description'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'sort_order' => $index,
                ]);
            }
        }

        session()->flash('message', 'Project created successfully with code: ' . $this->project_code . ' and budget quotation. VAT ' . ($this->vat_rate > 0 ? $this->vat_rate . '% ' . ($this->vat_included ? 'included' : 'added') : 'not applied') . '.');

        // Reset form
        $this->reset();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.hr.projects.add-project')
            ->layout('layouts.hrmanagerdashboard');
    }
}