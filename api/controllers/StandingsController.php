<?php
// api/controllers/StandingsController.php
// Controller handling Points Table / Standings operations: list by season, add/update row, delete row

class StandingsController {
    private $conn;
    private $jsonFile;

    private $teamLogos = [
        'Barbarik Warriors'  => 'assets/images/teams/bhadohi-logo.jpeg',
        'Ghaziabad Panthers' => 'assets/images/teams/ghaziabad-logo.jpeg',
        'Gorakhpur Rowdies'  => 'assets/images/teams/gorakhapur-logo.jpeg',
        'Kashi Kings'        => 'assets/images/teams/kashi-logo.jpeg',
        'Mathura Brij Star'  => 'assets/images/teams/mathura-logo.jpeg',
        'Noida Blasters'     => 'assets/images/teams/noida-logo.jpeg'
    ];

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/standings.json';
    }

    // GET /api/standings.php?season=...
    public function getAll($filterSeason = '') {
        $standings = [];

        // 1. Fetch from JSON
        if (file_exists($this->jsonFile)) {
            $standings = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. Fallback to DB
        if (empty($standings) && $this->conn !== null) {
            $res = $this->conn->query("SELECT * FROM standings ORDER BY pts DESC, gd DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $standings[] = [
                        'id'       => $row['row_id'],
                        'season'   => $row['season'],
                        'teamName' => $row['team_name'],
                        'teamLogo' => $row['team_logo'],
                        'played'   => (int)$row['played'],
                        'won'      => (int)$row['won'],
                        'draw'     => (int)$row['draw'],
                        'lost'     => (int)$row['lost'],
                        'gf'       => (int)$row['gf'],
                        'ga'       => (int)$row['ga'],
                        'gd'       => (int)$row['gd'],
                        'pts'      => (int)$row['pts'],
                        'form'     => $row['form']
                    ];
                }
            }
        }

        // Extract unique seasons
        $seasons = [];
        foreach ($standings as $s) {
            $seasonName = trim($s['season'] ?? '');
            if (!empty($seasonName) && !in_array($seasonName, $seasons)) {
                $seasons[] = $seasonName;
            }
        }
        rsort($seasons);

        // Filter by season if specified
        $filtered = $standings;
        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $filtered = array_filter($filtered, fn($s) => strtolower($s['season'] ?? '') === strtolower($filterSeason));
        }

        // Sort by Points DESC, then GD DESC, then GF DESC
        usort($filtered, function($a, $b) {
            if ($b['pts'] !== $a['pts']) return $b['pts'] - $a['pts'];
            if ($b['gd'] !== $a['gd']) return $b['gd'] - $a['gd'];
            return $b['gf'] - $a['gf'];
        });

        echo json_encode([
            'success'   => true,
            'count'     => count($filtered),
            'seasons'   => array_values($seasons),
            'standings' => array_values($filtered)
        ]);
        exit;
    }

    // POST /api/standings.php?action=save
    public function save() {
        $rowId    = trim($_POST['rowId'] ?? '');
        $season   = trim($_POST['season'] ?? 'Season 2');
        $teamName = trim($_POST['teamName'] ?? '');
        $played   = (int)($_POST['played'] ?? 0);
        $won      = (int)($_POST['won'] ?? 0);
        $draw     = (int)($_POST['draw'] ?? 0);
        $lost     = (int)($_POST['lost'] ?? 0);
        $gf       = (int)($_POST['gf'] ?? 0);
        $ga       = (int)($_POST['ga'] ?? 0);
        $form     = trim($_POST['form'] ?? 'W,W,W,D,W');

        if (empty($teamName)) {
            echo json_encode(['success' => false, 'message' => 'Team name is required.']);
            exit;
        }

        if (empty($season)) {
            $season = 'Season 2';
        }

        // Auto calculate GD and PTS
        $gd = $gf - $ga;
        $pts = isset($_POST['pts']) && $_POST['pts'] !== '' ? (int)$_POST['pts'] : (($won * 2) + $draw);

        // Team Logo
        $teamLogo = $this->teamLogos[$teamName] ?? 'assets/images/teams/bhadohi-logo.jpeg';

        $standings = [];
        if (file_exists($this->jsonFile)) {
            $standings = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = !empty($rowId);
        if (!$isEdit) {
            $rowId = 'std_' . time() . '_' . rand(100, 999);
        }

        $rowData = [
            'id'       => $rowId,
            'season'   => $season,
            'teamName' => $teamName,
            'teamLogo' => $teamLogo,
            'played'   => $played,
            'won'      => $won,
            'draw'     => $draw,
            'lost'     => $lost,
            'gf'       => $gf,
            'ga'       => $ga,
            'gd'       => $gd,
            'pts'      => $pts,
            'form'     => $form
        ];

        if ($isEdit) {
            $updated = false;
            foreach ($standings as &$s) {
                if ($s['id'] === $rowId) {
                    $s = $rowData;
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $standings[] = $rowData;
            }
        } else {
            $standings[] = $rowData;
        }

        file_put_contents($this->jsonFile, json_encode($standings, JSON_PRETTY_PRINT));

        // Sync to DB
        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("INSERT INTO standings (row_id, season, team_name, team_logo, played, won, draw, lost, gf, ga, gd, pts, form)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                season=VALUES(season), team_name=VALUES(team_name), team_logo=VALUES(team_logo), played=VALUES(played), won=VALUES(won), draw=VALUES(draw), lost=VALUES(lost), gf=VALUES(gf), ga=VALUES(ga), gd=VALUES(gd), pts=VALUES(pts), form=VALUES(form)");
            if ($stmt) {
                $stmt->bind_param('ssssiiiiiiiis', $rowId, $season, $teamName, $teamLogo, $played, $won, $draw, $lost, $gf, $ga, $gd, $pts, $form);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'Team standings updated successfully!' : 'New team added to points table!',
            'row'     => $rowData
        ]);
        exit;
    }

    // POST /api/standings.php?action=delete
    public function delete() {
        $rowId = trim($_POST['rowId'] ?? '');
        if (empty($rowId)) {
            echo json_encode(['success' => false, 'message' => 'Row ID required.']);
            exit;
        }

        $standings = [];
        if (file_exists($this->jsonFile)) {
            $standings = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            $standings = array_values(array_filter($standings, fn($s) => ($s['id'] ?? '') !== $rowId));
            file_put_contents($this->jsonFile, json_encode($standings, JSON_PRETTY_PRINT));
        }

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM standings WHERE row_id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $rowId);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Team row deleted from points table.']);
        exit;
    }

    private function ensureTable() {
        $this->conn->query("CREATE TABLE IF NOT EXISTS standings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            row_id VARCHAR(50) UNIQUE NOT NULL,
            season VARCHAR(50) DEFAULT 'Season 2',
            team_name VARCHAR(150) NOT NULL,
            team_logo TEXT,
            played INT DEFAULT 0,
            won INT DEFAULT 0,
            draw INT DEFAULT 0,
            lost INT DEFAULT 0,
            gf INT DEFAULT 0,
            ga INT DEFAULT 0,
            gd INT DEFAULT 0,
            pts INT DEFAULT 0,
            form VARCHAR(50) DEFAULT 'W,W,W,D,W',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}
