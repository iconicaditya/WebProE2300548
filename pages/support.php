<?php
/**
 * Support Center page.
 * Inspired by the provided reference design.
 */

$supportIsEmbedded = isset($embedSupportSection) && $embedSupportSection === true;

if (!$supportIsEmbedded) {
    $pageTitle = 'Support Center';
    $pageStylesheet = 'support-aaditya.css';
    $extraScripts = ['support-aaditya.js'];
}

$supportTopics = [
    [
        'title' => 'Platform Overview',
        'icon' => 'bi-journal-text',
        'summary' => 'Understand how EduSkill works for learners, providers, and officers from one quick guide.',
        'tags' => ['overview', 'onboarding', 'roles', 'eduskill'],
    ],
    [
        'title' => 'Provider Course Setup',
        'icon' => 'bi-pc-display',
        'summary' => 'Create and publish course details, media, modules, pricing, and launch checklists correctly.',
        'tags' => ['provider', 'publish', 'modules', 'pricing'],
    ],
    [
        'title' => 'Learner Walkthroughs',
        'icon' => 'bi-window-stack',
        'summary' => 'Step-by-step help for enrollment, dashboards, progress tracking, and certificate downloads.',
        'tags' => ['learner', 'enroll', 'progress', 'certificate'],
    ],
    [
        'title' => 'Certification & Progress',
        'icon' => 'bi-people',
        'summary' => 'Know completion criteria, assessment flow, and how profile progress is calculated.',
        'tags' => ['progress', 'certificates', 'assessment', 'completion'],
    ],
    [
        'title' => 'Account & Security',
        'icon' => 'bi-gear',
        'summary' => 'Manage profile settings, password updates, role access, and account protection steps.',
        'tags' => ['security', 'profile', 'password', 'settings'],
    ],
    [
        'title' => 'Integration & API',
        'icon' => 'bi-cpu',
        'summary' => 'Developer guidance for API usage, authentication, and integration-ready endpoints.',
        'tags' => ['api', 'developer', 'endpoint', 'integration'],
    ],
    [
        'title' => 'Payments & Access Issues',
        'icon' => 'bi-chat-square-dots',
        'summary' => 'Fix common issues related to checkout, access permissions, and course visibility.',
        'tags' => ['payment', 'checkout', 'access', 'issue'],
    ],
    [
        'title' => 'Customer Support',
        'icon' => 'bi-headset',
        'summary' => 'Reach support channels for urgent help, escalation, and general service assistance.',
        'tags' => ['support', 'helpdesk', 'contact', 'escalation'],
    ],
];

if (!$supportIsEmbedded) {
    require_once(__DIR__ . '/../includes/header.php');
    require_once(__DIR__ . '/../includes/navbar.php');
}
?>

<main class="support-page<?php echo $supportIsEmbedded ? ' support-page--embedded' : ''; ?>">
    <section class="support-hero" data-support-reveal>
        <img class="support-hero__image" src="<?php echo BASE_URL; ?>assets/images/support/support-hero.jpg" alt="Support team helping users with product guidance">
        <div class="support-hero__overlay" aria-hidden="true"></div>
        <div class="support-hero__content">
            <h1>Get help with EduSkill courses, enrollments, provider tools, and more</h1>
            <div class="support-hero__search-wrap">
                <input type="search" id="supportSearchInput" placeholder="Search enrollment, certificates, provider setup, payment, API..." aria-label="Search support topics">
                <span class="support-hero__search-icon" aria-hidden="true"><i class="bi bi-search"></i></span>
            </div>
        </div>
    </section>

    <section class="support-topics" aria-label="Support knowledge categories">
        <div class="support-topics__grid" id="supportTopicsGrid">
            <?php foreach ($supportTopics as $topic): ?>
                <?php
                $title = (string) ($topic['title'] ?? 'Topic');
                $icon = (string) ($topic['icon'] ?? 'bi-info-circle');
                $summary = (string) ($topic['summary'] ?? 'Helpful information is available for this topic.');
                $tags = (array) ($topic['tags'] ?? []);
                $keywords = strtolower(trim($title . ' ' . $summary . ' ' . implode(' ', $tags)));
                ?>
                <article class="support-topic-card" data-support-item data-support-reveal data-keywords="<?php echo htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="support-topic-card__icon" aria-hidden="true">
                        <i class="bi <?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo htmlspecialchars($summary, ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="support-topics__empty" id="supportEmptyState" hidden>No support topics matched your search. Try another keyword.</p>
    </section>
</main>

<?php if (!$supportIsEmbedded): ?>
<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
<?php endif; ?>
