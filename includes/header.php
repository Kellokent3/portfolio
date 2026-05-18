<?php
// includes/header.php - Shared top navbar + sidebar
// Requires: $pageTitle, $activePage, $user (from getCurrentUser())
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= clean($pageTitle ?? 'Dashboard') ?> — EduPortfolio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
    <div>
      <h2>EduPortfolio</h2>
      <span>Digital Learning Hub</span>
    </div>
  </div>

  <nav class="sidebar-nav">

    <?php if ($user['role'] === 'student'): ?>
    <!-- STUDENT NAV -->
    <div class="nav-section-label">Main</div>
    <a href="<?= APP_URL ?>/pages/student/dashboard.php" class="nav-item <?= $activePage==='dashboard'?'active':'' ?>">
      <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="<?= APP_URL ?>/pages/student/profile.php" class="nav-item <?= $activePage==='profile'?'active':'' ?>">
      <i class="fas fa-user"></i> My Profile
    </a>

    <div class="nav-section-label">Portfolio</div>
    <a href="<?= APP_URL ?>/pages/student/upload.php" class="nav-item <?= $activePage==='upload'?'active':'' ?>">
      <i class="fas fa-cloud-upload-alt"></i> Upload Work
    </a>
    <a href="<?= APP_URL ?>/pages/student/submissions.php" class="nav-item <?= $activePage==='submissions'?'active':'' ?>">
      <i class="fas fa-folder-open"></i> My Submissions
    </a>
    <a href="<?= APP_URL ?>/pages/student/grades.php" class="nav-item <?= $activePage==='grades'?'active':'' ?>">
      <i class="fas fa-star"></i> Grades
    </a>
    <a href="<?= APP_URL ?>/pages/student/feedback.php" class="nav-item <?= $activePage==='feedback'?'active':'' ?>">
      <i class="fas fa-comments"></i> Feedback
    </a>

    <div class="nav-section-label">Account</div>
    <a href="<?= APP_URL ?>/pages/student/settings.php" class="nav-item <?= $activePage==='settings'?'active':'' ?>">
      <i class="fas fa-cog"></i> Settings
    </a>

    <?php elseif ($user['role'] === 'teacher'): ?>
    <!-- TEACHER NAV -->
    <div class="nav-section-label">Main</div>
    <a href="<?= APP_URL ?>/pages/teacher/dashboard.php" class="nav-item <?= $activePage==='dashboard'?'active':'' ?>">
      <i class="fas fa-home"></i> Dashboard
    </a>

    <div class="nav-section-label">Review</div>
    <a href="<?= APP_URL ?>/pages/teacher/submissions.php" class="nav-item <?= $activePage==='submissions'?'active':'' ?>">
      <i class="fas fa-inbox"></i> Submissions
    </a>
    <a href="<?= APP_URL ?>/pages/teacher/students.php" class="nav-item <?= $activePage==='students'?'active':'' ?>">
      <i class="fas fa-users"></i> Students
    </a>
    <a href="<?= APP_URL ?>/pages/teacher/grades.php" class="nav-item <?= $activePage==='grades'?'active':'' ?>">
      <i class="fas fa-star"></i> Grades Given
    </a>

    <div class="nav-section-label">Account</div>
    <a href="<?= APP_URL ?>/pages/teacher/settings.php" class="nav-item <?= $activePage==='settings'?'active':'' ?>">
      <i class="fas fa-cog"></i> Settings
    </a>

    <?php elseif ($user['role'] === 'admin'): ?>
    <!-- ADMIN NAV -->
    <div class="nav-section-label">Main</div>
    <a href="<?= APP_URL ?>/pages/admin/dashboard.php" class="nav-item <?= $activePage==='dashboard'?'active':'' ?>">
      <i class="fas fa-home"></i> Dashboard
    </a>

    <div class="nav-section-label">Manage</div>
    <a href="<?= APP_URL ?>/pages/admin/users.php" class="nav-item <?= $activePage==='users'?'active':'' ?>">
      <i class="fas fa-users"></i> All Users
    </a>
    <a href="<?= APP_URL ?>/pages/admin/submissions.php" class="nav-item <?= $activePage==='submissions'?'active':'' ?>">
      <i class="fas fa-folder"></i> All Submissions
    </a>
    <a href="<?= APP_URL ?>/pages/admin/reports.php" class="nav-item <?= $activePage==='reports'?'active':'' ?>">
      <i class="fas fa-chart-bar"></i> Reports
    </a>
    <a href="<?= APP_URL ?>/pages/admin/activity.php" class="nav-item <?= $activePage==='activity'?'active':'' ?>">
      <i class="fas fa-list-alt"></i> Activity Log
    </a>

    <div class="nav-section-label">Account</div>
    <a href="<?= APP_URL ?>/pages/admin/settings.php" class="nav-item <?= $activePage==='settings'?'active':'' ?>">
      <i class="fas fa-cog"></i> Settings
    </a>
    <?php endif; ?>

  </nav>

  <div class="sidebar-footer">
    <div class="user-mini">
      <div class="avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
      <div class="info">
        <strong><?= clean($user['full_name']) ?></strong>
        <span><?= clean($user['role']) ?></span>
      </div>
      <a href="<?= APP_URL ?>/logout.php" title="Logout" style="color:var(--text-muted);font-size:1rem;">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </div>
</aside>

<!-- ===== TOP NAVBAR ===== -->
<nav class="topnav">
  <button class="topnav-btn hamburger" id="hamburger">
    <i class="fas fa-bars"></i>
  </button>
  <div class="topnav-title">
    <h1><?= clean($pageTitle ?? 'Dashboard') ?></h1>
    <p><?= date('l, F j, Y') ?></p>
  </div>
  <div class="topnav-right">
    <button class="topnav-btn" id="themeToggle" title="Toggle theme">
      <i class="fas fa-moon"></i>
    </button>
    <a href="<?= APP_URL ?>/logout.php" class="topnav-btn" title="Logout">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</nav>

<!-- ===== MAIN CONTENT STARTS ===== -->
<div class="app-layout">
  <div class="main-content">
    <div class="page-content">

<?php
// Show flash message if any
$flash = getFlash();
if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>">
    <i class="fas <?= $flash['type']==='success' ? 'fa-check-circle' : ($flash['type']==='error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?>"></i>
    <?= clean($flash['message']) ?>
  </div>
<?php endif; ?>
