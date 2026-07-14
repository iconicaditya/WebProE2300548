<div align="center">
  <br />
  <a href="https://chiyanepal.vercel.app">
    <img src="favicon.ico" alt="EduSkill Logo" width="72" height="72" style="border-radius: 12px;" />
  </a>
  <br />
  <h1 align="center" style="font-size: 2.5rem; font-weight: 700; letter-spacing: -0.02em;">EduSkill Marketplace System (EMS)</h1>
  <p align="center" style="font-size: 1.1rem; max-width: 600px; color: #666;">
    A multi-role web platform connecting training providers with learners — enabling course discovery, enrollment, progress tracking, and administrative oversight.
  </p>
  <br />
  <p align="center">
    <a href="https://chiyanepal.vercel.app"><strong>🌐 Live Demo</strong></a> &nbsp;·&nbsp;
    <a href="#-features"><strong>Features</strong></a> &nbsp;·&nbsp;
    <a href="#-user-roles"><strong>Roles</strong></a> &nbsp;·&nbsp;
    <a href="#-tech-stack"><strong>Tech Stack</strong></a> &nbsp;·&nbsp;
    <a href="#-getting-started"><strong>Setup</strong></a>
  </p>

  <p align="center">
    <img src="https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&style=flat-square" alt="PHP 8" />
    <img src="https://img.shields.io/badge/MySQL-4479A1?logo=mysql&style=flat-square" alt="MySQL" />
    <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&style=flat-square" alt="Bootstrap 5" />
    <img src="https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&style=flat-square" alt="JavaScript" />
    <img src="https://img.shields.io/badge/HTML5-E34F26?logo=html5&style=flat-square" alt="HTML5" />
    <img src="https://img.shields.io/badge/CSS3-1572B6?logo=css3&style=flat-square" alt="CSS3" />
    <img src="https://img.shields.io/badge/XAMPP-FB7A24?logo=xampp&style=flat-square" alt="XAMPP" />
    <img src="https://img.shields.io/badge/Version-1.2.4-2563eb?style=flat-square" alt="Version 1.2.4" />
  </p>

  <p align="center">
    <sub>📘 BIT210 Web Programming Project · Assignment by Group 1</sub>
  </p>
</div>

---

## 📖 Overview

**EduSkill Marketplace System (EMS)** is a web-based platform that bridges the gap between training providers and learners. Providers can register, create, and manage course listings, while learners can browse, filter, enroll in courses, and track their learning progress. An admin officer oversees the ecosystem — approving provider registrations, managing users, and generating reports.

The system solves a real-world problem: fragmented skill development and lack of a centralized marketplace where quality training content meets motivated learners. EMS provides a unified interface for course discovery, enrollment, payment handling, and progress tracking.

---

## 👥 User Roles

| Role | Description |
|---|---|
| **Learner** | Browse courses, filter by category/level/price/instructor, enroll, add to cart/wishlist, track progress, earn certificates, manage profile |
| **Training Provider** | Register as an instructor, create and manage courses with curriculum, sections, and lessons; view student enrollments, analytics, and reviews |
| **Admin Officer** | Approve provider registrations, manage all users (learners & providers), oversee course listings, view analytical reports and system statistics |

---

## ✨ Features

### 🌐 Public Website
| Feature | Details |
|---|---|
| **Dynamic Hero Section** | Background image with animated typing effect and dual call-to-action (Start Learning / Become an Instructor) |
| **Course Catalog** | Grid layout with search, category chips, level filter, instructor dropdown with search, and price range filter |
| **Course Details Page** | Full layout with overview, curriculum (expandable sections with lessons), reviews, instructor bio, sidebar with pricing, enrollment, cart/wishlist, course info grid (duration, lessons, level, language, certificate, students), and share buttons |
| **Partnership Institutes** | Showcase of partner organizations and institutions |
| **Testimonials** | User reviews and success stories |
| **FAQ Section** | Expandable frequently asked questions |
| **Support Section** | Contact and support information |
| **Responsive Design** | Fully adaptive layout from desktop to mobile with breakpoints at 1280px, 1080px, 900px, 640px, and 520px |

### 🔐 Learner Dashboard
| Feature | Details |
|---|---|
| **Dashboard Overview** | Enrolled courses, progress summary, and quick stats |
| **Course Management** | View enrolled courses with progress tracking |
| **Cart & Wishlist** | Add/remove courses, save for later |
| **Payment History** | View past payments and receipts |
| **Certificates** | Downloadable course completion certificates |
| **Profile Management** | Update personal details, profile image upload |
| **Messages** | Communication with instructors |
| **Security Settings** | Password management and account security |

### 🏫 Provider Dashboard
| Feature | Details |
|---|---|
| **Dashboard Overview** | Course performance, enrollment stats, and revenue summaries |
| **Course Management** | Full CRUD for courses with sections, lessons, pricing, and media upload |
| **Student Management** | View enrolled students and their progress |
| **Analytics** | Performance metrics and visualizations |
| **Reviews** | View and respond to student reviews |
| **Payment Management** | Track earnings and payouts |
| **Profile** | Instructor profile with bio, image, and credentials |

### 🔧 Admin Dashboard
| Feature | Details |
|---|---|
| **Dashboard Overview** | System-wide statistics and activity summaries |
| **Course Management** | Oversee all courses across providers |
| **Learner Management** | Manage learner accounts, approvals, and status |
| **Provider Management** | Approve/block provider registrations and manage accounts |
| **User Management** | Comprehensive user administration across all roles |
| **Analytics & Reports** | System-wide analytical reports and data exports |
| **Settings** | System configuration and preferences |

---

## 🛠 Tech Stack

<div align="center">

| Category | Technology |
|---|---|
| **Backend** | [PHP 8.x](https://www.php.net/) (procedural, with prepared statements) |
| **Database** | [MySQL](https://www.mysql.com/) (via XAMPP) |
| **Frontend** | [HTML5](https://developer.mozilla.org/en-US/docs/Web/HTML) / [CSS3](https://developer.mozilla.org/en-US/docs/Web/CSS) / [JavaScript (ES6+)](https://developer.mozilla.org/en-US/docs/Web/JavaScript) |
| **CSS Framework** | [Bootstrap 5](https://getbootstrap.com/) |
| **Icons** | [Bootstrap Icons](https://icons.getbootstrap.com/) + [Font Awesome 6](https://fontawesome.com/) |
| **Local Server** | [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP) |
| **Version Control** | [Git](https://git-scm.com/) + [GitHub](https://github.com/) |
| **Security** | CSRF tokens, password hashing (bcrypt), prepared SQL statements, session-based auth |

</div>

---

## 📁 Project Architecture

```
WebProE2300548/
├── index.php                          # Landing page (hero, courses, about, partnerships, testimonials, FAQ, support)
├── favicon.ico
├── config/                            # Application configuration
│   ├── config.php                     # Global constants (BASE_URL, APP_NAME, upload limits, timezone)
│   ├── db.php                         # Database connection
│   └── eduskill.sql                   # Database schema & initial data
├── includes/                          # Shared partials
│   ├── header.php                     # HTML head, meta tags, stylesheets
│   ├── navbar.php                     # Navigation bar
│   ├── footer.php                     # Footer with scripts
│   ├── auth.php                       # Authentication library (login, session, CSRF, role checks)
│   └── flash.php                      # Flash message utilities
├── pages/                             # Public page sections
│   ├── hero.php                       # Hero banner with typing effect
│   ├── cources.php                    # Course catalog with filters (search, category, level, instructor, price)
│   ├── allcources.php                 # Standalone full course listing page
│   ├── courcedetails.php              # Course detail page (508 lines: tabs, sidebar, related courses)
│   ├── about.php                      # About section
│   ├── partnership.php                # Partner institutes showcase
│   ├── testinomials.php               # Testimonials
│   ├── faq.php                        # FAQ accordion
│   ├── support.php                    # Support & contact
│   └── payment.php                    # Payment processing
├── auth/                              # Authentication routes
│   ├── login.php                      # Login with remember-me & CSRF protection
│   ├── register-learner.php           # Learner registration
│   ├── register-provider.php          # Provider registration
│   └── logout.php                     # Logout handler
├── learner/                           # Learner dashboard
│   ├── index.php                      # Learner portal entry
│   ├── api.php                        # Learner AJAX API endpoints
│   ├── includes/                      # Learner-specific includes
│   │   └── learner_data.php           # Learner data access functions
│   └── pages/                         # Dashboard pages
│       ├── dashboard.php              # Overview with stats
│       ├── courses.php                # Enrolled courses
│       ├── cart.php                   # Shopping cart
│       ├── wishlist.php               # Saved courses
│       ├── payments.php               # Payment history
│       ├── certificates.php           # Course certificates
│       ├── messages.php               # Inbox & conversations
│       ├── progress.php               # Learning progress
│       ├── profile.php                # Profile editing
│       ├── security.php               # Password/security
│       ├── drafts.php                 # Draft courses
│       ├── payouts.php                # Earnings (if applicable)
│       └── settings.php               # Account settings
├── provider/                          # Training provider dashboard
│   ├── index.php                      # Provider portal entry
│   ├── addcourses/                    # Course creation flow
│   ├── includes/                      # Provider-specific includes
│   └── pages/                         # Dashboard pages
│       ├── dashboard.php              # Performance overview
│       ├── courses.php                # Course management
│       ├── students.php               # Student enrollments
│       ├── analytics.php              # Performance analytics
│       ├── reviews.php                # Student reviews
│       ├── payments.php               # Earnings & payouts
│       ├── profile.php                # Instructor profile
│       ├── completeprofile.php        # Profile completion wizard
│       └── settings.php               # Account settings
├── admin-officer/                     # Admin dashboard
│   ├── index.php                      # Admin portal entry
│   ├── api.php                        # Admin AJAX API endpoints
│   ├── includes/                      # Admin-specific includes
│   └── pages/                         # Dashboard pages
│       ├── dashboard.php              # System overview
│       ├── courses.php                # Course oversight
│       ├── learnermanagement.php      # Learner account management
│       ├── providermanagement.php     # Provider approval & management
│       ├── users.php                  # All users management
│       ├── analytic-reports.php       # Analytical reports
│       ├── reports.php                # Data exports
│       ├── profile.php                # Admin profile
│       └── settings.php               # System settings
├── api/                               # Public API endpoints
│   └── public-courses.php             # Public course data API
├── assets/                            # Static assets
│   ├── css/                           # Stylesheets
│   ├── js/                            # JavaScript files
│   ├── images/                        # Images & icons
│   └── courseportal/                  # Course portal assets
├── scripts/                           # Utility scripts
│   ├── create_admin.php               # Admin user creation
│   ├── logout_officers.php            # Force logout utility
│   └── update_role.php                # Role update script
├── uploads/                           # File uploads
│   ├── courses/                       # Course thumbnails & materials
│   ├── learner-profiles/              # Learner profile images
│   └── provider-profiles/             # Provider profile images
└── README.md
```

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- [Git](https://git-scm.com/) (optional, for cloning)
- A web browser

### Installation & Setup

```bash
# 1. Clone the repository
git clone https://github.com/iconicaditya/WebProE2300548.git

# 2. Move the project folder to XAMPP's htdocs
#    (e.g., C:\xampp\htdocs\WebProE2300548)

# 3. Start Apache and MySQL in XAMPP Control Panel

# 4. Create the database
#    Open phpMyAdmin (http://localhost/phpmyadmin) and create a database named:
#    eduskill_marketplace

# 5. Import the SQL schema
#    In phpMyAdmin, select the eduskill_marketplace database,
#    then import the file: config/eduskill.sql

# 6. Open the application in your browser
#    http://localhost/WebProE2300548/
```

### Default Accounts (after database import)

| Role | Email | Password |
|---|---|---|
| Admin Officer | *(check database or run script)* | Set via `scripts/create_admin.php` |
| Training Provider | Register at `/auth/register-provider.php` | User-defined |
| Learner | Register at `/auth/register-learner.php` | User-defined |

### Quick Scripts

| Script | Purpose | Run Command |
|---|---|---|
| `scripts/create_admin.php` | Create an admin officer account | `php scripts/create_admin.php` |
| `scripts/logout_officers.php` | Forcefully log out all admin sessions | `php scripts/logout_officers.php` |
| `scripts/update_role.php` | Update a user's role | `php scripts/update_role.php` |

---

## 🎨 Design & UX Highlights

- **Clean, modern UI** with Bootstrap 5 components and custom CSS enhancements
- **Responsive layout** with carefully crafted breakpoints at 1280px, 1080px, 900px, 640px, and 520px
- **Teal-based color palette** (`#0d6e84` primary) conveying trust and education
- **Hero section** with animated typing effect and dual CTA buttons
- **Course catalog** with sticky filter sidebar, category chip buttons, instructor search dropdown, and live grid filtering
- **Course cards** with hover animations, smooth transitions, and gradient overlays on thumbnails
- **Course details page** with tabbed interface (Overview / Curriculum / Reviews), sticky price sidebar, and related courses carousel
- **Expandable curriculum modules** with lesson counts and duration metadata
- **Star ratings** with half-star support and review timestamps
- **Filter reset** functionality for easy navigation
- **Glassmorphism effects** on hero and course cards with backdrop blur

---

## ⚡ Performance & Architecture Highlights

- **Prepared SQL statements** prevent SQL injection and improve query performance
- **Indexed database schema** with foreign key relationships for efficient joins
- **Session-based authentication** with CSRF token validation on all forms
- **Modular PHP includes** — each page section (hero, courses, about, FAQ, etc.) is a reusable partial
- **Separation of concerns** — public pages, learner dashboard, provider dashboard, and admin dashboard are fully independent modules
- **AJAX API endpoints** (`learner/api.php`, `admin-officer/api.php`, `api/public-courses.php`) for asynchronous operations
- **Dynamic course loading** with JavaScript-driven filtering, searching, and pagination
- **Consistent error handling** with user-friendly error pages (custom 404 for missing courses)

---

## 🔒 Security Features

| Measure | Implementation |
|---|---|
| **CSRF Protection** | Token validation on all form submissions via `ems_verify_csrf_token()` |
| **Password Hashing** | `password_hash()` with bcrypt algorithm |
| **Prepared Statements** | All database queries use `mysqli_stmt` with bound parameters |
| **Session Security** | `session_regenerate_id()` on login; role-based access control |
| **Input Validation** | Email validation, numeric casting, string trimming on all inputs |
| **XSS Prevention** | `htmlspecialchars()` on all user-supplied output |
| **File Upload Security** | Extension whitelist, size limits (5MB), and dedicated upload directories |
| **Role-Based Redirects** | `ems_dashboard_path_for_role()` routes users to their correct dashboard |

---

## 🗺 Roadmap

- [ ] Email notifications for enrollment confirmations
- [ ] Online payment gateway integration
- [ ] Course progress tracking with video completion
- [ ] Instructor-student messaging system
- [ ] Certificate generation with unique verification codes
- [ ] Advanced analytics with chart visualizations
- [ ] Multi-language support
- [ ] API-first architecture for mobile app integration

---

## 📄 License

This project was developed as a **BIT210 Web Programming** academic assignment. All rights reserved.

---

## 👨‍💻 Group Members

| Name | ID |
|---|---|
| Aaditya Chaudhary | E2300548 |
| Aakroshan Chaudhary | E2300551 |
| Sandhya Dhami | E2300577 |

---

<div align="center">
  <p>
    Built with 💻 for BIT210 Web Programming
  </p>
  <p>
    <a href="https://github.com/iconicaditya/WebProE2300548"><strong>📦 GitHub Repository</strong></a>
  </p>
</div>