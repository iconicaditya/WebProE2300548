<?php
require_once(__DIR__ . '/../../config/config.php');
require_once(__DIR__ . '/../../config/db.php');
require_once(__DIR__ . '/../../includes/auth.php');

ems_require_login(['provider']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'provider') {
    ems_api_fail('FORBIDDEN', 'Provider account is required.', 403);
}

$providerUserId = (int)($portalUser['id'] ?? 0);
if ($providerUserId <= 0) {
    ems_api_fail('FORBIDDEN', 'Invalid provider account.', 403);
}

$action = isset($_REQUEST['action']) ? trim((string)$_REQUEST['action']) : '';
if ($action === '') {
    ems_api_fail('BAD_REQUEST', 'Missing action.', 400);
}

switch ($action) {
    case 'create_draft':
        ems_api_require_post();
        ems_api_verify_csrf();
        ems_api_handle_create_draft($conn, $providerUserId);
        break;

    case 'save_step':
        ems_api_require_post();
        ems_api_verify_csrf();
        ems_api_handle_save_step($conn, $providerUserId);
        break;

    case 'get_course':
        ems_api_handle_get_course($conn, $providerUserId);
        break;

    case 'publish':
        ems_api_require_post();
        ems_api_verify_csrf();
        ems_api_handle_publish($conn, $providerUserId);
        break;

    default:
        ems_api_fail('BAD_REQUEST', 'Unsupported action.', 400);
}

function ems_api_require_post()
{
    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        ems_api_fail('METHOD_NOT_ALLOWED', 'POST method is required.', 405);
    }
}

function ems_api_verify_csrf()
{
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!ems_verify_csrf_token($token)) {
        ems_api_fail('CSRF_FAILED', 'Security token is invalid or expired.', 403);
    }
}

function ems_api_handle_create_draft($conn, $providerUserId)
{
    if (!ems_api_tables_ready($conn)) {
        ems_api_fail('SCHEMA_MISSING', 'Course schema is not available. Apply migration first.', 409);
    }

    $title = ems_api_post_text('title', 180);
    $shortDescription = ems_api_post_text('short_description', 500);

    $errors = [];
    if ($title === '') {
        $errors['title'] = 'Course name is required.';
    }
    if ($shortDescription === '') {
        $errors['short_description'] = 'Short description is required.';
    }

    if (!empty($errors)) {
        ems_api_validation_fail($errors);
    }

    $stmt = $conn->prepare('INSERT INTO courses (provider_user_id, title, short_description, status) VALUES (?, ?, ?, "draft")');
    if (!$stmt) {
        ems_api_fail('SERVER_ERROR', 'Unable to create draft.', 500);
    }

    $stmt->bind_param('iss', $providerUserId, $title, $shortDescription);
    if (!$stmt->execute()) {
        $stmt->close();
        ems_api_fail('SERVER_ERROR', 'Failed to persist draft.', 500);
    }

    $courseId = (int)$stmt->insert_id;
    $stmt->close();

    ems_api_ok([
        'course_id' => $courseId,
        'status' => 'draft',
    ]);
}

function ems_api_handle_save_step($conn, $providerUserId)
{
    if (!ems_api_tables_ready($conn)) {
        ems_api_fail('SCHEMA_MISSING', 'Course schema is not available. Apply migration first.', 409);
    }

    $courseId = (int)($_POST['course_id'] ?? 0);
    $step = (int)($_POST['step'] ?? 0);

    if ($courseId <= 0) {
        ems_api_validation_fail(['course_id' => 'Course id is required.']);
    }

    if ($step < 1 || $step > 6) {
        ems_api_validation_fail(['step' => 'Invalid step.']);
    }

    $course = ems_api_get_provider_course($conn, $providerUserId, $courseId);
    if (!$course) {
        ems_api_fail('FORBIDDEN', 'Course not found or access denied.', 403);
    }

    $conn->begin_transaction();
    try {
        if ($step === 1) {
            ems_api_save_step_basic_details($conn, $courseId);
        } elseif ($step === 2) {
            ems_api_save_step_media($conn, $courseId);
        } elseif ($step === 3) {
            ems_api_save_step_modules($conn, $courseId);
        } elseif ($step === 4) {
            ems_api_save_step_price($conn, $courseId);
        } elseif ($step === 5) {
            ems_api_save_step_resources($conn, $courseId);
        } elseif ($step === 6) {
            ems_api_save_step_publish($conn, $courseId);
        }

        $conn->commit();
    } catch (Throwable $throwable) {
        $conn->rollback();
        ems_api_fail('SERVER_ERROR', $throwable->getMessage(), 500);
    }

    ems_api_ok([
        'course_id' => $courseId,
        'saved_step' => $step,
    ]);
}

function ems_api_handle_get_course($conn, $providerUserId)
{
    if (!ems_api_tables_ready($conn)) {
        ems_api_ok([
            'schema_ready' => false,
            'course' => null,
        ]);
    }

    $courseId = (int)($_GET['course_id'] ?? 0);
    if ($courseId <= 0) {
        ems_api_validation_fail(['course_id' => 'Course id is required.']);
    }

    $course = ems_api_get_provider_course($conn, $providerUserId, $courseId);
    if (!$course) {
        ems_api_fail('FORBIDDEN', 'Course not found or access denied.', 403);
    }

    $sectionsStmt = $conn->prepare('SELECT id, section_order, title FROM course_sections WHERE course_id = ? ORDER BY section_order ASC');
    $sectionsStmt->bind_param('i', $courseId);
    $sectionsStmt->execute();
    $sections = $sectionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $sectionsStmt->close();

    $lessonsStmt = $conn->prepare('SELECT id, section_id, lesson_order, lesson_type, title, video_path, pdf_path, quiz_json, is_preview FROM course_lessons WHERE course_id = ? ORDER BY lesson_order ASC');
    $lessonsStmt->bind_param('i', $courseId);
    $lessonsStmt->execute();
    $lessons = $lessonsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $lessonsStmt->close();

    $resourcesStmt = $conn->prepare('SELECT id, title, subtitle, file_path, mime_type, file_size_bytes FROM course_resources WHERE course_id = ? ORDER BY id ASC');
    $resourcesStmt->bind_param('i', $courseId);
    $resourcesStmt->execute();
    $resources = $resourcesStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $resourcesStmt->close();

    foreach ($lessons as &$lesson) {
        if (!empty($lesson['quiz_json'])) {
            $decoded = json_decode((string)$lesson['quiz_json'], true);
            $lesson['quiz'] = is_array($decoded) ? $decoded : [];
        } else {
            $lesson['quiz'] = [];
        }
        unset($lesson['quiz_json']);
    }
    unset($lesson);

    $coursePayload = [
        'id' => (int)$course['id'],
        'title' => (string)$course['title'],
        'short_description' => (string)$course['short_description'],
        'description' => (string)($course['description'] ?? ''),
        'level' => (string)$course['level'],
        'language' => (string)$course['language'],
        'duration_label' => (string)($course['duration_label'] ?? ''),
        'lesson_count_estimate' => (int)($course['lesson_count_estimate'] ?? 0),
        'student_count_estimate' => (int)($course['student_count_estimate'] ?? 0),
        'certification_enabled' => (int)($course['certification_enabled'] ?? 0) === 1,
        'includes' => ems_api_json_decode_array($course['includes_json'] ?? null),
        'outcomes' => ems_api_json_decode_array($course['outcomes_json'] ?? null),
        'requirements' => ems_api_json_decode_array($course['requirements_json'] ?? null),
        'thumbnail_path' => (string)($course['thumbnail_path'] ?? ''),
        'promo_video_url' => (string)($course['promo_video_url'] ?? ''),
        'trailer_path' => (string)($course['trailer_path'] ?? ''),
        'gallery' => ems_api_json_decode_array($course['gallery_json'] ?? null),
        'access_type' => (string)$course['access_type'],
        'price_amount' => $course['price_amount'] !== null ? (float)$course['price_amount'] : null,
        'currency_code' => (string)$course['currency_code'],
        'coupon_code' => (string)($course['coupon_code'] ?? ''),
        'visibility' => (string)$course['visibility'],
        'status' => (string)$course['status'],
        'sections' => $sections ?: [],
        'lessons' => $lessons ?: [],
        'resources' => $resources ?: [],
    ];

    ems_api_ok([
        'schema_ready' => true,
        'course' => $coursePayload,
    ]);
}

function ems_api_handle_publish($conn, $providerUserId)
{
    if (!ems_api_tables_ready($conn)) {
        ems_api_fail('SCHEMA_MISSING', 'Course schema is not available. Apply migration first.', 409);
    }

    $courseId = (int)($_POST['course_id'] ?? 0);
    if ($courseId <= 0) {
        ems_api_validation_fail(['course_id' => 'Course id is required.']);
    }

    $course = ems_api_get_provider_course($conn, $providerUserId, $courseId);
    if (!$course) {
        ems_api_fail('FORBIDDEN', 'Course not found or access denied.', 403);
    }

    $errors = [];
    if (trim((string)$course['title']) === '') {
        $errors['title'] = 'Course name is required.';
    }
    if (trim((string)$course['short_description']) === '') {
        $errors['short_description'] = 'Short description is required.';
    }

    $lessonStmt = $conn->prepare('SELECT COUNT(*) AS total FROM course_lessons WHERE course_id = ?');
    if ($lessonStmt) {
        $lessonStmt->bind_param('i', $courseId);
        $lessonStmt->execute();
        $lessonCount = (int)($lessonStmt->get_result()->fetch_assoc()['total'] ?? 0);
        $lessonStmt->close();
        if ($lessonCount <= 0) {
            $errors['lessons'] = 'Add at least one lesson before publishing.';
        }
    }

    if (($course['access_type'] ?? 'free') === 'paid' && (float)($course['price_amount'] ?? 0) <= 0) {
        $errors['price_amount'] = 'Paid course requires a valid price amount.';
    }

    if (!empty($errors)) {
        ems_api_validation_fail($errors);
    }

    $publishStmt = $conn->prepare('UPDATE courses SET status = "published", published_at = NOW(), updated_at = NOW() WHERE id = ? AND provider_user_id = ? LIMIT 1');
    if (!$publishStmt) {
        ems_api_fail('SERVER_ERROR', 'Unable to publish course.', 500);
    }

    $publishStmt->bind_param('ii', $courseId, $providerUserId);
    $ok = $publishStmt->execute();
    $publishStmt->close();

    if (!$ok) {
        ems_api_fail('SERVER_ERROR', 'Failed to publish course.', 500);
    }

    ems_api_ok([
        'course_id' => $courseId,
        'status' => 'published',
        'redirect_url' => BASE_URL . 'provider/?page=courses',
    ]);
}

function ems_api_save_step_basic_details($conn, $courseId)
{
    $title = ems_api_post_text('title', 180);
    $shortDescription = ems_api_post_text('short_description', 500);
    $description = ems_api_post_text('description', 20000);

    $duration = ems_api_post_text('duration', 80);
    $lessonsCountEstimate = max(0, (int)($_POST['lessons'] ?? 0));
    $studentsCountEstimate = max(0, (int)($_POST['students'] ?? 0));

    $levelRaw = strtolower(trim((string)($_POST['level'] ?? 'all levels')));
    $levelMap = [
        'all levels' => 'all_levels',
        'all_levels' => 'all_levels',
        'beginner' => 'beginner',
        'intermediate' => 'intermediate',
        'advanced' => 'advanced',
    ];
    $level = $levelMap[$levelRaw] ?? 'all_levels';

    $language = ems_api_post_text('language', 80);
    if ($language === '') {
        $language = 'English';
    }

    $certification = (string)($_POST['certification'] ?? 'yes');
    $certificationEnabled = $certification === 'no' ? 0 : 1;

    $includes = isset($_POST['included']) && is_array($_POST['included'])
        ? array_values(array_filter(array_map('trim', $_POST['included']), static function ($v) {
            return $v !== '';
        }))
        : [];

    $outcomes = ems_api_post_array('outcomes');
    $requirements = ems_api_post_array('requirements');

    $errors = [];
    if ($title === '') {
        $errors['title'] = 'Course name is required.';
    }
    if ($shortDescription === '') {
        $errors['short_description'] = 'Short description is required.';
    }
    if (!empty($errors)) {
        ems_api_validation_fail($errors);
    }

    $thumbnailPath = null;
    if (!empty($_FILES['thumbnail']) && (int)($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $thumbnailUpload = ems_api_store_upload($_FILES['thumbnail'], $courseId, 'thumbnail', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024);
        $thumbnailPath = $thumbnailUpload['path'];
    }

    $sql = 'UPDATE courses
            SET title = ?, short_description = ?, description = ?, level = ?, language = ?, duration_label = ?,
                lesson_count_estimate = ?, student_count_estimate = ?, certification_enabled = ?,
                includes_json = ?, outcomes_json = ?, requirements_json = ?,
                updated_at = NOW()';
    $types = 'ssssssiiisss';
    $params = [
        $title,
        $shortDescription,
        $description,
        $level,
        $language,
        $duration,
        $lessonsCountEstimate,
        $studentsCountEstimate,
        $certificationEnabled,
        json_encode($includes, JSON_UNESCAPED_UNICODE),
        json_encode($outcomes, JSON_UNESCAPED_UNICODE),
        json_encode($requirements, JSON_UNESCAPED_UNICODE),
    ];

    if ($thumbnailPath !== null) {
        $sql .= ', thumbnail_path = ?';
        $types .= 's';
        $params[] = $thumbnailPath;
    }

    $sql .= ' WHERE id = ?';
    $types .= 'i';
    $params[] = $courseId;

    ems_api_exec_prepared($conn, $sql, $types, $params);
}

function ems_api_save_step_media($conn, $courseId)
{
    $promoVideoUrl = ems_api_post_text('promo_video_url', 500);

    $galleryPaths = [];
    if (isset($_FILES['gallery']) && is_array($_FILES['gallery']['name'] ?? null)) {
        $galleryFiles = ems_api_normalize_multi_upload($_FILES['gallery']);
        foreach ($galleryFiles as $galleryFile) {
            if ((int)($galleryFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $stored = ems_api_store_upload($galleryFile, $courseId, 'gallery', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024);
            $galleryPaths[] = $stored['path'];
        }
    }

    $trailerPath = null;
    if (!empty($_FILES['trailer']) && (int)($_FILES['trailer']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $trailer = ems_api_store_upload($_FILES['trailer'], $courseId, 'trailer', ['video/mp4', 'video/webm', 'video/quicktime'], 200 * 1024 * 1024);
        $trailerPath = $trailer['path'];
    }

    $sql = 'UPDATE courses SET promo_video_url = ?, updated_at = NOW()';
    $types = 's';
    $params = [$promoVideoUrl];

    if (!empty($galleryPaths)) {
        $sql .= ', gallery_json = ?';
        $types .= 's';
        $params[] = json_encode($galleryPaths, JSON_UNESCAPED_UNICODE);
    }

    if ($trailerPath !== null) {
        $sql .= ', trailer_path = ?';
        $types .= 's';
        $params[] = $trailerPath;
    }

    $sql .= ' WHERE id = ?';
    $types .= 'i';
    $params[] = $courseId;

    ems_api_exec_prepared($conn, $sql, $types, $params);
}

function ems_api_save_step_modules($conn, $courseId)
{
    $sectionsPayload = ems_api_json_request_array('sections');
    $lessonsPayload = ems_api_json_request_array('lessons');

    ems_api_exec_prepared($conn, 'DELETE FROM course_lessons WHERE course_id = ?', 'i', [$courseId]);
    ems_api_exec_prepared($conn, 'DELETE FROM course_sections WHERE course_id = ?', 'i', [$courseId]);

    $sectionIdByOrder = [];
    $insertSectionStmt = $conn->prepare('INSERT INTO course_sections (course_id, section_order, title) VALUES (?, ?, ?)');
    if (!$insertSectionStmt) {
        throw new RuntimeException('Unable to insert course sections.');
    }

    foreach ($sectionsPayload as $sectionRow) {
        $sectionOrder = max(1, (int)($sectionRow['order'] ?? 0));
        $sectionTitle = trim((string)($sectionRow['title'] ?? ('Section ' . $sectionOrder)));
        if ($sectionTitle === '') {
            $sectionTitle = 'Section ' . $sectionOrder;
        }
        $insertSectionStmt->bind_param('iis', $courseId, $sectionOrder, $sectionTitle);
        if (!$insertSectionStmt->execute()) {
            $insertSectionStmt->close();
            throw new RuntimeException('Failed to save sections.');
        }
        $sectionIdByOrder[$sectionOrder] = (int)$insertSectionStmt->insert_id;
    }
    $insertSectionStmt->close();

    $insertLessonStmt = $conn->prepare('INSERT INTO course_lessons (course_id, section_id, lesson_order, lesson_type, title, video_path, pdf_path, quiz_json, is_preview) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$insertLessonStmt) {
        throw new RuntimeException('Unable to insert lessons.');
    }

    $order = 0;
    foreach ($lessonsPayload as $lessonRow) {
        $order++;
        $sectionOrder = max(1, (int)($lessonRow['section_order'] ?? 1));
        $sectionId = $sectionIdByOrder[$sectionOrder] ?? null;

        $lessonType = strtolower(trim((string)($lessonRow['type'] ?? 'video')));
        if (!in_array($lessonType, ['video', 'pdf', 'quiz'], true)) {
            $lessonType = 'video';
        }

        $title = trim((string)($lessonRow['title'] ?? ''));
        if ($title === '') {
            $title = ucfirst($lessonType) . ' lesson';
        }

        $videoPath = null;
        $pdfPath = null;
        $quizJson = null;

        if ($lessonType === 'video') {
            $uploadKey = trim((string)($lessonRow['video_upload_key'] ?? ''));
            if ($uploadKey !== '' && isset($_FILES[$uploadKey])) {
                $uploaded = ems_api_store_upload(
                    $_FILES[$uploadKey],
                    $courseId,
                    'lessons',
                    ['video/mp4', 'video/webm', 'video/quicktime'],
                    500 * 1024 * 1024
                );
                $videoPath = $uploaded['path'];
            } else {
                $videoPath = trim((string)($lessonRow['video_path'] ?? '')) ?: null;
            }
        } elseif ($lessonType === 'pdf') {
            $uploadKey = trim((string)($lessonRow['pdf_upload_key'] ?? ''));
            if ($uploadKey !== '' && isset($_FILES[$uploadKey])) {
                $uploaded = ems_api_store_upload(
                    $_FILES[$uploadKey],
                    $courseId,
                    'lessons',
                    [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ],
                    20 * 1024 * 1024
                );
                $pdfPath = $uploaded['path'];
            } else {
                $pdfPath = trim((string)($lessonRow['pdf_path'] ?? '')) ?: null;
            }
        } else {
            $quizValue = $lessonRow['quiz'] ?? [];
            if (!is_array($quizValue)) {
                $quizValue = [];
            }
            $quizJson = json_encode($quizValue, JSON_UNESCAPED_UNICODE);
        }

        $isPreview = !empty($lessonRow['is_preview']) ? 1 : 0;

        $insertLessonStmt->bind_param(
            'iiisssssi',
            $courseId,
            $sectionId,
            $order,
            $lessonType,
            $title,
            $videoPath,
            $pdfPath,
            $quizJson,
            $isPreview
        );

        if (!$insertLessonStmt->execute()) {
            $insertLessonStmt->close();
            throw new RuntimeException('Failed to save lessons.');
        }
    }
    $insertLessonStmt->close();

    ems_api_exec_prepared($conn, 'UPDATE courses SET lesson_count_estimate = ?, updated_at = NOW() WHERE id = ?', 'ii', [$order, $courseId]);
}

function ems_api_save_step_price($conn, $courseId)
{
    $access = strtolower(trim((string)($_POST['access'] ?? 'free')));
    if (!in_array($access, ['free', 'paid'], true)) {
        $access = 'free';
    }

    $priceAmount = null;
    if ($access === 'paid') {
        $priceAmount = (float)($_POST['price'] ?? 0);
        if ($priceAmount < 0) {
            $priceAmount = 0;
        }
    }

    $currency = strtoupper(trim((string)($_POST['currency'] ?? 'USD')));
    if (!in_array($currency, ['USD', 'NPR', 'EUR'], true)) {
        $currency = 'USD';
    }

    $couponCode = ems_api_post_text('coupon', 40);
    if ($couponCode === '') {
        $couponCode = null;
    }

    ems_api_exec_prepared(
        $conn,
        'UPDATE courses SET access_type = ?, price_amount = ?, currency_code = ?, coupon_code = ?, updated_at = NOW() WHERE id = ?',
        'sdssi',
        [$access, $priceAmount, $currency, $couponCode, $courseId]
    );
}

function ems_api_save_step_resources($conn, $courseId)
{
    $resourcesPayload = ems_api_json_request_array('resources');
    ems_api_exec_prepared($conn, 'DELETE FROM course_resources WHERE course_id = ?', 'i', [$courseId]);

    if (empty($resourcesPayload)) {
        return;
    }

    $stmt = $conn->prepare('INSERT INTO course_resources (course_id, title, subtitle, file_path, mime_type, file_size_bytes) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new RuntimeException('Unable to save resources.');
    }

    foreach ($resourcesPayload as $resourceRow) {
        $title = trim((string)($resourceRow['title'] ?? ''));
        if ($title === '') {
            $stmt->close();
            ems_api_validation_fail(['resources' => 'Resource title is required.']);
        }

        $subtitle = trim((string)($resourceRow['subtitle'] ?? ''));

        $filePath = '';
        $mimeType = 'application/octet-stream';
        $fileSize = 0;

        $uploadKey = trim((string)($resourceRow['upload_key'] ?? ''));
        if ($uploadKey !== '' && isset($_FILES[$uploadKey])) {
            $uploaded = ems_api_store_upload(
                $_FILES[$uploadKey],
                $courseId,
                'resources',
                [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/octet-stream'
                ],
                10 * 1024 * 1024
            );
            $filePath = (string)$uploaded['path'];
            $mimeType = (string)$uploaded['mime_type'];
            $fileSize = (int)$uploaded['file_size_bytes'];
        } else {
            $filePath = trim((string)($resourceRow['file_path'] ?? ''));
            $mimeType = trim((string)($resourceRow['mime_type'] ?? 'application/octet-stream'));
            $fileSize = max(0, (int)($resourceRow['file_size_bytes'] ?? 0));
        }

        if ($filePath === '') {
            $stmt->close();
            ems_api_validation_fail(['resources' => 'Each resource needs a valid file path.']);
        }

        $stmt->bind_param('issssi', $courseId, $title, $subtitle, $filePath, $mimeType, $fileSize);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('Failed to save resource entries.');
        }
    }

    $stmt->close();
}

function ems_api_save_step_publish($conn, $courseId)
{
    $visibility = !empty($_POST['visibility']) && $_POST['visibility'] === 'private' ? 'private' : 'public';
    ems_api_exec_prepared($conn, 'UPDATE courses SET visibility = ?, updated_at = NOW() WHERE id = ?', 'si', [$visibility, $courseId]);
}

function ems_api_get_provider_course($conn, $providerUserId, $courseId)
{
    $stmt = $conn->prepare('SELECT * FROM courses WHERE id = ? AND provider_user_id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('ii', $courseId, $providerUserId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function ems_api_tables_ready($conn)
{
    static $ready = null;
    if ($ready !== null) {
        return $ready;
    }

    $required = ['courses', 'course_sections', 'course_lessons', 'course_resources', 'enrollments', 'reviews'];
    foreach ($required as $table) {
        $stmt = $conn->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1');
        if (!$stmt) {
            $ready = false;
            return false;
        }
        $stmt->bind_param('s', $table);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result && $result->num_rows > 0;
        $stmt->close();
        if (!$exists) {
            $ready = false;
            return false;
        }
    }

    $ready = true;
    return true;
}

function ems_api_post_text($key, $maxLength)
{
    $value = trim((string)($_POST[$key] ?? ''));
    if ($maxLength > 0 && function_exists('mb_substr')) {
        return mb_substr($value, 0, $maxLength, 'UTF-8');
    }
    return $maxLength > 0 ? substr($value, 0, $maxLength) : $value;
}

function ems_api_post_array($key)
{
    if (!isset($_POST[$key]) || !is_array($_POST[$key])) {
        return [];
    }
    $clean = [];
    foreach ($_POST[$key] as $item) {
        $text = trim((string)$item);
        if ($text !== '') {
            $clean[] = $text;
        }
    }
    return $clean;
}

function ems_api_json_request_array($key)
{
    $raw = $_POST[$key] ?? '[]';
    if (is_array($raw)) {
        return $raw;
    }
    $decoded = json_decode((string)$raw, true);
    return is_array($decoded) ? $decoded : [];
}

function ems_api_json_decode_array($json)
{
    if (!is_string($json) || trim($json) === '') {
        return [];
    }
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}

function ems_api_normalize_multi_upload(array $upload)
{
    $normalized = [];
    $names = $upload['name'] ?? [];
    $types = $upload['type'] ?? [];
    $tmpNames = $upload['tmp_name'] ?? [];
    $errors = $upload['error'] ?? [];
    $sizes = $upload['size'] ?? [];

    if (!is_array($names)) {
        return $normalized;
    }

    foreach ($names as $index => $name) {
        $normalized[] = [
            'name' => $name,
            'type' => $types[$index] ?? '',
            'tmp_name' => $tmpNames[$index] ?? '',
            'error' => $errors[$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $sizes[$index] ?? 0,
        ];
    }

    return $normalized;
}

function ems_api_store_upload(array $file, $courseId, $segment, array $allowedMimeTypes, $maxSize)
{
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        ems_api_fail('UPLOAD_ERROR', 'Failed to upload file (' . $segment . ').', 400);
    }

    $tmpPath = (string)($file['tmp_name'] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        ems_api_fail('UPLOAD_ERROR', 'Invalid uploaded file (' . $segment . ').', 400);
    }

    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > (int)$maxSize) {
        ems_api_fail('VALIDATION_ERROR', 'Invalid file size for ' . $segment . '.', 422);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmpPath);

    $extension = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
    $mimeAllowed = in_array($mime, $allowedMimeTypes, true);

    if (!$mimeAllowed) {
        ems_api_fail('VALIDATION_ERROR', 'Invalid file type for ' . $segment . '.', 422);
    }

    $safeSegment = preg_replace('/[^a-z0-9_-]/i', '', $segment);
    $courseDir = rtrim(UPLOAD_DIR, '/\\') . '/courses/' . (int)$courseId . '/' . $safeSegment;
    if (!is_dir($courseDir) && !mkdir($courseDir, 0775, true) && !is_dir($courseDir)) {
        ems_api_fail('SERVER_ERROR', 'Cannot prepare upload directory.', 500);
    }

    $random = bin2hex(random_bytes(6));
    $fileName = $safeSegment . '_' . time() . '_' . $random;
    if ($extension !== '') {
        $fileName .= '.' . $extension;
    }

    $target = $courseDir . '/' . $fileName;
    if (!move_uploaded_file($tmpPath, $target)) {
        ems_api_fail('SERVER_ERROR', 'Unable to move uploaded file.', 500);
    }

    $relativePath = 'uploads/courses/' . (int)$courseId . '/' . $safeSegment . '/' . $fileName;
    return [
        'path' => $relativePath,
        'mime_type' => $mime,
        'file_size_bytes' => $size,
    ];
}

function ems_api_exec_prepared($conn, $sql, $types, array $params)
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Failed preparing SQL statement.');
    }

    if ($types !== '' && !empty($params)) {
        $bindParams = [];
        $bindParams[] = $types;
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new RuntimeException('Failed executing SQL statement.');
    }

    $stmt->close();
}

function ems_api_validation_fail(array $errors)
{
    ems_api_fail('VALIDATION_ERROR', 'Validation failed.', 422, ['errors' => $errors]);
}

function ems_api_ok(array $data = [])
{
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function ems_api_fail($code, $message, $status = 400, array $extra = [])
{
    http_response_code((int)$status);
    header('Content-Type: application/json; charset=utf-8');

    $payload = [
        'ok' => false,
        'code' => (string)$code,
        'message' => (string)$message,
    ];

    foreach ($extra as $k => $v) {
        $payload[$k] = $v;
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

