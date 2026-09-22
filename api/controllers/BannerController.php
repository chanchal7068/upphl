<?php
// api/controllers/BannerController.php
// Controller handling all Banner operations: list, save/upload, delete, status toggle

class BannerController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/banners.json';
        $this->uploadDir = __DIR__ . '/../../uploads/banners/';

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // GET /api/banners.php or ?scope=all
    public function getAll($scope = 'active') {
        $banners = [];

        // 1. Fetch from JSON
        if (file_exists($this->jsonFile)) {
            $banners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. Fallback to DB
        if (empty($banners) && $this->conn !== null) {
            $res = $this->conn->query("SELECT * FROM banners ORDER BY sort_order ASC, id DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $banners[] = [
                        'bannerId'   => $row['banner_id'],
                        'badgeText'  => $row['badge_text'] ?? '',
                        'heading'    => $row['heading'],
                        'subtitle'   => $row['subtitle'] ?? '',
                        'btn1Text'   => $row['btn1_text'] ?? '',
                        'btn1Link'   => $row['btn1_link'] ?? '',
                        'btn2Text'   => $row['btn2_text'] ?? '',
                        'btn2Link'   => $row['btn2_link'] ?? '',
                        'mediaType'  => $row['media_type'] ?? 'image',
                        'mediaUrl'   => $row['media_url'],
                        'status'     => $row['status'] ?? 'Active',
                        'sortOrder'  => isset($row['sort_order']) ? (int)$row['sort_order'] : 0,
                        'createdAt'  => $row['created_at'] ?? ''
                    ];
                }
            }
        }

        // Sort by sortOrder ASC, then createdAt DESC
        usort($banners, function($a, $b) {
            $orderA = isset($a['sortOrder']) && is_numeric($a['sortOrder']) ? (int)$a['sortOrder'] : 0;
            $orderB = isset($b['sortOrder']) && is_numeric($b['sortOrder']) ? (int)$b['sortOrder'] : 0;
            if ($orderA === $orderB) {
                return strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? '');
            }
            return $orderA <=> $orderB;
        });

        if ($scope !== 'all') {
            $banners = array_values(array_filter($banners, fn($b) => ($b['status'] ?? 'Active') === 'Active'));
        }

        echo json_encode([
            'success' => true,
            'count'   => count($banners),
            'banners' => $banners
        ]);
        exit;
    }

    // POST /api/banners.php?action=save
    public function save() {
        $bannerId    = trim($_POST['bannerId'] ?? '');
        $heading     = trim($_POST['heading'] ?? '');
        $subtitle    = trim($_POST['subtitle'] ?? '');
        $badgeText   = trim($_POST['badgeText'] ?? '');
        $btn1Text    = trim($_POST['btn1Text'] ?? '');
        $btn1Link    = trim($_POST['btn1Link'] ?? '');
        $btn2Text    = trim($_POST['btn2Text'] ?? '');
        $btn2Link    = trim($_POST['btn2Link'] ?? '');
        $status      = trim($_POST['status'] ?? 'Active');
        $sortOrder   = isset($_POST['sortOrder']) && is_numeric($_POST['sortOrder']) ? (int)$_POST['sortOrder'] : 0;
        $existingUrl = trim($_POST['existingMediaUrl'] ?? '');
        $existingType= trim($_POST['existingMediaType'] ?? 'image');

        if (empty($heading)) {
            echo json_encode(['success' => false, 'message' => 'Heading text is required.']);
            exit;
        }

        $mediaUrl = $existingUrl;
        $mediaType = $existingType;

        // Handle File Upload
        if (isset($_FILES['mediaFile']) && $_FILES['mediaFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmp  = $_FILES['mediaFile']['tmp_name'];
            $fileName = $_FILES['mediaFile']['name'];
            $fileSize = $_FILES['mediaFile']['size'];
            $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $videoExts = ['mp4', 'webm', 'ogg', 'mov', 'mkv', 'avi', 'wmv', 'm4v', 'flv'];
            $mediaType = in_array($ext, $videoExts) ? 'video' : 'image';

            $newFileName = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $this->uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destPath)) {
                $mediaUrl = 'uploads/banners/' . $newFileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload media file.']);
                exit;
            }
        }

        if (empty($mediaUrl)) {
            echo json_encode(['success' => false, 'message' => 'Please upload an image or video for this banner.']);
            exit;
        }

        // Load JSON
        $banners = [];
        if (file_exists($this->jsonFile)) {
            $banners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = !empty($bannerId);
        if (!$isEdit) {
            $bannerId = 'ban_' . time() . '_' . rand(100, 999);
        }

        $bannerData = [
            'bannerId'   => $bannerId,
            'badgeText'  => $badgeText,
            'heading'    => $heading,
            'subtitle'   => $subtitle,
            'btn1Text'   => $btn1Text,
            'btn1Link'   => $btn1Link,
            'btn2Text'   => $btn2Text,
            'btn2Link'   => $btn2Link,
            'mediaType'  => $mediaType,
            'mediaUrl'   => $mediaUrl,
            'status'     => $status,
            'sortOrder'  => $sortOrder,
            'createdAt'  => date('Y-m-d H:i:s')
        ];

        if ($isEdit) {
            $updated = false;
            foreach ($banners as &$b) {
                if ($b['bannerId'] === $bannerId) {
                    $bannerData['createdAt'] = $b['createdAt'] ?? date('Y-m-d H:i:s');
                    $b = $bannerData;
                    $updated = true;
                    break;
                }
            }
            if (!$updated) {
                $banners[] = $bannerData;
            }
        } else {
            $banners[] = $bannerData;
        }

        // Sort all banners by sortOrder ASC, then createdAt DESC
        usort($banners, function($a, $b) {
            $orderA = isset($a['sortOrder']) && is_numeric($a['sortOrder']) ? (int)$a['sortOrder'] : 0;
            $orderB = isset($b['sortOrder']) && is_numeric($b['sortOrder']) ? (int)$b['sortOrder'] : 0;
            if ($orderA === $orderB) {
                return strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? '');
            }
            return $orderA <=> $orderB;
        });

        file_put_contents($this->jsonFile, json_encode($banners, JSON_PRETTY_PRINT));

        // Sync to DB if connected
        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("INSERT INTO banners (banner_id, badge_text, heading, subtitle, btn1_text, btn1_link, btn2_text, btn2_link, media_type, media_url, status, sort_order, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                badge_text=VALUES(badge_text), heading=VALUES(heading), subtitle=VALUES(subtitle), btn1_text=VALUES(btn1_text), btn1_link=VALUES(btn1_link), btn2_text=VALUES(btn2_text), btn2_link=VALUES(btn2_link), media_type=VALUES(media_type), media_url=VALUES(media_url), status=VALUES(status), sort_order=VALUES(sort_order)");
            if ($stmt) {
                $stmt->bind_param('sssssssssssi', $bannerId, $badgeText, $heading, $subtitle, $btn1Text, $btn1Link, $btn2Text, $btn2Link, $mediaType, $mediaUrl, $status, $sortOrder);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'Hero banner updated successfully!' : 'New hero banner added successfully!',
            'banner'  => $bannerData
        ]);
        exit;
    }

    // POST /api/banners.php?action=delete
    public function delete() {
        $bannerId = trim($_POST['bannerId'] ?? '');
        if (empty($bannerId)) {
            echo json_encode(['success' => false, 'message' => 'Banner ID required.']);
            exit;
        }

        $banners = [];
        if (file_exists($this->jsonFile)) {
            $banners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $deletedUrl = null;
        $newBanners = [];
        foreach ($banners as $b) {
            if ($b['bannerId'] === $bannerId) {
                $deletedUrl = $b['mediaUrl'] ?? null;
            } else {
                $newBanners[] = $b;
            }
        }

        file_put_contents($this->jsonFile, json_encode($newBanners, JSON_PRETTY_PRINT));

        if ($deletedUrl && strpos($deletedUrl, 'uploads/banners/') !== false) {
            $fullPath = __DIR__ . '/../../' . $deletedUrl;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM banners WHERE banner_id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $bannerId);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Banner deleted successfully.']);
        exit;
    }

    // POST /api/banners.php?action=toggle
    public function toggleStatus() {
        $bannerId = trim($_POST['bannerId'] ?? '');
        $status   = trim($_POST['status'] ?? 'Active');

        if (empty($bannerId)) {
            echo json_encode(['success' => false, 'message' => 'Banner ID required.']);
            exit;
        }

        $banners = [];
        if (file_exists($this->jsonFile)) {
            $banners = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        foreach ($banners as &$b) {
            if ($b['bannerId'] === $bannerId) {
                $b['status'] = $status;
                break;
            }
        }
        file_put_contents($this->jsonFile, json_encode($banners, JSON_PRETTY_PRINT));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("UPDATE banners SET status = ? WHERE banner_id = ?");
            if ($stmt) {
                $stmt->bind_param('ss', $status, $bannerId);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Status updated to ' . $status]);
        exit;
    }

    private function ensureTable() {
        $this->conn->query("CREATE TABLE IF NOT EXISTS banners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            banner_id VARCHAR(50) UNIQUE NOT NULL,
            badge_text VARCHAR(150),
            heading VARCHAR(255) NOT NULL,
            subtitle TEXT,
            btn1_text VARCHAR(100),
            btn1_link VARCHAR(255),
            btn2_text VARCHAR(100),
            btn2_link VARCHAR(255),
            media_type VARCHAR(20) DEFAULT 'image',
            media_url TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'Active',
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Ensure sort_order column exists if table already existed
        $colCheck = $this->conn->query("SHOW COLUMNS FROM banners LIKE 'sort_order'");
        if ($colCheck && $colCheck->num_rows === 0) {
            $this->conn->query("ALTER TABLE banners ADD COLUMN sort_order INT DEFAULT 0 AFTER status");
        }
    }
}
