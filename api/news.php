<?php
// api/news.php
// Router & Entry Point for Latest News & Updates

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/NewsController.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new NewsController($conn ?? null);
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
            $newsId = trim($input['id'] ?? ($input['newsId'] ?? ''));
            $res = $controller->delete($newsId);
            echo json_encode($res);
            break;

        case 'toggle_status':
        case 'togglestatus':
            $newsId = trim($input['id'] ?? ($input['newsId'] ?? ''));
            $res = $controller->toggleStatus($newsId);
            echo json_encode($res);
            break;

        case 'save':
        default:
            $res = $controller->save($input, $_FILES);
            echo json_encode($res);
            break;
    }
} else {
    // GET Request
    if ($action === 'get_single' || $action === 'get') {
        $newsId = trim($_GET['id'] ?? '');
        $news = $controller->getById($newsId);
        if ($news) {
            echo json_encode(['success' => true, 'news' => $news]);
        } else {
            echo json_encode(['success' => false, 'message' => 'News not found.']);
        }
    } else {
        $activeOnly = isset($_GET['active_only']) && ($_GET['active_only'] === '1' || $_GET['active_only'] === 'true');
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
        $news = $controller->getAll($activeOnly, $limit);
        echo json_encode(['success' => true, 'news' => $news, 'total' => count($news)]);
    }
}
