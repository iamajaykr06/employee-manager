# Employee Management System (PHP & MySQL)

A multi-page web application for managing employee records built with PHP and MySQL. This project fulfills all requirements of **BCA-VI Assignment 2** (3CCC305 - PHP & MySQL).

## Features

- **Secure Database Connection** using environment variables (`.env`)
- **Form Validation** – no empty fields, valid email, at least one hobby selected
- **Session & Cookie Handling** – store and display user information
- **Full CRUD Operations** – Create, Read, Update, Delete
- **Conditional Queries**:
    - Retrieve employees with salary > ₹50,000
    - Filter by gender or department
    - Sort records ascending/descending
    - Count total employees
    - Custom search with multiple criteria
- **Dynamic Table Creation & Alteration**
- **Error & Exception Handling** for validation and database operations
- **Responsive UI** with clean CSS styling

## Project Structure
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


## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/employee-manager.git
   cd employee-manager
   
2. **Configure environment variables**
   ```bash
   cp .env.example .env

Edit `.env` with your database credentials:

 ```bash
DB_HOST=localhost
DB_NAME=employee_db
DB_USER=root
DB_PASS=your_password
DB_CHARSET=utf8mb4 
```

3. **Set up the database**

The application will automatically:

Create the database if it does not exist

Create the employees table

Add the department column dynamically if missing

Run the application

Using PHP built-in server:

bash
php -S localhost:8000
Then open http://localhost:8000 in your browser.

Alternatively, place the project folder in your web server's document root (e.g., htdocs).

🚀 Usage
1. Add Employee (index.php)
Fill the form with Name, Email, Gender, Hobbies (multi-select checkboxes), and Salary

Form validates all fields and displays errors

On success: data stored in session, cookie set, redirected to display.php with GET parameters

2. View Submitted Data (display.php)
Shows employee details passed via GET

Displays session data and cookie timestamp

3. Manage Employees (manage.php)
View all employees in a table

Filters: Salary > ₹50k, Gender, Department, Search, Sorting

Actions: Edit or Delete any record

Shows total employee count

4. Edit Employee (edit.php)
Pre-filled form with existing data

Update any field and save changes

5. Delete Employee (delete.php)
Confirmation page before permanent deletion

6. Advanced Search (search.php)
Search by name, email, gender, salary range, department, and hobby

Results displayed in a table with edit/delete options

🔐 Security Features
Database credentials stored in .env (excluded from Git)

Prepared statements prevent SQL injection

Input validation and sanitization

Error messages logged to file, not displayed to users

.gitignore configured to exclude sensitive files

✅ Assignment Requirements Mapping
Requirement	Implementation
Web form using $_POST	index.php
Validation (no empty, valid email, one hobby)	PHP validation in index.php & edit.php
Display data on another page via $_GET	display.php
Session and Cookie handling	$_SESSION['last_employee'], $_COOKIE['last_submission']
PHP-MySQL connection with DB creation	config/database.php
Dynamic table creation/alteration	CREATE TABLE IF NOT EXISTS, ALTER TABLE for department column
CRUD: Insert	index.php
CRUD: Retrieve	manage.php, search.php
CRUD: Update	edit.php
CRUD: Delete	delete.php
Conditional Queries: Salary > 50000	Filter in manage.php
Conditional Queries: Department/Gender filter	Filter in manage.php
Conditional Queries: Sorting	Sort dropdown in manage.php
Conditional Queries: Count total	Displayed in manage.php
Conditional Queries: Custom search	search.php
Structured display (HTML table)	All list pages use <table>
Exception/error handling	Try-catch blocks with error_log()
📝 Notes for Evaluation
The .env file is not included in the repository for security reasons. Use .env.example as a template.

The application automatically creates the database and table on first run.

The department column is added via ALTER TABLE to demonstrate dynamic schema modification.

All error messages are logged using error_log() for debugging while showing friendly messages to users.

### License
This project is created for academic purposes (BCA-VI Assignment).

### Author
Ajay Kumar