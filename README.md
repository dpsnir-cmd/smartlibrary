# SmartLibrary

SmartLibrary is a PHP-based library management system for managing books, student accounts, book issuing/returning, reservations, and defaulter tracking in a simple web application.

The project is designed for a school or college library environment and includes both a public catalog and separate admin/student dashboard flows.

## Features

- Book catalog with search and browsing
- Student login and dashboard
- Book reservation system
- Book issue and return management
- Admin dashboard for overview and transaction tracking
- Inventory and defaulter monitoring
- Modern UI using Tailwind CSS and Font Awesome

## Tech Stack

- PHP
- MySQL
- HTML/CSS/JavaScript
- Tailwind CSS
- Font Awesome

## Project Structure

- `index.php` — Public library catalog page
- `student_login.php` — Student authentication page
- `student_dashboard.php` — Student access area
- `admin_dashboard.php` — Librarian overview dashboard
- `issue_book.php` — Issue book workflow
- `return_book.php` — Return book workflow
- `add_book.php` — Add a new book
- `manage_inventory.php` — Manage inventory records
- `manage_reservations.php` — View and manage reservations
- `defaulters.php` — Defaulter/fine-related records
- `db_config.php` — Database connection configuration
- `ses3.py` — Utility script (if used for related automation or notifications)

## Prerequisites

Before running the project, make sure you have:

- PHP installed
- MySQL database server running
- Apache or XAMPP/WAMP/LAMP environment
- A web browser

## Setup Instructions

1. Clone or download this repository.
2. Place the project folder inside your local web server directory, for example:
   - `htdocs` for XAMPP
   - `www` for WAMP
3. Create a MySQL database named `library_db`.
4. Update the database credentials in `db_config.php`:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "library_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

5. Create the required tables in your MySQL database. At minimum, the app expects tables such as:

```sql
CREATE DATABASE IF NOT EXISTS library_db;

USE library_db;

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    author VARCHAR(255),
    quantity INT,
    available_qty INT,
    isbn VARCHAR(100)
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    student_name VARCHAR(255),
    due_date DATE,
    status VARCHAR(50),
    issue_date DATE,
    return_date DATE
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    student_id INT,
    reserved_at DATETIME
);
```

6. Start your local server and open the app in a browser.

Example:

```text
http://localhost/smartlibrary/
```

## Default Usage

- Students can access the public catalog from `index.php`.
- Student login is handled through `student_login.php`.
- Librarians can manage the system from `admin_dashboard.php`.

## Notes

- This project appears to be a prototype or academic project and may not yet include advanced production-level authentication or validation.
- Some pages refer to flows such as admin login protection or future improvements, which may require additional implementation depending on your use case.
- Security hardening, input validation, and password hashing are recommended before deploying this in a production environment.

## License

This project does not currently include an explicit license file. If you plan to publish or distribute it publicly, you should add an appropriate open-source license.

## Contributing

Pull requests and improvements are welcome. If you're working on this project, consider improving:

- Input sanitization and SQL safety
- Password hashing
- Role-based access control
- Better error handling
- Database schema normalization
- Frontend responsiveness

## Contact

For questions or project-related updates, contact the repository owner or maintainers on GitHub.
