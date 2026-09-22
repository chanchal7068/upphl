<?php
// api/contact.php
// Router & Entry Point for Contact Settings & Contact Messages

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/ContactController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new ContactController($conn);
$action = strtolower(trim($_GET['action'] ?? ''));
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    switch ($action) {
        case 'save-settings':
        case 'settings':
            $controller->saveSettings();
            break;
        case 'submit':
        case 'submit-message':
            $controller->submitMessage();
            break;
        case 'delete-message':
        case 'delete':
            $controller->deleteMessage();
            break;
        default:
            // Check if it's contact form submission or settings
            if (isset($_POST['name']) && isset($_POST['email'])) {
                $controller->submitMessage();
            } else {
                $controller->saveSettings();
            }
            break;
    }
} else {
    // GET: return settings
    $controller->getSettings();
}
