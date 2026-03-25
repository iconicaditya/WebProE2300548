<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');

if (ems_is_logged_in()) {
    ems_redirect(ems_dashboard_path_for_role(ems_current_role()));
}

$providerErrors  = [];
$uploadedPhotoUrl = null;
$uploadedPhotoPath = null;

$providerForm = [
    'full_name'           => trim((string)($_POST['full_name'] ?? '')),
    'professional_title'  => trim((string)($_POST['professional_title'] ?? '')),
    'email'               => trim((string)($_POST['email'] ?? '')),
    'mobile_number'       => trim((string)($_POST['mobile_number'] ?? '')),
    'skill_category'      => trim((string)($_POST['skill_category'] ?? '')),
    'teaching_experience' => trim((string)($_POST['teaching_experience'] ?? '')),
    'short_bio'           => trim((string)($_POST['short_bio'] ?? '')),
    'accept_terms'        => isset($_POST['accept_terms']) ? '1' : '',
];

$allowedSkillCategories = ['Programming', 'Business', 'Design', 'Digital Marketing', 'Data Science'];
$allowedTeachingExperience = ['0-1 years', '2-4 years', '5-8 years', '9+ years'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password        = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    // CSRF guard
    if (!ems_verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $providerErrors[] = 'Security check failed. Please refresh the page and try again.';
    }

    if ($providerForm['full_name'] === '') {
        $providerErrors[] = 'Full name is required.';
    }

    if ($providerForm['professional_title'] === '') {
        $providerErrors[] = 'Professional title is required.';
    }

    if ($providerForm['email'] === '' || !filter_var($providerForm['email'], FILTER_VALIDATE_EMAIL)) {
        $providerErrors[] = 'Please enter a valid email address.';
    }

    if ($providerForm['mobile_number'] === '') {
        $providerErrors[] = 'Mobile number is required.';
    }

    if (!in_array($providerForm['skill_category'], $allowedSkillCategories, true)) {
        $providerErrors[] = 'Please select a valid skill category.';
    }

    if (!in_array($providerForm['teaching_experience'], $allowedTeachingExperience, true)) {
        $providerErrors[] = 'Please select a valid teaching experience.';
    }

    if ($providerForm['short_bio'] === '') {
        $providerErrors[] = 'Short bio is required.';
    }

    if ($password === '') {
        $providerErrors[] = 'Password is required.';
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $providerErrors[] = 'Password must be at least 8 characters and include both letters and numbers.';
    }

    if ($confirmPassword === '') {
        $providerErrors[] = 'Please confirm your password.';
    } elseif ($password !== $confirmPassword) {
        $providerErrors[] = 'Password and confirm password do not match.';
    }

    if ($providerForm['accept_terms'] !== '1') {
        $providerErrors[] = 'You must agree to the provider terms and policy.';
    }

    // Profile photo upload
    if (!empty($providerErrors) === false
        && isset($_FILES['profile_photo'])
        && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $photo = $_FILES['profile_photo'];

        if ($photo['error'] !== UPLOAD_ERR_OK) {
            $providerErrors[] = 'Profile photo upload failed (error code ' . (int)$photo['error'] . '). Please try again.';
        } else {
            // Hard-cap at 2 MB
            if ((int)$photo['size'] > 2 * 1024 * 1024) {
                $providerErrors[] = 'Profile photo must be 2 MB or smaller.';
            }

            // Verify MIME via finfo (not the browser-supplied type)
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = $finfo ? finfo_file($finfo, $photo['tmp_name']) : '';
            if ($finfo) finfo_close($finfo);

            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
            ];

            if (!isset($mimeToExt[$mimeType])) {
                $providerErrors[] = 'Please choose a JPG, PNG, WEBP, or GIF image.';
            }

            if (empty($providerErrors)) {
                $uploadDir = UPLOAD_DIR . 'provider-profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                try {
                    $safeName = 'provider_' . bin2hex(random_bytes(10)) . '.' . $mimeToExt[$mimeType];
                } catch (Exception $e) {
                    $safeName = 'provider_' . uniqid('', true) . '.' . $mimeToExt[$mimeType];
                }

                if (move_uploaded_file($photo['tmp_name'], $uploadDir . $safeName)) {
                    $uploadedPhotoPath = $uploadDir . $safeName;
                    $uploadedPhotoUrl = BASE_URL . 'uploads/provider-profiles/' . $safeName;
                } else {
                    $providerErrors[] = 'Failed to save profile photo. Please try again.';
                }
            }
        }
    }

    if (empty($providerErrors)) {
        $createProviderProfilesSql = "CREATE TABLE IF NOT EXISTS provider_profiles (
            user_id INT UNSIGNED NOT NULL,
            professional_title VARCHAR(150) NOT NULL,
            mobile_number VARCHAR(30) NOT NULL,
            skill_category VARCHAR(100) NOT NULL,
            teaching_experience VARCHAR(50) NOT NULL,
            short_bio TEXT NOT NULL,
            profile_photo_url VARCHAR(255) DEFAULT NULL,
            accepted_terms TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id),
            CONSTRAINT fk_provider_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        if (!$conn->query($createProviderProfilesSql)) {
            $providerErrors[] = 'Unable to prepare provider profile storage.';
        }
    }

    if (empty($providerErrors)) {
        $emailCheckStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');

        if ($emailCheckStmt) {
            $emailCheckStmt->bind_param('s', $providerForm['email']);
            $emailCheckStmt->execute();
            $emailCheckStmt->store_result();

            if ($emailCheckStmt->num_rows > 0) {
                $providerErrors[] = 'An account with this email already exists. Please log in instead.';
            }

            $emailCheckStmt->close();
        } else {
            $providerErrors[] = 'Unable to validate email uniqueness right now. Please try again.';
        }
    }

    if (!empty($providerErrors) && $uploadedPhotoPath && is_file($uploadedPhotoPath)) {
        @unlink($uploadedPhotoPath);
        $uploadedPhotoPath = null;
        $uploadedPhotoUrl = null;
    }

    if (empty($providerErrors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $conn->begin_transaction();

        try {
            $role = 'provider';
            $status = 'active';
            $insertUserStmt = $conn->prepare('INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)');

            if (!$insertUserStmt) {
                throw new RuntimeException('Failed to prepare user insert statement.');
            }

            $insertUserStmt->bind_param('sssss', $providerForm['full_name'], $providerForm['email'], $passwordHash, $role, $status);

            if (!$insertUserStmt->execute()) {
                throw new RuntimeException('Failed to create provider account.');
            }

            $userId = (int)$conn->insert_id;
            $insertUserStmt->close();

            $acceptedTermsInt = 1;
            $insertProfileStmt = $conn->prepare('INSERT INTO provider_profiles (user_id, professional_title, mobile_number, skill_category, teaching_experience, short_bio, profile_photo_url, accepted_terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

            if (!$insertProfileStmt) {
                throw new RuntimeException('Failed to prepare provider profile insert statement.');
            }

            $insertProfileStmt->bind_param(
                'issssssi',
                $userId,
                $providerForm['professional_title'],
                $providerForm['mobile_number'],
                $providerForm['skill_category'],
                $providerForm['teaching_experience'],
                $providerForm['short_bio'],
                $uploadedPhotoUrl,
                $acceptedTermsInt
            );

            if (!$insertProfileStmt->execute()) {
                throw new RuntimeException('Failed to save provider profile.');
            }

            $insertProfileStmt->close();
            $conn->commit();

            ems_set_flash('success', 'Provider account created successfully. Please log in.');
            ems_redirect('auth/login.php');
        } catch (Throwable $e) {
            $conn->rollback();

            if ($uploadedPhotoPath && is_file($uploadedPhotoPath)) {
                @unlink($uploadedPhotoPath);
                $uploadedPhotoPath = null;
                $uploadedPhotoUrl = null;
            }

            if (DEBUG_MODE) {
                $providerErrors[] = 'Registration failed: ' . $e->getMessage();
            } else {
                $providerErrors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register as Provider';
require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../includes/navbar.php');
?>

<main class="provider-register-page">
    <section class="provider-register-shell">
        <aside class="provider-register-media">
            <img src="<?php echo BASE_URL; ?>assets/images/register1.png" alt="Course Provider Registration">
            <div class="provider-media-overlay">
                <h2>Teach What You Know</h2>
                <p>Build courses, reach learners, and grow your educator profile with EduSkill.</p>
            </div>
        </aside>

        <div class="provider-register-panel">
            <div class="provider-register-head">
                <h1>Course <span class="provider-title-accent">Provider Registration</span></h1>
                <p>Complete your profile to start publishing courses on EduSkill Marketplace.</p>
            </div>

            <?php if (!empty($providerErrors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($providerErrors as $err): ?>
                            <li><?php echo ems_e($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($uploadedPhotoUrl): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3" role="alert">
                    <img src="<?php echo ems_e($uploadedPhotoUrl); ?>" alt="" class="provider-flash-thumb">
                    <div><strong>Photo uploaded!</strong><br><span class="text-muted" style="font-size:13px">Your profile photo has been saved.</span></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="provider-register-form" novalidate autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo ems_e(ems_csrf_token()); ?>">
                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control provider-input" placeholder="Enter your full name" autocomplete="off" value="<?php echo ems_e($providerForm['full_name']); ?>">
                    </div>
                    <div>
                        <label class="provider-label">Professional Title</label>
                        <input type="text" name="professional_title" class="form-control provider-input" placeholder="Ex: Data Science Instructor" autocomplete="off" value="<?php echo ems_e($providerForm['professional_title']); ?>">
                    </div>
                </div>

                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Email Address</label>
                        <input type="email" name="email" class="form-control provider-input" placeholder="name@example.com" autocomplete="off" autocapitalize="off" spellcheck="false" value="<?php echo ems_e($providerForm['email']); ?>">
                    </div>
                    <div>
                        <label class="provider-label">Mobile Number</label>
                        <input type="tel" name="mobile_number" class="form-control provider-input" placeholder="Enter mobile number" autocomplete="off" value="<?php echo ems_e($providerForm['mobile_number']); ?>">
                    </div>
                </div>

                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Primary Skill Category</label>
                        <select name="skill_category" class="form-select provider-input">
                            <option value="" disabled <?php echo $providerForm['skill_category'] === '' ? 'selected' : ''; ?>>Select category</option>
                            <option value="Programming" <?php echo $providerForm['skill_category'] === 'Programming' ? 'selected' : ''; ?>>Programming</option>
                            <option value="Business" <?php echo $providerForm['skill_category'] === 'Business' ? 'selected' : ''; ?>>Business</option>
                            <option value="Design" <?php echo $providerForm['skill_category'] === 'Design' ? 'selected' : ''; ?>>Design</option>
                            <option value="Digital Marketing" <?php echo $providerForm['skill_category'] === 'Digital Marketing' ? 'selected' : ''; ?>>Digital Marketing</option>
                            <option value="Data Science" <?php echo $providerForm['skill_category'] === 'Data Science' ? 'selected' : ''; ?>>Data Science</option>
                        </select>
                    </div>
                    <div>
                        <label class="provider-label">Teaching Experience</label>
                        <select name="teaching_experience" class="form-select provider-input">
                            <option value="" disabled <?php echo $providerForm['teaching_experience'] === '' ? 'selected' : ''; ?>>Select experience</option>
                            <option value="0-1 years" <?php echo $providerForm['teaching_experience'] === '0-1 years' ? 'selected' : ''; ?>>0-1 years</option>
                            <option value="2-4 years" <?php echo $providerForm['teaching_experience'] === '2-4 years' ? 'selected' : ''; ?>>2-4 years</option>
                            <option value="5-8 years" <?php echo $providerForm['teaching_experience'] === '5-8 years' ? 'selected' : ''; ?>>5-8 years</option>
                            <option value="9+ years" <?php echo $providerForm['teaching_experience'] === '9+ years' ? 'selected' : ''; ?>>9+ years</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="provider-label">Short Bio</label>
                    <textarea name="short_bio" class="form-control provider-input provider-textarea" placeholder="Tell learners about your expertise and teaching approach" autocomplete="off"><?php echo ems_e($providerForm['short_bio']); ?></textarea>
                </div>

                <!-- ── Profile Photo Upload ── -->
                <div style="margin: clamp(10px, 1.6vw, 14px) clamp(10px, 1.6vw, 14px) clamp(18px, 2.2vw, 24px);">
                    <label class="provider-label">Profile Photo</label>
                    <div class="ppu-card">

                        <!-- Avatar circle triggers file picker -->
                        <label class="ppu-trigger" for="profile_photo">
                            <div class="ppu-circle">
                                <!-- Preview image: hidden by default via inline style -->
                                <img id="ppuImg"
                                     src="<?php echo $uploadedPhotoUrl ? ems_e($uploadedPhotoUrl) : ''; ?>"
                                     alt="Profile photo preview"
                                     style="<?php echo $uploadedPhotoUrl ? '' : 'display:none;'; ?>width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                <!-- Person placeholder SVG -->
                                <span id="ppuIcon" <?php echo $uploadedPhotoUrl ? 'style="display:none"' : ''; ?>>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="52" height="52" aria-hidden="true">
                                        <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z"/>
                                    </svg>
                                </span>
                            </div>
                            <!-- Camera badge -->
                            <span class="ppu-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="white" width="12" height="12" aria-hidden="true">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a2 2 0 0 0 1.414-.586l.828-.828A2 2 0 0 1 6.828 3h2.344a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM8 11a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                                </svg>
                            </span>
                        </label>

                        <!-- Right-side info -->
                        <div class="ppu-body">
                            <p class="ppu-heading">Upload a professional photo</p>
                            <p class="ppu-sub">JPG, PNG, WEBP or GIF &middot; Max 2&nbsp;MB</p>
                            <label class="ppu-btn" for="profile_photo">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="14" height="14" aria-hidden="true">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                                </svg>
                                Choose Photo
                            </label>
                            <p class="ppu-fname" id="ppuFname"><?php echo $uploadedPhotoUrl ? ems_e(basename($uploadedPhotoUrl)) : 'No file chosen'; ?></p>
                        </div>

                        <!-- Native input: hidden with inline style so Bootstrap cannot override -->
                        <input type="file" id="profile_photo" name="profile_photo"
                               accept=".jpg,.jpeg,.png,.webp,.gif,image/*"
                               style="display:none">
                    </div>
                </div>

                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Create Password</label>
                        <input type="password" name="password" class="form-control provider-input" placeholder="Create a strong password" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="provider-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control provider-input" placeholder="Re-enter password" autocomplete="new-password">
                    </div>
                </div>

                <label class="provider-check-row">
                    <input class="form-check-input" type="checkbox" name="accept_terms" value="1" <?php echo $providerForm['accept_terms'] === '1' ? 'checked' : ''; ?>>
                    <span>I agree to the platform terms, provider policy, and course quality guidelines.</span>
                </label>

                <button type="submit" class="btn provider-submit-btn">Create Provider Account</button>

                <p class="provider-login-text">
                    Already have a provider account? <a href="<?php echo BASE_URL; ?>auth/login.php">Log in</a>
                </p>
            </form>
        </div>
    </section>
</main>

<style>
.ppu-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 18px;
    margin: 0;
    box-sizing: border-box;
    background: #fff;
    border: 2px dashed rgba(65, 134, 160, 0.38);
    border-radius: 14px;
    transition: border-color .2s, background .2s, box-shadow .2s;
}
.ppu-card:hover {
    border-color: #4186a0;
    background: rgba(65, 134, 160, 0.03);
    box-shadow: 0 0 0 4px rgba(65, 134, 160, 0.08);
}
/* Avatar trigger */
.ppu-trigger {
    position: relative;
    flex-shrink: 0;
    display: block;
    cursor: pointer;
    margin: 0;
}
.ppu-circle {
    width: 78px;
    height: 78px;
    border-radius: 50%;
    background: linear-gradient(145deg, #c8dde5 0%, #e8f4f9 100%);
    border: 3px solid rgba(65, 134, 160, 0.28);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: border-color .2s, filter .2s;
    color: rgba(65, 134, 160, 0.45);
}
.ppu-trigger:hover .ppu-circle {
    border-color: #4186a0;
    filter: brightness(0.88);
}
/* Camera badge */
.ppu-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 28px;
    height: 28px;
    background: #4186a0;
    border-radius: 50%;
    border: 2.5px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.18);
    transition: transform .18s, background .18s;
}
.ppu-trigger:hover .ppu-badge {
    transform: scale(1.12);
    background: #2f728a;
}
/* Info column */
.ppu-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.ppu-heading {
    margin: 0 0 2px;
    font-size: 14px;
    font-weight: 700;
    color: #1f2937;
}
.ppu-sub {
    margin: 0;
    font-size: 12px;
    color: #9ca3af;
}
/* Choose button */
.ppu-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-top: 8px;
    padding: 6px 14px;
    border-radius: 8px;
    border: 1.5px solid #4186a0;
    color: #4186a0;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    width: fit-content;
    transition: background .15s, color .15s, transform .12s;
    user-select: none;
    text-decoration: none;
}
.ppu-btn:hover {
    background: #4186a0;
    color: #fff;
    transform: translateY(-1px);
}
/* Filename */
.ppu-fname {
    margin: 6px 0 0;
    font-size: 12px;
    color: #adb5bd;
    font-style: italic;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}
/* Upload success flash thumbnail */
.provider-flash-thumb {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid rgba(65, 134, 160, 0.30);
}
@media (max-width: 540px) {
    .ppu-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        padding: 16px;
        margin: 0 4px;
    }
    .ppu-fname { max-width: 100%; }
}
</style>

<script>
(function () {
    var input = document.getElementById('profile_photo');
    var img   = document.getElementById('ppuImg');
    var icon  = document.getElementById('ppuIcon');
    var fname = document.getElementById('ppuFname');

    if (!input) return;

    input.addEventListener('change', function () {
        var file = this.files[0];

        if (!file) {
            img.style.display  = 'none';
            img.src            = '';
            icon.style.display = '';
            fname.textContent  = 'No file chosen';
            return;
        }

        if (!file.type.startsWith('image/')) {
            fname.textContent = '\u26a0 Please choose a valid image file';
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            img.src            = e.target.result;
            img.style.display  = 'block';
            icon.style.display = 'none';
            fname.textContent  = file.name;
        };
        reader.readAsDataURL(file);
    });
})();
</script>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
