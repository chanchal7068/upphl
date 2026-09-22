<?php
// api/announcements.php
// Router & Entry Point for Marquee Announcements

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/AnnouncementController.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new AnnouncementController($conn ?? null);
$action = strtolower(trim($_GET['action'] ?? ($_POST['action'] ?? '')));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $jsonData = json_decode($raw, true);
            if (is_array($jsonData)) {
                $input = $jsonData;
            }
        }
    }
    $action = strtolower(trim($input['action'] ?? $action));

    switch ($action) {
        case 'delete':
            $annId = trim($input['id'] ?? ($input['announcementId'] ?? ''));
            $res = $controller->delete($annId);
            echo json_encode($res);
            break;

        case 'toggle_status':
        case 'togglestatus':
            $annId = trim($input['id'] ?? ($input['announcementId'] ?? ''));
            $res = $controller->toggleStatus($annId);
            echo json_encode($res);
            break;

        case 'save':
        default:
            $res = $controller->save($input);
            echo json_encode($res);
            break;
    }
} else {
    // GET Request
    if ($action === 'get_single' || $action === 'get') {
        $annId = trim($_GET['id'] ?? '');
        $item = $controller->getById($annId);
        if ($item) {
            echo json_encode(['success' => true, 'data' => $item]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Announcement not found.']);
        }
    } else {
        $scope = strtolower(trim($_GET['scope'] ?? 'active'));
        $activeOnly = ($scope !== 'all');
        $list = $controller->getAll($activeOnly);
        echo json_encode(['success' => true, 'data' => $list]);
    }
}
