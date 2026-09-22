<?php
// api/players.php
// Router & Entry Point for Player operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/PlayerController.php';

$controller = new PlayerController($conn);
$action = strtolower(trim($_GET['action'] ?? ''));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    switch ($action) {
        case 'delete':
            header('Content-Type: application/json');
            $controller->deletePlayer();
            break;
        case 'update-status':
        case 'status':
            header('Content-Type: application/json');
            $controller->updateStatus();
            break;
        default:
            header('Content-Type: application/json');
            $controller->updateStatus();
            break;
    }
} else {
    // GET request
    if ($action === 'export-csv' || $action === 'export') {
        $controller->exportCsv();
    } else {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        $controller->getApproved();
    }
}
