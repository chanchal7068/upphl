<?php
// api/live-partners.php
// Router for Live Broadcast Partners API operations

header('Content-Type: application/json');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/LivePartnerController.php';

$controller = new LivePartnerController($conn ?? null);
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
        $activeOnly = isset($_GET['activeOnly']) ? filter_var($_GET['activeOnly'], FILTER_VALIDATE_BOOLEAN) : false;
        $controller->getAll($activeOnly);
        break;
}
