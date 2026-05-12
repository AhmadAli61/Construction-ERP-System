<?php

namespace App\Livewire\Hr;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance as AttendanceModel;
use App\Models\Project;
use App\Models\Worker;
use Carbon\Carbon;

class Attendance extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form properties
    public $project_id;
    public $worker_id;
    public $date;
    public $check_in;
    public $check_out;
    public $hours_worked;
    public $overtime_hours;
    public $status = 'present';
    public $client_billing_type = 'daily';
    public $client_hours = null;
    public $notes;

    // Edit properties
    public $edit_id;
    public $edit_project_id;
    public $edit_worker_id;
    public $edit_date;
    public $edit_check_in;
    public $edit_check_out;
    public $edit_hours_worked;
    public $edit_overtime_hours;
    public $edit_status;
    public $edit_notes;
    public $editWorkers = [];
    public $edit_worker_name = '';
    public $edit_project_name = '';
    public $edit_client_billing_type;
    public $edit_client_hours;

    // Search filter properties
    public $search_project_id = '';
    public $search_worker_id = '';
    public $search_status = '';
    public $search_date_from = '';
    public $search_date_to = '';
    public $search_hours_min = '';
    public $search_hours_max = '';
    public $search_keyword = '';

    // Flag to trigger search
    public $isSearching = false;

    public $workers = [];

    // Constants for hour rules
    const FULL_DAY_HOURS = 9;
    const HALF_DAY_HOURS = 4.5;
    const MIN_HOURS_PRESENT = 5;
    const MAX_HOURS_PRESENT = 9;
    const MIN_HOURS_HALF_DAY = 1;
    const MAX_HOURS_HALF_DAY = 4.5;

    protected function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'worker_id' => 'required|exists:workers,id',
            'date' => 'required|date',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:present,absent,half_day',
        ];
    }

    protected function editRules()
    {
        return [
            'edit_project_id' => 'required|exists:projects,id',
            'edit_worker_id' => 'required|exists:workers,id',
            'edit_date' => 'required|date',
            'edit_check_in' => 'nullable',
            'edit_check_out' => 'nullable',
            'edit_hours_worked' => 'nullable|numeric|min:0|max:24',
            'edit_overtime_hours' => 'nullable|numeric|min:0',
            'edit_status' => 'required|in:present,absent,half_day',
        ];
    }

    public function mount()
    {
        $this->hours_worked = self::FULL_DAY_HOURS;
        $this->client_billing_type = 'daily';
        $this->client_hours = self::FULL_DAY_HOURS;
        $this->overtime_hours = 0;
        $this->check_in = '08:00';
        $this->check_out = '17:00';
        $this->date = now()->format('Y-m-d');
    }

    /**
     * Apply search filters when search button is clicked
     */
    public function applySearch()
    {
        $this->isSearching = true;
        $this->resetPage();
        session()->flash('message', 'Search applied successfully.');
    }

    /**
     * Reset all search filters
     */
    public function resetFilters()
    {
        $this->search_project_id = '';
        $this->search_worker_id = '';
        $this->search_status = '';
        $this->search_date_from = '';
        $this->search_date_to = '';
        $this->search_hours_min = '';
        $this->search_hours_max = '';
        $this->search_keyword = '';
        $this->isSearching = false;

        $this->resetPage();
        session()->flash('message', 'All filters have been reset.');
    }

    /**
     * Update status and hours based on selection
     */
    public function updatedStatus($value)
    {
        if ($value === 'present') {
            $this->hours_worked = self::FULL_DAY_HOURS;
            // If client billing type is daily, set client hours to 9
            if ($this->client_billing_type === 'daily') {
                $this->client_hours = self::FULL_DAY_HOURS;
            } else {
                $this->client_hours = $this->hours_worked;
            }
        } elseif ($value === 'half_day') {
            $this->hours_worked = self::HALF_DAY_HOURS;
            if ($this->client_billing_type === 'daily') {
                $this->client_hours = self::HALF_DAY_HOURS;
            } else {
                $this->client_hours = $this->hours_worked;
            }
        } elseif ($value === 'absent') {
            $this->hours_worked = 0;
            $this->client_hours = 0;
        }
    }

    public function updatedClientBillingType($value)
    {
        if ($value === 'daily') {
            // Daily billing: set client hours based on status
            if ($this->status === 'present') {
                $this->client_hours = self::FULL_DAY_HOURS;
            } elseif ($this->status === 'half_day') {
                $this->client_hours = self::HALF_DAY_HOURS;
            } else {
                $this->client_hours = 0;
            }
        } else {
            // Hourly billing: set client hours equal to hours_worked
            $this->client_hours = $this->hours_worked;
        }
    }

    /**
     * Validate and adjust hours based on status when user manually edits hours
     */
    public function updatedHoursWorked($value)
    {
        if ($this->status === 'present') {
            if ($value < self::MIN_HOURS_PRESENT) {
                $this->status = 'half_day';
                $this->hours_worked = min($value, self::MAX_HOURS_HALF_DAY);
                session()->flash('info', 'Hours less than ' . self::MIN_HOURS_PRESENT . ' automatically changed to Half Day.');
            } elseif ($value > self::MAX_HOURS_PRESENT) {
                $this->overtime_hours = $value - self::MAX_HOURS_PRESENT;
                $this->hours_worked = self::MAX_HOURS_PRESENT;
                session()->flash('info', 'Hours above ' . self::MAX_HOURS_PRESENT . ' moved to overtime.');
            }
        } elseif ($this->status === 'half_day') {
            if ($value > self::MAX_HOURS_HALF_DAY) {
                $this->status = 'present';
                $this->hours_worked = min($value, self::MAX_HOURS_PRESENT);
                session()->flash('info', 'Hours above ' . self::MAX_HOURS_HALF_DAY . ' automatically changed to Full Day.');
            }
        }

        // Sync client hours for hourly billing
        if ($this->client_billing_type === 'hourly') {
            $this->client_hours = $this->hours_worked;
        }
    }

    public function updatedEditStatus($value)
    {
        if ($value === 'present') {
            $this->edit_hours_worked = self::FULL_DAY_HOURS;
        } elseif ($value === 'half_day') {
            $this->edit_hours_worked = self::HALF_DAY_HOURS;
        } elseif ($value === 'absent') {
            $this->edit_hours_worked = 0;
        }
    }

    public function updatedEditHoursWorked($value)
    {
        if ($this->edit_status === 'present') {
            if ($value < self::MIN_HOURS_PRESENT) {
                $this->edit_status = 'half_day';
                $this->edit_hours_worked = min($value, self::MAX_HOURS_HALF_DAY);
                session()->flash('info', 'Hours less than ' . self::MIN_HOURS_PRESENT . ' automatically changed to Half Day.');
            } elseif ($value > self::MAX_HOURS_PRESENT) {
                $this->edit_overtime_hours = $value - self::MAX_HOURS_PRESENT;
                $this->edit_hours_worked = self::MAX_HOURS_PRESENT;
                session()->flash('info', 'Hours above ' . self::MAX_HOURS_PRESENT . ' moved to overtime.');
            }
        } elseif ($this->edit_status === 'half_day') {
            if ($value > self::MAX_HOURS_HALF_DAY) {
                $this->edit_status = 'present';
                $this->edit_hours_worked = min($value, self::MAX_HOURS_PRESENT);
                session()->flash('info', 'Hours above ' . self::MAX_HOURS_HALF_DAY . ' automatically changed to Full Day.');
            }
        }
    }

    /**
     * Auto-calculate hours when check-in or check-out changes
     */
    public function updatedCheckIn($value)
    {
        if ($value && $this->check_out) {
            $this->calculateHoursFromTimes();
        }
    }

    public function updatedCheckOut($value)
    {
        if ($value && $this->check_in) {
            $this->calculateHoursFromTimes();
        }
    }

    public function updatedEditCheckIn($value)
    {
        if ($value && $this->edit_check_out) {
            $this->calculateEditHoursFromTimes();
        }
    }

    public function updatedEditCheckOut($value)
    {
        if ($value && $this->edit_check_in) {
            $this->calculateEditHoursFromTimes();
        }
    }

    private function calculateHoursFromTimes()
    {
        if (!$this->check_in || !$this->check_out) {
            return;
        }

        try {
            list($inHour, $inMin) = explode(':', $this->check_in);
            list($outHour, $outMin) = explode(':', $this->check_out);

            $inTotalMinutes = ($inHour * 60) + $inMin;
            $outTotalMinutes = ($outHour * 60) + $outMin;

            if ($outTotalMinutes < $inTotalMinutes) {
                $outTotalMinutes += 24 * 60;
            }

            $totalMinutes = $outTotalMinutes - $inTotalMinutes;
            $totalHours = $totalMinutes / 60;

            if ($totalHours > 0) {
                if ($totalHours > self::MAX_HOURS_PRESENT) {
                    $this->hours_worked = self::MAX_HOURS_PRESENT;
                    $this->overtime_hours = round($totalHours - self::MAX_HOURS_PRESENT, 2);

                    if ($this->hours_worked >= self::MIN_HOURS_PRESENT) {
                        $this->status = 'present';
                    } elseif ($this->hours_worked >= self::MIN_HOURS_HALF_DAY) {
                        $this->status = 'half_day';
                    }
                } else {
                    $this->hours_worked = round($totalHours, 2);
                    $this->overtime_hours = 0;

                    if ($totalHours >= self::MIN_HOURS_PRESENT) {
                        $this->status = 'present';
                    } elseif ($totalHours >= self::MIN_HOURS_HALF_DAY) {
                        $this->status = 'half_day';
                    } else {
                        $this->status = 'half_day';
                    }
                }
            }
        } catch (\Exception $e) {
            // If error, don't update
        }
    }

    private function calculateEditHoursFromTimes()
    {
        if (!$this->edit_check_in || !$this->edit_check_out) {
            return;
        }

        try {
            list($inHour, $inMin) = explode(':', $this->edit_check_in);
            list($outHour, $outMin) = explode(':', $this->edit_check_out);

            $inTotalMinutes = ($inHour * 60) + $inMin;
            $outTotalMinutes = ($outHour * 60) + $outMin;

            if ($outTotalMinutes < $inTotalMinutes) {
                $outTotalMinutes += 24 * 60;
            }

            $totalMinutes = $outTotalMinutes - $inTotalMinutes;
            $totalHours = $totalMinutes / 60;

            if ($totalHours > 0) {
                if ($totalHours > self::MAX_HOURS_PRESENT) {
                    $this->edit_hours_worked = self::MAX_HOURS_PRESENT;
                    $this->edit_overtime_hours = round($totalHours - self::MAX_HOURS_PRESENT, 2);

                    if ($this->edit_hours_worked >= self::MIN_HOURS_PRESENT) {
                        $this->edit_status = 'present';
                    } elseif ($this->edit_hours_worked >= self::MIN_HOURS_HALF_DAY) {
                        $this->edit_status = 'half_day';
                    }
                } else {
                    $this->edit_hours_worked = round($totalHours, 2);
                    $this->edit_overtime_hours = 0;

                    if ($totalHours >= self::MIN_HOURS_PRESENT) {
                        $this->edit_status = 'present';
                    } elseif ($totalHours >= self::MIN_HOURS_HALF_DAY) {
                        $this->edit_status = 'half_day';
                    } else {
                        $this->edit_status = 'half_day';
                    }
                }
            }
        } catch (\Exception $e) {
            // If error, don't update
        }
    }

    private function hasFullDayAttendanceOnOtherSite($workerId, $date, $currentProjectId = null, $currentAttendanceId = null)
    {
        $query = AttendanceModel::where('worker_id', $workerId)
            ->where('date', $date)
            ->where(function ($q) {
                $q->where('status', 'present')
                    ->orWhere('hours_worked', '>=', self::MIN_HOURS_PRESENT);
            });

        if ($currentProjectId) {
            $query->where('project_id', '!=', $currentProjectId);
        }

        if ($currentAttendanceId) {
            $query->where('id', '!=', $currentAttendanceId);
        }

        return $query->exists();
    }

    private function getExistingAttendanceCount($workerId, $date, $excludeId = null)
    {
        $query = AttendanceModel::where('worker_id', $workerId)
            ->where('date', $date);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count();
    }

    public function updatedProjectId($value)
    {
        $this->worker_id = null;
        $this->loadWorkers();
    }

    public function updatedEditProjectId($value)
    {
        $this->edit_worker_id = null;
        $this->loadEditWorkers();
    }

    public function resetForm()
    {
        $this->reset([
            'project_id',
            'worker_id',
            'notes'
        ]);
        $this->status = 'present';
        $this->date = now()->format('Y-m-d');
        $this->hours_worked = self::FULL_DAY_HOURS;
        $this->overtime_hours = 0;
        $this->check_in = '08:00';
        $this->check_out = '17:00';
        $this->workers = collect();

        $this->resetPage();

        session()->flash('message', 'Form has been reset.');
    }

    public function loadWorkers()
    {
        if ($this->project_id) {
            $this->workers = Worker::whereHas('projects', function ($query) {
                $query->where('project_worker.project_id', $this->project_id)
                    ->where('project_worker.status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('project_worker.release_date')
                            ->orWhere('project_worker.release_date', '>=', now()->format('Y-m-d'));
                    });
            })->get();
        } else {
            $this->workers = collect();
        }
    }

    public function loadEditWorkers()
    {
        if ($this->edit_project_id) {
            $this->editWorkers = Worker::whereHas('projects', function ($query) {
                $query->where('project_worker.project_id', $this->edit_project_id)
                    ->where('project_worker.status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('project_worker.release_date')
                            ->orWhere('project_worker.release_date', '>=', now()->format('Y-m-d'));
                    });
            })->get();
        } else {
            $this->editWorkers = collect();
        }
    }

    public function save()
    {
        $this->validate();

        $hours = $this->hours_worked ?? 0;
        $overtime = $this->overtime_hours ?? 0;
        $checkIn = $this->check_in;
        $checkOut = $this->check_out;
        $clientHours = $this->client_hours ?? $hours;

        // Apply rules based on status
        if ($this->status === 'present') {
            if ($hours < self::MIN_HOURS_PRESENT) {
                $this->status = 'half_day';
                $hours = max($hours, self::MIN_HOURS_HALF_DAY);
                $clientHours = $this->client_billing_type === 'daily' ? self::HALF_DAY_HOURS : $hours;
            } elseif ($hours > self::MAX_HOURS_PRESENT) {
                $overtime = $hours - self::MAX_HOURS_PRESENT;
                $hours = self::MAX_HOURS_PRESENT;
                $clientHours = $this->client_billing_type === 'daily' ? self::FULL_DAY_HOURS : $hours;
            }
        } elseif ($this->status === 'half_day') {
            if ($hours > self::MAX_HOURS_HALF_DAY) {
                $this->status = 'present';
                $hours = min($hours, self::MAX_HOURS_PRESENT);
                $clientHours = $this->client_billing_type === 'daily' ? self::FULL_DAY_HOURS : $hours;
            } elseif ($hours < self::MIN_HOURS_HALF_DAY && $hours > 0) {
                $hours = self::MIN_HOURS_HALF_DAY;
                $clientHours = $this->client_billing_type === 'daily' ? self::HALF_DAY_HOURS : $hours;
            }
        }

        $existingAttendance = AttendanceModel::where('project_id', $this->project_id)
            ->where('worker_id', $this->worker_id)
            ->where('date', $this->date)
            ->first();

        if ($existingAttendance) {
            session()->flash('error', 'Attendance already exists for this worker on this project and date.');
            return;
        }

        $hasFullDay = $this->hasFullDayAttendanceOnOtherSite($this->worker_id, $this->date);
        $existingCount = $this->getExistingAttendanceCount($this->worker_id, $this->date);

        $isFullDay = ($this->status === 'present' && $hours >= self::MIN_HOURS_PRESENT);

        if ($isFullDay && $existingCount >= 1) {
            session()->flash('error', 'Worker already has attendance on another site. Full-day is only allowed on the first site of the day.');
            return;
        }

        if ($isFullDay && $hasFullDay) {
            session()->flash('error', 'This worker already has a full-day attendance on another site for this date.');
            return;
        }

        AttendanceModel::create([
            'project_id' => $this->project_id,
            'worker_id' => $this->worker_id,
            'date' => $this->date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'hours_worked' => round($hours, 2),
            'overtime_hours' => round($overtime, 2),
            'status' => $this->status,
            'client_billing_type' => $this->client_billing_type,
            'client_hours' => round($clientHours, 2),
            'notes' => $this->notes,
            'payroll_generated' => false,
        ]);

        session()->flash('message', 'Attendance saved successfully.');

        $this->reset(['worker_id', 'notes']);
        $this->hours_worked = self::FULL_DAY_HOURS;
        $this->client_billing_type = 'daily';
        $this->client_hours = self::FULL_DAY_HOURS;
        $this->overtime_hours = 0;
        $this->check_in = '08:00';
        $this->check_out = '17:00';
        $this->status = 'present';

        $this->loadWorkers();
        $this->resetPage();
    }

    public function editAttendance($id)
    {
        $attendance = AttendanceModel::with(['worker', 'project'])->findOrFail($id);

        $this->edit_id = $attendance->id;
        $this->edit_project_id = $attendance->project_id;
        $this->edit_worker_id = $attendance->worker_id;
        $this->edit_date = $attendance->date->format('Y-m-d');
        $this->edit_check_in = $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : null;
        $this->edit_check_out = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : null;
        $this->edit_hours_worked = $attendance->hours_worked ?? self::FULL_DAY_HOURS;
        $this->edit_overtime_hours = $attendance->overtime_hours ?? 0;
        $this->edit_status = $attendance->status;
        $this->edit_client_billing_type = $attendance->client_billing_type ?? 'daily';
        $this->edit_client_hours = $attendance->client_hours ?? $attendance->hours_worked;
        $this->edit_notes = $attendance->notes;
        $this->edit_worker_name = $attendance->worker->name;
        $this->edit_project_name = $attendance->project->name;

        $this->loadEditWorkers();

        $this->dispatch('openModal');
    }

    public function updateAttendance()
    {
        $this->validate($this->editRules());

        $attendance = AttendanceModel::findOrFail($this->edit_id);

        if ($attendance->payroll_generated) {
            session()->flash('error', 'Cannot edit attendance that is already included in payroll.');
            $this->dispatch('closeModal');
            return;
        }

        $hours = $this->edit_hours_worked ?? 0;
        $overtime = $this->edit_overtime_hours ?? 0;
        $checkIn = $this->edit_check_in;
        $checkOut = $this->edit_check_out;
        $clientHours = $this->edit_client_hours ?? $hours;

        // Apply rules based on status
        if ($this->edit_status === 'present') {
            if ($hours < self::MIN_HOURS_PRESENT) {
                $this->edit_status = 'half_day';
                $hours = max($hours, self::MIN_HOURS_HALF_DAY);
                $clientHours = $this->edit_client_billing_type === 'daily' ? self::HALF_DAY_HOURS : $hours;
            } elseif ($hours > self::MAX_HOURS_PRESENT) {
                $overtime = $hours - self::MAX_HOURS_PRESENT;
                $hours = self::MAX_HOURS_PRESENT;
                $clientHours = $this->edit_client_billing_type === 'daily' ? self::FULL_DAY_HOURS : $hours;
            }
        } elseif ($this->edit_status === 'half_day') {
            if ($hours > self::MAX_HOURS_HALF_DAY) {
                $this->edit_status = 'present';
                $hours = min($hours, self::MAX_HOURS_PRESENT);
                $clientHours = $this->edit_client_billing_type === 'daily' ? self::FULL_DAY_HOURS : $hours;
            } elseif ($hours < self::MIN_HOURS_HALF_DAY && $hours > 0) {
                $hours = self::MIN_HOURS_HALF_DAY;
                $clientHours = $this->edit_client_billing_type === 'daily' ? self::HALF_DAY_HOURS : $hours;
            }
        }

        $duplicateExists = AttendanceModel::where('project_id', $this->edit_project_id)
            ->where('worker_id', $this->edit_worker_id)
            ->where('date', $this->edit_date)
            ->where('id', '!=', $this->edit_id)
            ->exists();

        if ($duplicateExists) {
            session()->flash('error', 'An attendance record already exists for this worker on this project and date.');
            $this->dispatch('closeModal');
            return;
        }

        $isFullDay = ($this->edit_status === 'present' && $hours >= self::MIN_HOURS_PRESENT);
        $hasFullDay = $this->hasFullDayAttendanceOnOtherSite(
            $this->edit_worker_id,
            $this->edit_date,
            $this->edit_project_id,
            $this->edit_id
        );
        $existingCount = $this->getExistingAttendanceCount($this->edit_worker_id, $this->edit_date, $this->edit_id);

        if ($isFullDay && $existingCount >= 1) {
            session()->flash('error', 'Worker already has attendance on another site.');
            $this->dispatch('closeModal');
            return;
        }

        if ($isFullDay && $hasFullDay) {
            session()->flash('error', 'This worker already has a full-day attendance on another site for this date.');
            $this->dispatch('closeModal');
            return;
        }

        $attendance->update([
            'project_id' => $this->edit_project_id,
            'worker_id' => $this->edit_worker_id,
            'date' => $this->edit_date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'hours_worked' => round($hours, 2),
            'overtime_hours' => round($overtime, 2),
            'status' => $this->edit_status,
            'client_billing_type' => $this->edit_client_billing_type,
            'client_hours' => round($clientHours, 2),
            'notes' => $this->edit_notes,
        ]);

        session()->flash('message', 'Attendance updated successfully.');
        $this->resetEditForm();
        $this->dispatch('closeModal');
        $this->resetPage();
    }

    /**
     * Prepare data for delete confirmation
     */
    public function prepareDeleteConfirmation()
    {
        if ($this->edit_id) {
            $attendance = AttendanceModel::with(['worker', 'project'])->find($this->edit_id);
            if ($attendance) {
                $this->edit_worker_name = $attendance->worker->name;
                $this->edit_project_name = $attendance->project->name;
                $this->edit_date = $attendance->date->format('Y-m-d');
            }
        }
        
        $this->dispatch('confirmDelete');
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance()
    {
        if (!$this->edit_id) {
            session()->flash('error', 'No attendance record selected for deletion.');
            $this->dispatch('closeDeleteModal');
            return;
        }

        $attendance = AttendanceModel::find($this->edit_id);
        
        if (!$attendance) {
            session()->flash('error', 'Attendance record not found.');
            $this->dispatch('closeDeleteModal');
            return;
        }

        // Check if attendance is already included in payroll
        if ($attendance->payroll_generated) {
            session()->flash('error', 'Cannot delete attendance that is already included in payroll. Please delete the payroll record first if you want to delete this attendance.');
            $this->dispatch('closeDeleteModal');
            return;
        }

        // Delete the attendance record
        $attendance->delete();

        session()->flash('message', 'Attendance record deleted successfully.');
        
        // Reset the edit form and close modals
        $this->resetEditForm();
        $this->dispatch('closeDeleteModal');
        $this->dispatch('closeModal');
        
        // Refresh the records list
        $this->resetPage();
    }

    /**
     * Clear delete modal data
     */
    public function clearDeleteModal()
    {
        $this->edit_id = null;
        $this->edit_worker_name = '';
        $this->edit_project_name = '';
        $this->edit_date = null;
    }

    public function resetEditForm()
    {
        $this->reset([
            'edit_id',
            'edit_project_id',
            'edit_worker_id',
            'edit_date',
            'edit_check_in',
            'edit_check_out',
            'edit_hours_worked',
            'edit_overtime_hours',
            'edit_notes',
            'edit_worker_name',
            'edit_project_name'
        ]);
        $this->edit_status = 'present';
        $this->editWorkers = collect();
    }

    public function render()
    {
        $projects = Project::all();

        // Get all workers for search filter
        $allWorkers = Worker::orderBy('name')->get();

        if ($this->project_id && $this->workers->isEmpty()) {
            $this->loadWorkers();
        }

        // Build query with filters - ONLY APPLY FILTERS IF isSearching IS TRUE
        $query = AttendanceModel::with(['worker', 'project']);

        if ($this->isSearching) {
            // Filter by project
            if ($this->search_project_id) {
                $query->where('project_id', $this->search_project_id);
            }

            // Filter by worker
            if ($this->search_worker_id) {
                $query->where('worker_id', $this->search_worker_id);
            }

            // Filter by status
            if ($this->search_status) {
                $query->where('status', $this->search_status);
            }

            // Filter by date range
            if ($this->search_date_from) {
                $query->where('date', '>=', $this->search_date_from);
            }
            if ($this->search_date_to) {
                $query->where('date', '<=', $this->search_date_to);
            }

            // Filter by hours worked range
            if ($this->search_hours_min !== '') {
                $query->where('hours_worked', '>=', (float)$this->search_hours_min);
            }
            if ($this->search_hours_max !== '') {
                $query->where('hours_worked', '<=', (float)$this->search_hours_max);
            }

            // Keyword search (searches in notes, worker name, project name)
            if ($this->search_keyword) {
                $query->where(function ($q) {
                    $q->where('notes', 'like', '%' . $this->search_keyword . '%')
                        ->orWhereHas('worker', function ($workerQuery) {
                            $workerQuery->where('name', 'like', '%' . $this->search_keyword . '%');
                        })
                        ->orWhereHas('project', function ($projectQuery) {
                            $projectQuery->where('name', 'like', '%' . $this->search_keyword . '%');
                        });
                });
            }
        }

        // Order by latest created first (most recent at the top)
        $records = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.hr.attendance', [
            'projects' => $projects,
            'workers' => $this->workers,
            'records' => $records,
            'allWorkers' => $allWorkers,
        ])->layout('layouts.hrmanagerdashboard');
    }
}