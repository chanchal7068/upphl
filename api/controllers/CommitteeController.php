<?php
// api/controllers/CommitteeController.php
// Controller for League Management Committee Members with rich JSON store and MySQL sync fallback

class CommitteeController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($conn = null) {
        $this->conn = $conn;
        $this->jsonFile = __DIR__ . '/../../uploads/committee.json';
        $this->uploadDir = __DIR__ . '/../../uploads/committee/';

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }

        $this->initDb();
    }

    private function initDb() {
        if ($this->conn === null) return;
        $sql = "CREATE TABLE IF NOT EXISTS league_committee (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id VARCHAR(100) UNIQUE NOT NULL,
            name VARCHAR(255) NOT NULL,
            badge VARCHAR(100) DEFAULT '',
            designation VARCHAR(255) DEFAULT '',
            sub_designation VARCHAR(255) DEFAULT '',
            image_url VARCHAR(500) DEFAULT '',
            sort_order INT DEFAULT 1,
            status ENUM('Active', 'Inactive') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        @$this->conn->query($sql);
    }

    public function getAll($activeOnly = false) {
        $members = [];
        if (file_exists($this->jsonFile)) {
            $data = json_decode(file_get_contents($this->jsonFile), true);
            if (is_array($data)) {
                $members = $data;
            }
        }

        if (empty($members) && $this->conn !== null) {
            $res = $this->conn->query("SELECT * FROM league_committee ORDER BY sort_order ASC, id ASC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $members[] = [
                        'id'             => $row['member_id'],
                        'name'           => $row['name'],
                        'badge'          => $row['badge'] ?? '',
                        'designation'    => $row['designation'] ?? '',
                        'subDesignation' => $row['sub_designation'] ?? '',
                        'imageUrl'       => $row['image_url'] ?? '',
                        'sortOrder'      => isset($row['sort_order']) ? (int)$row['sort_order'] : 1,
                        'status'         => $row['status'] ?? 'Active'
                    ];
                }
            }
        }

        // Sort by sortOrder ASC, then id ASC
        usort($members, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['id'] ?? '', $b['id'] ?? '');
            }
            return $sa - $sb;
        });

        if ($activeOnly) {
            $members = array_filter($members, function($m) {
                return ($m['status'] ?? 'Active') === 'Active';
            });
        }

        return array_values($members);
    }

    public function getById($memberId) {
        $all = $this->getAll(false);
        foreach ($all as $item) {
            if (($item['id'] ?? '') === $memberId) {
                return $item;
            }
        }
        return null;
    }

    public function save($data, $files = []) {
        $memberId = trim($data['id'] ?? ($data['memberId'] ?? ''));
        $name = trim($data['name'] ?? '');
        $badge = trim($data['badge'] ?? '');
        $designation = trim($data['designation'] ?? '');
        $subDesignation = trim($data['subDesignation'] ?? ($data['sub_designation'] ?? 'UP Pro Handball League'));
        $sortOrder = isset($data['sortOrder']) ? (int)$data['sortOrder'] : 1;
        $status = in_array(($data['status'] ?? ''), ['Active', 'Inactive']) ? $data['status'] : 'Active';

        if (empty($name)) {
            return ['success' => false, 'message' => 'Committee member name is required.'];
        }

        if (empty($memberId)) {
            $memberId = 'cm_' . time() . '_' . rand(100, 999);
        }

        $allMembers = $this->getAll(false);
        $existing = null;
        $existingIndex = -1;

        foreach ($allMembers as $idx => $item) {
            if (($item['id'] ?? '') === $memberId) {
                $existing = $item;
                $existingIndex = $idx;
                break;
            }
        }

        // Handle Image Upload
        $fileKey = !empty($files['committeePhoto']['name']) ? 'committeePhoto' : (!empty($files['imageFile']['name']) ? 'imageFile' : (!empty($files['photoFile']['name']) ? 'photoFile' : null));
        $imageUrl = $existing['imageUrl'] ?? (!empty($data['existingImage']) ? trim($data['existingImage']) : (!empty($data['imageUrl']) ? trim($data['imageUrl']) : ''));

        if ($fileKey && $files[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files[$fileKey], 'cm_' . $memberId);
            if ($uploaded) {
                $imageUrl = $uploaded;
            }
        }

        $record = [
            'id'             => $memberId,
            'name'           => $name,
            'badge'          => $badge,
            'designation'    => $designation,
            'subDesignation' => $subDesignation,
            'imageUrl'       => $imageUrl,
            'sortOrder'      => $sortOrder,
            'status'         => $status
        ];

        if ($existingIndex >= 0) {
            $allMembers[$existingIndex] = $record;
        } else {
            $allMembers[] = $record;
        }

        $this->saveToJson($allMembers);
        $this->saveToDb($record);

        return ['success' => true, 'message' => 'Committee member saved successfully.', 'id' => $memberId, 'member' => $record];
    }

    public function delete($memberId) {
        $all = $this->getAll(false);
        $filtered = [];
        $found = false;

        foreach ($all as $item) {
            if (($item['id'] ?? '') === $memberId) {
                $found = true;
                if (!empty($item['imageUrl']) && strpos($item['imageUrl'], 'uploads/committee/') !== false) {
                    $file = __DIR__ . '/../../' . $item['imageUrl'];
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
                continue;
            }
            $filtered[] = $item;
        }

        if (!$found) {
            return ['success' => false, 'message' => 'Committee member not found.'];
        }

        $this->saveToJson($filtered);

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM league_committee WHERE member_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $memberId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => 'Committee member deleted successfully.'];
    }

    public function toggleStatus($memberId) {
        $all = $this->getAll(false);
        $found = false;
        $newStatus = 'Active';

        foreach ($all as &$item) {
            if (($item['id'] ?? '') === $memberId) {
                $item['status'] = (($item['status'] ?? 'Active') === 'Active') ? 'Inactive' : 'Active';
                $newStatus = $item['status'];
                $found = true;
                $this->saveToDb($item);
                break;
            }
        }
        unset($item);

        if (!$found) {
            return ['success' => false, 'message' => 'Committee member not found.'];
        }

        $this->saveToJson($all);
        return ['success' => true, 'message' => 'Status updated to ' . $newStatus, 'status' => $newStatus];
    }

    private function handleFileUpload($file, $prefix) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        if (!in_array($ext, $allowed)) {
            return null;
        }

        $cleanPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '_', $prefix);
        $fileName = $cleanPrefix . '_' . time() . '.' . $ext;
        $target = $this->uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return 'uploads/committee/' . $fileName;
        }
        return null;
    }

    private function saveToJson($data) {
        // Sort before saving
        usort($data, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($a['id'] ?? '', $b['id'] ?? '');
            }
            return $sa - $sb;
        });
        @file_put_contents($this->jsonFile, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function saveToDb($item) {
        if ($this->conn === null) return;
        $stmt = $this->conn->prepare("INSERT INTO league_committee (member_id, name, badge, designation, sub_designation, image_url, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE name=VALUES(name), badge=VALUES(badge), designation=VALUES(designation), sub_designation=VALUES(sub_designation), image_url=VALUES(image_url), sort_order=VALUES(sort_order), status=VALUES(status)");
        if ($stmt) {
            $memberId = $item['id'];
            $name = $item['name'];
            $badge = $item['badge'] ?? '';
            $desig = $item['designation'] ?? '';
            $subDesig = $item['subDesignation'] ?? '';
            $img = $item['imageUrl'] ?? '';
            $order = (int)($item['sortOrder'] ?? 1);
            $status = $item['status'] ?? 'Active';
            $stmt->bind_param("ssssssis", $memberId, $name, $badge, $desig, $subDesig, $img, $order, $status);
            $stmt->execute();
            $stmt->close();
        }
    }
}
