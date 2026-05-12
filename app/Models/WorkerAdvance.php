<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkerAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'parent_advance_id',
        'amount',
        'is_deduction',
        'advance_date',
        'notes',
        'status',
        'remaining_amount',
        'running_balance',
        'deducted_in_payroll_id',
        'payroll_generated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'advance_date' => 'date',
        'is_deduction' => 'boolean',
        'payroll_generated_at' => 'datetime',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function parentAdvance()
    {
        return $this->belongsTo(WorkerAdvance::class, 'parent_advance_id');
    }

    public function childAdvances()
    {
        return $this->hasMany(WorkerAdvance::class, 'parent_advance_id');
    }

    public function deductedInPayroll()
    {
        return $this->belongsTo(PayrollBatch::class, 'deducted_in_payroll_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')->where('remaining_amount', '>', 0);
    }

    public function getDeductibleAmountAttribute()
    {
        return $this->remaining_amount ?? 0;
    }
}
