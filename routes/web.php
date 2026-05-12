<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Logout;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Hr\Dashboard as HRDashboard;
use App\Livewire\Hr\AddWorker;
use App\Livewire\Hr\EditWorker;
use App\Livewire\Hr\Attendance;
use App\Livewire\Hr\Projects\AddProject;
use App\Livewire\Hr\Projects\EditProject;
use App\Livewire\Hr\Projects\ProjectAssignment;
use App\Livewire\Hr\Payment\AddWorkerAdvance;
use App\Livewire\Hr\Payment\WorkerAdvanceList;
use App\Services\PayrollService;
use App\Livewire\Hr\Payroll\RealTimeDashboard;
use App\Livewire\Hr\Payroll\PayrollHistory;
use App\Livewire\Hr\Payroll\SingleWorkerPayroll;
use App\Livewire\Admin\PayrollDashboard;
use App\Livewire\Admin\PayrollSummary;
use App\Livewire\Admin\WorkerProfile;
use App\Livewire\Admin\ProjectExpenses;
use App\Livewire\Admin\ProfitLossDashboard;
use App\Livewire\Hr\Reports\AttendanceSheet;
use App\Livewire\Hr\Reports\ProjectWiseAttendance;
use App\Livewire\Admin\BudgetQuotation;
use App\Livewire\Hr\Reports\ClientBillingReport;

// Public Routes (no auth required)
Route::middleware([StartSession::class])->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/', Login::class);
    Route::get('/logout', Logout::class)->name('logout');
});

// Authenticated Routes
Route::middleware([StartSession::class, 'auth', 'check.status'])->group(function () {

    // Admin Dashboard
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/payroll-dashboard', PayrollDashboard::class)->name('admin.payroll-dashboard');
        Route::get('/admin/payroll-summary', PayrollSummary::class)->name('admin.payroll-summary');
        Route::get('/admin/worker-profile', WorkerProfile::class)->name('admin.worker-profile');
        Route::get('/admin/expenses', ProjectExpenses::class)->name('admin.expenses');
        Route::get('/admin/profit-loss', ProfitLossDashboard::class)->name('admin.profit-loss');
        Route::get('/admin/budget-quotations', BudgetQuotation::class)->name('admin.budget-quotations');
        // Sales Invoice Routes (Admin)
        Route::get('/admin/sales-invoices', \App\Livewire\Admin\SalesInvoice\SalesInvoiceList::class)->name('admin.sales-invoices.list');
        Route::get('/admin/sales-invoices/create', \App\Livewire\Admin\SalesInvoice\SalesInvoiceForm::class)->name('admin.sales-invoices.create');
        Route::get('/admin/sales-invoices/edit/{id}', \App\Livewire\Admin\SalesInvoice\SalesInvoiceForm::class)->name('admin.sales-invoices.edit');


        // Add more admin pages here later
    });

    Route::get('/generate-payroll/{year}/{month}', function ($year, $month) {
        app(PayrollService::class)->generate($year, $month);
        return "Payroll Generated";
    });

    // HR Dashboard
    Route::middleware(['role:hr'])->group(function () {
        Route::get('/hr/dashboard', HRDashboard::class)->name('hr.dashboard');
        Route::get('/hr/workers', EditWorker::class)->name('hr.workers.list');

        // Separate routes for create and edit
        Route::get('/hr/worker/create', AddWorker::class)->name('hr.worker.create');
        Route::get('/hr/worker/{workerId}/edit', AddWorker::class)->name('hr.worker.edit');
        Route::get('/hr/projects/add', AddProject::class)->name('hr.project.add');
        Route::get('/hr/projects', EditProject::class)->name('hr.projects.list');
        Route::get('/hr/project/{project}/edit', [EditProject::class, 'edit'])->name('hr.project.edit');
        Route::get('/hr/project-assignment', ProjectAssignment::class)->name('hr.project.assignment');
        Route::get('hr/attendance', Attendance::class)->name('hr.attendance');
        Route::get('/hr/advances', WorkerAdvanceList::class)->name('hr.advances.list');
        Route::get('/hr/advances/form/{id?}', AddWorkerAdvance::class)->name('hr.advances.form');
        Route::get('/payroll/history', PayrollHistory::class)->name('payroll.history');
        Route::get('/payroll/single-worker', SingleWorkerPayroll::class)->name('payroll.single-worker');
        Route::get('/hr/reports/attendancesheet', AttendanceSheet::class)->name('hr.attendancesheet');
        Route::get('/hr/reports/projectwiseattendance', ProjectWiseAttendance::class)->name('hr.projectwiseattendance');
        Route::get('/hr/reports/clientbillingsystem', ClientBillingReport::class)->name('hr.companybillingsystem');

        // Add more HR pages here later
    });
});
