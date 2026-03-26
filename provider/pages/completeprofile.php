<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$profileTablesReady = function_exists('ems_provider_profile_tables_ready') ? ems_provider_profile_tables_ready($conn) : false;

$completeAction = trim((string)($_POST['completeprofile_action'] ?? ''));
$completeFeedback = null;

if ($profileTablesReady && $providerUserId > 0 && $_SERVER['REQUEST_METHOD'] === 'POST' && $completeAction !== '') {
    if (!ems_verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
        $completeFeedback = ['type' => 'error', 'message' => 'Security token is invalid or expired.'];
    } else {
        if ($completeAction === 'update_basic') {
            $result = ems_provider_update_basic_profile($conn, $providerUserId, [
                'full_name' => trim((string)($_POST['full_name'] ?? '')),
                'professional_title' => trim((string)($_POST['professional_title'] ?? '')),
                'mobile_number' => trim((string)($_POST['mobile_number'] ?? '')),
                'skill_category' => trim((string)($_POST['skill_category'] ?? '')),
                'teaching_experience' => trim((string)($_POST['teaching_experience'] ?? '')),
                'short_bio' => trim((string)($_POST['short_bio'] ?? '')),
            ]);
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to update profile.'),
            ];
        }

        if ($completeAction === 'add_education') {
            $result = ems_provider_add_education($conn, $providerUserId, [
                'degree_title' => trim((string)($_POST['degree_title'] ?? '')),
                'institution_name' => trim((string)($_POST['institution_name'] ?? '')),
                'field_of_study' => trim((string)($_POST['field_of_study'] ?? '')),
                'start_year' => (int)($_POST['start_year'] ?? 0),
                'end_year' => (int)($_POST['end_year'] ?? 0),
                'is_current' => !empty($_POST['is_current']),
                'description' => trim((string)($_POST['description'] ?? '')),
            ]);
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to add education.'),
            ];
        }

        if ($completeAction === 'add_experience') {
            $result = ems_provider_add_experience($conn, $providerUserId, [
                'job_title' => trim((string)($_POST['job_title'] ?? '')),
                'company_name' => trim((string)($_POST['company_name'] ?? '')),
                'employment_type' => trim((string)($_POST['employment_type'] ?? '')),
                'start_date' => trim((string)($_POST['start_date'] ?? '')),
                'end_date' => trim((string)($_POST['end_date'] ?? '')),
                'is_current' => !empty($_POST['is_current']),
                'description' => trim((string)($_POST['description'] ?? '')),
            ]);
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to add experience.'),
            ];
        }

        if ($completeAction === 'add_certification') {
            $result = ems_provider_add_certification($conn, $providerUserId, [
                'certificate_name' => trim((string)($_POST['certificate_name'] ?? '')),
                'issued_by' => trim((string)($_POST['issued_by'] ?? '')),
                'issue_date' => trim((string)($_POST['issue_date'] ?? '')),
                'expiry_date' => trim((string)($_POST['expiry_date'] ?? '')),
                'credential_id' => trim((string)($_POST['credential_id'] ?? '')),
                'credential_url' => trim((string)($_POST['credential_url'] ?? '')),
            ]);
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to add certification.'),
            ];
        }

        if ($completeAction === 'delete_education') {
            $result = ems_provider_delete_education($conn, $providerUserId, (int)($_POST['education_id'] ?? 0));
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => !empty($result['removed']) ? 'Education removed.' : 'Unable to remove education.',
            ];
        }

        if ($completeAction === 'delete_experience') {
            $result = ems_provider_delete_experience($conn, $providerUserId, (int)($_POST['experience_id'] ?? 0));
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => !empty($result['removed']) ? 'Experience removed.' : 'Unable to remove experience.',
            ];
        }

        if ($completeAction === 'delete_certification') {
            $result = ems_provider_delete_certification($conn, $providerUserId, (int)($_POST['certification_id'] ?? 0));
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => !empty($result['removed']) ? 'Certification removed.' : 'Unable to remove certification.',
            ];
        }

        if ($completeAction === 'submit_approval') {
            $reloadPortalUser = ems_load_portal_user($conn) ?: $portalUser;
            $result = ems_provider_submit_approval_request($conn, $providerUserId, $reloadPortalUser);
            $completeFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to submit approval request.'),
            ];
        }

        if (is_array($completeFeedback) && $completeFeedback['type'] === 'success') {
            $portalUser = ems_load_portal_user($conn) ?: $portalUser;
        }
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
        'completion_percent' => 0,
    ];

$educations = $profileTablesReady ? ems_provider_fetch_educations($conn, $providerUserId, 100) : [];
$experiences = $profileTablesReady ? ems_provider_fetch_experiences($conn, $providerUserId, 100) : [];
$certifications = $profileTablesReady ? ems_provider_fetch_certifications($conn, $providerUserId, 100) : [];

$approval = $summary['approval'] ?? ['request_status' => 'draft'];
$approvalStatus = strtolower(trim((string)($approval['request_status'] ?? 'draft')));
$approvalLabel = 'Draft';
$approvalClass = 'cp-badge-draft';
if ($approvalStatus === 'pending') {
    $approvalLabel = 'Pending Admin Review';
    $approvalClass = 'cp-badge-pending';
}
if ($approvalStatus === 'approved') {
    $approvalLabel = 'Approved';
    $approvalClass = 'cp-badge-approved';
}
if ($approvalStatus === 'rejected') {
    $approvalLabel = 'Rejected - Update Needed';
    $approvalClass = 'cp-badge-rejected';
}

$completionPercent = max(0, min(100, (int)round((float)($summary['completion_percent'] ?? 0))));
$counts = is_array($summary['counts'] ?? null) ? $summary['counts'] : [];
$csrfToken = ems_csrf_token();

$providerFullName = trim((string)($portalUser['full_name'] ?? ''));
$providerEmail = trim((string)($portalUser['email'] ?? ''));
$providerMobile = trim((string)($portalUser['mobile_number'] ?? ''));
$providerTitle = trim((string)($portalUser['professional_title'] ?? ''));
$providerCategory = trim((string)($portalUser['skill_category'] ?? ''));
$providerExperience = trim((string)($portalUser['teaching_experience'] ?? ''));
$providerBio = trim((string)($portalUser['short_bio'] ?? ''));
?>

<main class="provider-main-content provider-profile-main">
<style>
.cp-page {
    --cp-accent:#4186a0;
    --cp-accent-dark:#2f728a;
    --cp-border:#dbe5ec;
    --cp-bg:#f5f8fa;
    --cp-text:#1f2d3d;
    --cp-muted:#607487;
    display:flex;
    flex-direction:column;
    gap:18px;
}

.cp-head {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    background:linear-gradient(135deg,var(--cp-accent),var(--cp-accent-dark));
    color:#fff;
    border-radius:14px;
    padding:20px 22px;
    box-shadow:0 10px 28px rgba(47,114,138,.22);
}

.cp-head h1 {
    margin:0;
    font-size:1.35rem;
    font-weight:800;
}

.cp-head p {
    margin:6px 0 0;
    font-size:.88rem;
    opacity:.9;
}

.cp-head-right {
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}

.cp-completion {
    background:rgba(255,255,255,.16);
    border:1px solid rgba(255,255,255,.3);
    border-radius:999px;
    padding:6px 12px;
    font-size:.8rem;
    font-weight:700;
}

.cp-link-btn {
    display:inline-flex;
    align-items:center;
    text-decoration:none;
    border-radius:8px;
    border:1px solid rgba(255,255,255,.45);
    color:#fff;
    padding:8px 12px;
    font-size:.82rem;
    font-weight:700;
}

.cp-grid {
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:14px;
}

.cp-card {
    background:#fff;
    border:1px solid var(--cp-border);
    border-radius:12px;
    box-shadow:0 2px 14px rgba(15,23,42,.05);
    overflow:hidden;
}

.cp-card.full {
    grid-column:1 / -1;
}

.cp-card-head {
    padding:14px 16px;
    border-bottom:1px solid var(--cp-border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
}

.cp-card-head h2 {
    margin:0;
    color:var(--cp-text);
    font-size:.97rem;
    font-weight:800;
}

.cp-badge {
    border-radius:999px;
    font-size:.72rem;
    font-weight:800;
    padding:4px 10px;
}

.cp-badge-draft { background:#e2e8f0; color:#334155; }
.cp-badge-pending { background:#fef3c7; color:#92400e; }
.cp-badge-approved { background:#dcfce7; color:#166534; }
.cp-badge-rejected { background:#fee2e2; color:#991b1b; }

.cp-card-body {
    padding:14px 16px 16px;
}

.cp-alert {
    border-radius:10px;
    padding:10px 12px;
    border:1px solid var(--cp-border);
    font-size:.84rem;
}

.cp-alert.success {
    background:#ecfdf5;
    color:#065f46;
    border-color:#a7f3d0;
}

.cp-alert.error {
    background:#fef2f2;
    color:#991b1b;
    border-color:#fecaca;
}

.cp-form-grid {
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:10px 12px;
}

.cp-field {
    display:flex;
    flex-direction:column;
    gap:5px;
}

.cp-field.full {
    grid-column:1 / -1;
}

.cp-field label {
    font-size:.72rem;
    text-transform:uppercase;
    letter-spacing:.05em;
    color:var(--cp-muted);
    font-weight:700;
}

.cp-field input,
.cp-field textarea,
.cp-field select {
    border:1px solid var(--cp-border);
    border-radius:8px;
    padding:8px 10px;
    font-size:.86rem;
    color:var(--cp-text);
    background:#fff;
}

.cp-field textarea {
    min-height:80px;
    resize:vertical;
}

.cp-inline-check {
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-size:.82rem;
    color:var(--cp-text);
}

.cp-actions {
    margin-top:10px;
    display:flex;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
}

.cp-btn {
    border:none;
    border-radius:8px;
    padding:8px 12px;
    font-size:.82rem;
    font-weight:800;
    cursor:pointer;
}

.cp-btn-primary { background:var(--cp-accent); color:#fff; }
.cp-btn-primary:hover { background:var(--cp-accent-dark); }
.cp-btn-danger { background:#ffe4e6; color:#9f1239; }
.cp-btn-danger:hover { background:#fecdd3; }

.cp-list {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(230px,1fr));
    gap:10px;
    margin-top:12px;
}

.cp-item {
    background:var(--cp-bg);
    border:1px solid var(--cp-border);
    border-radius:10px;
    padding:10px 12px;
}

.cp-item h3 {
    margin:0;
    font-size:.88rem;
    color:var(--cp-text);
}

.cp-item p {
    margin:5px 0 0;
    font-size:.8rem;
    color:var(--cp-muted);
}

.cp-item-foot {
    margin-top:8px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
}

.cp-item-meta {
    font-size:.74rem;
    color:#475569;
    font-weight:700;
}

.cp-empty {
    margin-top:12px;
    font-size:.84rem;
    color:var(--cp-muted);
}

.cp-pill-row {
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.cp-pill {
    border:1px solid var(--cp-border);
    border-radius:999px;
    padding:6px 10px;
    font-size:.78rem;
    background:#f8fafc;
    color:#334155;
    font-weight:700;
}

@media (max-width: 900px) {
    .cp-grid { grid-template-columns:1fr; }
}
</style>

<div class="cp-page">
    <header class="cp-head">
        <div>
            <h1>Complete Provider Profile</h1>
            <p>Add education, experience, and certificates before applying for admin approval.</p>
        </div>
        <div class="cp-head-right">
            <span class="cp-completion">Completion: <?php echo (int)$completionPercent; ?>%</span>
            <a class="cp-link-btn" href="<?php echo BASE_URL; ?>provider/?page=profile">Back to Profile</a>
        </div>
    </header>

    <?php if (!$profileTablesReady): ?>
        <div class="cp-alert error">Provider profile domain tables are not ready. Apply <code>config/migrations/2026_03_provider_profile_domain.sql</code>.</div>
    <?php endif; ?>

    <?php if (is_array($completeFeedback)): ?>
        <div class="cp-alert <?php echo $completeFeedback['type'] === 'success' ? 'success' : 'error'; ?>">
            <?php echo ems_e((string)$completeFeedback['message']); ?>
        </div>
    <?php endif; ?>

    <div class="cp-grid">
        <section class="cp-card full" id="basic-info">
            <div class="cp-card-head">
                <h2>Basic Information</h2>
                <span class="cp-badge cp-badge-draft">Required</span>
            </div>
            <div class="cp-card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                    <input type="hidden" name="completeprofile_action" value="update_basic">

                    <div class="cp-form-grid">
                        <div class="cp-field">
                            <label>Full Name</label>
                            <input type="text" name="full_name" required value="<?php echo ems_e($providerFullName); ?>">
                        </div>
                        <div class="cp-field">
                            <label>Email</label>
                            <input type="email" value="<?php echo ems_e($providerEmail); ?>" disabled>
                        </div>
                        <div class="cp-field">
                            <label>Professional Title</label>
                            <input type="text" name="professional_title" required value="<?php echo ems_e($providerTitle); ?>">
                        </div>
                        <div class="cp-field">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile_number" value="<?php echo ems_e($providerMobile); ?>">
                        </div>
                        <div class="cp-field">
                            <label>Skill Category</label>
                            <input type="text" name="skill_category" required value="<?php echo ems_e($providerCategory); ?>">
                        </div>
                        <div class="cp-field">
                            <label>Teaching Experience</label>
                            <input type="text" name="teaching_experience" value="<?php echo ems_e($providerExperience); ?>" placeholder="e.g. 5 years">
                        </div>
                        <div class="cp-field full">
                            <label>Short Bio</label>
                            <textarea name="short_bio" placeholder="Add your teaching bio"><?php echo ems_e($providerBio); ?></textarea>
                        </div>
                    </div>

                    <div class="cp-actions">
                        <button class="cp-btn cp-btn-primary" type="submit">Save Basic Information</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="cp-card" id="education">
            <div class="cp-card-head">
                <h2>Education</h2>
                <span class="cp-pill"><?php echo (int)($counts['educations'] ?? 0); ?> records</span>
            </div>
            <div class="cp-card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                    <input type="hidden" name="completeprofile_action" value="add_education">
                    <div class="cp-form-grid">
                        <div class="cp-field">
                            <label>Degree</label>
                            <input type="text" name="degree_title" required>
                        </div>
                        <div class="cp-field">
                            <label>Institution</label>
                            <input type="text" name="institution_name" required>
                        </div>
                        <div class="cp-field">
                            <label>Field of Study</label>
                            <input type="text" name="field_of_study">
                        </div>
                        <div class="cp-field">
                            <label>Start Year</label>
                            <input type="number" name="start_year" min="1950" max="2100">
                        </div>
                        <div class="cp-field">
                            <label>End Year</label>
                            <input type="number" name="end_year" min="1950" max="2100">
                        </div>
                        <div class="cp-field full">
                            <label class="cp-inline-check">
                                <input type="checkbox" name="is_current" value="1">
                                I am currently studying here
                            </label>
                        </div>
                        <div class="cp-field full">
                            <label>Description</label>
                            <textarea name="description"></textarea>
                        </div>
                    </div>
                    <div class="cp-actions">
                        <button class="cp-btn cp-btn-primary" type="submit">Add Education</button>
                    </div>
                </form>

                <?php if (empty($educations)): ?>
                    <p class="cp-empty">No education records added yet.</p>
                <?php else: ?>
                    <div class="cp-list">
                        <?php foreach ($educations as $education): ?>
                            <?php
                                $educationYears = [];
                                if (!empty($education['start_year'])) {
                                    $educationYears[] = (string)$education['start_year'];
                                }
                                if (!empty($education['is_current'])) {
                                    $educationYears[] = 'Present';
                                } elseif (!empty($education['end_year'])) {
                                    $educationYears[] = (string)$education['end_year'];
                                }
                                $educationYearText = implode(' - ', $educationYears);
                            ?>
                            <article class="cp-item">
                                <h3><?php echo ems_e((string)($education['degree_title'] ?? 'Degree')); ?></h3>
                                <p><?php echo ems_e((string)($education['institution_name'] ?? '')); ?></p>
                                <div class="cp-item-foot">
                                    <span class="cp-item-meta"><?php echo ems_e($educationYearText !== '' ? $educationYearText : 'Year not provided'); ?></span>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                        <input type="hidden" name="completeprofile_action" value="delete_education">
                                        <input type="hidden" name="education_id" value="<?php echo (int)($education['id'] ?? 0); ?>">
                                        <button type="submit" class="cp-btn cp-btn-danger">Remove</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="cp-card" id="experience">
            <div class="cp-card-head">
                <h2>Experience</h2>
                <span class="cp-pill"><?php echo (int)($counts['experiences'] ?? 0); ?> records</span>
            </div>
            <div class="cp-card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                    <input type="hidden" name="completeprofile_action" value="add_experience">
                    <div class="cp-form-grid">
                        <div class="cp-field">
                            <label>Job Title</label>
                            <input type="text" name="job_title" required>
                        </div>
                        <div class="cp-field">
                            <label>Company Name</label>
                            <input type="text" name="company_name" required>
                        </div>
                        <div class="cp-field">
                            <label>Employment Type</label>
                            <select name="employment_type">
                                <option value="">Select type</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="cp-field">
                            <label>Start Date</label>
                            <input type="date" name="start_date">
                        </div>
                        <div class="cp-field">
                            <label>End Date</label>
                            <input type="date" name="end_date">
                        </div>
                        <div class="cp-field full">
                            <label class="cp-inline-check">
                                <input type="checkbox" name="is_current" value="1">
                                I currently work here
                            </label>
                        </div>
                        <div class="cp-field full">
                            <label>Description</label>
                            <textarea name="description"></textarea>
                        </div>
                    </div>
                    <div class="cp-actions">
                        <button class="cp-btn cp-btn-primary" type="submit">Add Experience</button>
                    </div>
                </form>

                <?php if (empty($experiences)): ?>
                    <p class="cp-empty">No experience records added yet.</p>
                <?php else: ?>
                    <div class="cp-list">
                        <?php foreach ($experiences as $experience): ?>
                            <?php
                                $experienceStart = !empty($experience['start_date']) && strtotime((string)$experience['start_date'])
                                    ? date('M Y', strtotime((string)$experience['start_date']))
                                    : '';
                                $experienceEnd = !empty($experience['is_current'])
                                    ? 'Present'
                                    : ((!empty($experience['end_date']) && strtotime((string)$experience['end_date'])) ? date('M Y', strtotime((string)$experience['end_date'])) : '');
                                $experiencePeriod = trim($experienceStart . ($experienceEnd !== '' ? ' - ' . $experienceEnd : ''));
                            ?>
                            <article class="cp-item">
                                <h3><?php echo ems_e((string)($experience['job_title'] ?? 'Role')); ?></h3>
                                <p><?php echo ems_e((string)($experience['company_name'] ?? '')); ?></p>
                                <div class="cp-item-foot">
                                    <span class="cp-item-meta"><?php echo ems_e($experiencePeriod !== '' ? $experiencePeriod : 'Period not provided'); ?></span>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                        <input type="hidden" name="completeprofile_action" value="delete_experience">
                                        <input type="hidden" name="experience_id" value="<?php echo (int)($experience['id'] ?? 0); ?>">
                                        <button type="submit" class="cp-btn cp-btn-danger">Remove</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="cp-card full" id="certificates">
            <div class="cp-card-head">
                <h2>Certificates</h2>
                <span class="cp-pill"><?php echo (int)($counts['certifications'] ?? 0); ?> records</span>
            </div>
            <div class="cp-card-body">
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                    <input type="hidden" name="completeprofile_action" value="add_certification">
                    <div class="cp-form-grid">
                        <div class="cp-field">
                            <label>Certificate Name</label>
                            <input type="text" name="certificate_name" required>
                        </div>
                        <div class="cp-field">
                            <label>Issued By</label>
                            <input type="text" name="issued_by" required>
                        </div>
                        <div class="cp-field">
                            <label>Issue Date</label>
                            <input type="date" name="issue_date">
                        </div>
                        <div class="cp-field">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date">
                        </div>
                        <div class="cp-field">
                            <label>Credential ID</label>
                            <input type="text" name="credential_id">
                        </div>
                        <div class="cp-field">
                            <label>Credential URL</label>
                            <input type="url" name="credential_url" placeholder="https://">
                        </div>
                    </div>
                    <div class="cp-actions">
                        <button class="cp-btn cp-btn-primary" type="submit">Add Certificate</button>
                    </div>
                </form>

                <?php if (empty($certifications)): ?>
                    <p class="cp-empty">No certificates added yet.</p>
                <?php else: ?>
                    <div class="cp-list">
                        <?php foreach ($certifications as $certification): ?>
                            <?php
                                $issueDateLabel = !empty($certification['issue_date']) && strtotime((string)$certification['issue_date'])
                                    ? date('d M Y', strtotime((string)$certification['issue_date']))
                                    : 'Issue date not provided';
                            ?>
                            <article class="cp-item">
                                <h3><?php echo ems_e((string)($certification['certificate_name'] ?? 'Certificate')); ?></h3>
                                <p><?php echo ems_e((string)($certification['issued_by'] ?? '')); ?></p>
                                <div class="cp-item-foot">
                                    <span class="cp-item-meta"><?php echo ems_e($issueDateLabel); ?></span>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                        <input type="hidden" name="completeprofile_action" value="delete_certification">
                                        <input type="hidden" name="certification_id" value="<?php echo (int)($certification['id'] ?? 0); ?>">
                                        <button type="submit" class="cp-btn cp-btn-danger">Remove</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="cp-card full" id="approval">
            <div class="cp-card-head">
                <h2>Apply for Admin Approval</h2>
                <span class="cp-badge <?php echo ems_e($approvalClass); ?>"><?php echo ems_e($approvalLabel); ?></span>
            </div>
            <div class="cp-card-body">
                <div class="cp-pill-row">
                    <span class="cp-pill">Education: <?php echo (int)($counts['educations'] ?? 0); ?></span>
                    <span class="cp-pill">Experience: <?php echo (int)($counts['experiences'] ?? 0); ?></span>
                    <span class="cp-pill">Certificates: <?php echo (int)($counts['certifications'] ?? 0); ?></span>
                </div>

                <p style="margin:12px 0 6px;font-size:.88rem;color:#334155;">
                    Current status: <strong><?php echo ems_e(ucfirst($approvalStatus)); ?></strong>
                    <?php if (!empty($approval['submitted_at'])): ?>
                        • Submitted on <?php echo ems_e(date('d M Y', strtotime((string)$approval['submitted_at']))); ?>
                    <?php endif; ?>
                </p>

                <?php if (!empty($approval['review_note'])): ?>
                    <p style="margin:0 0 10px;font-size:.83rem;color:#64748b;">Admin Note: <?php echo ems_e((string)$approval['review_note']); ?></p>
                <?php endif; ?>

                <?php if ($approvalStatus !== 'pending'): ?>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                        <input type="hidden" name="completeprofile_action" value="submit_approval">
                        <div class="cp-actions" style="justify-content:flex-start;">
                            <button class="cp-btn cp-btn-primary" type="submit">Apply for Admin Approval</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="cp-alert success" style="margin-top:10px;">Your profile has already been submitted and is currently under review.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
</main>
