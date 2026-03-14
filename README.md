# HR Management System

## Overview

The **HR Management System** is a web-based application developed using **PHP** and hosted on the **XAMPP** server. This system simplifies and automates essential HR functions, offering two primary modules:

1. **Admin Module**: Designed for HR administrators to manage employees, attendance, payroll, and leave requests.
2. **Employee Module**: Allows employees to manage their profiles and apply for leaves.

---

## Features

### Admin Module

- **User Management**:
  - Create, update, or delete employee records.
  - Assign roles and manage user profiles.
- **Leave Management**:
  - Approve or reject employee leave requests.
  - Track leave balances and leave history.
- **Attendance Management**:
  - Record employee clock-in and clock-out times.
  - Generate attendance reports.
- **Payroll Management**:
  - Calculate salaries based on attendance, overtime, bonuses, and deductions.
  - Generate and manage payslips for employees.

### Employee Module

- **Leave Application**:
  - Employees can apply for different types of leaves (e.g., vacation, sick leave).
  - View the status of leave requests (pending, approved, or rejected).
- **Profile Management**:
  - Update personal information, such as contact details and profile picture.

---

## Technology Stack

- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL (via phpMyAdmin)
- **Timer**: cron
- **Web Server**: Apache (provided by XAMPP)

---

## Setup and Installation

### Prerequisites

1. **XAMPP** installed on your system. Download from [https://www.apachefriends.org/](https://www.apachefriends.org/).
2. A modern web browser (e.g., Chrome, Firefox).

### Steps

1. Clone the project repository:
   ```bash
   git clone https://github.com/t1noo7/Hr-Management-Sys-OPS/
   ```
2. Move the project folder to the XAMPP htdocs directory:

```bash
mv hr_management_system /path/to/xampp/htdocs/
```

3. Start the XAMPP Control Panel and run the following services:
   Apache (Web Server)
   MySQL (Database Server)

4. Open phpMyAdmin at http://localhost/phpmyadmin:
   Create a new database named hr_management.
   Import the provided SQL file (database.sql) into this database.

### Usage

**Admin Module**
Log in using admin credentials.
Navigate to:
Users: Manage employee records.
Attendance: Record and view attendance.
Payroll: Process and generate payslips.
Leaves: Approve or reject leave requests.
**Employee Module**
Log in using employee credentials.
Navigate to:
Profile: Update personal information.
Leave Requests: Apply for leave and check leave status.
