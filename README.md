# 🏗️ Construction ERP System

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Livewire Version](https://img.shields.io/badge/Livewire-3.x-blue.svg)](https://livewire.laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://php.net)
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

##  Overview

A **comprehensive Enterprise Resource Planning (ERP) system** built specifically for construction companies. This system streamlines worker management, project tracking, attendance, payroll, and client billing.

##  Key Features

###  Worker Management
- Complete worker profiles with contact details
- Rate types: **Hourly, Daily, or Monthly**
- Medical certificate tracking with expiry alerts
- Worker assignment to multiple projects
- Status tracking (active/inactive/terminated)

### Project Management
- Project budgeting with line-item breakdowns
- Expense tracking by category (materials, equipment, subcontractors)
- Contract value vs. actual cost tracking
- Profit/Loss analysis per project
- Quotation management with VAT support

### Attendance System
- Daily check-in/out with time tracking
- Overtime calculation with multiplier support
- Client billing hours (different from worked hours)
- Project-wise attendance filtering
- Payroll generation flag to prevent duplicate processing

### Payroll Processing
- Automatic payroll generation from attendance records
- Support for hourly/daily/monthly pay rates
- Advance deduction management with running balance
- Payroll batch system (monthly)
- Project cost breakdown per payroll
- Overtime multiplier configuration
- Manual adjustment support

### Client Billing (Invoices)
- Professional invoice generation with auto-numbering
- Spanish VAT (IVA) calculation (configurable percentage)
- Service-based line items
- Payment status tracking (paid/unpaid/partial)
- Terms & conditions section
- Company details configuration

### Reporting & Dashboards
- Real-time payroll dashboard
- Project-wise profit/loss reports
- Attendance sheets (daily/monthly)
- Client billing reports
- Monthly payroll summaries with trends
- Worker advance reports

## Technology Stack

| Category | Technologies |
|----------|-------------|
| **Backend** | Laravel 11.x, PHP 8.2+ |
| **Frontend** | Livewire 3.x, Alpine.js, Tailwind CSS |
| **Database** | MySQL / PostgreSQL |
| **Authentication** | Laravel Breeze/Sanctum |
| **Queue** | Database/SQS/Redis |
| **Assets** | Vite, NPM |

## Database Schema

### Core Tables
- **users** - System users with role-based access (Admin/HR)
- **roles** - User roles for authorization
- **workers** - Employee information with rate types and medical tracking
- **projects** - Project details, budgets, and client information
- **attendances** - Daily check-in/out with project linking and overtime
- **worker_advances** - Salary advance tracking with running balance
- **payroll_batches** - Monthly payroll processing batches
- **payrolls** - Individual worker payroll calculations
- **payroll_project_breakdowns** - Project-wise payroll allocation
- **sale_invoices** - Client billing with Spanish VAT
- **invoice_items** - Service line items for invoices
- **project_expenses** - Expense tracking by category
- **expense_categories** - Organized expense classification
- **budget_items** - Project budget breakdown

### Key Relationships
- Workers ↔ Projects (Many-to-Many with assignment dates)
- Workers ↔ Attendances (One-to-Many)
- Workers ↔ Advances (One-to-Many)
- Projects ↔ Expenses (One-to-Many)
- Payrolls ↔ Projects (Many-to-Many via breakdowns)

## Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL >= 5.7 or PostgreSQL >= 10
- Node.js & NPM

### Step 1: Clone the repository
```bash
git clone https://github.com/AhmadAli61/Construction-ERP-System.git
cd Construction-ERP-System
```

### Step 2: Install dependencies
```bash

composer install
npm install
```

### Step 3: Environment configuration
cp .env.example .env
```bash
php artisan key:generate
```

### Step 4: Run migrations
```bash
php artisan migrate
```

### Step 5: Create storage link
```bash

php artisan storage:link
```

### Step 6: Start the application
```bash
php artisan serve
```


Contact

**Ahmad Ali**
- GitHub: [@AhmadAli61](https://github.com/AhmadAli61)
- Email: mlkahmi61@gmail.com
- LinkedIn: https://www.linkedin.com/in/ahmedali61/

---

Show Your Support

If you find this project helpful, please give it a star!

---

**Built with Laravel & Livewire**