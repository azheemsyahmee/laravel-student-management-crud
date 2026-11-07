# Laravel Student Management System (CRUD with Role-Based Access Control)

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-9%2F10-FF2D20.svg)](https://laravel.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3.svg)](https://getbootstrap.com/)
[![Vite](https://img.shields.io/badge/Vite-Bundler-646CFF.svg)](https://vitejs.dev/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A robust full-stack academic student information and registration management web application built with **Laravel**, **MySQL**, **Blade**, and **Bootstrap 5**.

The system features complete CRUD operations, user-scoped data isolation (users manage only their enrolled students), role-based admin authorization middleware, multi-field search filtering, file attachment storage, and asynchronous background email queueing.

---

## Key Features

* **Authentication & Role Authorization:**
  * Complete user authentication (registration, login, logout, password resets) via `laravel/ui`.
  * Custom `AdminMiddleware` enforcing route-level access restrictions (e.g., student editing and admin dashboard controls restricted to `isAdmin = 1`).
* **User-Scoped Data Isolation:**
  * Enforces multi-tenant data privacy where standard users query and view only records belonging to their account (`$user->students()->paginate()`).
* **Full CRUD Operations:**
  * Create, Read, Update, Delete student profiles with server-side request validation (`$request->validate(...)`).
  * Database-level soft deletes (`SoftDeletes`) preserving audit records.
* **Multi-Field Live Search:**
  * Keyword search matching dynamically across student names and enrolled course names.
* **Document & Avatar Uploads:**
  * Integrated with Laravel Storage (`public` disk) with timestamped unique file naming (`{id}-{date}.{ext}`).
  * Automated physical file removal from disk upon record deletion.
* **Asynchronous Queue-Driven Email Dispatching:**
  * Decoupled mail delivery using Laravel Jobs (`SendEmailJob`) dispatched to background database queues (`jobs` and `failed_jobs` tables) to maintain instant UI responsiveness.

---

## System Architecture

```
                                  ┌──────────────────────────┐
                                  │   Browser HTTP Request   │
                                  └─────────────┬────────────┘
                                                │
                                                ▼
                                    [Laravel Routing (web.php)]
                                                │
                         ┌──────────────────────┴──────────────────────┐
                         ▼                                             ▼
                 [Standard Routes]                             [Protected Routes]
              Auth, Home, View, Store                         AdminMiddleware Check
                         │                                             │
                         │                                    isAdmin == 1 ? Allow : 403
                         │                                             │
                         └──────────────────────┬──────────────────────┘
                                                │
                                                ▼
                                   [StudentController.php]
                                 - Scoped Query: $user->students()
                                 - Validation: Form Requests
                                 - File Storage: public disk
                                 - Queue Dispatch: SendEmailJob
                                                │
                         ┌──────────────────────┴──────────────────────┐
                         ▼                                             ▼
                  [MySQL Database]                              [Database Queue]
               - users (roles)                               - jobs (async worker)
               - students (FK user_id)                       - Mailtrap SMTP
```

---

## Database Schema (ER Overview)

```
┌─────────────────────────────────┐           ┌─────────────────────────────────┐
│              users              │           │            students             │
├─────────────────────────────────┼───────────┼─────────────────────────────────┤
│ id (PK)                         │ 1       * │ id (PK)                         │
│ name                            │──────────<│ user_id (FK -> users.id)        │
│ email (UNIQUE)                  │           │ name                            │
│ password                        │           │ course (NULLABLE)               │
│ role / isAdmin (TINYINT/STRING) │           │ attachment (NULLABLE)           │
│ created_at / updated_at         │           │ deleted_at (SOFT DELETES)       │
└─────────────────────────────────┘           │ created_at / updated_at         │
                                              └─────────────────────────────────┘
```

---

## Repository Structure

```
laravel-student-management-crud/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                     # Authentication controllers
│   │   │   ├── HomeController.php        # User dashboard
│   │   │   └── StudentController.php     # Full student CRUD, storage & queue dispatch
│   │   └── Middleware/
│   │       └── AdminMiddleware.php       # Role verification guard
│   ├── Jobs/
│   │   └── SendEmailJob.php              # Asynchronous queued email dispatcher
│   ├── Mail/
│   │   └── StudentCreated.php            # Mailable template
│   └── Models/
│       ├── Student.php                   # Student Eloquent model with SoftDeletes
│       └── User.php                      # User model with role methods & relationships
├── database/
│   ├── migrations/                       # Database schema migrations
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2023_02_14_072144_create_students_table.php
│   │   ├── 2023_02_15_013533_add_role_column_to_users_table.php
│   │   └── 2023_02_15_035803_create_jobs_table.php
│   └── seeders/                          # Database seeders
├── resources/
│   └── views/
│       ├── admin/                        # Admin layout components (navbar, sidebar)
│       ├── auth/                         # Login, registration, password reset views
│       ├── layouts/                      # Main Bootstrap application layout
│       └── students/                     # CRUD views (index, create, edit, show)
├── routes/
│   └── web.php                           # Application route definitions
├── .env.example                          # Sanitized configuration template
├── composer.json                         # PHP backend dependencies
├── package.json                          # Vite and frontend dependencies
└── vite.config.js                        # Frontend asset bundling configuration
```

---

## Installation & Local Setup

### Prerequisites
* **PHP:** >= 8.1
* **Composer:** >= 2.x
* **Node.js & npm:** >= 16.x
* **MySQL:** >= 5.7 or MariaDB

### 1. Clone the Repository
```bash
git clone https://github.com/azheemsyahmee/laravel-student-management-crud.git
cd laravel-student-management-crud
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
Copy the sanitized environment configuration file and generate an application key:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your local MySQL database credentials inside `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Database Migrations & Symlinks
Run the migrations and create the storage symbolic link for user file uploads:
```bash
php artisan migrate
php artisan storage:link
```

### 5. Build Frontend Assets
Compile the Vite CSS and JavaScript bundles:
```bash
npm run build
# Or for local hot reloading: npm run dev
```

### 6. Run the Application
In separate terminal windows:
```bash
# Start the Laravel development server
php artisan serve

# Start the background queue worker (for email notifications)
php artisan queue:work
```

Open your browser at `http://localhost:8000`.

---

## Security & Best Practices

* **CSRF Protection:** All Blade forms enforce Laravel's `@csrf` token validation.
* **SQL Injection Prevention:** All database operations utilize Eloquent ORM parameter bindings.
* **Secret Hygiene:** Zero production credentials or application keys are tracked in version control; all environment secrets are kept in `.env` (ignored by `.gitignore`).
* **Input Sanitization:** Controller request validation rules enforce field constraints before database persistence.

---

## Author & Attribution

* **Developer:** Azheem Syahmee bin Zulkifli
* **Project Type:** Full-Stack Web Engineering Showcase (Laravel / PHP)
