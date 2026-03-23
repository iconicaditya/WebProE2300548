<style>
/* Modern Profile Styles */
.provider-main-content {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    padding: 32px 24px;
}
.dashboard-header {
    border-bottom: 1px solid #eee;
    margin-bottom: 24px;
    padding-bottom: 16px;
}
.dashboard-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2a2a2a;
}
.dashboard-subtitle {
    color: #888;
    font-size: 1.1rem;
}
.dashboard-section {
    margin-bottom: 32px;
}
.section-header {
    margin-bottom: 16px;
}
.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1a1a1a;
}
.profile-container {
    background: #f9f9fb;
    border-radius: 10px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.profile-header {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 24px;
}
.profile-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f8cff 60%, #a1c4fd 100%);
    color: #fff;
    font-size: 2.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(79,140,255,0.15);
}
.profile-info h3 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 600;
    color: #2a2a2a;
}
.profile-info p {
    margin: 2px 0;
    color: #666;
}
.profile-status {
    font-size: 0.95rem;
    color: #4f8cff;
    font-weight: 500;
}
.btn-secondary {
    background: #4f8cff;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 18px;
    font-size: 1rem;
    transition: background 0.2s;
}
.btn-secondary:hover {
    background: #357ae8;
}
.profile-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}
.detail-row {
    background: #fff;
    border-radius: 6px;
    padding: 12px 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
}
.detail-row label {
    font-weight: 500;
    color: #888;
    margin-bottom: 4px;
}
.detail-row span {
    color: #222;
    font-size: 1.05rem;
}
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    background: #e6f7ff;
    color: #4f8cff;
}
.status-active {
    background: #d1f7d6;
    color: #2ecc40;
}
.qualifications-list {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
}
.qualification-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    padding: 18px 20px;
    min-width: 220px;
    flex: 1 1 220px;
}
.qual-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.qual-header h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2a2a2a;
    margin: 0;
}
.qual-date {
    font-size: 0.95rem;
    color: #888;
    font-weight: 500;
}
.social-links-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.social-link-item {
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
}
.social-link-item label {
    font-weight: 500;
    color: #888;
    margin-bottom: 4px;
}
.social-link-item input {
    border: none;
    background: #f5f5f5;
    border-radius: 4px;
    padding: 6px 10px;
    color: #222;
    font-size: 1rem;
    pointer-events: none;
}
@media (max-width: 700px) {
    .provider-main-content {
        padding: 12px 4px;
    }
    .profile-header {
        flex-direction: column;
        gap: 12px;
    }
    .profile-details, .social-links-list {
        grid-template-columns: 1fr;
    }
    .qualifications-list {
        flex-direction: column;
    }
}
</style>
<style>
/* Fix content overlap with navbar/sidebar */
.provider-main-content {
    margin-top: 100px;
}
@media (max-width: 700px) {
    .provider-main-content {
        margin-top: 80px;
    }
}
</style>
<main class="provider-main-content">
<div class="provider-layout">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="provider-main-content">
        <!-- PROFILE COMPLETION MODULE -->
        <div id="profile-completion-module" style="margin-bottom: 32px; display: flex; align-items: center; gap: 32px;">
            <div style="position: relative; width: 90px; height: 90px;">
                <svg width="90" height="90">
                    <circle cx="45" cy="45" r="40" stroke="#e0e0e0" stroke-width="8" fill="none"/>
                    <circle id="profile-progress-bar" cx="45" cy="45" r="40" stroke="#4f8cff" stroke-width="8" fill="none" stroke-linecap="round" stroke-dasharray="251.2" stroke-dashoffset="125.6"/>
                </svg>
                <div id="profile-progress-label" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.3rem; font-weight: 700; color: #4f8cff;">50%</div>
            </div>
            <div>
                <div style="font-size: 1.1rem; font-weight: 500; color: #333; margin-bottom: 8px;">Profile Completion</div>
                <button id="complete-profile-btn" class="btn btn-secondary" style="font-size: 1rem;">Complete Profile</button>
            </div>
        </div>

        <!-- EXPANDABLE PROFILE COMPLETION INTERFACE -->
        <div id="profile-completion-interface" style="display: none; background: #f9f9fb; border-radius: 10px; padding: 32px 24px; margin-bottom: 32px;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #2a2a2a; margin-bottom: 24px;">Complete Your Profile</h2>
            <form id="profile-completion-form" autocomplete="off">
                <!-- Education Section -->
                <div style="margin-bottom: 32px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 1.2rem; font-weight: 600; color: #1a1a1a;">Education</h3>
                        <button type="button" class="btn btn-secondary" onclick="addEducation()">Add Education</button>
                    </div>
                    <div id="education-section"></div>
                </div>
                <!-- Experience Section -->
                <div style="margin-bottom: 32px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 1.2rem; font-weight: 600; color: #1a1a1a;">Experience</h3>
                        <button type="button" class="btn btn-secondary" onclick="addExperience()">Add Experience</button>
                    </div>
                    <div id="experience-section"></div>
                </div>
                <!-- Certificates Section -->
                <div style="margin-bottom: 32px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-size: 1.2rem; font-weight: 600; color: #1a1a1a;">Certificates</h3>
                        <button type="button" class="btn btn-secondary" onclick="addCertificate()">Add Certificate</button>
                    </div>
                    <div id="certificates-section"></div>
                </div>
                <button type="button" class="btn btn-success" style="font-size: 1.1rem; padding: 10px 32px;" onclick="submitProfileCompletion()">Complete & Apply</button>
            </form>
        </div>
        <!-- HEADER SECTION -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">My Profile</h1>
            <p class="dashboard-subtitle">View and update your profile information.</p>
        </div>

        <!-- PROFILE INFORMATION SECTION -->
        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">Profile Information</h2>
            </div>

            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar-large">JD</div>
                    <div class="profile-info">
                        <h3>John Doe</h3>
                        <p>john@example.com</p>
                        <p class="profile-status">Verified Instructor • Member since March 2024</p>
                    </div>
                    <button class="btn btn-secondary">Edit Profile</button>
                </div>

                <div class="profile-details">
                    <div class="detail-row">
                        <label>Full Name</label>
                        <span>John Doe</span>
                    </div>
                    <div class="detail-row">
                        <label>Email Address</label>
                        <span>john@example.com</span>
                    </div>
                    <div class="detail-row">
                    <label>Phone Number</label>
                    <span>+1 (555) 123-4567</span>
                </div>
                <div class="detail-row">
                    <label>Location</label>
                    <span>San Francisco, USA</span>
                </div>
                <div class="detail-row">
                    <label>Bio</label>
                    <span>Passionate about teaching web development and design. 10+ years of experience in the tech industry.</span>
                </div>
                <div class="detail-row">
                    <label>Specialization</label>
                    <span>Web Development, UI/UX Design, JavaScript</span>
                </div>
                <div class="detail-row">
                    <label>verification Status</label>
                    <span><span class="status-badge status-active">Verified</span></span>
                </div>
            </div>
        </div>
    </section>

    <!-- QUALIFICATIONS & CERTIFICATIONS SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Qualifications & Certifications</h2>
        </div>

        <div class="qualifications-list">
            <div class="qualification-card">
                <div class="qual-header">
                    <h4>Bachelor of Science in Computer Science</h4>
                    <span class="qual-date">2013</span>
                </div>
                <p>University of California, Berkeley</p>
            </div>

            <div class="qualification-card">
                <div class="qual-header">
                    <h4>Web Development Specialist Certification</h4>
                    <span class="qual-date">2020</span>
                </div>
                <p>Google Developers Certification Program</p>
            </div>

            <div class="qualification-card">
                <div class="qual-header">
                    <h4>Advanced JavaScript Course</h4>
                    <span class="qual-date">2021</span>
                </div>
                <p>The Complete JavaScript Course 2021</p>
            </div>
        </div>
    </section>

    <!-- SOCIAL LINKS SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Social & Website Links</h2>
        </div>

        <div class="social-links-list">
            <div class="social-link-item">
                <label>Website</label>
                <input type="text" value="https://johndoe.com" readonly>
            </div>
            <div class="social-link-item">
                <label>LinkedIn</label>
                <input type="text" value="https://linkedin.com/in/johndoe" readonly>
            </div>
            <div class="social-link-item">
                <label>GitHub</label>
                <input type="text" value="https://github.com/johndoe" readonly>
            </div>
            <div class="social-link-item">
                <label>Twitter</label>
                <input type="text" value="https://twitter.com/johndoe" readonly>
            </div>
        </div>
    </section>
</main>
<script>
// --- Profile Completion Progress Logic ---
let completion = 50; // Default
function updateProgress(percent) {
    const circle = document.getElementById('profile-progress-bar');
    const label = document.getElementById('profile-progress-label');
    const radius = 40;
    const circumference = 2 * Math.PI * radius;
    const offset = circumference * (1 - percent / 100);
    circle.style.strokeDashoffset = offset;
    label.textContent = percent + '%';
}
function recalculateCompletion() {
    let total = 0, filled = 0;
    // Education
    const edu = document.querySelectorAll('.edu-entry');
    total += 1; filled += edu.length > 0 ? 1 : 0;
    // Experience
    const exp = document.querySelectorAll('.exp-entry');
    total += 1; filled += exp.length > 0 ? 1 : 0;
    // Certificates
    const cert = document.querySelectorAll('.cert-entry');
    total += 1; filled += cert.length > 0 ? 1 : 0;
    // You can add more required fields here
    let percent = Math.round((filled / total) * 100);
    if (percent < 50) percent = 50; // Minimum default
    updateProgress(percent);
}
// --- Expand/Collapse Profile Completion Interface ---
document.getElementById('complete-profile-btn').onclick = function() {
    document.getElementById('profile-completion-interface').style.display = 'block';
    this.style.display = 'none';
};
// --- Dynamic Add/Remove for Education ---
function addEducation() {
    const container = document.getElementById('education-section');
    const idx = Date.now();
    const div = document.createElement('div');
    div.className = 'edu-entry';
    div.style = 'background:#fff; border-radius:8px; padding:16px 18px; margin-bottom:16px; box-shadow:0 1px 4px rgba(0,0,0,0.04); position:relative;';
    div.innerHTML = `
        <button type="button" onclick="this.parentNode.remove(); recalculateCompletion();" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:18px;color:#888;">&times;</button>
        <div style="margin-bottom:8px;"><label>School/University*</label><input type="text" name="education[${idx}][school]" class="form-control" required onchange="recalculateCompletion()"></div>
        <div style="margin-bottom:8px;"><label>Degree*</label><input type="text" name="education[${idx}][degree]" class="form-control" required onchange="recalculateCompletion()"></div>
        <div style="margin-bottom:8px;"><label>Year*</label><input type="text" name="education[${idx}][year]" class="form-control" required onchange="recalculateCompletion()"></div>
    `;
    container.appendChild(div);
    recalculateCompletion();
}
// --- Dynamic Add/Remove for Experience ---
function addExperience() {
    const container = document.getElementById('experience-section');
    const idx = Date.now();
    const div = document.createElement('div');
    div.className = 'exp-entry';
    div.style = 'background:#fff; border-radius:8px; padding:16px 18px; margin-bottom:16px; box-shadow:0 1px 4px rgba(0,0,0,0.04); position:relative;';
    div.innerHTML = `
        <button type="button" onclick="this.parentNode.remove(); recalculateCompletion();" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:18px;color:#888;">&times;</button>
        <div style="margin-bottom:8px;"><label>Company*</label><input type="text" name="experience[${idx}][company]" class="form-control" required onchange="recalculateCompletion()"></div>
        <div style="margin-bottom:8px;"><label>Role*</label><input type="text" name="experience[${idx}][role]" class="form-control" required onchange="recalculateCompletion()"></div>
        <div style="margin-bottom:8px;"><label>Years*</label><input type="text" name="experience[${idx}][years]" class="form-control" required onchange="recalculateCompletion()"></div>
    `;
    container.appendChild(div);
    recalculateCompletion();
}
// --- Dynamic Add/Remove for Certificates ---
function addCertificate() {
    const container = document.getElementById('certificates-section');
    const idx = Date.now();
    const div = document.createElement('div');
    div.className = 'cert-entry';
    div.style = 'background:#fff; border-radius:8px; padding:16px 18px; margin-bottom:16px; box-shadow:0 1px 4px rgba(0,0,0,0.04); position:relative;';
    div.innerHTML = `
        <button type="button" onclick="this.parentNode.remove(); recalculateCompletion();" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:18px;color:#888;">&times;</button>
        <div style="margin-bottom:8px;"><label>Certificate Name*</label><input type="text" name="certificates[${idx}][name]" class="form-control" required onchange="recalculateCompletion()"></div>
        <div style="margin-bottom:8px;"><label>Issued By*</label><input type="text" name="certificates[${idx}][issuer]" class="form-control" required onchange="recalculateCompletion()"></div>
        <div style="margin-bottom:8px;"><label>Year*</label><input type="text" name="certificates[${idx}][year]" class="form-control" required onchange="recalculateCompletion()"></div>
    `;
    container.appendChild(div);
    recalculateCompletion();
}
// --- Submission Logic ---
function submitProfileCompletion() {
    // Validate required fields
    const form = document.getElementById('profile-completion-form');
    const required = form.querySelectorAll('input[required]');
    let valid = true;
    required.forEach(input => { if (!input.value.trim()) valid = false; });
    if (!valid) {
        alert('Please fill all required fields in each section.');
        return;
    }
    // Simulate save and mark as complete
    updateProgress(100);
    alert('Profile completed and applied!');
    document.getElementById('profile-completion-interface').style.display = 'none';
    document.getElementById('complete-profile-btn').style.display = 'inline-block';
}
// Initial progress
updateProgress(50);
</script>
