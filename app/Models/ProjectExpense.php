<?php
// app/Models/ProjectExpense.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProjectExpense extends Model
{
    protected $fillable = [
        'project_id',
        'category_id',
        'description',
        'amount',
        'expense_date',
        'notes',
        'created_by',
        'expense_type',
        'invoice_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Boot method to handle unique validation
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->invoice_number) {
                $exists = static::where('invoice_number', $model->invoice_number)
                    ->where('id', '!=', $model->id)
                    ->exists();
                
                if ($exists) {
                    throw ValidationException::withMessages([
                        'invoice_number' => 'This invoice number already exists!'
                    ]);
                }
            }
        });
    }
}