<?php

namespace App\Livewire\Hr\Payroll;

use Livewire\Component;
use App\Models\Worker;
use App\Models\PayrollBatch;
use App\Models\Attendance;
use App\Models\WorkerAdvance;
use App\Services\PayrollService;
use Carbon\Carbon;

class SingleWorkerPayroll extends Component
{
    public $workers = [];
    public $selectedWorker = null;
    public $selectedMonth = null;
    public $selectedYear = null;
    public $overtimeMultiplier = null;
    public $customNotes = '';
    public $earningsData = null;
    public $existingPayroll = null;
    public $showConfirmation = false;
    public $generatedBatch = null;
    public $manualAdjustment = 0;

    // Advance Deduction Properties
    public $deductAdvance = false;
    public $advanceDeductionAmount = 0;
    public $totalPendingAdvances = 0;
    public $pendingAdvancesList = [];

    // Calendar view
    public $viewMode = 'calendar';
    public $attendanceData = [];

    // Payroll Type Override
    public $payrollType = 'default';
    public $overrideRate = null;
    public $showOverrideWarning = false;

    public function mount()
    {
        $this->workers = Worker::where('status', 'active')->get();
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function updatedSelectedWorker()
    {
        if ($this->selectedWorker) {
            $this->payrollType = 'default';
            $this->overrideRate = null;
            $this->showOverrideWarning = false;
            $this->deductAdvance = false;
            $this->advanceDeductionAmount = 0;
            $this->loadPendingAdvances();
            $this->loadRealTimeEarnings();
            $this->checkExistingPayroll();
            $this->loadAttendanceForCalendar();
        } else {
            $this->earningsData = null;
            $this->existingPayroll = null;
            $this->attendanceData = [];
            $this->totalPendingAdvances = 0;
            $this->pendingAdvancesList = [];
        }
    }

    public function updatedPayrollType()
    {
        $this->showOverrideWarning = $this->payrollType !== 'default';
        $this->loadRealTimeEarnings();
    }

    public function updatedOverrideRate()
    {
        $this->loadRealTimeEarnings();
    }

    public function updatedSelectedMonth()
    {
        if ($this->selectedWorker) {
            $this->loadRealTimeEarnings();
            $this->checkExistingPayroll();
            $this->loadAttendanceForCalendar();
        }
    }

    public function updatedSelectedYear()
    {
        if ($this->selectedWorker) {
            $this->loadRealTimeEarnings();
            $this->checkExistingPayroll();
            $this->loadAttendanceForCalendar();
        }
    }

    public function updatedOvertimeMultiplier()
    {
        if ($this->selectedWorker) {
            $this->loadRealTimeEarnings();
        }
    }

    public function updatedDeductAdvance($value)
    {
        if ($value) {
            $this->advanceDeductionAmount = $this->totalPendingAdvances;
        } else {
            $this->advanceDeductionAmount = 0;
        }
        $this->loadRealTimeEarnings();
    }

    public function updatedAdvanceDeductionAmount()
    {
        if ($this->advanceDeductionAmount > $this->totalPendingAdvances) {
            $this->advanceDeductionAmount = $this->totalPendingAdvances;
            session()->flash('info', 'Advance deduction cannot exceed total pending advances (€' . number_format($this->totalPendingAdvances, 2) . ')');
        }
        if ($this->advanceDeductionAmount < 0) {
            $this->advanceDeductionAmount = 0;
        }
        $this->loadRealTimeEarnings();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'calendar' ? 'summary' : 'calendar';
    }

    public function loadAttendanceForCalendar()
    {
        if (!$this->selectedWorker) return;

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::with('project')
            ->where('worker_id', $this->selectedWorker)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $this->attendanceData = $attendances->mapWithKeys(function ($attendance) {
            return [$attendance->date->day => [
                'status' => $attendance->status,
                'hours_worked' => $attendance->hours_worked,
                'overtime' => $attendance->overtime_hours,
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
                'project' => $attendance->project->name,
                'project_id' => $attendance->project_id,
            ]];
        });
    }

    public function loadPendingAdvances()
    {
        if (!$this->selectedWorker) {
            $this->totalPendingAdvances = 0;
            $this->pendingAdvancesList = [];
            return;
        }

        $pendingAdvances = WorkerAdvance::where('worker_id', $this->selectedWorker)
            ->where('status', 'pending')
            ->where('remaining_amount', '>', 0)
            ->orderBy('advance_date', 'asc')
            ->get();

        $this->totalPendingAdvances = $pendingAdvances->sum('remaining_amount');
        $this->pendingAdvancesList = $pendingAdvances;
    }

    public function loadRealTimeEarnings()
    {
        if (!$this->selectedWorker) return;

        $worker = Worker::find($this->selectedWorker);
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('worker_id', $worker->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Load pending advances
        $this->loadPendingAdvances();

        // Determine actual calculation type
        $calculationType = $this->payrollType;
        if ($calculationType === 'default') {
            $calculationType = $worker->rate_type;
        }

        // Get effective rate (with override)
        $effectiveDailyRate = $worker->daily_rate;
        $effectiveHourlyRate = $worker->hourly_rate;

        if ($this->overrideRate && $this->overrideRate > 0) {
            if ($calculationType === 'daily') {
                $effectiveDailyRate = $this->overrideRate;
                $effectiveHourlyRate = $this->overrideRate / 9;
            } else {
                $effectiveHourlyRate = $this->overrideRate;
                $effectiveDailyRate = $this->overrideRate * 9;
            }
        }

        // Calculate based on selected calculation type
        if ($calculationType === 'daily') {
            $fullDays = $attendances->where('status', 'present')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();
            $halfDayRate = $effectiveDailyRate / 2;
            $regularPay = ($fullDays * $effectiveDailyRate) + ($halfDays * $halfDayRate);
            $overtimePay = 0;
            $totalHours = ($fullDays * 9) + ($halfDays * 4.5);
            $attendanceCount = $fullDays + $halfDays;
            $calculatedRate = $effectiveDailyRate;
            $rateLabel = 'Daily Rate';
            $rateUnit = 'day';

            $projectBreakdown = [];
            foreach ($attendances->groupBy('project_id') as $projectId => $projectAttendances) {
                $fullDaysCount = $projectAttendances->where('status', 'present')->count();
                $halfDaysCount = $projectAttendances->where('status', 'half_day')->count();
                $projectHours = ($fullDaysCount * 9) + ($halfDaysCount * 4.5);
                $projectAmount = ($fullDaysCount * $effectiveDailyRate) + ($halfDaysCount * $halfDayRate);
                $projectBreakdown[] = [
                    'project' => $projectAttendances->first()->project,
                    'days' => $projectAttendances->count(),
                    'full_days' => $fullDaysCount,
                    'half_days' => $halfDaysCount,
                    'hours' => $projectHours,
                    'amount' => $projectAmount,
                ];
            }
        } else {
            $totalHours = $attendances->sum('hours_worked');
            $totalOvertime = $attendances->sum('overtime_hours');
            $regularHours = $totalHours - $totalOvertime;
            $multiplier = $this->overtimeMultiplier ?? ($worker->overtime_rate ?? 1.5);
            $regularPay = $regularHours * $effectiveHourlyRate;
            $overtimePay = $totalOvertime * ($effectiveHourlyRate * $multiplier);
            $attendanceCount = $attendances->count();
            $calculatedRate = $effectiveHourlyRate;
            $rateLabel = 'Hourly Rate';
            $rateUnit = 'hour';

            $projectBreakdown = [];
            foreach ($attendances->groupBy('project_id') as $projectId => $projectAttendances) {
                $projectHours = $projectAttendances->sum('hours_worked');
                $projectOvertime = $projectAttendances->sum('overtime_hours');
                $projectRegularHours = $projectHours - $projectOvertime;
                $projectAmount = ($projectRegularHours * $effectiveHourlyRate) +
                    ($projectOvertime * $effectiveHourlyRate * $multiplier);
                $projectBreakdown[] = [
                    'project' => $projectAttendances->first()->project,
                    'days' => $projectAttendances->count(),
                    'full_days' => $projectAttendances->where('status', 'present')->count(),
                    'half_days' => $projectAttendances->where('status', 'half_day')->count(),
                    'hours' => $projectHours,
                    'amount' => $projectAmount,
                ];
            }
        }

        $grossEarnings = ($regularPay ?? 0) + ($overtimePay ?? 0);

        // Calculate net earnings with advance deduction
        $advanceToDeduct = $this->deductAdvance ? $this->advanceDeductionAmount : 0;
        $netEarnings = $grossEarnings - $advanceToDeduct + $this->manualAdjustment;

        $this->earningsData = [
            'worker' => $worker,
            'worker_rate_type' => $worker->rate_type,
            'calculation_type' => $calculationType,
            'payroll_type_override' => $this->payrollType !== 'default',
            'from_date' => $startDate,
            'to_date' => $endDate,
            'attendances' => $attendances,
            'total_hours' => $totalHours ?? 0,
            'regular_hours' => $regularHours ?? $totalHours,
            'overtime_hours' => $totalOvertime ?? 0,
            'regular_pay' => $regularPay ?? 0,
            'overtime_pay' => $overtimePay ?? 0,
            'gross_earnings' => $grossEarnings,
            'total_pending_advances' => $this->totalPendingAdvances,
            'advance_to_deduct' => $advanceToDeduct,
            'net_earnings' => $netEarnings,
            'attendance_count' => $attendanceCount,
            'full_days' => $fullDays ?? 0,
            'half_days' => $halfDays ?? 0,
            'project_breakdown' => $projectBreakdown,
            'pending_advances_list' => $this->pendingAdvancesList,
            'hourly_rate' => $effectiveHourlyRate,
            'daily_rate' => $effectiveDailyRate,
            'rate_label' => $rateLabel,
            'rate_unit' => $rateUnit,
            'calculated_rate' => $calculatedRate,
            'overtime_multiplier_used' => $multiplier ?? 1.5,
            'original_daily_rate' => $worker->daily_rate,
            'original_hourly_rate' => $worker->hourly_rate,
        ];
    }

    public function checkExistingPayroll()
    {
        if (!$this->selectedWorker) return;

        $this->existingPayroll = PayrollBatch::where('year', $this->selectedYear)
            ->where('month', $this->selectedMonth)
            ->where('type', 'single')
            ->where('worker_id', $this->selectedWorker)
            ->with('payrolls.worker', 'payrolls.projectBreakdowns.project')
            ->first();
    }

    public function generatePayroll()
    {
        $this->validate([
            'selectedWorker' => 'required',
            'selectedMonth' => 'required',
            'selectedYear' => 'required',
        ]);

        try {
            $worker = Worker::find($this->selectedWorker);
            $service = new PayrollService();

            $multiplier = $this->overtimeMultiplier ?? ($worker->overtime_rate ?? 1.5);

            $settings = [
                'notes' => $this->customNotes,
                'manual_adjustment' => $this->manualAdjustment,
                'payroll_type_override' => $this->payrollType !== 'default' ? $this->payrollType : null,
                'override_rate' => $this->overrideRate,
                'deduct_advance' => $this->deductAdvance,
                'advance_deduction_amount' => $this->advanceDeductionAmount,
            ];

            $batch = $service->generateSingleWorkerPayrollWithAdvanceDeduction(
                $worker,
                $this->selectedYear,
                $this->selectedMonth,
                $multiplier,
                $settings
            );

            $this->generatedBatch = $batch;
            $this->showConfirmation = true;
            $this->checkExistingPayroll();
            $this->loadRealTimeEarnings();
            $this->loadAttendanceForCalendar();
            $this->loadPendingAdvances();

            session()->flash('message', 'Payroll generated successfully for ' . $worker->name);
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function removeAdvance($advanceId)
    {
        if (!$this->existingPayroll || !$this->existingPayroll->payrolls->first()) {
            session()->flash('error', 'No payroll found to remove advance from');
            return;
        }

        try {
            $payroll = $this->existingPayroll->payrolls->first();
            $service = new PayrollService();
            $service->removeAdvancesFromPayroll($payroll, [$advanceId]);

            session()->flash('message', 'Advance removed from payroll successfully');
            $this->checkExistingPayroll();
            $this->loadRealTimeEarnings();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updatePayroll()
    {
        if (!$this->existingPayroll || !$this->existingPayroll->payrolls->first()) {
            session()->flash('error', 'No payroll found to update');
            return;
        }

        try {
            $payroll = $this->existingPayroll->payrolls->first();
            $service = new PayrollService();

            $service->updatePayroll($payroll, [
                'manual_adjustment' => $this->manualAdjustment,
                'notes' => $this->customNotes,
            ]);

            session()->flash('message', 'Payroll updated successfully');
            $this->checkExistingPayroll();
            $this->loadRealTimeEarnings();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function debugAttendance()
    {
        if (!$this->selectedWorker) {
            session()->flash('error', 'No worker selected');
            return;
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('worker_id', $this->selectedWorker)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalHours = $attendances->sum('hours_worked');
        $fullDays = $attendances->where('status', 'present')->count();
        $halfDays = $attendances->where('status', 'half_day')->count();

        session()->flash('info', "Debug: {$attendances->count()} records | Full: {$fullDays} | Half: {$halfDays} | Hours: {$totalHours}");
    }

    public function render()
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        return view('livewire.hr.payroll.single-worker-payroll', [
            'months' => $months,
        ])->layout('layouts.hrmanagerdashboard');
    }
}
