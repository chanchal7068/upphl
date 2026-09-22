<?php
// api/controllers/LivePartnerController.php
// Controller handling CRUD for Official Streaming & TV Broadcast Partners

class LivePartnerController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn = null) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/live_partners.json';
        $this->uploadDir = __DIR__ . '/../../uploads/partners/';

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }

        $this->ensureDefaults();
    }

    // Ensure default partners exist in JSON/DB if file does not exist
    private function ensureDefaults() {
        if (!file_exists($this->jsonFile)) {
            $defaultPartners = [
                [
                    'id'           => 'lp_youtube',
                    'name'         => 'YouTube Live',
                    'platformType' => 'SportsCast India',
                    'badgeText'    => 'Live Stream',
                    'description'  => 'Official high-definition live streaming of matches, analysis & highlights.',
                    'metaPill'     => 'Free HD Live',
                    'watchUrl'     => 'https://www.youtube.com/@sportscastindia',
                    'buttonText'   => 'Watch Live',
                    'iconClass'    => 'fa-brands fa-youtube',
                    'logoUrl'      => '',
                    'cardStyle'    => 'youtube-card',
                    'sortOrder'    => 1,
                    'status'       => 'Active',
                    'createdAt'    => date('Y-m-d H:i:s')
                ],
                [
                    'id'           => 'lp_ddsports',
                    'name'         => 'DD Sports',
                    'platformType' => 'National TV Network',
                    'badgeText'    => 'TV Partner',
                    'description'  => 'Nationwide broadcast on Doordarshan across cable, DTH & DD Free Dish.',
                    'metaPill'     => 'Free-To-Air TV',
                    'watchUrl'     => 'https://prasarbharati.gov.in/dd-sports/',
                    'buttonText'   => 'TV Guide',
                    'iconClass'    => 'fa-solid fa-tv',
                    'logoUrl'      => '',
                    'cardStyle'    => 'ddsports-card',
                    'sortOrder'    => 2,
                    'status'       => 'Active',
                    'createdAt'    => date('Y-m-d H:i:s')
                ],
                [
                    'id'           => 'lp_fancode',
                    'name'         => 'FanCode',
                    'platformType' => 'Official OTT Partner',
                    'badgeText'    => 'OTT Partner',
                    'description'  => 'Live stream on FanCode app & web with live scorecards & stats.',
                    'metaPill'     => 'Mobile & App',
                    'watchUrl'     => 'https://www.fancode.com',
                    'buttonText'   => 'Watch Now',
                    'iconClass'    => 'fa-solid fa-mobile-screen-button',
                    'logoUrl'      => '',
                    'cardStyle'    => 'fancode-card',
                    'sortOrder'    => 3,
                    'status'       => 'Active',
                    'createdAt'    => date('Y-m-d H:i:s')
                ]
            ];
            file_put_contents($this->jsonFile, json_encode($defaultPartners, JSON_PRETTY_PRINT));
        }

        $this->ensureTable();
    }

    private function ensureTable() {
        if ($this->conn === null) return;
        $sql = "CREATE TABLE IF NOT EXISTS live_broadcast_partners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            partner_id VARCHAR(100) UNIQUE NOT NULL,
            name VARCHAR(255) NOT NULL,
            platform_type VARCHAR(255) NULL,
            badge_text VARCHAR(100) NULL,
            description TEXT NULL,
            meta_pill VARCHAR(100) NULL,
            watch_url VARCHAR(500) NULL,
            button_text VARCHAR(100) NULL,
            icon_class VARCHAR(100) NULL,
            logo_url VARCHAR(500) NULL,
            card_style VARCHAR(100) NULL,
            sort_order INT DEFAULT 0,
            status VARCHAR(50) DEFAULT 'Active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->conn->query($sql);
    }

    // GET /api/live-partners.php
    public function getAll($activeOnly = false, $returnArray = false) {
        $partners = [];

        if (file_exists($this->jsonFile)) {
            $data = json_decode(file_get_contents($this->jsonFile), true);
            if (is_array($data)) {
                $partners = $data;
            }
        }

        // Sort by sortOrder ASC
        usort($partners, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['id'] ?? '', $b['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $partners = array_filter($partners, function($p) {
                return ($p['status'] ?? 'Active') === 'Active';
            });
            $partners = array_values($partners);
        }

        $result = [
            'success'  => true,
            'count'    => count($partners),
            'partners' => $partners
        ];

        if ($returnArray) {
            return $result;
        }

        echo json_encode($result);
        exit;
    }

    // POST /api/live-partners.php?action=save
    public function save() {
        $partnerId    = trim($_POST['partnerId'] ?? '');
        $name         = trim($_POST['name'] ?? '');
        $platformType = trim($_POST['platformType'] ?? '');
        $badgeText    = trim($_POST['badgeText'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $metaPill     = trim($_POST['metaPill'] ?? '');
        $watchUrl     = trim($_POST['watchUrl'] ?? '');
        $buttonText   = trim($_POST['buttonText'] ?? 'Watch Now');
        $iconClass    = trim($_POST['iconClass'] ?? '');
        $cardStyle    = trim($_POST['cardStyle'] ?? '');
        $sortOrder    = isset($_POST['sortOrder']) && is_numeric($_POST['sortOrder']) ? (int)$_POST['sortOrder'] : 0;
        $status       = trim($_POST['status'] ?? 'Active');
        $existingLogo = trim($_POST['existingLogoUrl'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Partner name is required.']);
            exit;
        }

        $logoUrl = $existingLogo;

        // Handle logo file upload
        if (isset($_FILES['logoFile']) && !empty($_FILES['logoFile']['name']) && $_FILES['logoFile']['error'] === UPLOAD_ERR_OK) {
            $tmpName  = $_FILES['logoFile']['tmp_name'];
            $origName = $_FILES['logoFile']['name'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'avif', 'gif'];

            if (in_array($ext, $allowed)) {
                $newFileName = 'lp_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $destPath    = $this->uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $destPath)) {
                    $logoUrl = 'uploads/partners/' . $newFileName;
                }
            }
        }

        $partners = [];
        if (file_exists($this->jsonFile)) {
            $partners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = false;
        $finalId = !empty($partnerId) ? $partnerId : ('lp_' . time() . '_' . rand(100, 999));

        $record = [
            'id'           => $finalId,
            'name'         => $name,
            'platformType' => $platformType,
            'badgeText'    => $badgeText,
            'description'  => $description,
            'metaPill'     => $metaPill,
            'watchUrl'     => $watchUrl,
            'buttonText'   => $buttonText,
            'iconClass'    => $iconClass,
            'logoUrl'      => $logoUrl,
            'cardStyle'    => $cardStyle,
            'sortOrder'    => $sortOrder,
            'status'       => $status,
            'createdAt'    => date('Y-m-d H:i:s')
        ];

        if (!empty($partnerId)) {
            foreach ($partners as &$p) {
                if ($p['id'] === $partnerId) {
                    $record['createdAt'] = $p['createdAt'] ?? date('Y-m-d H:i:s');
                    $p = $record;
                    $isEdit = true;
                    break;
                }
            }
            unset($p);
        }

        if (!$isEdit) {
            $partners[] = $record;
        }

        // Save to JSON
        file_put_contents($this->jsonFile, json_encode(array_values($partners), JSON_PRETTY_PRINT));

        // Save to DB
        $this->saveToDb($record);

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'Broadcast partner updated successfully!' : 'Broadcast partner added successfully!',
            'partner' => $record
        ]);
        exit;
    }

    private function saveToDb($record) {
        if ($this->conn === null) return;
        $this->ensureTable();

        $stmt = $this->conn->prepare("INSERT INTO live_broadcast_partners (partner_id, name, platform_type, badge_text, description, meta_pill, watch_url, button_text, icon_class, logo_url, card_style, sort_order, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name), platform_type = VALUES(platform_type), badge_text = VALUES(badge_text),
            description = VALUES(description), meta_pill = VALUES(meta_pill), watch_url = VALUES(watch_url),
            button_text = VALUES(button_text), icon_class = VALUES(icon_class), logo_url = VALUES(logo_url),
            card_style = VALUES(card_style), sort_order = VALUES(sort_order), status = VALUES(status)");

        if ($stmt) {
            $stmt->bind_param("ssssssssssssss",
                $record['id'],
                $record['name'],
                $record['platformType'],
                $record['badgeText'],
                $record['description'],
                $record['metaPill'],
                $record['watchUrl'],
                $record['buttonText'],
                $record['iconClass'],
                $record['logoUrl'],
                $record['cardStyle'],
                $record['sortOrder'],
                $record['status'],
                $record['createdAt']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    // POST /api/live-partners.php?action=delete
    public function delete() {
        $partnerId = trim($_POST['partnerId'] ?? '');

        if (empty($partnerId)) {
            echo json_encode(['success' => false, 'message' => 'Partner ID is required.']);
            exit;
        }

        $partners = [];
        if (file_exists($this->jsonFile)) {
            $partners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $filtered = array_filter($partners, function($p) use ($partnerId) {
            return $p['id'] !== $partnerId;
        });

        file_put_contents($this->jsonFile, json_encode(array_values($filtered), JSON_PRETTY_PRINT));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM live_broadcast_partners WHERE partner_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $partnerId);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Broadcast partner deleted successfully!']);
        exit;
    }

    // POST /api/live-partners.php?action=toggle-status
    public function toggleStatus() {
        $partnerId = trim($_POST['partnerId'] ?? '');
        $status    = trim($_POST['status'] ?? 'Active');

        if (empty($partnerId)) {
            echo json_encode(['success' => false, 'message' => 'Partner ID is required.']);
            exit;
        }

        $partners = [];
        if (file_exists($this->jsonFile)) {
            $partners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        foreach ($partners as &$p) {
            if ($p['id'] === $partnerId) {
                $p['status'] = $status;
                break;
            }
        }
        unset($p);

        file_put_contents($this->jsonFile, json_encode(array_values($partners), JSON_PRETTY_PRINT));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("UPDATE live_broadcast_partners SET status = ? WHERE partner_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $status, $partnerId);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Partner status updated to ' . $status]);
        exit;
    }
}
