<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Worker;
use App\Models\ProjectExpense;
use App\Models\BudgetItem;

class Project extends Model
{
    protected $fillable = [
        'name',
        'project_code',
        'client_name',
        'client_phone',
        'client_email',
        'client_address',
        'location',
        'description',
        'contract_value',
        'estimated_cost',
        'budget_total',
        'valid_until',
        'quotation_number',
        'vat_rate',
        'vat_included',
        'start_date',
        'end_date',
        'status',
    ];

    public function workers()
    {
        return $this->belongsToMany(Worker::class)
            ->withPivot('assigned_date', 'release_date', 'status')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getNetProfitAttribute()
    {
        return ($this->contract_value ?? 0) - ($this->total_expenses + $this->labor_cost);
    }

    public function getLaborCostAttribute()
    {
        // Get labor cost from payroll breakdowns
        return \DB::table('payroll_project_breakdowns')
            ->where('project_id', $this->id)
            ->sum('amount');
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class)->orderBy('sort_order');
    }

    // Get total with VAT - use the saved budget_total column
    public function getBudgetTotalAttribute()
    {
        return $this->attributes['budget_total'] ?? $this->contract_value ?? $this->estimated_cost ?? 0;
    }

    public function saleInvoices()
    {
        return $this->hasMany(SaleInvoice::class);
    }

    // Optional: Get total invoiced amount
    public function getTotalInvoicedAttribute()
    {
        return $this->saleInvoices()->sum('total');
    }
}