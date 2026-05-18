# 🎓 EduPortfolio — Electronic Student Portfolio System

A modern, clean, and fully functional digital student portfolio system built with **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**. No frameworks — beginner-friendly and easy to understand.

---

## ✨ Features

| Role | Features |
|------|----------|
| **Student** | Register/Login, Upload Work, View Grades & Feedback, Track Progress, Edit Profile |
| **Teacher** | View Submissions, Give Grades & Feedback, Monitor Students |
| **Admin** | Manage Users, View Reports & Analytics, Post Announcements, Monitor Activity |

---

## 🎨 Design

- **Light Mode**: `rgb(176, 190, 229)` accent
- **Dark Mode**: `rgb(32, 38, 57)` background
- Responsive sidebar + top navbar
- Dashboard cards with soft shadows
- Font Awesome 6 icons
- Google Fonts (DM Sans + Sora)
- Smooth hover effects & transitions
- Drag & drop file upload
- Progress bars & grade circles

---

## 🚀 Quick Setup

### 1. Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx with mod_rewrite (XAMPP, WAMP, or Laragon recommended)

### 2. Install Steps

```bash
# 1. Copy the portfolio/ folder into your web server root
#    e.g. C:/xampp/htdocs/portfolio/

# 2. Import the database
#    Open phpMyAdmin → Import → Select database.sql

# 3. Edit database config
#    Open includes/config.php and set your DB credentials

# 4. Visit in browser
#    http://localhost/portfolio/
```

### 3. Configure Database (`includes/config.php`)

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
define('DB_NAME', 'student_portfolio');
define('APP_URL', 'http://localhost/portfolio');
```

---

## 🔑 Demo Login Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@school.edu | password |
| Teacher | sarah@school.edu | password |
| Student | alice@student.edu | password |
| Student | bob@student.edu | password |

> Use the **Quick Demo Login** buttons on the login page!

---

## 📁 Project Structure

```
portfolio/
├── index.php                  ← Login/Register page
├── logout.php                 ← Session logout
├── database.sql               ← DB schema + sample data
├── assets/
│   ├── css/style.css          ← All styles (light/dark themes)
│   └── js/app.js              ← UI interactions
├── includes/
│   ├── config.php             ← DB config, helper functions
│   ├── layout.php             ← Sidebar + navbar (shared header)
│   └── layout_end.php         ← Closing tags (shared footer)
├── pages/
│   ├── student/
│   │   ├── dashboard.php
│   │   ├── upload.php
│   │   ├── submissions.php
│   │   ├── grades.php
│   │   ├── feedback.php
│   │   ├── profile.php
│   │   └── settings.php
│   ├── teacher/
│   │   ├── dashboard.php
│   │   ├── submissions.php
│   │   ├── grade.php
│   │   ├── students.php
│   │   └── settings.php
│   └── admin/
│       ├── dashboard.php
│       ├── users.php
│       ├── submissions.php
│       ├── reports.php
│       ├── announcements.php
│       └── settings.php
└── uploads/
    ├── assignments/           ← Student file uploads go here
    ├── certificates/
    └── profiles/
```

---

## 🗄️ Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Students, teachers, and admins |
| `submissions` | Uploaded assignments, projects, certificates |
| `grades` | Scores and teacher feedback per submission |
| `announcements` | School-wide announcements |
| `activity_log` | Login/action tracking for security |

---

## 🛠️ Customization Tips

- **Change colors**: Edit CSS variables in `assets/css/style.css` under `:root`
- **Add subjects**: Update the `<select>` in `upload.php`
- **Change school name**: Search for "EduPortfolio" and replace
- **File size limit**: Change `MAX_FILE_SIZE` in `config.php`
- **Allowed file types**: Edit `ALLOWED_TYPES` array in `config.php`

---

## 🔒 Security Notes

- All user input is sanitized with `htmlspecialchars()` via `clean()`
- Passwords hashed with `password_hash()` (bcrypt)
- Role-based access with `requireRole()` on every page
- PDO prepared statements prevent SQL injection
- Session-based authentication

---

## 📱 Responsive Breakpoints

- **Desktop**: Full sidebar + content grid
- **Tablet (≤1024px)**: Single column content
- **Mobile (≤768px)**: Hamburger menu, collapsible sidebar

---

*Built with ❤️ for schools replacing paper portfolios with digital systems.*
"# portfolio" 
