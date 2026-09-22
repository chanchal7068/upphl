<?php
// api/teams.php
// Router & Entry Point for Franchise Teams operations

$noJsonHeader = true;
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/TeamController.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = new TeamController($conn ?? null);
$action = strtolower(trim($_GET['action'] ?? ($_POST['action'] ?? '')));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    // Support JSON raw body as well as standard multipart/form-data
    $input = $_POST;
    if (empty($input)) {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $jsonData = json_decode($raw, true);
            if (is_array($jsonData)) {
                $input = $jsonData;
            }
        }
    }
    $action = strtolower(trim($input['action'] ?? $action));

    switch ($action) {
        case 'delete':
            $teamId = trim($input['id'] ?? ($input['teamId'] ?? ''));
            $res = $controller->delete($teamId);
            echo json_encode($res);
            break;

        case 'toggle_status':
        case 'togglestatus':
            $teamId = trim($input['id'] ?? ($input['teamId'] ?? ''));
            $res = $controller->toggleStatus($teamId);
            echo json_encode($res);
            break;

        case 'save':
        default:
            $res = $controller->save($input, $_FILES);
            echo json_encode($res);
            break;
    }
} else {
    // GET Request
    $action = strtolower(trim($_GET['action'] ?? ''));
    if ($action === 'get_single' || $action === 'get') {
        $teamId = trim($_GET['id'] ?? '');
        $team = $controller->getById($teamId);
        if ($team) {
            echo json_encode(['success' => true, 'team' => $team]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Team not found.']);
        }
    } elseif ($action === 'seasons') {
        $seasons = $controller->getSeasons();
        echo json_encode(['success' => true, 'seasons' => $seasons]);
    } else {
        $season = trim($_GET['season'] ?? '');
        $activeOnly = isset($_GET['active_only']) && ($_GET['active_only'] === '1' || $_GET['active_only'] === 'true');
        $teams = $controller->getAll($season, $activeOnly);
        $seasons = $controller->getSeasons();
        echo json_encode(['success' => true, 'teams' => $teams, 'seasons' => $seasons, 'total' => count($teams)]);
    }
}
