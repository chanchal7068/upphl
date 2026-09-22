<?php
// api/controllers/PageBannerController.php
// Controller handling all Inner Page Banners (all pages except Homepage)

class PageBannerController {
    private $conn;
    private $jsonFile;
    private $uploadDir;

    private $defaultPages = [
        'about' => [
            'pageKey'     => 'about',
            'pageName'    => 'About Us',
            'url'         => 'about.php',
            'badgeText'   => 'UP PRO HANDBALL LEAGUE',
            'title'       => 'About the League',
            'subtitle'    => 'Discover the history, objectives, and vision behind the UP Pro Handball League.',
            'bannerImage' => 'assets/images/aboutus-banner.jpeg',
            'status'      => 'Active'
        ],
        'league-details' => [
            'pageKey'     => 'league-details',
            'pageName'    => 'League Details',
            'url'         => 'league-details.php',
            'badgeText'   => 'SEASON 2 RULES & STRUCTURE',
            'title'       => 'League Details',
            'subtitle'    => 'Get to know the eligibility, age limits, rules, and structures governing our professional league matches.',
            'bannerImage' => 'assets/images/league-details-banner.jpg',
            'status'      => 'Active'
        ],
        'fixtures' => [
            'pageKey'     => 'fixtures',
            'pageName'    => 'Match Fixtures',
            'url'         => 'fixtures.php',
            'badgeText'   => 'OFFICIAL MATCH SCHEDULE',
            'title'       => 'Match Fixtures',
            'subtitle'    => 'Stay updated with official match fixtures, match days, starting times, venue locations, live statuses, and historical scores.',
            'bannerImage' => 'assets/images/fixtures-banner.jpeg',
            'status'      => 'Active'
        ],
        'team-start' => [
            'pageKey'     => 'team-start',
            'pageName'    => 'Teams & MVPs',
            'url'         => 'team-start.php',
            'badgeText'   => 'FRANCHISE TEAMS & PLAYERS',
            'title'       => 'Teams & Player Stats',
            'subtitle'    => 'Explore the 6 official franchise teams and the standout MVP players of the UP Pro Handball League.',
            'bannerImage' => 'assets/images/fixtures-banner.jpeg',
            'status'      => 'Active'
        ],
        'point-table' => [
            'pageKey'     => 'point-table',
            'pageName'    => 'Points Table',
            'url'         => 'point-table.php',
            'badgeText'   => 'SEASON 2 LEAGUE STANDINGS',
            'title'       => 'Point Table',
            'subtitle'    => 'Check the latest league standings, points, goals difference, and team forms for the UP Pro Handball League.',
            'bannerImage' => 'assets/images/league-details-banner.jpg',
            'status'      => 'Active'
        ],
        'gallery' => [
            'pageKey'     => 'gallery',
            'pageName'    => 'Media Gallery',
            'url'         => 'gallery.php',
            'badgeText'   => 'PHOTO & VIDEO HIGHLIGHTS',
            'title'       => 'Media Gallery',
            'subtitle'    => 'Explore high-energy match glimpses, trophy launch, auction highlights, trials and updates from UPPHL.',
            'bannerImage' => 'assets/images/fixtures-banner.jpeg',
            'status'      => 'Active'
        ],
        'live' => [
            'pageKey'     => 'live',
            'pageName'    => 'Live Matches',
            'url'         => 'live.php',
            'badgeText'   => 'Official Live Broadcast Hub',
            'title'       => 'Watch UPPHL Live Action',
            'subtitle'    => 'Experience every high-octane goal, rapid counter-attack, and dramatic finish live across our official digital streaming and national television broadcast partners.',
            'bannerImage' => 'assets/images/fixtures-banner.jpeg',
            'status'      => 'Active'
        ],
        'contact' => [
            'pageKey'     => 'contact',
            'pageName'    => 'Contact Us',
            'url'         => 'contact.php',
            'badgeText'   => 'GET IN TOUCH',
            'title'       => 'Contact Us',
            'subtitle'    => 'Get in touch with the UP Pro Handball League team for inquiries, registrations, sponsorships, and support.',
            'bannerImage' => 'assets/images/contact-banner.jpeg',
            'status'      => 'Active'
        ],
        'player-registration' => [
            'pageKey'     => 'player-registration',
            'pageName'    => 'Player Registration',
            'url'         => 'player-registration.php',
            'badgeText'   => 'SEASON 2 TRIALS',
            'title'       => 'UP Pro Handball League Player Trials Registration',
            'subtitle'    => 'Fill every detail exactly as it appears on your Aadhaar card. UPPHL officials verify all information and documents during player selection.',
            'bannerImage' => 'assets/images/aboutus-banner.jpeg',
            'status'      => 'Active'
        ],
        'players' => [
            'pageKey'     => 'players',
            'pageName'    => 'Player Profile',
            'url'         => 'players.php',
            'badgeText'   => 'UPPHL PLAYER PORTAL',
            'title'       => 'Player Profile',
            'subtitle'    => 'View and manage your registered player information for the UP Pro Handball League.',
            'bannerImage' => 'assets/images/league-details-banner.jpg',
            'status'      => 'Active'
        ],
        'privacy-policy' => [
            'pageKey'     => 'privacy-policy',
            'pageName'    => 'Privacy Policy',
            'url'         => 'privacy-policy.php',
            'badgeText'   => 'Official Policy',
            'title'       => 'Privacy Policy',
            'subtitle'    => 'Welcome to the official website of UP Pro Handball League (UPPHL). We respect your privacy and are committed to protecting your personal information.',
            'bannerImage' => 'assets/images/aboutus-banner.jpeg',
            'status'      => 'Active'
        ],
        'terms-and-conditions' => [
            'pageKey'     => 'terms-and-conditions',
            'pageName'    => 'Terms & Conditions',
            'url'         => 'terms-and-conditions.php',
            'badgeText'   => 'Legal Terms',
            'title'       => 'Terms and Conditions',
            'subtitle'    => 'Please read these terms and conditions carefully before participating in or using the UP Pro Handball League platform.',
            'bannerImage' => 'assets/images/aboutus-banner.jpeg',
            'status'      => 'Active'
        ],
        'refund-policy' => [
            'pageKey'     => 'refund-policy',
            'pageName'    => 'Refund Policy',
            'url'         => 'refund-policy.php',
            'badgeText'   => 'Official Policy',
            'title'       => 'Cancellation & Refund Policy',
            'subtitle'    => 'Please review our policy regarding player registrations, trial fees, and refund requests.',
            'bannerImage' => 'assets/images/aboutus-banner.jpeg',
            'status'      => 'Active'
        ]
    ];

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/page_banners.json';
        $this->uploadDir = __DIR__ . '/../../uploads/page_banners/';

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // GET /api/page-banners.php
    public function getAll() {
        $banners = $this->defaultPages;

        if (file_exists($this->jsonFile)) {
            $custom = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            if (is_array($custom)) {
                foreach ($custom as $k => $item) {
                    if (isset($banners[$k])) {
                        $banners[$k] = array_merge($banners[$k], $item);
                    } else {
                        $banners[$k] = $item;
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'count'   => count($banners),
            'banners' => $banners
        ]);
        exit;
    }

    // POST /api/page-banners.php?action=save
    public function save() {
        $pageKey   = trim($_POST['pageKey'] ?? '');
        $title     = trim($_POST['title'] ?? '');
        $subtitle  = trim($_POST['subtitle'] ?? '');
        $badgeText = trim($_POST['badgeText'] ?? '');
        $status    = trim($_POST['status'] ?? 'Active');
        $existingImg = trim($_POST['existingBannerImage'] ?? '');

        if (empty($pageKey)) {
            echo json_encode(['success' => false, 'message' => 'Page key is required.']);
            exit;
        }

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Banner heading/title is required.']);
            exit;
        }

        $bannerImage = $existingImg;

        // Handle Image Upload
        if (isset($_FILES['bannerImageFile']) && $_FILES['bannerImageFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmp  = $_FILES['bannerImageFile']['tmp_name'];
            $fileName = $_FILES['bannerImageFile']['name'];
            $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
            if (!in_array($ext, $allowedExts)) {
                echo json_encode(['success' => false, 'message' => 'Invalid image format. Allowed: JPG, PNG, WEBP, GIF, SVG.']);
                exit;
            }

            $newFileName = 'page_' . preg_replace('/[^a-z0-9_-]/i', '_', $pageKey) . '_' . time() . '.' . $ext;
            $destPath = $this->uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destPath)) {
                $bannerImage = 'uploads/page_banners/' . $newFileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload banner image.']);
                exit;
            }
        }

        // Load existing
        $banners = $this->defaultPages;
        if (file_exists($this->jsonFile)) {
            $custom = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            if (is_array($custom)) {
                $banners = array_merge($banners, $custom);
            }
        }

        $baseInfo = $this->defaultPages[$pageKey] ?? [
            'pageKey'  => $pageKey,
            'pageName' => ucwords(str_replace(['-', '_'], ' ', $pageKey)),
            'url'      => $pageKey . '.php'
        ];

        $bannerData = [
            'pageKey'     => $pageKey,
            'pageName'    => $baseInfo['pageName'] ?? ucwords(str_replace(['-', '_'], ' ', $pageKey)),
            'url'         => $baseInfo['url'] ?? ($pageKey . '.php'),
            'badgeText'   => $badgeText,
            'title'       => $title,
            'subtitle'    => $subtitle,
            'bannerImage' => $bannerImage,
            'status'      => $status,
            'updatedAt'   => date('Y-m-d H:i:s')
        ];

        $banners[$pageKey] = $bannerData;

        file_put_contents($this->jsonFile, json_encode($banners, JSON_PRETTY_PRINT));

        // Sync to DB if available
        if ($this->conn !== null) {
            $this->ensureTable();
            $stmt = $this->conn->prepare("INSERT INTO page_banners (page_key, page_name, url, badge_text, title, subtitle, banner_image, status, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                page_name=VALUES(page_name), url=VALUES(url), badge_text=VALUES(badge_text), title=VALUES(title), subtitle=VALUES(subtitle), banner_image=VALUES(banner_image), status=VALUES(status), updated_at=NOW()");
            if ($stmt) {
                $stmt->bind_param('ssssssss', $pageKey, $bannerData['pageName'], $bannerData['url'], $badgeText, $title, $subtitle, $bannerImage, $status);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Banner for "' . ($bannerData['pageName']) . '" updated successfully!',
            'banner'  => $bannerData
        ]);
        exit;
    }

    private function ensureTable() {
        $this->conn->query("CREATE TABLE IF NOT EXISTS page_banners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_key VARCHAR(50) UNIQUE NOT NULL,
            page_name VARCHAR(100) NOT NULL,
            url VARCHAR(100) NOT NULL,
            badge_text VARCHAR(150),
            title VARCHAR(255) NOT NULL,
            subtitle TEXT,
            banner_image TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'Active',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}
