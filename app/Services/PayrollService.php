<?php

namespace App\Services;

use App\Models\Worker;
use App\Models\Attendance;
use App\Models\WorkerAdvance;
use App\Models\PayrollBatch;
use App\Models\Payroll;
use App\Models\PayrollProjectBreakdown;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollService
{
    const HOURS_PER_DAY = 9;
    const DEFAULT_OVERTIME_MULTIPLIER = 1.5;

    protected $overtimeMultiplier = self::DEFAULT_OVERTIME_MULTIPLIER;

    public function setOvertimeMultiplier($multiplier)
    {
        $this->overtimeMultiplier = $multiplier;
        return $this;
    }

    /**
     * Get real-time earnings for a worker in a given period
     */
    public function getRealTimeEarnings(Worker $worker, $year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get attendances for the period
        $attendances = Attendance::where('worker_id', $worker->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Calculate totals
        $totalHours = $attendances->sum('hours_worked');
        $totalOvertime = $attendances->sum('overtime_hours');

        // For Daily rate workers: Count full days and half days
        if ($worker->rate_type === 'daily') {
            $fullDays = $attendances->where('status', 'present')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();

            $dailyRate = $worker->daily_rate;
            $halfDayRate = $dailyRate / 2;

            $regularPay = ($fullDays * $dailyRate) + ($halfDays * $halfDayRate);
            $overtimePay = 0;

            $totalHours = ($fullDays * self::HOURS_PER_DAY) + ($halfDays * (self::HOURS_PER_DAY / 2));
            $attendanceCount = $fullDays + $halfDays;
        }
        // For Hourly rate workers: Calculate by hours
        else {
            $hourlyRate = $worker->hourly_rate;
            $regularHours = $totalHours - $totalOvertime;

            $regularPay = $regularHours * $hourlyRate;
            $overtimePay = $totalOvertime * ($hourlyRate * $this->overtimeMultiplier);

            $attendanceCount = $attendances->count();
        }

        $grossEarnings = ($regularPay ?? 0) + ($overtimePay ?? 0);

        // Get pending advances
        $pendingAdvances = WorkerAdvance::where('worker_id', $worker->id)
            ->where('status', 'pending')
            ->where('remaining_amount', '>', 0)
            ->orderBy('advance_date', 'asc')
            ->get();

        $totalPendingAdvances = $pendingAdvances->sum('remaining_amount');

        // Project breakdown
        $projectBreakdown = [];
        foreach ($attendances->groupBy('project_id') as $projectId => $projectAttendances) {
            if ($worker->rate_type === 'daily') {
                $fullDaysCount = $projectAttendances->where('status', 'present')->count();
                $halfDaysCount = $projectAttendances->where('status', 'half_day')->count();
                $projectHours = ($fullDaysCount * self::HOURS_PER_DAY) + ($halfDaysCount * (self::HOURS_PER_DAY / 2));
                $projectAmount = ($fullDaysCount * $worker->daily_rate) + ($halfDaysCount * ($worker->daily_rate / 2));
            } else {
                $projectHours = $projectAttendances->sum('hours_worked');
                $projectOvertime = $projectAttendances->sum('overtime_hours');
                $projectRegularHours = $projectHours - $projectOvertime;
                $projectAmount = ($projectRegularHours * $worker->hourly_rate) +
                    ($projectOvertime * $worker->hourly_rate * $this->overtimeMultiplier);
            }

            $projectBreakdown[] = [
                'project' => $projectAttendances->first()->project,
                'days' => $projectAttendances->count(),
                'hours' => $projectHours,
                'amount' => $projectAmount,
            ];
        }

        return [
            'worker' => $worker,
            'from_date' => $startDate,
            'to_date' => $endDate,
            'attendances' => $attendances,
            'total_hours' => $totalHours,
            'regular_hours' => $regularHours ?? $totalHours,
            'overtime_hours' => $totalOvertime,
            'regular_pay' => $regularPay ?? 0,
            'overtime_pay' => $overtimePay ?? 0,
            'gross_earnings' => $grossEarnings,
            'pending_advances' => $totalPendingAdvances,
            'net_earnings' => $grossEarnings - $totalPendingAdvances,
            'attendance_count' => $attendanceCount,
            'full_days' => $fullDays ?? 0,
            'half_days' => $halfDays ?? 0,
            'project_breakdown' => $projectBreakdown,
            'pending_advances_list' => $pendingAdvances,
            'hourly_rate' => $worker->hourly_rate,
            'daily_rate' => $worker->daily_rate,
            'rate_type' => $worker->rate_type,
            'overtime_multiplier_used' => $this->overtimeMultiplier,
        ];
    }

    /**
     * Get current worker's advance balance
     */
    public function getCurrentWorkerBalance($workerId)
    {
        $lastAdvance = WorkerAdvance::where('worker_id', $workerId)
            ->orderBy('id', 'desc')
            ->first();

        return $lastAdvance ? $lastAdvance->running_balance : 0;
    }

    /**
     * Generate payroll for a single worker (basic version)
     */
    public function generateSingleWorkerPayroll(Worker $worker, $year, $month, $overtimeMultiplier = null, $options = [])
    {
        $this->setOvertimeMultiplier($overtimeMultiplier ?? self::DEFAULT_OVERTIME_MULTIPLIER);

        $earnings = $this->getRealTimeEarnings($worker, $year, $month);

        if ($earnings['attendance_count'] == 0) {
            throw new \Exception('No attendance records found for this period.');
        }

        DB::beginTransaction();

        try {
            // Create or get batch
            $batch = PayrollBatch::firstOrCreate(
                [
                    'year' => $year,
                    'month' => $month,
                    'type' => 'single',
                    'worker_id' => $worker->id,
                ],
                [
                    'period_start' => $earnings['from_date'],
                    'period_end' => $earnings['to_date'],
                    'status' => 'draft',
                    'settings' => json_encode([
                        'overtime_multiplier' => $this->overtimeMultiplier,
                        'generated_at' => now()->toDateTimeString(),
                    ]),
                ]
            );

            // Delete existing payroll for this worker in this batch
            Payroll::where('payroll_batch_id', $batch->id)
                ->where('worker_id', $worker->id)
                ->delete();

            // Create payroll record
            $payroll = Payroll::create([
                'payroll_batch_id' => $batch->id,
                'worker_id' => $worker->id,
                'rate_type' => $worker->rate_type,
                'rate_snapshot' => $worker->rate_type === 'daily' ? $worker->daily_rate : $worker->hourly_rate,
                'total_days' => $earnings['attendance_count'],
                'total_hours' => $earnings['total_hours'],
                'overtime_multiplier_used' => $this->overtimeMultiplier,
                'gross_amount' => $earnings['gross_earnings'],
                'advance_deduction' => $earnings['pending_advances'],
                'manual_adjustment' => $options['manual_adjustment'] ?? 0,
                'net_amount' => $earnings['net_earnings'] + ($options['manual_adjustment'] ?? 0),
                'attendance_ids' => json_encode($earnings['attendances']->pluck('id')),
                'advance_ids' => json_encode($earnings['pending_advances_list']->pluck('id')),
                'notes' => $options['notes'] ?? null,
            ]);

            // Create project breakdowns
            foreach ($earnings['project_breakdown'] as $breakdown) {
                PayrollProjectBreakdown::create([
                    'payroll_id' => $payroll->id,
                    'project_id' => $breakdown['project']->id,
                    'days' => $breakdown['days'],
                    'hours' => $breakdown['hours'],
                    'amount' => $breakdown['amount'],
                ]);
            }

            // Mark attendances as payroll_generated
            Attendance::whereIn('id', $earnings['attendances']->pluck('id'))
                ->update(['payroll_generated' => true]);

            // Mark advances as paid
            if ($earnings['pending_advances'] > 0) {
                WorkerAdvance::whereIn('id', $earnings['pending_advances_list']->pluck('id'))
                    ->update(['status' => 'paid']);
            }

            DB::commit();

            return $batch;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payroll generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate payroll with override options (rate type and custom rate)
     */
    public function generateSingleWorkerPayrollWithOverride($worker, $year, $month, $overtimeMultiplier = null, $settings = [])
    {
        $this->setOvertimeMultiplier($overtimeMultiplier ?? self::DEFAULT_OVERTIME_MULTIPLIER);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get attendances
        $attendances = Attendance::where('worker_id', $worker->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        if ($attendances->isEmpty()) {
            throw new \Exception('No attendance records found for this period.');
        }

        // Determine calculation type
        $calculationType = $settings['payroll_type_override'] ?? $worker->rate_type;
        $overrideRate = $settings['override_rate'] ?? null;

        // Calculate based on type
        if ($calculationType === 'daily') {
            $fullDays = $attendances->where('status', 'present')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();
            $dailyRate = $overrideRate ?? $worker->daily_rate;
            $halfDayRate = $dailyRate / 2;

            $grossAmount = ($fullDays * $dailyRate) + ($halfDays * $halfDayRate);
            $totalHours = ($fullDays * 9) + ($halfDays * 4.5);
            $rateSnapshot = $dailyRate;
            $rateType = 'daily';
        } else {
            $totalHours = $attendances->sum('hours_worked');
            $totalOvertime = $attendances->sum('overtime_hours');
            $regularHours = $totalHours - $totalOvertime;
            $hourlyRate = $overrideRate ?? $worker->hourly_rate;

            $regularPay = $regularHours * $hourlyRate;
            $overtimePay = $totalOvertime * ($hourlyRate * $this->overtimeMultiplier);
            $grossAmount = $regularPay + $overtimePay;
            $rateSnapshot = $hourlyRate;
            $rateType = 'hourly';
        }

        // Get advances
        $pendingAdvances = WorkerAdvance::where('worker_id', $worker->id)
            ->where('status', 'pending')
            ->where('remaining_amount', '>', 0)
            ->get();
        $advanceDeduction = $pendingAdvances->sum('remaining_amount');

        $netAmount = $grossAmount - $advanceDeduction + ($settings['manual_adjustment'] ?? 0);

        DB::beginTransaction();

        try {
            $batch = PayrollBatch::firstOrCreate(
                [
                    'year' => $year,
                    'month' => $month,
                    'type' => 'single',
                    'worker_id' => $worker->id,
                ],
                [
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'status' => 'draft',
                    'settings' => json_encode([
                        'calculation_type' => $calculationType,
                        'override_rate' => $overrideRate,
                        'original_rate_type' => $worker->rate_type,
                        'generated_at' => now()->toDateTimeString(),
                    ]),
                ]
            );

            Payroll::where('payroll_batch_id', $batch->id)
                ->where('worker_id', $worker->id)
                ->delete();

            $payroll = Payroll::create([
                'payroll_batch_id' => $batch->id,
                'worker_id' => $worker->id,
                'rate_type' => $rateType,
                'rate_snapshot' => $rateSnapshot,
                'total_days' => $attendances->count(),
                'total_hours' => $totalHours,
                'overtime_multiplier_used' => $this->overtimeMultiplier,
                'gross_amount' => $grossAmount,
                'advance_deduction' => $advanceDeduction,
                'manual_adjustment' => $settings['manual_adjustment'] ?? 0,
                'net_amount' => $netAmount,
                'attendance_ids' => json_encode($attendances->pluck('id')),
                'advance_ids' => json_encode($pendingAdvances->pluck('id')),
                'notes' => $settings['notes'] ?? null,
            ]);

            // Project breakdown
            foreach ($attendances->groupBy('project_id') as $projectId => $projectAttendances) {
                if ($calculationType === 'daily') {
                    $fullDaysCount = $projectAttendances->where('status', 'present')->count();
                    $halfDaysCount = $projectAttendances->where('status', 'half_day')->count();
                    $projectHours = ($fullDaysCount * 9) + ($halfDaysCount * 4.5);
                    $projectAmount = ($fullDaysCount * $dailyRate) + ($halfDaysCount * $halfDayRate);
                } else {
                    $projectHours = $projectAttendances->sum('hours_worked');
                    $projectOvertime = $projectAttendances->sum('overtime_hours');
                    $projectRegularHours = $projectHours - $projectOvertime;
                    $projectAmount = ($projectRegularHours * $hourlyRate) +
                        ($projectOvertime * $hourlyRate * $this->overtimeMultiplier);
                }

                PayrollProjectBreakdown::create([
                    'payroll_id' => $payroll->id,
                    'project_id' => $projectId,
                    'days' => $projectAttendances->count(),
                    'hours' => $projectHours,
                    'amount' => $projectAmount,
                ]);
            }

            Attendance::whereIn('id', $attendances->pluck('id'))
                ->update(['payroll_generated' => true]);

            if ($advanceDeduction > 0) {
                WorkerAdvance::whereIn('id', $pendingAdvances->pluck('id'))
                    ->update(['status' => 'paid']);
            }

            DB::commit();

            return $batch;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payroll generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate payroll with proper advance deduction (deduct from running balance)
     * This is the main method used by the SingleWorkerPayroll component
     */
    public function generateSingleWorkerPayrollWithAdvanceDeduction($worker, $year, $month, $overtimeMultiplier = null, $settings = [])
    {
        $this->setOvertimeMultiplier($overtimeMultiplier ?? self::DEFAULT_OVERTIME_MULTIPLIER);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get attendances
        $attendances = Attendance::where('worker_id', $worker->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        if ($attendances->isEmpty()) {
            throw new \Exception('No attendance records found for this period.');
        }

        // Determine calculation type
        $calculationType = $settings['payroll_type_override'] ?? $worker->rate_type;
        $overrideRate = $settings['override_rate'] ?? null;

        // Calculate gross amount
        if ($calculationType === 'daily') {
            $fullDays = $attendances->where('status', 'present')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();
            $dailyRate = $overrideRate ?? $worker->daily_rate;
            $halfDayRate = $dailyRate / 2;

            $grossAmount = ($fullDays * $dailyRate) + ($halfDays * $halfDayRate);
            $totalHours = ($fullDays * 9) + ($halfDays * 4.5);
            $rateSnapshot = $dailyRate;
            $rateType = 'daily';
        } else {
            $totalHours = $attendances->sum('hours_worked');
            $totalOvertime = $attendances->sum('overtime_hours');
            $regularHours = $totalHours - $totalOvertime;
            $hourlyRate = $overrideRate ?? $worker->hourly_rate;

            $regularPay = $regularHours * $hourlyRate;
            $overtimePay = $totalOvertime * ($hourlyRate * $this->overtimeMultiplier);
            $grossAmount = $regularPay + $overtimePay;
            $rateSnapshot = $hourlyRate;
            $rateType = 'hourly';
        }

        // Get pending advances (only those with remaining_amount > 0)
        $pendingAdvances = WorkerAdvance::where('worker_id', $worker->id)
            ->where('status', 'pending')
            ->where('remaining_amount', '>', 0)
            ->orderBy('advance_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalPending = $pendingAdvances->sum('remaining_amount');

        // Determine advance deduction amount
        $advanceDeductionAmount = 0;
        $advanceIdsToUpdate = [];

        if (($settings['deduct_advance'] ?? false) && $totalPending > 0) {
            $advanceDeductionAmount = $settings['advance_deduction_amount'] ?? $totalPending;

            // Ensure deduction doesn't exceed gross amount or total pending
            $advanceDeductionAmount = min($advanceDeductionAmount, $grossAmount, $totalPending);

            if ($advanceDeductionAmount > 0) {
                $remainingToDeduct = $advanceDeductionAmount;

                foreach ($pendingAdvances as $advance) {
                    if ($remainingToDeduct <= 0) break;

                    $deductFromThis = min($advance->remaining_amount, $remainingToDeduct);
                    $newRemaining = $advance->remaining_amount - $deductFromThis;

                    $advanceIdsToUpdate[] = [
                        'id' => $advance->id,
                        'old_remaining' => $advance->remaining_amount,
                        'new_remaining' => $newRemaining,
                        'deducted' => $deductFromThis
                    ];

                    $remainingToDeduct -= $deductFromThis;
                }
            }
        }

        $netAmount = $grossAmount - $advanceDeductionAmount + ($settings['manual_adjustment'] ?? 0);

        DB::beginTransaction();

        try {
            // Create or get batch
            $batch = PayrollBatch::firstOrCreate(
                [
                    'year' => $year,
                    'month' => $month,
                    'type' => 'single',
                    'worker_id' => $worker->id,
                ],
                [
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'status' => 'draft',
                    'settings' => json_encode([
                        'calculation_type' => $calculationType,
                        'override_rate' => $overrideRate,
                        'original_rate_type' => $worker->rate_type,
                        'advance_deducted' => $advanceDeductionAmount,
                        'generated_at' => now()->toDateTimeString(),
                    ]),
                ]
            );

            // Delete existing payroll for this worker in this batch
            Payroll::where('payroll_batch_id', $batch->id)
                ->where('worker_id', $worker->id)
                ->delete();

            // Create payroll record
            $payroll = Payroll::create([
                'payroll_batch_id' => $batch->id,
                'worker_id' => $worker->id,
                'rate_type' => $rateType,
                'rate_snapshot' => $rateSnapshot,
                'total_days' => $attendances->count(),
                'total_hours' => $totalHours,
                'overtime_multiplier_used' => $this->overtimeMultiplier,
                'gross_amount' => $grossAmount,
                'advance_deduction' => $advanceDeductionAmount,
                'manual_adjustment' => $settings['manual_adjustment'] ?? 0,
                'net_amount' => $netAmount,
                'attendance_ids' => json_encode($attendances->pluck('id')),
                'advance_ids' => json_encode(array_column($advanceIdsToUpdate, 'id')),
                'notes' => $settings['notes'] ?? null,
            ]);

            // Create project breakdowns
            foreach ($attendances->groupBy('project_id') as $projectId => $projectAttendances) {
                if ($calculationType === 'daily') {
                    $fullDaysCount = $projectAttendances->where('status', 'present')->count();
                    $halfDaysCount = $projectAttendances->where('status', 'half_day')->count();
                    $projectHours = ($fullDaysCount * 9) + ($halfDaysCount * 4.5);
                    $projectAmount = ($fullDaysCount * $dailyRate) + ($halfDaysCount * $halfDayRate);
                } else {
                    $projectHours = $projectAttendances->sum('hours_worked');
                    $projectOvertime = $projectAttendances->sum('overtime_hours');
                    $projectRegularHours = $projectHours - $projectOvertime;
                    $projectAmount = ($projectRegularHours * $hourlyRate) +
                        ($projectOvertime * $hourlyRate * $this->overtimeMultiplier);
                }

                PayrollProjectBreakdown::create([
                    'payroll_id' => $payroll->id,
                    'project_id' => $projectId,
                    'days' => $projectAttendances->count(),
                    'hours' => $projectHours,
                    'amount' => $projectAmount,
                ]);
            }

            // Mark attendances as payroll_generated
            Attendance::whereIn('id', $attendances->pluck('id'))
                ->update(['payroll_generated' => true]);

            // Update advance records and create deduction entries
            $currentBalance = $this->getCurrentWorkerBalance($worker->id);

            foreach ($advanceIdsToUpdate as $advanceData) {
                $advance = WorkerAdvance::find($advanceData['id']);

                // Update the original advance
                $advance->update([
                    'remaining_amount' => $advanceData['new_remaining'],
                    'status' => $advanceData['new_remaining'] <= 0 ? 'paid' : 'pending',
                ]);

                // Create a deduction record
                WorkerAdvance::create([
                    'worker_id' => $worker->id,
                    'parent_advance_id' => $advance->id,
                    'amount' => $advanceData['deducted'],
                    'is_deduction' => true,
                    'advance_date' => now(),
                    'notes' => "Deducted from payroll - Batch #{$batch->id} - Period: " . $startDate->format('M Y'),
                    'status' => 'paid',
                    'remaining_amount' => 0,
                    'running_balance' => $currentBalance - $advanceData['deducted'],
                    'deducted_in_payroll_id' => $batch->id,
                    'payroll_generated_at' => now(),
                ]);

                $currentBalance -= $advanceData['deducted'];
            }

            DB::commit();

            return $batch;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payroll generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate payroll for all workers in a period (batch processing)
     */
    public function generatePayrollBatch($year, $month, $overtimeMultiplier = null)
    {
        $this->setOvertimeMultiplier($overtimeMultiplier ?? self::DEFAULT_OVERTIME_MULTIPLIER);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get all workers with attendance in this period
        $workersWithAttendance = Attendance::whereBetween('date', [$startDate, $endDate])
            ->where('payroll_generated', false)
            ->distinct('worker_id')
            ->pluck('worker_id');

        if ($workersWithAttendance->isEmpty()) {
            throw new \Exception('No pending attendance records found for this period.');
        }

        DB::beginTransaction();

        try {
            // Create batch
            $batch = PayrollBatch::create([
                'year' => $year,
                'month' => $month,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'status' => 'draft',
                'type' => 'batch',
                'settings' => json_encode([
                    'overtime_multiplier' => $this->overtimeMultiplier,
                    'workers_count' => $workersWithAttendance->count(),
                    'generated_at' => now()->toDateTimeString(),
                ]),
            ]);

            $totalGross = 0;
            $totalNet = 0;

            foreach ($workersWithAttendance as $workerId) {
                $worker = Worker::find($workerId);
                if (!$worker) continue;

                $earnings = $this->getRealTimeEarnings($worker, $year, $month);

                if ($earnings['attendance_count'] == 0) continue;

                $payroll = Payroll::create([
                    'payroll_batch_id' => $batch->id,
                    'worker_id' => $worker->id,
                    'rate_type' => $worker->rate_type,
                    'rate_snapshot' => $worker->rate_type === 'daily' ? $worker->daily_rate : $worker->hourly_rate,
                    'total_days' => $earnings['attendance_count'],
                    'total_hours' => $earnings['total_hours'],
                    'overtime_multiplier_used' => $this->overtimeMultiplier,
                    'gross_amount' => $earnings['gross_earnings'],
                    'advance_deduction' => $earnings['pending_advances'],
                    'manual_adjustment' => 0,
                    'net_amount' => $earnings['net_earnings'],
                    'attendance_ids' => json_encode($earnings['attendances']->pluck('id')),
                    'advance_ids' => json_encode($earnings['pending_advances_list']->pluck('id')),
                ]);

                foreach ($earnings['project_breakdown'] as $breakdown) {
                    PayrollProjectBreakdown::create([
                        'payroll_id' => $payroll->id,
                        'project_id' => $breakdown['project']->id,
                        'days' => $breakdown['days'],
                        'hours' => $breakdown['hours'],
                        'amount' => $breakdown['amount'],
                    ]);
                }

                // Mark attendances as payroll_generated
                Attendance::whereIn('id', $earnings['attendances']->pluck('id'))
                    ->update(['payroll_generated' => true]);

                // Mark advances as paid
                if ($earnings['pending_advances'] > 0) {
                    WorkerAdvance::whereIn('id', $earnings['pending_advances_list']->pluck('id'))
                        ->update(['status' => 'paid']);
                }

                $totalGross += $earnings['gross_earnings'];
                $totalNet += $earnings['net_earnings'];
            }

            DB::commit();

            return $batch;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Batch payroll generation error: ' . $e->getMessage());
            throw $e;
        }
    }
}
