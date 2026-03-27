<?php
/**
 * Frontend-only Courses Catalog Page
 * Intentional filename: cources.php (as requested)
 */
$isEmbeddedCoursesSection = isset($embedCoursesSection) && $embedCoursesSection === true;
$useExternalAllCoursesAssets = isset($useExternalAllCoursesAssets) && $useExternalAllCoursesAssets === true;
$renderCoursesOnly = isset($renderCoursesOnly) && $renderCoursesOnly === true;

// Fetch courses from backend if not already set
if (!isset($coursesData)) {
    if (!defined('BASE_URL')) {
        require_once(__DIR__ . '/../config/config.php');
    }
    if (!isset($conn) || !$conn) {
        require_once(__DIR__ . '/../config/db.php');
    }
    require_once(__DIR__ . '/../learner/includes/learner_data.php');
    
    // Initialize with empty array as fallback
    $coursesData = [];
    
    // Only fetch from database if $conn is available
    if (isset($conn) && $conn) {
        $allCourses = ems_learner_fetch_all_published_courses($conn, 1000, 0);
        if (!empty($allCourses)) {
            foreach ($allCourses as $course) {
                $coursesData[] = [
                    'id' => (int)$course['id'],
                    'image' => $course['thumbnail_url'],
                    'title' => $course['title'],
                    'category' => $course['level'],
                    'level' => ucfirst($course['level']),
                    'duration' => $course['duration_label'] ?? 'Self-paced',
                    'price' => ems_learner_currency_format($course['price_amount'], $course['currency_code']),
                    'instructor' => $course['instructor_name'],
                    'rating' => $course['avg_rating'],
                    'students' => number_format($course['student_count_estimate']),
                    'overview' => $course['short_description'],
                ];
            }
        }
    }
} else {
    $allCourses = [];
    foreach ($coursesData as $data) {
        $allCourses[] = $data;
    }
}
?>

<main class="courses-catalog-page<?php echo $isEmbeddedCoursesSection ? ' courses-catalog-embedded' : ''; ?>">
    <?php if (!isset($hideCoursesHero) || !$hideCoursesHero): ?>
    <section class="catalog-hero">
        <div class="catalog-hero-inner">
            <div class="catalog-copy">
                <p class="catalog-kicker">CURATED LEARNING PATHS</p>
                <h1>Choose The Right Course For Your Next Career Step</h1>
                <p>Discover practical, job-ready courses designed by industry mentors. Compare level, duration, outcomes, and tools before you enroll.</p>
                <div class="catalog-metrics">
                    <div>
                        <strong>120+</strong>
                        <span>Live Courses</span>
                    </div>
                    <div>
                        <strong>42K+</strong>
                        <span>Learners</span>
                    </div>
                    <div>
                        <strong>94%</strong>
                        <span>Completion Rate</span>
                    </div>
                </div>
            </div>
            <div class="catalog-hero-card">
                <h3>Learning Planner</h3>
                <p>Pick a category and see detailed curriculum, mentor profile, and expected outcomes instantly.</p>
                <ul>
                    <li><i class="bi bi-check2-circle"></i> Beginner to Advanced tracks</li>
                    <li><i class="bi bi-check2-circle"></i> Real-world capstone projects</li>
                    <li><i class="bi bi-check2-circle"></i> Certificate + portfolio support</li>
                </ul>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="catalog-layout">
        <aside class="catalog-filter-box">
            <h2>Filter Courses</h2>
            <div class="filter-group">
                <label for="searchCourse">Search</label>
                <input id="searchCourse" type="text" placeholder="Title or skill...">
            </div>

            <div class="filter-group">
                <label>Category</label>
                <div class="chip-row" id="categoryFilters">
                    <button class="filter-chip active" data-filter="all" type="button">All</button>
                    <button class="filter-chip" data-filter="Programming" type="button">Programming</button>
                    <button class="filter-chip" data-filter="Data Science" type="button">Data Science</button>
                    <button class="filter-chip" data-filter="Design" type="button">Design</button>
                    <button class="filter-chip" data-filter="Business" type="button">Business</button>
                    <button class="filter-chip" data-filter="Cybersecurity" type="button">Cybersecurity</button>
                    <button class="filter-chip" data-filter="Cloud & DevOps" type="button">Cloud &amp; DevOps</button>
                </div>
            </div>

            <div class="filter-group">
                <label for="levelFilter">Level</label>
                <select id="levelFilter">
                    <option value="all">All Levels</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>


            <div class="filter-group">
                <label for="instructorFilter">Instructor</label>
                <div class="instructor-dropdown" id="instructorDropdown">
                    <button type="button" class="instructor-toggle" id="instructorToggle" aria-expanded="false">
                        <span id="instructorSelectedLabel">All Instructors</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="instructor-menu" id="instructorMenu" hidden>
                        <div class="instructor-search-wrap">
                            <i class="bi bi-search"></i>
                            <input id="instructorSearch" type="text" placeholder="Search instructor..." autocomplete="off">
                        </div>
                        <div class="instructor-options" id="instructorOptions"></div>
                    </div>
                </div>
            </div>

            <div class="filter-group">
                <label for="priceRangeFilter">Budget Range</label>
                <select id="priceRangeFilter">
                    <option value="all">All Budgets</option>
                    <option value="0-0">Free</option>
                    <option value="0-75">Under $75</option>
                    <option value="75-110">$75 - $110</option>
                    <option value="110-140">$110 - $140</option>
                    <option value="140-170">$140 - $170</option>
                    <option value="170-9999">$170 and Above</option>
                </select>
            </div>

            <button id="resetFilters" class="reset-btn" type="button">Reset Filters</button>
        </aside>

        <div class="catalog-main">
            <div class="catalog-headline-row">
                <div class="catalog-headline-copy">
                    <h2>Top Cources</h2>
                    <p>Discover hand-picked, career-ready courses by category, level, instructor, and budget in one place.</p>
                </div>
                <span id="courseCount">0 courses</span>
            </div>

            <div class="courses-grid" id="coursesGrid"></div>

            <?php if ($isEmbeddedCoursesSection): ?>
            <div class="view-all-wrap">
                <a href="<?php echo BASE_URL; ?>pages/allcources.php" class="view-all-btn">
                    <span>View All Cources</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php if (!$useExternalAllCoursesAssets): ?>
<style>
    .courses-catalog-page {
        --catalog-primary: #0d6e84;
        --catalog-secondary: #124e66;
        --catalog-accent: #f2b134;
        --catalog-surface: #f5f8fa;
        --catalog-ink: #1d2939;
        --catalog-gutter: clamp(14px, 2.2vw, 28px);
        background:
            radial-gradient(circle at 10% 5%, rgba(13, 110, 132, 0.12), transparent 38%),
            radial-gradient(circle at 95% 20%, rgba(242, 177, 52, 0.18), transparent 36%),
            #eef3f7;
        padding-bottom: 16px;
        min-height: auto;
    }

    main.courses-catalog-page {
        min-height: 0 !important;
        padding-top: 12px !important;
        padding-bottom: 8px !important;
    }

    .catalog-hero {
        padding: clamp(20px, 4vw, 48px) clamp(12px, 2vw, 20px) 20px;
    }

    .catalog-hero-inner {
        width: min(1320px, calc(100% - (var(--catalog-gutter) * 2)));
        margin: 0 auto;
        box-sizing: border-box;
        display: grid;
        grid-template-columns: 1.3fr 0.9fr;
        gap: 20px;
    }

    .catalog-copy,
    .catalog-hero-card {
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid rgba(13, 110, 132, 0.12); 
        backdrop-filter: blur(4px);
        border-radius: 18px;
        box-shadow: 0 14px 30px rgba(16, 24, 40, 0.08);
    }

    .catalog-copy {
        padding: clamp(22px, 3vw, 34px);
    }

    .catalog-kicker {
        margin: 0 0 8px;
        color: var(--catalog-primary);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.14em;
    }

    .catalog-copy h1 {
        margin: 0;
        color: var(--catalog-ink);
        font-size: clamp(24px, 3vw, 38px);
        line-height: 1.18;
    }

    .catalog-copy > p {
        margin: 14px 0 0;
        font-size: 15px;
        color: #465467;
        max-width: 65ch;
    }

    .catalog-metrics {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .catalog-metrics div {
        border-radius: 12px;
        background: linear-gradient(140deg, rgba(13, 110, 132, 0.11), rgba(255, 255, 255, 0.8));
        border: 1px solid rgba(13, 110, 132, 0.2);
        padding: 10px;
        display: grid;
        gap: 2px;
    }

    .catalog-metrics strong {
        color: var(--catalog-secondary);
        font-size: 18px;
    }

    .catalog-metrics span {
        color: #5d6b7c;
        font-size: 12px;
    }

    .catalog-hero-card {
        padding: clamp(18px, 2.2vw, 28px);
    }

    .catalog-hero-card h3 {
        margin: 0;
        font-size: 23px;
        color: var(--catalog-secondary);
    }

    .catalog-hero-card p {
        margin: 10px 0 14px;
        color: #4a5768;
    }

    .catalog-hero-card ul {
        padding: 0;
        margin: 0;
        list-style: none;
        display: grid;
        gap: 10px;
    }

    .catalog-hero-card li {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #314155;
        font-size: 14px;
    }

    .catalog-hero-card i {
        color: var(--catalog-primary);
    }

    .catalog-layout {
        width: min(1320px, calc(100% - (var(--catalog-gutter) * 2)));
        margin: 0 auto;
        padding: 0;
        box-sizing: border-box;
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 28px;
        align-items: start;
    }

    .catalog-filter-box {
        position: sticky;
        top: 122px;
        background: #fff;
        border-radius: 16px;
        border: 1px solid #dde6ee;
        box-shadow: 0 10px 26px rgba(17, 24, 39, 0.07);
        padding: 20px;
        display: grid;
        gap: 18px;
    }

    .catalog-filter-box h2 {
        margin: 0;
        font-size: 22px;
        color: var(--catalog-ink);
    }

    .filter-group {
        display: grid;
        gap: 8px;
    }

    .filter-group label {
        font-size: 14px;
        color: #526070;
        font-weight: 600;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        min-height: 46px;
        border-radius: 10px;
        border: 1px solid #ccd6e0;
        padding: 11px 14px;
        font-size: 15px;
        color: #1f2a37;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: var(--catalog-primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 132, 0.14);
        outline: none;
    }

    .chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-chip {
        border: 1px solid #c8d5e2;
        background: #fff;
        color: #344357;
        border-radius: 999px;
        padding: 9px 16px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .filter-chip.active {
        background: var(--catalog-primary);
        color: #fff;
        border-color: var(--catalog-primary);
    }

    .instructor-dropdown {
        position: relative;
    }

    .instructor-toggle {
        width: 100%;
        min-height: 46px;
        border: 1px solid #ccd6e0;
        border-radius: 10px;
        background: #fff;
        color: #1f2a37;
        padding: 11px 14px;
        font-size: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .instructor-toggle i {
        color: #72879d;
        font-size: 13px;
        transition: transform 0.2s ease;
    }

    .instructor-dropdown.open .instructor-toggle {
        border-color: var(--catalog-primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 132, 0.14);
    }

    .instructor-dropdown.open .instructor-toggle i {
        transform: rotate(180deg);
    }

    .instructor-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d3dde7;
        border-radius: 12px;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.16);
        padding: 10px;
        display: grid;
        gap: 8px;
        z-index: 30;
    }

    .instructor-menu[hidden] {
        display: none !important;
    }

    .instructor-search-wrap {
        position: relative;
    }

    .instructor-search-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #7a8ca2;
        font-size: 13px;
        pointer-events: none;
    }

    .instructor-search-wrap input {
        width: 100%;
        min-height: 38px;
        border-radius: 9px;
        border: 1px solid #d5dee8;
        padding: 8px 10px 8px 31px;
        font-size: 13px;
    }

    .instructor-search-wrap input:focus {
        border-color: var(--catalog-primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 132, 0.14);
        outline: none;
    }

    .instructor-options {
        max-height: 220px;
        overflow-y: auto;
        display: grid;
        gap: 4px;
        padding-right: 2px;
    }

    .instructor-option {
        border: none;
        background: #fff;
        border-radius: 8px;
        padding: 8px 10px;
        text-align: left;
        font-size: 13px;
        color: #334155;
        cursor: pointer;
    }

    .instructor-option:hover {
        background: #eff6fa;
    }

    .instructor-option.active {
        background: rgba(13, 110, 132, 0.12);
        color: #0f4f63;
        font-weight: 700;
    }

    .instructor-empty {
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 12px;
        color: #6b7d91;
        background: #f4f8fb;
    }

    .reset-btn {
        border: none;
        border-radius: 10px;
        min-height: 46px;
        padding: 0 16px;
        margin-top: 8px;
        background: #243447;
        color: #fff;
        font-weight: 600;
        font-size: 15px;
    }

    .catalog-main {
        display: grid;
        gap: 20px;
    }

    .catalog-headline-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        padding: 4px 4px 0;
    }

    .catalog-headline-copy {
        display: grid;
        gap: 4px;
    }

    .catalog-headline-row h2 {
        margin: 0;
        font-size: 25px;
        color: #1f2937;
    }

    .catalog-headline-row p {
        margin: 0;
        color: #607286;
        font-size: 13px;
        line-height: 1.45;
        max-width: 62ch;
    }

    .catalog-headline-row span {
        color: #4d5d70;
        font-weight: 600;
        font-size: 14px;
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-top: 10px;
    }

    .course-card {
        background: #fff;
        border: 1px solid #dce4ec;
        border-radius: 0;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.07);
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.3s ease;
        cursor: pointer;
        display: grid;
        grid-template-rows: auto 1fr;
        height: 100%;
        position: relative;
        isolation: isolate;
    }

    .course-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border: 1px solid rgba(13, 110, 132, 0.22);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 2;
    }

    .course-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.16);
        border-color: rgba(13, 110, 132, 0.35);
    }

    .course-card:hover::before {
        opacity: 1;
    }

    .course-card.active {
        border-color: var(--catalog-primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 132, 0.14);
    }

    .course-thumb {
        aspect-ratio: 16 / 9;
        overflow: hidden;
        position: relative;
        background-color: #0d4a5a;
        background-size: 108%;
        background-position: center;
        background-repeat: no-repeat;
        transition: background-size 0.45s cubic-bezier(0.22, 1, 0.36, 1), filter 0.35s ease;
    }
    .course-thumb::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.45) 100%);
        pointer-events: none;
        transition: opacity 0.35s ease;
    }

    .course-card:hover .course-thumb {
        background-size: 116%;
        filter: brightness(1.05) saturate(1.06);
    }

    .course-card:hover .course-thumb::after {
        opacity: 0.82;
    }

    .course-card-content {
        padding: 12px;
        display: grid;
        gap: 8px;
    }

    .course-meta-top {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        align-items: center;
    }

    .course-category {
        font-size: 11px;
        font-weight: 700;
        color: var(--catalog-secondary);
        background: rgba(13, 110, 132, 0.1);
        border-radius: 999px;
        padding: 4px 8px;
    }

    .course-level {
        font-size: 12px;
        color: #415166;
    }

    .course-title {
        margin: 0;
        font-size: 17px;
        line-height: 1.3;
        color: #132033;
    }

    .course-instructor {
        margin: 0;
        font-size: 13px;
        color: #5a6b80;
    }

    .course-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }
    .course-rating .stars {
        color: #f59e0b;
        font-size: 13px;
        letter-spacing: 1px;
    }
    .course-rating .rating-num {
        font-weight: 700;
        color: #374151;
        font-size: 13px;
    }
    .course-rating .rating-students {
        color: #6b7280;
        font-size: 11px;
    }

    .course-meta-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .course-duration {
        font-size: 13px;
        color: #506278;
    }

    .course-price {
        font-size: 16px;
        color: #0f766e;
        font-weight: 700;
    }

    .course-details {
        margin-top: 4px;
        background: #fff;
        border: 1px solid #dbe4ec;
        border-radius: 16px;
        padding: clamp(16px, 2vw, 24px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
    }

    .course-details h2 {
        margin: 0 0 6px;
        font-size: 24px;
        color: #1b2a3d;
    }

    .course-details > p {
        margin: 0;
        color: #53657a;
    }

    .detail-grid {
        margin-top: 14px;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 16px;
    }

    .detail-panel {
        border: 1px solid #e4ebf2;
        background: #fcfdff;
        border-radius: 12px;
        padding: 12px;
    }

    .detail-panel h3 {
        margin: 0 0 8px;
        font-size: 16px;
        color: #1f2f44;
    }

    .detail-panel ul {
        margin: 0;
        padding-left: 18px;
        color: #4f6074;
        display: grid;
        gap: 6px;
    }

    .detail-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .detail-badges span {
        background: rgba(18, 78, 102, 0.1);
        color: #18465a;
        font-size: 12px;
        border-radius: 999px;
        padding: 5px 10px;
        font-weight: 600;
    }

    .enroll-box {
        border-radius: 12px;
        border: 1px solid #d4e0ea;
        background: linear-gradient(160deg, #f8fcff, #eff6fb);
        padding: 12px;
        display: grid;
        gap: 8px;
    }

    .enroll-box strong {
        color: #134357;
        font-size: 20px;
    }

    .enroll-box button {
        border: none;
        border-radius: 10px;
        min-height: 42px;
        background: var(--catalog-primary);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
    }

    .empty-state {
        background: #fff;
        border: 1px dashed #bfd0de;
        border-radius: 14px;
        padding: 30px 18px;
        text-align: center;
        color: #546679;
    }

    @media (max-width: 1280px) {
        .catalog-layout {
            grid-template-columns: 260px minmax(0, 1fr);
            gap: 22px;
        }

        .courses-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1080px) {
        .catalog-hero-inner {
            grid-template-columns: 1fr;
        }

        .catalog-layout {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .catalog-filter-box {
            position: static;
            top: auto;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .courses-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .catalog-headline-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .catalog-headline-row span {
            font-size: 13px;
        }

        .courses-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .catalog-hero {
            padding: 16px 0 12px;
        }

        .catalog-hero-inner,
        .catalog-layout {
            width: calc(100% - 24px);
        }

        .catalog-copy,
        .catalog-hero-card {
            border-radius: 14px;
        }

        .catalog-copy {
            padding: 16px;
        }

        .catalog-copy h1 {
            font-size: clamp(24px, 6vw, 30px);
        }

        .catalog-copy > p,
        .catalog-hero-card p {
            font-size: 14px;
        }

        .catalog-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .catalog-hero-card {
            padding: 16px;
        }

        .catalog-headline-row h2 {
            font-size: 22px;
        }

        .course-card-content {
            padding: 10px;
        }

        .course-title {
            font-size: 15px;
        }
    }

    @media (max-width: 520px) {
        .catalog-metrics {
            grid-template-columns: 1fr;
        }

        .courses-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .filter-chip {
            padding: 7px 12px;
            font-size: 12px;
        }

        .catalog-headline-row p {
            font-size: 12px;
        }

        .course-meta-top,
        .course-rating {
            flex-wrap: wrap;
        }
    }

    /* Compact version used when this page is embedded on home */
    .courses-catalog-embedded {
        padding-bottom: 18px;
        min-height: auto;
        --embedded-side-gap: clamp(16px, 2vw, 28px);
    }

    .courses-catalog-embedded .catalog-hero {
        padding: 14px 0 10px;
    }

    .courses-catalog-embedded .catalog-hero-inner {
        width: calc(100% - (var(--embedded-side-gap) * 2));
        max-width: none;
        margin: 0 auto;
        gap: 10px;
    }

    .courses-catalog-embedded .catalog-copy,
    .courses-catalog-embedded .catalog-hero-card {
        border-radius: 10px;
        box-shadow: 0 6px 14px rgba(16, 24, 40, 0.08);
    }

    .courses-catalog-embedded .catalog-copy {
        padding: 12px;
    }

    .courses-catalog-embedded .catalog-kicker {
        margin-bottom: 4px;
        font-size: 10px;
    }

    .courses-catalog-embedded .catalog-copy h1 {
        font-size: clamp(20px, 2vw, 28px);
        line-height: 1.15;
    }

    .courses-catalog-embedded .catalog-copy > p {
        margin-top: 6px;
        font-size: 12px;
    }

    .courses-catalog-embedded .catalog-metrics {
        margin-top: 8px;
        gap: 6px;
    }

    .courses-catalog-embedded .catalog-metrics div {
        padding: 6px 8px;
    }

    .courses-catalog-embedded .catalog-metrics strong {
        font-size: 13px;
    }

    .courses-catalog-embedded .catalog-metrics span {
        font-size: 10px;
    }

    .courses-catalog-embedded .catalog-hero-card {
        padding: 12px;
    }

    .courses-catalog-embedded .catalog-hero-card h3 {
        font-size: 16px;
    }

    .courses-catalog-embedded .catalog-hero-card p,
    .courses-catalog-embedded .catalog-hero-card li {
        font-size: 11px;
        margin: 0;
    }

    .courses-catalog-embedded .catalog-layout {
        width: calc(100% - (var(--embedded-side-gap) * 2));
        max-width: none;
        margin: 0 auto;
        gap: 14px;
        padding: 0;
        grid-template-columns: 240px minmax(0, 1fr);
    }

    .courses-catalog-embedded .catalog-filter-box {
        top: 110px;
        border-radius: 10px;
        padding: 10px;
        gap: 10px;
    }

    .courses-catalog-embedded .catalog-filter-box h2 {
        font-size: 18px;
    }

    .courses-catalog-embedded .filter-group label {
        font-size: 12px;
    }

    .courses-catalog-embedded .filter-group input,
    .courses-catalog-embedded .filter-group select,
    .courses-catalog-embedded .reset-btn {
        min-height: 38px;
        font-size: 13px;
    }

    .courses-catalog-embedded .filter-chip {
        font-size: 11px;
        padding: 5px 10px;
    }

    .courses-catalog-embedded .catalog-headline-row {
        padding: 2px;
    }

    .courses-catalog-embedded .catalog-headline-row h2 {
        font-size: clamp(24px, 3.2vw, 30px);
    }

    .courses-catalog-embedded .catalog-headline-row p {
        font-size: 11px;
        max-width: 56ch;
    }

    .courses-catalog-embedded .catalog-headline-row span {
        font-size: 11px;
    }

    .courses-catalog-embedded .courses-grid {
        gap: 12px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-top: 6px;
    }

    .courses-catalog-embedded .course-card {
        border-radius: 0;
    }

    .courses-catalog-embedded .course-card-content {
        padding: 8px;
        gap: 5px;
    }

    .courses-catalog-embedded .course-thumb {
        font-size: 10px;
        padding: 7px;
    }

    .courses-catalog-embedded .course-category,
    .courses-catalog-embedded .course-level,
    .courses-catalog-embedded .course-duration,
    .courses-catalog-embedded .course-instructor {
        font-size: 10px;
    }

    .courses-catalog-embedded .course-title {
        font-size: 14px;
    }

    .courses-catalog-embedded .course-price {
        font-size: 13px;
    }

    .courses-catalog-embedded .course-details {
        margin-top: 0;
        border-radius: 10px;
        padding: 12px;
    }

    .courses-catalog-embedded .course-details h2 {
        font-size: 20px;
    }

    .courses-catalog-embedded .course-details > p,
    .courses-catalog-embedded .detail-panel ul,
    .courses-catalog-embedded .detail-panel h3,
    .courses-catalog-embedded .detail-badges span,
    .courses-catalog-embedded .enroll-box span {
        font-size: 12px;
    }

    .courses-catalog-embedded .enroll-box strong {
        font-size: 17px;
    }

    .view-all-wrap {
        display: flex;
        justify-content: flex-end;
        padding: 32px 0 8px;
    }

    .view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 36px;
        background: linear-gradient(135deg, #0d6e84 0%, #0a5568 100%);
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        letter-spacing: 0.3px;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 4px 18px rgba(13,110,132,0.28);
        transition: background 0.22s, box-shadow 0.22s, transform 0.15s;
    }

    .view-all-btn:hover,
    .view-all-btn:focus-visible {
        background: linear-gradient(135deg, #0a5568 0%, #083f50 100%);
        box-shadow: 0 6px 24px rgba(13,110,132,0.38);
        transform: translateY(-2px);
        color: #fff;
        text-decoration: none;
    }

    .view-all-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 10px rgba(13,110,132,0.22);
    }

    .view-all-btn svg {
        transition: transform 0.18s;
        flex-shrink: 0;
    }

    .view-all-btn:hover svg {
        transform: translateX(4px);
    }

    @media (max-width: 1200px) {
        .courses-catalog-embedded .catalog-layout {
            grid-template-columns: 220px minmax(0, 1fr);
            gap: 12px;
        }

        .courses-catalog-embedded .courses-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1080px) {
        .courses-catalog-embedded .catalog-layout {
            grid-template-columns: 1fr;
        }

        .courses-catalog-embedded .catalog-filter-box {
            position: static;
            top: auto;
        }

        .courses-catalog-embedded .catalog-hero-inner {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .courses-catalog-embedded {
            --embedded-side-gap: 12px;
        }

        .courses-catalog-embedded .catalog-headline-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .courses-catalog-embedded .catalog-headline-row h2 {
            font-size: clamp(22px, 6vw, 28px);
        }

        .courses-catalog-embedded .courses-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .courses-catalog-embedded .view-all-wrap {
            justify-content: center;
            padding: 20px 0 8px;
        }

        .courses-catalog-embedded .view-all-btn {
            width: min(360px, 100%);
            justify-content: center;
            padding: 12px 20px;
        }
    }

    @media (max-width: 520px) {
        .courses-catalog-embedded .catalog-copy,
        .courses-catalog-embedded .catalog-hero-card,
        .courses-catalog-embedded .catalog-filter-box,
        .courses-catalog-embedded .course-details {
            border-radius: 10px;
        }

        .courses-catalog-embedded .catalog-metrics {
            grid-template-columns: 1fr;
        }

        .courses-catalog-embedded .courses-grid {
            grid-template-columns: 1fr;
        }

        .courses-catalog-embedded .course-card-content {
            padding: 10px;
            gap: 6px;
        }

        .courses-catalog-embedded .course-title {
            font-size: 15px;
        }

        .courses-catalog-embedded .course-meta-top,
        .courses-catalog-embedded .course-rating {
            flex-wrap: wrap;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .course-card,
        .course-thumb,
        .course-thumb::after,
        .course-card::before {
            transition: none;
        }

        .course-card:hover {
            transform: none;
        }

        .course-card:hover .course-thumb {
            background-size: 108%;
            filter: none;
        }
    }
</style>

<script>
(function () {
window.eduSkillBaseUrl = <?php echo json_encode(BASE_URL); ?>;
window.coursesData = <?php echo json_encode($coursesData ?? [], JSON_UNESCAPED_UNICODE); ?>;
let courses = Array.isArray(window.coursesData) ? window.coursesData : [];
    courses = courses.map(function (course) {
        const item = course && typeof course === 'object' ? course : {};
        return {
            id: item.id || null,
            image: item.image || (window.eduSkillBaseUrl + 'assets/images/cources/web-dev.jpg'),
            title: String(item.title || ''),
            category: String(item.category || ''),
            level: String(item.level || 'All Levels'),
            duration: String(item.duration || 'Self-paced'),
            price: String(item.price || '$0.00'),
            instructor: String(item.instructor || 'Instructor'),
            rating: String(item.rating ?? '0.0'),
            students: String(item.students || '0'),
            overview: String(item.overview || ''),
            tools: Array.isArray(item.tools) ? item.tools : []
        };
    });
    /*
        { id:'c2',  image: IMAGES_BASE+'data-analytics.jpg',   title:'Data Analytics With Python and Power BI',     category:'Data Science',   level:'Intermediate', duration:'8 Weeks',  price:'$149', instructor:'Nisha Koirala',   rating:'4.9', students:'2,050', overview:'Clean, analyze, and visualize business data to make better decisions through dashboards and reports.',                                                    syllabus:['NumPy and Pandas workflows','Exploratory data analysis','Power BI dashboard design','Storytelling with data case study'],                               tools:['Python','Jupyter','Power BI','Excel'],                 outcomes:['Build KPI dashboards','Perform end-to-end analytics workflow','Communicate insights to stakeholders'] }
    ];
    */

    const searchInput = document.getElementById('searchCourse');
    const levelFilter = document.getElementById('levelFilter');
    const categoryFilters = document.getElementById('categoryFilters');
    const instructorDropdown = document.getElementById('instructorDropdown');
    const instructorToggle = document.getElementById('instructorToggle');
    const instructorMenu = document.getElementById('instructorMenu');
    const instructorSearch = document.getElementById('instructorSearch');
    const instructorOptions = document.getElementById('instructorOptions');
    const instructorSelectedLabel = document.getElementById('instructorSelectedLabel');
    const priceRangeFilter = document.getElementById('priceRangeFilter');
    const resetFilters = document.getElementById('resetFilters');
    const coursesGrid = document.getElementById('coursesGrid');
    const courseCount = document.getElementById('courseCount');
    const viewAllWrap = document.querySelector('.view-all-wrap');

    let currentCategory = 'all';
    let currentCourseId = null;
    let currentInstructor = 'all';

    function parseCoursePrice(priceText) {
        return parseInt(String(priceText).replace(/[^0-9]/g, ''), 10) || 0;
    }

    function getSelectedBudgetRange() {
        if (priceRangeFilter.value === 'all') {
            return { min: 0, max: Number.MAX_SAFE_INTEGER };
        }
        if (priceRangeFilter.value === '0-0') {
            return { min: 0, max: 0 };
        }
        const parts = priceRangeFilter.value.split('-');
        return {
            min: parseInt(parts[0], 10),
            max: parseInt(parts[1], 10)
        };
    }

    function getUniqueInstructors() {
        return Array.from(new Set(courses.map(function (course) { return course.instructor; }))).sort();
    }

    function closeInstructorDropdown() {
        instructorDropdown.classList.remove('open');
        instructorMenu.hidden = true;
        instructorToggle.setAttribute('aria-expanded', 'false');
    }

    function openInstructorDropdown() {
        instructorDropdown.classList.add('open');
        instructorMenu.hidden = false;
        instructorToggle.setAttribute('aria-expanded', 'true');
    }

    function setInstructor(value) {
        currentInstructor = value;
        instructorSelectedLabel.textContent = value === 'all' ? 'All Instructors' : value;
        rerender();
    }

    function renderInstructorOptions(query) {
        const keyword = (query || '').trim().toLowerCase();
        const names = getUniqueInstructors().filter(function (name) {
            return !keyword || name.toLowerCase().includes(keyword);
        });

        instructorOptions.innerHTML = '';

        if (!keyword || 'all instructors'.includes(keyword)) {
            const allBtn = document.createElement('button');
            allBtn.type = 'button';
            allBtn.className = 'instructor-option' + (currentInstructor === 'all' ? ' active' : '');
            allBtn.setAttribute('data-value', 'all');
            allBtn.textContent = 'All Instructors';
            instructorOptions.appendChild(allBtn);
        }

        if (!names.length) {
            const empty = document.createElement('div');
            empty.className = 'instructor-empty';
            empty.textContent = 'No instructor found.';
            instructorOptions.appendChild(empty);
            return;
        }

        names.forEach(function (name) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'instructor-option' + (currentInstructor === name ? ' active' : '');
            btn.setAttribute('data-value', name);
            btn.textContent = name;
            instructorOptions.appendChild(btn);
        });
    }

    var isEmbeddedMode = document.querySelector('.courses-catalog-embedded') !== null;

    function updateViewAllButtonVisibility() {
        if (!isEmbeddedMode || !viewAllWrap) return;
        viewAllWrap.style.display = courses.length < 24 ? 'none' : '';
    }

    function openCourseDetails(courseId) {
        var safeCourseId = Number(courseId || 0);
        if (!Number.isFinite(safeCourseId) || safeCourseId <= 0) {
            return;
        }

        var baseUrl = window.eduSkillBaseUrl || '/';
        window.location.href = baseUrl + 'pages/courcedetails.php?id=' + encodeURIComponent(String(safeCourseId));
    }

    function renderCards(filteredCourses) {
        var displayCourses = isEmbeddedMode ? filteredCourses.slice(0, 24) : filteredCourses;
        coursesGrid.innerHTML = '';
        courseCount.textContent = filteredCourses.length + (filteredCourses.length === 1 ? ' course' : ' courses');

        if (!filteredCourses.length) {
            coursesGrid.innerHTML = '<div class="empty-state">No courses match these filters. Try a different category or reset all filters.</div>';
            return;
        }

        displayCourses.forEach(function (course) {
            const card = document.createElement('article');
            card.className = 'course-card' + (course.id === currentCourseId ? ' active' : '');
            card.setAttribute('data-id', course.id);
            card.style.cursor = 'pointer';
            card.innerHTML =
                '<div class="course-thumb" style="background-image:url(\'' + course.image + '\')"></div>' +
                '<div class="course-card-content">' +
                    '<div class="course-meta-top">' +
                        '<span class="course-category">' + course.category + '</span>' +
                        '<span class="course-level">' + course.level + '</span>' +
                    '</div>' +
                    '<h3 class="course-title">' + course.title + '</h3>' +
                    '<p class="course-instructor">By ' + course.instructor + '</p>' +
                    '<div class="course-rating">' +
                        '<span class="stars">&#9733;</span>' +
                        '<span class="rating-num">' + course.rating + '</span>' +
                        '<span class="rating-students">(' + course.students + ' students)</span>' +
                    '</div>' +
                    '<div class="course-meta-bottom">' +
                        '<span class="course-duration"><i class="bi bi-clock"></i> ' + course.duration + '</span>' +
                        '<span class="course-price">' + course.price + '</span>' +
                    '</div>' +
                '</div>';

            card.addEventListener('click', function () {
                openCourseDetails(course.id);
            });

            coursesGrid.appendChild(card);
        });

        if (!currentCourseId) {
            currentCourseId = filteredCourses[0].id;
            renderCards(filteredCourses);
        }
    }

    function getFilteredCourses() {
        const keyword = (searchInput.value || '').trim().toLowerCase();
        const selectedLevel = levelFilter.value;
        const budgetRange = getSelectedBudgetRange();

        return courses.filter(function (course) {
            const byCategory    = currentCategory === 'all' || course.category === currentCategory;
            const byLevel       = selectedLevel === 'all' || course.level === selectedLevel;
            const byInstructor  = currentInstructor === 'all' || course.instructor === currentInstructor;
            const priceNum      = parseCoursePrice(course.price);
            const byPrice       = priceNum >= budgetRange.min && priceNum <= budgetRange.max;
            const courseTitle   = String(course.title || '').toLowerCase();
            const courseCategory = String(course.category || '').toLowerCase();
            const courseInstructor = String(course.instructor || '').toLowerCase();
            const courseTools = Array.isArray(course.tools)
                ? course.tools.join(' ').toLowerCase()
                : '';
            const byKeyword     = !keyword ||
                courseTitle.includes(keyword) ||
                courseCategory.includes(keyword) ||
                courseInstructor.includes(keyword) ||
                courseTools.includes(keyword);

            return byCategory && byLevel && byInstructor && byPrice && byKeyword;
        });
    }

    function rerender() {
        const filtered = getFilteredCourses();
        if (!filtered.some(function (c) { return c.id === currentCourseId; })) {
            currentCourseId = filtered.length ? filtered[0].id : null;
        }
        renderCards(filtered);
    }

    categoryFilters.addEventListener('click', function (event) {
        const target = event.target;
        if (!target.classList.contains('filter-chip')) return;

        currentCategory = target.getAttribute('data-filter');
        Array.prototype.forEach.call(categoryFilters.querySelectorAll('.filter-chip'), function (chip) {
            chip.classList.remove('active');
        });
        target.classList.add('active');
        rerender();
    });

    instructorToggle.addEventListener('click', function () {
        if (instructorDropdown.classList.contains('open')) {
            closeInstructorDropdown();
            return;
        }
        renderInstructorOptions(instructorSearch.value);
        openInstructorDropdown();
        instructorSearch.focus();
    });

    instructorSearch.addEventListener('input', function () {
        renderInstructorOptions(instructorSearch.value);
    });

    instructorOptions.addEventListener('click', function (event) {
        const target = event.target;
        if (!target.classList.contains('instructor-option')) return;
        setInstructor(target.getAttribute('data-value'));
        instructorSearch.value = '';
        renderInstructorOptions('');
        closeInstructorDropdown();
    });

    document.addEventListener('click', function (event) {
        if (!instructorDropdown.contains(event.target)) {
            closeInstructorDropdown();
        }
    });

    instructorSearch.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeInstructorDropdown();
            instructorToggle.focus();
        }
    });

    [searchInput, levelFilter, priceRangeFilter].forEach(function (el) {
        el.addEventListener('input', rerender);
        el.addEventListener('change', rerender);
    });

    resetFilters.addEventListener('click', function () {
        currentCategory = 'all';
        currentCourseId = null;
        searchInput.value = '';
        levelFilter.value = 'all';
        currentInstructor = 'all';
        instructorSelectedLabel.textContent = 'All Instructors';
        instructorSearch.value = '';
        renderInstructorOptions('');
        closeInstructorDropdown();
        priceRangeFilter.value = 'all';
        Array.prototype.forEach.call(categoryFilters.querySelectorAll('.filter-chip'), function (chip) {
            chip.classList.toggle('active', chip.getAttribute('data-filter') === 'all');
        });
        rerender();
    });

    renderInstructorOptions('');
    updateViewAllButtonVisibility();
    rerender();
})();
</script>
<?php endif; ?>
