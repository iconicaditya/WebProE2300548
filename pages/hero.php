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
        min-height: clamp(220px, 30vw, 300px) !important;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        padding-left: clamp(12px, 2vw, 24px) !important;
        padding-right: clamp(12px, 2vw, 24px) !important;
        align-items: center !important;
    }

    .hero-ad-banner .hero-content-wrapper {
        min-height: 100% !important;
        width: min(100%, 620px) !important;
        max-width: 620px !important;
        padding-inline: clamp(4px, 1vw, 12px) !important;
        justify-content: center !important;
    }

    .hero-ad-banner .hero-text-content {
        max-width: 56ch;
    }

    .hero-ad-banner .hero-heading {
        font-size: clamp(1.45rem, 2.7vw, 2rem) !important;
        margin-bottom: 8px !important;
        line-height: 1.12 !important;
        text-wrap: balance;
    }

    .hero-ad-banner .hero-subheading {
        font-size: clamp(0.78rem, 1.05vw, 0.92rem) !important;
        margin-bottom: 10px !important;
        line-height: 1.4 !important;
        max-width: 50ch;
    }

    .hero-ad-banner .hero-buttons {
        gap: 8px !important;
        align-items: center;
    }

    .hero-ad-banner .btn-primary-hero,
    .hero-ad-banner .btn-secondary-hero {
        padding: 8px 14px !important;
        font-size: 0.78rem !important;
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    @media (max-width: 1024px) {
        .hero-section-new.hero-ad-banner {
            min-height: clamp(200px, 30vw, 260px) !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        .hero-ad-banner .hero-content-wrapper {
            width: min(100%, 560px) !important;
            max-width: 560px !important;
        }

        .hero-ad-banner .hero-heading {
            font-size: clamp(1.35rem, 3.3vw, 1.8rem) !important;
        }

        .hero-ad-banner .hero-subheading {
            max-width: 48ch;
        }
    }

    @media (max-width: 768px) {
        .hero-section-new.hero-ad-banner {
            min-height: clamp(220px, 38vh, 280px) !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

        .hero-ad-banner .hero-heading {
            font-size: clamp(1.2rem, 5.6vw, 1.5rem) !important;
            margin-bottom: 8px !important;
        }

        .hero-ad-banner .hero-subheading {
            font-size: 0.8rem !important;
            margin-bottom: 10px !important;
            line-height: 1.35 !important;
            max-width: 100%;
        }

        .hero-ad-banner .hero-buttons {
            width: 100%;
            gap: 10px !important;
            justify-content: flex-start;
        }

        .hero-ad-banner .btn-primary-hero,
        .hero-ad-banner .btn-secondary-hero {
            min-height: 40px;
            padding: 9px 14px !important;
            font-size: 0.78rem !important;
        }
    }

    @media (max-width: 560px) {
        .hero-section-new.hero-ad-banner {
            min-height: clamp(240px, 46vh, 320px) !important;
            padding-top: 14px !important;
            padding-bottom: 14px !important;
            justify-content: center !important;
        }

        .hero-ad-banner .hero-content-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            padding-inline: 4px !important;
        }

        .hero-ad-banner .hero-text-content {
            width: 100%;
        }

        .hero-ad-banner .hero-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .hero-ad-banner .btn-primary-hero,
        .hero-ad-banner .btn-secondary-hero {
            width: 100%;
            font-size: 0.82rem !important;
        }
    }
</style>
