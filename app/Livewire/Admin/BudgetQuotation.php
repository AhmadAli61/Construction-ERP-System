<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\BudgetItem;
use Carbon\Carbon;

class BudgetQuotation extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Actual filter properties (applied after search button click)
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Temporary properties for button search
    public $tempSearch = '';
    public $tempStatusFilter = '';

    // Project fields
    public $projectId = null;
    public $quotation_number = '';
    public $name = '';
    public $project_code = '';
    public $client_name = '';
    public $client_phone = '';
    public $client_email = '';
    public $client_address = '';
    public $location = '';
    public $description = '';
    public $valid_until = null;
    public $vat_rate = 0;
    public $vat_included = false;
protected $listeners = ['closePrintModal' => 'closePrintModal'];

    // Budget items
    public $budgetItems = [];

    // Company info
    public $company = [
        'name' => 'Your company 2023 S.L',
        'address' => '20570 Bergara, Gipuzkoa',
        'tax_id' => 'B-13988001',
        'phone' => '662511334',
        'email' => 'Yourcompany2023@gmail.com',
    ];

    // UI state
    public $showForm = false;
    public $isEditing = false;
    public $printMode = false;

    // Search flag
    public $isSearching = false;

    public function mount()
    {
        $this->quotation_number = 'Q-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $this->valid_until = Carbon::now()->addDays(45)->format('Y-m-d');
        $this->addBudgetItem();
    }

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->search = $this->tempSearch;
        $this->statusFilter = $this->tempStatusFilter;
        $this->isSearching = true;
        $this->resetPage();
    }
public function closePrintModal()
{
    $this->printMode = false;
}

    // Reset all filters
    public function resetFilters()
    {
        $this->tempSearch = '';
        $this->tempStatusFilter = '';
        $this->search = '';
        $this->statusFilter = '';
        $this->isSearching = false;
        $this->resetPage();
    }

    // Clear individual filters
    public function clearSearch()
    {
        $this->tempSearch = '';
        $this->search = '';
        $this->resetPage();
    }

    public function clearStatusFilter()
    {
        $this->tempStatusFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function addBudgetItem()
    {
        $this->budgetItems[] = [
            'id' => null,
            'section' => 'General',
            'description' => '',
            'unit' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
            'notes' => '',
        ];
    }

    public function removeBudgetItem($index)
    {
        if (isset($this->budgetItems[$index])) {
            if ($this->budgetItems[$index]['id']) {
                BudgetItem::find($this->budgetItems[$index]['id'])->delete();
            }
            array_splice($this->budgetItems, $index, 1);
            $this->recalculateItemTotals();
        }
    }

    public function updatedBudgetItems()
    {
        $this->recalculateItemTotals();
    }

    public function updatedVatRate()
    {
        $this->dispatch('vat-updated');
    }

    public function updatedVatIncluded()
    {
        $this->dispatch('vat-updated');
    }

    public function recalculateItemTotals()
    {
        foreach ($this->budgetItems as $index => $item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitPrice = floatval($item['unit_price'] ?? 0);
            $this->budgetItems[$index]['total'] = $quantity * $unitPrice;
        }
    }

    public function editProject($id)
    {
        $project = Project::findOrFail($id);
        $this->projectId = $project->id;
        $this->quotation_number = $project->quotation_number;
        $this->name = $project->name;
        $this->project_code = $project->project_code;
        $this->client_name = $project->client_name;
        $this->client_phone = $project->client_phone;
        $this->client_email = $project->client_email;
        $this->client_address = $project->client_address;
        $this->location = $project->location;
        $this->description = $project->description;

        if ($project->valid_until) {
            if ($project->valid_until instanceof Carbon) {
                $this->valid_until = $project->valid_until->format('Y-m-d');
            } else {
                try {
                    $this->valid_until = Carbon::parse($project->valid_until)->format('Y-m-d');
                } catch (\Exception $e) {
                    $this->valid_until = null;
                }
            }
        } else {
            $this->valid_until = null;
        }

        $this->vat_rate = $project->vat_rate;
        $this->vat_included = $project->vat_included;

        $this->budgetItems = [];
        foreach ($project->budgetItems as $item) {
            $this->budgetItems[] = [
                'id' => $item->id,
                'section' => $item->section,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'notes' => $item->notes,
            ];
        }

        if (empty($this->budgetItems)) {
            $this->addBudgetItem();
        }

        $this->isEditing = true;
        $this->showForm = true;
    }

    public function saveQuotation()
    {
        // Validate main project fields
        $this->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'required|string|unique:projects,project_code,' . $this->projectId,
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string',
            'client_email' => 'nullable|email',
            'location' => 'nullable|string',
            'valid_until' => 'nullable|date',
        ]);

        // Validate budget items section fields
        foreach ($this->budgetItems as $index => $item) {
            if (!empty($item['description'])) {
                $this->validate([
                    "budgetItems.{$index}.section" => 'nullable|string|max:50|regex:/^[\p{L}\s]+$/u',
                ], [
                    "budgetItems.{$index}.section.regex" => 'The section field can only contain letters and spaces.',
                    "budgetItems.{$index}.section.max" => 'The section field cannot exceed 50 characters.',
                ]);

                // Additional word count validation
                if (!empty($item['section'])) {
                    $wordCount = str_word_count(trim($item['section']));
                    if ($wordCount > 2) {
                        $this->addError("budgetItems.{$index}.section", 'Section field cannot have more than 2 words.');
                        return;
                    }
                }
            }
        }


        try {
            $this->recalculateItemTotals();

            $subtotal = collect($this->budgetItems)->sum('total');
            $vatAmount = $subtotal * ($this->vat_rate / 100);
            $totalWithVat = $this->vat_included ? $subtotal : $subtotal + $vatAmount;

            $project = Project::updateOrCreate(
                ['id' => $this->projectId],
                [
                    'name' => $this->name,
                    'project_code' => $this->project_code,
                    'client_name' => $this->client_name,
                    'client_phone' => $this->client_phone,
                    'client_email' => $this->client_email,
                    'client_address' => $this->client_address,
                    'location' => $this->location,
                    'description' => $this->description,
                    'estimated_cost' => $subtotal,
                    'contract_value' => $totalWithVat,
                    'budget_total' => $totalWithVat,
                    'valid_until' => $this->valid_until,
                    'quotation_number' => $this->quotation_number,
                    'vat_rate' => $this->vat_rate,
                    'vat_included' => $this->vat_included,
                ]
            );

            foreach ($this->budgetItems as $index => $item) {
                if ($item['description']) {
                    BudgetItem::updateOrCreate(
                        ['id' => $item['id']],
                        [
                            'project_id' => $project->id,
                            'section' => $item['section'],
                            'description' => $item['description'],
                            'unit' => $item['unit'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'total' => $item['total'],
                            'notes' => $item['notes'],
                            'sort_order' => $index,
                        ]
                    );
                }
            }

            session()->flash('message', $this->projectId ? 'Quotation updated successfully!' : 'Quotation created successfully!');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    public function validateSectionField($index)
    {
        if (isset($this->budgetItems[$index]['section'])) {
            $section = trim($this->budgetItems[$index]['section']);

            // Check word count
            $wordCount = str_word_count($section);
            if ($wordCount > 2) {
                $this->addError("budgetItems.{$index}.section", 'Maximum 2 words allowed (e.g., Cementary Material)');
                $this->dispatch('section-validation-error', index: $index);
            } else {
                $this->resetErrorBag("budgetItems.{$index}.section");
            }

            // Check for invalid characters (optional)
            if (!empty($section) && !preg_match('/^[\p{L}\s]+$/u', $section)) {
                $this->addError("budgetItems.{$index}.section", 'Only letters and spaces are allowed');
            }
        }
    }

    public function printQuotation($id)
    {
        $project = Project::with('budgetItems')->findOrFail($id);

        $this->projectId = $project->id;
        $this->quotation_number = $project->quotation_number;
        $this->name = $project->name;
        $this->project_code = $project->project_code;
        $this->client_name = $project->client_name;
        $this->client_phone = $project->client_phone;
        $this->client_email = $project->client_email;
        $this->client_address = $project->client_address;
        $this->location = $project->location;
        $this->description = $project->description;

        if ($project->valid_until) {
            try {
                $this->valid_until = Carbon::parse($project->valid_until)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->valid_until = null;
            }
        } else {
            $this->valid_until = null;
        }

        $this->vat_rate = $project->vat_rate;
        $this->vat_included = $project->vat_included;

        $this->budgetItems = [];
        foreach ($project->budgetItems as $item) {
            $this->budgetItems[] = [
                'id' => $item->id,
                'section' => $item->section,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'notes' => $item->notes,
            ];
        }

        $this->printMode = true;
        $this->dispatch('print-quotation');
    }

    public function resetForm()
    {
        $this->projectId = null;
        $this->quotation_number = 'Q-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $this->name = '';
        $this->project_code = '';
        $this->client_name = '';
        $this->client_phone = '';
        $this->client_email = '';
        $this->client_address = '';
        $this->location = '';
        $this->description = '';
        $this->valid_until = Carbon::now()->addDays(45)->format('Y-m-d');
        $this->vat_rate = 0;
        $this->vat_included = false;
        $this->budgetItems = [];
        $this->addBudgetItem();
        $this->isEditing = false;
        $this->showForm = false;
        $this->printMode = false;
    }

    public function render()
    {
        $query = Project::with('budgetItems');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('project_code', 'like', '%' . $this->search . '%')
                    ->orWhere('client_name', 'like', '%' . $this->search . '%')
                    ->orWhere('quotation_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $projects = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.budget-quotation', [
            'projects' => $projects,
        ])->layout('layouts.admindashboard');
    }
}
