<?php
// api/controllers/AnnouncementController.php
// Controller handling Marquee Announcement Strip management

class AnnouncementController {
    private $conn;
    private $jsonPath;

    public function __construct($dbConnection = null) {
        $this->conn = $dbConnection;
        $this->jsonPath = __DIR__ . '/../../uploads/announcements.json';

        $uploadDir = __DIR__ . '/../../uploads/';
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        // Initialize table if DB exists
        if ($this->conn !== null) {
            $this->initTable();
        }
    }

    private function initTable() {
        $sql = "CREATE TABLE IF NOT EXISTS announcements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            announcement_id VARCHAR(100) UNIQUE NOT NULL,
            badge_text VARCHAR(100) NULL,
            text TEXT NOT NULL,
            link_url VARCHAR(500) NULL,
            link_text VARCHAR(100) NULL,
            open_in_new_tab TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 1,
            status ENUM('Active', 'Inactive') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        @$this->conn->query($sql);
    }

    public function getAll($activeOnly = false) {
        $announcements = [];

        if (file_exists($this->jsonPath)) {
            $announcements = json_decode(file_get_contents($this->jsonPath), true) ?? [];
        }

        if (empty($announcements) && $this->conn !== null) {
            $res = $this->conn->query("SELECT * FROM announcements ORDER BY sort_order ASC, id DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $announcements[] = [
                        'id'           => $row['announcement_id'],
                        'badgeText'    => $row['badge_text'] ?? 'ANNOUNCEMENT',
                        'text'         => $row['text'],
                        'linkUrl'      => $row['link_url'] ?? '',
                        'linkText'     => $row['link_text'] ?? '',
                        'openInNewTab' => !empty($row['open_in_new_tab']) ? true : false,
                        'sortOrder'    => isset($row['sort_order']) ? (int)$row['sort_order'] : 1,
                        'status'       => $row['status'] ?? 'Active',
                        'createdAt'    => $row['created_at'] ?? date('Y-m-d H:i:s')
                    ];
                }
            }
        }

        // Sort by sortOrder ASC
        usort($announcements, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $announcements = array_filter($announcements, function($item) {
                return ($item['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($announcements);
    }

    public function getById($id) {
        $all = $this->getAll(false);
        foreach ($all as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    public function save($data) {
        $id = trim($data['id'] ?? ($data['announcementId'] ?? ''));
        $badgeText = trim($data['badgeText'] ?? 'ANNOUNCEMENT');
        $text = trim($data['text'] ?? '');
        $linkUrl = trim($data['linkUrl'] ?? '');
        $linkText = trim($data['linkText'] ?? '');
        $openInNewTab = !empty($data['openInNewTab']) && ($data['openInNewTab'] === '1' || $data['openInNewTab'] === true || $data['openInNewTab'] === 'true') ? true : false;
        $sortOrder = isset($data['sortOrder']) && is_numeric($data['sortOrder']) ? (int)$data['sortOrder'] : 1;
        $status = in_array(trim($data['status'] ?? 'Active'), ['Active', 'Inactive']) ? trim($data['status']) : 'Active';

        if (empty($text)) {
            return ['success' => false, 'message' => 'Announcement text is required.'];
        }

        $all = $this->getAll(false);
        $isEdit = false;
        $savedItem = null;

        if (!empty($id)) {
            // Update existing
            foreach ($all as &$item) {
                if ($item['id'] === $id) {
                    $isEdit = true;
                    $item['badgeText']    = $badgeText;
                    $item['text']         = $text;
                    $item['linkUrl']      = $linkUrl;
                    $item['linkText']     = $linkText;
                    $item['openInNewTab'] = $openInNewTab;
                    $item['sortOrder']    = $sortOrder;
                    $item['status']       = $status;
                    $item['updatedAt']    = date('Y-m-d H:i:s');
                    $savedItem = $item;
                    break;
                }
            }
            unset($item);
        }

        if (!$isEdit) {
            // Create new
            $id = 'ann_' . uniqid() . '_' . rand(100, 999);
            $savedItem = [
                'id'           => $id,
                'badgeText'    => $badgeText,
                'text'         => $text,
                'linkUrl'      => $linkUrl,
                'linkText'     => $linkText,
                'openInNewTab' => $openInNewTab,
                'sortOrder'    => $sortOrder,
                'status'       => $status,
                'createdAt'    => date('Y-m-d H:i:s')
            ];
            $all[] = $savedItem;
        }

        // Save to JSON
        file_put_contents($this->jsonPath, json_encode(array_values($all), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Sync to DB
        if ($this->conn !== null) {
            $newTabVal = $openInNewTab ? 1 : 0;
            $stmt = $this->conn->prepare("INSERT INTO announcements (announcement_id, badge_text, text, link_url, link_text, open_in_new_tab, sort_order, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE badge_text = VALUES(badge_text), text = VALUES(text), link_url = VALUES(link_url), link_text = VALUES(link_text), open_in_new_tab = VALUES(open_in_new_tab), sort_order = VALUES(sort_order), status = VALUES(status)");
            if ($stmt) {
                $stmt->bind_param("sssssiis", $id, $badgeText, $text, $linkUrl, $linkText, $newTabVal, $sortOrder, $status);
                @$stmt->execute();
            }
        }

        return [
            'success' => true,
            'message' => $isEdit ? 'Announcement updated successfully.' : 'Announcement added successfully.',
            'data'    => $savedItem
        ];
    }

    public function toggleStatus($id) {
        if (empty($id)) {
            return ['success' => false, 'message' => 'Invalid announcement ID.'];
        }

        $all = $this->getAll(false);
        $found = false;
        $newStatus = 'Active';

        foreach ($all as &$item) {
            if ($item['id'] === $id) {
                $found = true;
                $newStatus = ($item['status'] === 'Active') ? 'Inactive' : 'Active';
                $item['status'] = $newStatus;
                $item['updatedAt'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($item);

        if (!$found) {
            return ['success' => false, 'message' => 'Announcement not found.'];
        }

        file_put_contents($this->jsonPath, json_encode(array_values($all), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("UPDATE announcements SET status = ? WHERE announcement_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $newStatus, $id);
                @$stmt->execute();
            }
        }

        return [
            'success'   => true,
            'newStatus' => $newStatus,
            'message'   => "Announcement status changed to $newStatus."
        ];
    }

    public function delete($id) {
        if (empty($id)) {
            return ['success' => false, 'message' => 'Invalid announcement ID.'];
        }

        $all = $this->getAll(false);
        $filtered = [];
        $found = false;

        foreach ($all as $item) {
            if ($item['id'] === $id) {
                $found = true;
            } else {
                $filtered[] = $item;
            }
        }

        if (!$found) {
            return ['success' => false, 'message' => 'Announcement not found.'];
        }

        file_put_contents($this->jsonPath, json_encode(array_values($filtered), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM announcements WHERE announcement_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $id);
                @$stmt->execute();
            }
        }

        return ['success' => true, 'message' => 'Announcement deleted successfully.'];
    }
}
