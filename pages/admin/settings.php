<?php
// pages/admin/settings.php
require_once '../../includes/config.php';
requireRole('admin');

$db  = getDB();
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['full_name'] ?? '');
    if ($name) {
        $db->prepare("UPDATE users SET full_name=? WHERE id=?")->execute([$name, $uid]);
        $_SESSION['full_name'] = $name;
        setFlash('success', 'Profile updated!');
    }
    header('Location: settings.php'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new = $_POST['new_password'] ?? '';
    $cnf = $_POST['confirm_password'] ?? '';
    if (strlen($new) < 6) { setFlash('error', 'Password must be at least 6 characters.'); }
    elseif ($new !== $cnf) { setFlash('error', 'Passwords do not match.'); }
    else {
        $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new, PASSWORD_DEFAULT), $uid]);
        setFlash('success', 'Password updated!');
    }
    header('Location: settings.php'); exit();
}

// Clear old activity logs (older than 30 days)
if (isset($_GET['clear_logs'])) {
    $db->query("DELETE FROM activity_log WHERE logged_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    setFlash('success', 'Old activity logs cleared.');
    header('Location: settings.php'); exit();
}

$user    = getCurrentUser();
$logCount = $db->query("SELECT COUNT(*) FROM activity_log")->fetchColumn();
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$subCount  = $db->query("SELECT COUNT(*) FROM submissions")->fetchColumn();

$pageTitle  = 'Admin Settings';
$activePage = 'settings.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header"><div><h2>Admin Settings</h2><p>System and account management.</p></div></div>

<div class="content-grid">
  <!-- Profile Update -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-user-cog"></i> Admin Profile</h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="update_profile" value="1">
        <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="<?= clean($user['full_name']) ?>" required></div>
        <div class="form-group"><label>Email (read-only)</label><input type="email" value="<?= clean($user['email']) ?>" readonly style="opacity:0.6;cursor:not-allowed;"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Profile</button>
      </form>
    </div>
  </div>

  <!-- Password -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-lock"></i> Change Password</h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" required></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button>
      </form>
    </div>
  </div>
</div>

<!-- System Info -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-header"><h3><i class="fas fa-server"></i> System Information</h3></div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;">
      <?php
      $sysInfo = [
        ['label'=>'Total Users','value'=>$userCount,'icon'=>'fa-users','color'=>'var(--blue)'],
        ['label'=>'Total Submissions','value'=>$subCount,'icon'=>'fa-folder','color'=>'var(--purple)'],
        ['label'=>'Activity Logs','value'=>$logCount,'icon'=>'fa-history','color'=>'var(--orange)'],
        ['label'=>'PHP Version','value'=>phpversion(),'icon'=>'fa-code','color'=>'var(--green)'],
      ];
      foreach ($sysInfo as $s):
      ?>
      <div style="padding:16px;background:var(--bg-primary);border-radius:12px;text-align:center;border:1px solid var(--border);">
        <div style="font-size:1.3rem;color:<?= $s['color'] ?>;margin-bottom:6px;"><i class="fas <?= $s['icon'] ?>"></i></div>
        <strong style="font-family:'Sora',sans-serif;font-size:1rem;display:block;"><?= clean($s['value']) ?></strong>
        <small style="color:var(--text-muted)"><?= $s['label'] ?></small>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Danger Zone -->
<div class="card" style="border-color:rgba(248,113,113,0.3);">
  <div class="card-header"><h3 style="color:var(--red);"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3></div>
  <div class="card-body">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:rgba(248,113,113,0.06);border-radius:10px;border:1px solid rgba(248,113,113,0.2);gap:16px;flex-wrap:wrap;">
      <div>
        <strong style="font-size:0.875rem;">Clear Old Activity Logs</strong>
        <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px;">Remove activity logs older than 30 days to free up space.</p>
      </div>
      <a href="?clear_logs=1" class="btn btn-danger btn-sm" data-confirm="Clear all old activity logs?"
         style="white-space:nowrap;">
        <i class="fas fa-trash"></i> Clear Logs
      </a>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
