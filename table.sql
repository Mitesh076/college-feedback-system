-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    role ENUM('student', 'admin') NOT NULL
);

-- Create the faculty feedback table
CREATE TABLE faculty_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(10),
    sem VARCHAR(10),
    branch VARCHAR(10),
    section VARCHAR(10),
    name VARCHAR(100),
    enrollment VARCHAR(100),
    faculty_name VARCHAR(100),
    subject VARCHAR(100),
    q1 INT,
    q2 INT,
    q3 INT,
    q4 INT,

    remarks TEXT
);

-
-- Create the infrastructure feedback table
CREATE TABLE infrastructure_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(10),
    sem VARCHAR(10),
    branch VARCHAR(10),
    section VARCHAR(10),
    name VARCHAR(100),
    enrollment VARCHAR(100),
    q1 INT,
    q2 INT,
    q3 INT,
    q4 INT,
    remarks TEXT
);
-- Main feedback table
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    enrollment VARCHAR(20) NOT NULL,
    year VARCHAR(4) NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester VARCHAR(2) NOT NULL,
    section VARCHAR(2) NOT NULL,
    subject_code VARCHAR(50) NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    comments TEXT,
    
    -- Adding columns for course outcomes
    co1_text TEXT,
    co1_rating INT,
    co2_text TEXT,
    co2_rating INT,
    co3_text TEXT,
    co3_rating INT,
    co4_text TEXT,
    co4_rating INT,
    co5_text TEXT,
    co5_rating INT,
    co6_text TEXT,
    co6_rating INT,
    
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (enrollment),
    INDEX (department, semester),
    
    -- Add constraints for ratings (1-5)
    CONSTRAINT chk_co1_rating CHECK (co1_rating IS NULL OR (co1_rating BETWEEN 1 AND 5)),
    CONSTRAINT chk_co2_rating CHECK (co2_rating IS NULL OR (co2_rating BETWEEN 1 AND 5)),
    CONSTRAINT chk_co3_rating CHECK (co3_rating IS NULL OR (co3_rating BETWEEN 1 AND 5)),
    CONSTRAINT chk_co4_rating CHECK (co4_rating IS NULL OR (co4_rating BETWEEN 1 AND 5)),
    CONSTRAINT chk_co5_rating CHECK (co5_rating IS NULL OR (co5_rating BETWEEN 1 AND 5)),
    CONSTRAINT chk_co6_rating CHECK (co6_rating IS NULL OR (co6_rating BETWEEN 1 AND 5))
);
CREATE TABLE `course_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `enrollment_number` varchar(50) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `department` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `section` varchar(10) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `co1_text` text DEFAULT NULL,
  `co1_rating` int(11) DEFAULT NULL,
  `co2_text` text DEFAULT NULL,
  `co2_rating` int(11) DEFAULT NULL,
  `co3_text` text DEFAULT NULL,
  `co3_rating` int(11) DEFAULT NULL,
  `co4_text` text DEFAULT NULL,
  `co4_rating` int(11) DEFAULT NULL,
  `co5_text` text DEFAULT NULL,
  `co5_rating` int(11) DEFAULT NULL,
  `co6_text` text DEFAULT NULL,
  `co6_rating` int(11) DEFAULT NULL,
  `co7_text` text DEFAULT NULL,
  `co7_rating` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `submission_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_feedback` (`enrollment_number`,`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `faculty_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `enrollment_number` varchar(50) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `department` varchar(10) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `section` varchar(10) NOT NULL,
  `teacher` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `co1_text` text DEFAULT NULL,
  `co1_rating` int(11) DEFAULT NULL,
  `co2_text` text DEFAULT NULL,
  `co2_rating` int(11) DEFAULT NULL,
  `co3_text` text DEFAULT NULL,
  `co3_rating` int(11) DEFAULT NULL,
  `co4_text` text DEFAULT NULL,
  `co4_rating` int(11) DEFAULT NULL,
  `co5_text` text DEFAULT NULL,
  `co5_rating` int(11) DEFAULT NULL,
  `co6_text` text DEFAULT NULL,
  `co6_rating` int(11) DEFAULT NULL,
  `co7_text` text DEFAULT NULL,
  `co7_rating` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `submission_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_feedback` (`enrollment_number`,`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE infrastructure_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    enrollment_number VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    department VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    section VARCHAR(10) NOT NULL,
    classroom_rating INT NOT NULL,
    classroom_text TEXT,
    lab_rating INT NOT NULL,
    lab_text TEXT,
    library_rating INT NOT NULL,
    library_text TEXT,
    wifi_rating INT NOT NULL,
    wifi_text TEXT,
    washroom_rating INT NOT NULL,
    washroom_text TEXT,
    parking_rating INT NOT NULL,
    parking_text TEXT,
    canteen_rating INT NOT NULL,
    canteen_text TEXT,
    security_rating INT NOT NULL,
    security_text TEXT,
    overall_rating INT NOT NULL,
    general_comments TEXT,
    submission_date DATETIME NOT NULL
);