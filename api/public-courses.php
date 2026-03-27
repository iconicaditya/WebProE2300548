<?php
/**
 * Public Courses API Endpoint
 * No authentication required
 * Used by course discovery pages (allcourses.php, etc)
 */

require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../learner/includes/learner_data.php');

header('Content-Type: application/json; charset=utf-8');

$action = trim((string)($_REQUEST['action'] ?? ''));

switch ($action) {
    case 'list_courses':
        $limit = min(1000, max(1, (int)($_GET['limit'] ?? 100)));
        $offset = max(0, (int)($_GET['offset'] ?? 0));
        
        $courses = ems_learner_fetch_all_published_courses($conn, $limit, $offset);
        
        http_response_code(200);
        echo json_encode([
            'ok' => true,
            'data' => [
                'courses' => $courses,
                'count' => count($courses),
            ],
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'get_course':
        $courseId = (int)($_GET['course_id'] ?? 0);
        if ($courseId <= 0) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => 'Course ID is required.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $course = ems_learner_fetch_course_by_id($conn, $courseId);
        if (!$course) {
            http_response_code(404);
            echo json_encode([
                'ok' => false,
                'message' => 'Course not found.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'ok' => true,
            'data' => $course,
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => 'Invalid action.',
        ], JSON_UNESCAPED_UNICODE);
}
?>
