# Vacation Portal

A simple PHP + MySQL vacation request portal with login for managers and employees.

## Requirements

- PHP 8.0+
- MySQL or MariaDB


---

## 1️ Clone the repo

```bash
git clone https://github.com/takis96/vacation-portal.git
cd vacation-portal
```

---

## 2️ Create database

Log into MySQL:
```bash
mysql -u root -p
```

Create the database and user:
```sql
CREATE DATABASE vacation_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'vacation_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON vacation_portal.* TO 'vacation_user'@'localhost';
FLUSH PRIVILEGES;
```

Use the database:
```sql
USE vacation_portal;
```

Create the `users` table:
```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('manager', 'employee') NOT NULL,
  api_token VARCHAR(255) DEFAULT NULL
);
```

Insert test users (hash corresponds to password `password123`):
```sql
INSERT INTO users (name, email, password_hash, role) VALUES
('Manager', 'manager@vacation.local', '$2y$10$tdtuNrIyZRsjkl3ujhNyOOluGDznvzvJgTpfZioXaLSemzAqPezZu', 'manager'),
('Employee', 'employee@vacation.local', '$2y$10$tdtuNrIyZRsjkl3ujhNyOOluGDznvzvJgTpfZioXaLSemzAqPezZu', 'employee');
```


## 3️ Configure database connection

Edit `config.php` and set your DB credentials:
<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'vacation_portal');
define('DB_USER', 'vacation_user');
define('DB_PASS', 'StrongPassword123!');



## 4️ Start the PHP built-in server

From your project directory:
```bash
php -S localhost:8000 api.php
```

**Important**: You must use `api.php` as router script, or API routes won’t work.

---

## 5️ Open the login page

Go to:
```
http://localhost:8000/login.html
```

---

## 6️ Example Test credentials

- **Manager**
  - Email: manager@vacation.local
  - Password: password123

- **Employee**
  - Email: employee@vacation.local
  - Password: password123

---

## Notes

 Static files (HTML/CSS/JS) are served automatically.

 API routes (e.g., `/api/login`) require running the built-in server with `api.php` router.


