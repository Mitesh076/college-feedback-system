# College Feedback System

## Overview

The **College Feedback System** is a web-based application designed to collect, manage, and analyze feedback from students about faculty, courses, and campus infrastructure. Built with PHP, MySQL, HTML, CSS, and JavaScript, the system provides a user-friendly interface for students to submit feedback and for administrators to view, report, and export feedback data. It features role-based access (students and admins), responsive design, and visualizations using Chart.js.

## Features

**Student Features**

- **Dashboard:** A centralized dashboard (`student-dashboard.php`) for students to access feedback forms for faculty, courses, and infrastructure.
- **Feedback Submission:**
  - **Faculty Feedback:** Rate faculty performance and teaching methods (1-10 scale for course outcomes, CO1-CO7).
   -  **Course Feedback:** Provide feedback on course content and structure (1-10 scale for CO1-CO7).
   -  **Infrastructure Feedback:** Rate campus facilities like classrooms, labs, library, Wi-Fi, washrooms, parking, canteen, and security (1-10 scale).
- **Duplicate Check:** Prevents multiple submissions for the same feedback type using AJAX-based checks.
- **Responsive UI:** Modern, mobile-friendly design with gradients and hover effects.

  **Admin Features**
  - **Dashboard:** Centralized admin dashboard (`admin-dashboard.php`) with links to manage users and view feedback.
 - **Feedback Management:**
 - -  View all feedback submissions (`faculty-display-feedback.php`, `course-display-feedback.php`, `infrastructure-display-feedback.php`) with filters for department, semester, section, and year.
  Delete specific feedback entries (faculty-delete-feedback.php, course-delete-feedback.php, infrastructure-delete-feedback.php).
  Reports: Generate reports with average ratings and bar charts (faculty-generate-report.php, course-generate-report.php, infrastructure-generate-report.php).
  Export: Download feedback data as Excel files (faculty-export-to-excel.php, course-export-to-excel.php, infrastructure-export-to-excel.php).
  User Management: Add or delete users (admins and students) via view-users.php.
  Feedback Options: Select feedback types to view (view-feedback-options.php).
  General Features
  Authentication: Role-based login system (login.php, authenticate.php, logout.php) supporting students and admins.
  Database: MySQL database (student_feedback) with tables for users, course feedback, faculty feedback, and infrastructure feedback.
  AJAX: Seamless form submissions and duplicate checks for a smooth user experience.
  Visualizations: Chart.js for graphical representation of feedback data in reports.

## Tech Stack

- _Frontend_: HTML, CSS (with Tailwind-inspired custom styles), JavaScript
- _Backend_: Node.js, Express.js
- _Database_: MongoDB with Mongoose
- _Dependencies_: bcrypt (for password hashing), express-session (for session management)

## Installation

1. _Clone the repository:_

   ```bash
   git clone https://github.com/ekalavya-cmd/graphical-auth-system.git
   ```

2. _Navigate to the project directory:_

   bash
   cd graphical-auth

3. _Install the required dependencies:_

   bash
   npm install

4. Set up MongoDB locally and ensure it is running. Update the connection string in server.js (e.g., mongodb://localhost/auth_system) if necessary.
5. _Start the server:_
   bash
   node server.js
6. Open your web browser and visit http://localhost:3000 to access the application.

## Usage

1. _Registration:_

- Navigate to the registration page by following the "New to GAS? Register" link on the login page.
- Complete the eight registration layers sequentially:
  - _Layer 1:_ Enter email (must end with @gas.com), username, and password.
  - _Layer 2:_ Mark at least 3 points on an image.
  - _Layer 3:_ Select at least 3 colors from a color wheel.
  - _Layer 4:_ Draw a pattern with at least 3 dots.
  - _Layer 5:_ Select at least 3 audio clips in sequence.
  - _Layer 6:_ Arrange a 3x3 puzzle with numbered pieces.
  - _Layer 7:_ Choose at least 3 emojis in sequence.
  - _Layer 8:_ Pair 3 left names with 3 right names using drag-and-drop.
- Submit each layer to proceed to the next until registration is complete.

2. _Login:_

- Enter your email or username on the login page and click "Next" to select an authentication layer.
- Complete the chosen layer's challenge (e.g., entering a password, marking points, etc.).
- Successfully passing all required layers redirects you to the homepage.

3. _Logout:_

- Click the "Logout" button on the homepage to end your session and return to the login page.

## Contributing

Contributions are welcome! Please fork the repository and submit pull requests for any enhancements or bug fixes.

## License

This project is licensed under the ISC License.

## Acknowledgments

- Inspired by advanced authentication research and interactive web technologies.
- Thanks to the open-source community for tools like Express.js and Mongoose.
