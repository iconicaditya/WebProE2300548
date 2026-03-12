<?php
/**
 * Hero section - EduSkill Marketplace
 */
?>
<section class="hero-section-new hero-ad-banner" style="background-image: url('<?php echo BASE_URL; ?>assets/images/herobg.png');">
    <div class="hero-overlay-gradient"></div>
    
    <div class="hero-content-wrapper">
        <div class="hero-text-content">
            <h1 class="hero-heading">Master New Skills in<br><span class="hero-highlight" id="typingText">the Digital Age</span></h1>
            <p class="hero-subheading">Connect with industry experts, learn hands-on skills, and advance your career at your own pace</p>
            
            <div class="hero-buttons">
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn-primary-hero">Start Learning</a>
                <a href="<?php echo BASE_URL; ?>auth/register-provider.php" class="btn-secondary-hero">Become an Instructor</a>
            </div>
        </div>
    </div>
</section>

<style>
    /* Ad-size hero: compact height with balanced vertical alignment */
    .hero-section-new.hero-ad-banner {
        min-height: clamp(220px, 32vw, 300px) !important;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        align-items: center !important;
    }

    .hero-ad-banner .hero-content-wrapper {
        min-height: 100% !important;
        max-width: 520px !important;
        justify-content: center !important;
    }

    .hero-ad-banner .hero-heading {
        font-size: clamp(1.5rem, 3vw, 2.05rem) !important;
        margin-bottom: 8px !important;
        line-height: 1.08 !important;
    }

    .hero-ad-banner .hero-subheading {
        font-size: clamp(0.74rem, 1.15vw, 0.88rem) !important;
        margin-bottom: 10px !important;
        line-height: 1.35 !important;
        max-width: 44ch;
    }

    .hero-ad-banner .hero-buttons {
        gap: 8px !important;
    }

    .hero-ad-banner .btn-primary-hero,
    .hero-ad-banner .btn-secondary-hero {
        padding: 8px 14px !important;
        font-size: 0.78rem !important;
        min-height: 34px;
    }

    @media (max-width: 1024px) {
        .hero-section-new.hero-ad-banner {
            min-height: clamp(200px, 30vw, 260px) !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        .hero-ad-banner .hero-content-wrapper {
            max-width: 480px !important;
        }
    }

    @media (max-width: 768px) {
        .hero-section-new.hero-ad-banner {
            min-height: clamp(180px, 34vh, 230px) !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
        }

        .hero-ad-banner .hero-heading {
            font-size: clamp(1.25rem, 5.5vw, 1.55rem) !important;
            margin-bottom: 7px !important;
        }

        .hero-ad-banner .hero-subheading {
            font-size: 0.76rem !important;
            margin-bottom: 9px !important;
            line-height: 1.3 !important;
        }

        .hero-ad-banner .btn-primary-hero,
        .hero-ad-banner .btn-secondary-hero {
            padding: 7px 12px !important;
            font-size: 0.74rem !important;
            min-height: 32px;
        }
    }
</style>