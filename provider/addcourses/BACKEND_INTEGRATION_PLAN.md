# Provider Add Course Backend Integration Plan

## 1) Objective and Scope

This plan defines a full backend integration for the provider course-creation flow currently rendered by [`provider/addcourses/index.php`](provider/addcourses/index.php) and frontend logic in [`assets/js/provider-addcourses.js`](assets/js/provider-addcourses.js).

### In Scope
- Add normalized data model for:
  - `courses`
  - `course_sections`
  - `course_lessons`
  - `course_resources`
  - `enrollments`
  - `reviews`
- Implement provider add-course backend lifecycle:
  - create draft
  - update draft
  - fetch draft/course for edit
  - publish
- Integrate file/media uploads for thumbnails, trailer, lesson files, and resources.
- Replace static provider dashboard data with real DB queries in:
  - [`provider/pages/courses.php`](provider/pages/courses.php)
  - [`provider/pages/dashboard.php`](provider/pages/dashboard.php)
  - [`provider/pages/students.php`](provider/pages/students.php)
  - [`provider/pages/reviews.php`](provider/pages/reviews.php)
  - [`provider/pages/payments.php`](provider/pages/payments.php)
  - [`provider/pages/analytics.php`](provider/pages/analytics.php)

### Out of Scope (Phase-2+)
- Advanced review moderation workflows.
- Payout settlement ledger with external gateways.
- Lesson streaming DRM and signed CDN URLs.

---

## 2) Current State Findings

1. Auth/profile base exists in [`config/eduskill.sql`](config/eduskill.sql), but no course domain tables currently exist.
2. Provider views are mostly static mock HTML (no persistence).
3. Add-course UI is multi-step and already functional in frontend mode, but submission currently only alerts.
4. Existing provider auth context and ownership utilities are available via [`includes/auth.php`](includes/auth.php).

Implication: database schema must be introduced first, then backend APIs, then frontend binding.

---

## 3) Target Data Model (Normalized)

> Add directly in [`config/eduskill.sql`](config/eduskill.sql).

### 3.1 `courses`

Purpose: root aggregate for provider-owned course.

Columns:
- `id` BIGINT UNSIGNED PK AI
- `provider_user_id` INT UNSIGNED NOT NULL FK -> `users(id)`
- `title` VARCHAR(180) NOT NULL
- `short_description` VARCHAR(500) NOT NULL
- `description` TEXT NULL
- `level` ENUM('all_levels','beginner','intermediate','advanced') NOT NULL DEFAULT 'all_levels'
- `language` VARCHAR(80) NOT NULL DEFAULT 'English'
- `duration_label` VARCHAR(80) NULL
- `lesson_count_estimate` INT UNSIGNED NOT NULL DEFAULT 0
- `student_count_estimate` INT UNSIGNED NOT NULL DEFAULT 0
- `certification_enabled` TINYINT(1) NOT NULL DEFAULT 1
- `includes_json` JSON NULL
- `outcomes_json` JSON NULL
- `requirements_json` JSON NULL
- `thumbnail_path` VARCHAR(255) NULL
- `promo_video_url` VARCHAR(500) NULL
- `trailer_path` VARCHAR(255) NULL
- `gallery_json` JSON NULL
- `access_type` ENUM('free','paid') NOT NULL DEFAULT 'free'
- `price_amount` DECIMAL(10,2) NULL
- `currency_code` CHAR(3) NOT NULL DEFAULT 'USD'
- `coupon_code` VARCHAR(40) NULL
- `visibility` ENUM('public','private') NOT NULL DEFAULT 'public'
- `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft'
- `published_at` DATETIME NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

Indexes:
- `idx_courses_provider_status` (`provider_user_id`, `status`)
- `idx_courses_published` (`status`, `published_at`)

Constraints:
- `price_amount` must be NULL when free, >=0 when paid (enforce in app layer if MySQL CHECK compatibility varies).

### 3.2 `course_sections`

Columns:
- `id` BIGINT UNSIGNED PK AI
- `course_id` BIGINT UNSIGNED NOT NULL FK -> `courses(id)` ON DELETE CASCADE
- `section_order` SMALLINT UNSIGNED NOT NULL
- `title` VARCHAR(180) NOT NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

Indexes/Unique:
- UNIQUE `uq_course_sections_order` (`course_id`,`section_order`)
- `idx_sections_course` (`course_id`)

### 3.3 `course_lessons`

Columns:
- `id` BIGINT UNSIGNED PK AI
- `course_id` BIGINT UNSIGNED NOT NULL FK -> `courses(id)` ON DELETE CASCADE
- `section_id` BIGINT UNSIGNED NULL FK -> `course_sections(id)` ON DELETE SET NULL
- `lesson_order` SMALLINT UNSIGNED NOT NULL
- `lesson_type` ENUM('video','pdf','quiz') NOT NULL
- `title` VARCHAR(180) NOT NULL
- `video_path` VARCHAR(255) NULL
- `pdf_path` VARCHAR(255) NULL
- `quiz_json` JSON NULL
- `duration_seconds` INT UNSIGNED NULL
- `is_preview` TINYINT(1) NOT NULL DEFAULT 0
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

Indexes:
- `idx_lessons_course` (`course_id`,`lesson_order`)
- `idx_lessons_section` (`section_id`)

### 3.4 `course_resources`

Columns:
- `id` BIGINT UNSIGNED PK AI
- `course_id` BIGINT UNSIGNED NOT NULL FK -> `courses(id)` ON DELETE CASCADE
- `title` VARCHAR(180) NOT NULL
- `subtitle` VARCHAR(220) NULL
- `file_path` VARCHAR(255) NOT NULL
- `mime_type` VARCHAR(120) NOT NULL
- `file_size_bytes` BIGINT UNSIGNED NOT NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

Indexes:
- `idx_resources_course` (`course_id`)

### 3.5 `enrollments`

Columns:
- `id` BIGINT UNSIGNED PK AI
- `course_id` BIGINT UNSIGNED NOT NULL FK -> `courses(id)` ON DELETE CASCADE
- `learner_user_id` INT UNSIGNED NOT NULL FK -> `users(id)` ON DELETE CASCADE
- `enrollment_status` ENUM('active','completed','cancelled','refunded') NOT NULL DEFAULT 'active'
- `enrolled_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `completed_at` DATETIME NULL
- `progress_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00

Indexes/Unique:
- UNIQUE `uq_enrollment_course_learner` (`course_id`,`learner_user_id`)
- `idx_enrollment_learner` (`learner_user_id`)
- `idx_enrollment_status` (`enrollment_status`)

### 3.6 `reviews`

Columns:
- `id` BIGINT UNSIGNED PK AI
- `course_id` BIGINT UNSIGNED NOT NULL FK -> `courses(id)` ON DELETE CASCADE
- `enrollment_id` BIGINT UNSIGNED NULL FK -> `enrollments(id)` ON DELETE SET NULL
- `learner_user_id` INT UNSIGNED NOT NULL FK -> `users(id)` ON DELETE CASCADE
- `rating` TINYINT UNSIGNED NOT NULL
- `review_text` TEXT NULL
- `is_visible` TINYINT(1) NOT NULL DEFAULT 1
- `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME NULL

Indexes/Unique:
- UNIQUE `uq_review_course_learner` (`course_id`,`learner_user_id`)
- `idx_reviews_course_visible` (`course_id`,`is_visible`)

Validation:
- rating 1..5 enforced in app layer (or CHECK if available).

---

## 4) Upload and Media Policy

Root path:
- [`UPLOAD_DIR`](config/config.php:23) + `courses/{course_id}/`

Structure:
- `uploads/courses/{id}/thumbnail/`
- `uploads/courses/{id}/gallery/`
- `uploads/courses/{id}/trailer/`
- `uploads/courses/{id}/lessons/`
- `uploads/courses/{id}/resources/`

File rules:
- Use `finfo` MIME sniffing + extension allowlist.
- Max sizes (proposal):
  - thumbnail/image <= 5MB
  - trailer/video <= 200MB
  - lesson video <= 500MB
  - pdf/doc/ppt resource <= 10MB (matching UI hint)
- Filenames: `<type>_<unix>_<random>.<ext>` to avoid collisions.
- Never trust client name/path.
- On update, defer deletion of replaced files until DB transaction commits.

---

## 5) Backend Endpoint Contracts

Create new backend controller under:
- `provider/addcourses/api.php` (single endpoint by `action`) **or** dedicated action files in `provider/addcourses/actions/`.

Recommended actions:

1. `POST action=create_draft`
- Input: minimal root fields from step-1.
- Output: `{ok:true, course_id, status:'draft'}`.

2. `POST action=save_step`
- Input: `course_id`, `step`, fields/files for one step.
- Output: `{ok:true, saved_step, warnings:[]}`.

3. `GET action=get_course&course_id=...`
- Ownership enforced.
- Output: full hydrated payload for edit mode.

4. `POST action=publish`
- Input: `course_id` + final visibility.
- Server validates required completeness.
- Output: `{ok:true, status:'published', redirect_url:'...'}`.

5. `POST action=archive` (phase-1 optional)

Response standard:
- Success: `{ok:true, data:{...}}`
- Validation failure: `{ok:false, code:'VALIDATION_ERROR', errors:{field:'message'}}`
- Auth/ownership failure: `{ok:false, code:'FORBIDDEN'}`
- Server failure: `{ok:false, code:'SERVER_ERROR'}`

---

## 6) Transactional Persistence Strategy

For publish and full save:
1. `BEGIN`
2. Upsert `courses`
3. Replace `course_sections` for `course_id`
4. Replace `course_lessons` with deterministic ordering
5. Replace `course_resources`
6. Update status transition (`draft` -> `published`) and `published_at`
7. `COMMIT`

On failure:
- `ROLLBACK`
- Return structured error payload.

Media consistency:
- Stage new files first; mark old files for deletion.
- Physically delete old files only after successful commit.

---

## 7) Frontend Integration Mapping

Update [`assets/js/provider-addcourses.js`](assets/js/provider-addcourses.js):
- Add `currentCourseId` state.
- On first valid data entry, call `create_draft`.
- On each step change / explicit next, call `save_step` with step payload.
- Convert current dynamic lesson/resource DOM into structured payload arrays.
- Replace final alert with `publish` API call.
- Render server validation under field controls and section alerts.
- Support edit mode by loading `get_course` and hydrating step forms.

Keep existing UI in:
- [`provider/addcourses/index.php`](provider/addcourses/index.php)
- [`provider/addcourses/pages/*.php`](provider/addcourses/pages)

No visual redesign required for backend integration phase.

---

## 8) Provider Dashboard Data Binding Plan

### 8.1 Courses Page
- [`provider/pages/courses.php`](provider/pages/courses.php): replace static rows with provider-owned `courses` query.
- Include status, price, enrolled count, avg rating.

### 8.2 Dashboard Overview
- [`provider/pages/dashboard.php`](provider/pages/dashboard.php):
  - total courses
  - active students (distinct enrollments active)
  - total/period revenue (if payment table exists later; else placeholder from enrollments in phase-1)
  - avg rating from `reviews`

### 8.3 Students Page
- [`provider/pages/students.php`](provider/pages/students.php): join `enrollments + users + courses`.

### 8.4 Reviews Page
- [`provider/pages/reviews.php`](provider/pages/reviews.php): join `reviews + users + courses` filtered by provider ownership.

### 8.5 Payments/Analytics
- [`provider/pages/payments.php`](provider/pages/payments.php): phase-1 can show derived values from enrollment states until payment ledger exists.
- [`provider/pages/analytics.php`](provider/pages/analytics.php): build monthly aggregates from enrollments/reviews.

---

## 9) Security and Ownership Controls

Every provider endpoint must:
1. Require auth role `provider` via [`ems_require_login()`](includes/auth.php:102).
2. Resolve provider user id from [`ems_load_portal_user()`](includes/auth.php:153).
3. Verify `course.provider_user_id = current_provider_id` before read/write/delete.
4. Enforce CSRF token for mutating actions with [`ems_csrf_token()`](includes/auth.php:42) and [`ems_verify_csrf_token()`](includes/auth.php:51).
5. Validate all server-side payloads independent of frontend.

---

## 10) Implementation Phases

### Phase A - Foundation
- Add migration SQL for six tables and indexes.
- Add lightweight data-access layer/helpers for provider courses.

### Phase B - Add-Course Backend
- Implement `create_draft`, `save_step`, `get_course`, `publish`.
- Integrate uploads and media validation.

### Phase C - Frontend Binding
- Wire [`assets/js/provider-addcourses.js`](assets/js/provider-addcourses.js) to API.
- Add draft/edit hydration and publish flow.

### Phase D - Provider Dashboard Binding
- Replace static tables/cards with real queries.

### Phase E - Hardening
- QA/UAT, error-path testing, migration rollback rehearsal.

---

## 11) QA/UAT and Rollback Plan

QA matrix:
- Draft creation
- Step saves with partial data
- File upload validation failures
- Publish with missing required fields
- Publish success and dashboard reflection
- Ownership violation attempts

Migration safety:
- Keep migration idempotent where possible.
- Backup DB before apply.
- Rollback script drops new tables in reverse FK order:
  1. `reviews`
  2. `enrollments`
  3. `course_resources`
  4. `course_lessons`
  5. `course_sections`
  6. `courses`

---

## 12) Deliverables Checklist

- [ ] SQL migration file in [`config/`](config)
- [ ] Provider add-course API/controller implementation in [`provider/addcourses/`](provider/addcourses)
- [ ] Frontend API integration in [`assets/js/provider-addcourses.js`](assets/js/provider-addcourses.js)
- [ ] Dynamic data integration for provider pages in [`provider/pages/`](provider/pages)
- [ ] Upload directories and validation utilities
- [ ] QA test document and rollout notes

