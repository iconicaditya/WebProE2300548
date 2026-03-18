<?php
/**
 * Custom Navbar matching screenshot (two layers, logo only, no border, no subtitle)
 */
?>
<!-- EduSkill Custom Navbar -->
<nav class="eduskill-navbar" style="background:#fff; box-shadow:none; border:none; padding:0;">
  <div class="navbar-container" style="background:#fff; display:flex; align-items:center; justify-content:space-between; min-height:64px; border:none; box-shadow:none; padding:0 8px;">
    <!-- Logo only -->
    <a class="eduskill-logo" href="<?php echo BASE_URL; ?>" style="display:flex; align-items:center; height:64px;">
      <img src="<?php echo BASE_URL; ?>assets/images/logo-eduskill.png" alt="EDUSKILL Logo" style="height:36px; width:auto; display:block;">
    </a>
    <button type="button" class="mobile-menu-toggle" aria-label="Toggle navigation" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </button>
    <!-- Search -->
    <div class="eduskill-search" style="position:relative; flex:0 1 520px; margin:0 8px; max-width:520px;">
      <span class="search-icon" style="position:absolute; left:18px; top:50%; transform:translateY(-50%);">
        <svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="8" r="7" stroke="#b0b7c3" stroke-width="2"/><path d="M13.5 13.5L17 17" stroke="#b0b7c3" stroke-width="2" stroke-linecap="round"/></svg>
      </span>
      <input type="text" placeholder="Search for anything" style="width:100%; max-width:100%; border-radius:30px; border:2px solid #d1d7dc; padding:8px 36px 8px 34px; font-size:1rem; background:#fff; outline:none; transition:border 0.2s; box-shadow:none;">
    </div>
    <!-- Right links and actions -->
    <div class="navbar-actions" style="display:flex; align-items:center; gap:12px;">
      <a href="<?php echo BASE_URL; ?>index.php" style="color:#6a6f73; font-size:1.05rem; text-decoration:none; margin-right:8px; display:inline-flex; align-items:center; gap:8px;"> 
        <span class="nav-link-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5z" stroke="#6a6f73" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        Home
      </a>
      <a href="<?php echo BASE_URL; ?>pages/allcources.php" style="color:#6a6f73; font-size:1.05rem; text-decoration:none; margin-right:8px; display:inline-flex; align-items:center; gap:8px;"> 
        <span class="nav-link-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6z" stroke="#6a6f73" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 10h8" stroke="#6a6f73" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        All Courses
      </a>
      <span class="cart-icon" title="Cart" style="font-size:1.3rem; color:#333; margin:0 4px; cursor:pointer;">
        <svg width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 19a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm10 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM2 3h2.27a1 1 0 0 1 .98.8l2.1 10.5a1 1 0 0 0 .98.8h7.72a1 1 0 0 0 .98-.8l1.38-6.5H5.25" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </span>
      <span class="wishlist-icon" title="Wishlist" style="font-size:1.25rem; color:#333; margin:0 6px; cursor:pointer; display:inline-flex; align-items:center;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 6l-1.3-1.4a5.3 5.3 0 0 0-7.5 0 5.5 5.5 0 0 0 0 7.8L12 22l8.8-9.6a5.5 5.5 0 0 0 0-7.8z" stroke="#333" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </span>
      <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-login text-decoration-none" style="background:#fff; color:#4186a0; border:1.5px solid #4186a0; border-radius:0; font-weight:600; font-size:0.95rem; padding:6px 12px; text-decoration:none;">Log in</a>
      <div class="register-menu" style="position:relative; display:inline-block;">
        <a href="#" class="btn btn-signup text-decoration-none" aria-expanded="false" style="background:#4186a0; color:#fff; border:none; border-radius:0; font-weight:600; font-size:0.95rem; padding:6px 12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
          <span>Register</span>
          <span class="register-chevron" aria-hidden="true" style="display:inline-flex;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
        </a>
        <div class="register-dropdown" style="display:none; position:absolute; right:0; top:100%; margin-top:8px; min-width:230px; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.12); z-index:1000; padding:8px;">
          <a href="<?php echo BASE_URL; ?>auth/register-learner.php" style="display:block; padding:10px 12px; color:#1f2937; text-decoration:none; border-radius:8px; font-weight:500;">Register as Learner</a>
          <a href="<?php echo BASE_URL; ?>auth/register-provider.php" style="display:block; padding:10px 12px; color:#1f2937; text-decoration:none; border-radius:8px; font-weight:500;">Register as Provider</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Category Links Layer -->
  <div class="navbar-links" style="background:#4186a0; display:flex; align-items:center; min-height:44px; gap:28px;">
    <div style="width:calc(100% - 16px); max-width:1400px; margin:0 auto; padding:0 8px; display:flex; align-items:center; gap:24px;">
    <a href="#">Programming</a>
    <a href="#">Bussiness</a>
    <a href="#">Cyber security</a>
    <a href="#">Data Science</a>
    <a href="#">Digital Marketing</a>
    <a href="#">DevOps</a>
    <a href="#">cloud</a>
    <a href="#">Project Management</a>
    <span class="more-link">......More</span>
    </div>
  </div>
</nav>

<style>
  /* Force navbar fixed positioning and ensure content isn't hidden */
  .eduskill-navbar {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1050 !important;
    width: 100% !important;
    --theme-color: #4186a0;
    --theme-color-dark: #2f728a;
  }

  .register-menu.open .register-dropdown {
    display: block !important;
  }

  .register-menu .register-chevron {
    transition: transform .18s ease;
  }

  .register-menu.open .register-chevron {
    transform: rotate(180deg);
  }

  .navbar-actions .btn-login:hover {
    background: var(--theme-color) !important;
    color: #fff !important;
    border-color: var(--theme-color) !important;
  }

  .navbar-actions .btn-signup:hover {
    background: var(--theme-color-dark) !important;
    color: #fff !important;
    border-color: var(--theme-color-dark) !important;
  }

  .register-dropdown a,
  .register-dropdown a:visited {
    color: #1f2937 !important;
    background: transparent;
    border: none !important;
    box-shadow: none !important;
    -webkit-tap-highlight-color: transparent;
    transition: background-color .16s ease, color .16s ease;
  }

  .register-dropdown a + a {
    border-top: 1px solid #edf1f5;
  }

  .register-dropdown a:hover,
  .register-dropdown a:focus,
  .register-dropdown a:focus-visible {
    background: #eef6fa;
    color: var(--theme-color-dark) !important;
    border: none !important;
    box-shadow: none !important;
    outline: none;
  }

  .register-dropdown a:active {
    background: #e2eef5;
    color: #1f4f62 !important;
  }

  .mobile-menu-toggle {
    display: none;
    background: #fff;
    border: 1.5px solid var(--theme-color);
    border-radius: 8px;
    padding: 8px 10px;
    margin-right: 12px;
    flex-direction: column;
    gap: 4px;
  }

  .mobile-menu-toggle span {
    display: block;
    width: 20px;
    height: 2px;
    background: var(--theme-color);
  }

  /* Cart & Wishlist spacing on larger screens */
  .navbar-actions .cart-icon,
  .navbar-actions .wishlist-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: color .12s ease, transform .08s ease;
  }

  .navbar-actions .wishlist-icon:hover {
    color: #ff6b81;
    transform: translateY(-1px);
  }

  /* Small inline icon used in top-right links (Home / All Courses) */
  .nav-link-icon {
    display: inline-flex;
    width: 18px;
    height: 18px;
    align-items: center;
    justify-content: center;
    flex: 0 0 18px;
    opacity: 0.95;
  }
  .nav-link-icon svg { display:block; }

  /* Logo entrance and hover animation */
  @keyframes logo-enter {
    0% { transform: translateY(-6px) scale(.98); opacity: 0; }
    60% { transform: translateY(2px) scale(1.02); opacity: 1; }
    100% { transform: translateY(0) scale(1); opacity: 1; }
  }

  .eduskill-logo img {
    transition: transform .28s cubic-bezier(.2,.9,.2,1), filter .20s ease;
    will-change: transform;
    animation: logo-enter .8s ease both;
    display: block !important;
  }

  .eduskill-logo img:hover {
    transform: scale(1.06) translateY(-2px);
    filter: drop-shadow(0 6px 14px rgba(65,134,160,0.12));
  }

  /* Category link subtle marker + underline slide for a professional look */
  .navbar-links a {
    color: #fff;
    text-decoration: none;
    position: relative;
    padding: 6px 6px;
    display: inline-block;
    transition: color .16s ease, transform .12s ease;
    font-weight: 600;
  }

  .navbar-links a::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 50%;
    transform: translateY(-50%) scale(.6);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    transition: transform .18s ease, background .18s ease, left .18s ease;
  }

  .navbar-links a::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -6px;
    height: 2px;
    background: linear-gradient(90deg, rgba(255,255,255,0.14), rgba(255,255,255,0));
    transform: scaleX(0);
    transform-origin: left center;
    transition: transform .22s cubic-bezier(.2,.9,.2,1);
  }

  .navbar-links a:hover {
    color: #fff;
    transform: translateY(-2px);
  }

  .navbar-links a:hover::before {
    left: -14px;
    transform: translateY(-50%) scale(1);
    background: rgba(255,255,255,0.26);
  }

  .navbar-links a:hover::after {
    transform: scaleX(1);
  }

  /* Gentle icon hover for cart and wishlist */
  .navbar-actions .cart-icon:hover,
  .navbar-actions .wishlist-icon:hover {
    transform: translateY(-2px) scale(1.04);
    color: var(--theme-color-dark);
  }

  @media (max-width: 991px) {
    .mobile-menu-toggle {
      display: inline-flex;
    }

    .eduskill-navbar .navbar-container {
      flex-wrap: wrap;
      padding: 8px 10px !important;
      gap: 10px;
    }

    .eduskill-navbar .eduskill-logo {
      height: auto !important;
      margin-right: auto !important;
      flex: 0 0 auto;
      order: 1;
    }

    .eduskill-navbar .mobile-menu-toggle {
      margin-left: auto;
      margin-right: 0;
      flex: 0 0 auto;
      order: 2;
    }

    .eduskill-navbar .eduskill-search,
    .eduskill-navbar .navbar-actions,
    .eduskill-navbar .navbar-links {
      display: none !important;
      width: 100%;
      flex: 0 0 100%;
    }

    .eduskill-navbar.mobile-open .eduskill-search,
    .eduskill-navbar.mobile-open .navbar-actions,
    .eduskill-navbar.mobile-open .navbar-links {
      display: flex !important;
    }

    .eduskill-navbar.mobile-open .eduskill-search {
      margin: 4px 0 0 0 !important;
      max-width: 100% !important;
      order: 3;
    }

    .eduskill-navbar.mobile-open .navbar-actions {
      order: 4;
      display: grid !important;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      gap: 10px 10px !important;
      margin: 0 !important;
      padding: 10px;
      background: #fff;
      border: 1px solid #d9e2e7;
      border-radius: 10px;
    }

    .eduskill-navbar.mobile-open .navbar-actions > a:nth-of-type(1),
    .eduskill-navbar.mobile-open .navbar-actions > a:nth-of-type(2) {
      grid-column: 1 / -1;
      font-size: 1.12rem !important;
      text-align: center;
      margin: 0 !important;
      padding: 2px 0;
    }

    .eduskill-navbar.mobile-open .navbar-actions > a,
    .eduskill-navbar.mobile-open .navbar-actions .btn,
    .eduskill-navbar.mobile-open .navbar-actions .register-menu {
      width: 100%;
      margin: 0 !important;
    }

    .eduskill-navbar.mobile-open .navbar-actions .cart-icon {
      display: none;
    }

    .eduskill-navbar.mobile-open .register-menu {
      position: static !important;
      grid-column: 2;
    }

    .eduskill-navbar.mobile-open .register-menu .btn-signup,
    .eduskill-navbar.mobile-open .btn-login {
      width: 100%;
      min-height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.02rem !important;
      border-radius: 0 !important;
    }

    .eduskill-navbar.mobile-open .btn-login {
      grid-column: 1;
    }

    .eduskill-navbar.mobile-open .register-dropdown {
      display: none !important;
      position: static !important;
      right: auto !important;
      top: auto !important;
      margin-top: 8px !important;
      min-width: 100% !important;
      box-shadow: none !important;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #f8fafb;
      padding: 6px;
      grid-column: 1 / -1;
    }

    .eduskill-navbar.mobile-open .register-menu.open .register-dropdown {
      display: block !important;
    }

    .eduskill-navbar.mobile-open .register-dropdown a {
      font-size: 1.05rem;
      text-align: center;
    }

    .eduskill-navbar.mobile-open .navbar-links {
      order: 5;
      flex-wrap: wrap;
      gap: 8px !important;
      padding: 10px !important;
      min-height: auto !important;
      justify-content: flex-start;
      border-radius: 10px;
      overflow: hidden;
    }

    .eduskill-navbar.mobile-open .navbar-links a {
      font-size: .84rem;
      background: rgba(255,255,255,0.14);
      padding: 4px 8px;
      border-radius: 8px;
    }
  }
</style>

<script>
  (function () {
    const navbar = document.querySelector('.eduskill-navbar');
    if (!navbar) return;
    const toggleButton = navbar.querySelector('.mobile-menu-toggle');
    const registerMenu = navbar.querySelector('.register-menu');
    const registerTrigger = registerMenu ? registerMenu.querySelector('.btn-signup') : null;
    if (!toggleButton) return;

    toggleButton.addEventListener('click', function () {
      const isOpen = navbar.classList.toggle('mobile-open');
      toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (!isOpen && registerMenu) {
        registerMenu.classList.remove('open');
      }
    });

    if (registerTrigger && registerMenu) {
      registerTrigger.addEventListener('click', function (event) {
        event.preventDefault();
        const isRegisterOpen = registerMenu.classList.toggle('open');
        registerTrigger.setAttribute('aria-expanded', isRegisterOpen ? 'true' : 'false');
      });

      document.addEventListener('click', function (event) {
        if (!registerMenu.contains(event.target)) {
          registerMenu.classList.remove('open');
          registerTrigger.setAttribute('aria-expanded', 'false');
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          registerMenu.classList.remove('open');
          registerTrigger.setAttribute('aria-expanded', 'false');
        }
      });
    }
  })();
</script>
