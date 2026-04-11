# Employee Management System

A full-featured web application for managing employee records, built with **PHP** and **MySQL**. This project is designed as a BCA-VI Assignment (3CCC305 - PHP & MySQL).

## ✨ Features

- **Secure Database Connection** – Environment variables (`.env`) for sensitive credentials
- **Form Validation** – Email format, required fields, hobby selection
- **Session & Cookie Management** – Store and retrieve user session data
- **Complete CRUD Operations** – Create, Read, Update, Delete employee records
- **Advanced Filtering & Queries**:
  - Filter by salary (> ₹50,000)
  - Filter by gender or department
  - Flexible sorting (ascending/descending)
  - Total employee count
  - Multi-criteria search
- **Database Management** – Dynamic table creation and schema modifications
- **Error Handling** – Comprehensive exception handling and logging
- **Responsive Design** – Clean, user-friendly interface

## 📁 Project Structure

```
employee-manager/
├── .env.example
├── .gitignore
├── config/
│   └── database.php
├── css/
│   └── style.css
├── includes/
│   ├── header.php
│   └── footer.php
├── index.php
├── display.php
├── manage.php
├── edit.php
├── delete.php
├── search.php
└── README.md
```

## 🚀 Getting Started

### Prerequisites

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- A web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/employee-manager.git
   cd employee-manager
   ```

2. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` with your database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=employee_db
   DB_USER=root
   DB_PASS=your_password
   DB_CHARSET=utf8mb4
   ```

3. **Run the application**

   Using PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
   
   Then open `http://localhost:8000` in your browser.
   
   **Or** place the project folder in your web server's document root (e.g., `htdocs`).

   The application will automatically create the database and tables on first run.

## 📖 Usage Guide

### 1. Add Employee (`index.php`)
- Fill out the employee form with name, email, gender, hobbies, and salary
- Form validates all required fields in real-time
- On successful submission, data is stored in a session and cookie, then redirected to the display page

### 2. View Submitted Data (`display.php`)
- Displays the employee details submitted via form
- Shows session and cookie information with timestamps

### 3. Manage Employees (`manage.php`)
- View all employees in a structured table
- Available filters: salary range, gender, department
- Sorting options: ascending or descending
- Quick actions: Edit or delete records
- Displays total employee count

### 4. Edit Employee (`edit.php`)
- Pre-filled form with existing employee data
- Update any field and save changes

### 5. Delete Employee (`delete.php`)
- Confirmation page before permanent deletion
- Prevents accidental data loss

### 6. Advanced Search (`search.php`)
- Search by name, email, gender, salary range, department, or hobby
- Results displayed in an editable, deletable table

## 🔐 Security Features

- **Credential Protection** – Database credentials stored in `.env` (excluded from Git)
- **SQL Injection Prevention** – Prepared statements for all database queries
- **Input Validation** – All user inputs validated and sanitized
- **Error Logging** – Errors logged securely; user-friendly messages displayed
- **Git Security** – `.gitignore` configured to exclude sensitive files

## ✅ Assignment Requirements

| Requirement | Implementation |
|-------------|-----------------|
| Web form with `$_POST` | `index.php` |
| Form validation | `index.php`, `edit.php` |
| Display data via `$_GET` | `display.php` |
| Session and cookies | Session/cookie handling throughout app |
| Database connection & creation | `config/database.php` |
| Dynamic table operations | `ALTER TABLE` for schema updates |
| **C**reate | `index.php` |
| **R**ead | `manage.php`, `search.php` |
| **U**pdate | `edit.php` |
| **D**elete | `delete.php` |
| Conditional queries (salary filter) | `manage.php` |
| Conditional queries (gender/department) | `manage.php` |
| Sorting functionality | `manage.php` |
| Employee count | `manage.php` |
| Custom multi-criteria search | `search.php` |
| HTML table display | All list pages |
| Exception handling | Try-catch blocks with logging |

## 📝 Notes

- The `.env` file is not included in the repository for security. Use `.env.example` as a template.
- The database and tables are created automatically on first application run.
- The department column is added dynamically via `ALTER TABLE` to demonstrate schema modification.
- All errors are logged using `error_log()` for debugging purposes.

## 📄 License

This project is created for academic purposes (BCA-VI Assignment).

## 👤 Author

**Ajay Kumar**