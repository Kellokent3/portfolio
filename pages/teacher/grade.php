<?php
// pages/teacher/grade.php
require_once '../../includes/config.php';
requireRole('teacher');

$db  = getDB();
$tid = $_SESSION['user_id'];
$sid = intval($_GET['id'] ?? 0);

if (!$sid) { header('Location: submissions.php'); exit(); }

// Get submission + student info
$stmt = $db->prepare("SELECT s.*, u.full_name as student_name, u.email as student_email, u.department FROM submissions s JOIN users u ON s.student_id=u.id WHERE s.id=?");
$stmt->execute([$sid]); $sub = $stmt->fetch();
if (!$sub) { header('Location: submissions.php'); exit(); }

// Check existing grade
$gStmt = $db->prepare("SELECT * FROM grades WHERE submission_id=?");
$gStmt->execute([$sid]); $existing = $gStmt->fetch();

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score    = floatval($_POST['score'] ?? 0);
    $grade    = trim($_POST['grade'] ?? '');
    $feedback = trim($_POST['feedback'] ?? '');
    $status   = $_POST['status'] ?? 'reviewed';

    if ($score < 0 || $score > 100) {
        setFlash('error', 'Score must be between 0 and 100.');
    } else {
        if ($existing) {
            $db->prepare("UPDATE grades SET score=?, grade=?, feedback=?, teacher_id=?, graded_at=NOW() WHERE submission_id=?")->execute([$score, $grade, $feedback, $tid, $sid]);
        } else {
            $db->prepare("INSERT INTO grades (submission_id, teacher_id, student_id, grade, score, max_score, feedback) VALUES (?,?,?,?,?,100,?)")->execute([$sid, $tid, $sub['student_id'], $grade, $score, $feedback]);
        }
        $db->prepare("UPDATE submissions SET status=? WHERE id=?")->execute([$status, $sid]);
        logActivity($tid, 'grade', "Graded submission ID $sid");
        setFlash('success', 'Grade saved successfully!');
        header('Location: submissions.php'); exit();
    }
}

$pageTitle  = 'Grade Submission';
$activePage = 'grade.php';
$baseUrl    = '../../';
require_once '../../includes/layout.php';
?>

<div class="page-header">
  <div><h2>Grade Submission</h2><p>Review the student's work and provide feedback.</p></div>
  <a href="submissions.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="content-grid">
  <!-- Submission Details -->
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-file-alt"></i> Submission Details</h3></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:14px;">
        <div>
          <label style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Title</label>
          <p style="font-weight:600;font-size:1rem;margin-top:2px;"><?= clean($sub['title']) ?></p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div style="padding:10px;background:var(--bg-primary);border-radius:8px;">
            <label style="font-size:0.75rem;color:var(--text-muted);">Student</label>
            <p style="font-weight:500;font-size:0.875rem;"><?= clean($sub['student_name']) ?></p>
          </div>
          <div style="padding:10px;background:var(--bg-primary);border-radius:8px;">
            <label style="font-size:0.75rem;color:var(--text-muted);">Type</label>
            <p><span class="badge-status approved"><?= clean($sub['type']) ?></span></p>
          </div>
          <div style="padding:10px;background:var(--bg-primary);border-radius:8px;">
            <label style="font-size:0.75rem;color:var(--text-muted);">Subject</label>
            <p style="font-size:0.875rem;"><?= clean($sub['subject'] ?: '—') ?></p>
          </div>
          <div style="padding:10px;background:var(--bg-primary);border-radius:8px;">
            <label style="font-size:0.75rem;color:var(--text-muted);">Department</label>
            <p style="font-size:0.875rem;"><?= clean($sub['department'] ?: '—') ?></p>
          </div>
        </div>
        <?php if ($sub['description']): ?>
        <div>
          <label style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Description</label>
          <p style="font-size:0.875rem;color:var(--text-secondary);margin-top:4px;line-height:1.7;"><?= clean($sub['description']) ?></p>
        </div>
        <?php endif; ?>
        <?php if ($sub['file_name']): ?>
        <div style="padding:12px;background:var(--accent-light);border-radius:10px;display:flex;align-items:center;gap:10px;">
          <i class="fas fa-paperclip" style="color:var(--accent-dark);"></i>
          <span style="font-size:0.875rem;font-weight:500;"><?= clean($sub['file_name']) ?></span>
          <?php if (!empty($sub['file_path'])): ?>
          <a href="<?= APP_URL . '/' . $sub['file_path'] ?>" download class="btn btn-sm btn-secondary" style="margin-left:auto;" title="Download file">
            <i class="fas fa-download"></i>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <small style="color:var(--text-muted);">Submitted: <?= date('M j, Y g:i A', strtotime($sub['submitted_at'])) ?></small>
      </div>
    </div>
  </div>

  <!-- Grading Form -->
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-pen"></i> <?= $existing ? 'Update Grade' : 'Add Grade' ?></h3>
      <?php if ($existing): ?><span class="badge-status reviewed">Already graded</span><?php endif; ?>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Score (out of 100) <span style="color:var(--red)">*</span></label>
            <input type="number" name="score" min="0" max="100" step="0.5"
                   value="<?= $existing['score'] ?? '' ?>" placeholder="e.g. 85" required>
          </div>
          <div class="form-group">
            <label>Letter Grade</label>
            <select name="grade">
              <?php foreach (['A+','A','A-','B+','B','B-','C+','C','C-','D','F'] as $g): ?>
              <option value="<?= $g ?>" <?= ($existing['grade'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Submission Status</label>
          <select name="status">
            <option value="reviewed"  <?= ($sub['status'] ?? '') === 'reviewed'  ? 'selected':'' ?>>Reviewed</option>
            <option value="approved"  <?= ($sub['status'] ?? '') === 'approved'  ? 'selected':'' ?>>Approved</option>
            <option value="rejected"  <?= ($sub['status'] ?? '') === 'rejected'  ? 'selected':'' ?>>Rejected</option>
          </select>
        </div>

        <div class="form-group">
          <label>Feedback / Comments <span style="color:var(--red)">*</span></label>
          <textarea name="feedback" placeholder="Write detailed feedback for the student..." rows="6" required><?= clean($existing['feedback'] ?? '') ?></textarea>
        </div>

        <!-- Score Preview -->
        <div style="margin-bottom:16px;padding:14px;background:var(--bg-primary);border-radius:10px;">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:0.875rem;">Score Preview</span>
            <span id="scoreLabel" style="font-size:0.875rem;font-weight:600;">—</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" id="scoreFill" style="width:0%"></div></div>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> <?= $existing ? 'Update Grade' : 'Submit Grade' ?>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
const scoreInput = document.querySelector('input[name="score"]');
const fill = document.getElementById('scoreFill');
const label = document.getElementById('scoreLabel');
function updatePreview() {
  const v = Math.min(100, Math.max(0, parseFloat(scoreInput.value) || 0));
  fill.style.width = v + '%';
  label.textContent = v + '%';
  fill.style.background = v >= 70 ? 'var(--green)' : v >= 50 ? 'var(--orange)' : 'var(--red)';
}
scoreInput?.addEventListener('input', updatePreview);
updatePreview();
</script>

<?php require_once '../../includes/layout_end.php'; ?>