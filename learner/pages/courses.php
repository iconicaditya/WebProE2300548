<?php
/**
 * Learner - My Courses / Course Portal
 * Fully self-contained: all CSS + JS inside this file only.
 * No dependency on sandhya.css or any external stylesheet for portal layout.
 */

$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$portalCourses = ems_learner_fetch_learning_portal_courses($conn, $learnerUserId, 30);
?>

<main class="provider-main-content courses-main-content">

<style>
/* ══════════════════════════════════════════════════════
   COURSE PORTAL — ALL STYLES SELF-CONTAINED
   ══════════════════════════════════════════════════════ */

/* 1. Override parent padding when portal is active — done via JS inline style */
/* 2. All variables scoped to .courses-page so nothing leaks */

.courses-page {
    --accent:     #4186a0;
    --accent-dk:  #2f728a;
    --accent-lt:  #e8f4f8;
    --success:    #0f766e;
    --border:     #e2e8f0;
    --bg:         #f4f7f9;
    --surface:    #ffffff;
    --text:       #1e293b;
    --muted:      #64748b;
    --sidebar-w:  320px;
    --card-radius: 18px;
    font-family: 'Inter', system-ui, sans-serif;
    width: min(1380px, 100%);
    margin: 0 auto;
    min-width: 0;
}

/* Expand learner content width specifically for course portal page */
body.learner-dashboard .provider-main-content.courses-main-content {
    max-width: none !important;
    margin-right: 0 !important;
    width: 100%;
    background: transparent;
    box-shadow: none;
    border-radius: 0;
    padding: 22px 26px 30px;
}

/* ── view toggle ── */
.cp-view          { display: none; }
.cp-view.active   { display: block; }

/* ══ MY LEARNING LIST ══ */
.ml-header {
    margin-bottom: 22px;
    display: grid;
    gap: 6px;
}
.ml-header h1 {
    margin: 0;
    font-size: clamp(1.42rem, 2.2vw, 2rem);
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.02em;
}
.ml-header p {
    margin: 0;
    color: var(--muted);
    font-size: 0.95rem;
    line-height: 1.58;
    max-width: 760px;
}

.enrolled-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(285px, 1fr));
    gap: 20px;
    align-items: stretch;
}

.sidebar-empty {
    padding: 16px;
    font-size: 0.84rem;
    color: #607387;
}

.enrolled-empty {
    grid-column: 1 / -1;
    border: 1px dashed #c9d8e5;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
    border-radius: 16px;
    padding: 24px;
}

.enrolled-empty h3 {
    margin: 0 0 8px;
    font-size: 1.05rem;
    color: #203043;
}

.enrolled-empty p {
    margin: 0;
    font-size: 0.92rem;
    color: #607387;
}

.enroll-card {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border: 1px solid #d8e5ef;
    border-radius: var(--card-radius);
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    transition: transform .25s, box-shadow .25s, border-color .25s;
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    min-height: 100%;
}
.enroll-card::before {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 3px;
    background: linear-gradient(90deg, #0f766e, #2ba5c7, #5bb4d6);
    opacity: 0;
    transition: opacity .25s ease;
    z-index: 2;
}
.enroll-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 34px rgba(15, 23, 42, 0.14);
    border-color: #bcd4e2;
}
.enroll-card:hover::before { opacity: 1; }
.enroll-thumb {
    aspect-ratio: 16 / 9;
    background-size: cover;
    background-position: center;
    position: relative;
    min-height: 172px;
}
.enroll-thumb-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.05) 8%, rgba(15, 23, 42, 0.62) 100%);
}
.enroll-badge {
    position: absolute; top: 10px; left: 10px;
    background: rgba(65, 134, 160, 0.92);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 999px;
    backdrop-filter: blur(2px);
    z-index: 1;
}
.enroll-progress-chip {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #fff;
    background: rgba(15, 118, 110, 0.9);
    padding: 4px 9px;
    border-radius: 999px;
    z-index: 1;
}
.enroll-body {
    padding: 16px 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 11px;
    flex: 1;
}
.enroll-title {
    margin: 0;
    font-size: 1.04rem;
    font-weight: 800;
    color: #142338;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.7em;
}
.enroll-inst {
    margin: 0;
    font-size: 0.83rem;
    color: #607387;
    display: flex;
    align-items: center;
    gap: 7px;
}
.inst-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #3c9aba;
    flex-shrink: 0;
}
.pb-wrap {
    background: #f6fafc;
    border: 1px solid #e2edf4;
    border-radius: 11px;
    padding: 10px 11px;
    margin-top: 2px;
}
.pb-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.76rem;
    color: #617487;
    margin-bottom: 7px;
}
.pb-label strong {
    font-size: 0.78rem;
    color: #1f2d3d;
}
.pb-track {
    height: 8px;
    background: #dbe6ef;
    border-radius: 99px;
    overflow: hidden;
}
.pb-fill {
    height: 100%;
    background: linear-gradient(90deg, #0f766e 0%, #14b8a6 52%, #4cc9b0 100%);
    border-radius: 99px;
}
.enroll-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: auto;
}
.enroll-status {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 999px;
    border: 1px solid transparent;
    white-space: nowrap;
}
.enroll-status.active {
    color: #0f766e;
    background: #ecfdf5;
    border-color: #b9f2d7;
}
.enroll-status.complete {
    color: #1d4ed8;
    background: #eff6ff;
    border-color: #c7ddff;
}
.btn-continue {
    flex: 1;
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dk) 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 0.89rem;
    font-weight: 700;
    cursor: pointer;
    transition: transform .16s ease, box-shadow .18s ease, filter .16s ease;
    box-shadow: 0 7px 16px rgba(65, 134, 160, 0.28);
    min-height: 42px;
}
.btn-continue:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(65, 134, 160, 0.33);
    filter: brightness(1.03);
}
.enroll-card:focus-visible,
.btn-continue:focus-visible,
.portal-back:focus-visible,
.btn-nav:focus-visible {
    outline: 3px solid rgba(65, 134, 160, 0.38);
    outline-offset: 2px;
}

/* ══ COURSE PORTAL ══ */
.portal-wrap { display: flex; flex-direction: column; }

/* Header */
.portal-header       { padding-bottom: 18px; border-bottom: 1px solid var(--border); margin-bottom: 0; }
.portal-back {
    display: inline-flex; align-items: center; gap: 6px;
    background: none; border: none; color: var(--accent);
    font-size: 0.84rem; font-weight: 600; cursor: pointer;
    padding: 0; margin-bottom: 10px; transition: gap .15s;
}
.portal-back:hover { gap: 10px; }
.portal-header h1   { margin: 0 0 5px; font-size: 1.55rem; font-weight: 800; color: var(--text); line-height: 1.2; }
.portal-header p    { margin: 0; color: var(--muted); font-size: 0.88rem; line-height: 1.55; max-width: 800px; }

/* Portal body: sidebar + viewer — FULL WIDTH, NO GAPS */
.portal-body {
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr;
    min-height: 580px;
    border: 1px solid var(--border);
    border-radius: 12px 12px 0 0;
    border-bottom: none;
    overflow: hidden;
    margin-top: 18px;
    background: var(--surface);
    box-shadow: 0 4px 24px rgba(15,23,42,0.07);
    width: 100%;
    margin-left: 0;
    margin-right: 0;
    box-sizing: border-box;
}

/* ── Sidebar ── */
.portal-sidebar {
    border-right: 1px solid var(--border);
    overflow-y: auto;
    max-height: 650px;
    background: #fafbfc;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}
.portal-sidebar::-webkit-scrollbar       { width: 5px; }
.portal-sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.sidebar-section        { border-bottom: 1px solid var(--border); }
.sidebar-sec-hdr {
    display: flex; align-items: flex-start; gap: 8px;
    padding: 12px 14px; cursor: pointer;
    background: var(--accent-lt); user-select: none;
}
.sidebar-sec-hdr:hover  { background: #d4eaf3; }
.sidebar-sec-title      { font-size: 0.8rem; font-weight: 700; color: var(--accent-dk); flex: 1; min-width: 0; line-height: 1.35; }
.sidebar-sec-title strong { color: var(--accent-dk); margin-right: 3px; }
.sidebar-sec-dur        { font-size: 0.7rem; color: var(--muted); white-space: nowrap; flex-shrink: 0; padding-top: 2px; }
.sidebar-sec-chevron    { font-size: 0.68rem; color: var(--accent); transition: transform .2s; flex-shrink: 0; padding-top: 3px; }
.sidebar-section.collapsed .sidebar-sec-chevron { transform: rotate(-90deg); }
.sidebar-section.collapsed .sidebar-lessons     { display: none; }

.sidebar-lessons        { list-style: none; margin: 0; padding: 0; }
.sidebar-lesson {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px 10px 18px;
    cursor: pointer; border-bottom: 1px solid #f0f4f7;
    transition: background .12s;
}
.sidebar-lesson:hover   { background: #f0f7fb; }
.sidebar-lesson.active  {
    background: var(--accent-lt);
    border-left: 3px solid var(--accent);
    padding-left: 15px;
}
.l-icon  { font-size: 0.85rem; flex-shrink: 0; width: 18px; text-align: center; color: var(--accent); }
.l-icon.quiz-i { color: #7c3aed; }
.l-icon.proj-i { color: #0369a1; }
.l-name  { flex: 1; min-width: 0; font-size: 0.81rem; color: var(--text); line-height: 1.35; }
.l-dur   { font-size: 0.7rem; color: var(--muted); white-space: nowrap; flex-shrink: 0; }

/* ── Viewer ── */
.portal-viewer {
    display: flex; flex-direction: column;
    min-width: 0; flex: 1; overflow: hidden;
}
.viewer-content {
    flex: 1;
    background: #000;
    min-height: clamp(560px, 72vh, 920px);
    display: flex;
    position: relative;
    width: 100%;
    overflow: hidden;
    align-items: stretch;
}
.viewer-empty-state {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 18px;
    color: #64748b;
    font-size: 0.92rem;
    background: linear-gradient(180deg, #f8fbfd 0%, #f0f6fa 100%);
    z-index: 1;
}
.viewer-empty-state.hidden { display: none; }
.viewer-video {
    width: 100%; min-height: 500px;
    display: block; background: #000; object-fit: contain;
}
.viewer-pdf {
    width: 100%;
    height: 100%;
    min-height: clamp(560px, 72vh, 920px);
    border: none;
    background: #fff;
    display: none;
}
.viewer-content.pdf-active {
    background: #ecf2f7;
    padding: 12px;
}
.viewer-content.pdf-active .viewer-pdf {
    border: 1px solid #d2dee8;
    border-radius: 12px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
    min-height: calc(clamp(560px, 72vh, 920px) - 24px);
}
.viewer-quiz, .viewer-project {
    display: none; width: 100%; padding: 32px;
    background: var(--surface); min-height: 500px;
    overflow-y: auto; box-sizing: border-box;
}

/* Quiz */
.quiz-title  { font-size: 1.1rem; font-weight: 700; color: var(--text); margin: 0 0 20px; }
.quiz-q      { margin-bottom: 22px; }
.quiz-q-text { font-size: 0.93rem; font-weight: 600; color: var(--text); margin-bottom: 10px; }
.quiz-opts   { display: grid; gap: 8px; }
.quiz-opt {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border: 1.5px solid var(--border);
    border-radius: 8px; cursor: pointer; font-size: 0.87rem; color: var(--text);
    transition: border-color .15s, background .15s;
}
.quiz-opt:hover    { border-color: var(--accent); background: var(--accent-lt); }
.quiz-opt input    { flex-shrink: 0; accent-color: var(--accent); }
.btn-quiz-sub {
    margin-top: 16px; background: var(--accent); color: #fff;
    border: none; border-radius: 8px; padding: 10px 28px;
    font-size: 0.88rem; font-weight: 700; cursor: pointer;
}
.btn-quiz-sub:hover { background: var(--accent-dk); }

/* Project */
.proj-title  { font-size: 1.1rem; font-weight: 700; color: var(--text); margin: 0 0 10px; }
.proj-desc   { color: var(--muted); font-size: 0.9rem; margin: 0 0 20px; line-height: 1.55; }
.proj-submit-box {
    background: var(--bg); border: 1px solid var(--border);
    border-radius: 10px; padding: 20px;
}
.proj-submit-box p  { font-size: 0.84rem; color: var(--muted); margin: 0 0 12px; }
.proj-input {
    width: 100%; padding: 10px 14px; margin-bottom: 10px;
    border: 1.5px solid var(--border); border-radius: 8px;
    font-size: 0.88rem; box-sizing: border-box;
}
.proj-input:focus { outline: none; border-color: var(--accent); }

/* Footer nav */
.viewer-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 20px; border-top: 1px solid var(--border);
    background: var(--surface); gap: 12px;
    border-top: 1px solid var(--border);
}
.viewer-label        { flex: 1; min-width: 0; text-align: center; }
.viewer-label strong { display: block; font-size: 0.87rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.viewer-label span   { font-size: 0.72rem; color: var(--muted); }
.btn-nav {
    background: var(--accent); color: #fff; border: none;
    border-radius: 8px; padding: 10px 28px;
    font-size: 0.88rem; font-weight: 700; cursor: pointer;
    white-space: nowrap; transition: background .15s, opacity .15s;
    flex-shrink: 0;
}
.btn-nav:hover    { background: var(--accent-dk); }
.btn-nav:disabled { opacity: 0.35; cursor: not-allowed; }
.btn-nav.prev {
    background: #fff; color: var(--accent);
    border: 2px solid var(--accent);
}
.btn-nav.prev:hover { background: var(--accent-lt); }

/* ── Responsive ── */
@media (max-width: 1200px) {
    .courses-page { --sidebar-w: 300px; }
    .enrolled-grid { grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
}

@media (max-width: 900px) {
    .courses-page { --sidebar-w: 260px; }
    body.learner-dashboard .provider-main-content.courses-main-content {
        padding: 18px 18px 24px;
    }
    .enrolled-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
}
@media (max-width: 680px) {
    body.learner-dashboard .provider-main-content.courses-main-content {
        padding: 14px 14px 22px;
    }
    .portal-body {
        grid-template-columns: 1fr;
        min-height: auto;
    }
    .portal-sidebar { max-height: 240px; border-right: none; border-bottom: 1px solid var(--border); }
    .enrolled-grid  { grid-template-columns: 1fr; gap: 14px; }
    .enroll-body { padding: 14px; gap: 10px; }
    .enroll-footer { flex-direction: column; align-items: stretch; }
    .btn-continue { width: 100%; }
    .portal-header h1 { font-size: 1.25rem; }
    .btn-nav { padding: 9px 14px; font-size: 0.8rem; }
    .viewer-content { min-height: 440px; }
    .viewer-pdf { min-height: 440px; }
    .viewer-content.pdf-active { padding: 8px; }
}
</style>

<div class="courses-page">

<!-- ══ VIEW 1: MY LEARNING LIST ══ -->
<div id="cpViewList" class="cp-view active">
    <div class="ml-header">
        <h1>My Learning</h1>
        <p>Continue where you left off — all your enrolled courses in one place.</p>
    </div>
    <div class="enrolled-grid" id="enrolledGrid"></div>
</div>

<!-- ══ VIEW 2: COURSE PORTAL ══ -->
<div id="cpViewPortal" class="cp-view">
    <div class="portal-wrap">

        <div class="portal-header">
            <button class="portal-back" onclick="showCourseList()">&#8592; Back to My Learning</button>
            <h1 id="portalTitle">Course Title</h1>
            <p id="portalDesc">Course description.</p>
        </div>

        <div class="portal-body">

            <!-- Sidebar -->
            <div class="portal-sidebar" id="portalSidebar"></div>

            <!-- Viewer -->
            <div class="portal-viewer">
                <div class="viewer-content" id="viewerContent">

                    <div id="viewerEmptyState" class="viewer-empty-state">Select a lesson from the left panel to start learning.</div>

                    <video id="viewerVideo" class="viewer-video" controls>
                        <source src="" type="video/mp4">
                    </video>

                    <iframe id="viewerPdf" class="viewer-pdf" title="PDF Viewer"></iframe>

                    <div id="viewerQuiz" class="viewer-quiz">
                        <p class="quiz-title" id="quizTitle"></p>
                        <div id="quizBody"></div>
                        <button class="btn-quiz-sub" onclick="submitQuiz()">Submit Quiz</button>
                    </div>

                    <div id="viewerProject" class="viewer-project">
                        <p class="proj-title" id="projTitle"></p>
                        <p class="proj-desc"  id="projDesc"></p>
                        <div class="proj-submit-box">
                            <p>Upload your project files or paste a GitHub link below.</p>
                            <input class="proj-input" type="url" placeholder="https://github.com/yourrepo">
                            <button class="btn-quiz-sub">Submit Project</button>
                        </div>
                    </div>

                </div>

                <!-- Nav footer -->
                <div class="viewer-footer">
                    <button class="btn-nav prev" id="btnPrev" onclick="navigateLesson(-1)" disabled>&#9664; Previous</button>
                    <div class="viewer-label">
                        <strong id="curLessonName">Select a lesson</strong>
                        <span   id="curLessonMeta"></span>
                    </div>
                    <button class="btn-nav" id="btnNext" onclick="navigateLesson(1)">Next &#9654;</button>
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- .courses-page -->

<script>
(function(){
/* ══════════════════════════════════════════
   DATA
══════════════════════════════════════════ */
var BASE = '<?php echo BASE_URL; ?>';
var COURSES = <?php echo json_encode($portalCourses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

if (!Array.isArray(COURSES)) {
    COURSES = [];
}

// Backend-only source: no static fallback courses are rendered here.

/* ══ STATE ══ */
var currentCourse    = null;
var flatLessons      = [];
var currentIdx       = 0;

function esc(value){
    return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function normalizeProgress(value){
    var n = Number(value);
    if (!Number.isFinite(n)) return 0;
    return Math.max(0, Math.min(100, Math.round(n)));
}

function normalizeCourseId(value){
    return String(value == null ? '' : value);
}

function courseThumb(value){
    var src = String(value == null ? '' : value).trim();
    if (src) return src;
    return BASE + 'assets/images/cources/web-dev.jpg';
}

function setViewerEmptyState(show, message){
    var node = document.getElementById('viewerEmptyState');
    if (!node) return;
    if (message) node.textContent = message;
    if (show) {
        node.classList.remove('hidden');
    } else {
        node.classList.add('hidden');
    }
}

/* ══ RENDER ENROLLED GRID ══ */
function renderGrid(){
    var g = document.getElementById('enrolledGrid');
    g.innerHTML = '';

    if (!Array.isArray(COURSES) || COURSES.length === 0) {
        g.innerHTML =
            '<div class="enrolled-empty">' +
                '<h3>No enrolled courses yet</h3>' +
                '<p>Browse the catalog and enroll to start building your learning journey.</p>' +
            '</div>';
        return;
    }

    COURSES.forEach(function(c){
        var progress = normalizeProgress(c.progress);
        var statusClass = progress >= 100 ? 'complete' : 'active';
        var statusLabel = progress >= 100 ? 'Completed' : 'In progress';
        var card = document.createElement('div');
        card.className = 'enroll-card';
        card.setAttribute('role', 'button');
        card.setAttribute('tabindex', '0');
        card.setAttribute('aria-label', 'Open course ' + String(c.title || 'Untitled course'));
        card.innerHTML =
            '<div class="enroll-thumb" style="background-image:url(\''+esc(courseThumb(c.image))+'\')">' +
                '<div class="enroll-thumb-overlay"></div>' +
                '<span class="enroll-badge">'+esc(c.category || 'General')+'</span>' +
                '<span class="enroll-progress-chip">'+progress+'% complete</span>' +
            '</div>' +
            '<div class="enroll-body">' +
                '<h3 class="enroll-title">'+esc(c.title || 'Untitled course')+'</h3>' +
                '<p class="enroll-inst"><span class="inst-dot"></span>By '+esc(c.instructor || 'Instructor')+'</p>' +
                '<div class="pb-wrap">' +
                    '<div class="pb-label"><span>Learning Progress</span><strong>'+progress+'%</strong></div>' +
                    '<div class="pb-track"><div class="pb-fill" style="width:'+progress+'%"></div></div>' +
                '</div>' +
                '<div class="enroll-footer">' +
                    '<span class="enroll-status '+statusClass+'">'+statusLabel+'</span>' +
                    '<button class="btn-continue">&#9654; Continue Learning</button>' +
                '</div>' +
            '</div>';
        card.addEventListener('keydown', function(e){
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openPortal(c.id);
            }
        });
        card.querySelector('.btn-continue').addEventListener('click', function(e){
            e.stopPropagation(); openPortal(c.id);
        });
        card.addEventListener('click', function(){ openPortal(c.id); });
        g.appendChild(card);
    });
}

/* ══ OPEN PORTAL ══ */
function openPortal(id){
    currentCourse = null;
    var wantedId = normalizeCourseId(id);
    for(var i=0;i<COURSES.length;i++){
        if(normalizeCourseId(COURSES[i].id) === wantedId){
            currentCourse = COURSES[i];
            break;
        }
    }
    if(!currentCourse) return;

    document.getElementById('portalTitle').textContent = currentCourse.title || 'Untitled course';
    document.getElementById('portalDesc').textContent  = currentCourse.desc || 'Continue your enrolled learning path.';

    // Build flat lesson array
    flatLessons = [];
    var sections = Array.isArray(currentCourse.sections) ? currentCourse.sections : [];
    sections.forEach(function(s){
        var lessons = Array.isArray(s.lessons) ? s.lessons : [];
        lessons.forEach(function(l){ flatLessons.push(l); });
    });

    buildSidebar(sections);

    document.getElementById('cpViewList').classList.remove('active');
    document.getElementById('cpViewPortal').classList.add('active');

    if (flatLessons.length > 0) {
        loadLesson(0);
        setViewerEmptyState(false);
    } else {
        setViewerEmptyState(true, 'No lessons are available for this course yet.');
        document.getElementById('curLessonName').textContent = 'No lessons available';
        document.getElementById('curLessonMeta').textContent = '';
        document.getElementById('btnPrev').disabled = true;
        document.getElementById('btnNext').disabled = true;
        document.getElementById('viewerVideo').style.display='none';
        document.getElementById('viewerPdf').style.display='none';
        document.getElementById('viewerQuiz').style.display='none';
        document.getElementById('viewerProject').style.display='none';
        document.getElementById('viewerContent').classList.remove('pdf-active');
        document.getElementById('viewerContent').style.background='#f8fbfd';
    }
}

/* ══ BUILD SIDEBAR ══ */
function buildSidebar(sections){
    var sb = document.getElementById('portalSidebar');
    sb.innerHTML = '';
    var li = 0;

    if (!Array.isArray(sections) || sections.length === 0) {
        sb.innerHTML = '<div class="sidebar-empty">Course modules are being prepared and will appear here soon.</div>';
        return;
    }

    sections.forEach(function(sec, si){
        var secEl = document.createElement('div');
        secEl.className = 'sidebar-section';

        var hdr = document.createElement('div');
        hdr.className = 'sidebar-sec-hdr';
        hdr.innerHTML =
            '<div class="sidebar-sec-title"><strong>Section '+(si+1)+':</strong> '+sec.title+'</div>' +
            '<span class="sidebar-sec-dur">· '+sec.dur+'</span>' +
            '<span class="sidebar-sec-chevron">&#9660;</span>';
        hdr.addEventListener('click', function(){ secEl.classList.toggle('collapsed'); });

        var ul = document.createElement('ul');
        ul.className = 'sidebar-lessons';

        sec.lessons.forEach(function(les){
            var idx = li++;
            var icon='&#9654;', iClass='l-icon';
            if(les.type==='quiz')    { icon='&#128221;'; iClass='l-icon quiz-i'; }
            if(les.type==='pdf')     { icon='&#128196;'; }
            if(les.type==='project') { icon='&#9881;';   iClass='l-icon proj-i'; }

            var item = document.createElement('li');
            item.className = 'sidebar-lesson';
            item.setAttribute('data-idx', idx);
            item.innerHTML =
                '<span class="'+iClass+'">'+icon+'</span>' +
                '<span class="l-name">'+les.name+'</span>' +
                (les.dur ? '<span class="l-dur">'+les.dur+'</span>' : '');
            item.addEventListener('click', function(){ loadLesson(idx); });
            ul.appendChild(item);
        });

        secEl.appendChild(hdr);
        secEl.appendChild(ul);
        sb.appendChild(secEl);
    });
}

/* ══ LOAD LESSON ══ */
function loadLesson(idx){
    if(idx<0||idx>=flatLessons.length) return;
    currentIdx = idx;
    var les = flatLessons[idx];
    setViewerEmptyState(false);

    // Highlight sidebar
    document.querySelectorAll('.sidebar-lesson').forEach(function(el){ el.classList.remove('active'); });
    var active = document.querySelector('.sidebar-lesson[data-idx="'+idx+'"]');
    if(active){ active.classList.add('active'); active.scrollIntoView({block:'nearest',behavior:'smooth'}); }

    // Footer label
    document.getElementById('curLessonName').textContent = les.name;
    document.getElementById('curLessonMeta').textContent = les.dur || les.type.toUpperCase();

    // Nav buttons
    document.getElementById('btnPrev').disabled = (idx===0);
    document.getElementById('btnNext').disabled = (idx===flatLessons.length-1);

    // Hide all panels
    var vid=document.getElementById('viewerVideo'),
        pdf=document.getElementById('viewerPdf'),
        qz =document.getElementById('viewerQuiz'),
        pj =document.getElementById('viewerProject'),
        vc =document.getElementById('viewerContent');

    vid.style.display='none'; pdf.style.display='none';
    qz.style.display='none';  pj.style.display='none';
    vc.style.background='#000';
    vc.classList.remove('pdf-active');

    if(les.type==='video'){
        vid.style.display='block';
        vid.src=les.src; vid.load();
    } else if(les.type==='pdf'){
        pdf.style.display='block';
        var pdfSrc = String(les.src || '');
        if (pdfSrc !== '') {
            pdfSrc += (pdfSrc.indexOf('#') >= 0 ? '&' : '#') + 'zoom=page-width&view=FitH';
        }
        pdf.src=pdfSrc;
        vc.style.background='#ecf2f7';
        vc.classList.add('pdf-active');
    } else if(les.type==='quiz'){
        qz.style.display='block';
        vc.style.background='#fff';
        document.getElementById('quizTitle').textContent=les.quiz.title;
        var body=document.getElementById('quizBody'); body.innerHTML='';
        les.quiz.qs.forEach(function(q,qi){
            var div=document.createElement('div'); div.className='quiz-q';
            div.innerHTML='<div class="quiz-q-text">Q'+(qi+1)+'. '+q.q+'</div>' +
                '<div class="quiz-opts">'+
                q.opts.map(function(o,oi){
                    return '<label class="quiz-opt"><input type="radio" name="q'+qi+'" value="'+oi+'"> '+o+'</label>';
                }).join('')+'</div>';
            body.appendChild(div);
        });
    } else if(les.type==='project'){
        pj.style.display='block';
        vc.style.background='#fff';
        document.getElementById('projTitle').textContent=les.proj.title;
        document.getElementById('projDesc').textContent=les.proj.desc;
    }
}

/* ══ NAV ══ */
window.navigateLesson = function(dir){ loadLesson(currentIdx+dir); };
window.submitQuiz     = function(){ alert('Quiz submitted! Great work \uD83C\uDF89'); };
window.showCourseList = function(){
    document.getElementById('cpViewPortal').classList.remove('active');
    document.getElementById('cpViewList').classList.add('active');
};
window.openPortal = openPortal;

/* ══ INIT ══ */
renderGrid();

if (typeof URLSearchParams === 'function') {
    var params = new URLSearchParams(window.location.search || '');
    var preselectedCourseId = params.get('course_id');
    if (preselectedCourseId) {
        openPortal(preselectedCourseId);
    }
}

})();
</script>

</main>
