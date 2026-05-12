<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyPayrollSummary extends Model
{
    protected $fillable = [
        'year',
        'month',
        'total_gross_amount',
        'total_net_amount',
        'total_advances_deducted',
        'total_workers',
        'total_payrolls',
        'single_worker_payrolls',
        'batch_payrolls',
        'notes',
        'saved_by',
        'saved_at',
    ];

    protected $casts = [
        'total_gross_amount' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
        'total_advances_deducted' => 'decimal:2',
        'saved_at' => 'datetime',
    ];

    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getYearMonthAttribute()
    {
        return $this->monthName . ' ' . $this->year;
    }

    public function savedBy()
    {
        return $this->belongsTo(User::class, 'saved_by');
    }
}