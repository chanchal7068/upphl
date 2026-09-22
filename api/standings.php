<?php
// api/standings.php
// Router & Entry Point for Points Table / Standings operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/StandingsController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new StandingsController($conn);
$action = strtolower(trim($_GET['action'] ?? ''));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    switch ($action) {
        case 'save':
            $controller->save();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            $controller->save();
            break;
    }
} else {
    $season = trim($_GET['season'] ?? '');
    $controller->getAll($season);
}
