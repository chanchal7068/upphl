<?php
// api/gallery.php
// Router & Entry Point for Gallery operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/GalleryController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new GalleryController($conn);
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
        default:
            $controller->save();
            break;
    }
} else {
    $filterSeason   = trim($_GET['season'] ?? '');
    $filterCategory = trim($_GET['category'] ?? ($_GET['cat'] ?? ''));
    $controller->getAll($filterSeason, $filterCategory);
}
