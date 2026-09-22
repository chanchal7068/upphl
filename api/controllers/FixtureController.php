<?php
// api/controllers/FixtureController.php
// Controller handling Match Fixtures operations: list with auto-status calculation, add/update fixture, delete fixture

class FixtureController {
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
        $this->jsonFile = __DIR__ . '/../../uploads/fixtures.json';
    }

    // Compute status automatically based on match date, start time, and end time
    public function computeStatus($fixture) {
        $manualStatus = trim($fixture['status'] ?? '');
        if (!empty($manualStatus) && !in_array(strtolower($manualStatus), ['auto', 'upcoming'])) {
            return $manualStatus;
        }

        $dateStr = trim($fixture['matchDate'] ?? '');
        $startTimeStr = trim($fixture['startTime'] ?? ($fixture['matchTime'] ?? '18:00'));
        $endTimeStr = trim($fixture['endTime'] ?? '');

        if (empty($dateStr)) {
            return 'Upcoming';
        }

        $startTimestamp = strtotime("$dateStr $startTimeStr");
        if (!$startTimestamp) {
            $startTimestamp = strtotime($dateStr);
        }

        if (!$startTimestamp) {
            return 'Upcoming';
        }

        if (!empty($endTimeStr)) {
            $endTimestamp = strtotime("$dateStr $endTimeStr");
            if (!$endTimestamp || $endTimestamp < $startTimestamp) {
                // In case of next day or parse issue fallback
                $endTimestamp = $startTimestamp + (2 * 3600);
            }
        } else {
            $endTimestamp = $startTimestamp + (2 * 3600); // 2 hours match window
        }

        $now = time();

        if ($now > $endTimestamp) {
            return 'Completed';
        } elseif ($now >= $startTimestamp && $now <= $endTimestamp) {
            return 'Live';
        } else {
            return 'Upcoming';
        }
    }

    // GET /api/fixtures.php?season=...&status=...
    public function getAll($filterSeason = '', $filterStatus = '') {
        $fixtures = [];

        // 1. Fetch from JSON
        if (file_exists($this->jsonFile)) {
            $fixtures = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. Fallback to DB
        if (empty($fixtures) && $this->conn !== null) {
            $this->ensureTable();
            $res = $this->conn->query("SELECT * FROM fixtures ORDER BY match_date ASC, start_time ASC, match_time ASC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $fixtures[] = [
                        'id'          => $row['fixture_id'],
                        'season'      => $row['season'],
                        'matchDay'    => $row['match_day'] ?? 'Day 1',
                        'matchNumber' => $row['match_number'] ?? 'Match 1',
                        'matchTitle'  => $row['match_title'],
                        'team1Name'   => $row['team1_name'],
                        'team1Logo'   => $row['team1_logo'],
                        'team2Name'   => $row['team2_name'],
                        'team2Logo'   => $row['team2_logo'],
                        'matchDate'   => $row['match_date'],
                        'startTime'   => $row['start_time'] ?? ($row['match_time'] ?? '04:00 PM'),
                        'endTime'     => $row['end_time'] ?? '',
                        'matchTime'   => $row['start_time'] ?? ($row['match_time'] ?? '04:00 PM'),
                        'stadium'     => $row['stadium'],
                        'team1Score'  => $row['team1_score'] !== null ? (int)$row['team1_score'] : '',
                        'team2Score'  => $row['team2_score'] !== null ? (int)$row['team2_score'] : '',
                        'status'      => $row['status'],
                        'createdAt'   => $row['created_at']
                    ];
                }
            }
        }

        // Extract unique seasons
        $seasons = [];
        foreach ($fixtures as $f) {
            $sName = trim($f['season'] ?? '');
            if (!empty($sName) && !in_array($sName, $seasons)) {
                $seasons[] = $sName;
            }
        }
        rsort($seasons);

        // Process dynamic computed statuses & ensure matchDay/matchNumber, timings and logos
        foreach ($fixtures as &$f) {
            if (empty($f['startTime']) && !empty($f['matchTime'])) {
                $f['startTime'] = $f['matchTime'];
            }
            if (empty($f['matchTime']) && !empty($f['startTime'])) {
                $f['matchTime'] = $f['startTime'];
            }
            if (!isset($f['endTime'])) {
                $f['endTime'] = '';
            }

            $f['computedStatus'] = $this->computeStatus($f);
            if (empty($f['team1Logo']) && isset($this->teamLogos[$f['team1Name']])) {
                $f['team1Logo'] = $this->teamLogos[$f['team1Name']];
            }
            if (empty($f['team2Logo']) && isset($this->teamLogos[$f['team2Name']])) {
                $f['team2Logo'] = $this->teamLogos[$f['team2Name']];
            }
            if (empty($f['matchDay'])) {
                $f['matchDay'] = 'Day 1';
            }
            if (empty($f['matchNumber'])) {
                $f['matchNumber'] = 'Match 1';
            }
        }
        unset($f);

        // Filter by Season
        $filtered = $fixtures;
        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $filtered = array_filter($filtered, fn($item) => strtolower($item['season'] ?? '') === strtolower($filterSeason));
        }

        // Filter by Status
        if (!empty($filterStatus) && strtolower($filterStatus) !== 'all') {
            $filtered = array_filter($filtered, fn($item) => strtolower($item['computedStatus'] ?? '') === strtolower($filterStatus));
        }

        // Sort: Chronologically by matchDate and startTime
        usort($filtered, function($a, $b) {
            $timeA = $a['startTime'] ?? ($a['matchTime'] ?? '');
            $timeB = $b['startTime'] ?? ($b['matchTime'] ?? '');
            $tA = strtotime(($a['matchDate'] ?? '') . ' ' . $timeA);
            $tB = strtotime(($b['matchDate'] ?? '') . ' ' . $timeB);
            if ($tA === $tB) {
                return strnatcasecmp($a['matchNumber'] ?? '', $b['matchNumber'] ?? '');
            }
            return $tA - $tB;
        });

        echo json_encode([
            'success'  => true,
            'count'    => count($filtered),
            'seasons'  => array_values($seasons),
            'fixtures' => array_values($filtered)
        ]);
        exit;
    }

    // POST /api/fixtures.php?action=save
    public function save() {
        $fixtureId   = trim($_POST['fixtureId'] ?? '');
        $season      = trim($_POST['season'] ?? 'Season 2');
        $matchDay    = trim($_POST['matchDay'] ?? 'Day 1');
        $matchNumber = trim($_POST['matchNumber'] ?? 'Match 1');
        $matchTitle  = trim($_POST['matchTitle'] ?? '');
        $team1Name   = trim($_POST['team1Name'] ?? '');
        $team2Name   = trim($_POST['team2Name'] ?? '');
        $team1Logo   = trim($_POST['team1Logo'] ?? '');
        $team2Logo   = trim($_POST['team2Logo'] ?? '');
        $matchDate   = trim($_POST['matchDate'] ?? '');
        $startTime   = trim($_POST['startTime'] ?? ($_POST['matchTime'] ?? '04:00 PM'));
        $endTime     = trim($_POST['endTime'] ?? '');
        $stadium     = trim($_POST['stadium'] ?? 'K.D. Singh Babu Stadium, Lucknow');
        $status      = trim($_POST['status'] ?? 'Auto');
        $team1Score  = isset($_POST['team1Score']) && $_POST['team1Score'] !== '' ? (int)$_POST['team1Score'] : null;
        $team2Score  = isset($_POST['team2Score']) && $_POST['team2Score'] !== '' ? (int)$_POST['team2Score'] : null;

        if (empty($team1Name) || empty($team2Name)) {
            echo json_encode(['success' => false, 'message' => 'Both Team 1 and Team 2 are required.']);
            exit;
        }

        if (empty($matchDate)) {
            echo json_encode(['success' => false, 'message' => 'Match Date is required.']);
            exit;
        }

        if (empty($matchDay)) {
            $matchDay = 'Day 1';
        }

        if (empty($matchNumber)) {
            $matchNumber = 'Match 1';
        }

        if (empty($matchTitle)) {
            $matchTitle = 'League Stage';
        }

        if (empty($season)) {
            $season = 'Season 2';
        }

        if (empty($startTime)) {
            $startTime = '04:00 PM';
        }

        if (empty($team1Logo)) {
            $team1Logo = $this->teamLogos[$team1Name] ?? 'assets/images/teams/bhadohi-logo.jpeg';
        }
        if (empty($team2Logo)) {
            $team2Logo = $this->teamLogos[$team2Name] ?? 'assets/images/teams/ghaziabad-logo.jpeg';
        }

        $fixtures = [];
        if (file_exists($this->jsonFile)) {
            $fixtures = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = !empty($fixtureId);
        if (!$isEdit) {
            $fixtureId = 'fix_' . time() . '_' . rand(100, 999);
        }

        $fixtureData = [
            'id'          => $fixtureId,
            'season'      => $season,
            'matchDay'    => $matchDay,
            'matchNumber' => $matchNumber,
            'matchTitle'  => $matchTitle,
            'team1Name'   => $team1Name,
            'team1Logo'   => $team1Logo,
            'team2Name'   => $team2Name,
            'team2Logo'   => $team2Logo,
            'matchDate'   => $matchDate,
            'startTime'   => $startTime,
            'endTime'     => $endTime,
            'matchTime'   => $startTime,
            'stadium'     => $stadium,
            'team1Score'  => $team1Score,
            'team2Score'  => $team2Score,
            'status'      => $status,
            'createdAt'   => date('Y-m-d H:i:s')
        ];

        if ($isEdit) {
            $updated = false;
            foreach ($fixtures as &$f) {
                if ($f['id'] === $fixtureId) {
                    $fixtureData['createdAt'] = $f['createdAt'] ?? date('Y-m-d H:i:s');
                    $f = $fixtureData;
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $fixtures[] = $fixtureData;
            }
        } else {
            $fixtures[] = $fixtureData;
        }

        file_put_contents($this->jsonFile, json_encode($fixtures, JSON_PRETTY_PRINT));

        // Sync to MySQL
        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("INSERT INTO fixtures (fixture_id, season, match_day, match_number, match_title, team1_name, team1_logo, team2_name, team2_logo, match_date, start_time, end_time, match_time, stadium, team1_score, team2_score, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                season=VALUES(season), match_day=VALUES(match_day), match_number=VALUES(match_number), match_title=VALUES(match_title), team1_name=VALUES(team1_name), team1_logo=VALUES(team1_logo),
                team2_name=VALUES(team2_name), team2_logo=VALUES(team2_logo), match_date=VALUES(match_date), start_time=VALUES(start_time), end_time=VALUES(end_time), match_time=VALUES(match_time),
                stadium=VALUES(stadium), team1_score=VALUES(team1_score), team2_score=VALUES(team2_score), status=VALUES(status)");
            if ($stmt) {
                $stmt->bind_param('ssssssssssssssiis', $fixtureId, $season, $matchDay, $matchNumber, $matchTitle, $team1Name, $team1Logo, $team2Name, $team2Logo, $matchDate, $startTime, $endTime, $startTime, $stadium, $team1Score, $team2Score, $status);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'Fixture updated successfully!' : 'New match fixture added successfully!',
            'fixture' => $fixtureData
        ]);
        exit;
    }

    // POST /api/fixtures.php?action=delete
    public function delete() {
        $fixtureId = trim($_POST['fixtureId'] ?? '');
        if (empty($fixtureId)) {
            echo json_encode(['success' => false, 'message' => 'Fixture ID required.']);
            exit;
        }

        $fixtures = [];
        if (file_exists($this->jsonFile)) {
            $fixtures = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            $fixtures = array_values(array_filter($fixtures, fn($f) => ($f['id'] ?? '') !== $fixtureId));
            file_put_contents($this->jsonFile, json_encode($fixtures, JSON_PRETTY_PRINT));
        }

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("DELETE FROM fixtures WHERE fixture_id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $fixtureId);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Match fixture deleted successfully.']);
        exit;
    }

    private function ensureTable() {
        if (!$this->conn) return;

        $this->conn->query("CREATE TABLE IF NOT EXISTS fixtures (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fixture_id VARCHAR(50) UNIQUE NOT NULL,
            season VARCHAR(50) DEFAULT 'Season 2',
            match_day VARCHAR(50) DEFAULT 'Day 1',
            match_number VARCHAR(50) DEFAULT 'Match 1',
            match_title VARCHAR(150) DEFAULT 'League Stage',
            team1_name VARCHAR(150) NOT NULL,
            team1_logo TEXT,
            team2_name VARCHAR(150) NOT NULL,
            team2_logo TEXT,
            match_date VARCHAR(50) NOT NULL,
            start_time VARCHAR(50) DEFAULT '04:00 PM',
            end_time VARCHAR(50) DEFAULT '',
            match_time VARCHAR(50) DEFAULT '04:00 PM',
            stadium VARCHAR(255) DEFAULT 'K.D. Singh Babu Stadium, Lucknow',
            team1_score INT NULL,
            team2_score INT NULL,
            status VARCHAR(50) DEFAULT 'Auto',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Ensure columns match_day, match_number, start_time, and end_time exist
        $res = $this->conn->query("SHOW COLUMNS FROM fixtures LIKE 'match_day'");
        if ($res && $res->num_rows == 0) {
            $this->conn->query("ALTER TABLE fixtures ADD COLUMN match_day VARCHAR(50) DEFAULT 'Day 1' AFTER season");
        }
        $res = $this->conn->query("SHOW COLUMNS FROM fixtures LIKE 'match_number'");
        if ($res && $res->num_rows == 0) {
            $this->conn->query("ALTER TABLE fixtures ADD COLUMN match_number VARCHAR(50) DEFAULT 'Match 1' AFTER match_day");
        }
        $res = $this->conn->query("SHOW COLUMNS FROM fixtures LIKE 'start_time'");
        if ($res && $res->num_rows == 0) {
            $this->conn->query("ALTER TABLE fixtures ADD COLUMN start_time VARCHAR(50) DEFAULT '04:00 PM' AFTER match_date");
        }
        $res = $this->conn->query("SHOW COLUMNS FROM fixtures LIKE 'end_time'");
        if ($res && $res->num_rows == 0) {
            $this->conn->query("ALTER TABLE fixtures ADD COLUMN end_time VARCHAR(50) DEFAULT '' AFTER start_time");
        }
    }
}
