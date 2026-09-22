<?php
// api/partners.php
// Router & Entry Point for Our Partners & Sponsors

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/PartnerController.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new PartnerController($conn ?? null);
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
            $partnerId = trim($input['id'] ?? ($input['partnerId'] ?? ''));
            $res = $controller->delete($partnerId);
            echo json_encode($res);
            break;

        case 'toggle_status':
        case 'togglestatus':
            $partnerId = trim($input['id'] ?? ($input['partnerId'] ?? ''));
            $res = $controller->toggleStatus($partnerId);
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
        $partnerId = trim($_GET['id'] ?? '');
        $partner = $controller->getById($partnerId);
        if ($partner) {
            echo json_encode(['success' => true, 'partner' => $partner]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Partner not found.']);
        }
    } else {
        $activeOnly = isset($_GET['active_only']) && ($_GET['active_only'] === '1' || $_GET['active_only'] === 'true');
        $partners = $controller->getAll($activeOnly);
        echo json_encode(['success' => true, 'partners' => $partners, 'total' => count($partners)]);
    }
}
