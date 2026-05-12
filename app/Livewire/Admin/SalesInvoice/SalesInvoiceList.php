<?php

namespace App\Livewire\Admin\SalesInvoice;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SaleInvoice;

class SalesInvoiceList extends Component
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
    
    // Search flag
    public $isSearching = false;

    // Perform search when button is clicked
    public function performSearch()
    {
        $this->search = $this->tempSearch;
        $this->statusFilter = $this->tempStatusFilter;
        $this->isSearching = true;
        $this->resetPage();
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

    public function delete($id)
    {
        $invoice = SaleInvoice::findOrFail($id);
        $invoice->delete();
        session()->flash('message', 'Invoice deleted successfully!');
    }

    public function render()
    {
        $invoices = SaleInvoice::with('project')
            ->when($this->search, function($query) {
                $query->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('client_name', 'like', '%' . $this->search . '%')
                    ->orWhere('client_email', 'like', '%' . $this->search . '%')
                    ->orWhere('client_phone', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function($query) {
                $query->where('payment_status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Calculate summary statistics
        $totalSubtotal = $invoices->sum('subtotal');
        $totalVat = $invoices->sum('vat_amount');
        $totalAmount = $invoices->sum('total');
        $totalCount = $invoices->total();

        return view('livewire.admin.sales-invoice.sales-invoice-list', [
            'invoices' => $invoices,
            'totalSubtotal' => $totalSubtotal,
            'totalVat' => $totalVat,
            'totalAmount' => $totalAmount,
            'totalCount' => $totalCount,
        ])->layout('layouts.admindashboard');
    }
}