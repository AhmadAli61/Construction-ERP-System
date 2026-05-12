<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'dob',
        'national_id',
        'blood_group',
        'medical_issue_date',
        'medical_expiry_date',
        'rate',
        'rate_type',
        'department',
        'designation',
        'date_of_joining',
        'status',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'date_of_joining' => 'date',
        'medical_issue_date' => 'date',
        'medical_expiry_date' => 'date',
    ];

    // Accessor to check if medical is valid
    public function getIsMedicalValidAttribute()
    {
        if (!$this->medical_expiry_date) {
            return false;
        }
        return $this->medical_expiry_date >= now();
    }

    // Accessor to get medical status
    public function getMedicalStatusAttribute()
    {
        if (!$this->medical_issue_date || !$this->medical_expiry_date) {
            return 'Not Provided';
        }

        if ($this->medical_expiry_date < now()) {
            return 'Expired';
        }

        $daysLeft = now()->diffInDays($this->medical_expiry_date);
        if ($daysLeft <= 30) {
            return 'Expiring Soon (' . $daysLeft . ' days left)';
        }

        return 'Valid';
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withPivot('assigned_date', 'release_date', 'status')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function advances()
    {
        return $this->hasMany(WorkerAdvance::class);
    }

    public function getTotalPendingAdvancesAttribute()
    {
        return $this->advances()->where('status', 'pending')->sum('remaining_amount');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

// Add these methods to your Worker model if they don't exist

public function getHourlyRateAttribute()
{
    if ($this->rate_type === 'hourly') {
        return $this->rate;
    } elseif ($this->rate_type === 'daily') {
        return $this->rate / self::HOURS_PER_DAY; // 9 hours per day
    }
    return $this->rate;
}

public function getDailyRateAttribute()
{
    if ($this->rate_type === 'hourly') {
        return $this->rate * self::HOURS_PER_DAY;
    } elseif ($this->rate_type === 'daily') {
        return $this->rate;
    }
    return $this->rate;
}

// Add constant for hours per day
const HOURS_PER_DAY = 9;
}
