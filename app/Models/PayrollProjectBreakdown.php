<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollProjectBreakdown extends Model
{
    protected $fillable = [
        'payroll_id',
        'project_id',
        'days',
        'hours',
        'amount',
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
