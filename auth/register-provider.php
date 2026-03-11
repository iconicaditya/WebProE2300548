<?php
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

            <form action="#" method="post" class="provider-register-form" novalidate autocomplete="off">
                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Full Name</label>
                        <input type="text" class="form-control provider-input" placeholder="Enter your full name" autocomplete="off">
                    </div>
                    <div>
                        <label class="provider-label">Professional Title</label>
                        <input type="text" class="form-control provider-input" placeholder="Ex: Data Science Instructor" autocomplete="off">
                    </div>
                </div>

                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Email Address</label>
                        <input type="email" class="form-control provider-input" placeholder="name@example.com" autocomplete="off" autocapitalize="off" spellcheck="false">
                    </div>
                    <div>
                        <label class="provider-label">Mobile Number</label>
                        <input type="tel" class="form-control provider-input" placeholder="Enter mobile number" autocomplete="off">
                    </div>
                </div>

                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Primary Skill Category</label>
                        <select class="form-select provider-input">
                            <option selected disabled>Select category</option>
                            <option>Programming</option>
                            <option>Business</option>
                            <option>Design</option>
                            <option>Digital Marketing</option>
                            <option>Data Science</option>
                        </select>
                    </div>
                    <div>
                        <label class="provider-label">Teaching Experience</label>
                        <select class="form-select provider-input">
                            <option selected disabled>Select experience</option>
                            <option>0-1 years</option>
                            <option>2-4 years</option>
                            <option>5-8 years</option>
                            <option>9+ years</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="provider-label">Short Bio</label>
                    <textarea class="form-control provider-input provider-textarea" placeholder="Tell learners about your expertise and teaching approach" autocomplete="off"></textarea>
                </div>

                <div class="provider-grid two-col">
                    <div>
                        <label class="provider-label">Create Password</label>
                        <input type="password" class="form-control provider-input" placeholder="Create a strong password" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="provider-label">Confirm Password</label>
                        <input type="password" class="form-control provider-input" placeholder="Re-enter password" autocomplete="new-password">
                    </div>
                </div>

                <label class="provider-check-row">
                    <input class="form-check-input" type="checkbox">
                    <span>I agree to the platform terms, provider policy, and course quality guidelines.</span>
                </label>

                <button type="button" class="btn provider-submit-btn">Create Provider Account</button>

                <p class="provider-login-text">
                    Already have a provider account? <a href="<?php echo BASE_URL; ?>auth/login.php">Log in</a>
                </p>
            </form>
        </div>
    </section>
</main>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
