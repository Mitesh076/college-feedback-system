# College Feedback System

## Overview

The **College Feedback System** is a web-based application designed to collect, manage, and analyze feedback from students about faculty, courses, and campus infrastructure. Built with PHP, MySQL, HTML, CSS, and JavaScript, the system provides a user-friendly interface for students to submit feedback and for administrators to view, report, and export feedback data. It features role-based access (students and admins), responsive design, and visualizations using Chart.js.

## Features

**Student Features**

- **Dashboard:** A centralized dashboard (`student-dashboard.php`) for students to access feedback forms for faculty, courses, and infrastructure.
- **Feedback Submission:**
  - **Faculty Feedback:** Rate faculty performance and teaching methods (1-10 scale for course outcomes, CO1-CO7).
  - **Course Feedback:** Provide feedback on course content and structure (1-10 scale for CO1-CO7).
  - **Infrastructure Feedback:** Rate campus facilities like classrooms, labs, library, Wi-Fi, washrooms, parking, canteen, and security (1-10 scale).
- **Duplicate Check:** Prevents multiple submissions for the same feedback type using AJAX-based checks.
- **Responsive UI:** Modern, mobile-friendly design with gradients and hover effects.

**Admin Features**

- **Dashboard:** Centralized admin dashboard (`admin-dashboard.php`) with links to manage users and view feedback.
- **Feedback Management:**
  - View all feedback submissions (`faculty-display-feedback.php`, `course-display-feedback.php`, `infrastructure-display-feedback.php`) with filters for department, semester, section, and year.
  - Delete specific feedback entries (`faculty-delete-feedback.php`, `course-delete-feedback.php`, `infrastructure-delete-feedback.php`).
- **Reports:** Generate reports with average ratings and bar charts (`faculty-generate-report.php`, `course-generate-report.php`, `infrastructure-generate-report.php`).
- **Export:** Download feedback data as Excel files (`faculty-export-to-excel.php`, `course-export-to-excel.php`, `infrastructure-export-to-excel.php`).
- **User Management:** Add or delete users (admins and students) via `view-users.php`.
- **Feedback Options:** Select feedback types to view (`view-feedback-options.php`).

**General Features**

- **Authentication:** Role-based login system (`login.php`, `authenticate.php`, `logout.php`) supporting students and admins.
- **Database:** MySQL database (`student_feedback`) with tables for `users`, `course_feedback`, `faculty_feedback`, and `infrastructure_feedback`.
- **AJAX:** Seamless form submissions and duplicate checks for a smooth user experience.
- **Visualizations:** Chart.js for graphical representation of feedback data in reports.

## Prerequisites

To run the College Feedback System, you need:

- **XAMPP:** For Apache and MySQL servers (download from https://www.apachefriends.org/).
- **Web Browser:** Chrome, Firefox, or any modern browser.
- **PHP:** Version 7.4 or higher (included in XAMPP).
- **MySQL:** Included in XAMPP.
- **Text Editor:** VS Code, Sublime Text, or any editor for code inspection (optional).

## Setup Instructions

**1. Install XAMPP:**

- Download and install XAMPP for your operating system.
- Ensure Apache and MySQL modules are enabled in the XAMPP Control Panel.

**2. Clone the Repository:**

- Clone this repository to your local machine:
- ```bash
  git clone https://github.com/your-username/college-feedback-system.git
  ```
- Alternatively, download the ZIP file and extract it.

**3. Move Project to XAMPP:**

- Copy the `college-feedback-system` folder to the `htdocs` directory in your XAMPP installation (e.g.,` C:\xampp\htdocs\` on Windows).

**4. Set Up the Database:**

- Open the XAMPP Control Panel and start the **Apache** and **MySQL** servers.

- Open your browser and navigate to` http://localhost/phpmyadmin`.

- Create a new database named `student_feedback`.

- Import the `table.sql` file from the project root:

- In phpMyAdmin, select the `student_feedback` database.

- Go to the **Import** tab, choose `table.sql`, and click **Go**.

**5. Configure Database Connection:**

- Ensure the database connection settings in PHP files (e.g., `authenticate.php`, `infrastructure-feedback.php`, `view-users.php`) match your setup:

- $conn = new mysqli("localhost", "root", "", "student_feedback");

- The default MySQL user is `root` with an empty password (`""`). Update if your MySQL setup uses different credentials.

**6. Test Database Connection:**

- Run `db_test.php` by navigating to `http://localhost/college-feedback-system/db_test.php.`

- Verify it outputs "Connected successfully".

## Running the Project

Start the XAMPP Control Panel.

Ensure Apache and MySQL servers are running.

Open a web browser and navigate to:

http://localhost/college-feedback-system/login.php

Log in with your credentials:

Admin: Access the admin dashboard, user management, feedback viewing, and reporting.

Student: Access the student dashboard to submit feedback.

Note: You may need to add users via view-users.php (admin access) or directly in the users table.

## Contributing

Contributions are welcome! Please fork the repository and submit pull requests for any enhancements or bug fixes.

## License

This project is licensed under the ISC License.

## Acknowledgments

- Inspired by advanced authentication research and interactive web technologies.
- Thanks to the open-source community for tools like Express.js and Mongoose.

```

```
