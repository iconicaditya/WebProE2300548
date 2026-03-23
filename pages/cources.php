<?php
/**
 * Frontend-only Courses Catalog Page
 * Intentional filename: cources.php (as requested)
 */
$isEmbeddedCoursesSection = isset($embedCoursesSection) && $embedCoursesSection === true;
$useExternalAllCoursesAssets = isset($useExternalAllCoursesAssets) && $useExternalAllCoursesAssets === true;
$renderCoursesOnly = isset($renderCoursesOnly) && $renderCoursesOnly === true;

if (!$isEmbeddedCoursesSection && !$renderCoursesOnly) {
    $pageTitle = 'Explore Courses';
    require_once(__DIR__ . '/../includes/header.php');
    require_once(__DIR__ . '/../includes/navbar.php');
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

    @media (max-width: 1200px) {
        .catalog-layout {
            grid-template-columns: 250px minmax(0, 1fr);
        }

        .courses-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 1080px) {
        .catalog-layout {
            grid-template-columns: 1fr;
        }

        .catalog-filter-box {
            position: static;
        }

        .catalog-hero-inner {
            grid-template-columns: 1fr;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .catalog-metrics {
            grid-template-columns: 1fr;
        }

        .catalog-headline-row h2 {
            font-size: 22px;
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
        font-size: 34px;
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
        grid-template-columns: repeat(4, 1fr);
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

    @media (max-width: 900px) {
        .courses-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 640px) {
        .catalog-metrics { grid-template-columns: 1fr; }
        .catalog-headline-row h2 { font-size: 22px; }
        .courses-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .courses-grid { grid-template-columns: 1fr; }
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
    const IMAGES_BASE = '<?php echo BASE_URL; ?>assets/images/cources/';
    const courses = [
        { id:'c1',  image: IMAGES_BASE+'web-dev.jpg',         title:'Full-Stack Web Development Bootcamp',         category:'Programming',    level:'Beginner',     duration:'10 Weeks', price:'$129', instructor:'Aaditya Sharma',  rating:'4.8', students:'3,200', overview:'Build responsive websites and web apps from scratch using HTML, CSS, JavaScript, PHP, and MySQL.',                                                            syllabus:['Modern HTML5 and semantic structure','Responsive CSS and component layouts','JavaScript DOM and API integration','PHP + MySQL mini-project deployment'],    tools:['VS Code','XAMPP','Git/GitHub','Figma Basics'],         outcomes:['Create production-ready portfolio projects','Understand full request-response workflow','Deploy and present complete web apps'] },
        { id:'c2',  image: IMAGES_BASE+'data-analytics.jpg',   title:'Data Analytics With Python and Power BI',     category:'Data Science',   level:'Intermediate', duration:'8 Weeks',  price:'$149', instructor:'Nisha Koirala',   rating:'4.9', students:'2,050', overview:'Clean, analyze, and visualize business data to make better decisions through dashboards and reports.',                                                    syllabus:['NumPy and Pandas workflows','Exploratory data analysis','Power BI dashboard design','Storytelling with data case study'],                               tools:['Python','Jupyter','Power BI','Excel'],                 outcomes:['Build KPI dashboards','Perform end-to-end analytics workflow','Communicate insights to stakeholders'] },
        { id:'c3',  image: IMAGES_BASE+'ui-ux.jpg',            title:'UI/UX Design for Digital Products',           category:'Design',         level:'Beginner',     duration:'6 Weeks',  price:'$99',  instructor:'Karan Basnet',    rating:'4.7', students:'1,480', overview:'Design user-centered interfaces with strong visual hierarchy, accessibility, and practical design systems.',                                                 syllabus:['UX research and user personas','Wireframing and prototyping','Visual design system foundations','Usability testing and iteration'],                     tools:['Figma','FigJam','Notion','Maze'],                     outcomes:['Create polished app/web screens','Run practical UX tests','Present a complete case study portfolio'] },
        { id:'c4',  image: IMAGES_BASE+'digital-marketing.jpg',title:'Strategic Digital Marketing Masterclass',     category:'Business',       level:'Advanced',     duration:'7 Weeks',  price:'$139', instructor:'Ritika Adhikari', rating:'4.8', students:'2,760', overview:'Plan, launch, and optimize marketing campaigns using SEO, paid media, social channels, and analytics.',                                                     syllabus:['Campaign strategy and funnel design','SEO and content planning','Ads optimization and conversion metrics','Marketing performance reporting'],            tools:['GA4','Meta Ads','Google Ads','Canva'],                outcomes:['Run measurable digital campaigns','Optimize CAC and ROAS','Build a full-funnel marketing plan'] },
        { id:'c5',  image: IMAGES_BASE+'react-frontend.jpg',   title:'React.js & Modern Frontend Development',      category:'Programming',    level:'Intermediate', duration:'8 Weeks',  price:'$119', instructor:'Saurav Pandey',   rating:'4.7', students:'2,140', overview:'Master React hooks, state management, routing, and deployment to build real-world single-page applications.',                                               syllabus:['React components and JSX','Hooks: useState, useEffect, useContext','React Router and code splitting','Redux Toolkit state management'],                  tools:['React','Vite','Redux','Tailwind CSS'],                outcomes:['Build and deploy full SPAs','Manage complex app state','Integrate REST APIs with React'] },
        { id:'c6',  image: IMAGES_BASE+'machine-learning.jpg', title:'Machine Learning Fundamentals with Python',   category:'Data Science',   level:'Advanced',     duration:'10 Weeks', price:'$179', instructor:'Priya Dhakal',    rating:'4.9', students:'1,820', overview:'Learn supervised and unsupervised machine learning algorithms, model evaluation, and deployment pipelines.',                                               syllabus:['Linear and logistic regression','Decision trees & random forests','Clustering and dimensionality reduction','Model deployment with Flask/FastAPI'],       tools:['Python','scikit-learn','TensorFlow','Jupyter'],       outcomes:['Build and evaluate ML models','Deploy prediction APIs','Interpret model results'] },
        { id:'c7',  image: IMAGES_BASE+'graphic-design.jpg',   title:'Graphic Design with Adobe Creative Suite',    category:'Design',         level:'Beginner',     duration:'6 Weeks',  price:'$89',  instructor:'Anita Thapa',     rating:'4.6', students:'1,630', overview:'Master Photoshop, Illustrator, and InDesign to produce professional-grade brand assets and print/digital media.',                                         syllabus:['Photoshop layer masking and retouching','Illustrator vector illustration','InDesign layout and typography','Brand identity design project'],              tools:['Photoshop','Illustrator','InDesign','Adobe Fonts'],   outcomes:['Design logos and brand kits','Create print-ready materials','Build a design portfolio'] },
        { id:'c8',  image: IMAGES_BASE+'ecommerce.jpg',        title:'E-Commerce Business Blueprint',               category:'Business',       level:'Intermediate', duration:'6 Weeks',  price:'$109', instructor:'Mohan Gurung',    rating:'4.6', students:'1,350', overview:'Launch and scale an online store using Shopify or WooCommerce with effective product listings, SEO, and ad strategies.',                                  syllabus:['Platform setup and theme customization','Product research and catalog optimization','Checkout and payment gateway setup','E-commerce SEO and ROAS'],       tools:['Shopify','WooCommerce','GA4','Klaviyo'],               outcomes:['Launch a profitable online store','Drive organic and paid traffic','Optimize conversions and checkout'] },
        { id:'c9',  image: IMAGES_BASE+'mobile-dev.jpg',       title:'iOS & Android App Development',               category:'Programming',    level:'Intermediate', duration:'9 Weeks',  price:'$149', instructor:'Rajan Poudel',    rating:'4.7', students:'1,760', overview:'Build cross-platform mobile apps with React Native and publish them to App Store and Google Play.',                                                       syllabus:['React Native setup and components','Navigation with Expo Router','Platform APIs: camera, storage, push notifications','App Store submission workflow'],  tools:['React Native','Expo','Android Studio','TestFlight'],  outcomes:['Ship an app to both stores','Handle device APIs','Build offline-first mobile UIs'] },
        { id:'c10', image: IMAGES_BASE+'cybersecurity.jpg',    title:'Cybersecurity Essentials for Beginners',      category:'Cybersecurity',  level:'Beginner',     duration:'5 Weeks',  price:'$79',  instructor:'Deepak Rana',     rating:'4.5', students:'2,210', overview:'Understand cyber threats, secure network design, encryption fundamentals, and safe browsing practices.',                                                   syllabus:['Threat landscape and attack vectors','Network security basics','Password management and MFA','Phishing and social engineering defense'],                   tools:['Wireshark','Kali Linux','VPN','1Password'],           outcomes:['Identify common cyber threats','Secure home and work networks','Follow security best practices'] },
        { id:'c11', image: IMAGES_BASE+'python.jpg',           title:'Python Programming for Absolute Beginners',   category:'Programming',    level:'Beginner',     duration:'5 Weeks',  price:'$69',  instructor:'Suresh Byanjankar',rating:'4.8', students:'4,100', overview:'Start coding in Python from scratch with hands-on exercises covering variables, loops, functions, and OOP.',                                              syllabus:['Variables, data types, operators','Control flow and loops','Functions and modules','Object-oriented programming basics'],                                   tools:['Python','VS Code','Jupyter','Replit'],                outcomes:['Write clean Python scripts','Build small automation tools','Understand foundational OOP concepts'] },
        { id:'c12', image: IMAGES_BASE+'video-editing.jpg',    title:'Video Editing & Post-Production',             category:'Design',         level:'Beginner',     duration:'5 Weeks',  price:'$89',  instructor:'Rina Tamrakar',   rating:'4.6', students:'1,220', overview:'Learn professional video editing, color grading, and audio mixing using Adobe Premiere Pro and DaVinci Resolve.',                                          syllabus:['Timeline workflow and cuts','Color grading techniques','Audio mixing and sound design','Exporting for web, broadcast, and social'],                        tools:['Premiere Pro','DaVinci Resolve','After Effects','Audition'], outcomes:['Edit polished short films','Grade and color videos','Export for multiple platforms'] },
        { id:'c13', image: IMAGES_BASE+'seo-marketing.jpg',    title:'SEO & Content Marketing Strategy',            category:'Business',       level:'Intermediate', duration:'6 Weeks',  price:'$109', instructor:'Mina Shrestha',   rating:'4.7', students:'1,890', overview:'Rank higher in search engines and drive organic traffic with on-page SEO, link building, and a content calendar.',                                        syllabus:['Keyword research and SERP analysis','On-page and technical SEO','Content calendar and pillar strategy','Link building and authority building'],            tools:['Ahrefs','SEMrush','Google Search Console','WordPress'], outcomes:['Improve organic search rankings','Build a content pipeline','Track and report SEO performance'] },
        { id:'c14', image: IMAGES_BASE+'cloud-aws.jpg',        title:'Cloud Computing with AWS',                    category:'Cloud & DevOps', level:'Intermediate', duration:'8 Weeks',  price:'$169', instructor:'Binod Shrestha',  rating:'4.8', students:'1,540', overview:'Provision, manage, and secure cloud infrastructure on Amazon Web Services including EC2, S3, Lambda, and VPC.',                                             syllabus:['AWS core services overview','EC2 instances and auto-scaling','S3 storage and CloudFront CDN','IAM roles, policies, and CloudWatch'],                       tools:['AWS Console','Terraform','CLI','CloudFormation'],     outcomes:['Deploy scalable cloud apps','Configure secure IAM policies','Monitor and optimize cloud costs'] },
        { id:'c15', image: IMAGES_BASE+'photography.jpg',      title:'Digital Photography Masterclass',             category:'Design',         level:'Beginner',     duration:'4 Weeks',  price:'$69',  instructor:'Samita Karki',    rating:'4.5', students:'990',   overview:'Learn manual camera controls, composition rules, lighting techniques, and Lightroom post-processing.',                                                     syllabus:['Aperture, shutter speed, ISO triangle','Composition: rule of thirds, leading lines','Natural and artificial lighting setups','Lightroom editing workflow'], tools:['DSLR/Mirrorless','Lightroom','Photoshop','Snapseed'], outcomes:['Shoot in full manual mode','Edit photos professionally','Build a photo portfolio'] },
        { id:'c16', image: IMAGES_BASE+'finance.jpg',          title:'Financial Literacy & Smart Investing',        category:'Business',       level:'Beginner',     duration:'5 Weeks',  price:'$79',  instructor:'Anil Maharjan',   rating:'4.6', students:'2,440', overview:'Understand personal finance, budgeting, stock market basics, mutual funds, and long-term wealth building strategies.',                                    syllabus:['Budgeting and expense tracking','Emergency funds and insurance','Stock market and index investing','Retirement planning and compound interest'],           tools:['Excel','Google Sheets','Trading View','Zerodha'],     outcomes:['Create a personal budget plan','Start investing with low risk','Understand tax-efficient saving'] },
        { id:'c17', image: IMAGES_BASE+'nodejs-backend.jpg',   title:'Node.js & Express Backend Development',       category:'Programming',    level:'Intermediate', duration:'7 Weeks',  price:'$119', instructor:'Kabir Lama',      rating:'4.7', students:'1,380', overview:'Build performant REST APIs, handle authentication, and connect Node.js apps to MongoDB and PostgreSQL databases.',                                        syllabus:['Node.js runtime and event loop','Express routing and middleware','JWT authentication and authorization','MongoDB and PostgreSQL integration'],              tools:['Node.js','Express','MongoDB','Postman'],              outcomes:['Build production-grade APIs','Implement secure auth flows','Deploy Node apps to cloud'] },
        { id:'c18', image: IMAGES_BASE+'ai-chatgpt.jpg',       title:'AI & ChatGPT for Productivity',               category:'Data Science',   level:'Beginner',     duration:'3 Weeks',  price:'$59',  instructor:'Pratima Gurung',  rating:'4.7', students:'3,600', overview:'Use AI tools including ChatGPT, Copilot, and Midjourney to automate tasks, generate content, and boost daily productivity.',                             syllabus:['Prompt engineering fundamentals','AI for writing and research','Image generation with Midjourney','Automation with Zapier and Make'],                       tools:['ChatGPT','Copilot','Midjourney','Zapier'],            outcomes:['Write effective prompts','Automate repetitive workflows','Use AI for business tasks'] },
        { id:'c19', image: IMAGES_BASE+'3d-modeling.jpg',      title:'3D Modeling & Animation with Blender',        category:'Design',         level:'Intermediate', duration:'8 Weeks',  price:'$109', instructor:'Saroj Basnet',    rating:'4.6', students:'870',   overview:'Create characters, environments, and animated sequences in Blender from polygon modeling to final render.',                                                syllabus:['Blender interface and navigation','Polygon modeling and sculpting','Rigging and basic animation','Lighting, materials, and final render'],                 tools:['Blender','Cycles Renderer','HDRI Haven','After Effects'], outcomes:['Model 3D assets for games or film','Rig and animate characters','Produce studio-quality renders'] },
        { id:'c20', image: IMAGES_BASE+'project-mgmt.jpg',     title:'Project Management Professional (PMP Prep)',  category:'Business',       level:'Advanced',     duration:'7 Weeks',  price:'$149', instructor:'Sunita Thapa',    rating:'4.8', students:'2,080', overview:'Master agile, scrum, and waterfall project management methodologies required for the PMP certification exam.',                                             syllabus:['Project initiation and scope definition','Agile and Scrum frameworks','Risk management and mitigation','Stakeholder communication and reporting'],         tools:['Jira','MS Project','Confluence','Miro'],              outcomes:['Prepare for PMP certification','Lead cross-functional teams','Deliver projects on time and budget'] },
        { id:'c21', image: IMAGES_BASE+'excel-analytics.jpg',  title:'Excel & Business Data Analytics',             category:'Business',       level:'Beginner',     duration:'4 Weeks',  price:'$69',  instructor:'Bimala Upreti',   rating:'4.5', students:'3,280', overview:'Master Excel formulas, pivot tables, dashboards, and Power Query to analyze and report business data efficiently.',                                       syllabus:['Core formulas: VLOOKUP, INDEX-MATCH, IF','PivotTables and PivotCharts','Power Query data transformation','Dashboard design best practices'],               tools:['Excel','Power Query','Power Pivot','Power BI'],       outcomes:['Automate reporting workflows','Build dynamic dashboards','Clean and transform raw data'] },
        { id:'c22', image: IMAGES_BASE+'copywriting.jpg',      title:'Copywriting & Content Creation',              category:'Business',       level:'Beginner',     duration:'4 Weeks',  price:'$69',  instructor:'Smriti Panta',    rating:'4.6', students:'1,710', overview:'Write persuasive copy for websites, emails, ads, and social media using proven frameworks like AIDA and PAS.',                                           syllabus:['AIDA and PAS copywriting frameworks','Headline and hook writing','Email sequences and nurture campaigns','Social media content calendars'],                 tools:['Grammarly','Notion','Canva','Mailchimp'],             outcomes:['Write high-converting landing pages','Build email sequences','Create a consistent brand voice'] },
        { id:'c23', image: IMAGES_BASE+'devops.jpg',           title:'DevOps with Docker & Kubernetes',             category:'Cloud & DevOps', level:'Advanced',     duration:'9 Weeks',  price:'$179', instructor:'Roshan Karmacharya',rating:'4.8',students:'1,200', overview:'Containerize applications with Docker, orchestrate with Kubernetes, and set up CI/CD pipelines for automated delivery.',                                  syllabus:['Docker containers and image building','Docker Compose multi-service apps','Kubernetes pods, services, and deployments','CI/CD with GitHub Actions'],        tools:['Docker','Kubernetes','GitHub Actions','Helm'],       outcomes:['Containerize and deploy any app','Manage K8s clusters','Build automated CI/CD pipelines'] },
        { id:'c24', image: IMAGES_BASE+'java.jpg',             title:'Java Programming Mastery',                    category:'Programming',    level:'Intermediate', duration:'9 Weeks',  price:'$129', instructor:'Rajesh Joshi',    rating:'4.6', students:'1,640', overview:'Learn core and advanced Java including OOP, collections, multithreading, Spring Boot, and REST API development.',                                         syllabus:['Java OOP: classes, inheritance, polymorphism','Collections framework and generics','Multithreading and concurrency','Spring Boot REST API development'],    tools:['IntelliJ IDEA','Maven','Spring Boot','Postman'],      outcomes:['Build enterprise Java applications','Design RESTful APIs with Spring Boot','Write multithreaded programs'] },
        { id:'c25', image: IMAGES_BASE+'social-media.jpg',     title:'Social Media Marketing & Growth Strategy',    category:'Business',       level:'Beginner',     duration:'5 Weeks',  price:'$89',  instructor:'Preeti Manandhar',rating:'4.5', students:'2,970', overview:'Grow brand awareness, engagement, and leads across Instagram, TikTok, LinkedIn, and YouTube with proven strategies.',                                    syllabus:['Platform algorithms and organic growth tactics','Content pillars and posting cadence','Paid social ads (Meta & LinkedIn)','Analytics and reporting'],       tools:['Meta Business Suite','Buffer','Canva','Hootsuite'],   outcomes:['Grow an organic following', 'Run profitable social ads','Track social ROI'] },
        { id:'c26', image: IMAGES_BASE+'film-production.jpg',  title:'Film Production & Visual Storytelling',       category:'Design',         level:'Intermediate', duration:'7 Weeks',  price:'$119', instructor:'Bikash Maharjan', rating:'4.6', students:'760',   overview:'Direct, shoot, and edit short films and documentaries using professional cinematography and storytelling techniques.',                                     syllabus:['Script and storyboard development','Camera movement and shot composition','Location lighting and sound recording','Editing and sound design in Premiere'], tools:['Premiere Pro','DaVinci Resolve','Logic Pro','DSLR/Cinema Camera'], outcomes:['Produce a short film end-to-end','Apply cinematic storytelling techniques','Build a director\'s portfolio'] },
        { id:'c27', image: IMAGES_BASE+'flutter.jpg',          title:'Flutter Cross-Platform App Development',      category:'Programming',    level:'Advanced',     duration:'10 Weeks', price:'$159', instructor:'Bibek Tamang',    rating:'4.7', students:'1,050', overview:'Build beautiful, high-performance mobile and web apps for iOS, Android, and web from a single Dart codebase.',                                            syllabus:['Dart language fundamentals','Flutter widgets and layouts','State management with Riverpod/Bloc','Firebase integration and deployment'],                      tools:['Flutter','Dart','Firebase','Android Studio'],         outcomes:['Ship apps to iOS and Android simultaneously','Implement robust state management','Integrate cloud backends with Firebase'] },
        { id:'c28', image: IMAGES_BASE+'blockchain.jpg',       title:'Blockchain & Web3 Development',               category:'Programming',    level:'Advanced',     duration:'9 Weeks',  price:'$189', instructor:'Nabin Giri',      rating:'4.7', students:'820',   overview:'Build decentralized applications using Ethereum, Solidity smart contracts, and popular Web3 libraries.',                                                  syllabus:['Blockchain architecture and consensus','Solidity smart contract development','Web3.js and ethers.js integration','DeFi protocols and NFT standards'],       tools:['Solidity','Hardhat','MetaMask','Ethers.js'],          outcomes:['Deploy smart contracts to Ethereum testnet','Build a full dApp','Understand DeFi and NFT ecosystems'] },
        { id:'c29', image: IMAGES_BASE+'public-speaking.jpg',  title:'Public Speaking & Communication Mastery',     category:'Business',       level:'Beginner',     duration:'4 Weeks',  price:'$59',  instructor:'Geeta Bhattarai', rating:'4.5', students:'1,930', overview:'Overcome speaking anxiety, structure compelling presentations, and deliver with confidence in any professional setting.',                                  syllabus:['Managing nervousness and building confidence','Storytelling frameworks (Hero\'s Journey, STAR)','Slide design and visual storytelling','Q&A handling and panel discussions'], tools:['PowerPoint','Canva','Zoom','Loom'],             outcomes:['Speak confidently in meetings and events','Design compelling presentations','Handle tough audience questions'] },
        { id:'c30', image: IMAGES_BASE+'ethical-hacking.jpg',  title:'Ethical Hacking & Penetration Testing',       category:'Cybersecurity',  level:'Advanced',     duration:'10 Weeks', price:'$199', instructor:'Sagar Bhusal',    rating:'4.9', students:'1,340', overview:'Learn offensive security skills including reconnaissance, exploitation, privilege escalation, and reporting to help organizations stay secure.',           syllabus:['Pentest methodology and scoping','Network scanning and enumeration','Web application attacks: SQLi, XSS, CSRF','Post-exploitation and reporting'],          tools:['Kali Linux','Metasploit','Burp Suite','Nmap'],        outcomes:['Conduct authorized penetration tests','Write professional pentest reports','Earn pathways to CEH/OSCP certs'] },
        { id:'c31', image: IMAGES_BASE+'html-css-free.jpg',     title:'HTML & CSS for Complete Beginners',            category:'Programming',    level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Aaditya Sharma',  rating:'4.8', students:'5,420', overview:'Build your first webpage from scratch using clean, semantic HTML5 and modern CSS — no prior experience needed.',                                             syllabus:['HTML structure and semantic tags','CSS selectors and the box model','Flexbox and responsive layouts','Publishing your first webpage'], tools:['VS Code','Chrome DevTools','GitHub Pages','CodePen'], outcomes:['Build a personal webpage from scratch','Understand semantic HTML','Apply responsive CSS layouts'] },
        { id:'c32', image: IMAGES_BASE+'intro-datascience.jpg', title:'Introduction to Data Science',                 category:'Data Science',   level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Nisha Koirala',   rating:'4.7', students:'3,890', overview:'Explore what data science is, how it is used across industries, and get hands-on with basic Python data analysis.',                                         syllabus:['What is data science and who uses it','Python basics for data exploration','Reading and summarizing datasets with Pandas','Your first data visualization'], tools:['Python','Jupyter Notebook','Pandas','Matplotlib'], outcomes:['Understand the data science workflow','Explore real datasets with Python','Create basic charts and summaries'] },
        { id:'c33', image: IMAGES_BASE+'entrepreneurship.jpg',  title:'Entrepreneurship Fundamentals',                category:'Business',       level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Ritika Adhikari', rating:'4.6', students:'2,760', overview:'Learn how ideas become businesses — from identifying opportunities and validating products to writing a lean business plan.',                               syllabus:['Identifying market problems and opportunities','Customer discovery and lean validation','Business model canvas','Pitching your idea clearly'],              tools:['Notion','Google Forms','Canva','Lean Canvas'],        outcomes:['Validate a startup idea','Build a lean business model','Pitch confidently to stakeholders'] },
        { id:'c34', image: IMAGES_BASE+'design-thinking.jpg',   title:'Design Thinking Fundamentals',                 category:'Design',         level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Karan Basnet',    rating:'4.7', students:'2,100', overview:'Apply the five-stage design thinking process — empathise, define, ideate, prototype, and test — to solve real-world problems.',                           syllabus:['Empathy mapping and user research','Problem definition and insight framing','Ideation and brainstorming techniques','Paper prototyping and usability testing'], tools:['FigJam','Notion','Miro','Sticky Notes'],           outcomes:['Run a design thinking sprint','Create empathy maps','Build and test a paper prototype'] },
        { id:'c35', image: IMAGES_BASE+'cyber-safety.jpg',      title:'Cyber Safety & Privacy Basics',                category:'Cybersecurity',  level:'Beginner',     duration:'1 Week',   price:'Free', instructor:'Deepak Rana',     rating:'4.5', students:'4,670', overview:'Protect yourself online with practical knowledge about phishing, strong passwords, two-factor authentication, and safe browsing habits.',                   syllabus:['Understanding common online threats','Creating and managing strong passwords','Setting up two-factor authentication','Spotting phishing emails and scams'],  tools:['Bitwarden','Google Authenticator','Have I Been Pwned','Privacy Badger'], outcomes:['Identify and avoid common cyber threats','Set up strong password hygiene','Protect personal data online'] },
        { id:'c36', image: IMAGES_BASE+'git-github.jpg',        title:'Git & GitHub for Beginners',                   category:'Programming',    level:'Beginner',     duration:'1 Week',   price:'Free', instructor:'Saurav Pandey',   rating:'4.8', students:'6,200', overview:'Learn version control with Git and how to collaborate on code using GitHub — an essential skill for every developer.',                                     syllabus:['Git init, add, commit, log','Branching and merging','Pull requests and code review on GitHub','Resolving merge conflicts'],                               tools:['Git','GitHub','VS Code','GitHub Desktop'],            outcomes:['Manage code with Git version control','Collaborate via GitHub pull requests','Resolve merge conflicts confidently'] },
        { id:'c37', image: IMAGES_BASE+'intro-cloud.jpg',       title:'Introduction to Cloud Computing',              category:'Cloud & DevOps', level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Binod Shrestha',  rating:'4.6', students:'3,010', overview:'Understand cloud concepts, service models (IaaS, PaaS, SaaS), and major providers to start your cloud career path.',                                       syllabus:['What is cloud computing and why it matters','IaaS vs PaaS vs SaaS explained','Overview of AWS, Azure, and GCP','Cloud pricing, security, and compliance basics'], tools:['AWS Free Tier','Azure Free Account','Google Cloud Console','draw.io'], outcomes:['Explain core cloud concepts confidently','Compare AWS, Azure, and GCP','Set up a free cloud account and launch a service'] },
        { id:'c38', image: IMAGES_BASE+'communication.jpg',     title:'Professional Communication Skills',            category:'Business',       level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Geeta Bhattarai', rating:'4.5', students:'3,880', overview:'Build workplace communication skills — writing clear emails, running effective meetings, and presenting ideas with confidence.',                             syllabus:['Writing professional emails and messages','Active listening and giving feedback','Running productive meetings','Presenting ideas clearly and concisely'],   tools:['Gmail / Outlook','Notion','Zoom','Google Slides'],    outcomes:['Write clear, professional emails','Facilitate effective team meetings','Present ideas with confidence'] },
        { id:'c39', image: IMAGES_BASE+'statistics.jpg',        title:'Statistics for Data Science',                  category:'Data Science',   level:'Beginner',     duration:'3 Weeks',  price:'Free', instructor:'Priya Dhakal',    rating:'4.7', students:'2,540', overview:'Master the statistical foundations every data scientist needs — mean, median, distributions, correlation, and hypothesis testing.',                         syllabus:['Descriptive statistics: mean, median, mode','Probability and distributions','Correlation and regression basics','Hypothesis testing and p-values'],        tools:['Python','NumPy','SciPy','Jupyter Notebook'],          outcomes:['Interpret descriptive and inferential statistics','Apply probability to real datasets','Understand hypothesis testing results'] },
        { id:'c40', image: IMAGES_BASE+'motion-graphics.jpg',   title:'Introduction to Motion Graphics',              category:'Design',         level:'Beginner',     duration:'2 Weeks',  price:'Free', instructor:'Anita Thapa',     rating:'4.6', students:'1,890', overview:'Get started with motion design — learn keyframes, easing, and transitions to bring graphics to life in Adobe After Effects.',                               syllabus:['After Effects interface and workspace','Keyframes, timing, and easing','Text animations and shape layers','Exporting for social media and web'],           tools:['Adobe After Effects','Adobe Illustrator','Media Encoder','Behance'], outcomes:['Create smooth animated graphics','Apply easing to keyframe animations','Export motion graphics for digital use'] }
    ];

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
                currentCourseId = course.id;
                renderCards(getFilteredCourses());
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
            const byKeyword     = !keyword ||
                course.title.toLowerCase().includes(keyword) ||
                course.category.toLowerCase().includes(keyword) ||
                course.instructor.toLowerCase().includes(keyword) ||
                course.tools.join(' ').toLowerCase().includes(keyword);

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

<?php if (!$isEmbeddedCoursesSection && !$renderCoursesOnly) require_once(__DIR__ . '/../includes/footer.php'); ?>
