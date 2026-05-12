<?php

namespace App\Livewire\Admin\SalesInvoice;

use Livewire\Component;
use App\Models\Project;
use App\Models\SaleInvoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class SalesInvoiceForm extends Component
{
    public $invoiceId = null;
    public $invoice_number;
    public $invoice_date;
    public $payment_status = 'unpaid';

    // Company details (hardcoded)
    public $company_name = 'Your company';
    public $company_address = 'Calle Madura 10 02 dr Guipuzkoa, Bergara, Spain';
    public $company_cif = 'B13988001';
    public $company_phone = '+34 662 511 334';
    public $company_email = 'Yourcompany2023@gmail.com';

    // Project/Client details
    public $project_id;
    public $client_name = '';
    public $client_phone = '';
    public $client_address = '';

    // Financial
    public $subtotal = 0;
    public $vat_percentage = 21;
    public $vat_amount = 0;
    public $total = 0;

    // Items
    public $items = [];

    // Service types with their default units
    public $serviceTypes = [
        'Masonry' => ['description' => 'Laying Bricks', 'unit' => 'm²'],
        'Pre-frame Beam' => ['description' => 'Installation of Pre-frame Beam', 'unit' => 'unit'],
        'Install Pre-frame' => ['description' => 'Pre-frame Installation Service', 'unit' => 'unit'],
        'Master Plastering' => ['description' => 'Rasello Masitrao', 'unit' => 'm²'],
        'Rought Plastering' => ['description' => 'Rasello Manchado', 'unit' => 'm²'],
        'Wall And Floor Tiling' => ['description' => 'Tiling Work', 'unit' => 'm²'],
        'Official Labor Hour' => ['description' => 'Official Labor', 'unit' => 'hour'],
        'Laborer (peon) Hour' => ['description' => 'Peon Labor', 'unit' => 'hour'],
        'Painting' => ['description' => 'Painting Service', 'unit' => 'm²'],
        'Demolition' => ['description' => 'Demolition Work', 'unit' => 'm³']
    ];

    // Terms and conditions (hardcoded)
    public $terms_conditions = "1. Payment is due within 30 days of invoice date.\n2. Late payments may incur additional charges of 2% per month.\n3. All work is guaranteed for 6 months against defects.";
    public $exclusions = "1. Bringing materials up to the corresponding floor is not included\n2. Debris removal is not included\n3. Scaffolding and safety equipment not included\n4. Any unforeseen structural repairs not included";
    public $notes = '';

    protected function rules()
    {
        return [
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'payment_status' => 'required|in:paid,unpaid,partial',
            'project_id' => 'required|exists:projects,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'vat_percentage' => 'required|numeric|min:0|max:100',
        ];
    }

    protected function messages()
    {
        return [
            'invoice_number.required' => 'Please enter an invoice number.',
            'invoice_number.unique' => 'This invoice number already exists. Please use a different number.',
            'invoice_date.required' => 'Please select an invoice date.',
            'project_id.required' => 'Please select a project.',
            'client_name.required' => 'Please enter client name.',
            'items.required' => 'Please add at least one item to the invoice.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
        ];
    }

    public function mount($id = null)
    {
        $this->invoice_date = date('Y-m-d');

        // Check if we have an ID (edit mode)
        if ($id && $id !== 'create') {
            $this->loadInvoice($id);
        } else {
            // Create mode - add one empty item
            $this->addItem();
            // Leave invoice number empty for manual entry
            $this->invoice_number = '';
        }
    }

    public function loadInvoice($id)
    {
        $invoice = SaleInvoice::with('items')->find($id);

        if (!$invoice) {
            session()->flash('error', 'Invoice not found!');
            return redirect()->route('admin.sales-invoices.list');
        }

        $this->invoiceId = $invoice->id;
        $this->invoice_number = $invoice->invoice_number;
        $this->invoice_date = $invoice->invoice_date->format('Y-m-d');
        $this->payment_status = $invoice->payment_status;
        $this->project_id = $invoice->project_id;
        $this->client_name = $invoice->client_name;
        $this->client_phone = $invoice->client_phone;
        $this->client_address = $invoice->client_address;
        $this->subtotal = $invoice->subtotal;
        $this->vat_percentage = $invoice->vat_percentage;
        $this->vat_amount = $invoice->vat_amount;
        $this->total = $invoice->total;
        $this->notes = $invoice->notes;

        // Load items
        $this->items = [];
        foreach ($invoice->items as $item) {
            $this->items[] = [
                'service_type' => $item->service_type,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ];
        }

        // Load project data if project is selected
        if ($this->project_id) {
            $this->updatedProjectId($this->project_id);
        }
    }

    public function updatedProjectId($value)
    {
        if ($value) {
            $project = Project::find($value);
            if ($project) {
                $this->client_name = $project->client_name;
                $this->client_phone = $project->client_phone;
                $this->client_address = $project->location;
            }
        }
    }

    public function updatedServiceType($index, $value)
    {
        if (isset($this->serviceTypes[$value])) {
            $this->items[$index]['description'] = $this->serviceTypes[$value]['description'];
            $this->items[$index]['unit'] = $this->serviceTypes[$value]['unit'];
            $this->calculateTotals();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'service_type' => 'Masonry',
            'description' => 'Laying Bricks',
            'unit' => 'm²',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems()
    {
        $this->calculateTotals();
    }

    public function updatedVatPercentage()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->items as $index => &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            $this->subtotal += $item['total'];
        }

        $this->vat_amount = $this->subtotal * ($this->vat_percentage / 100);
        $this->total = $this->subtotal + $this->vat_amount;
    }

    // Optional: Auto-generate invoice number helper
    public function generateInvoiceNumber()
    {
        $latest = SaleInvoice::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;
        $generatedNumber = 'INV-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // Check if generated number is unique
        $exists = SaleInvoice::where('invoice_number', $generatedNumber)->exists();

        if (!$exists) {
            $this->invoice_number = $generatedNumber;
            session()->flash('info', 'Auto-generated invoice number: ' . $generatedNumber);
        } else {
            // If exists, try with next ID
            $this->generateInvoiceNumber();
        }
    }

    public function save()
    {
        // Check for duplicate invoice number
        $existingInvoice = SaleInvoice::where('invoice_number', $this->invoice_number)
            ->when($this->invoiceId, function($query) {
                $query->where('id', '!=', $this->invoiceId);
            })
            ->exists();

        if ($existingInvoice) {
            $this->addError('invoice_number', 'This invoice number already exists. Please use a different number.');
            return;
        }

        $this->validate();

        DB::beginTransaction();

        try {
            // This will either update existing or create new
            $invoice = SaleInvoice::updateOrCreate(
                ['id' => $this->invoiceId],
                [
                    'invoice_number' => $this->invoice_number,
                    'invoice_date' => $this->invoice_date,
                    'payment_status' => $this->payment_status,
                    'company_name' => $this->company_name,
                    'company_address' => $this->company_address,
                    'company_cif' => $this->company_cif,
                    'company_phone' => $this->company_phone,
                    'company_email' => $this->company_email,
                    'project_id' => $this->project_id,
                    'client_name' => $this->client_name,
                    'client_phone' => $this->client_phone,
                    'client_address' => $this->client_address,
                    'subtotal' => $this->subtotal,
                    'vat_percentage' => $this->vat_percentage,
                    'vat_amount' => $this->vat_amount,
                    'total' => $this->total,
                    'terms_conditions' => $this->terms_conditions,
                    'exclusions' => $this->exclusions,
                    'notes' => $this->notes,
                    'created_by' => auth()->id(),
                ]
            );

            // Delete old items and save new ones
            InvoiceItem::where('sale_invoice_id', $invoice->id)->delete();

            foreach ($this->items as $item) {
                InvoiceItem::create([
                    'sale_invoice_id' => $invoice->id,
                    'service_type' => $item['service_type'],
                    'description' => $item['description'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            $message = $this->invoiceId ? 'Invoice updated successfully!' : 'Invoice created successfully!';
            session()->flash('message', $message);

            return redirect()->route('admin.sales-invoices.list');

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Error saving invoice: ' . $e->getMessage());
            \Log::error('Invoice save error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $projects = Project::orderBy('name')->get();

        return view('livewire.admin.sales-invoice.sales-invoice-form', [
            'projects' => $projects,
        ])->layout('layouts.admindashboard');
    }
}
