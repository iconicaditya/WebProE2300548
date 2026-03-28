<?php
/**
 * FAQ section/page.
 * Reusable as standalone page and homepage embedded section.
 */

$faqIsEmbedded = isset($embedFaqSection) && $embedFaqSection === true;

if (!$faqIsEmbedded) {
    $pageTitle = 'Frequently Asked Questions';
    $pageStylesheet = 'faq-aaditya.css';
    $extraScripts = ['faq-aaditya.js'];

    require_once(__DIR__ . '/../includes/header.php');
    require_once(__DIR__ . '/../includes/navbar.php');
}

$faqItems = [
    [
        'question' => 'How do I enroll in a course and start learning?',
        'answer' => 'Open the course details page, click Enroll or Add to Cart, complete checkout, and your course will appear in your learner dashboard immediately. You can begin lessons right away from the My Courses area.',
    ],
    [
        'question' => 'Can I access EduSkill courses on both mobile and desktop devices?',
        'answer' => 'Yes. EduSkill is fully responsive for modern mobile, tablet, and desktop browsers. Your progress is synced across devices, so you can continue learning wherever you left off.',
    ],
    [
        'question' => 'When and how do I receive my certificate?',
        'answer' => 'Certificates are generated after you complete all required lessons and assessments for a course. Once eligible, you can download your certificate directly from the Certificates section in your learner portal.',
    ],
    [
        'question' => 'How can a training provider publish a new course?',
        'answer' => 'Providers can create a course from the provider dashboard by completing basic details, course media, modules, pricing, and publish settings. After review readiness, the course can be made live for learner enrollment.',
    ],
    [
        'question' => 'Are payments secure and what methods are supported?',
        'answer' => 'EduSkill follows secure checkout practices and protects transaction flows. Supported payment options depend on available gateway integrations, and successful payments automatically unlock eligible course access.',
    ],
    [
        'question' => 'How do I contact support for account or technical issues?',
        'answer' => 'Visit the Support page to review help topics or contact assistance for login problems, payment concerns, or course access issues. Include relevant details for faster resolution and escalation if needed.',
    ],
];
?>

<main class="faq-page<?php echo $faqIsEmbedded ? ' faq-page--embedded' : ''; ?>">
    <section class="faq-pro-section" id="faqSection" aria-label="Frequently asked questions">
        <span class="faq-pro-glow faq-pro-glow--one" aria-hidden="true"></span>
        <span class="faq-pro-glow faq-pro-glow--two" aria-hidden="true"></span>

        <div class="container">
            <div class="faq-pro-head" data-faq-reveal>
                <p class="faq-pro-kicker">Knowledge Base</p>
                <h1>Frequently Asked Questions</h1>
                <p>Clear answers for learners and providers on enrollment, certificates, payments, publishing, and support workflows.</p>
            </div>

            <div class="faq-pro-list" id="faqProList">
                <?php foreach ($faqItems as $index => $faq): ?>
                    <?php
                    $question = (string) ($faq['question'] ?? 'Question');
                    $answer = (string) ($faq['answer'] ?? 'Answer will be available soon.');
                    $itemNumber = $index + 1;
                    $questionId = 'faq-question-' . $itemNumber;
                    $answerId = 'faq-answer-' . $itemNumber;
                    $isOpen = $index === 0;
                    ?>
                    <article class="faq-pro-item<?php echo $isOpen ? ' is-open' : ''; ?>" data-faq-item data-faq-reveal>
                        <h2 class="faq-pro-question">
                            <button
                                type="button"
                                class="faq-pro-trigger"
                                id="<?php echo htmlspecialchars($questionId, ENT_QUOTES, 'UTF-8'); ?>"
                                aria-expanded="<?php echo $isOpen ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo htmlspecialchars($answerId, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <span class="faq-pro-question-text"><?php echo htmlspecialchars($question, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="faq-pro-icon" aria-hidden="true">
                                    <i class="bi bi-plus-lg"></i>
                                </span>
                            </button>
                        </h2>

                        <div
                            class="faq-pro-answer-wrap"
                            id="<?php echo htmlspecialchars($answerId, ENT_QUOTES, 'UTF-8'); ?>"
                            role="region"
                            aria-labelledby="<?php echo htmlspecialchars($questionId, ENT_QUOTES, 'UTF-8'); ?>"
                            aria-hidden="<?php echo $isOpen ? 'false' : 'true'; ?>"
                        >
                            <div class="faq-pro-answer">
                                <p><?php echo htmlspecialchars($answer, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="faq-pro-help" data-faq-reveal>
                <div class="faq-pro-help__content">
                    <h2>Still need help?</h2>
                    <p>Our support team can assist with enrollment, provider onboarding, and account recovery workflows.</p>
                </div>
                <div class="faq-pro-help__actions">
                    <a href="<?php echo BASE_URL; ?>pages/support.php" class="btn btn-primary">Contact Support</a>
                    <a href="<?php echo BASE_URL; ?>pages/allcources.php" class="btn btn-outline-primary">Browse Courses</a>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php if (!$faqIsEmbedded): ?>
<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
<?php endif; ?>

