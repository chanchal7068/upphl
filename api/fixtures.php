<?php
// api/fixtures.php
// Router & Entry Point for Match Fixtures operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/FixtureController.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new FixtureController($conn ?? null);
$action = strtolower(trim($_GET['action'] ?? ($_POST['action'] ?? '')));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    switch ($action) {
        case 'delete':
            $controller->delete();
            break;
        case 'save':
        default:
            $controller->save();
            break;
    }
} else {
    $season = trim($_GET['season'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $controller->getAll($season, $status);
}
