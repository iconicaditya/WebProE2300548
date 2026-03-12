<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../includes/auth.php');

$pageTitle = 'Register as Learner';
$learnerErrors  = [];
$uploadedPhotoUrl = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF guard
    if (!ems_verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $learnerErrors[] = 'Security check failed. Please refresh the page and try again.';
    }

    // Profile photo upload
    if (!empty($learnerErrors) === false
        && isset($_FILES['profile_photo'])
        && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $photo = $_FILES['profile_photo'];

        if ($photo['error'] !== UPLOAD_ERR_OK) {
            $learnerErrors[] = 'Profile photo upload failed (error code ' . (int)$photo['error'] . '). Please try again.';
        } else {
            // Hard-cap at 2 MB
            if ((int)$photo['size'] > 2 * 1024 * 1024) {
                $learnerErrors[] = 'Profile photo must be 2 MB or smaller.';
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
                $learnerErrors[] = 'Please choose a JPG, PNG, WEBP, or GIF image.';
            }

            if (empty($learnerErrors)) {
                $uploadDir = UPLOAD_DIR . 'learner-profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                try {
                    $safeName = 'learner_' . bin2hex(random_bytes(10)) . '.' . $mimeToExt[$mimeType];
                } catch (Exception $e) {
                    $safeName = 'learner_' . uniqid('', true) . '.' . $mimeToExt[$mimeType];
                }

                if (move_uploaded_file($photo['tmp_name'], $uploadDir . $safeName)) {
                    $uploadedPhotoUrl = BASE_URL . 'uploads/learner-profiles/' . $safeName;
                } else {
                    $learnerErrors[] = 'Failed to save profile photo. Please try again.';
                }
            }
        }
    }
}

require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../includes/navbar.php');
?>

<main class="learner-register-page">
    <section class="learner-register-shell">
        <aside class="learner-register-media">
            <img src="<?php echo BASE_URL; ?>assets/images/register2.png" alt="Learner Registration">
            <div class="learner-media-overlay">
                <h2>Start Learning Today</h2>
                <p>Join EduSkill to discover top instructors and build skills for your future.</p>
            </div>
        </aside>

        <div class="learner-register-panel">
            <div class="learner-register-head">
                <h1><span class="learner-title-accent">Learner Registration</span></h1>
                <p>Create your learner profile and begin exploring high-quality courses.</p>
            </div>

            <?php if (!empty($learnerErrors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($learnerErrors as $err): ?>
                            <li><?php echo ems_e($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($uploadedPhotoUrl): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3" role="alert">
                    <img src="<?php echo ems_e($uploadedPhotoUrl); ?>" alt="" class="learner-flash-thumb">
                    <div><strong>Photo uploaded!</strong><br><span class="text-muted" style="font-size:13px">Your profile photo has been saved.</span></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="learner-register-form" novalidate autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo ems_e(ems_csrf_token()); ?>">
                <div class="learner-grid two-col">
                    <div>
                        <label class="learner-label">Full Name</label>
                        <input type="text" class="form-control learner-input" placeholder="Enter your full name" autocomplete="off">
                    </div>
                    <div>
                        <label class="learner-label">Current Role</label>
                        <input type="text" class="form-control learner-input" placeholder="Ex: Student, Job Seeker" autocomplete="off">
                    </div>
                </div>

                <div class="learner-grid two-col">
                    <div>
                        <label class="learner-label">Email Address</label>
                        <input type="email" class="form-control learner-input" placeholder="name@example.com" autocomplete="off" autocapitalize="off" spellcheck="false">
                    </div>
                    <div>
                        <label class="learner-label">Mobile Number</label>
                        <input type="tel" class="form-control learner-input" placeholder="Enter mobile number" autocomplete="off">
                    </div>
                </div>

                <div class="learner-grid two-col">
                    <div>
                        <label class="learner-label">Learning Interest</label>
                        <select class="form-select learner-input">
                            <option selected disabled>Select interest</option>
                            <option>Programming</option>
                            <option>Business</option>
                            <option>Design</option>
                            <option>Digital Marketing</option>
                            <option>Data Science</option>
                        </select>
                    </div>
                    <div>
                        <label class="learner-label">Experience Level</label>
                        <select class="form-select learner-input">
                            <option selected disabled>Select level</option>
                            <option>Beginner</option>
                            <option>Intermediate</option>
                            <option>Advanced</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="learner-label">Learning Goal</label>
                    <textarea class="form-control learner-input learner-textarea" placeholder="Tell us what you want to achieve from learning" autocomplete="off"></textarea>
                </div>

                <!-- ── Profile Photo Upload ── -->
                <div class="learner-photo-wrap">
                    <label class="learner-label">Profile Photo</label>
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

                <div class="learner-grid two-col">
                    <div>
                        <label class="learner-label">Create Password</label>
                        <input type="password" class="form-control learner-input" placeholder="Create a strong password" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="learner-label">Confirm Password</label>
                        <input type="password" class="form-control learner-input" placeholder="Re-enter password" autocomplete="new-password">
                    </div>
                </div>

                <label class="learner-check-row">
                    <input class="form-check-input" type="checkbox">
                    <span>I agree to the platform terms, privacy policy, and learner community guidelines.</span>
                </label>

                <button type="button" class="btn learner-submit-btn">Create Learner Account</button>

                <p class="learner-login-text">
                    Already have an account? <a href="<?php echo BASE_URL; ?>auth/login.php">Log in</a>
                </p>
            </form>
        </div>
    </section>
</main>

<style>
    .learner-register-page {
        --form-theme: #4186a0;
        --form-theme-dark: #2f728a;
    }

    .learner-register-page {
        background: linear-gradient(rgba(7, 20, 32, 0.55), rgba(7, 20, 32, 0.55)), url('<?php echo BASE_URL; ?>assets/images/registerlearnerbg.png') center/cover no-repeat fixed;
        padding: clamp(20px, 3vw, 36px) clamp(10px, 2vw, 16px) !important;
        min-height: calc(100vh - 64px);
        min-height: calc(100dvh - 64px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .learner-register-shell {
        width: min(100%, 1240px);
        min-height: auto;
        margin: 0 auto clamp(28px, 5vh, 56px);
        background: rgba(255, 255, 255, 0.60);
        border-radius: 0;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        box-shadow: 0 18px 38px rgba(0, 0, 0, 0.2);
    }

    .learner-register-media {
        display: block;
        position: relative;
        min-height: 560px;
        background: #0f172a;
    }

    .learner-register-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .learner-register-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .learner-media-overlay {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 22px;
        color: #fff;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(2, 6, 23, 0.75) 100%);
    }

    .learner-media-overlay h2 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 700;
    }

    .learner-media-overlay p {
        margin: 0;
        font-size: 14px;
        opacity: 0.95;
    }

    .learner-register-panel {
        padding: clamp(16px, 2vw, 28px);
        border-left: 1px solid rgba(65, 134, 160, 0.45);
        display: flex;
        flex-direction: column;
        gap: 10px;
        justify-content: flex-start;
        align-items: flex-start;
    }

    .learner-register-panel > * {
        width: 100%;
    }

    .learner-register-head h1 {
        margin: 0;
        font-size: clamp(22px, 2.2vw, 30px);
        font-weight: 700;
        color: #1f2937;
    }

    .learner-title-accent {
        color: var(--form-theme-dark);
    }

    .learner-register-head p {
        margin: 8px 0 16px;
        color: #6b7280;
        font-size: 15px;
    }

    .learner-register-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        padding-bottom: clamp(12px, 2.2vh, 22px);
    }

    .learner-grid.two-col {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 18px;
    }

    .learner-photo-wrap {
        margin: clamp(10px, 1.6vw, 14px) clamp(10px, 1.6vw, 14px) clamp(18px, 2.2vw, 24px);
    }

    .learner-label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .learner-input {
        width: 100%;
        min-height: 50px;
        border-radius: 10px;
        border: 1px solid rgba(65, 134, 160, 0.45);
        font-size: 16px;
        line-height: 1.3;
        color: #1f2937;
        padding: 12px 14px;
        box-shadow: none;
        outline: none;
    }

    .learner-input::placeholder {
        color: #9aa0a6;
    }

    .learner-input:hover {
        border-color: var(--form-theme);
        box-shadow: 0 0 0 2px rgba(65, 134, 160, 0.10);
    }

    .learner-register-form .form-select.learner-input {
        padding-right: 40px;
        background-position: right 14px center;
    }

    .learner-check-row .form-check-input {
        border-color: rgba(65, 134, 160, 0.65);
    }

    .learner-check-row .form-check-input:hover,
    .learner-check-row .form-check-input:focus,
    .learner-check-row .form-check-input:checked {
        border-color: var(--form-theme);
        box-shadow: 0 0 0 3px rgba(65, 134, 160, 0.16);
    }

    .learner-input:focus {
        border-color: var(--form-theme);
        box-shadow: 0 0 0 3px rgba(65, 134, 160, 0.16);
    }

    .learner-register-form .form-select.learner-input:hover,
    .learner-register-form .form-select.learner-input:focus {
        border-color: var(--form-theme);
    }

    .learner-textarea {
        min-height: 112px;
        resize: vertical;
        padding-top: 12px;
    }

    .learner-check-row {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 13px;
        color: #4b5563;
        margin-top: 2px;
        line-height: 1.45;
    }

    .learner-submit-btn {
        min-height: 46px;
        border: none;
        border-radius: 10px;
        background: var(--form-theme);
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin-top: 4px;
        width: 100%;
    }

    .learner-submit-btn:hover {
        background: var(--form-theme-dark);
        color: #fff;
    }

    .learner-login-text {
        margin: 2px 0 0;
        font-size: 14px;
        text-align: center;
        color: #6b7280;
    }

    .learner-login-text a {
        color: var(--form-theme);
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 1320px) {
        .learner-register-shell {
            width: min(100%, 1120px);
            grid-template-columns: 1fr 1fr;
        }

        .learner-register-media {
            min-height: clamp(420px, 48vw, 560px);
        }
    }

    @media (max-width: 1080px) {
        .learner-register-page {
            padding: 20px 12px !important;
            background-attachment: scroll;
        }

        .learner-register-shell {
            width: min(100%, 860px);
            grid-template-columns: 1fr;
        }

        .learner-register-media {
            min-height: 240px;
            max-height: 300px;
        }

        .learner-register-panel {
            border-left: none;
            border-top: 1px solid rgba(65, 134, 160, 0.45);
            justify-content: center;
        }

        .learner-photo-wrap {
            margin: 8px 0 18px;
        }
    }

    @media (max-width: 680px) {
        .learner-register-panel {
            padding: 16px 14px;
        }

        .learner-grid.two-col {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .learner-register-shell {
            border-radius: 0;
        }

        .learner-register-head p {
            margin: 8px 0 12px;
        }

        .learner-register-page {
            padding: 16px 8px !important;
        }

        .alert {
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .learner-register-panel {
            padding: 14px 10px;
        }

        .learner-input {
            min-height: 46px;
            font-size: 15px;
            padding: 10px 12px;
        }

        .learner-textarea {
            min-height: 96px;
        }

        .learner-submit-btn {
            min-height: 44px;
            font-size: 15px;
        }

        .learner-login-text {
            font-size: 13px;
        }

        .learner-photo-wrap {
            margin: 6px 0 14px;
        }
    }

    /* Profile Photo Upload Styles */
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
    .learner-flash-thumb {
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
            margin: 0;
        }
        .ppu-fname { max-width: 100%; }

        .ppu-btn {
            width: 100%;
            justify-content: center;
        }
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
