<?php
// api/controllers/GalleryController.php
// Controller handling all Gallery operations: single & multiple photo uploads, season-wise, delete

class GalleryController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn = null) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/gallery.json';
        $this->uploadDir = __DIR__ . '/../../uploads/gallery/';

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }

        $this->ensureTable();
    }

    // GET /api/gallery.php
    public function getAll($filterSeason = '', $filterCategory = '', $returnArray = false) {
        $photos = [];

        // 1. Fetch from JSON
        if (file_exists($this->jsonFile)) {
            $data = json_decode(file_get_contents($this->jsonFile), true);
            if (is_array($data)) {
                $photos = $data;
            }
        }

        // 2. Fallback to DB
        if (empty($photos) && $this->conn !== null) {
            $res = $this->conn->query("SELECT * FROM gallery_photos ORDER BY id DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $photos[] = [
                        'id'            => $row['photo_id'],
                        'title'         => $row['title'] ?? '',
                        'category'      => $row['category'] ?? 'glimpses',
                        'categoryLabel' => $row['category_label'] ?? 'Match Glimpses',
                        'season'        => $row['season'] ?? 'Season 2',
                        'imageUrl'      => $row['image_url'],
                        'createdAt'     => $row['created_at']
                    ];
                }
            }
        }

        // Extract unique seasons
        $seasons = [];
        foreach ($photos as $p) {
            $s = trim($p['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
        }
        rsort($seasons);

        // Apply Filters
        $filtered = $photos;
        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $filtered = array_filter($filtered, function($p) use ($filterSeason) {
                return strtolower(trim($p['season'] ?? '')) === strtolower(trim($filterSeason));
            });
        }

        if (!empty($filterCategory) && strtolower($filterCategory) !== 'all') {
            $filtered = array_filter($filtered, function($p) use ($filterCategory) {
                return strtolower(trim($p['category'] ?? '')) === strtolower(trim($filterCategory));
            });
        }

        $result = [
            'success' => true,
            'count'   => count($filtered),
            'seasons' => array_values($seasons),
            'photos'  => array_values($filtered)
        ];

        if ($returnArray) {
            return $result;
        }

        echo json_encode($result);
        exit;
    }

    // POST /api/gallery.php?action=save
    public function save() {
        $photoId     = trim($_POST['photoId'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $season      = trim($_POST['season'] ?? 'Season 2');
        $category    = trim($_POST['category'] ?? 'glimpses');
        $existingUrl = trim($_POST['existingImageUrl'] ?? '');

        if (empty($season)) {
            $season = 'Season 2';
        }

        $categoryMap = [
            'announcement' => 'Announcement Day',
            'trail'        => 'Trails',
            'trophy'       => 'Trophy Launch',
            'auction'      => 'Auction',
            'glimpses'     => 'Match Glimpses',
            'news'         => 'News'
        ];
        $categoryLabel = $categoryMap[$category] ?? ucfirst($category);

        $photos = [];
        if (file_exists($this->jsonFile)) {
            $photos = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $uploadedRecords = [];

        // Check if multiple files were uploaded via photoFiles[]
        $hasMultiFiles = isset($_FILES['photoFiles']) && is_array($_FILES['photoFiles']['name']);
        $hasSingleFile = isset($_FILES['photoFile']) && !empty($_FILES['photoFile']['name']) && $_FILES['photoFile']['error'] === UPLOAD_ERR_OK;

        if ($hasMultiFiles) {
            $totalFiles = count($_FILES['photoFiles']['name']);
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES['photoFiles']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName  = $_FILES['photoFiles']['tmp_name'][$i];
                    $origName = $_FILES['photoFiles']['name'][$i];
                    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                    $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg'];

                    if (!in_array($ext, $allowed)) continue;

                    $newFileName = 'gal_' . time() . '_' . bin2hex(random_bytes(4)) . '_' . $i . '.' . $ext;
                    $destPath    = $this->uploadDir . $newFileName;

                    if (move_uploaded_file($tmpName, $destPath)) {
                        $newPhotoId = 'gal_' . time() . '_' . rand(100, 999) . '_' . $i;
                        $record = [
                            'id'            => $newPhotoId,
                            'title'         => $title,
                            'category'      => $category,
                            'categoryLabel' => $categoryLabel,
                            'season'        => $season,
                            'imageUrl'      => 'uploads/gallery/' . $newFileName,
                            'createdAt'     => date('Y-m-d H:i:s')
                        ];
                        $uploadedRecords[] = $record;
                        array_unshift($photos, $record);
                        $this->saveToDb($record);
                    }
                }
            }
        } elseif ($hasSingleFile) {
            $tmpName  = $_FILES['photoFile']['tmp_name'];
            $origName = $_FILES['photoFile']['name'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg'];

            if (in_array($ext, $allowed)) {
                $newFileName = 'gal_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destPath    = $this->uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $destPath)) {
                    $newPhotoId = !empty($photoId) ? $photoId : ('gal_' . time() . '_' . rand(100, 999));
                    $record = [
                        'id'            => $newPhotoId,
                        'title'         => $title,
                        'category'      => $category,
                        'categoryLabel' => $categoryLabel,
                        'season'        => $season,
                        'imageUrl'      => 'uploads/gallery/' . $newFileName,
                        'createdAt'     => date('Y-m-d H:i:s')
                    ];

                    if (!empty($photoId)) {
                        $updated = false;
                        foreach ($photos as &$p) {
                            if ($p['id'] === $photoId) {
                                $record['createdAt'] = $p['createdAt'] ?? date('Y-m-d H:i:s');
                                $p = $record;
                                $updated = true;
                                break;
                            }
                        }
                        unset($p);
                        if (!$updated) array_unshift($photos, $record);
                    } else {
                        array_unshift($photos, $record);
                    }

                    $uploadedRecords[] = $record;
                    $this->saveToDb($record);
                }
            }
        } elseif (!empty($photoId) && !empty($existingUrl)) {
            // Edit existing photo metadata without re-uploading file
            $record = [
                'id'            => $photoId,
                'title'         => $title,
                'category'      => $category,
                'categoryLabel' => $categoryLabel,
                'season'        => $season,
                'imageUrl'      => $existingUrl,
                'createdAt'     => date('Y-m-d H:i:s')
            ];

            foreach ($photos as &$p) {
                if ($p['id'] === $photoId) {
                    $record['createdAt'] = $p['createdAt'] ?? date('Y-m-d H:i:s');
                    $p = $record;
                    break;
                }
            }
            unset($p);
            $uploadedRecords[] = $record;
            $this->saveToDb($record);
        }

        if (empty($uploadedRecords)) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one valid image to upload.']);
            exit;
        }

        file_put_contents($this->jsonFile, json_encode(array_values($photos), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $count = count($uploadedRecords);
        echo json_encode([
            'success' => true,
            'message' => $count > 1 ? "$count photos uploaded to gallery successfully!" : "Photo saved to gallery successfully!",
            'count'   => $count,
            'photos'  => $uploadedRecords
        ]);
        exit;
    }

    // POST /api/gallery.php?action=delete
    public function delete() {
        $photoId = trim($_POST['photoId'] ?? ($_POST['id'] ?? ''));
        if (empty($photoId)) {
            echo json_encode(['success' => false, 'message' => 'Photo ID required.']);
            exit;
        }

        $photos = [];
        if (file_exists($this->jsonFile)) {
            $photos = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $deletedUrl = null;
        $newPhotos = [];
        foreach ($photos as $p) {
            if (($p['id'] ?? '') === $photoId) {
                $deletedUrl = $p['imageUrl'] ?? null;
            } else {
                $newPhotos[] = $p;
            }
        }

        file_put_contents($this->jsonFile, json_encode(array_values($newPhotos), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($deletedUrl && strpos($deletedUrl, 'uploads/gallery/') !== false) {
            $fullPath = __DIR__ . '/../../' . $deletedUrl;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM gallery_photos WHERE photo_id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $photoId);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Photo deleted from gallery successfully.']);
        exit;
    }

    private function saveToDb($record) {
        if ($this->conn === null) return;
        $this->ensureTable();
        $stmt = $this->conn->prepare("INSERT INTO gallery_photos (photo_id, title, category, category_label, season, image_url, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            title=VALUES(title), category=VALUES(category), category_label=VALUES(category_label), season=VALUES(season), image_url=VALUES(image_url)");
        if ($stmt) {
            $pId = $record['id'];
            $title = $record['title'] ?? '';
            $cat = $record['category'] ?? 'glimpses';
            $catLbl = $record['categoryLabel'] ?? 'Match Glimpses';
            $season = $record['season'] ?? 'Season 2';
            $imgUrl = $record['imageUrl'];
            $stmt->bind_param('ssssss', $pId, $title, $cat, $catLbl, $season, $imgUrl);
            $stmt->execute();
            $stmt->close();
        }
    }

    private function ensureTable() {
        if ($this->conn === null) return;
        $this->conn->query("CREATE TABLE IF NOT EXISTS gallery_photos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            photo_id VARCHAR(50) UNIQUE NOT NULL,
            title VARCHAR(255) DEFAULT '',
            category VARCHAR(50) NOT NULL,
            category_label VARCHAR(100) NOT NULL,
            season VARCHAR(50) DEFAULT 'Season 2',
            image_url TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}
