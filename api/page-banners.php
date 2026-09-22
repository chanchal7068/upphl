<?php
// api/page-banners.php
// Router & Entry Point for Inner Page Banner operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/PageBannerController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new PageBannerController($conn);
$action = strtolower(trim($_GET['action'] ?? ''));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    switch ($action) {
        case 'save':
        default:
            $controller->save();
            break;
    }
} else {
    $controller->getAll();
}
