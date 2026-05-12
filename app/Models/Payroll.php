<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'payroll_batch_id',
        'worker_id',
        'rate_type',
        'rate_snapshot',
        'total_days',
        'total_hours',
        'gross_amount',
        'advance_deduction',
        'manual_adjustment',
        'net_amount',
    ];

    protected $casts = [
        'rate_snapshot' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'manual_adjustment' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function projectBreakdowns()
    {
        return $this->hasMany(PayrollProjectBreakdown::class);
    }
}
