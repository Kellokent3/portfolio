-- ============================================
-- Electronic Student Portfolio System
-- Database Schema + Sample Data
-- ============================================

CREATE DATABASE IF NOT EXISTS student_portfolio;
USE student_portfolio;

-- USERS TABLE (students, teachers, admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher','admin') DEFAULT 'student',
    avatar VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    grade_level VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- SUBMISSIONS TABLE (assignments, projects, certificates)
CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    type ENUM('assignment','project','certificate') DEFAULT 'assignment',
    subject VARCHAR(100),
    file_path VARCHAR(255),
    file_name VARCHAR(255),
    status ENUM('pending','reviewed','approved','rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- GRADES TABLE
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    teacher_id INT NOT NULL,
    student_id INT NOT NULL,
    grade VARCHAR(10),
    score DECIMAL(5,2),
    max_score DECIMAL(5,2) DEFAULT 100,
    feedback TEXT,
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    FOREIGN KEY (student_id) REFERENCES users(id)
);

-- ANNOUNCEMENTS TABLE
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    audience ENUM('all','students','teachers') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- ACTIVITY LOG TABLE
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    details TEXT,
    ip_address VARCHAR(45),
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Admin: password = admin123
INSERT INTO users (full_name, email, password, role, department) VALUES
('System Admin', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administration');

-- Teachers: password = teacher123
INSERT INTO users (full_name, email, password, role, department) VALUES
('Dr. Sarah Johnson', 'sarah@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Computer Science'),
('Prof. Michael Chen', 'michael@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'Mathematics'),
('Ms. Amina Uwase', 'amina@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'English');

-- Students: password = student123
INSERT INTO users (full_name, email, password, role, department, grade_level, bio) VALUES
('Alice Mukamana', 'alice@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Computer Science', 'Year 2', 'Passionate about web development and design.'),
('Bob Habimana', 'bob@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Mathematics', 'Year 3', 'Math enthusiast and problem solver.'),
('Clara Niyonsaba', 'clara@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Computer Science', 'Year 1', 'Aspiring software engineer.'),
('David Nkurunziza', 'david@student.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'English', 'Year 2', 'Love literature and creative writing.');

-- Sample submissions
INSERT INTO submissions (student_id, title, description, type, subject, file_name, status) VALUES
(5, 'Web Design Final Project', 'A complete website for a local business using HTML, CSS and JavaScript.', 'project', 'Web Development', 'web_project.zip', 'approved'),
(5, 'Database Assignment 1', 'ER diagram and SQL queries for library management system.', 'assignment', 'Database Systems', 'db_assignment.pdf', 'reviewed'),
(5, 'HTML/CSS Certificate', 'Completed online course in HTML and CSS fundamentals.', 'certificate', 'Web Development', 'html_cert.pdf', 'approved'),
(6, 'Calculus Problem Set', 'Solutions to integration and differentiation problems.', 'assignment', 'Calculus', 'calculus_ps.pdf', 'reviewed'),
(6, 'Statistics Project', 'Data analysis of student performance trends using Excel.', 'project', 'Statistics', 'stats_project.xlsx', 'approved'),
(7, 'Python Basics Assignment', 'Beginner Python programs covering loops, functions, and lists.', 'assignment', 'Programming', 'python_basics.zip', 'pending'),
(8, 'Essay: Climate Change', 'A 2000-word argumentative essay on climate change impacts.', 'assignment', 'English Composition', 'essay_climate.docx', 'reviewed');

-- Sample grades and feedback
INSERT INTO grades (submission_id, teacher_id, student_id, grade, score, max_score, feedback) VALUES
(1, 2, 5, 'A', 92.00, 100, 'Excellent work! The website is clean and well-structured. Great use of CSS flexbox and responsive design. Keep it up!'),
(2, 2, 5, 'B+', 85.00, 100, 'Good understanding of database concepts. The ER diagram is accurate. Consider normalizing your tables further.'),
(4, 3, 6, 'A-', 88.00, 100, 'Well-solved problems. Show more working steps for partial credit in exams.'),
(5, 3, 6, 'A', 95.00, 100, 'Outstanding project! Your data visualization charts are insightful and the analysis is thorough.'),
(7, 4, 8, 'B', 82.00, 100, 'Strong arguments presented. Work on varying your sentence structure for more engaging writing.');

-- Sample announcements
INSERT INTO announcements (author_id, title, content, audience) VALUES
(1, 'Welcome to the New Portfolio System!', 'We are excited to launch our new digital portfolio platform. Students can now upload their work and track their academic progress online.', 'all'),
(2, 'Assignment Submission Deadline', 'All pending assignments for Computer Science must be submitted by end of this week.', 'students'),
(1, 'System Maintenance Notice', 'The system will undergo brief maintenance this Saturday from 2-4 AM.', 'all');

-- Activity log samples
INSERT INTO activity_log (user_id, action, details) VALUES
(5, 'login', 'Student logged in'),
(5, 'upload', 'Uploaded: Web Design Final Project'),
(2, 'grade', 'Graded submission ID 1'),
(6, 'login', 'Student logged in'),
(1, 'system', 'Admin accessed reports');
