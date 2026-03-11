<?php
$pageTitle = 'Register as Learner';
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

            <form action="#" method="post" class="learner-register-form" novalidate autocomplete="off">
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
        background: linear-gradient(rgba(7, 20, 32, 0.55), rgba(7, 20, 32, 0.55)), url('<?php echo BASE_URL; ?>assets/images/registerlearnerbg.png') center/cover no-repeat;
        padding: 28px 14px;
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .learner-register-shell {
        width: min(100%, 1240px);
        min-height: auto;
        margin: 0 auto;
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
        padding: clamp(18px, 2.4vw, 34px);
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
        margin: 8px 0 20px;
        color: #6b7280;
        font-size: 15px;
    }

    .learner-register-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
        width: 100%;
    }

    .learner-grid.two-col {
        display: grid;
        grid-template-columns: repeat(2, minmax(250px, 1fr));
        gap: 16px 18px;
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
    }

    @media (max-width: 1080px) {
        .learner-register-page {
            padding: 20px 12px;
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
    }
</style>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
