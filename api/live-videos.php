<?php
// api/live-videos.php
// Router for Live Season Videos & Replays API operations

header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/LiveVideoController.php';

$controller = new LiveVideoController($conn ?? null);
$action = $_GET['action'] ?? ($_POST['action'] ?? 'getAll');

switch ($action) {
    case 'save':
        $controller->save();
        break;

    case 'delete':
        $controller->delete();
        break;

    case 'toggle-status':
        $controller->toggleStatus();
        break;

    case 'getAll':
    default:
        $filterSeason   = trim($_GET['season'] ?? '');
        $filterCategory = trim($_GET['category'] ?? '');
        $activeOnly     = isset($_GET['activeOnly']) ? filter_var($_GET['activeOnly'], FILTER_VALIDATE_BOOLEAN) : false;
        $controller->getAll($filterSeason, $filterCategory, $activeOnly);
        break;
}
