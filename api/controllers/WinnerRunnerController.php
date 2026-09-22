<?php
// api/controllers/WinnerRunnerController.php
// Controller handling Winner and Runner-Up slides: CRUD, status toggle, uploads

class WinnerRunnerController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/winners.json';
        $this->uploadDir = __DIR__ . '/../../uploads/winners/';

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // GET /api/winners.php?scope=active|all&category=...
    public function getAll($scope = 'active', $filterCategory = '', $filterSeason = '') {
        $items = [];

        // 1. Fetch from JSON
        if (file_exists($this->jsonFile)) {
            $items = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. Fallback to DB
        if (empty($items) && $this->conn !== null) {
            $this->ensureTable();
            $res = $this->conn->query("SELECT * FROM winners_runners ORDER BY display_order ASC, id DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $items[] = [
                        'id'           => $row['slide_id'],
                        'category'     => $row['category'],
                        'season'       => $row['season'],
                        'title'        => $row['title'],
                        'teamName'     => $row['team_name'] ?? '',
                        'description'  => $row['description'] ?? '',
                        'imageUrl'     => $row['image_url'],
                        'status'       => $row['status'] ?? 'Active',
                        'displayOrder' => (int)($row['display_order'] ?? 0),
                        'createdAt'    => $row['created_at']
                    ];
                }
            }
        }

        if ($scope !== 'all') {
            $items = array_values(array_filter($items, fn($i) => ($i['status'] ?? 'Active') === 'Active'));
        }

        if (!empty($filterCategory) && strtolower($filterCategory) !== 'all') {
            $items = array_values(array_filter($items, fn($i) => strtolower($i['category'] ?? '') === strtolower($filterCategory)));
        }

        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $items = array_values(array_filter($items, fn($i) => strtolower($i['season'] ?? '') === strtolower($filterSeason)));
        }

        // Sort by display order
        usort($items, function($a, $b) {
            $oa = $a['displayOrder'] ?? 0;
            $ob = $b['displayOrder'] ?? 0;
            if ($oa !== $ob) return $oa <=> $ob;
            return strcmp($b['id'] ?? '', $a['id'] ?? '');
        });

        // Distinct seasons
        $seasons = [];
        foreach ($items as $it) {
            $s = trim($it['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
        }

        echo json_encode([
            'success' => true,
            'count'   => count($items),
            'seasons' => $seasons,
            'items'   => $items
        ]);
        exit;
    }

    // POST /api/winners.php?action=save
    public function save() {
        $slideId      = trim($_POST['slideId'] ?? '');
        $category     = trim($_POST['category'] ?? 'Winner');
        $season       = trim($_POST['season'] ?? 'Season 1');
        $title        = trim($_POST['title'] ?? '');
        $teamName     = trim($_POST['teamName'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $displayOrder = (int)($_POST['displayOrder'] ?? 0);
        $status       = trim($_POST['status'] ?? 'Active');
        $existingImg  = trim($_POST['existingImageUrl'] ?? '');

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Title is required.']);
            exit;
        }

        if (empty($season)) $season = 'Season 1';
        if (empty($category)) $category = 'Winner';

        $imageUrl = $existingImg;

        // Handle File Upload
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmp  = $_FILES['imageFile']['tmp_name'];
            $fileName = $_FILES['imageFile']['name'];
            $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $validExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $validExts)) {
                $newFileName = 'winner_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $destPath = $this->uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $destPath)) {
                    $imageUrl = 'uploads/winners/' . $newFileName;
                }
            }
        }

        if (empty($imageUrl)) {
            echo json_encode(['success' => false, 'message' => 'Please upload or specify a banner image.']);
            exit;
        }

        $items = [];
        if (file_exists($this->jsonFile)) {
            $items = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = !empty($slideId);
        if (!$isEdit) {
            $slideId = 'win_' . time() . '_' . rand(100, 999);
        }

        $slideData = [
            'id'           => $slideId,
            'category'     => $category,
            'season'       => $season,
            'title'        => $title,
            'teamName'     => $teamName,
            'description'  => $description,
            'imageUrl'     => $imageUrl,
            'status'       => $status,
            'displayOrder' => $displayOrder,
            'createdAt'    => date('Y-m-d H:i:s')
        ];

        if ($isEdit) {
            $updated = false;
            foreach ($items as &$it) {
                if ($it['id'] === $slideId) {
                    $slideData['createdAt'] = $it['createdAt'] ?? date('Y-m-d H:i:s');
                    $it = $slideData;
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $items[] = $slideData;
            }
        } else {
            $items[] = $slideData;
        }

        file_put_contents($this->jsonFile, json_encode($items, JSON_PRETTY_PRINT));

        // Sync to MySQL
        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("INSERT INTO winners_runners (slide_id, category, season, title, team_name, description, image_url, status, display_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                category=VALUES(category), season=VALUES(season), title=VALUES(title), team_name=VALUES(team_name),
                description=VALUES(description), image_url=VALUES(image_url), status=VALUES(status), display_order=VALUES(display_order)");
            if ($stmt) {
                $stmt->bind_param('ssssssssi', $slideId, $category, $season, $title, $teamName, $description, $imageUrl, $status, $displayOrder);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'Winner/Runner slide updated successfully!' : 'New Winner/Runner slide added successfully!',
            'slide'   => $slideData
        ]);
        exit;
    }

    // POST /api/winners.php?action=delete
    public function delete() {
        $slideId = trim($_POST['slideId'] ?? '');
        if (empty($slideId)) {
            echo json_encode(['success' => false, 'message' => 'Slide ID is required.']);
            exit;
        }

        $items = [];
        if (file_exists($this->jsonFile)) {
            $items = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            $items = array_values(array_filter($items, fn($i) => ($i['id'] ?? '') !== $slideId));
            file_put_contents($this->jsonFile, json_encode($items, JSON_PRETTY_PRINT));
        }

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("DELETE FROM winners_runners WHERE slide_id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $slideId);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Slide removed successfully.']);
        exit;
    }

    private function ensureTable() {
        if (!$this->conn) return;

        $this->conn->query("CREATE TABLE IF NOT EXISTS winners_runners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slide_id VARCHAR(50) UNIQUE NOT NULL,
            category VARCHAR(50) DEFAULT 'Winner',
            season VARCHAR(50) DEFAULT 'Season 1',
            title VARCHAR(255) NOT NULL,
            team_name VARCHAR(150) DEFAULT '',
            description TEXT,
            image_url TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'Active',
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}
