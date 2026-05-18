<?php
// includes/layout.php - Shared sidebar + navbar layout
// Requires $pageTitle and $activePage to be set before including

$user = getCurrentUser();
$initials = strtoupper(substr($user['full_name'], 0, 1) . (strpos($user['full_name'], ' ') ? substr($user['full_name'], strpos($user['full_name'], ' ') + 1, 1) : ''));
$role = $user['role'];

// Define nav items per role
$navItems = [];
if ($role === 'student') {
    $navItems = [
        ['icon' => 'fa-home', 'label' => 'Dashboard', 'href' => 'dashboard.php'],
        ['icon' => 'fa-user', 'label' => 'My Profile', 'href' => 'profile.php'],
        ['icon' => 'fa-cloud-upload-alt', 'label' => 'Upload Work', 'href' => 'upload.php'],
        ['icon' => 'fa-folder-open', 'label' => 'My Submissions', 'href' => 'submissions.php'],
        ['icon' => 'fa-star', 'label' => 'Grades', 'href' => 'grades.php'],
        ['icon' => 'fa-comments', 'label' => 'Feedback', 'href' => 'feedback.php'],
        ['icon' => 'fa-cog', 'label' => 'Settings', 'href' => 'settings.php'],
    ];
} elseif ($role === 'teacher') {
    $navItems = [
        ['icon' => 'fa-home', 'label' => 'Dashboard', 'href' => 'dashboard.php'],
        ['icon' => 'fa-inbox', 'label' => 'Submissions', 'href' => 'submissions.php'],
        ['icon' => 'fa-pen', 'label' => 'Give Grades', 'href' => 'grade.php'],
        ['icon' => 'fa-users', 'label' => 'Students', 'href' => 'students.php'],
        ['icon' => 'fa-cog', 'label' => 'Settings', 'href' => 'settings.php'],
    ];
} elseif ($role === 'admin') {
    $navItems = [
        ['icon' => 'fa-home', 'label' => 'Dashboard', 'href' => 'dashboard.php'],
        ['icon' => 'fa-users', 'label' => 'Users', 'href' => 'users.php'],
        ['icon' => 'fa-folder', 'label' => 'Submissions', 'href' => 'submissions.php'],
        ['icon' => 'fa-chart-bar', 'label' => 'Reports', 'href' => 'reports.php'],
        ['icon' => 'fa-bullhorn', 'label' => 'Announcements', 'href' => 'announcements.php'],
        ['icon' => 'fa-cog', 'label' => 'Settings', 'href' => 'settings.php'],
    ];
}

// $baseUrl = '../';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= clean($pageTitle ?? 'EduPortfolio') ?> — EduPortfolio</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
    <div>
      <h2>EduPortfolio</h2>
      <span><?= ucfirst($role) ?> Portal</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Navigation</div>
    <?php foreach ($navItems as $item): ?>
      <a href="<?= $item['href'] ?>"
         class="nav-item <?= ($activePage ?? '') === $item['href'] ? 'active' : '' ?>">
        <i class="fas <?= $item['icon'] ?>"></i>
        <?= $item['label'] ?>
      </a>
    <?php endforeach; ?>

    <div class="nav-section-label" style="margin-top:16px;">Account</div>
    <a href="<?= $baseUrl ?>logout.php" class="nav-item" style="color:var(--red);">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-mini">
      <div class="avatar"><?= $initials ?></div>
      <div class="info">
        <strong><?= clean($user['full_name']) ?></strong>
        <span><?= clean($user['role']) ?></span>
      </div>
    </div>
  </div>
</aside>

<!-- ===== TOP NAVBAR ===== -->
<nav class="topnav">
  <button class="topnav-btn hamburger" id="hamburger" title="Menu">
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
    <a href="<?= $baseUrl ?>pages/<?= $role ?>/profile.php" class="topnav-btn" title="Profile" style="text-decoration:none;">
      <i class="fas fa-user"></i>
    </a>
  </div>
</nav>

<!-- ===== MAIN CONTENT ===== -->
<main class="main-content">
  <div class="page-content">

    <?php
    // Show flash message if any
    $flash = getFlash();
    if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'error' : 'info') ?>">
        <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
        <?= clean($flash['message']) ?>
      </div>
    <?php endif; ?>
