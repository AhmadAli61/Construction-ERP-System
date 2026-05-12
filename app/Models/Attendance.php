<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Worker;
use App\Models\Project;

class Attendance extends Model
{
    protected $fillable = [
        'project_id',
        'worker_id',
        'date',
        'check_in',
        'check_out',
        'hours_worked',
        'overtime_hours',
        'status',
        'client_billing_type',  // NEW
        'client_hours',
        'notes',
        'payroll_generated',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'client_hours' => 'decimal:2',  // NEW
        'payroll_generated' => 'boolean',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeNotInPayroll(Builder $query)
    {
        return $query->where('payroll_generated', false);
    }

    public function scopeInDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
