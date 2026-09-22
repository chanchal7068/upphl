<?php
// api/controllers/LiveVideoController.php
// Controller handling CRUD and Season-wise filtering for Completed Season Videos & Match Replays

class LiveVideoController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    public function __construct($dbConn = null) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/live_videos.json';
        $this->uploadDir = __DIR__ . '/../../uploads/videos/';

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }

        $this->ensureDefaults();
    }

    // Helper: Extract YouTube Video ID from any YouTube URL
    public static function extractYoutubeId($url) {
        $url = trim($url);
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $match)) {
            return $match[1];
        }
        return '';
    }

    // Ensure default completed season videos if JSON file does not exist
    private function ensureDefaults() {
        if (!file_exists($this->jsonFile)) {
            $defaultVideos = [
                [
                    'id'            => 'lv_s1_final',
                    'title'         => 'UPPHL Season 1 Grand Finale: Barbarik Warriors vs Ghaziabad Panthers - Full Match Replay',
                    'season'        => 'Season 1',
                    'videoCategory' => 'Full Match',
                    'videoUrl'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'embedUrl'      => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1',
                    'thumbnailUrl'  => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=800&q=80',
                    'duration'      => '1h 48m',
                    'matchDate'     => '2025-05-18',
                    'sortOrder'     => 1,
                    'status'        => 'Active',
                    'createdAt'     => date('Y-m-d H:i:s')
                ],
                [
                    'id'            => 'lv_s1_highlights',
                    'title'         => 'Season 1 Championship Highlights & Best Goals of the Tournament',
                    'season'        => 'Season 1',
                    'videoCategory' => 'Highlights',
                    'videoUrl'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'embedUrl'      => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1',
                    'thumbnailUrl'  => 'https://images.unsplash.com/photo-1587280501635-68a0e82cd5ff?auto=format&fit=crop&w=800&q=80',
                    'duration'      => '14m 20s',
                    'matchDate'     => '2025-05-19',
                    'sortOrder'     => 2,
                    'status'        => 'Active',
                    'createdAt'     => date('Y-m-d H:i:s')
                ],
                [
                    'id'            => 'lv_s1_semi1',
                    'title'         => 'Season 1 Semi Final 1: Kashi Kings vs Gorakhpur Rowdies - Thriller Replay',
                    'season'        => 'Season 1',
                    'videoCategory' => 'Full Match',
                    'videoUrl'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'embedUrl'      => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1',
                    'thumbnailUrl'  => 'https://images.unsplash.com/photo-1569517282132-25d22f4573e6?auto=format&fit=crop&w=800&q=80',
                    'duration'      => '1h 35m',
                    'matchDate'     => '2025-05-16',
                    'sortOrder'     => 3,
                    'status'        => 'Active',
                    'createdAt'     => date('Y-m-d H:i:s')
                ],
                [
                    'id'            => 'lv_s1_trophy',
                    'title'         => 'Season 1 Trophy Presentation & Closing Ceremony Highlights',
                    'season'        => 'Season 1',
                    'videoCategory' => 'Ceremony',
                    'videoUrl'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'embedUrl'      => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1',
                    'thumbnailUrl'  => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?auto=format&fit=crop&w=800&q=80',
                    'duration'      => '22m 10s',
                    'matchDate'     => '2025-05-18',
                    'sortOrder'     => 4,
                    'status'        => 'Active',
                    'createdAt'     => date('Y-m-d H:i:s')
                ]
            ];
            file_put_contents($this->jsonFile, json_encode($defaultVideos, JSON_PRETTY_PRINT));
        }

        $this->ensureTable();
    }

    private function ensureTable() {
        if ($this->conn === null) return;
        $sql = "CREATE TABLE IF NOT EXISTS live_season_videos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            video_id VARCHAR(100) UNIQUE NOT NULL,
            title VARCHAR(255) NOT NULL,
            season VARCHAR(100) DEFAULT 'Season 1',
            video_category VARCHAR(100) DEFAULT 'Full Match',
            video_url VARCHAR(500) NOT NULL,
            embed_url VARCHAR(500) NULL,
            thumbnail_url VARCHAR(500) NULL,
            duration VARCHAR(100) NULL,
            match_date VARCHAR(100) NULL,
            sort_order INT DEFAULT 0,
            status VARCHAR(50) DEFAULT 'Active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $this->conn->query($sql);
    }

    // GET /api/live-videos.php?season=...&category=...&activeOnly=...
    public function getAll($filterSeason = '', $filterCategory = '', $activeOnly = false, $returnArray = false) {
        $videos = [];

        if (file_exists($this->jsonFile)) {
            $data = json_decode(file_get_contents($this->jsonFile), true);
            if (is_array($data)) {
                $videos = $data;
            }
        }

        // Extract unique seasons
        $seasons = [];
        foreach ($videos as $v) {
            $s = trim($v['season'] ?? '');
            if (!empty($s) && !in_array($s, $seasons)) {
                $seasons[] = $s;
            }
        }
        rsort($seasons);

        // Sort by sortOrder ASC, then createdAt DESC
        usort($videos, function($a, $b) {
            $sa = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 999;
            $sb = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 999;
            if ($sa === $sb) {
                return strcmp($b['id'] ?? '', $a['id'] ?? '');
            }
            return $sa - $sb;
        });

        // Filter active
        if ($activeOnly) {
            $videos = array_filter($videos, function($v) {
                return ($v['status'] ?? 'Active') === 'Active';
            });
        }

        // Filter by Season
        if (!empty($filterSeason) && strtolower($filterSeason) !== 'all') {
            $videos = array_filter($videos, function($v) use ($filterSeason) {
                return strtolower(trim($v['season'] ?? '')) === strtolower(trim($filterSeason));
            });
        }

        // Filter by Category
        if (!empty($filterCategory) && strtolower($filterCategory) !== 'all') {
            $videos = array_filter($videos, function($v) use ($filterCategory) {
                return strtolower(trim($v['videoCategory'] ?? '')) === strtolower(trim($filterCategory));
            });
        }

        $result = [
            'success' => true,
            'count'   => count($videos),
            'seasons' => array_values($seasons),
            'videos'  => array_values($videos)
        ];

        if ($returnArray) {
            return $result;
        }

        echo json_encode($result);
        exit;
    }

    // POST /api/live-videos.php?action=save
    public function save() {
        $videoId       = trim($_POST['videoId'] ?? '');
        $title         = trim($_POST['title'] ?? '');
        $season        = trim($_POST['season'] ?? 'Season 1');
        $videoCategory = trim($_POST['videoCategory'] ?? 'Full Match');
        $videoUrl      = trim($_POST['videoUrl'] ?? '');
        $duration      = trim($_POST['duration'] ?? '');
        $matchDate     = trim($_POST['matchDate'] ?? date('Y-m-d'));
        $sortOrder     = isset($_POST['sortOrder']) && is_numeric($_POST['sortOrder']) ? (int)$_POST['sortOrder'] : 0;
        $status        = trim($_POST['status'] ?? 'Active');
        $existingThumb = trim($_POST['existingThumbnailUrl'] ?? '');

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Video title is required.']);
            exit;
        }
        if (empty($videoUrl)) {
            echo json_encode(['success' => false, 'message' => 'Video URL or YouTube link is required.']);
            exit;
        }
        if (empty($season)) {
            $season = 'Season 1';
        }

        // Parse YouTube ID & Embed URL
        $ytId = self::extractYoutubeId($videoUrl);
        if (!empty($ytId)) {
            $embedUrl = "https://www.youtube-nocookie.com/embed/{$ytId}?autoplay=1";
            $defaultThumb = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
        } else {
            $embedUrl = $videoUrl;
            $defaultThumb = 'assets/images/aboutus-banner.jpeg';
        }

        $thumbnailUrl = !empty($existingThumb) ? $existingThumb : $defaultThumb;

        // Handle custom thumbnail upload if provided
        if (isset($_FILES['thumbnailFile']) && !empty($_FILES['thumbnailFile']['name']) && $_FILES['thumbnailFile']['error'] === UPLOAD_ERR_OK) {
            $tmpName  = $_FILES['thumbnailFile']['tmp_name'];
            $origName = $_FILES['thumbnailFile']['name'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

            if (in_array($ext, $allowed)) {
                $newFileName = 'vid_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $destPath    = $this->uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $destPath)) {
                    $thumbnailUrl = 'uploads/videos/' . $newFileName;
                }
            }
        }

        $videos = [];
        if (file_exists($this->jsonFile)) {
            $videos = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $isEdit = false;
        $finalId = !empty($videoId) ? $videoId : ('lv_' . time() . '_' . rand(100, 999));

        $record = [
            'id'            => $finalId,
            'title'         => $title,
            'season'        => $season,
            'videoCategory' => $videoCategory,
            'videoUrl'      => $videoUrl,
            'embedUrl'      => $embedUrl,
            'thumbnailUrl'  => $thumbnailUrl,
            'duration'      => $duration,
            'matchDate'     => $matchDate,
            'sortOrder'     => $sortOrder,
            'status'        => $status,
            'createdAt'     => date('Y-m-d H:i:s')
        ];

        if (!empty($videoId)) {
            foreach ($videos as &$v) {
                if ($v['id'] === $videoId) {
                    $record['createdAt'] = $v['createdAt'] ?? date('Y-m-d H:i:s');
                    $v = $record;
                    $isEdit = true;
                    break;
                }
            }
            unset($v);
        }

        if (!$isEdit) {
            $videos[] = $record;
        }

        // Save to JSON
        file_put_contents($this->jsonFile, json_encode(array_values($videos), JSON_PRETTY_PRINT));

        // Save to DB
        $this->saveToDb($record);

        echo json_encode([
            'success' => true,
            'message' => $isEdit ? 'Video replay updated successfully!' : 'Video replay added successfully!',
            'video'   => $record
        ]);
        exit;
    }

    private function saveToDb($record) {
        if ($this->conn === null) return;
        $this->ensureTable();

        $stmt = $this->conn->prepare("INSERT INTO live_season_videos (video_id, title, season, video_category, video_url, embed_url, thumbnail_url, duration, match_date, sort_order, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            title = VALUES(title), season = VALUES(season), video_category = VALUES(video_category),
            video_url = VALUES(video_url), embed_url = VALUES(embed_url), thumbnail_url = VALUES(thumbnail_url),
            duration = VALUES(duration), match_date = VALUES(match_date), sort_order = VALUES(sort_order), status = VALUES(status)");

        if ($stmt) {
            $stmt->bind_param("ssssssssssss",
                $record['id'],
                $record['title'],
                $record['season'],
                $record['videoCategory'],
                $record['videoUrl'],
                $record['embedUrl'],
                $record['thumbnailUrl'],
                $record['duration'],
                $record['matchDate'],
                $record['sortOrder'],
                $record['status'],
                $record['createdAt']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    // POST /api/live-videos.php?action=delete
    public function delete() {
        $videoId = trim($_POST['videoId'] ?? '');

        if (empty($videoId)) {
            echo json_encode(['success' => false, 'message' => 'Video ID is required.']);
            exit;
        }

        $videos = [];
        if (file_exists($this->jsonFile)) {
            $videos = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $filtered = array_filter($videos, function($v) use ($videoId) {
            return $v['id'] !== $videoId;
        });

        file_put_contents($this->jsonFile, json_encode(array_values($filtered), JSON_PRETTY_PRINT));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM live_season_videos WHERE video_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $videoId);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Video deleted successfully!']);
        exit;
    }

    // POST /api/live-videos.php?action=toggle-status
    public function toggleStatus() {
        $videoId = trim($_POST['videoId'] ?? '');
        $status  = trim($_POST['status'] ?? 'Active');

        if (empty($videoId)) {
            echo json_encode(['success' => false, 'message' => 'Video ID is required.']);
            exit;
        }

        $videos = [];
        if (file_exists($this->jsonFile)) {
            $videos = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        foreach ($videos as &$v) {
            if ($v['id'] === $videoId) {
                $v['status'] = $status;
                break;
            }
        }
        unset($v);

        file_put_contents($this->jsonFile, json_encode(array_values($videos), JSON_PRETTY_PRINT));

        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("UPDATE live_season_videos SET status = ? WHERE video_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $status, $videoId);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Video status updated to ' . $status]);
        exit;
    }
}
