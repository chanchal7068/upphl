<?php
// api/controllers/NewsController.php
// Controller handling Latest News & Updates management

class NewsController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn = null) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/news.json';
        $this->uploadDir = __DIR__ . '/../../uploads/news/';
        if (!file_exists($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }
    }

    private function ensureTable() {
        if ($this->conn === null) return;
        $sql = "CREATE TABLE IF NOT EXISTS news_updates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            news_id VARCHAR(100) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            news_date VARCHAR(100) NOT NULL,
            category VARCHAR(100) DEFAULT 'News',
            description TEXT DEFAULT NULL,
            image_url VARCHAR(255) NOT NULL,
            gallery_link VARCHAR(255) DEFAULT 'gallery.php?cat=news',
            sort_order INT DEFAULT 0,
            status ENUM('Active','Inactive') DEFAULT 'Active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        @$this->conn->query($sql);
    }

    public function getAll($activeOnly = false, $limit = null) {
        $newsList = [];

        // 1. Read from JSON
        if (file_exists($this->jsonFile)) {
            $newsList = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        // 2. DB fallback
        if (empty($newsList) && $this->conn !== null) {
            $this->ensureTable();
            $res = $this->conn->query("SELECT * FROM news_updates ORDER BY sort_order ASC, id DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $newsList[] = [
                        'id'          => $row['news_id'],
                        'title'       => $row['title'],
                        'date'        => $row['news_date'],
                        'category'    => $row['category'] ?? 'News',
                        'description' => $row['description'] ?? '',
                        'imageUrl'    => $row['image_url'],
                        'galleryLink' => $row['gallery_link'] ?? 'gallery.php?cat=news',
                        'sortOrder'   => (int)($row['sort_order'] ?? 0),
                        'status'      => $row['status'] ?? 'Active'
                    ];
                }
            }
        }

        // Sort by sortOrder ASC
        usort($newsList, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($b['id'] ?? '', $a['id'] ?? '');
            }
            return $sa - $sb;
        });

        // Filter active only
        if ($activeOnly) {
            $newsList = array_filter($newsList, function($n) {
                return ($n['status'] ?? 'Active') === 'Active';
            });
        }

        $newsList = array_values($newsList);

        if ($limit !== null && $limit > 0) {
            $newsList = array_slice($newsList, 0, $limit);
        }

        return $newsList;
    }

    public function getById($newsId) {
        $all = $this->getAll(false);
        foreach ($all as $item) {
            if (($item['id'] ?? '') === $newsId) {
                return $item;
            }
        }
        return null;
    }

    public function save($data, $files = []) {
        $newsId = trim($data['id'] ?? ($data['newsId'] ?? ''));
        $title = trim($data['title'] ?? '');
        $dateStr = trim($data['date'] ?? '');
        $category = trim($data['category'] ?? 'News');
        $description = trim($data['description'] ?? '');
        $galleryLink = trim($data['galleryLink'] ?? 'gallery.php?cat=news');
        $sortOrder = isset($data['sortOrder']) ? (int)$data['sortOrder'] : 1;
        $status = in_array(($data['status'] ?? ''), ['Active', 'Inactive']) ? $data['status'] : 'Active';

        if (empty($title)) {
            return ['success' => false, 'message' => 'News title is required.'];
        }

        if (empty($newsId)) {
            $newsId = 'news_' . time() . '_' . rand(100, 999);
        }

        if (empty($dateStr)) {
            $dateStr = date('jS F Y');
        }

        if (empty($galleryLink)) {
            $galleryLink = 'gallery.php?cat=news';
        }

        $allNews = $this->getAll(false);
        $existing = null;
        $existingIndex = -1;

        foreach ($allNews as $idx => $item) {
            if (($item['id'] ?? '') === $newsId) {
                $existing = $item;
                $existingIndex = $idx;
                break;
            }
        }

        // Handle Image Upload
        $fileKey = !empty($files['newsImage']['name']) ? 'newsImage' : (!empty($files['imageFile']['name']) ? 'imageFile' : null);
        $imageUrl = $existing['imageUrl'] ?? (!empty($data['existingImage']) ? trim($data['existingImage']) : 'assets/images/ind1.jpg');
        
        if ($fileKey && $files[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $uploaded = $this->handleFileUpload($files[$fileKey], 'news_' . $newsId);
            if ($uploaded) {
                $imageUrl = $uploaded;
            }
        } elseif (!empty($data['imageUrl'])) {
            $imageUrl = trim($data['imageUrl']);
        }

        $newsRecord = [
            'id'          => $newsId,
            'title'       => $title,
            'date'        => $dateStr,
            'category'    => $category,
            'description' => $description,
            'imageUrl'    => $imageUrl,
            'galleryLink' => $galleryLink,
            'sortOrder'   => $sortOrder,
            'status'      => $status
        ];

        if ($existingIndex >= 0) {
            $allNews[$existingIndex] = $newsRecord;
        } else {
            $allNews[] = $newsRecord;
        }

        $this->saveToJson($allNews);
        $this->saveToDb($newsRecord);

        return ['success' => true, 'message' => 'News update saved successfully.', 'news' => $newsRecord];
    }

    public function delete($newsId) {
        $allNews = $this->getAll(false);
        $filtered = [];
        $found = false;

        foreach ($allNews as $item) {
            if (($item['id'] ?? '') === $newsId) {
                $found = true;
            } else {
                $filtered[] = $item;
            }
        }

        if (!$found) {
            return ['success' => false, 'message' => 'News item not found.'];
        }

        $this->saveToJson($filtered);

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("DELETE FROM news_updates WHERE news_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $newsId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => 'News item deleted successfully.'];
    }

    public function toggleStatus($newsId) {
        $allNews = $this->getAll(false);
        $found = false;
        $newStatus = 'Active';

        foreach ($allNews as &$item) {
            if (($item['id'] ?? '') === $newsId) {
                $item['status'] = (($item['status'] ?? 'Active') === 'Active') ? 'Inactive' : 'Active';
                $newStatus = $item['status'];
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            return ['success' => false, 'message' => 'News item not found.'];
        }

        $this->saveToJson($allNews);

        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("UPDATE news_updates SET status = ? WHERE news_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $newStatus, $newsId);
                $stmt->execute();
                $stmt->close();
            }
        }

        return ['success' => true, 'message' => "News status changed to {$newStatus}.", 'status' => $newStatus];
    }

    private function handleFileUpload($file, $prefix) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/gif', 'image/avif'];
        if (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
            return false;
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . '_' . time() . '.' . $ext;
        $dest = $this->uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/news/' . $filename;
        }
        return false;
    }

    private function saveToJson($data) {
        @file_put_contents($this->jsonFile, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function saveToDb($record) {
        if ($this->conn === null) return;
        $this->ensureTable();

        $newsId      = $record['id'];
        $title       = $record['title'];
        $newsDate    = $record['date'];
        $category    = $record['category'] ?? 'News';
        $description = $record['description'] ?? '';
        $imageUrl    = $record['imageUrl'];
        $galleryLink = $record['galleryLink'] ?? 'gallery.php?cat=news';
        $sortOrder   = (int)($record['sortOrder'] ?? 0);
        $status      = $record['status'] ?? 'Active';

        $stmt = $this->conn->prepare("INSERT INTO news_updates (news_id, title, news_date, category, description, image_url, gallery_link, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            title = VALUES(title), news_date = VALUES(news_date), category = VALUES(category),
            description = VALUES(description), image_url = VALUES(image_url), gallery_link = VALUES(gallery_link),
            sort_order = VALUES(sort_order), status = VALUES(status)");

        if ($stmt) {
            $stmt->bind_param("sssssssis", $newsId, $title, $newsDate, $category, $description, $imageUrl, $galleryLink, $sortOrder, $status);
            $stmt->execute();
            $stmt->close();
        }
    }
}
