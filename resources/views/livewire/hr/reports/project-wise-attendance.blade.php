{{-- resources/views/livewire/hr/reports/project-wise-attendance.blade.php --}}
<div>
    <div class="card shadow-sm border-0">
        <!-- Header with Gradient Background -->
        <div class="card-header border-0 pt-4 pb-4" style="background: linear-gradient(135deg, #ff0000 0%, #000000 100%); border-radius: 1rem 1rem 0 0;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-building text-white fs-4"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-white">Project Wise Attendance</h3>
                        <p class="text-white-50 small mb-0">Comprehensive project attendance tracking & analytics</p>
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
                <!-- Project Filter -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-project-diagram me-1" style="color: #ff0000;"></i>
                        Project
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-building"></i>
                        </span>
                        <select class="form-select" wire:model="tempProjectId">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->project_code }})</option>
                            @endforeach
                        </select>
                        @if($tempProjectId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearProjectFilter">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Worker Filter -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-user me-1" style="color: #ff0000;"></i>
                        Worker (Optional)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user"></i>
                        </span>
                        <select class="form-select" wire:model="tempWorkerId" @if(!$tempProjectId) disabled @endif>
                            <option value="">All Workers</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->designation ?: 'No Designation' }})</option>
                            @endforeach
                        </select>
                        @if($tempWorkerId)
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearWorkerFilter">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    @if(!$tempProjectId)
                        <small class="text-muted">Select a project first</small>
                    @endif
                </div>

                <!-- Month Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar-month me-1" style="color: #ff0000;"></i>
                        Month
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <select class="form-select" wire:model="tempMonth">
                            @foreach($months as $key => $month)
                                <option value="{{ $key }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Year Filter -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar-year me-1" style="color: #ff0000;"></i>
                        Year
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <select class="form-select" wire:model="tempYear">
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- View Mode -->
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
            </div>

            <!-- Search Actions -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4" style="background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%); border: none;">
                            <i class="fas fa-search me-2"></i> Search Attendance
                        </button>
                        <button type="button" class="btn btn-secondary px-4" wire:click="resetFilters">
                            <i class="fas fa-undo-alt me-2"></i> Clear All
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if($isSearching && ($selectedProjectId || $selectedWorkerId))
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted me-2">
                        <i class="fas fa-filter me-1"></i>Active filters:
                    </small>
                    @if($selectedProjectId)
                        <span class="badge bg-primary">
                            <i class="fas fa-project-diagram me-1"></i>
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

            @if($selectedProjectId && $projectInfo)
                <!-- Project Info & Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle p-3" style="background: rgba(255, 0, 0, 0.1);">
                                            <i class="fas fa-building fa-2x" style="color: #ff0000;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Project Details</h6>
                                        <h5 class="mb-0 fw-bold">{{ $projectInfo->name }}</h5>
                                        <small class="text-muted">{{ $projectInfo->project_code }}</small>
                                        <div class="mt-2">
                                            <small><i class="fas fa-user me-1"></i> {{ $projectInfo->client_name }}</small><br>
                                            <small><i class="fas fa-map-marker-alt me-1"></i> {{ Str::limit($projectInfo->location, 30) ?: 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Project Summary</p>
                                        <h3 class="mb-0 fw-bold">{{ $totalWorkers }}</h3>
                                        <small class="text-muted">active workers</small>
                                        <div class="mt-2">
                                            <small><i class="fas fa-calendar-alt me-1"></i> Started: {{ $projectInfo->start_date ? Carbon\Carbon::parse($projectInfo->start_date)->format('M d, Y') : 'N/A' }}</small><br>
                                            <small><i class="fas fa-flag-checkered me-1"></i> Status: 
                                                <span class="badge bg-{{ $projectInfo->status == 'ongoing' ? 'success' : ($projectInfo->status == 'completed' ? 'info' : 'warning') }}">
                                                    {{ ucfirst($projectInfo->status) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(40, 167, 69, 0.1);">
                                        <i class="fas fa-users fa-2x" style="color: #28a745;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Monthly Summary</p>
                                        <h3 class="mb-0 fw-bold">{{ $totalWorkingDays }}</h3>
                                        <small class="text-muted">working days this month</small>
                                        <div class="mt-2">
                                            <small><i class="fas fa-clock me-1"></i> Total Hours: {{ number_format($totalHoursWorked, 2) }}</small><br>
                                            <small><i class="fas fa-chart-line me-1"></i> Avg/Day: {{ number_format($averageHoursPerDay, 2) }} hrs</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(255, 193, 7, 0.1);">
                                        <i class="fas fa-chart-bar fa-2x" style="color: #ffc107;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Attendance Rate</p>
                                        <h3 class="mb-0 fw-bold">{{ $attendanceRate }}%</h3>
                                        <small class="text-muted">of total possible</small>
                                        <div class="mt-1">
                                            <small>{{ number_format($totalActualAttendance, 0) }} / {{ $totalPossibleAttendance }} days</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(23, 162, 184, 0.1);">
                                        <i class="fas fa-chart-line fa-2x" style="color: #17a2b8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Present</p>
                                        <h3 class="mb-0 fw-bold text-success">{{ $totalPresent }}</h3>
                                        <small class="text-muted">days present</small>
                                        <div class="mt-1">
                                            <small>Workers: <strong class="text-success">{{ $totalPresentWorkers }}</strong> present</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(40, 167, 69, 0.1);">
                                        <i class="fas fa-check-circle fa-2x" style="color: #28a745;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Absent</p>
                                        <h3 class="mb-0 fw-bold text-danger">{{ $totalAbsent }}</h3>
                                        <small class="text-muted">days absent</small>
                                        <div class="mt-1">
                                            <small>Workers: <strong class="text-danger">{{ $totalAbsentWorkers }}</strong> absent</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(220, 53, 69, 0.1);">
                                        <i class="fas fa-times-circle fa-2x" style="color: #dc3545;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-1">Half Day</p>
                                        <h3 class="mb-0 fw-bold text-warning">{{ $totalHalfDay }}</h3>
                                        <small class="text-muted">days half day</small>
                                        <div class="mt-1">
                                            <small>Hours: <strong>{{ number_format($totalOvertime, 2) }}</strong> overtime</small>
                                        </div>
                                    </div>
                                    <div class="rounded-circle p-3" style="background: rgba(255, 193, 7, 0.1);">
                                        <i class="fas fa-adjust fa-2x" style="color: #ffc107;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Display Section -->
                @if($viewMode == 'calendar')
                    <!-- Calendar View - Visual Calendar with Fixed Size Boxes -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-calendar-alt me-2" style="color: #ff0000;"></i>
                                Attendance Calendar - {{ $months[$selectedMonth] }} {{ $selectedYear }}
                                @if($selectedWorkerId && $selectedWorkerDetails)
                                    <span class="badge bg-info ms-2">Filtered: {{ $selectedWorkerDetails->name }}</span>
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
                                                        $calendarRecord = $calendarData->firstWhere('date.day', $currentDay);
                                                        
                                                        if($showDay && $currentDay <= $daysInMonth) {
                                                            $hasData = $calendarRecord ? $calendarRecord['has_data'] : false;
                                                            $present = $calendarRecord ? $calendarRecord['present'] : 0;
                                                            $absent = $calendarRecord ? $calendarRecord['absent'] : 0;
                                                            $halfDay = $calendarRecord ? $calendarRecord['half_day'] : 0;
                                                            $totalWorkers = $calendarRecord ? $calendarRecord['total_workers'] : 0;
                                                            $totalHours = $calendarRecord ? $calendarRecord['total_hours'] : 0;
                                                            $totalOvertime = $calendarRecord ? $calendarRecord['total_overtime'] : 0;
                                                            
                                                            $attendanceRate = $totalWorkers > 0 ? round((($present + ($halfDay * 0.5)) / $totalWorkers) * 100, 1) : 0;
                                                            
                                                            $statusClass = '';
                                                            $statusIcon = '';
                                                            $statusBg = '#ffffff';
                                                            
                                                            if($hasData) {
                                                                if($attendanceRate >= 80) {
                                                                    $statusClass = 'status-good';
                                                                    $statusIcon = 'fa-smile';
                                                                    $statusBg = '#d4edda';
                                                                } elseif($attendanceRate >= 50) {
                                                                    $statusClass = 'status-average';
                                                                    $statusIcon = 'fa-meh';
                                                                    $statusBg = '#fff3cd';
                                                                } else {
                                                                    $statusClass = 'status-poor';
                                                                    $statusIcon = 'fa-frown';
                                                                    $statusBg = '#f8d7da';
                                                                }
                                                            } else {
                                                                $statusClass = 'status-no-data';
                                                                $statusIcon = 'fa-calendar-times';
                                                                $statusBg = '#f8f9fa';
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    @if($showDay && $currentDay <= $daysInMonth)
                                                        <td class="calendar-day-cell {{ $statusClass }}" style="background-color: {{ $statusBg }};">
                                                            <div class="calendar-day-header">
                                                                <span class="day-number">{{ $currentDay }}</span>
                                                                <i class="fas {{ $statusIcon }} status-icon"></i>
                                                            </div>
                                                            <div class="calendar-day-details">
                                                                @if($hasData)
                                                                    <div class="attendance-stats">
                                                                        <div class="stat-item present">
                                                                            <i class="fas fa-check-circle"></i>
                                                                            <span>{{ $present }}</span>
                                                                        </div>
                                                                        <div class="stat-item absent">
                                                                            <i class="fas fa-times-circle"></i>
                                                                            <span>{{ $absent }}</span>
                                                                        </div>
                                                                        <div class="stat-item half">
                                                                            <i class="fas fa-adjust"></i>
                                                                            <span>{{ $halfDay }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="hours-info">
                                                                        <i class="fas fa-clock"></i>
                                                                        <span>{{ number_format($totalHours, 1) }}h</span>
                                                                        @if($totalOvertime > 0)
                                                                            <span class="overtime-badge">+{{ number_format($totalOvertime, 1) }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="workers-info">
                                                                        <i class="fas fa-users"></i>
                                                                        <span>{{ $totalWorkers }} workers</span>
                                                                    </div>
                                                                    <div class="rate-info">
                                                                        <div class="progress-bar-small">
                                                                            <div class="progress-fill" style="width: {{ $attendanceRate }}%; background-color: {{ $attendanceRate >= 80 ? '#28a745' : ($attendanceRate >= 50 ? '#ffc107' : '#dc3545') }};"></div>
                                                                        </div>
                                                                        <span class="rate-text">{{ $attendanceRate }}%</span>
                                                                    </div>
                                                                @else
                                                                    <div class="no-data-text">
                                                                        <i class="fas fa-calendar-times"></i>
                                                                        <span>No records</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        @php $currentDay++ @endphp
                                                    @else
                                                        <td class="calendar-day-cell empty-cell"> </td>
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
                    <!-- List View - Detailed Attendance -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">
                                <i class="fas fa-list me-2" style="color: #ff0000;"></i>
                                Detailed Attendance List - {{ $months[$selectedMonth] }} {{ $selectedYear }}
                                @if($selectedWorkerId && $selectedWorkerDetails)
                                    <span class="badge bg-info ms-2">{{ $selectedWorkerDetails->name }}</span>
                                @endif
                            </h6>
                            <div class="text-muted small">
                                <i class="fas fa-chart-line me-1"></i>
                                Total: {{ $attendanceData->count() }} records
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Date</th>
                                        <th class="py-3">Worker</th>
                                        <th class="py-3">Designation</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Check In</th>
                                        <th class="py-3">Check Out</th>
                                        <th class="py-3">Hours</th>
                                        <th class="py-3">OT</th>
                                        <th class="py-3">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendanceData as $record)
                                        @php
                                            $statusColor = [
                                                'present' => 'success',
                                                'absent' => 'danger',
                                                'half_day' => 'warning'
                                            ][$record->status];
                                            
                                            $statusIcon = [
                                                'present' => 'fa-check-circle',
                                                'absent' => 'fa-times-circle',
                                                'half_day' => 'fa-adjust'
                                            ][$record->status];
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ Carbon\Carbon::parse($record->date)->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Carbon\Carbon::parse($record->date)->format('l') }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $record->worker->name }}</div>
                                                <small class="text-muted">{{ $record->worker->email }}</small>
                                            </td>
                                            <td>{{ $record->worker->designation ?: 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $statusColor }} px-3 py-2">
                                                    <i class="fas {{ $statusIcon }} me-1"></i>
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($record->check_in)
                                                    {{ Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->check_out)
                                                    {{ Carbon\Carbon::parse($record->check_out)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">{{ number_format($record->hours_worked, 2) }} hrs</td>
                                            <td>
                                                @if($record->overtime_hours > 0)
                                                    <span class="badge bg-info">{{ number_format($record->overtime_hours, 2) }} hrs</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->notes)
                                                    <small class="text-muted">{{ Str::limit($record->notes, 30) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No attendance records found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                @else
                    <!-- Chart View - Analytics -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light border-0 py-3">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-chart-bar me-2" style="color: #ff0000;"></i>
                                        Worker Performance Summary
                                        @if($selectedWorkerId && $selectedWorkerDetails)
                                            <span class="badge bg-info ms-2">{{ $selectedWorkerDetails->name }}</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Worker</th>
                                                    <th class="text-center">Present</th>
                                                    <th class="text-center">Absent</th>
                                                    <th class="text-center">Half</th>
                                                    <th class="text-center">Hours</th>
                                                    <th class="text-center">Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($workerSummary as $worker)
                                                    @php
                                                        $totalDays = $worker->present + $worker->absent + $worker->half_day;
                                                        $attRate = $totalDays > 0 ? round(($worker->present / $totalDays) * 100, 1) : 0;
                                                        $rateClass = $attRate >= 80 ? 'success' : ($attRate >= 50 ? 'warning' : 'danger');
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold">{{ $worker->worker->name }}</div>
                                                            <small class="text-muted">{{ $worker->worker->designation ?: 'N/A' }}</small>
                                                        </td>
                                                        <td class="text-center text-success fw-semibold">{{ $worker->present }}</td>
                                                        <td class="text-center text-danger">{{ $worker->absent }}</td>
                                                        <td class="text-center text-warning">{{ $worker->half_day }}</td>
                                                        <td class="text-center">{{ number_format($worker->total_hours, 1) }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-{{ $rateClass }}">{{ $attRate }}%</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
                                        Daily Attendance Trend
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailyTrendChart" style="height: 350px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                <!-- No Project Selected -->
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-building fa-4x mb-3 opacity-50"></i>
                        <h5>No Project Selected</h5>
                        <p>Please select a project to view attendance analytics</p>
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
            const pieCtx = document.getElementById('attendancePieChart')?.getContext('2d');
            if (pieCtx && window.attendancePieChart) {
                window.attendancePieChart.destroy();
            }
            
            const trendCtx = document.getElementById('dailyTrendChart')?.getContext('2d');
            if (trendCtx && window.dailyTrendChart) {
                window.dailyTrendChart.destroy();
            }
            
            @if($viewMode == 'chart' && $selectedProjectId)
                if (pieCtx) {
                    window.attendancePieChart = new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Present', 'Absent', 'Half Day'],
                            datasets: [{
                                data: [{{ $totalPresent }}, {{ $totalAbsent }}, {{ $totalHalfDay }}],
                                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' },
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
                
                if (trendCtx) {
                    const dailyData = @json($dailySummary);
                    const labels = dailyData.map(item => {
                        const date = new Date(item.date);
                        return date.getDate() + ' ' + date.toLocaleString('default', { month: 'short' });
                    });
                    const presentData = dailyData.map(item => item.present);
                    const absentData = dailyData.map(item => item.absent);
                    const halfData = dailyData.map(item => item.half_day);
                    
                    window.dailyTrendChart = new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Present',
                                    data: presentData,
                                    borderColor: '#28a745',
                                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'Absent',
                                    data: absentData,
                                    borderColor: '#dc3545',
                                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'Half Day',
                                    data: halfData,
                                    borderColor: '#ffc107',
                                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, title: { display: true, text: 'Number of Workers' } },
                                x: { title: { display: true, text: 'Date' } }
                            }
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
        .card {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }
        
        .badge {
            font-weight: 500;
            border-radius: 0.5rem;
        }
        
        .progress {
            border-radius: 1rem;
            background-color: #e9ecef;
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #e0e0e0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1);
        }
        
        .btn-light:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 0.5rem;
            }
            .card-body {
                padding: 1rem;
            }
        }
        
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
            height: 150px;
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
        
        .status-good .status-icon {
            color: #28a745;
        }
        
        .status-average .status-icon {
            color: #ffc107;
        }
        
        .status-poor .status-icon {
            color: #dc3545;
        }
        
        .status-no-data .status-icon {
            color: #adb5bd;
        }
        
        .calendar-day-details {
            padding: 8px 10px;
            font-size: 11px;
        }
        
        .attendance-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 8px;
            justify-content: space-around;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .stat-item.present {
            color: #28a745;
        }
        
        .stat-item.absent {
            color: #dc3545;
        }
        
        .stat-item.half {
            color: #ffc107;
        }
        
        .hours-info, .workers-info {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 6px;
            color: #6c757d;
            font-size: 10px;
        }
        
        .hours-info i, .workers-info i {
            font-size: 9px;
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
        
        .rate-info {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
        }
        
        .progress-bar-small {
            flex: 1;
            height: 4px;
            background-color: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 2px;
        }
        
        .rate-text {
            font-size: 10px;
            font-weight: 600;
            min-width: 35px;
        }
        
        .no-data-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #adb5bd;
            font-size: 11px;
            padding: 15px 0;
        }
        /* Red outline styling for all focusable elements */
.form-control:focus, 
.form-select:focus, 
.input-group-text:focus,
.input-group .form-control:focus,
.input-group .form-select:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1) !important;
    outline: none !important;
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

/* For number inputs and text inputs */
input[type="number"]:focus,
input[type="text"]:focus,
input[type="date"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
textarea:focus {
    border-color: #ff0000 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.1) !important;
    outline: none !important;
}

/* For buttons that might have focus outline */
.btn:focus,
.btn:active:focus,
.btn.active:focus {
    outline: none !important;
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

/* For modal close button focus */
.btn-close:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.25) !important;
}
        
        .empty-cell {
            background-color: #fafafa;
        }
        
        @media (max-width: 992px) {
            .calendar-day-cell {
                height: 130px;
            }
            .day-number {
                font-size: 16px;
            }
            .calendar-day-details {
                font-size: 10px;
                padding: 6px 8px;
            }
            .attendance-stats {
                gap: 8px;
            }
        }
        
        @media (max-width: 768px) {
            .calendar-day-cell {
                height: 110px;
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
            .attendance-stats {
                gap: 6px;
            }
            .stat-item {
                font-size: 9px;
            }
            .attendance-calendar-table th {
                padding: 8px 4px;
                font-size: 12px;
            }
        }
        
        @media (max-width: 576px) {
            .calendar-day-cell {
                height: 100px;
            }
            .day-number {
                font-size: 12px;
            }
            .calendar-day-details {
                padding: 4px 6px;
                font-size: 8px;
            }
        }
        
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
        
        .card {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</div>