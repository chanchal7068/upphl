<?php
// api/controllers/MvpController.php
// Controller handling MVP Players operations: season-wise, day-wise, match-wise management

class MvpController {
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

    private $teamSlugs = [
        'Barbarik Warriors'  => 'barbarik-warriors',
        'Ghaziabad Panthers' => 'ghaziabad-panthers',
        'Gorakhpur Rowdies'  => 'gorakhpur-rowdies',
        'Kashi Kings'        => 'kashi-kings',
        'Mathura Brij Star'  => 'mathura-brij-star',
        'Noida Blasters'     => 'noida-blasters'
    ];

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/mvp.json';
    }

    // GET /api/mvp.php?season=...&day=...
    public function getAll($filterSeason = '', $filterDay = '') {
        $players = [];

        // 1. Fetch from JSON
        if (file_exists($this->jsonFile)) {
            $players = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. Fallback to DB
        if (empty($players) && $this->conn !== null) {
            $this->ensureTable();
            $res = $this->conn->query("SELECT * FROM mvp_players ORDER BY season DESC, match_day ASC, match_number ASC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $players[] = [
                        'id'           => $row['mvp_id'],
                        'season'       => $row['season'],
                        'matchDay'     => $row['match_day'],
                        'matchNumber'  => $row['match_number'],
                        'playerName'   => $row['player_name'],
                        'teamName'     => $row['team_name'],
                        'teamLogo'     => $row['team_logo'],
                        'teamSlug'     => $row['team_slug'] ?? '',
                        'awardTitle'   => $row['award_title'],
                        'playerPhoto'  => $row['player_photo'] ?? '',
                        'jerseyNumber' => $row['jersey_number'] ?? '',
                        'position'     => $row['position'] ?? '',
                        'goals'        => $row['goals'] ?? '',
                        'rating'       => $row['rating'] ?? '',
                        'createdAt'    => $row['created_at']
                    ];
                }
            }
        }

        // Extract unique seasons and days
        $seasons = [];
        $days = [];
        foreach ($players as &$p) {
            $s = trim($p['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
            $d = trim($p['matchDay'] ?? '');
            if (!empty($d) && !in_array($d, $days)) {
                $days[] = $d;
            }

            if (empty($p['teamLogo']) && isset($this->teamLogos[$p['teamName']])) {
                $p['teamLogo'] = $this->teamLogos[$p['teamName']];
            }
            if (empty($p['teamSlug']) && isset($this->teamSlugs[$p['teamName']])) {
                $p['teamSlug'] = $this->teamSlugs[$p['teamName']];
            }
        }
        unset($p);
        rsort($seasons);

        // Filter by season
        $filtered = $players;
        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $filtered = array_filter($filtered, fn($p) => strtolower($p['season'] ?? '') === strtolower($filterSeason));
        }

        // Filter by day
        if (!empty($filterDay) && strtolower($filterDay) !== 'all') {
            $filtered = array_filter($filtered, fn($p) => strtolower($p['matchDay'] ?? '') === strtolower($filterDay));
        }

        // Sort by Day then Match
        usort($filtered, function($a, $b) {
            $c = strnatcasecmp($a['matchDay'] ?? '', $b['matchDay'] ?? '');
            if ($c !== 0) return $c;
            return strnatcasecmp($a['matchNumber'] ?? '', $b['matchNumber'] ?? '');
        });

        echo json_encode([
            'success'  => true,
            'count'    => count($filtered),
            'seasons'  => array_values($seasons),
            'days'     => array_values($days),
            'players'  => array_values($filtered)
        ]);
        exit;
    }

    // POST /api/mvp.php?action=save
    public function save() {
        $mvpId        = trim($_POST['mvpId'] ?? '');
        $season       = trim($_POST['season'] ?? 'Season 1');
        $matchDay     = trim($_POST['matchDay'] ?? 'Day 1');
        $matchNumber  = trim($_POST['matchNumber'] ?? 'Match 1');
        $playerName   = trim($_POST['playerName'] ?? '');
        $teamName     = trim($_POST['teamName'] ?? '');
        $awardTitle   = trim($_POST['awardTitle'] ?? 'Player of the Match');
        $jerseyNumber = trim($_POST['jerseyNumber'] ?? '');
        $position     = trim($_POST['position'] ?? '');
        $goals        = trim($_POST['goals'] ?? '');
        $rating       = trim($_POST['rating'] ?? '');
        $existingPhoto= trim($_POST['existingPhoto'] ?? '');

        if (empty($playerName)) {
            echo json_encode(['success' => false, 'message' => 'Player name is required.']);
            exit;
        }

        if (empty($teamName)) {
            echo json_encode(['success' => false, 'message' => 'Team name is required.']);
            exit;
        }

        if (empty($season)) $season = 'Season 1';
        if (empty($matchDay)) $matchDay = 'Day 1';
        if (empty($matchNumber)) $matchNumber = 'Match 1';
        if (empty($awardTitle)) $awardTitle = 'Player of the Match';

        $teamLogo = $this->teamLogos[$teamName] ?? 'assets/images/teams/bhadohi-logo.jpeg';
        $teamSlug = $this->teamSlugs[$teamName] ?? 'barbarik-warriors';

        $playerPhoto = $existingPhoto;
        // Handle image upload if provided
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/mvp/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $filename = 'mvp_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $target = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                    $playerPhoto = 'uploads/mvp/' . $filename;
                }
            }
        }

        $players = [];
        if (file_exists($this->jsonFile)) {
            $players = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = !empty($mvpId);
        if (!$isEdit) {
            $mvpId = 'mvp_' . time() . '_' . rand(100, 999);
        }

        $mvpData = [
            'id'           => $mvpId,
            'season'       => $season,
            'matchDay'     => $matchDay,
            'matchNumber'  => $matchNumber,
            'playerName'   => $playerName,
            'teamName'     => $teamName,
            'teamLogo'     => $teamLogo,
            'teamSlug'     => $teamSlug,
            'awardTitle'   => $awardTitle,
            'playerPhoto'  => $playerPhoto,
            'jerseyNumber' => $jerseyNumber,
            'position'     => $position,
            'goals'        => $goals,
            'rating'       => $rating,
            'createdAt'    => date('Y-m-d H:i:s')
        ];

        if ($isEdit) {
            $updated = false;
            foreach ($players as &$p) {
                if ($p['id'] === $mvpId) {
                    $mvpData['createdAt'] = $p['createdAt'] ?? date('Y-m-d H:i:s');
                    if (empty($playerPhoto) && !empty($p['playerPhoto'])) {
                        $mvpData['playerPhoto'] = $p['playerPhoto'];
                    }
                    $p = $mvpData;
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $players[] = $mvpData;
            }
        } else {
            $players[] = $mvpData;
        }

        file_put_contents($this->jsonFile, json_encode($players, JSON_PRETTY_PRINT));

        // Sync to MySQL
        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("INSERT INTO mvp_players (mvp_id, season, match_day, match_number, player_name, team_name, team_logo, team_slug, award_title, player_photo, jersey_number, position, goals, rating)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                season=VALUES(season), match_day=VALUES(match_day), match_number=VALUES(match_number), player_name=VALUES(player_name),
                team_name=VALUES(team_name), team_logo=VALUES(team_logo), team_slug=VALUES(team_slug), award_title=VALUES(award_title),
                player_photo=VALUES(player_photo), jersey_number=VALUES(jersey_number), position=VALUES(position), goals=VALUES(goals), rating=VALUES(rating)");
            if ($stmt) {
                $stmt->bind_param('ssssssssssssss', $mvpId, $season, $matchDay, $matchNumber, $playerName, $teamName, $teamLogo, $teamSlug, $awardTitle, $playerPhoto, $jerseyNumber, $position, $goals, $rating);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'MVP Player details updated successfully!' : 'New MVP Player added successfully!',
            'player'  => $mvpData
        ]);
        exit;
    }

    // POST /api/mvp.php?action=delete
    public function delete() {
        $mvpId = trim($_POST['mvpId'] ?? '');
        if (empty($mvpId)) {
            echo json_encode(['success' => false, 'message' => 'MVP ID required.']);
            exit;
        }

        $players = [];
        if (file_exists($this->jsonFile)) {
            $players = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            $players = array_values(array_filter($players, fn($p) => ($p['id'] ?? '') !== $mvpId));
            file_put_contents($this->jsonFile, json_encode($players, JSON_PRETTY_PRINT));
        }

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("DELETE FROM mvp_players WHERE mvp_id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $mvpId);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'MVP Player deleted successfully.']);
        exit;
    }

    private function ensureTable() {
        if (!$this->conn) return;

        $this->conn->query("CREATE TABLE IF NOT EXISTS mvp_players (
            id INT AUTO_INCREMENT PRIMARY KEY,
            mvp_id VARCHAR(50) UNIQUE NOT NULL,
            season VARCHAR(50) DEFAULT 'Season 1',
            match_day VARCHAR(50) DEFAULT 'Day 1',
            match_number VARCHAR(50) DEFAULT 'Match 1',
            player_name VARCHAR(150) NOT NULL,
            team_name VARCHAR(150) NOT NULL,
            team_logo TEXT,
            team_slug VARCHAR(100) DEFAULT '',
            award_title VARCHAR(150) DEFAULT 'Player of the Match',
            player_photo TEXT,
            jersey_number VARCHAR(20) DEFAULT '',
            position VARCHAR(100) DEFAULT '',
            goals VARCHAR(50) DEFAULT '',
            rating VARCHAR(50) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}
