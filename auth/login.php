<?php
$pageTitle = 'Log In';
require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../includes/navbar.php');
?>

<main class="login-page">
    <section class="login-shell">
        <aside class="login-media">
            <img src="<?php echo BASE_URL; ?>assets/images/logincard.png" alt="Login Card">
            <div class="login-media-overlay">
                <h2>Welcome Back</h2>
                <p>Log in to continue to your courses and dashboards.</p>
            </div>
        </aside>

        <div class="login-panel">
            <div class="login-head">
                <h1><span class="login-title-accent">Sign In</span></h1>
                <p>Enter your credentials to access your EduSkill account.</p>
            </div>

            <form action="#" method="post" class="login-form" novalidate autocomplete="off">
                <!-- Hidden fields to prevent browser autofill from inserting saved credentials
                     Browsers often autofill the first username/password fields they detect.
                     These invisible fields capture autofill so the visible inputs remain empty. -->
                <input type="text" name="prevent_autofill_username" id="prevent_autofill_username" autocomplete="username" style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; opacity:0; pointer-events:none;" />
                <input type="password" name="prevent_autofill_password" id="prevent_autofill_password" autocomplete="current-password" style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; opacity:0; pointer-events:none;" />
                <div>
                    <label class="login-label">Email Address</label>
                    <input type="email" name="email" class="form-control login-input" placeholder="name@example.com" autocomplete="username" autocapitalize="off" spellcheck="false" value="">
                </div>

                <div>
                    <label class="login-label">Password</label>
                    <input type="password" name="password" class="form-control login-input" placeholder="Enter your password" autocomplete="current-password" value="">
                </div>

                <label class="login-check-row">
                    <input class="form-check-input" type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="btn login-submit-btn">Log In</button>

                <p class="login-register-text">
                    New here? <a href="<?php echo BASE_URL; ?>auth/register-learner.php">Register as Learner</a> or <a href="<?php echo BASE_URL; ?>auth/register-provider.php">Register as Provider</a>
                </p>

                <div class="login-info">
                    <p class="mb-0">Access your EduSkill dashboard to manage courses, enrollments, and account settings. For help, email <a href="mailto:support@eduskill.com">support@eduskill.com</a>.</p>
                </div>
            </form>
        </div>
    </section>
</main>

<style>
    .login-page {
        --form-theme: #4186a0;
        --form-theme-dark: #2f728a;
        background: url('<?php echo BASE_URL; ?>assets/images/loginbg.png') top center/cover no-repeat;
        padding: 0 14px; /* remove bottom padding that created white gap */
        height: calc(100vh - 108px); /* account for fixed navbar (64 + 44) */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-shell {
        width: min(100%, 1000px);
        min-height: auto;
        margin: 0 auto;
        background: rgba(255,255,255,0.60);
        border-radius: 0;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        box-shadow: 0 18px 38px rgba(0,0,0,0.2);
    }

    .login-media { position: relative; min-height: 420px; background: #0f172a; }
    .login-media img { width:100%; height:100%; object-fit:cover; display:block; }
    .login-media-overlay { position:absolute; left:0; right:0; bottom:0; padding:22px; color:#fff; background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(2,6,23,0.75) 100%); }
    .login-media-overlay h2 { margin:0 0 6px; font-size:24px; font-weight:700; }
    .login-media-overlay p { margin:0; font-size:14px; opacity:0.95; }

    .login-panel { padding: clamp(18px, 2.4vw, 34px); border-left: 1px solid rgba(65, 134, 160, 0.25); display:flex; flex-direction:column; gap:10px; justify-content:center; }
    .login-head h1 { margin:0; font-size:clamp(22px,2.2vw,30px); font-weight:700; color:#1f2937; }
    .login-title-accent { color: var(--form-theme-dark); }
    .login-head p { margin:8px 0 20px; color:#6b7280; font-size:15px; }

    .login-form { display:flex; flex-direction:column; gap:14px; width:100%; }
    .login-label { display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#374151; }
    .login-input { width:100%; min-height:50px; border-radius:10px; border:1px solid rgba(65,134,160,0.25); font-size:16px; padding:12px 14px; }
    .login-input::placeholder { color:#9aa0a6; }
    .login-input:focus { border-color:var(--form-theme); box-shadow: 0 0 0 3px rgba(65,134,160,0.10); }

    .login-check-row { display:flex; gap:10px; align-items:center; font-size:13px; color:#4b5563; margin-top:2px; }

    .login-submit-btn { min-height:46px; border:none; border-radius:10px; background:var(--form-theme); color:#fff; font-size:16px; font-weight:600; }
    .login-submit-btn:hover { background:var(--form-theme-dark); }

    .login-register-text { margin:2px 0 0; font-size:14px; text-align:left; color:#6b7280; }
    .login-register-text a { color:var(--form-theme); text-decoration:none; font-weight:600; }

    @media (max-width: 1080px) {
        .login-shell { grid-template-columns: 1fr; }
        .login-media { min-height: 260px; }
        .login-panel { border-left:none; border-top:1px solid rgba(65,134,160,0.25); }
    }

    @media (max-width: 680px) {
        .login-panel { padding: 16px 14px; }
        .login-shell { border-radius: 0; }
    }
</style>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
