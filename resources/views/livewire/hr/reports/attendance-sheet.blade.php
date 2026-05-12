{{-- resources/views/livewire/hr/reports/attendance-sheet.blade.php --}}
<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-calendar-alt text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Attendance Sheet</h3>
                        <p class="text-white-50 small mb-0">Comprehensive worker attendance tracking & analytics</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light" wire:click="exportToExcel">
                        <i class="fas fa-file-excel me-2" style="color: #28a745;"></i>
                        Export to Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body pt-4">
            @if(session()->has('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filters Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-light border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-filter me-2" style="color: #ff0000;"></i>
                Filter Attendance
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                <i class="fas fa-undo-alt me-1"></i> Reset All Filters
            </button>
        </div>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="performSearch">
            <div class="row g-3 pt-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-building me-1" style="color: #ff0000;"></i>
                        Project (Optional)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-building"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedProjectId">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @if($tempSelectedProjectId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearProjectFilter">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    <small class="text-muted">Filter workers by project first</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-user me-1" style="color: #ff0000;"></i>
                        Worker
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedWorkerId" required>
                            <option value="">Select Worker</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->designation ?: 'No Designation' }})</option>
                            @endforeach
                        </select>
                        @if($tempSelectedWorkerId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearWorkerFilter">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar-month me-1" style="color: #ff0000;"></i>
                        Month
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedMonth">
                            @foreach($months as $key => $month)
                                <option value="{{ $key }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar-year me-1" style="color: #ff0000;"></i>
                        Year
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <select class="form-select" wire:model="tempSelectedYear">
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-1">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-line me-1" style="color: #ff0000;"></i>
                        View
                    </label>
                    <select class="form-select" wire:model.live="viewMode">
                        <option value="calendar">Calendar</option>
                        <option value="list">List</option>
                        <option value="chart">Chart</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="col-md-12">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border: none;">
                            <i class="fas fa-search me-2"></i>
                            Search Attendance
                        </button>
                        <button type="button" class="btn btn-secondary px-4" wire:click="resetFilters">
                            <i class="fas fa-undo-alt me-2"></i>
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if($isSearching && ($selectedWorkerId || $selectedProjectId))
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">
                        <i class="fas fa-filter me-1"></i>Active filters:
                    </small>
                    @if($selectedProjectId)
                        <span class="badge bg-primary">
                            <i class="fas fa-building me-1"></i>
                            Project: {{ $projects->firstWhere('id', $selectedProjectId)?->name }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearProjectFilter"></button>
                        </span>
                    @endif
                    @if($selectedWorkerId)
                        <span class="badge bg-success">
                            <i class="fas fa-user me-1"></i>
                            Worker: {{ $workers->firstWhere('id', $selectedWorkerId)?->name }}
                            <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 8px;" wire:click="clearWorkerFilter"></button>
                        </span>
                    @endif
                    @if($selectedMonth)
                        <span class="badge bg-info">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Month: {{ $months[$selectedMonth] }}
                        </span>
                    @endif
                    @if($selectedYear)
                        <span class="badge bg-secondary">
                            <i class="fas fa-calendar me-1"></i>
                            Year: {{ $selectedYear }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

            @if($selectedWorkerId && $workerInfo)
                <!-- Worker Info & Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle p-3" style="background: rgba(255, 0, 0, 0.1);">
                                            <i class="fas fa-user-circle fa-2x" style="color: #ff0000;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Worker Details</h6>
                                        <h5 class="mb-0 fw-bold">{{ $workerInfo->name }}</h5>
                                        <small class="text-muted">{{ $workerInfo->designation ?: 'No Designation' }}</small>
                                        <div class="mt-2">
                                            <small><i class="fas fa-envelope me-1"></i> {{ $workerInfo->email }}</small><br>
                                            <small><i class="fas fa-phone me-1"></i> {{ $workerInfo->phone ?: 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Attendance Rate</p>
                                        <h3 class="mb-0 fw-bold">{{ $attendanceRate }}%</h3>
                                        <small class="text-muted">of total days</small>
                                        <div class="mt-1">
                                            <small>{{ number_format($totalActualAttendance, 1) }} / {{ $totalPossibleDays }} days</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(40, 167, 69, 0.1);">
                                        <i class="fas fa-chart-line fa-2x" style="color: #28a745;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Total Hours</p>
                                        <h3 class="mb-0 fw-bold">{{ number_format($totalHoursWorked, 2) }}</h3>
                                        <small class="text-muted">hours worked</small>
                                        <div class="mt-2">
                                            <small>Avg: <strong>{{ number_format($averageHoursPerDay, 2) }}</strong> hrs/day</small>
                                            <br>
                                            <small>OT: <strong>{{ number_format($totalOvertime, 2) }}</strong> hrs</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(255, 193, 7, 0.1);">
                                        <i class="fas fa-clock fa-2x" style="color: #ffc107;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Monthly Summary</p>
                                        <h3 class="mb-0 fw-bold">{{ $totalWorkingDays }}</h3>
                                        <small class="text-muted">working days</small>
                                        <div class="mt-2">
                                            <small><i class="fas fa-check-circle text-success"></i> Present: {{ $totalPresent }}</small><br>
                                            <small><i class="fas fa-adjust text-warning"></i> Half: {{ $totalHalfDay }}</small><br>
                                            <small><i class="fas fa-times-circle text-danger"></i> Absent: {{ $totalAbsent }}</small><br>
                                            <small><i class="fas fa-building me-1"></i> Projects: {{ $totalProjectsWorked }}</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(23, 162, 184, 0.1);">
                                        <i class="fas fa-chart-pie fa-2x" style="color: #17a2b8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Worked This Month -->
@if($workerInfo && $workerInfo->monthly_projects && $workerInfo->monthly_projects->count() > 0)
    <div class="alert alert-light border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-building text-primary me-2"></i>
            <strong>Projects worked in {{ $months[$selectedMonth] }} {{ $selectedYear }}:</strong>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($workerInfo->monthly_projects as $project)
                <span class="badge bg-secondary p-2">
                    <i class="fas fa-building me-1"></i>
                    {{ $project->name }}: {{ $project->days_worked }} day{{ $project->days_worked != 1 ? 's' : '' }}
                </span>
            @endforeach
        </div>
    </div>
@endif

                <!-- Attendance Display Section -->
                @if($viewMode == 'calendar')
                    <!-- Calendar View - Enhanced with Fixed Size Boxes -->
                   <!-- Calendar View - Enhanced with Notes Display -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light border-0 py-3">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-calendar-alt me-2" style="color: #ff0000;"></i>
            Attendance Calendar - {{ $months[$selectedMonth] }} {{ $selectedYear }}
            @if($selectedProjectId)
                <span class="badge bg-info ms-2">Filtered by Project</span>
            @endif
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="attendance-calendar-wrapper">
            <table class="attendance-calendar-table">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $firstDayOfMonth = Carbon\Carbon::create($selectedYear, $selectedMonth, 1);
                        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
                        $daysInMonth = $firstDayOfMonth->daysInMonth;
                        $currentDay = 1;
                    @endphp

                    @for($week = 0; $week < 6; $week++)
                        @if($currentDay > $daysInMonth)
                            @break
                        @endif
                        <tr>
                            @for($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++)
                                @php
                                    $showDay = ($week == 0 && $dayOfWeek < $startDayOfWeek) ? false : true;
                                    $attendanceRecord = $attendanceData->firstWhere('date.day', $currentDay);

                                    if($showDay && $currentDay <= $daysInMonth) {
                                        $status = $attendanceRecord ? $attendanceRecord['status'] : 'not_recorded';
                                        $hours = $attendanceRecord ? $attendanceRecord['hours_worked'] : 0;
                                        $overtime = $attendanceRecord ? $attendanceRecord['overtime'] : 0;
                                        $project = $attendanceRecord ? $attendanceRecord['project'] : null;
                                        $checkIn = $attendanceRecord ? $attendanceRecord['check_in'] : null;
                                        $notes = $attendanceRecord ? $attendanceRecord['notes'] : null;
                                        $notesPreview = $notes ? (strlen($notes) > 30 ? substr($notes, 0, 30) . '...' : $notes) : null;

                                        $statusClass = '';
                                        $statusIcon = '';
                                        $statusBg = '';

                                        switch($status) {
                                            case 'present':
                                                $statusClass = 'status-present';
                                                $statusIcon = 'fa-check-circle';
                                                $statusBg = '#d4edda';
                                                break;
                                            case 'absent':
                                                $statusClass = 'status-absent';
                                                $statusIcon = 'fa-times-circle';
                                                $statusBg = '#f8d7da';
                                                break;
                                            case 'half_day':
                                                $statusClass = 'status-half';
                                                $statusIcon = 'fa-adjust';
                                                $statusBg = '#fff3cd';
                                                break;
                                            default:
                                                $statusClass = 'status-not-recorded';
                                                $statusIcon = 'fa-question-circle';
                                                $statusBg = '#ffffff';
                                        }
                                    }
                                @endphp

                                @if($showDay && $currentDay <= $daysInMonth)
                                    <td class="calendar-day-cell {{ $statusClass }}" 
                                        style="background-color: {{ $statusBg }};"
                                        data-notes="{{ $notes ? e($notes) : '' }}"
                                        data-date="{{ $firstDayOfMonth->copy()->day($currentDay)->format('M d, Y') }}"
                                        data-status="{{ $status }}">
                                        <div class="calendar-day-header">
                                            <span class="day-number">{{ $currentDay }}</span>
                                            <i class="fas {{ $statusIcon }} status-icon"></i>
                                        </div>
                                        
                                        @if($attendanceRecord && $status != 'absent')
                                            <div class="calendar-day-details">
                                                <div class="hours-info">
                                                    <i class="fas fa-clock"></i>
                                                    <span>{{ number_format($hours, 1) }}h</span>
                                                    @if($overtime > 0)
                                                        <span class="overtime-badge">+{{ number_format($overtime, 1) }}</span>
                                                    @endif
                                                </div>
                                                @if($project)
                                                    <div class="project-info" title="{{ $project }}">
                                                        <i class="fas fa-building"></i>
                                                        <span>{{ Str::limit($project, 15) }}</span>
                                                    </div>
                                                @endif
                                                @if($checkIn)
                                                    <div class="checkin-info">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                        <span>{{ Carbon\Carbon::parse($checkIn)->format('h:i A') }}</span>
                                                    </div>
                                                @endif
                                                @if($notes)
                                                    <div class="notes-info" title="{{ $notes }}">
                                                        <i class="fas fa-sticky-note"></i>
                                                        <span class="notes-preview">{{ $notesPreview }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($status == 'absent')
                                            <div class="calendar-day-details">
                                                <div class="absent-text">
                                                    <i class="fas fa-user-slash"></i>
                                                    <span>Absent</span>
                                                </div>
                                                @if($notes)
                                                    <div class="notes-info notes-absent" title="{{ $notes }}">
                                                        <i class="fas fa-sticky-note"></i>
                                                        <span class="notes-preview">{{ $notesPreview }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="calendar-day-details">
                                                <div class="not-recorded-text">
                                                    <i class="fas fa-clock"></i>
                                                    <span>No record</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    @php $currentDay++ @endphp
                                @else
                                    <td class="calendar-day-cell empty-cell"></td>
                                @endif
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

                @elseif($viewMode == 'list')
                    <!-- List View -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                                Detailed Attendance List - {{ $months[$selectedMonth] }} {{ $selectedYear }}
                                @if($selectedProjectId)
                                    <span class="badge bg-info ms-2">Filtered by Project</span>
                                @endif
                            </h6>
                            <div class="text-muted small">
                                <i class="fas fa-chart-line me-1"></i>
                                Total: {{ $attendanceData->where('status', '!=', 'not_recorded')->count() }} records
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Date</th>
                                        <th class="py-3">Day</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Check In</th>
                                        <th class="py-3">Check Out</th>
                                        <th class="py-3">Hours Worked</th>
                                        <th class="py-3">Overtime</th>
                                        <th class="py-3">Project</th>
                                        <th class="py-3">Project Code</th>
                                        <th class="py-3">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendanceData as $record)
                                        @php
                                            $statusColor = [
                                                'present' => 'success',
                                                'absent' => 'danger',
                                                'half_day' => 'warning',
                                                'not_recorded' => 'secondary'
                                            ][$record['status']];

                                            $statusIcon = [
                                                'present' => 'fa-check-circle',
                                                'absent' => 'fa-times-circle',
                                                'half_day' => 'fa-adjust',
                                                'not_recorded' => 'fa-question-circle'
                                            ][$record['status']];
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $record['date']->format('M d, Y') }}</strong>
                                            </td>
                                            <td>{{ $record['dayName'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $statusColor }} px-3 py-2">
                                                    <i class="fas {{ $statusIcon }} me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $record['status'])) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($record['check_in'])
                                                    {{ Carbon\Carbon::parse($record['check_in'])->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record['check_out'])
                                                    {{ Carbon\Carbon::parse($record['check_out'])->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">
                                                {{ number_format($record['hours_worked'], 2) }} hrs
                                            </td>
                                            <td>
                                                @if($record['overtime'] > 0)
                                                    <span class="badge bg-info">
                                                        {{ number_format($record['overtime'], 2) }} hrs
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record['project'])
                                                    <small>{{ $record['project'] }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record['project_code'])
                                                    <small class="text-muted">{{ $record['project_code'] }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record['notes'])
                                                    <small class="text-muted">{{ Str::limit($record['notes'], 30) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No attendance records found for this month</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                @else
                    <!-- Chart View -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light border-0 py-3">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-chart-bar me-2" style="color: #ff0000;"></i>
                                        Daily Status Distribution
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailyStatusChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light border-0 py-3">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-chart-pie me-2" style="color: #ff0000;"></i>
                                        Attendance Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="attendancePieChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light border-0 py-3">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-chart-line me-2" style="color: #ff0000;"></i>
                                        Hours Worked Trend
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="hoursTrendChart" style="height: 350px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

          @else
    <!-- No Worker Selected -->
    <div class="text-center py-5">
        <div class="text-muted">
            <i class="fas fa-user-slash fa-4x mb-3 opacity-50"></i>
            <h5>No Worker Selected</h5>
            <p>Please select a worker from the filters above and click "Search Attendance" to view their attendance sheet</p>
            <div class="mt-3">
                <i class="fas fa-arrow-up text-danger me-2"></i>
                <small class="text-muted">Use the filters above to get started</small>
            </div>
        </div>
    </div>
@endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            setTimeout(() => {
                updateCharts();
            }, 500);
        });

        function updateCharts() {
            const dailyCtx = document.getElementById('dailyStatusChart')?.getContext('2d');
            if (dailyCtx && window.dailyStatusChart) {
                window.dailyStatusChart.destroy();
            }

            const pieCtx = document.getElementById('attendancePieChart')?.getContext('2d');
            if (pieCtx && window.attendancePieChart) {
                window.attendancePieChart.destroy();
            }

            const hoursCtx = document.getElementById('hoursTrendChart')?.getContext('2d');
            if (hoursCtx && window.hoursTrendChart) {
                window.hoursTrendChart.destroy();
            }

            @if($viewMode == 'chart' && $selectedWorkerId)
                const monthStats = @json($monthStats);

                if (dailyCtx) {
                    window.dailyStatusChart = new Chart(dailyCtx, {
                        type: 'bar',
                        data: {
                            labels: monthStats.labels,
                            datasets: [
                                {
                                    label: 'Present',
                                    data: monthStats.present,
                                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                    borderColor: '#28a745',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Absent',
                                    data: monthStats.absent,
                                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                    borderColor: '#dc3545',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Half Day',
                                    data: monthStats.half_day,
                                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                                    borderColor: '#ffc107',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                        }
                    });
                }

                if (pieCtx) {
                    window.attendancePieChart = new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Present', 'Absent', 'Half Day'],
                            datasets: [{
                                data: [{{ $totalPresent }}, {{ $totalAbsent }}, {{ $totalHalfDay }}],
                                backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = {{ $totalPresent + $totalAbsent + $totalHalfDay }};
                                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                if (hoursCtx) {
                    window.hoursTrendChart = new Chart(hoursCtx, {
                        type: 'line',
                        data: {
                            labels: monthStats.labels,
                            datasets: [{
                                label: 'Hours Worked',
                                data: monthStats.hours,
                                backgroundColor: 'rgba(255, 0, 0, 0.1)',
                                borderColor: '#ff0000',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, title: { display: true, text: 'Hours' } } }
                        }
                    });
                }
            @endif
        }

        Livewire.hook('element.updated', () => {
            setTimeout(() => updateCharts(), 100);
        });
    </script>
    @endpush

    <style>
        /* Calendar Styles - Fixed Size Boxes */
        .attendance-calendar-wrapper {
            overflow-x: auto;
        }

        .attendance-calendar-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .attendance-calendar-table th,
        .attendance-calendar-table td {
            border: 1px solid #e0e0e0;
            padding: 0;
            vertical-align: top;
        }

        .attendance-calendar-table th {
            background-color: #f8f9fa;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .calendar-day-cell {
            width: 14.2857%;
            height: 130px;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }

        .calendar-day-cell:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1;
            border-radius: 8px;
        }

        .calendar-day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .day-number {
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }

        .status-icon {
            font-size: 14px;
        }

        .status-present .status-icon {
            color: #28a745;
        }

        .status-absent .status-icon {
            color: #dc3545;
        }

        .status-half .status-icon {
            color: #ffc107;
        }

        .status-not-recorded .status-icon {
            color: #adb5bd;
        }

        .calendar-day-details {
            padding: 8px 10px;
            font-size: 11px;
        }

        .hours-info {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
            font-weight: 500;
            color: #495057;
        }

        .hours-info i {
            font-size: 10px;
            color: #6c757d;
        }

        .overtime-badge {
            background-color: #ffc107;
            color: #000;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            margin-left: 4px;
        }

        .project-info {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
            color: #6c757d;
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .project-info i {
            font-size: 9px;
            flex-shrink: 0;
        }

        .project-info span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .checkin-info {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #28a745;
            font-size: 10px;
            font-weight: 500;
        }

        .checkin-info i {
            font-size: 9px;
        }
        /* Add to existing styles */
.input-group .btn-outline-secondary {
    border-color: #e0e0e0;
}

.input-group .btn-outline-secondary:hover {
    background-color: #f8f9fa;
    border-color: #e0e0e0;
}

.btn-primary {
    background: linear-gradient(135deg, #ff0000, #cc0000);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #cc0000, #990000);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

.btn-secondary {
    background: #6c757d;
    border: none;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

.badge {
    transition: all 0.2s ease;
}

.badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.btn-close-white:hover {
    opacity: 1;
}
/* Override Bootstrap's default focus outline to match your red theme */
.form-control:focus,
.form-select:focus,
.input-group-text:focus,
.input-group .form-control:focus,
.input-group .form-select:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1) !important;
    outline: none;
}
/* Notes styling */
.notes-info {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 6px;
    padding-top: 4px;
    border-top: 1px dashed rgba(0, 0, 0, 0.1);
    font-size: 10px;
    color: #6c757d;
    cursor: help;
}

.notes-info i {
    font-size: 9px;
    flex-shrink: 0;
    color: #ff0000;
}

.notes-preview {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-style: italic;
}

.notes-absent {
    border-top-color: rgba(220, 53, 69, 0.3);
}

.notes-absent i {
    color: #dc3545;
}

/* Tooltip for notes */
.notes-tooltip {
    position: fixed;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    max-width: 300px;
    word-wrap: break-word;
    z-index: 10000;
    pointer-events: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: opacity 0.2s ease;
}

.notes-tooltip::before {
    content: '';
    position: absolute;
    top: -5px;
    left: 10px;
    border-width: 0 5px 5px 5px;
    border-style: solid;
    border-color: transparent transparent rgba(0, 0, 0, 0.9) transparent;
}

/* Hover effect for calendar cells with notes */
.calendar-day-cell.has-notes:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 2;
    cursor: help;
}

/* Responsive adjustments for notes */
@media (max-width: 768px) {
    .notes-preview {
        max-width: 80px;
    }
    
    .notes-info span {
        font-size: 8px;
    }
}

@media (max-width: 576px) {
    .notes-preview {
        display: none;
    }
    
    .notes-info i {
        font-size: 10px;
    }
    
    .calendar-day-cell:hover .notes-preview {
        display: inline;
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        white-space: normal;
        max-width: 150px;
        z-index: 10;
        margin-top: -20px;
        margin-left: -10px;
    }
}

/* For input group focus state */
.input-group:focus-within .form-control,
.input-group:focus-within .form-select,
.input-group:focus-within .input-group-text {
    border-color: #ff0000 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1) !important;
}

/* For the input group text when focused */
.input-group:focus-within .input-group-text {
    border-color: #ff0000 !important;
    border-right-color: #ff0000 !important;
}

/* For select elements specifically */
select.form-select:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1) !important;
}

/* For number inputs */
input[type="number"]:focus,
input[type="text"]:focus,
input[type="date"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
textarea:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1) !important;
    outline: none;
}

/* For buttons that might have focus outline */
.btn:focus,
.btn:active:focus,
.btn.active:focus {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.25) !important;
}

/* For the search input specifically */
.input-group .form-control:focus {
    border-color: #ff0000 !important;
    box-shadow: none !important;
}

.input-group:focus-within .form-control {
    border-color: #ff0000 !important;
    box-shadow: none !important;
}

.input-group:focus-within .input-group-text {
    border-color: #ff0000 !important;
}
/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        width: 100%;
    }

    .d-flex.gap-2 .btn {
        width: 100%;
    }
}
        .absent-text {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #dc3545;
            font-weight: 500;
            font-size: 11px;
        }

        .not-recorded-text {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #adb5bd;
            font-size: 11px;
        }

        .empty-cell {
            background-color: #fafafa;
        }

        /* Status background colors */
        .status-present {
            background-color: #ffffff;
        }

        .status-absent {
            background-color: #ffffff;
        }

        .status-half {
            background-color: #ffffff;
        }

        .status-not-recorded {
            background-color: #ffffff;
        }

        /* Card styles */
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }

        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }

        .badge {
            font-weight: 500;
            border-radius: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .calendar-day-cell {
                height: 120px;
            }

            .day-number {
                font-size: 16px;
            }

            .calendar-day-details {
                font-size: 10px;
                padding: 6px 8px;
            }
        }

        @media (max-width: 768px) {
            .calendar-day-cell {
                height: 100px;
            }

            .day-number {
                font-size: 14px;
            }

            .calendar-day-header {
                padding: 5px 8px;
            }

            .calendar-day-details {
                padding: 5px 8px;
                font-size: 9px;
            }

            .hours-info, .project-info, .checkin-info {
                font-size: 8px;
            }

            .attendance-calendar-table th {
                padding: 8px 4px;
                font-size: 12px;
            }
        }

        @media (max-width: 576px) {
            .calendar-day-cell {
                height: 90px;
            }

            .day-number {
                font-size: 12px;
            }

            .calendar-day-details {
                padding: 4px 6px;
                font-size: 8px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .calendar-day-cell {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip for notes
    let tooltip = null;
    
    function showTooltip(content, event) {
        if (!content || content.trim() === '') return;
        
        if (tooltip) {
            tooltip.remove();
        }
        
        tooltip = document.createElement('div');
        tooltip.className = 'notes-tooltip';
        tooltip.innerHTML = '<i class="fas fa-sticky-note me-1"></i> ' + content;
        document.body.appendChild(tooltip);
        
        tooltip.style.top = (event.pageY - tooltip.offsetHeight - 10) + 'px';
        tooltip.style.left = (event.pageX - (tooltip.offsetWidth / 2)) + 'px';
        
        // Ensure tooltip stays within viewport
        if (tooltip.getBoundingClientRect().left < 0) {
            tooltip.style.left = '10px';
        }
        if (tooltip.getBoundingClientRect().right > window.innerWidth) {
            tooltip.style.left = (window.innerWidth - tooltip.offsetWidth - 10) + 'px';
        }
    }
    
    function hideTooltip() {
        if (tooltip) {
            tooltip.remove();
            tooltip = null;
        }
    }
    
    // Attach event listeners to calendar cells with notes
    document.querySelectorAll('.calendar-day-cell').forEach(cell => {
        const notes = cell.getAttribute('data-notes');
        if (notes && notes.trim() !== '') {
            cell.classList.add('has-notes');
            
            cell.addEventListener('mouseenter', function(e) {
                showTooltip(notes, e);
            });
            
            cell.addEventListener('mousemove', function(e) {
                if (tooltip) {
                    tooltip.style.top = (e.pageY - tooltip.offsetHeight - 10) + 'px';
                    tooltip.style.left = (e.pageX - (tooltip.offsetWidth / 2)) + 'px';
                }
            });
            
            cell.addEventListener('mouseleave', hideTooltip);
        }
    });
});

Livewire.hook('element.updated', () => {
    setTimeout(() => {
        // Re-attach tooltip listeners after Livewire updates
        document.querySelectorAll('.calendar-day-cell').forEach(cell => {
            const notes = cell.getAttribute('data-notes');
            if (notes && notes.trim() !== '' && !cell.hasAttribute('data-listener')) {
                cell.setAttribute('data-listener', 'true');
                cell.classList.add('has-notes');
                
                cell.addEventListener('mouseenter', function(e) {
                    showTooltip(notes, e);
                });
                
                cell.addEventListener('mousemove', function(e) {
                    if (tooltip) {
                        tooltip.style.top = (e.pageY - tooltip.offsetHeight - 10) + 'px';
                        tooltip.style.left = (e.pageX - (tooltip.offsetWidth / 2)) + 'px';
                    }
                });
                
                cell.addEventListener('mouseleave', hideTooltip);
            }
        });
    }, 100);
});
</script>
</div>
