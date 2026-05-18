<?php
// index.php - Login & Register Page
require_once 'includes/config.php';

if (isLoggedIn()) {
    $role = $_SESSION['role'];
    header("Location: " . APP_URL . "/pages/$role/dashboard.php");
    exit();
}

$error = '';
$success = '';
$mode = $_GET['mode'] ?? 'login';

// Handle LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        // Accept demo password 'password' or real hashed password
        if ($user && (password_verify($password, $user['password']) || $password === 'password')) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['full_name'];
            logActivity($user['id'], 'login', 'User logged in');
            header("Location: " . APP_URL . "/pages/{$user['role']}/dashboard.php");
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

// Handle REGISTER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $name     = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $dept     = trim($_POST['department'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (full_name, email, password, role, department) VALUES (?, ?, ?, 'student', ?)");
            $stmt->execute([$name, $email, $hash, $dept]);
            $success = 'Account created! Please sign in.';
            $mode = 'login';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduPortfolio — Sign In</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <style>
    .auth-tabs{display:flex;gap:4px;background:var(--bg-primary);padding:4px;border-radius:12px;margin-bottom:24px;}
    .auth-tab{flex:1;padding:10px;text-align:center;border-radius:9px;cursor:pointer;font-weight:600;font-size:.875rem;transition:all .2s;color:var(--text-muted);border:none;background:transparent;font-family:'DM Sans',sans-serif;}
    .auth-tab.active{background:var(--bg-card);color:var(--text-primary);box-shadow:0 2px 8px rgba(0,0,0,.08);}
    .demo-box{background:var(--accent-light);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-top:20px;}
    .demo-box h4{font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--accent-dark);margin-bottom:8px;}
    .demo-row{display:flex;justify-content:space-between;font-size:.8rem;color:var(--text-secondary);padding:3px 0;}
    .demo-row span:last-child{font-family:monospace;color:var(--text-primary);font-weight:600;}
    .theme-toggle-auth{position:fixed;top:20px;right:20px;}
  </style>
</head>
<body>
<button class="topnav-btn theme-toggle-auth" id="themeToggle" title="Toggle theme"><i class="fas fa-moon"></i></button>

<div class="auth-page">
  <div class="auth-container">
    <div class="auth-logo">
      <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
      <h1>EduPortfolio</h1>
      <p>Your complete digital academic portfolio</p>
    </div>

    <div class="auth-card">
      <div class="auth-tabs">
        <button class="auth-tab <?= $mode==='login'?'active':'' ?>" onclick="switchMode('login')">Sign In</button>
        <button class="auth-tab <?= $mode==='register'?'active':'' ?>" onclick="switchMode('register')">Register</button>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= clean($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= clean($success) ?></div>
      <?php endif; ?>

      <!-- LOGIN -->
      <div id="loginForm" style="display:<?= $mode==='login'?'block':'none' ?>">
        <h2>Welcome back!</h2>
        <p class="subtitle">Sign in to access your portfolio</p>
        <form method="POST">
          <input type="hidden" name="action" value="login">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="your@email.com" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Sign In</button>
        </form>
        <div class="demo-box">
          <h4><i class="fas fa-key"></i> Demo Credentials</h4>
          <div class="demo-row"><span>Student</span><span>alice@student.edu / password</span></div>
          <div class="demo-row"><span>Teacher</span><span>sarah@school.edu / password</span></div>
          <div class="demo-row"><span>Admin</span><span>admin@school.edu / password</span></div>
        </div>
      </div>

      <!-- REGISTER -->
      <div id="registerForm" style="display:<?= $mode==='register'?'block':'none' ?>">
        <h2>Create Account</h2>
        <p class="subtitle">Join and start building your portfolio</p>
        <form method="POST">
          <input type="hidden" name="action" value="register">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="full_name" placeholder="Your full name" required>
          </div>
          <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" placeholder="your@email.com" required>
          </div>
          <div class="form-group">
            <label>Department</label>
            <input type="text" name="department" placeholder="e.g. Computer Science">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Password *</label>
              <input type="password" name="password" placeholder="Min. 6 chars" required>
            </div>
            <div class="form-group">
              <label>Confirm Password *</label>
              <input type="password" name="confirm_password" placeholder="Repeat" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Create Account</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<script>
function switchMode(mode) {
  document.getElementById('loginForm').style.display = mode==='login'?'block':'none';
  document.getElementById('registerForm').style.display = mode==='register'?'block':'none';
  document.querySelectorAll('.auth-tab').forEach((t,i)=>{
    t.classList.toggle('active',(i===0&&mode==='login')||(i===1&&mode==='register'));
  });
}
</script>
</body>
</html>
