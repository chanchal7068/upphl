<?php
// api/controllers/HomeStatsController.php
// Controller handling Home Page Hero Banner Statistics Counters

class HomeStatsController {
    private $conn;
    private $jsonFile;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/home_stats.json';
    }

    // GET /api/home-stats.php
    public function getStats() {
        $stats = $this->loadStats();
        echo json_encode([
            'success' => true,
            'stats'   => $stats
        ]);
        exit;
    }

    // POST /api/home-stats.php?action=save
    public function saveStats() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['upphl_admin_logged'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            exit;
        }

        $inputStats = $_POST['stats'] ?? null;
        if (!is_array($inputStats)) {
            // Check direct POST keys: target_1, label_1, suffix_1, decimals_1, etc.
            $inputStats = [];
            for ($i = 1; $i <= 4; $i++) {
                $target   = trim($_POST["target_$i"] ?? '0');
                $suffix   = trim($_POST["suffix_$i"] ?? '');
                $decimals = (int)($_POST["decimals_$i"] ?? 0);
                $label    = trim($_POST["label_$i"] ?? '');
                $icon     = trim($_POST["icon_$i"] ?? '');

                $inputStats[] = [
                    'id'       => "stat_$i",
                    'target'   => $target,
                    'suffix'   => $suffix,
                    'decimals' => $decimals,
                    'label'    => $label,
                    'icon'     => $icon
                ];
            }
        }

        $cleanList = [];
        foreach ($inputStats as $idx => $st) {
            $num = $idx + 1;
            $cleanList[] = [
                'id'       => "stat_" . $num,
                'target'   => trim((string)($st['target'] ?? '0')),
                'suffix'   => trim((string)($st['suffix'] ?? '')),
                'decimals' => (int)($st['decimals'] ?? 0),
                'label'    => trim((string)($st['label'] ?? "Stat $num")),
                'icon'     => trim((string)($st['icon'] ?? 'fa-solid fa-chart-simple'))
            ];
        }

        $dir = dirname($this->jsonFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->jsonFile, json_encode($cleanList, JSON_PRETTY_PRINT));

        echo json_encode([
            'success' => true,
            'message' => 'Home hero statistics updated successfully!',
            'stats'   => $cleanList
        ]);
        exit;
    }

    private function loadStats() {
        if (file_exists($this->jsonFile)) {
            $data = json_decode(file_get_contents($this->jsonFile), true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }

        return [
            [
                'id'       => 'stat_1',
                'target'   => '6',
                'suffix'   => '',
                'decimals' => 0,
                'label'    => 'Franchise Teams',
                'icon'     => 'fa-solid fa-shield-halved'
            ],
            [
                'id'       => 'stat_2',
                'target'   => '120',
                'suffix'   => '+',
                'decimals' => 0,
                'label'    => 'Scouted Players',
                'icon'     => 'fa-solid fa-users'
            ],
            [
                'id'       => 'stat_3',
                'target'   => '2',
                'suffix'   => '',
                'decimals' => 0,
                'label'    => 'Successful Seasons',
                'icon'     => 'fa-solid fa-trophy'
            ],
            [
                'id'       => 'stat_4',
                'target'   => '1.5',
                'suffix'   => 'L+',
                'decimals' => 1,
                'label'    => 'Dedicated Fans',
                'icon'     => 'fa-solid fa-heart'
            ]
        ];
    }
}
