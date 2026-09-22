<?php
// api/banners.php
// Router & Entry Point for Banner operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/BannerController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new BannerController($conn);
$action = strtolower(trim($_GET['action'] ?? ''));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    switch ($action) {
        case 'save':
            $controller->save();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'toggle':
        case 'toggle-status':
            $controller->toggleStatus();
            break;
        default:
            // Default POST action is save
            $controller->save();
            break;
    }
} else {
    // GET request: all or active banners
    $scope = $_GET['scope'] ?? ($_GET['all'] ?? '') === '1' ? 'all' : 'active';
    if (isset($_GET['admin']) && $_GET['admin'] === '1') $scope = 'all';
    $controller->getAll($scope);
}
