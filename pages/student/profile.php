<?php
// pages/student/profile.php
require_once '../../includes/config.php';
requireRole('student');

$db  = getDB();
$uid = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['full_name'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $bio    = trim($_POST['bio'] ?? '');
    $dept   = trim($_POST['department'] ?? '');
    $grade  = trim($_POST['grade_level'] ?? '');

    if (empty($name)) {
        setFlash('error', 'Name cannot be empty.');
    } else {
        $stmt = $db->prepare("UPDATE users SET full_name=?, phone=?, bio=?, department=?, grade_level=? WHERE id=?");
        $stmt->execute([$name, $phone, $bio, $dept, $grade, $uid]);
        $_SESSION['full_name'] = $name;
        setFlash('success', 'Profile updated successfully!');
        header('Location: profile.php'); exit();
    }
}

$user = getCurrentUser();
$initials = strtoupper(substr($user['full_name'], 0, 1) . (strpos($user['full_name'], ' ') !== false ? substr($user['full_name'], strpos($user['full_name'], ' ') + 1, 1) : ''));

// Portfolio stats
$stats = $db->prepare("SELECT
    COUNT(*) as total,
    SUM(type='assignment') as assignments,
    SUM(type='project') as projects,
    SUM(type='certificate') as certificates,
    SUM(status='approved') as approved
    FROM submissions WHERE student_id=?");
$stats->execute([$uid]); $pStats = $stats->fetch();

$pageTitle  = 'My Profile';
$activePage = 'profile.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<!-- Profile Header -->
<div class="profile-header">
  <div class="profile-avatar"><?= $initials ?></div>
  <div class="profile-info">
    <h2><?= clean($user['full_name']) ?></h2>
    <p><?= clean($user['email']) ?></p>
    <div class="profile-tags">
      <span class="profile-tag"><i class="fas fa-graduation-cap"></i> <?= clean($user['grade_level'] ?: 'Student') ?></span>
      <span class="profile-tag"><i class="fas fa-building"></i> <?= clean($user['department'] ?: 'Not set') ?></span>
      <?php if ($user['phone']): ?><span class="profile-tag"><i class="fas fa-phone"></i> <?= clean($user['phone']) ?></span><?php endif; ?>
    </div>
    <?php if ($user['bio']): ?><p style="margin-top:10px;font-size:0.875rem;color:var(--text-secondary);"><?= clean($user['bio']) ?></p><?php endif; ?>
  </div>
  <!-- Quick stats -->
  <div style="display:flex;gap:20px;margin-left:auto;flex-wrap:wrap;">
    <?php foreach ([['📁',$pStats['total'],'Total'],['✅',$pStats['approved'],'Approved'],['🏆',$pStats['certificates'],'Certs']] as $s): ?>
    <div style="text-align:center;">
      <div style="font-size:1.4rem;"><?= $s[0] ?></div>
      <strong style="font-family:'Sora',sans-serif;font-size:1.3rem;display:block;"><?= $s[1] ?></strong>
      <small style="color:var(--text-muted)"><?= $s[2] ?></small>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="content-grid">
  <!-- Edit Profile Form -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-edit"></i> Edit Profile</h3></div>
    <div class="card-body">
      <form method="POST">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="full_name" value="<?= clean($user['full_name']) ?>" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Department</label>
            <input type="text" name="department" value="<?= clean($user['department'] ?? '') ?>" placeholder="e.g. Computer Science">
          </div>
          <div class="form-group">
            <label>Grade / Year Level</label>
            <input type="text" name="grade_level" value="<?= clean($user['grade_level'] ?? '') ?>" placeholder="e.g. Year 2">
          </div>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone" value="<?= clean($user['phone'] ?? '') ?>" placeholder="+250 ...">
        </div>
        <div class="form-group">
          <label>Bio / About Me</label>
          <textarea name="bio" placeholder="Tell us about yourself..."><?= clean($user['bio'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </form>
    </div>
  </div>

  <!-- Portfolio Summary -->
  <div>
    <div class="card">
      <div class="card-header"><h3><i class="fas fa-chart-pie"></i> Portfolio Summary</h3></div>
      <div class="card-body">
        <?php
        $items = [
          ['label'=>'Assignments', 'val'=>$pStats['assignments'], 'max'=>max(1,$pStats['total']), 'color'=>'var(--blue)'],
          ['label'=>'Projects',    'val'=>$pStats['projects'],    'max'=>max(1,$pStats['total']), 'color'=>'var(--purple)'],
          ['label'=>'Certificates','val'=>$pStats['certificates'],'max'=>max(1,$pStats['total']), 'color'=>'var(--orange)'],
          ['label'=>'Approved',    'val'=>$pStats['approved'],    'max'=>max(1,$pStats['total']), 'color'=>'var(--green)'],
        ];
        foreach ($items as $item):
          $pct = $item['max'] > 0 ? round(($item['val'] / $item['max']) * 100) : 0;
        ?>
        <div style="margin-bottom:18px;">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:0.875rem;font-weight:500;"><?= $item['label'] ?></span>
            <span style="font-size:0.875rem;color:var(--text-muted)"><?= $item['val'] ?></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" data-width="<?= $pct ?>" style="background:<?= $item['color'] ?>;"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card" style="margin-top:20px;">
      <div class="card-header"><h3><i class="fas fa-shield-alt"></i> Account Info</h3></div>
      <div class="card-body">
        <div style="display:flex;flex-direction:column;gap:12px;">
          <div style="display:flex;justify-content:space-between;padding:10px;background:var(--bg-primary);border-radius:8px;">
            <span style="color:var(--text-secondary);font-size:0.875rem;">Email</span>
            <span style="font-size:0.875rem;font-weight:500;"><?= clean($user['email']) ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px;background:var(--bg-primary);border-radius:8px;">
            <span style="color:var(--text-secondary);font-size:0.875rem;">Role</span>
            <span class="badge-role student">Student</span>
          </div>
          <div style="display:flex;justify-content:space-between;padding:10px;background:var(--bg-primary);border-radius:8px;">
            <span style="color:var(--text-secondary);font-size:0.875rem;">Member since</span>
            <span style="font-size:0.875rem;"><?= date('M Y', strtotime($user['created_at'])) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
