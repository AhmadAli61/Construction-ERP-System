<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class BudgetItem extends Model
{
    protected $fillable = [
        'project_id',
        'section',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'total',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Relationship with Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
