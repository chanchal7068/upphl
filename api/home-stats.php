<?php
// api/home-stats.php
// Router & Entry Point for Home Page Hero Banner Statistics Counters

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/HomeStatsController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new HomeStatsController($conn);
$action = strtolower(trim($_GET['action'] ?? ''));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    switch ($action) {
        case 'save':
        case 'save-stats':
        case 'update':
        default:
            $controller->saveStats();
            break;
    }
} else {
    // GET: Return current stats
    $controller->getStats();
}
