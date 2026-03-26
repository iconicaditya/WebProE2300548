<?php
/**
 * Advanced About section for homepage.
 */
?>

<section class="about-pro-section" id="aboutSection" aria-label="About EduSkill Marketplace">
    <span class="about-pro-orb about-pro-orb--one" aria-hidden="true"></span>
    <span class="about-pro-orb about-pro-orb--two" aria-hidden="true"></span>

    <div class="container">
        <div class="about-pro-grid">
            <div class="about-pro-content">
                <span class="about-pro-kicker" data-reveal>About EduSkill</span>
                <h2 class="about-pro-title" data-reveal>Shaping Future-Ready Professionals Through Applied Learning</h2>
                <p class="about-pro-lead" data-reveal>
                    EduSkill Marketplace bridges the gap between ambition and employability by connecting learners with expert mentors,
                    practical projects, and market-relevant career pathways. Our ecosystem is designed for measurable growth,
                    real-world confidence, and long-term professional success.
                </p>

                <div class="about-pro-pillars">
                    <article class="about-pro-pillar" data-reveal>
                        <div class="about-pro-pillar-icon"><i class="bi bi-rocket-takeoff-fill" aria-hidden="true"></i></div>
                        <div>
                            <h3>Outcome-Driven Programs</h3>
                            <p>Structured curricula aligned with hiring needs, portfolios, and practical outcomes.</p>
                        </div>
                    </article>
                    <article class="about-pro-pillar" data-reveal>
                        <div class="about-pro-pillar-icon"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i></div>
                        <div>
                            <h3>Industry Collaboration</h3>
                            <p>Strategic partnerships with institutes and experts to keep learning pathways current.</p>
                        </div>
                    </article>
                </div>

                <div class="about-pro-metrics" data-reveal>
                    <div class="about-pro-metric">
                        <h4 class="about-pro-counter" data-target="12000" data-suffix="+">0</h4>
                        <p>Learners Upskilled</p>
                    </div>
                    <div class="about-pro-metric">
                        <h4 class="about-pro-counter" data-target="85" data-suffix="%">0</h4>
                        <p>Completion Rate</p>
                    </div>
                    <div class="about-pro-metric">
                        <h4 class="about-pro-counter" data-target="150" data-suffix="+">0</h4>
                        <p>Partner Institutions</p>
                    </div>
                </div>

                <div class="about-pro-actions" data-reveal>
                    <a href="<?php echo BASE_URL; ?>pages/allcources.php" class="btn btn-primary">Explore Learning Tracks</a>
                    <a href="<?php echo BASE_URL; ?>auth/register-provider.php" class="btn btn-outline-primary">Become a Training Partner</a>
                </div>
            </div>

            <div class="about-pro-visual" data-reveal>
                <div class="about-pro-gallery">
                    <figure class="about-pro-photo about-pro-photo--one" data-depth="16">
                        <img src="<?php echo BASE_URL; ?>assets/images/about/about-person-1.jpg" alt="Professional mentor leading a strategy session">
                    </figure>
                    <figure class="about-pro-photo about-pro-photo--two" data-depth="12">
                        <img src="<?php echo BASE_URL; ?>assets/images/about/about-person-2.jpg" alt="Skilled professional sharing project insights with learners">
                    </figure>
                    <figure class="about-pro-photo about-pro-photo--three" data-depth="18">
                        <img src="<?php echo BASE_URL; ?>assets/images/about/about-person-3.jpg" alt="Trainer coaching learners in a practical workshop">
                    </figure>
                    <figure class="about-pro-photo about-pro-photo--four" data-depth="10">
                        <img src="<?php echo BASE_URL; ?>assets/images/about/about-person-4.jpg" alt="Learning advisor planning modern career pathways">
                    </figure>

                    <aside class="about-pro-badge" data-depth="8">
                        <span class="about-pro-badge-label">Trusted Learning Network</span>
                        <strong>Career-Focused Education</strong>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</section>
