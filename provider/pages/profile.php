<main class="provider-main-content provider-profile-main">

<style>
.profile-page {
    --accent:       #4186a0;
    --accent-dark:  #2f728a;
    --accent-light: #e8f4f8;
    --success:      #16a34a;
    --success-bg:   #dcfce7;
    --warn:         #d97706;
    --warn-bg:      #fef3c7;
    --danger:       #b91c1c;
    --danger-bg:    #fee2e2;
    --surface:      #ffffff;
    --bg:           #f4f7f9;
    --border:       #e2e8f0;
    --text:         #1e293b;
    --muted:        #64748b;
    --radius:       14px;
    --shadow:       0 2px 16px rgba(0,0,0,0.07);
}

.provider-main-content.provider-profile-main {
    width: calc(100% - 250px) !important;
    max-width: none !important;
}

.profile-page {
    display:flex;
    flex-direction:column;
    gap:24px;
    padding:8px 0 40px;
    width:100%;
    max-width:none;
}

/* Hero */
.profile-hero { background:linear-gradient(135deg,var(--accent) 0%,var(--accent-dark) 100%); border-radius:var(--radius); padding:32px 32px 24px; color:#fff; position:relative; overflow:hidden; box-shadow:0 8px 32px rgba(65,134,160,0.28); }
.profile-hero::before { content:''; position:absolute; top:-60px; right:-60px; width:220px; height:220px; background:rgba(255,255,255,0.07); border-radius:50%; }
.profile-hero::after  { content:''; position:absolute; bottom:-40px; left:40%; width:160px; height:160px; background:rgba(255,255,255,0.05); border-radius:50%; }
.profile-hero-inner { display:flex; align-items:center; gap:24px; flex-wrap:wrap; position:relative; z-index:1; }
.profile-avatar { width:88px; height:88px; border-radius:50%; background:rgba(255,255,255,0.22); border:3px solid rgba(255,255,255,0.55); display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700; color:#fff; flex-shrink:0; }
.profile-hero-info { flex:1; min-width:0; }
.profile-hero-info h2 { margin:0 0 4px; font-size:1.6rem; font-weight:700; color:#fff; }
.hero-email { font-size:0.92rem; opacity:.85; margin-bottom:8px; }
.hero-badges { display:flex; gap:7px; flex-wrap:wrap; }
.hero-badge { display:inline-flex; align-items:center; gap:4px; background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.28); border-radius:20px; padding:3px 11px; font-size:0.8rem; font-weight:600; color:#fff; }
.hero-badge.verified { background:rgba(22,163,74,0.35); border-color:rgba(22,163,74,0.5); }
.hero-badge.pending { background:rgba(217,119,6,0.25); border-color:rgba(217,119,6,0.5); }
.hero-badge.rejected { background:rgba(185,28,28,0.3); border-color:rgba(185,28,28,0.55); }
.hero-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:20px; position:relative; z-index:1; }
.completion-bar-wrap { background:rgba(255,255,255,0.12); border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:12px; }
.completion-ring { position:relative; width:52px; height:52px; flex-shrink:0; }
.completion-ring svg { transform:rotate(-90deg); }
.completion-ring-label { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:0.72rem; font-weight:700; color:#fff; }
.completion-text strong { display:block; font-size:0.82rem; font-weight:700; color:#fff; }
.completion-text span   { font-size:0.74rem; color:rgba(255,255,255,0.75); }
.hero-actions { display:flex; gap:10px; flex-wrap:wrap; }
.btn-edit-hero { background:#fff; color:var(--accent-dark); border:none; border-radius:8px; padding:10px 22px; font-size:0.88rem; font-weight:700; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.12); transition:transform .15s,box-shadow .15s; white-space:nowrap; text-decoration:none; display:inline-flex; align-items:center; }
.btn-edit-hero:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(0,0,0,0.16); color:var(--accent-dark); }
.btn-complete-hero { background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.36); border-radius:8px; padding:10px 18px; font-size:0.84rem; font-weight:700; text-decoration:none; }
.btn-complete-hero:hover { color:#fff; background:rgba(255,255,255,0.24); }

/* Stats */
.stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
.stat-box { background:var(--bg); border:1px solid var(--border); border-radius:10px; padding:18px 14px; text-align:center; }
.stat-num { display:block; font-size:1.55rem; font-weight:800; color:var(--accent-dark); line-height:1; margin-bottom:4px; }
.stat-label { font-size:0.75rem; color:var(--muted); font-weight:600; }

/* Cards */
.profile-card { background:var(--surface); border-radius:var(--radius); border:1px solid var(--border); box-shadow:var(--shadow); overflow:hidden; }
.profile-card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 26px 14px; border-bottom:1px solid var(--border); }
.profile-card-header h3 { margin:0; font-size:1rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:8px; }
.card-icon { width:28px; height:28px; border-radius:7px; background:var(--accent-light); display:flex; align-items:center; justify-content:center; font-size:0.9rem; }
.btn-card-action { background:var(--accent-light); color:var(--accent-dark); border:none; border-radius:7px; padding:6px 14px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:background .15s; text-decoration:none; display:inline-flex; align-items:center; }
.btn-card-action:hover { background:#cce7f0; color:var(--accent-dark); }
.profile-card-body { padding:22px 26px; }

/* Alerts and forms */
.profile-alert { border-radius:10px; border:1px solid var(--border); padding:12px 14px; font-size:0.85rem; }
.profile-alert.success { background:var(--success-bg); color:var(--success); border-color:#b7e4c7; }
.profile-alert.error { background:var(--danger-bg); color:var(--danger); border-color:#fecaca; }

.inline-profile-form {
    margin-top: 14px;
    background:#fff;
    border:1px dashed var(--border);
    border-radius:10px;
    padding:14px;
}

.profile-form-grid {
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:10px 12px;
}

.profile-form-field {
    display:flex;
    flex-direction:column;
    gap:4px;
}

.profile-form-field.full {
    grid-column:1 / -1;
}

.profile-form-field label {
    font-size:0.72rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.05em;
    color:var(--muted);
}

.profile-form-field input,
.profile-form-field textarea,
.profile-form-field select {
    border:1px solid var(--border);
    background:#fff;
    border-radius:8px;
    padding:8px 10px;
    font-size:0.85rem;
    color:var(--text);
}

.profile-form-field textarea { min-height:76px; resize:vertical; }

.profile-form-actions {
    margin-top:10px;
    display:flex;
    justify-content:flex-end;
    gap:8px;
}

.profile-btn-primary,
.profile-btn-secondary,
.profile-btn-danger {
    border:none;
    border-radius:8px;
    padding:8px 12px;
    font-size:0.8rem;
    font-weight:700;
    cursor:pointer;
}

.profile-btn-primary { background:var(--accent); color:#fff; }
.profile-btn-secondary { background:#e5e7eb; color:#334155; }
.profile-btn-danger { background:#fee2e2; color:#991b1b; }

/* Info grid */
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.info-field { display:flex; flex-direction:column; gap:4px; }
.info-field.full { grid-column:1/-1; }
.info-field label { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted); }
.field-value { font-size:0.93rem; color:var(--text); background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:9px 13px; line-height:1.4; }
.field-value.badge-val { background:transparent; border:none; padding:0; }
.badge-verified { display:inline-flex; align-items:center; gap:5px; background:var(--success-bg); color:var(--success); border-radius:20px; padding:5px 13px; font-size:0.83rem; font-weight:700; }

/* Qualifications */
.qual-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:14px; }
.qual-card { border:1px solid var(--border); border-radius:10px; padding:16px 18px; background:var(--bg); transition:box-shadow .2s,transform .2s; position:relative; }
.qual-card:hover { box-shadow:0 6px 20px rgba(65,134,160,0.13); transform:translateY(-2px); }
.qual-type { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted); display:flex; align-items:center; gap:5px; }
.qual-dot { width:7px; height:7px; border-radius:50%; background:var(--accent); display:inline-block; }
.qual-card h4 { margin:7px 0 4px; font-size:0.92rem; font-weight:700; color:var(--text); line-height:1.3; }
.qual-card p  { margin:0; font-size:0.8rem; color:var(--muted); }
.qual-year { position:absolute; top:13px; right:14px; font-size:0.75rem; font-weight:700; color:var(--accent); background:var(--accent-light); border-radius:6px; padding:2px 7px; }
.qual-delete {
    margin-top:10px;
    display:inline-flex;
    align-items:center;
    gap:4px;
    font-size:0.74rem;
    border:none;
    color:#9f1239;
    background:#ffe4e6;
    border-radius:6px;
    padding:4px 8px;
    cursor:pointer;
}

/* Social */
.social-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.social-item { display:flex; align-items:center; gap:11px; background:var(--bg); border:1px solid var(--border); border-radius:10px; padding:11px 14px; text-decoration:none; transition:border-color .15s,box-shadow .15s; }
.social-item:hover { border-color:var(--accent); box-shadow:0 0 0 3px rgba(65,134,160,0.1); }
.social-icon-wrap { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.social-text label { display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted); margin-bottom:1px; cursor:default; }
.social-text span  { display:block; font-size:0.83rem; color:var(--accent-dark); font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px; }

/* Responsive */
@media(max-width:860px){ .stats-row{grid-template-columns:1fr 1fr;} }
@media(max-width:640px){
    .profile-page{gap:16px;}
    .profile-hero{padding:22px 18px;}
    .profile-hero-inner{flex-direction:column;align-items:flex-start;gap:14px;}
    .hero-actions{width:100%;}
    .info-grid{grid-template-columns:1fr;}
    .info-field.full{grid-column:1;}
    .profile-form-grid{grid-template-columns:1fr;}
    .profile-form-field.full{grid-column:1;}
    .social-grid{grid-template-columns:1fr;}
    .stats-row{grid-template-columns:1fr 1fr;}
    .profile-card-body{padding:16px;}
    .profile-card-header{padding:14px 16px 12px;}
    .qual-grid{grid-template-columns:1fr;}
}
</style>

<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$profileTablesReady = function_exists('ems_provider_profile_tables_ready') ? ems_provider_profile_tables_ready($conn) : false;

$profileAction = trim((string)($_POST['profile_action'] ?? ''));
$profileFeedback = null;

if ($profileTablesReady && $providerUserId > 0 && $profileAction !== '') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $profileFeedback = ['type' => 'error', 'message' => 'Invalid request method.'];
    } elseif (!ems_verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
        $profileFeedback = ['type' => 'error', 'message' => 'Security token is invalid or expired.'];
    } else {
        if ($profileAction === 'update_basic') {
            $result = ems_provider_update_basic_profile($conn, $providerUserId, [
                'full_name' => trim((string)($_POST['full_name'] ?? '')),
                'professional_title' => trim((string)($_POST['professional_title'] ?? '')),
                'mobile_number' => trim((string)($_POST['mobile_number'] ?? '')),
                'skill_category' => trim((string)($_POST['skill_category'] ?? '')),
                'teaching_experience' => trim((string)($_POST['teaching_experience'] ?? '')),
                'short_bio' => trim((string)($_POST['short_bio'] ?? '')),
            ]);
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => (string)($result['message'] ?? 'Unable to update profile.')];
        }

        if ($profileAction === 'add_education') {
            $result = ems_provider_add_education($conn, $providerUserId, [
                'degree_title' => trim((string)($_POST['degree_title'] ?? '')),
                'institution_name' => trim((string)($_POST['institution_name'] ?? '')),
                'field_of_study' => trim((string)($_POST['field_of_study'] ?? '')),
                'start_year' => (int)($_POST['start_year'] ?? 0),
                'end_year' => (int)($_POST['end_year'] ?? 0),
                'is_current' => !empty($_POST['is_current']),
                'description' => trim((string)($_POST['description'] ?? '')),
            ]);
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => (string)($result['message'] ?? 'Unable to add education.')];
        }

        if ($profileAction === 'add_experience') {
            $result = ems_provider_add_experience($conn, $providerUserId, [
                'job_title' => trim((string)($_POST['job_title'] ?? '')),
                'company_name' => trim((string)($_POST['company_name'] ?? '')),
                'employment_type' => trim((string)($_POST['employment_type'] ?? '')),
                'start_date' => trim((string)($_POST['start_date'] ?? '')),
                'end_date' => trim((string)($_POST['end_date'] ?? '')),
                'is_current' => !empty($_POST['is_current']),
                'description' => trim((string)($_POST['description'] ?? '')),
            ]);
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => (string)($result['message'] ?? 'Unable to add experience.')];
        }

        if ($profileAction === 'add_certification') {
            $result = ems_provider_add_certification($conn, $providerUserId, [
                'certificate_name' => trim((string)($_POST['certificate_name'] ?? '')),
                'issued_by' => trim((string)($_POST['issued_by'] ?? '')),
                'issue_date' => trim((string)($_POST['issue_date'] ?? '')),
                'expiry_date' => trim((string)($_POST['expiry_date'] ?? '')),
                'credential_id' => trim((string)($_POST['credential_id'] ?? '')),
                'credential_url' => trim((string)($_POST['credential_url'] ?? '')),
            ]);
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => (string)($result['message'] ?? 'Unable to add certification.')];
        }

        if ($profileAction === 'delete_education') {
            $result = ems_provider_delete_education($conn, $providerUserId, (int)($_POST['education_id'] ?? 0));
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => !empty($result['removed']) ? 'Education removed.' : 'Unable to remove education.'];
        }

        if ($profileAction === 'delete_experience') {
            $result = ems_provider_delete_experience($conn, $providerUserId, (int)($_POST['experience_id'] ?? 0));
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => !empty($result['removed']) ? 'Experience removed.' : 'Unable to remove experience.'];
        }

        if ($profileAction === 'delete_certification') {
            $result = ems_provider_delete_certification($conn, $providerUserId, (int)($_POST['certification_id'] ?? 0));
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => !empty($result['removed']) ? 'Certification removed.' : 'Unable to remove certification.'];
        }

        if ($profileAction === 'submit_approval') {
            $reloadPortalUser = ems_load_portal_user($conn) ?: $portalUser;
            $result = ems_provider_submit_approval_request($conn, $providerUserId, $reloadPortalUser);
            $profileFeedback = ['type' => !empty($result['ok']) ? 'success' : 'error', 'message' => (string)($result['message'] ?? 'Unable to submit approval request.')];
        }

        if (is_array($profileFeedback) && $profileFeedback['type'] === 'success') {
            $portalUser = ems_load_portal_user($conn) ?: $portalUser;
        }
    }
}

$providerProfileName = ems_profile_text($portalUser['full_name'] ?? '', 'Provider');
$providerProfileEmail = ems_profile_text($portalUser['email'] ?? '', 'Not provided');
$providerProfilePhone = ems_profile_text($portalUser['mobile_number'] ?? '', 'Not provided');
$providerProfileTitle = ems_profile_text($portalUser['professional_title'] ?? '', 'Not provided');
$providerProfileCategory = ems_profile_text($portalUser['skill_category'] ?? '', 'Not provided');
$providerProfileExperience = ems_profile_text($portalUser['teaching_experience'] ?? '', 'Not provided');
$providerProfileBio = ems_profile_text($portalUser['short_bio'] ?? '', 'Not provided');
$providerProfileInitials = ems_user_initials($providerProfileName);

$providerJoinedDateLabel = 'Not available';
if (!empty($portalUser['created_at'])) {
    $joinedTimestamp = strtotime((string)$portalUser['created_at']);
    if ($joinedTimestamp !== false) {
        $providerJoinedDateLabel = date('F Y', $joinedTimestamp);
    }
}

$summary = $profileTablesReady && function_exists('ems_provider_fetch_profile_summary')
    ? ems_provider_fetch_profile_summary($conn, $providerUserId, $portalUser)
    : [
        'metrics' => [
            'total_courses' => 0,
            'total_students' => 0,
            'avg_rating' => 0,
            'total_revenue' => 0,
        ],
        'counts' => ['educations' => 0, 'experiences' => 0, 'certifications' => 0],
        'approval' => ['request_status' => 'draft', 'review_note' => '', 'submitted_at' => null],
        'completion_percent' => 35,
        'educations' => [],
        'experiences' => [],
        'certifications' => [],
    ];

$completionPercent = (float)($summary['completion_percent'] ?? 0);
$completionPercent = max(0, min(100, (int)round($completionPercent)));

$approval = $summary['approval'] ?? ['request_status' => 'draft'];
$approvalStatus = strtolower(trim((string)($approval['request_status'] ?? 'draft')));

$providerVerifiedLabel = 'Verification Pending';
$providerVerifiedClass = 'pending';
if ($approvalStatus === 'approved') {
    $providerVerifiedLabel = '✓ Verified Instructor';
    $providerVerifiedClass = 'verified';
} elseif ($approvalStatus === 'rejected') {
    $providerVerifiedLabel = 'Verification Rejected';
    $providerVerifiedClass = 'rejected';
}

$approvalLabel = 'Profile Draft';
$approvalClass = 'pending';
if ($approvalStatus === 'pending') {
    $approvalLabel = 'Awaiting Admin Review';
    $approvalClass = 'pending';
}
if ($approvalStatus === 'approved') {
    $approvalLabel = 'Admin Approved';
    $approvalClass = 'verified';
}
if ($approvalStatus === 'rejected') {
    $approvalLabel = 'Revision Required';
    $approvalClass = 'rejected';
}

$csrf = ems_csrf_token();
$showBasicForm = isset($_GET['edit']) && $_GET['edit'] === 'basic';

$educations = is_array($summary['educations'] ?? null) ? $summary['educations'] : [];
$experiences = is_array($summary['experiences'] ?? null) ? $summary['experiences'] : [];
$certifications = is_array($summary['certifications'] ?? null) ? $summary['certifications'] : [];

$metrics = $summary['metrics'] ?? [];
$totalCourses = (int)($metrics['total_courses'] ?? 0);
$totalStudents = (int)($metrics['total_students'] ?? 0);
$avgRating = (float)($metrics['avg_rating'] ?? 0);
$totalRevenue = (float)($metrics['total_revenue'] ?? 0);
?>

<script>
document.body.classList.add('provider-profile-body');
</script>

<div class="profile-page">

    <?php if (!$profileTablesReady): ?>
        <div class="profile-alert error">Provider profile domain tables are not ready. Apply <code>config/migrations/2026_03_provider_profile_domain.sql</code>.</div>
    <?php endif; ?>

    <?php if (is_array($profileFeedback)): ?>
        <div class="profile-alert <?php echo $profileFeedback['type'] === 'success' ? 'success' : 'error'; ?>"><?php echo ems_e((string)$profileFeedback['message']); ?></div>
    <?php endif; ?>

    <!-- Hero Banner -->
    <div class="profile-hero">
        <div class="profile-hero-inner">
            <div class="profile-avatar"><?php echo ems_e($providerProfileInitials); ?></div>
            <div class="profile-hero-info">
                <h2><?php echo ems_e($providerProfileName); ?></h2>
                <p class="hero-email"><?php echo ems_e($providerProfileEmail); ?></p>
                <div class="hero-badges">
                    <span class="hero-badge <?php echo ems_e($providerVerifiedClass); ?>"><?php echo ems_e($providerVerifiedLabel); ?></span>
                    <span class="hero-badge">🎓 Member since <?php echo ems_e($providerJoinedDateLabel); ?></span>
                    <span class="hero-badge">🏷️ <?php echo ems_e($providerProfileCategory); ?></span>
                    <span class="hero-badge <?php echo ems_e($approvalClass); ?>">🛡️ <?php echo ems_e($approvalLabel); ?></span>
                </div>
            </div>
        </div>
        <div class="hero-footer">
            <div class="completion-bar-wrap">
                <div class="completion-ring">
                    <svg width="52" height="52" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="22" stroke="rgba(255,255,255,0.2)" stroke-width="5" fill="none"/>
                        <circle id="profileRingCircle" cx="26" cy="26" r="22"
                            stroke="#fff" stroke-width="5" fill="none"
                            stroke-linecap="round"
                            stroke-dasharray="138.2"
                            stroke-dashoffset="69.1"/>
                    </svg>
                    <div class="completion-ring-label" id="profileRingLabel"><?php echo (int)$completionPercent; ?>%</div>
                </div>
                <div class="completion-text">
                    <strong>Profile Completion</strong>
                    <span id="completionHint"><?php echo (int)$completionPercent >= 100 ? 'Profile complete! 🎉' : 'Add more info to reach 100%'; ?></span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="<?php echo BASE_URL; ?>provider/?page=completeprofile#basic-info" class="btn-edit-hero">✏️ Edit Profile</a>
                <a href="<?php echo BASE_URL; ?>provider/?page=completeprofile" class="btn-complete-hero">Complete Profile</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="profile-card">
        <div class="profile-card-body" style="padding:18px 26px;">
            <div class="stats-row">
                <div class="stat-box"><span class="stat-num"><?php echo (int)$totalCourses; ?></span><span class="stat-label">Total Courses</span></div>
                <div class="stat-box"><span class="stat-num"><?php echo (int)$totalStudents; ?></span><span class="stat-label">Students</span></div>
                <div class="stat-box"><span class="stat-num"><?php echo number_format($avgRating, 1); ?>★</span><span class="stat-label">Avg Rating</span></div>
                <div class="stat-box"><span class="stat-num"><?php echo ems_e(ems_provider_currency_format($totalRevenue, 'USD')); ?></span><span class="stat-label">Revenue</span></div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">👤</span> Profile Information</h3>
            <a href="<?php echo BASE_URL; ?>provider/?page=completeprofile#basic-info" class="btn-card-action">Edit</a>
        </div>
        <div class="profile-card-body">
            <div class="info-grid">
                <div class="info-field"><label>Full Name</label><div class="field-value"><?php echo ems_e($providerProfileName); ?></div></div>
                <div class="info-field"><label>Email Address</label><div class="field-value"><?php echo ems_e($providerProfileEmail); ?></div></div>
                <div class="info-field"><label>Phone Number</label><div class="field-value"><?php echo ems_e($providerProfilePhone); ?></div></div>
                <div class="info-field"><label>Professional Title</label><div class="field-value"><?php echo ems_e($providerProfileTitle); ?></div></div>
                <div class="info-field"><label>Skill Category</label><div class="field-value"><?php echo ems_e($providerProfileCategory); ?></div></div>
                <div class="info-field"><label>Verification Status</label><div class="field-value badge-val"><span class="badge-verified"><?php echo ems_e($providerVerifiedLabel); ?></span></div></div>
                <div class="info-field"><label>Teaching Experience</label><div class="field-value"><?php echo ems_e($providerProfileExperience); ?></div></div>
                <div class="info-field full"><label>Bio</label><div class="field-value"><?php echo ems_e($providerProfileBio); ?></div></div>
            </div>

            <?php if ($showBasicForm): ?>
                <form method="post" class="inline-profile-form">
                    <input type="hidden" name="profile_action" value="update_basic">
                    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrf); ?>">
                    <div class="profile-form-grid">
                        <div class="profile-form-field">
                            <label>Full Name</label>
                            <input type="text" name="full_name" required value="<?php echo ems_e($providerProfileName); ?>">
                        </div>
                        <div class="profile-form-field">
                            <label>Professional Title</label>
                            <input type="text" name="professional_title" required value="<?php echo ems_e($providerProfileTitle); ?>">
                        </div>
                        <div class="profile-form-field">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile_number" value="<?php echo ems_e($providerProfilePhone); ?>">
                        </div>
                        <div class="profile-form-field">
                            <label>Skill Category</label>
                            <input type="text" name="skill_category" required value="<?php echo ems_e($providerProfileCategory); ?>">
                        </div>
                        <div class="profile-form-field">
                            <label>Teaching Experience</label>
                            <input type="text" name="teaching_experience" value="<?php echo ems_e($providerProfileExperience); ?>">
                        </div>
                        <div class="profile-form-field full">
                            <label>Short Bio</label>
                            <textarea name="short_bio"><?php echo ems_e($providerProfileBio); ?></textarea>
                        </div>
                    </div>
                    <div class="profile-form-actions">
                        <a class="profile-btn-secondary" style="text-decoration:none;display:inline-flex;align-items:center;" href="<?php echo BASE_URL; ?>provider/?page=profile">Cancel</a>
                        <button class="profile-btn-primary" type="submit">Save Changes</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Qualifications -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">🎓</span> Qualifications & Certifications</h3>
            <a href="<?php echo BASE_URL; ?>provider/?page=completeprofile" class="btn-card-action">+ Add</a>
        </div>
        <div class="profile-card-body">
            <div class="qual-grid" id="qualGrid">
                <?php if (empty($educations) && empty($experiences) && empty($certifications)): ?>
                    <div class="qual-card">
                        <div class="qual-type"><span class="qual-dot"></span>Getting Started</div>
                        <h4>No qualifications added yet</h4>
                        <p>Add education, experience, and certifications from Complete Profile.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($educations as $education): ?>
                    <div class="qual-card">
                        <span class="qual-year"><?php echo ems_e((string)($education['end_year'] ?: $education['start_year'] ?: '')); ?></span>
                        <div class="qual-type"><span class="qual-dot"></span>Education</div>
                        <h4><?php echo ems_e((string)($education['degree_title'] ?? 'Degree')); ?></h4>
                        <p><?php echo ems_e((string)($education['institution_name'] ?? 'Institution')); ?></p>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrf); ?>">
                            <input type="hidden" name="profile_action" value="delete_education">
                            <input type="hidden" name="education_id" value="<?php echo (int)($education['id'] ?? 0); ?>">
                            <button type="submit" class="qual-delete">🗑 Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($experiences as $experience): ?>
                    <div class="qual-card">
                        <span class="qual-year"><?php echo ems_e(!empty($experience['start_date']) ? date('Y', strtotime((string)$experience['start_date'])) : ''); ?></span>
                        <div class="qual-type"><span class="qual-dot" style="background:#d97706;"></span>Experience</div>
                        <h4><?php echo ems_e((string)($experience['job_title'] ?? 'Role')); ?></h4>
                        <p><?php echo ems_e((string)($experience['company_name'] ?? 'Company')); ?></p>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrf); ?>">
                            <input type="hidden" name="profile_action" value="delete_experience">
                            <input type="hidden" name="experience_id" value="<?php echo (int)($experience['id'] ?? 0); ?>">
                            <button type="submit" class="qual-delete">🗑 Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($certifications as $certification): ?>
                    <div class="qual-card">
                        <span class="qual-year"><?php echo ems_e(!empty($certification['issue_date']) ? date('Y', strtotime((string)$certification['issue_date'])) : ''); ?></span>
                        <div class="qual-type"><span class="qual-dot" style="background:#16a34a;"></span>Certification</div>
                        <h4><?php echo ems_e((string)($certification['certificate_name'] ?? 'Certificate')); ?></h4>
                        <p><?php echo ems_e((string)($certification['issued_by'] ?? 'Issuer')); ?></p>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrf); ?>">
                            <input type="hidden" name="profile_action" value="delete_certification">
                            <input type="hidden" name="certification_id" value="<?php echo (int)($certification['id'] ?? 0); ?>">
                            <button type="submit" class="qual-delete">🗑 Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Social Links -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">🔗</span> Social & Website Links</h3>
            <a href="<?php echo BASE_URL; ?>provider/?page=completeprofile" class="btn-card-action">Edit Links</a>
        </div>
        <div class="profile-card-body">
            <div class="social-grid">
                <a href="#" class="social-item" onclick="return false;">
                    <div class="social-icon-wrap" style="background:#e0f2fe;">🌐</div>
                    <div class="social-text"><label>Website</label><span>Add in Complete Profile</span></div>
                </a>
                <a href="#" class="social-item" onclick="return false;">
                    <div class="social-icon-wrap" style="background:#dbeafe;">💼</div>
                    <div class="social-text"><label>LinkedIn</label><span>Add in Complete Profile</span></div>
                </a>
                <a href="#" class="social-item" onclick="return false;">
                    <div class="social-icon-wrap" style="background:#f3e8ff;">💻</div>
                    <div class="social-text"><label>GitHub</label><span>Add in Complete Profile</span></div>
                </a>
                <a href="#" class="social-item" onclick="return false;">
                    <div class="social-icon-wrap" style="background:#dbeafe;">🐦</div>
                    <div class="social-text"><label>Twitter / X</label><span>Add in Complete Profile</span></div>
                </a>
            </div>
        </div>
    </div>

    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">✅</span> Admin Approval</h3>
            <a href="<?php echo BASE_URL; ?>provider/?page=completeprofile" class="btn-card-action">Open Complete Profile</a>
        </div>
        <div class="profile-card-body">
            <p style="margin:0 0 8px;color:var(--text);font-size:.92rem;">
                Current status: <strong><?php echo ems_e(ucfirst($approvalStatus)); ?></strong>
                <?php if (!empty($approval['submitted_at'])): ?>
                    • Submitted on <?php echo ems_e(date('d M Y', strtotime((string)$approval['submitted_at']))); ?>
                <?php endif; ?>
            </p>
            <?php if (!empty($approval['review_note'])): ?>
                <p style="margin:0 0 10px;color:var(--muted);font-size:.84rem;">Admin note: <?php echo ems_e((string)$approval['review_note']); ?></p>
            <?php endif; ?>

            <?php if ($approvalStatus !== 'pending'): ?>
                <form method="post" style="margin:0;">
                    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrf); ?>">
                    <input type="hidden" name="profile_action" value="submit_approval">
                    <button type="submit" class="profile-btn-primary">Apply for Admin Approval</button>
                </form>
            <?php else: ?>
                <button class="profile-btn-secondary" type="button" disabled>Approval Request Submitted</button>
            <?php endif; ?>
        </div>
    </div>

</div><!-- .profile-page -->

<script>
(function(){
    var circumference = 2 * Math.PI * 22;
    function setRing(p){
        var offset = circumference * (1 - p / 100);
        var circle = document.getElementById('profileRingCircle');
        if (circle) {
            circle.style.strokeDashoffset = offset;
        }
        var label = document.getElementById('profileRingLabel');
        if (label) {
            label.textContent = p + '%';
        }
        var hint = document.getElementById('completionHint');
        if (hint) {
            hint.textContent = p >= 100 ? 'Profile complete! 🎉' : 'Add more info to reach 100%';
        }
    }
    setRing(<?php echo (int)$completionPercent; ?>);
    window._setProfileCompletion = setRing;
})();
</script>
</main>
