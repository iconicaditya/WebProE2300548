<?php
/**
 * Learner - My Courses / Course Portal
 * Fully self-contained: all CSS + JS inside this file only.
 * No dependency on sandhya.css or any external stylesheet for portal layout.
 */
?>

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
    --sidebar-w:  340px;
    font-family: 'Inter', system-ui, sans-serif;
}

/* ── view toggle ── */
.cp-view          { display: none; }
.cp-view.active   { display: block; }

/* ══ MY LEARNING LIST ══ */
.ml-header        { margin-bottom: 24px; }
.ml-header h1     { margin: 0 0 4px; font-size: 1.75rem; font-weight: 800; color: var(--text); }
.ml-header p      { margin: 0; color: var(--muted); font-size: 0.93rem; }

.enrolled-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.enroll-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(15,23,42,0.06);
    cursor: pointer;
    transition: transform .22s, box-shadow .22s;
}
.enroll-card:hover { transform: translateY(-5px); box-shadow: 0 14px 32px rgba(15,23,42,0.13); }
.enroll-thumb {
    aspect-ratio: 16/9;
    background-size: cover;
    background-position: center;
    position: relative;
}
.enroll-thumb-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.5) 100%);
}
.enroll-badge {
    position: absolute; top: 10px; left: 10px;
    background: var(--accent); color: #fff;
    font-size: 0.7rem; font-weight: 700;
    padding: 3px 10px; border-radius: 20px;
}
.enroll-body     { padding: 14px 16px 16px; }
.enroll-title    { margin: 0 0 3px; font-size: 0.97rem; font-weight: 700; color: var(--text); line-height: 1.35; }
.enroll-inst     { margin: 0 0 10px; font-size: 0.8rem; color: var(--muted); }
.pb-wrap         { margin-bottom: 12px; }
.pb-label        { display: flex; justify-content: space-between; font-size: 0.73rem; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
.pb-track        { height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.pb-fill         { height: 100%; background: linear-gradient(90deg, var(--success), #14b8a6); border-radius: 99px; }
.btn-continue {
    width: 100%; background: var(--accent); color: #fff;
    border: none; border-radius: 8px; padding: 9px 0;
    font-size: 0.88rem; font-weight: 700; cursor: pointer;
    transition: background .15s;
}
.btn-continue:hover { background: var(--accent-dk); }

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
    /* Stretch to full width regardless of parent padding */
    width: calc(100% + 80px);    /* cancel 40px padding on each side */
    margin-left: -40px;
    margin-right: -40px;
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
    flex: 1; background: #000;
    min-height: 500px;
    display: flex; position: relative;
    width: 100%; overflow: hidden;
}
.viewer-video {
    width: 100%; min-height: 500px;
    display: block; background: #000; object-fit: contain;
}
.viewer-pdf {
    width: 100%; height: 500px;
    border: none; background: #fff;
    display: none;
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
@media (max-width: 900px) {
    .courses-page { --sidebar-w: 260px; }
    .portal-body { width: calc(100% + 48px); margin-left: -24px; margin-right: -24px; }
    .enrolled-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 680px) {
    .portal-body {
        grid-template-columns: 1fr;
        width: calc(100% + 32px);
        margin-left: -16px; margin-right: -16px;
    }
    .portal-sidebar { max-height: 240px; border-right: none; border-bottom: 1px solid var(--border); }
    .enrolled-grid  { grid-template-columns: 1fr; }
    .portal-header h1 { font-size: 1.25rem; }
    .btn-nav { padding: 9px 14px; font-size: 0.8rem; }
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
var COURSES = [
    {
        id:'c1',
        title:'React.js & Modern Frontend Development',
        instructor:'Saurav Pandey', category:'Programming', progress:42,
        image: BASE+'assets/images/cources/react-frontend.jpg',
        desc:'Master React hooks, state management, routing, and deployment to build real-world single-page applications with industry best practices.',
        sections:[
            { title:'React Fundamentals & JSX', dur:'2h 30m', lessons:[
                { name:'1. Introduction to React',                    type:'video',   dur:'15:30', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'2. Components and Props',                     type:'video',   dur:'22:45', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'3. JSX Deep Dive',                            type:'video',   dur:'18:20', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'4. Rendering Lists and Conditional Rendering',type:'video',   dur:'25:10', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'5. Quiz: React Basics',                       type:'quiz',    dur:'',      quiz:{ title:'Quiz: React Basics', qs:[
                    { q:'What does JSX stand for?',               opts:['JavaScript XML','JavaScript Extension','Java Syntax Extension','None of the above'] },
                    { q:'Which hook manages local state in React?',opts:['useEffect','useState','useRef','useContext'] }
                ]}},
                { name:'6. Project: Build a Todo App',                type:'project', dur:'1h 15m',proj:{ title:'Build a Todo App', desc:'Build a fully functional Todo App using React with add, complete, and delete features. Style it with CSS modules.' }}
            ]},
            { title:'React Hooks & State Management', dur:'3h 45m', lessons:[
                { name:'1. useState Hook',                type:'video',   dur:'20:15', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'2. useEffect Hook',               type:'video',   dur:'28:40', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'3. useContext for Global State',  type:'video',   dur:'25:30', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'4. Custom Hooks',                 type:'video',   dur:'22:50', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'5. Quiz: React Hooks',            type:'quiz',    dur:'',      quiz:{ title:'Quiz: React Hooks', qs:[
                    { q:'What is the purpose of useEffect?',     opts:['State management','Side effects','Routing','Styling'] },
                    { q:'Custom hooks must start with?',          opts:['use','hook','custom','fn'] }
                ]}},
                { name:'6. Project: Weather App with Hooks', type:'project', dur:'1h 30m', proj:{ title:'Weather App with Hooks', desc:'Build a weather app using useEffect to fetch live weather data from an API and display it dynamically.' }}
            ]},
            { title:'React Router & Navigation', dur:'2h 10m', lessons:[
                { name:'1. Setting up React Router', type:'video', dur:'18:00', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'2. Dynamic Routes & Params', type:'video', dur:'24:15', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'3. Reading: Router Docs',    type:'pdf',   dur:'',      src:'https://www.w3.org/WAI/WCAG21/Techniques/pdf/PDF1' },
                { name:'4. Quiz: Routing',           type:'quiz',  dur:'',      quiz:{ title:'Quiz: React Router', qs:[
                    { q:'Which component wraps your app for routing?', opts:['Route','BrowserRouter','Link','Switch'] }
                ]}}
            ]}
        ]
    },
    {
        id:'c2',
        title:'Python Data Science Masterclass',
        instructor:'Priya Dhakal', category:'Data Science', progress:68,
        image: BASE+'assets/images/cources/python.jpg',
        desc:'Learn NumPy, Pandas, Matplotlib and scikit-learn to analyse real-world datasets and build machine-learning models from scratch.',
        sections:[
            { title:'Python & NumPy Fundamentals', dur:'3h', lessons:[
                { name:'1. Python Recap',           type:'video',   dur:'20:00', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'2. NumPy Arrays',           type:'video',   dur:'30:00', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'3. Quiz: NumPy Basics',     type:'quiz',    dur:'',      quiz:{ title:'Quiz: NumPy', qs:[
                    { q:'Which function creates a NumPy array?', opts:['np.array()','np.list()','array.new()','np.create()'] }
                ]}},
                { name:'4. Project: Data Cleaning', type:'project', dur:'1h',    proj:{ title:'Data Cleaning Project', desc:'Use Pandas to clean a messy CSV dataset — handle nulls, duplicates, and type mismatches.' }}
            ]}
        ]
    },
    {
        id:'c3',
        title:'UI/UX Design for Digital Products',
        instructor:'Karan Basnet', category:'Design', progress:85,
        image: BASE+'assets/images/cources/ui-ux.jpg',
        desc:'Design user-centred interfaces with strong visual hierarchy, accessibility standards, and a complete design-system foundation.',
        sections:[
            { title:'Design Thinking & Research', dur:'2h', lessons:[
                { name:'1. UX Research Methods',   type:'video', dur:'22:00', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'2. User Personas',          type:'video', dur:'18:30', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'3. Design System Docs',     type:'pdf',   dur:'',      src:'https://www.w3.org/WAI/WCAG21/Techniques/pdf/PDF1' },
                { name:'4. Quiz: Design Thinking',  type:'quiz',  dur:'',      quiz:{ title:'Quiz: Design Thinking', qs:[
                    { q:'What is a user persona?', opts:['A fictional user profile','A real user','A logo design','A wireframe'] }
                ]}}
            ]}
        ]
    },
    {
        id:'c4',
        title:'Full-Stack Web Development Bootcamp',
        instructor:'Aaditya Sharma', category:'Programming', progress:25,
        image: BASE+'assets/images/cources/web-dev.jpg',
        desc:'Build responsive websites and web apps from scratch using HTML, CSS, JavaScript, PHP, and MySQL.',
        sections:[
            { title:'HTML & CSS Foundations', dur:'2h', lessons:[
                { name:'1. HTML5 Semantic Structure', type:'video', dur:'20:00', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'2. CSS Flexbox & Grid',        type:'video', dur:'28:00', src:'https://www.w3schools.com/html/mov_bbb.mp4' },
                { name:'3. Reading: CSS Reference',    type:'pdf',   dur:'',      src:'https://www.w3.org/WAI/WCAG21/Techniques/pdf/PDF1' },
                { name:'4. Quiz: HTML & CSS',          type:'quiz',  dur:'',      quiz:{ title:'Quiz: HTML & CSS', qs:[
                    { q:'Which tag creates a hyperlink?',    opts:['<link>','<a>','<href>','<url>'] },
                    { q:'What does CSS stand for?',          opts:['Computer Style Sheets','Creative Style Sheets','Cascading Style Sheets','Colorful Style Sheets'] }
                ]}},
                { name:'5. Project: Portfolio Page', type:'project', dur:'1h 30m', proj:{ title:'Build a Portfolio Page', desc:'Create a fully responsive personal portfolio page using HTML5 and CSS Grid/Flexbox.' }}
            ]}
        ]
    }
];

/* ══ STATE ══ */
var currentCourse    = null;
var flatLessons      = [];
var currentIdx       = 0;

/* ══ RENDER ENROLLED GRID ══ */
function renderGrid(){
    var g = document.getElementById('enrolledGrid');
    g.innerHTML = '';
    COURSES.forEach(function(c){
        var card = document.createElement('div');
        card.className = 'enroll-card';
        card.innerHTML =
            '<div class="enroll-thumb" style="background-image:url(\''+c.image+'\')">' +
                '<div class="enroll-thumb-overlay"></div>' +
                '<span class="enroll-badge">'+c.category+'</span>' +
            '</div>' +
            '<div class="enroll-body">' +
                '<h3 class="enroll-title">'+c.title+'</h3>' +
                '<p class="enroll-inst">By '+c.instructor+'</p>' +
                '<div class="pb-wrap">' +
                    '<div class="pb-label"><span>Progress</span><span>'+c.progress+'%</span></div>' +
                    '<div class="pb-track"><div class="pb-fill" style="width:'+c.progress+'%"></div></div>' +
                '</div>' +
                '<button class="btn-continue">&#9654; Continue Learning</button>' +
            '</div>';
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
    for(var i=0;i<COURSES.length;i++){ if(COURSES[i].id===id){ currentCourse=COURSES[i]; break; } }
    if(!currentCourse) return;

    document.getElementById('portalTitle').textContent = currentCourse.title;
    document.getElementById('portalDesc').textContent  = currentCourse.desc;

    // Build flat lesson array
    flatLessons = [];
    currentCourse.sections.forEach(function(s){ s.lessons.forEach(function(l){ flatLessons.push(l); }); });

    buildSidebar();

    document.getElementById('cpViewList').classList.remove('active');
    document.getElementById('cpViewPortal').classList.add('active');

    loadLesson(0);
}

/* ══ BUILD SIDEBAR ══ */
function buildSidebar(){
    var sb = document.getElementById('portalSidebar');
    sb.innerHTML = '';
    var li = 0;
    currentCourse.sections.forEach(function(sec, si){
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

    if(les.type==='video'){
        vid.style.display='block';
        vid.src=les.src; vid.load();
    } else if(les.type==='pdf'){
        pdf.style.display='block';
        pdf.src=les.src;
        vc.style.background='#fff';
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

})();
</script>