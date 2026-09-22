<?php
// api/committee.php
// API endpoint for League Management Committee Members

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/CommitteeController.php';

$controller = new CommitteeController($conn ?? null);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $activeOnly = isset($_GET['active']) && ($_GET['active'] === '1' || $_GET['active'] === 'true');
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $member = $controller->getById(trim($_GET['id']));
        if ($member) {
            echo json_encode(['success' => true, 'member' => $member]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Committee member not found']);
        }
        exit;
    }

    $members = $controller->getAll($activeOnly);
    echo json_encode(['success' => true, 'count' => count($members), 'data' => $members]);
    exit;
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? 'save';

    if ($action === 'delete') {
        $memberId = trim($_POST['memberId'] ?? ($_POST['id'] ?? ''));
        if (empty($memberId)) {
            echo json_encode(['success' => false, 'message' => 'Missing memberId']);
            exit;
        }
        $res = $controller->delete($memberId);
        echo json_encode($res);
        exit;
    }

    if ($action === 'toggle_status') {
        $memberId = trim($_POST['memberId'] ?? ($_POST['id'] ?? ''));
        if (empty($memberId)) {
            echo json_encode(['success' => false, 'message' => 'Missing memberId']);
            exit;
        }
        $res = $controller->toggleStatus($memberId);
        echo json_encode($res);
        exit;
    }

    if ($action === 'save') {
        $res = $controller->save($_POST, $_FILES);
        echo json_encode($res);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action: ' . $action]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
