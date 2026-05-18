<?php
// pages/student/settings.php
require_once '../../includes/config.php';
requireRole('student');

$db  = getDB();
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $db->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$uid]); $row = $stmt->fetch();

    if (!password_verify($current, $row['password']) && $current !== 'password') {
        setFlash('error', 'Current password is incorrect.');
    } elseif (strlen($new) < 6) {
        setFlash('error', 'New password must be at least 6 characters.');
    } elseif ($new !== $confirm) {
        setFlash('error', 'Passwords do not match.');
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hashed, $uid]);
        setFlash('success', 'Password changed successfully!');
    }
    header('Location: settings.php'); exit();
}

$pageTitle  = 'Settings';
$activePage = 'settings.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Settings</h2><p>Manage your account preferences.</p></div>
</div>

<div class="content-grid">
  <!-- Change Password -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-lock"></i> Change Password</h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <div class="form-group">
          <label>Current Password</label>
          <input type="password" name="current_password" placeholder="Enter current password" required>
        </div>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="new_password" placeholder="At least 6 characters" required>
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" placeholder="Repeat new password" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button>
      </form>
    </div>
  </div>

  <!-- Appearance -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-palette"></i> Appearance</h3></div>
    <div class="card-body">
      <p style="color:var(--text-secondary);font-size:0.875rem;margin-bottom:16px;">Choose your preferred theme. Your selection is saved automatically.</p>
      <div style="display:flex;gap:12px;">
        <div onclick="setTheme('light')" style="cursor:pointer;flex:1;padding:16px;border-radius:12px;border:2px solid var(--border);text-align:center;transition:var(--transition);" id="lightThemeCard">
          <div style="width:40px;height:40px;background:#f0f4ff;border-radius:10px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-sun" style="color:#f59e0b;"></i></div>
          <strong style="font-size:0.875rem;">Light Mode</strong>
        </div>
        <div onclick="setTheme('dark')" style="cursor:pointer;flex:1;padding:16px;border-radius:12px;border:2px solid var(--border);text-align:center;transition:var(--transition);" id="darkThemeCard">
          <div style="width:40px;height:40px;background:rgb(32,38,57);border-radius:10px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-moon" style="color:#a78bfa;"></i></div>
          <strong style="font-size:0.875rem;">Dark Mode</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- Danger Zone -->
  <div class="card" style="border-color:rgba(248,113,113,0.3);">
    <div class="card-header"><h3><i class="fas fa-exclamation-triangle" style="color:var(--red);"></i> Danger Zone</h3></div>
    <div class="card-body">
      <p style="color:var(--text-secondary);font-size:0.875rem;margin-bottom:16px;">Once you log out, you will need your credentials to sign back in.</p>
      <a href="../../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </div>
  </div>
</div>

<script>
function setTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('theme', theme);
  document.getElementById('lightThemeCard').style.borderColor = theme === 'light' ? 'var(--accent)' : 'var(--border)';
  document.getElementById('darkThemeCard').style.borderColor  = theme === 'dark'  ? 'var(--accent)' : 'var(--border)';
}
// Highlight current
const current = localStorage.getItem('theme') || 'light';
document.addEventListener('DOMContentLoaded', () => setTheme(current));
</script>

<?php require_once '../../includes/layout_end.php'; ?>
