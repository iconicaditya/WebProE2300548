<!-- Frontend-only enrolled courses (no PHP session) -->

<style>
/* Enrolled courses styles (local to learner/pages/courses.php) */
.enrolled-courses-section .courses-grid,
.courses-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 12px;
}

.course-card {
    background: #fff;
    border: 1px solid #dce4ec;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15,23,42,0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
    display: grid;
    grid-template-rows: auto 1fr;
}

.course-card:hover { transform: translateY(-6px); box-shadow: 0 18px 34px rgba(15,23,42,0.12); }

.course-thumb {
    aspect-ratio: 16 / 9;
    background-size: cover;
    background-position: center;
}

.course-card-content { padding: 12px; display: grid; gap: 8px; }
.course-meta-top { display:flex; justify-content:space-between; gap:8px; align-items:center; }
.course-category { font-size:11px; font-weight:700; color:#0d6e84; background:rgba(13,110,132,0.08); padding:4px 8px; border-radius:999px; }
.course-level { font-size:12px; color:#415166; }
.course-title { margin:0; font-size:16px; color:#132033; }
.course-instructor { margin:0; font-size:13px; color:#5a6b80; }
.course-rating { display:flex; gap:6px; align-items:center; font-size:12px; }
.course-meta-bottom { display:flex; justify-content:space-between; align-items:center; gap:8px; }
.course-duration { font-size:13px; color:#506278; }
.course-progress-circle { display:inline-block; vertical-align:middle; margin-left:8px; }
.continue-btn {
    margin-top: 10px;
    width: 100%;
    background: #0f766e;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 8px 0;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.continue-btn:hover {
    background: #0d6e84;
}

/* Responsive */
@media (max-width: 992px) { .enrolled-courses-section .courses-grid, .courses-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .enrolled-courses-section .courses-grid, .courses-grid { grid-template-columns: 1fr; } }

/* Section title */
.enrolled-courses-section .section-title { margin: 0 0 8px; font-size: 20px; color: #123; }

</style>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">My Learning</h1>
        <p class="dashboard-subtitle">Manage and continue your enrolled courses.</p>
    </div>

    <section class="dashboard-section enrolled-courses-section">
        <h2 class="section-title">Enrolled Courses</h2>
        <div class="courses-grid" id="enrolledCoursesGrid"></div>
    </section>
</main>

<script>
// Small JS enhancements for course cards (keyboard + click focus)
document.addEventListener('DOMContentLoaded', function(){
    const grid = document.getElementById('enrolledCoursesGrid');
    if (!grid) return;
    const sampleCourses = [
        {
            title: 'JavaScript for Beginners', category: 'Programming', level: 'Beginner', instructor: 'John Doe', rating: '4.8', students: '1,200', duration: '6 Weeks', price: '$99', image: '../assets/images/cources/java.jpg'
        },
        {
            title: 'Figma UI Design', category: 'Design', level: 'Intermediate', instructor: 'Jane Smith', rating: '4.7', students: '950', duration: '4 Weeks', price: '$79', image: '../assets/images/cources/ui-ux.jpg'
        },
        {
            title: 'Python Data Science', category: 'Programming', level: 'Advanced', instructor: 'Alex Lee', rating: '4.9', students: '2,100', duration: '8 Weeks', price: '$129', image: '../assets/images/cources/python.jpg'
        },
        {
            title: 'Digital Marketing Basics', category: 'Marketing', level: 'Beginner', instructor: 'Sara Khan', rating: '4.6', students: '800', duration: '5 Weeks', price: '$89', image: '../assets/images/cources/digital-marketing.jpg'
        },
        {
            title: 'React Web Development', category: 'Programming', level: 'Intermediate', instructor: 'Mike Brown', rating: '4.8', students: '1,500', duration: '7 Weeks', price: '$119', image: '../assets/images/cources/react-frontend.jpg'
        },
        {
            title: 'Business Analytics', category: 'Business', level: 'Advanced', instructor: 'Priya Patel', rating: '4.7', students: '1,300', duration: '6 Weeks', price: '$109', image: '../assets/images/cources/data-analytics.jpg'
        }
    ];
    grid.innerHTML = '';
    sampleCourses.forEach(course => {
        const card = document.createElement('article');
        card.className = 'course-card';
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        // Random progress for demo
        const progress = Math.floor(Math.random() * 61) + 40; // 40-100%
        card.innerHTML = `
            <div class="course-thumb" style="background-image:url('${course.image}')"></div>
            <div class="course-card-content">
                <div class="course-meta-top">
                    <span class="course-category">${course.category}</span>
                    <span class="course-level">${course.level}</span>
                </div>
                <h3 class="course-title">${course.title}</h3>
                <p class="course-instructor">By ${course.instructor}</p>
                <div class="course-rating">
                    <span class="stars">&#9733;</span>
                    <span class="rating-num">${course.rating}</span>
                    <span class="rating-students">(${course.students} students)</span>
                </div>
                <div class="course-meta-bottom">
                    <span class="course-duration"><i class="bi bi-clock"></i> ${course.duration}</span>
                    <span class="course-progress-circle">
                        <svg width="44" height="44" viewBox="0 0 44 44">
                            <circle cx="22" cy="22" r="18" fill="none" stroke="#e5e7eb" stroke-width="6" />
                            <circle cx="22" cy="22" r="18" fill="none" stroke="#0f766e" stroke-width="6" stroke-dasharray="113" stroke-dashoffset="${113 - (progress / 100) * 113}" style="transition:stroke-dashoffset 0.3s" />
                            <text x="22" y="23.5" text-anchor="middle" font-size="12" fill="#0f766e" font-weight="bold" alignment-baseline="middle" style="dominant-baseline:central;">${progress}%</text>
                        </svg>
                    </span>
                </div>
                <button class="continue-btn">Continue</button>
            </div>
        `;
        card.addEventListener('keypress', function(e){
            if (e.key === 'Enter' || e.key === ' ') {
                card.click();
            }
        });
        grid.appendChild(card);
    });
});
</script>
