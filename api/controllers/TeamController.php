<?php
// api/controllers/TeamController.php
// Controller handling Franchise Teams management: season-wise, coaches, players, logos, posters

class TeamController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn = null) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/teams.json';
        $this->uploadDir = __DIR__ . '/../../uploads/teams/';
        if (!file_exists($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }
    }

    // Ensure database table exists
    private function ensureTable() {
        if ($this->conn === null) return;
        $sql = "CREATE TABLE IF NOT EXISTS teams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_id VARCHAR(100) UNIQUE NOT NULL,
            team_name VARCHAR(150) NOT NULL,
            season VARCHAR(50) DEFAULT 'Season 1',
            city VARCHAR(100) DEFAULT '',
            logo_url VARCHAR(255) NOT NULL,
            poster_url VARCHAR(255) DEFAULT '',
            gradient VARCHAR(255) DEFAULT '',
            accent_color VARCHAR(30) DEFAULT '#ea580c',
            owner_data TEXT DEFAULT NULL,
            coach_data TEXT DEFAULT NULL,
            captain_data TEXT DEFAULT NULL,
            players_data LONGTEXT DEFAULT NULL,
            sort_order INT DEFAULT 0,
            status ENUM('Active','Inactive') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        @$this->conn->query($sql);
    }

    // Get all teams
    public function getAll($filterSeason = '', $activeOnly = false) {
        $teams = [];

        // 1. Read from JSON
        if (file_exists($this->jsonFile)) {
            $teams = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. DB fallback if JSON is empty and DB exists
        if (empty($teams) && $this->conn !== null) {
            $this->ensureTable();
            $res = $this->conn->query("SELECT * FROM teams ORDER BY sort_order ASC, id ASC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $teams[] = [
                        'id'          => $row['team_id'],
                        'name'        => $row['team_name'],
                        'season'      => $row['season'] ?? 'Season 1',
                        'city'        => $row['city'] ?? '',
                        'logo'        => $row['logo_url'],
                        'posterImage' => $row['poster_url'] ?? '',
                        'gradient'    => $row['gradient'] ?? '',
                        'accentColor' => $row['accent_color'] ?? '#ea580c',
                        'owner'       => !empty($row['owner_data']) ? json_decode($row['owner_data'], true) : ['name' => '', 'role' => 'Franchise Owner'],
                        'coach'       => !empty($row['coach_data']) ? json_decode($row['coach_data'], true) : ['name' => '', 'role' => 'Head Coach'],
                        'captain'     => !empty($row['captain_data']) ? json_decode($row['captain_data'], true) : ['name' => '', 'role' => 'Team Captain'],
                        'players'     => !empty($row['players_data']) ? json_decode($row['players_data'], true) : [],
                        'sortOrder'   => (int)($row['sort_order'] ?? 0),
                        'status'      => $row['status'] ?? 'Active'
                    ];
                }
            }
        }

        // Normalize and ensure footerLogo & posterImage exist
        foreach ($teams as &$t) {
            if (empty($t['footerLogo'])) {
                $t['footerLogo'] = $t['logo'] ?? 'assets/images/teams/bhadohi-logo.jpeg';
            }
            if (empty($t['logo'])) {
                $t['logo'] = $t['footerLogo'];
            }
            if (empty($t['posterImage'])) {
                $t['posterImage'] = $t['footerLogo'];
            }
        }
        unset($t);

        // Sort by sortOrder ASC
        usort($teams, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['name'] ?? '', $b['name'] ?? '');
            }
            return $sa - $sb;
        });

        // Filter active only
        if ($activeOnly) {
            $teams = array_filter($teams, function($t) {
                return ($t['status'] ?? 'Active') === 'Active';
            });
        }

        // Filter by season
        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $teams = array_filter($teams, function($t) use ($filterSeason) {
                return strtolower(trim($t['season'] ?? '')) === strtolower(trim($filterSeason));
            });
        }

        return array_values($teams);
    }

    // Get single team by ID
    public function getById($teamId) {
        $teams = $this->getAll('', false);
        foreach ($teams as $t) {
            if (($t['id'] ?? '') === $teamId) {
                return $t;
            }
        }
        return null;
    }

    // Get unique seasons
    public function getSeasons() {
        $teams = $this->getAll('', false);
        $seasons = [];
        foreach ($teams as $t) {
            $s = trim($t['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
        }
        rsort($seasons);
        return $seasons;
    }

    // Save (Create / Update) Team
    public function save($data, $files = []) {
        $teamId = trim($data['id'] ?? '');
        $teamName = trim($data['name'] ?? '');
        $season = trim($data['season'] ?? 'Season 1');
        $city = trim($data['city'] ?? '');
        $accentColor = trim($data['accentColor'] ?? '#ea580c');
        $gradient = trim($data['gradient'] ?? '');
        $sortOrder = isset($data['sortOrder']) ? (int)$data['sortOrder'] : 1;
        $status = in_array(($data['status'] ?? ''), ['Active', 'Inactive']) ? $data['status'] : 'Active';

        if (empty($teamName)) {
            return ['success' => false, 'message' => 'Team Name is required.'];
        }

        // Generate slug/id if empty
        if (empty($teamId)) {
            $teamId = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $teamName), '-'));
        }

        // Default gradient if not provided
        if (empty($gradient)) {
            $gradient = "linear-gradient(135deg, #18181b 0%, #1e3a8a 50%, {$accentColor} 100%)";
        }

        $allTeams = $this->getAll('', false);
        $existing = null;
        $existingIndex = -1;

        foreach ($allTeams as $idx => $t) {
            if (($t['id'] ?? '') === $teamId) {
                $existing = $t;
                $existingIndex = $idx;
                break;
            }
        }

        // 1. Handle Footer Circular Logo Upload (Specific for Website Footer and Crests)
        $footerLogoUrl = $existing['footerLogo'] ?? ($existing['logo'] ?? 'assets/images/teams/bhadohi-logo.jpeg');
        if (!empty($files['footerLogoFile']['name']) && $files['footerLogoFile']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files['footerLogoFile'], 'footer_logo_' . $teamId);
            if ($uploaded) $footerLogoUrl = $uploaded;
        } elseif (!empty($files['logoFile']['name']) && $files['logoFile']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files['logoFile'], 'footer_logo_' . $teamId);
            if ($uploaded) $footerLogoUrl = $uploaded;
        } elseif (!empty($data['footerLogoUrl'])) {
            $footerLogoUrl = trim($data['footerLogoUrl']);
        } elseif (!empty($data['logoUrl'])) {
            $footerLogoUrl = trim($data['logoUrl']);
        }

        // 2. Handle Team Poster / Banner Upload (Used in Homepage Meets Out Teams & Teams page)
        $posterUrl = $existing['posterImage'] ?? '';
        if (!empty($files['posterFile']['name']) && $files['posterFile']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files['posterFile'], 'poster_' . $teamId);
            if ($uploaded) $posterUrl = $uploaded;
        } elseif (!empty($data['posterUrl'])) {
            $posterUrl = trim($data['posterUrl']);
        } elseif (!empty($data['posterImage'])) {
            $posterUrl = trim($data['posterImage']);
        }
        if (empty($posterUrl)) {
            $posterUrl = $footerLogoUrl;
        }

        // 3. Handle Coach Data
        $coachName = trim($data['coachName'] ?? ($existing['coach']['name'] ?? ''));
        $coachRole = trim($data['coachRole'] ?? ($existing['coach']['role'] ?? 'Head Coach'));
        $coachPhoto = $existing['coach']['photoUrl'] ?? '';
        if (!empty($files['coachPhotoFile']['name']) && $files['coachPhotoFile']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files['coachPhotoFile'], 'coach_' . $teamId);
            if ($uploaded) $coachPhoto = $uploaded;
        }
        $coachInitials = $this->getInitials($coachName ?: 'Coach');
        $coach = [
            'name'       => $coachName,
            'role'       => $coachRole ?: 'Head Coach',
            'avatarText' => $coachInitials,
            'avatarBg'   => '#0284c7',
            'photoUrl'   => $coachPhoto
        ];

        // 4. Handle Owner Data
        $ownerName = trim($data['ownerName'] ?? ($existing['owner']['name'] ?? ''));
        $ownerRole = trim($data['ownerRole'] ?? ($existing['owner']['role'] ?? 'Franchise Owner'));
        $ownerPhoto = $existing['owner']['photoUrl'] ?? '';
        if (!empty($files['ownerPhotoFile']['name']) && $files['ownerPhotoFile']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files['ownerPhotoFile'], 'owner_' . $teamId);
            if ($uploaded) $ownerPhoto = $uploaded;
        }
        $owner = [
            'name'       => $ownerName,
            'role'       => $ownerRole ?: 'Franchise Owner',
            'avatarText' => $this->getInitials($ownerName ?: 'Owner'),
            'avatarBg'   => '#b45309',
            'photoUrl'   => $ownerPhoto
        ];

        // 5. Handle Captain Data
        $captainName = trim($data['captainName'] ?? ($existing['captain']['name'] ?? ''));
        $captainRole = trim($data['captainRole'] ?? ($existing['captain']['role'] ?? 'Team Captain'));
        $captainPhoto = $existing['captain']['photoUrl'] ?? '';
        if (!empty($files['captainPhotoFile']['name']) && $files['captainPhotoFile']['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files['captainPhotoFile'], 'captain_' . $teamId);
            if ($uploaded) $captainPhoto = $uploaded;
        }
        $captain = [
            'name'       => $captainName,
            'role'       => $captainRole ?: 'Team Captain',
            'avatarText' => $this->getInitials($captainName ?: 'Captain'),
            'avatarBg'   => '#16a34a',
            'photoUrl'   => $captainPhoto
        ];

        // 6. Handle Players Data
        $players = [];
        if (!empty($data['playersJson'])) {
            $decoded = is_array($data['playersJson']) ? $data['playersJson'] : json_decode($data['playersJson'], true);
            if (is_array($decoded)) {
                $players = $decoded;
            }
        } elseif (isset($data['playerNames']) && is_array($data['playerNames'])) {
            foreach ($data['playerNames'] as $k => $pName) {
                $pName = trim($pName);
                if (!empty($pName)) {
                    $players[] = [
                        'name' => $pName,
                        'pos'  => trim($data['playerPositions'][$k] ?? 'Player'),
                        'no'   => trim($data['playerNumbers'][$k] ?? '00')
                    ];
                }
            }
        } elseif ($existing !== null && !empty($existing['players'])) {
            $players = $existing['players'];
        }

        // Construct Record
        $teamRecord = [
            'id'          => $teamId,
            'name'        => $teamName,
            'season'      => $season,
            'city'        => $city,
            'footerLogo'  => $footerLogoUrl,
            'logo'        => $footerLogoUrl,
            'posterImage' => $posterUrl,
            'gradient'    => $gradient,
            'accentColor' => $accentColor,
            'owner'       => $owner,
            'coach'       => $coach,
            'captain'     => $captain,
            'players'     => $players,
            'sortOrder'   => $sortOrder,
            'status'      => $status
        ];

        if ($existingIndex >= 0) {
            $allTeams[$existingIndex] = $teamRecord;
        } else {
            $allTeams[] = $teamRecord;
        }

        // Save to JSON
        $this->saveToJson($allTeams);

        // Save to DB
        $this->saveToDb($teamRecord);

        return ['success' => true, 'message' => 'Franchise team saved successfully.', 'team' => $teamRecord];
    }

    // Delete Team
    public function delete($teamId) {
        $allTeams = $this->getAll('', false);
        $filtered = [];
        $found = false;

        foreach ($allTeams as $t) {
            if (($t['id'] ?? '') === $teamId) {
                $found = true;
            } else {
                $filtered[] = $t;
            }
        }

        if (!$found) {
            return ['success' => false, 'message' => 'Team not found.'];
        }

        $this->saveToJson($filtered);

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("DELETE FROM teams WHERE team_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $teamId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => 'Team deleted successfully.'];
    }

    // Toggle Status
    public function toggleStatus($teamId) {
        $allTeams = $this->getAll('', false);
        $found = false;
        $newStatus = 'Active';

        foreach ($allTeams as &$t) {
            if (($t['id'] ?? '') === $teamId) {
                $t['status'] = (($t['status'] ?? 'Active') === 'Active') ? 'Inactive' : 'Active';
                $newStatus = $t['status'];
                $found = true;
                break;
            }
        }
        unset($t);

        if (!$found) {
            return ['success' => false, 'message' => 'Team not found.'];
        }

        $this->saveToJson($allTeams);

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("UPDATE teams SET status = ? WHERE team_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $newStatus, $teamId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => "Team status changed to {$newStatus}.", 'status' => $newStatus];
    }

    // Helper: upload handler
    private function handleFileUpload($file, $prefix) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/gif'];
        if (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
            return false;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $ext;
        $dest = $this->uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/teams/' . $filename;
        }
        return false;
    }

    // Helper: initials
    private function getInitials($name) {
        $parts = explode(' ', trim($name));
        $initials = '';
        foreach ($parts as $p) {
            if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
        }
        return substr($initials, 0, 2) ?: 'UP';
    }

    // Helper: save to JSON
    private function saveToJson($data) {
        @file_put_contents($this->jsonFile, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    // Helper: save single record to DB
    private function saveToDb($t) {
        if ($this->conn === null) return;
        $this->ensureTable();

        $teamId      = $t['id'];
        $teamName    = $t['name'];
        $season      = $t['season'] ?? 'Season 1';
        $city        = $t['city'] ?? '';
        $logo        = $t['logo'] ?? '';
        $poster      = $t['posterImage'] ?? '';
        $gradient    = $t['gradient'] ?? '';
        $accentColor = $t['accentColor'] ?? '#ea580c';
        $ownerData   = json_encode($t['owner'] ?? []);
        $coachData   = json_encode($t['coach'] ?? []);
        $captainData = json_encode($t['captain'] ?? []);
        $playersData = json_encode($t['players'] ?? []);
        $sortOrder   = (int)($t['sortOrder'] ?? 0);
        $status      = $t['status'] ?? 'Active';

        $stmt = $this->conn->prepare("INSERT INTO teams (team_id, team_name, season, city, logo_url, poster_url, gradient, accent_color, owner_data, coach_data, captain_data, players_data, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            team_name = VALUES(team_name), season = VALUES(season), city = VALUES(city), logo_url = VALUES(logo_url),
            poster_url = VALUES(poster_url), gradient = VALUES(gradient), accent_color = VALUES(accent_color),
            owner_data = VALUES(owner_data), coach_data = VALUES(coach_data), captain_data = VALUES(captain_data),
            players_data = VALUES(players_data), sort_order = VALUES(sort_order), status = VALUES(status)");

        if ($stmt) {
            $stmt->bind_param("ssssssssssssis", 
                $teamId, $teamName, $season, $city, $logo, $poster, $gradient, $accentColor,
                $ownerData, $coachData, $captainData, $playersData, $sortOrder, $status
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}
