<?php
// pages/teacher/settings.php
require_once '../../includes/config.php';
requireRole('teacher');
$db = getDB(); $uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['full_name'] ?? '');
    $dept = trim($_POST['department'] ?? '');
    $bio  = trim($_POST['bio'] ?? '');
    if ($name) {
        $db->prepare("UPDATE users SET full_name=?,department=?,bio=? WHERE id=?")->execute([$name,$dept,$bio,$uid]);
        $_SESSION['full_name'] = $name;
        setFlash('success','Profile updated!');
    }
    header('Location: settings.php'); exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $new = $_POST['new_password'] ?? '';
    $cnf = $_POST['confirm_password'] ?? '';
    if (strlen($new) < 6) { setFlash('error','Password too short.'); }
    elseif ($new !== $cnf) { setFlash('error','Passwords do not match.'); }
    else { $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($new,PASSWORD_DEFAULT),$uid]); setFlash('success','Password updated!'); }
    header('Location: settings.php'); exit();
}

$user = getCurrentUser();
$pageTitle = 'Settings'; $activePage = 'settings.php'; $baseUrl = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header"><div><h2>Settings</h2><p>Manage your teacher account.</p></div></div>

<div class="content-grid">
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-user"></i> Edit Profile</h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="update_profile" value="1">
        <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="<?= clean($user['full_name']) ?>" required></div>
        <div class="form-group"><label>Department</label><input type="text" name="department" value="<?= clean($user['department'] ?? '') ?>"></div>
        <div class="form-group"><label>Bio</label><textarea name="bio"><?= clean($user['bio'] ?? '') ?></textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-lock"></i> Change Password</h3></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" required></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update</button>
      </form>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
