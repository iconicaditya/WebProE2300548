<?php
/**
 * Testimonials section for homepage.
 */

$testimonials = [
    [
        'name' => 'Aarav Sharma',
        'role' => 'Frontend Developer Trainee',
        'location' => 'Kathmandu',
        'image' => 'assets/images/testimonials/person-01.jpg',
        'quote' => 'The mentor feedback loops and project reviews were outstanding. I built a portfolio that directly helped me secure my first internship.',
        'rating' => 5,
    ],
    [
        'name' => 'Sofia Bhandari',
        'role' => 'Digital Marketing Specialist',
        'location' => 'Pokhara',
        'image' => 'assets/images/testimonials/person-02.jpg',
        'quote' => 'The learning pathway was structured, practical, and highly relevant. I applied campaign strategies immediately in my workplace.',
        'rating' => 5,
    ],
    [
        'name' => 'Ritvik Gurung',
        'role' => 'Data Analyst Learner',
        'location' => 'Butwal',
        'image' => 'assets/images/testimonials/person-03.jpg',
        'quote' => 'The analytics tracks covered exactly what recruiters ask for. The capstone project made my resume stand out in interviews.',
        'rating' => 5,
    ],
    [
        'name' => 'Nisha KC',
        'role' => 'UI/UX Designer',
        'location' => 'Lalitpur',
        'image' => 'assets/images/testimonials/person-04.jpg',
        'quote' => 'From user research to polished prototypes, every module felt modern and industry-ready. The live critique sessions were game-changing.',
        'rating' => 5,
    ],
    [
        'name' => 'Prabin Thapa',
        'role' => 'Cloud Engineering Enthusiast',
        'location' => 'Chitwan',
        'image' => 'assets/images/testimonials/person-05.jpg',
        'quote' => 'Hands-on labs and real deployment tasks gave me confidence to work on production-like cloud projects with clarity.',
        'rating' => 5,
    ],
    [
        'name' => 'Anisha Lama',
        'role' => 'Cybersecurity Learner',
        'location' => 'Bhaktapur',
        'image' => 'assets/images/testimonials/person-06.jpg',
        'quote' => 'The simulated security exercises were excellent. I gained practical incident response skills instead of just theoretical knowledge.',
        'rating' => 5,
    ],
    [
        'name' => 'Suman Adhikari',
        'role' => 'Business Analyst Trainee',
        'location' => 'Biratnagar',
        'image' => 'assets/images/testimonials/person-07.jpg',
        'quote' => 'I loved the clarity of the modules and templates. They improved my communication and problem-solving at work.',
        'rating' => 5,
    ],
    [
        'name' => 'Mira Rijal',
        'role' => 'Motion Graphics Learner',
        'location' => 'Dharan',
        'image' => 'assets/images/testimonials/person-08.jpg',
        'quote' => 'The creative direction and expert reviews helped me elevate my editing style and win two freelance contracts.',
        'rating' => 5,
    ],
    [
        'name' => 'Kiran Neupane',
        'role' => 'Backend Developer',
        'location' => 'Janakpur',
        'image' => 'assets/images/testimonials/person-09.jpg',
        'quote' => 'The backend track was very practical with API architecture, security patterns, and real debugging scenarios.',
        'rating' => 5,
    ],
    [
        'name' => 'Elina Joshi',
        'role' => 'Project Coordination Professional',
        'location' => 'Hetauda',
        'image' => 'assets/images/testimonials/person-10.jpg',
        'quote' => 'I now manage teams and project timelines with far more confidence. The templates and frameworks are extremely useful.',
        'rating' => 5,
    ],
];
?>

<section class="testi-pro-section" id="testimonialsSection" aria-label="Learner testimonials">
    <span class="testi-pro-glow testi-pro-glow--one" aria-hidden="true"></span>
    <span class="testi-pro-glow testi-pro-glow--two" aria-hidden="true"></span>

    <div class="container">
        <div class="testi-pro-head" data-testi-reveal>
            <p class="testi-pro-kicker">Success Voices</p>
            <h2>What Our Learners and Professionals Say</h2>
            <p>Trusted by ambitious learners across Nepal for practical, career-focused, and confidence-building upskilling experiences.</p>
        </div>

        <div class="testi-pro-toolbar" data-testi-reveal>
            <div class="testi-pro-toolbar-text">
                <strong>10+ Verified Testimonials</strong>
                <span>Real growth stories from our active learning community</span>
            </div>
            <div class="testi-pro-nav-wrap" aria-label="Testimonial navigation controls">
                <button type="button" class="testi-pro-nav testi-pro-nav--prev" aria-label="Previous testimonials">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button type="button" class="testi-pro-nav testi-pro-nav--next" aria-label="Next testimonials">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <div class="testi-pro-viewport" id="testiViewport">
            <div class="testi-pro-track">
                <?php foreach ($testimonials as $item): ?>
                    <?php
                    $name = (string) ($item['name'] ?? 'Learner');
                    $role = (string) ($item['role'] ?? 'Professional Learner');
                    $location = (string) ($item['location'] ?? 'Nepal');
                    $image = (string) ($item['image'] ?? '');
                    $quote = (string) ($item['quote'] ?? 'Excellent learning experience.');
                    $rating = (int) ($item['rating'] ?? 5);

                    $parts = preg_split('/\s+/', trim($name));
                    $initials = '';
                    if (is_array($parts)) {
                        foreach (array_slice($parts, 0, 2) as $part) {
                            $initials .= strtoupper(substr((string) $part, 0, 1));
                        }
                    }
                    if ($initials === '') {
                        $initials = 'ED';
                    }
                    ?>
                    <article class="testi-pro-card" data-testi-reveal>
                        <div class="testi-pro-rating" aria-label="<?php echo $rating; ?> star rating">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="bi <?php echo $i < $rating ? 'bi-star-fill' : 'bi-star'; ?>" aria-hidden="true"></i>
                            <?php endfor; ?>
                        </div>

                        <p class="testi-pro-quote">“<?php echo htmlspecialchars($quote, ENT_QUOTES, 'UTF-8'); ?>”</p>

                        <div class="testi-pro-user">
                            <span class="testi-pro-avatar" aria-hidden="true">
                                <?php if ($image !== ''): ?>
                                    <img src="<?php echo htmlspecialchars(BASE_URL . ltrim($image, '/'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php else: ?>
                                    <?php echo htmlspecialchars($initials, ENT_QUOTES, 'UTF-8'); ?>
                                <?php endif; ?>
                            </span>
                            <div>
                                <h3><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p><?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
