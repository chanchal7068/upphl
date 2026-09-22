<?php
// api/controllers/PartnerController.php
// Controller handling Our Partners & Sponsors management

class PartnerController {
    private $conn;
    private $jsonPath;
    private $uploadDir;

    public function __construct($dbConnection = null) {
        $this->conn = $dbConnection;
        $this->jsonPath = __DIR__ . '/../../uploads/partners.json';
        $this->uploadDir = __DIR__ . '/../../uploads/partners/';

        if (!file_exists($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }

        // Initialize table if DB exists
        if ($this->conn !== null) {
            $this->initTable();
        }
    }

    private function initTable() {
        $sql = "CREATE TABLE IF NOT EXISTS partners_sponsors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            partner_id VARCHAR(100) UNIQUE NOT NULL,
            name VARCHAR(255) NOT NULL,
            partner_type VARCHAR(255) NULL,
            logo_url VARCHAR(500) NULL,
            icon_class VARCHAR(100) NULL,
            icon_color VARCHAR(50) NULL,
            link_url VARCHAR(500) NULL,
            sort_order INT DEFAULT 1,
            status ENUM('Active', 'Inactive') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        @$this->conn->query($sql);
    }

    public function getAll($activeOnly = false) {
        $partners = [];

        if (file_exists($this->jsonPath)) {
            $partners = json_decode(file_get_contents($this->jsonPath), true) ?? [];
        }

        if (empty($partners) && $this->conn !== null) {
            $res = $this->conn->query("SELECT * FROM partners_sponsors ORDER BY sort_order ASC, id DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $partners[] = [
                        'id'          => $row['partner_id'],
                        'name'        => $row['name'],
                        'partnerType' => $row['partner_type'] ?? '',
                        'logoUrl'     => $row['logo_url'] ?? '',
                        'iconClass'   => $row['icon_class'] ?? 'fa-solid fa-handshake',
                        'iconColor'   => $row['icon_color'] ?? '#3b82f6',
                        'linkUrl'     => $row['link_url'] ?? '',
                        'sortOrder'   => isset($row['sort_order']) ? (int)$row['sort_order'] : 1,
                        'status'      => $row['status'] ?? 'Active'
                    ];
                }
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

        // Filter active only
        if ($activeOnly) {
            $partners = array_filter($partners, function($p) {
                return ($p['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($partners);
    }

    public function getById($partnerId) {
        $all = $this->getAll(false);
        foreach ($all as $item) {
            if (($item['id'] ?? '') === $partnerId) {
                return $item;
            }
        }
        return null;
    }

    public function save($data, $files = []) {
        $partnerId = trim($data['id'] ?? ($data['partnerId'] ?? ''));
        $name = trim($data['name'] ?? ($data['partnerName'] ?? ''));
        $partnerType = trim($data['partnerType'] ?? ($data['type'] ?? 'Official Partner'));
        $iconClass = trim($data['iconClass'] ?? '');
        $iconColor = trim($data['iconColor'] ?? '');
        $linkUrl = trim($data['linkUrl'] ?? '');
        $sortOrder = isset($data['sortOrder']) ? (int)$data['sortOrder'] : 1;
        $status = in_array(($data['status'] ?? ''), ['Active', 'Inactive']) ? $data['status'] : 'Active';

        if (empty($name)) {
            return ['success' => false, 'message' => 'Partner/Sponsor name is required.'];
        }

        if (empty($partnerId)) {
            $partnerId = 'partner_' . time() . '_' . rand(100, 999);
        }

        if (empty($partnerType)) {
            $partnerType = 'Official Partner';
        }

        $allPartners = $this->getAll(false);
        $existing = null;
        $existingIndex = -1;

        foreach ($allPartners as $idx => $item) {
            if (($item['id'] ?? '') === $partnerId) {
                $existing = $item;
                $existingIndex = $idx;
                break;
            }
        }

        // Handle Logo / Image Upload
        $fileKey = !empty($files['partnerLogo']['name']) ? 'partnerLogo' : (!empty($files['imageFile']['name']) ? 'imageFile' : (!empty($files['logoFile']['name']) ? 'logoFile' : null));
        $logoUrl = $existing['logoUrl'] ?? (!empty($data['existingLogo']) ? trim($data['existingLogo']) : (!empty($data['logoUrl']) ? trim($data['logoUrl']) : ''));

        if ($fileKey && $files[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files[$fileKey], 'partner_' . $partnerId);
            if ($uploaded) {
                $logoUrl = $uploaded;
            }
        } elseif (!empty($data['logoUrl'])) {
            $logoUrl = trim($data['logoUrl']);
        }

        if (empty($iconClass) && $existing) {
            $iconClass = $existing['iconClass'] ?? 'fa-solid fa-handshake';
        }
        if (empty($iconColor) && $existing) {
            $iconColor = $existing['iconColor'] ?? '#3b82f6';
        }

        $record = [
            'id'          => $partnerId,
            'name'        => $name,
            'partnerType' => $partnerType,
            'logoUrl'     => $logoUrl,
            'iconClass'   => $iconClass ?: 'fa-solid fa-handshake',
            'iconColor'   => $iconColor ?: '#3b82f6',
            'linkUrl'     => $linkUrl,
            'sortOrder'   => $sortOrder,
            'status'      => $status
        ];

        if ($existingIndex >= 0) {
            $allPartners[$existingIndex] = $record;
        } else {
            $allPartners[] = $record;
        }

        $this->saveToJson($allPartners);
        $this->saveToDb($record);

        return ['success' => true, 'message' => 'Partner/Sponsor saved successfully.', 'id' => $partnerId, 'partner' => $record];
    }

    public function delete($partnerId) {
        $all = $this->getAll(false);
        $filtered = [];
        $found = false;

        foreach ($all as $item) {
            if (($item['id'] ?? '') === $partnerId) {
                $found = true;
                if (!empty($item['logoUrl']) && strpos($item['logoUrl'], 'uploads/partners/') !== false) {
                    $file = __DIR__ . '/../../' . $item['logoUrl'];
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
                continue;
            }
            $filtered[] = $item;
        }

        if (!$found) {
            return ['success' => false, 'message' => 'Partner not found.'];
        }

        $this->saveToJson($filtered);

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM partners_sponsors WHERE partner_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $partnerId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => 'Partner deleted successfully.'];
    }

    public function toggleStatus($partnerId) {
        $all = $this->getAll(false);
        $newStatus = 'Active';
        $found = false;

        foreach ($all as &$item) {
            if (($item['id'] ?? '') === $partnerId) {
                $newStatus = (($item['status'] ?? 'Active') === 'Active') ? 'Inactive' : 'Active';
                $item['status'] = $newStatus;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return ['success' => false, 'message' => 'Partner not found.'];
        }

        $this->saveToJson($all);

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("UPDATE partners_sponsors SET status = ? WHERE partner_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $newStatus, $partnerId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => "Partner status changed to {$newStatus}.", 'status' => $newStatus];
    }

    private function handleFileUpload($file, $prefix) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        if (!in_array($ext, $allowed)) {
            return null;
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $prefix);
        $filename = $safeName . '_' . time() . '.' . $ext;
        $targetPath = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/partners/' . $filename;
        }
        return null;
    }

    public function saveToJson($list) {
        @file_put_contents($this->jsonPath, json_encode(array_values($list), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function saveToDb($record) {
        if ($this->conn === null) return;

        $stmt = $this->conn->prepare("INSERT INTO partners_sponsors (partner_id, name, partner_type, logo_url, icon_class, icon_color, link_url, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            partner_type = VALUES(partner_type),
            logo_url = VALUES(logo_url),
            icon_class = VALUES(icon_class),
            icon_color = VALUES(icon_color),
            link_url = VALUES(link_url),
            sort_order = VALUES(sort_order),
            status = VALUES(status)");

        if ($stmt) {
            $stmt->bind_param(
                "sssssssis",
                $record['id'],
                $record['name'],
                $record['partnerType'],
                $record['logoUrl'],
                $record['iconClass'],
                $record['iconColor'],
                $record['linkUrl'],
                $record['sortOrder'],
                $record['status']
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}
