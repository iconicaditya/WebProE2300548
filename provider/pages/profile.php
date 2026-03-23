<main class="provider-main-content">

<style>
.profile-page {
    --accent:       #4186a0;
    --accent-dark:  #2f728a;
    --accent-light: #e8f4f8;
    --success:      #16a34a;
    --success-bg:   #dcfce7;
    --warn:         #d97706;
    --warn-bg:      #fef3c7;
    --surface:      #ffffff;
    --bg:           #f4f7f9;
    --border:       #e2e8f0;
    --text:         #1e293b;
    --muted:        #64748b;
    --radius:       14px;
    --shadow:       0 2px 16px rgba(0,0,0,0.07);
}
.profile-page { display:flex; flex-direction:column; gap:24px; padding:8px 0 40px; max-width:960px; }

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
.hero-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:20px; position:relative; z-index:1; }
.completion-bar-wrap { background:rgba(255,255,255,0.12); border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:12px; }
.completion-ring { position:relative; width:52px; height:52px; flex-shrink:0; }
.completion-ring svg { transform:rotate(-90deg); }
.completion-ring-label { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:0.72rem; font-weight:700; color:#fff; }
.completion-text strong { display:block; font-size:0.82rem; font-weight:700; color:#fff; }
.completion-text span   { font-size:0.74rem; color:rgba(255,255,255,0.75); }
.btn-edit-hero { background:#fff; color:var(--accent-dark); border:none; border-radius:8px; padding:10px 22px; font-size:0.88rem; font-weight:700; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.12); transition:transform .15s,box-shadow .15s; white-space:nowrap; }
.btn-edit-hero:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(0,0,0,0.16); }

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
.btn-card-action { background:var(--accent-light); color:var(--accent-dark); border:none; border-radius:7px; padding:6px 14px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:background .15s; }
.btn-card-action:hover { background:#cce7f0; }
.profile-card-body { padding:22px 26px; }

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
    .info-grid{grid-template-columns:1fr;}
    .info-field.full{grid-column:1;}
    .social-grid{grid-template-columns:1fr;}
    .stats-row{grid-template-columns:1fr 1fr;}
    .profile-card-body{padding:16px;}
    .profile-card-header{padding:14px 16px 12px;}
    .qual-grid{grid-template-columns:1fr;}
}
</style>

<div class="profile-page">

    <!-- Hero Banner -->
    <div class="profile-hero">
        <div class="profile-hero-inner">
            <div class="profile-avatar">JD</div>
            <div class="profile-hero-info">
                <h2>John Doe</h2>
                <p class="hero-email">john@example.com</p>
                <div class="hero-badges">
                    <span class="hero-badge verified">✓ Verified Instructor</span>
                    <span class="hero-badge">🎓 Member since March 2024</span>
                    <span class="hero-badge">📍 San Francisco, USA</span>
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
                    <div class="completion-ring-label" id="profileRingLabel">50%</div>
                </div>
                <div class="completion-text">
                    <strong>Profile Completion</strong>
                    <span id="completionHint">Add more info to reach 100%</span>
                </div>
            </div>
            <button class="btn-edit-hero">✏️ Edit Profile</button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="profile-card">
        <div class="profile-card-body" style="padding:18px 26px;">
            <div class="stats-row">
                <div class="stat-box"><span class="stat-num">12</span><span class="stat-label">Total Courses</span></div>
                <div class="stat-box"><span class="stat-num">340</span><span class="stat-label">Students</span></div>
                <div class="stat-box"><span class="stat-num">4.5★</span><span class="stat-label">Avg Rating</span></div>
                <div class="stat-box"><span class="stat-num">$4.5K</span><span class="stat-label">Revenue</span></div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">👤</span> Profile Information</h3>
            <button class="btn-card-action">Edit</button>
        </div>
        <div class="profile-card-body">
            <div class="info-grid">
                <div class="info-field"><label>Full Name</label><div class="field-value">John Doe</div></div>
                <div class="info-field"><label>Email Address</label><div class="field-value">john@example.com</div></div>
                <div class="info-field"><label>Phone Number</label><div class="field-value">+1 (555) 123-4567</div></div>
                <div class="info-field"><label>Location</label><div class="field-value">San Francisco, USA</div></div>
                <div class="info-field"><label>Specialization</label><div class="field-value">Web Development, UI/UX Design, JavaScript</div></div>
                <div class="info-field"><label>Verification Status</label><div class="field-value badge-val"><span class="badge-verified">✓ Verified</span></div></div>
                <div class="info-field full"><label>Bio</label><div class="field-value">Passionate about teaching web development and design. 10+ years of experience in the tech industry. I believe in hands-on learning and practical projects.</div></div>
            </div>
        </div>
    </div>

    <!-- Qualifications -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">🎓</span> Qualifications & Certifications</h3>
            <button class="btn-card-action" onclick="addQualCard()">+ Add</button>
        </div>
        <div class="profile-card-body">
            <div class="qual-grid" id="qualGrid">
                <div class="qual-card">
                    <span class="qual-year">2013</span>
                    <div class="qual-type"><span class="qual-dot"></span>Education</div>
                    <h4>Bachelor of Science in Computer Science</h4>
                    <p>University of California, Berkeley</p>
                </div>
                <div class="qual-card">
                    <span class="qual-year">2020</span>
                    <div class="qual-type"><span class="qual-dot" style="background:#16a34a;"></span>Certification</div>
                    <h4>Web Development Specialist Certification</h4>
                    <p>Google Developers Certification Program</p>
                </div>
                <div class="qual-card">
                    <span class="qual-year">2021</span>
                    <div class="qual-type"><span class="qual-dot" style="background:#d97706;"></span>Course</div>
                    <h4>Advanced JavaScript Course</h4>
                    <p>The Complete JavaScript Course 2021</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Links -->
    <div class="profile-card">
        <div class="profile-card-header">
            <h3><span class="card-icon">🔗</span> Social & Website Links</h3>
            <button class="btn-card-action">Edit Links</button>
        </div>
        <div class="profile-card-body">
            <div class="social-grid">
                <a href="https://johndoe.com" class="social-item" target="_blank">
                    <div class="social-icon-wrap" style="background:#e0f2fe;">🌐</div>
                    <div class="social-text"><label>Website</label><span>johndoe.com</span></div>
                </a>
                <a href="https://linkedin.com/in/johndoe" class="social-item" target="_blank">
                    <div class="social-icon-wrap" style="background:#dbeafe;">💼</div>
                    <div class="social-text"><label>LinkedIn</label><span>linkedin.com/in/johndoe</span></div>
                </a>
                <a href="https://github.com/johndoe" class="social-item" target="_blank">
                    <div class="social-icon-wrap" style="background:#f3e8ff;">💻</div>
                    <div class="social-text"><label>GitHub</label><span>github.com/johndoe</span></div>
                </a>
                <a href="https://twitter.com/johndoe" class="social-item" target="_blank">
                    <div class="social-icon-wrap" style="background:#dbeafe;">🐦</div>
                    <div class="social-text"><label>Twitter / X</label><span>twitter.com/johndoe</span></div>
                </a>
            </div>
        </div>
    </div>

</div><!-- .profile-page -->

<script>
(function(){
    var circumference = 2 * Math.PI * 22;
    function setRing(p){
        var offset = circumference * (1 - p / 100);
        document.getElementById('profileRingCircle').style.strokeDashoffset = offset;
        document.getElementById('profileRingLabel').textContent = p + '%';
        document.getElementById('completionHint').textContent =
            p >= 100 ? 'Profile complete! 🎉' : 'Add more info to reach 100%';
    }
    setRing(50);
    window._setProfileCompletion = setRing;
})();

function addQualCard(){
    var g = document.getElementById('qualGrid');
    var d = document.createElement('div');
    d.className = 'qual-card';
    d.style.border = '2px dashed var(--accent)';
    d.style.background = 'var(--accent-light)';
    d.innerHTML = '<div class="qual-type"><span class="qual-dot"></span>New</div><h4 style="color:var(--accent-dark);">New Qualification</h4><p>Connect backend to save</p>';
    g.appendChild(d);
}
</script>
</main>