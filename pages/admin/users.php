<?php
// pages/admin/users.php
require_once '../../includes/config.php';
requireRole('admin');

$db = getDB();

// Handle delete user
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    if ($delId !== $_SESSION['user_id']) { // can't delete yourself
        $db->prepare("DELETE FROM users WHERE id=?")->execute([$delId]);
        setFlash('success', 'User deleted successfully.');
    } else {
        setFlash('error', 'You cannot delete your own account.');
    }
    header('Location: users.php'); exit();
}

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name  = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? 'password';
    $role  = $_POST['role'] ?? 'student';
    $dept  = trim($_POST['department'] ?? '');

    if (empty($name) || empty($email)) {
        setFlash('error', 'Name and email are required.');
    } else {
        $check = $db->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);
        if ($check->fetch()) {
            setFlash('error', 'Email already exists.');
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $db->prepare("INSERT INTO users (full_name,email,password,role,department) VALUES (?,?,?,?,?)")->execute([$name,$email,$hashed,$role,$dept]);
            logActivity($_SESSION['user_id'], 'admin', "Added user: $name ($role)");
            setFlash('success', "User $name added successfully!");
        }
    }
    header('Location: users.php'); exit();
}

// Filter
$roleFilter = $_GET['role'] ?? 'all';
$search     = trim($_GET['search'] ?? '');

$sql    = "SELECT u.*, COUNT(s.id) as sub_count FROM users u LEFT JOIN submissions s ON u.id=s.student_id WHERE 1=1";
$params = [];
if ($roleFilter !== 'all') { $sql .= " AND u.role=?"; $params[] = $roleFilter; }
if ($search) { $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$sql .= " GROUP BY u.id ORDER BY u.created_at DESC";
$stmt = $db->prepare($sql); $stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle  = 'Manage Users';
$activePage = 'users.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Manage Users</h2><p>Add, view, and remove system users.</p></div>
  <button onclick="document.getElementById('addUserModal').style.display='flex'" class="btn btn-secondary btn-sm">
    <i class="fas fa-user-plus"></i> Add User
  </button>
</div>

<!-- Search & Filter -->
<form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
  <input type="text" name="search" value="<?= clean($search) ?>" placeholder="Search by name or email..."
         style="padding:10px 14px;border:2px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-secondary);color:var(--text-primary);font-family:'DM Sans',sans-serif;flex:1;min-width:200px;outline:none;">
  <div style="display:flex;gap:6px;">
    <?php foreach (['all'=>'All','student'=>'Students','teacher'=>'Teachers','admin'=>'Admins'] as $k=>$v): ?>
    <button type="submit" name="role" value="<?= $k ?>" class="btn btn-sm <?= $roleFilter===$k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></button>
    <?php endforeach; ?>
  </div>
</form>

<!-- Users Table -->
<div class="card">
  <div class="card-body" style="padding:0;">
    <?php if (empty($users)): ?>
      <div class="empty-state"><i class="fas fa-users"></i><h3>No users found</h3></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>User</th><th>Email</th><th>Role</th><th>Department</th><th>Submissions</th><th>Joined</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u):
          $initials = strtoupper(substr($u['full_name'],0,1) . (strpos($u['full_name'],' ')!==false ? substr($u['full_name'],strpos($u['full_name'],' ')+1,1) : ''));
        ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;flex-shrink:0;"><?= $initials ?></div>
              <strong style="font-size:0.875rem;"><?= clean($u['full_name']) ?></strong>
            </div>
          </td>
          <td style="color:var(--text-muted);font-size:0.85rem;"><?= clean($u['email']) ?></td>
          <td><span class="badge-role <?= $u['role'] ?>"><?= $u['role'] ?></span></td>
          <td><?= clean($u['department'] ?: '—') ?></td>
          <td><?= $u['sub_count'] ?? 0 ?></td>
          <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
            <a href="?delete=<?= $u['id'] ?>&role=<?= $roleFilter ?>&search=<?= urlencode($search) ?>"
               class="btn btn-danger btn-sm btn-icon"
               data-confirm="Delete user '<?= clean($u['full_name']) ?>'? This cannot be undone.">
              <i class="fas fa-trash"></i>
            </a>
            <?php else: ?>
            <span style="color:var(--text-muted);font-size:0.8rem;">You</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center;padding:20px;">
  <div style="background:var(--bg-card);border-radius:var(--radius);padding:32px;width:100%;max-width:480px;box-shadow:var(--shadow-hover);border:1px solid var(--border);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
      <h3 style="font-family:'Sora',sans-serif;font-size:1.1rem;"><i class="fas fa-user-plus" style="color:var(--accent-dark);margin-right:8px;"></i>Add New User</h3>
      <button onclick="document.getElementById('addUserModal').style.display='none'" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.2rem;"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST">
      <input type="hidden" name="add_user" value="1">
      <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div>
      <div class="form-row">
        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="Default: password"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Role</label>
          <select name="role">
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="form-group"><label>Department</label><input type="text" name="department" placeholder="e.g. Computer Science"></div>
      </div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-primary" style="flex:1;"><i class="fas fa-plus"></i> Add User</button>
        <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../../includes/layout_end.php'; ?>
