<?php
// admin/index.php
// UPPHL Admin Panel — Full Dashboard with Players, Hero Banners, Gallery, Messages & Contact Settings

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ADMIN_USER', 'Arnavdigitalfoundation@gmail.com');
define('ADMIN_PASS', 'Arnav#Digital26!admin');

// Logout Handler
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['upphl_admin_logged']);
    header('Location: index.php');
    exit;
}

// Login Handler
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['upphl_admin_logged'] = true;
        header('Location: index.php');
        exit;
    } else {
        $errorMsg = 'Invalid Admin Username or Password!';
    }
}

$isLogged = isset($_SESSION['upphl_admin_logged']);

// Fetch Data for Admin
$players = [];
$messages = [];
$banners = [];
$pageBanners = [];
$galleryPhotos = [];
$standings = [];
$fixtures = [];
$mvpPlayers = [];
$winners = [];
$standingsSeasons = [];
$fixturesSeasons = [];
$mvpSeasons = [];
$mvpDays = [];
$winnerSeasons = [];
$totalCount = 0;
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;
$messageCount = 0;
$bannerCount = 0;
$pageBannerCount = 0;
$galleryCount = 0;
$standingsCount = 0;
$fixturesCount = 0;
$mvpCount = 0;
$winnersCount = 0;
$totalRevenue = 0;
$contactSettings = [
    'phone'     => '+91 7084900009',
    'email'     => 'uphandballleague@gmail.com',
    'address'   => 'D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006',
    'mapEmbed'  => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8577.971649410943!2d82.97167766165312!3d25.317881457351188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x398e2df211e6302d%3A0x71a3709ed12c0baa!2sChandpur%2C%20Chandua%20Chhittupur%2C%20Shivpurwa%2C%20Varanasi%2C%20Uttar%20Pradesh%20221002!5e0!3m2!1sen!2sin!4v1787729110796!5m2!1sen!2sin',
    'facebook'  => 'https://facebook.com/upprohandballleague',
    'instagram' => 'https://instagram.com/upprohandballleague',
    'youtube'   => 'https://www.youtube.com/@sportscastindia'
];

if ($isLogged) {
    $noJsonHeader = true;
    require_once __DIR__ . '/../api/config.php';
    require_once __DIR__ . '/../includes/functions.php';

    // 1. Fetch Players (Prioritize rich JSON store, fallback to DB)
    $jsonFile = __DIR__ . '/../uploads/players.json';
    if (file_exists($jsonFile)) {
        $players = json_decode(file_get_contents($jsonFile), true) ?? [];
    }

    if (empty($players) && $conn !== null) {
        $res = $conn->query("SELECT * FROM registered_players ORDER BY id DESC");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $players[] = [
                    'playerId'        => $row['player_id'],
                    'password'        => $row['password'] ?? '',
                    'fullName'        => $row['full_name'],
                    'fatherName'      => $row['father_name'] ?? '',
                    'motherName'      => $row['mother_name'] ?? '',
                    'dob'             => $row['dob'],
                    'age'             => $row['age'],
                    'gender'          => $row['gender'],
                    'height'          => $row['height'],
                    'weight'          => $row['weight'],
                    'hand'            => $row['playing_hand'] ?? '',
                    'primaryPos'      => $row['primary_pos'],
                    'secondaryPos'    => $row['secondary_pos'] ?? 'None',
                    'totalExp'        => $row['total_exp'] ?? '',
                    'level'           => $row['level'] ?? '',
                    'club'            => $row['club'] ?? '',
                    'address'         => $row['address'] ?? '',
                    'district'        => $row['district'],
                    'state'           => $row['state'],
                    'mobile'          => $row['mobile'],
                    'email'           => $row['email'],
                    'photoUrl'        => $row['photo_url'] ?? '',
                    'aadhaarFrontUrl' => $row['aadhaar_front_url'] ?? '',
                    'aadhaarBackUrl'  => $row['aadhaar_back_url'] ?? '',
                    'certUrls'        => !empty($row['cert_urls']) ? json_decode($row['cert_urls'], true) : [],
                    'paymentId'       => $row['payment_id'] ?? '',
                    'paymentMethod'   => $row['payment_method'] ?? 'ICICI Orange PG',
                    'amountPaid'      => $row['amount_paid'] ?? '₹1,000',
                    'status'          => $row['status'] ?? 'Pending',
                    'submittedAt'     => $row['created_at'] ?? ''
                ];
            }
        }
    }

    // 2. Fetch Messages
    if ($conn !== null) {
        $mRes = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
        if ($mRes) {
            while ($mRow = $mRes->fetch_assoc()) {
                $messages[] = [
                    'id'          => $mRow['id'],
                    'fullName'    => $mRow['full_name'],
                    'email'       => $mRow['email'],
                    'phone'       => $mRow['phone'],
                    'subject'     => $mRow['subject'],
                    'message'     => $mRow['message'],
                    'submittedAt' => $mRow['submitted_at']
                ];
            }
        }
    }

    if (empty($messages)) {
        $msgJsonFile = __DIR__ . '/../uploads/messages.json';
        if (file_exists($msgJsonFile)) {
            $messages = json_decode(file_get_contents($msgJsonFile), true) ?? [];
        }
    }

    // 3. Fetch Banners
    $bannerJsonFile = __DIR__ . '/../uploads/banners.json';
    if (file_exists($bannerJsonFile)) {
        $banners = json_decode(file_get_contents($bannerJsonFile), true) ?? [];
    }

    if (empty($banners) && $conn !== null) {
        $bRes = $conn->query("SELECT * FROM banners ORDER BY sort_order ASC, id DESC");
        if ($bRes) {
            while ($bRow = $bRes->fetch_assoc()) {
                $banners[] = [
                    'bannerId'   => $bRow['banner_id'],
                    'badgeText'  => $bRow['badge_text'] ?? '',
                    'heading'    => $bRow['heading'],
                    'subtitle'   => $bRow['subtitle'] ?? '',
                    'btn1Text'   => $bRow['btn1_text'] ?? '',
                    'btn1Link'   => $bRow['btn1_link'] ?? '',
                    'btn2Text'   => $bRow['btn2_text'] ?? '',
                    'btn2Link'   => $bRow['btn2_link'] ?? '',
                    'mediaType'  => $bRow['media_type'] ?? 'image',
                    'mediaUrl'   => $bRow['media_url'],
                    'status'     => $bRow['status'] ?? 'Active',
                    'sortOrder'  => isset($bRow['sort_order']) ? (int)$bRow['sort_order'] : 0,
                    'createdAt'  => $bRow['created_at'] ?? ''
                ];
            }
        }
    }

    // Sort banners by sortOrder ASC, then createdAt DESC
    usort($banners, function($a, $b) {
        $orderA = isset($a['sortOrder']) && is_numeric($a['sortOrder']) ? (int)$a['sortOrder'] : 0;
        $orderB = isset($b['sortOrder']) && is_numeric($b['sortOrder']) ? (int)$b['sortOrder'] : 0;
        if ($orderA === $orderB) {
            return strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? '');
        }
        return $orderA <=> $orderB;
    });

    // 3b. Fetch Inner Page Banners
    $pageBannersJsonFile = __DIR__ . '/../uploads/page_banners.json';
    if (file_exists($pageBannersJsonFile)) {
        $pageBanners = json_decode(file_get_contents($pageBannersJsonFile), true) ?? [];
    }
    if (empty($pageBanners) && $conn !== null) {
        $pbRes = $conn->query("SELECT * FROM page_banners");
        if ($pbRes) {
            while ($pbRow = $pbRes->fetch_assoc()) {
                $pageBanners[$pbRow['page_key']] = [
                    'pageKey'     => $pbRow['page_key'],
                    'pageName'    => $pbRow['page_name'],
                    'url'         => $pbRow['url'],
                    'badgeText'   => $pbRow['badge_text'] ?? '',
                    'title'       => $pbRow['title'],
                    'subtitle'    => $pbRow['subtitle'] ?? '',
                    'bannerImage' => $pbRow['banner_image'],
                    'status'      => $pbRow['status'] ?? 'Active',
                    'updatedAt'   => $pbRow['updated_at'] ?? ''
                ];
            }
        }
    }

    // 4. Fetch Gallery Photos
    $galleryJsonFile = __DIR__ . '/../uploads/gallery.json';
    if (file_exists($galleryJsonFile)) {
        $galleryPhotos = json_decode(file_get_contents($galleryJsonFile), true) ?? [];
    }

    if (empty($galleryPhotos) && $conn !== null) {
        $gRes = $conn->query("SELECT * FROM gallery_photos ORDER BY id DESC");
        if ($gRes) {
            while ($gRow = $gRes->fetch_assoc()) {
                $galleryPhotos[] = [
                    'id'            => $gRow['photo_id'],
                    'title'         => $gRow['title'],
                    'category'      => $gRow['category'],
                    'categoryLabel' => $gRow['category_label'],
                    'season'        => $gRow['season'],
                    'imageUrl'      => $gRow['image_url'],
                    'createdAt'     => $gRow['created_at']
                ];
            }
        }
    }

    // 5. Fetch Contact Settings
    $contactSettingsFile = __DIR__ . '/../uploads/contact_settings.json';
    if (file_exists($contactSettingsFile)) {
        $loadedContact = json_decode(file_get_contents($contactSettingsFile), true);
        if (is_array($loadedContact)) {
            $contactSettings = array_merge($contactSettings, $loadedContact);
        }
    }

    // 5b. Fetch Home Hero Stats Counters
    $homeStats = upphl_get_home_stats();

    // 6. Fetch Standings / Points Table
    $standings = [];
    $standingsJsonFile = __DIR__ . '/../uploads/standings.json';
    if (file_exists($standingsJsonFile)) {
        $standings = json_decode(file_get_contents($standingsJsonFile), true) ?? [];
    }

    if (empty($standings) && $conn !== null) {
        $stdRes = $conn->query("SELECT * FROM standings ORDER BY pts DESC, gd DESC");
        if ($stdRes) {
            while ($stdRow = $stdRes->fetch_assoc()) {
                $standings[] = [
                    'id'       => $stdRow['row_id'],
                    'season'   => $stdRow['season'],
                    'teamName' => $stdRow['team_name'],
                    'teamLogo' => $stdRow['team_logo'],
                    'played'   => (int)$stdRow['played'],
                    'won'      => (int)$stdRow['won'],
                    'draw'     => (int)$stdRow['draw'],
                    'lost'     => (int)$stdRow['lost'],
                    'gf'       => (int)$stdRow['gf'],
                    'ga'       => (int)$stdRow['ga'],
                    'gd'       => (int)$stdRow['gd'],
                    'pts'      => (int)$stdRow['pts'],
                    'form'     => $stdRow['form']
                ];
            }
        }
    }

    $standingsSeasons = [];
    foreach ($standings as $st) {
        $stName = trim($st['season'] ?? '');
        if (!empty($stName) && !in_array($stName, $standingsSeasons)) {
            $standingsSeasons[] = $stName;
        }
    }
    rsort($standingsSeasons);

    // 7. Fetch Fixtures
    $fixtures = [];
    $fixturesJsonFile = __DIR__ . '/../uploads/fixtures.json';
    if (file_exists($fixturesJsonFile)) {
        $fixtures = json_decode(file_get_contents($fixturesJsonFile), true) ?? [];
    }

    if (empty($fixtures) && $conn !== null) {
        $fRes = $conn->query("SELECT * FROM fixtures ORDER BY match_date ASC, match_time ASC");
        if ($fRes) {
            while ($fRow = $fRes->fetch_assoc()) {
                $fixtures[] = [
                    'id'          => $fRow['fixture_id'],
                    'season'      => $fRow['season'],
                    'matchDay'    => $fRow['match_day'] ?? 'Day 1',
                    'matchNumber' => $fRow['match_number'] ?? 'Match 1',
                    'matchTitle'  => $fRow['match_title'],
                    'team1Name'   => $fRow['team1_name'],
                    'team1Logo'   => $fRow['team1_logo'],
                    'team2Name'   => $fRow['team2_name'],
                    'team2Logo'   => $fRow['team2_logo'],
                    'matchDate'   => $fRow['match_date'],
                    'startTime'   => $fRow['start_time'] ?? ($fRow['match_time'] ?? '04:00 PM'),
                    'endTime'     => $fRow['end_time'] ?? '',
                    'matchTime'   => $fRow['start_time'] ?? ($fRow['match_time'] ?? '04:00 PM'),
                    'stadium'     => $fRow['stadium'],
                    'team1Score'  => $fRow['team1_score'],
                    'team2Score'  => $fRow['team2_score'],
                    'status'      => $fRow['status'],
                    'createdAt'   => $fRow['created_at']
                ];
            }
        }
    }

    $fixturesSeasons = [];
    foreach ($fixtures as &$fx) {
        $sName = trim($fx['season'] ?? '');
        if (!empty($sName) && !in_array($sName, $fixturesSeasons)) {
            $fixturesSeasons[] = $sName;
        }
        if (empty($fx['matchDay'])) {
            $fx['matchDay'] = 'Day 1';
        }
        if (empty($fx['matchNumber'])) {
            $fx['matchNumber'] = 'Match 1';
        }
        if (empty($fx['startTime']) && !empty($fx['matchTime'])) {
            $fx['startTime'] = $fx['matchTime'];
        }
        if (empty($fx['matchTime']) && !empty($fx['startTime'])) {
            $fx['matchTime'] = $fx['startTime'];
        }
        if (!isset($fx['endTime'])) {
            $fx['endTime'] = '';
        }

        // Compute status dynamically
        $mStatus = trim($fx['status'] ?? '');
        if (!empty($mStatus) && !in_array(strtolower($mStatus), ['auto', 'upcoming'])) {
            $fx['computedStatus'] = $mStatus;
        } else {
            $dStr = trim($fx['matchDate'] ?? '');
            $tStart = trim($fx['startTime'] ?? ($fx['matchTime'] ?? '18:00'));
            $tEnd = trim($fx['endTime'] ?? '');
            $tsStart = strtotime("$dStr $tStart") ?: strtotime($dStr);
            if ($tsStart) {
                $tsEnd = !empty($tEnd) ? (strtotime("$dStr $tEnd") ?: ($tsStart + 7200)) : ($tsStart + 7200);
                if (time() > $tsEnd) {
                    $fx['computedStatus'] = 'Completed';
                } elseif (time() >= $tsStart && time() <= $tsEnd) {
                    $fx['computedStatus'] = 'Live';
                } else {
                    $fx['computedStatus'] = 'Upcoming';
                }
            } else {
                $fx['computedStatus'] = 'Upcoming';
            }
        }
    }
    unset($fx);
    rsort($fixturesSeasons);

    // 8. Fetch MVP Players
    $mvpPlayers = [];
    $mvpJsonFile = __DIR__ . '/../uploads/mvp.json';
    if (file_exists($mvpJsonFile)) {
        $mvpPlayers = json_decode(file_get_contents($mvpJsonFile), true) ?? [];
    }
    if (empty($mvpPlayers) && $conn !== null) {
        $mRes = $conn->query("SELECT * FROM mvp_players ORDER BY season DESC, match_day ASC, match_number ASC");
        if ($mRes) {
            while ($mRow = $mRes->fetch_assoc()) {
                $mvpPlayers[] = [
                    'id'           => $mRow['mvp_id'],
                    'season'       => $mRow['season'],
                    'matchDay'     => $mRow['match_day'],
                    'matchNumber'  => $mRow['match_number'],
                    'playerName'   => $mRow['player_name'],
                    'teamName'     => $mRow['team_name'],
                    'teamLogo'     => $mRow['team_logo'],
                    'teamSlug'     => $mRow['team_slug'] ?? '',
                    'awardTitle'   => $mRow['award_title'],
                    'playerPhoto'  => $mRow['player_photo'] ?? '',
                    'jerseyNumber' => $mRow['jersey_number'] ?? '',
                    'position'     => $mRow['position'] ?? '',
                    'goals'        => $mRow['goals'] ?? '',
                    'rating'       => $mRow['rating'] ?? '',
                    'createdAt'    => $mRow['created_at']
                ];
            }
        }
    }

    $mvpSeasons = [];
    $mvpDays = [];
    foreach ($mvpPlayers as &$mp) {
        $sName = trim($mp['season'] ?? '');
        if (!empty($sName) && !in_array($sName, $mvpSeasons)) {
            $mvpSeasons[] = $sName;
        }
        $dName = trim($mp['matchDay'] ?? '');
        if (!empty($dName) && !in_array($dName, $mvpDays)) {
            $mvpDays[] = $dName;
        }
    }
    unset($mp);
    rsort($mvpSeasons);

    // 9. Fetch Winners & Runners-Up
    $winners = [];
    $winnersJsonFile = __DIR__ . '/../uploads/winners.json';
    if (file_exists($winnersJsonFile)) {
        $winners = json_decode(file_get_contents($winnersJsonFile), true) ?? [];
    }
    if (empty($winners) && $conn !== null) {
        $wRes = $conn->query("SELECT * FROM winners_runners ORDER BY display_order ASC, id DESC");
        if ($wRes) {
            while ($wRow = $wRes->fetch_assoc()) {
                $winners[] = [
                    'id'           => $wRow['slide_id'],
                    'category'     => $wRow['category'],
                    'season'       => $wRow['season'],
                    'title'        => $wRow['title'],
                    'teamName'     => $wRow['team_name'] ?? '',
                    'description'  => $wRow['description'] ?? '',
                    'imageUrl'     => $wRow['image_url'],
                    'status'       => $wRow['status'] ?? 'Active',
                    'displayOrder' => (int)($wRow['display_order'] ?? 0),
                    'createdAt'    => $wRow['created_at']
                ];
            }
        }
    }
    $winnerSeasons = [];
    foreach ($winners as $wn) {
        $ws = trim($wn['season'] ?? '');
        if (!empty($ws) && !in_array($ws, $winnerSeasons)) {
            $winnerSeasons[] = $ws;
        }
    }
    rsort($winnerSeasons);

    // 10. Fetch Franchise Teams
    require_once __DIR__ . '/../api/controllers/TeamController.php';
    $teamController = new TeamController($conn ?? null);
    $teams = $teamController->getAll('', false);
    $teamSeasons = $teamController->getSeasons();

    // 11. Fetch Latest News & Updates
    require_once __DIR__ . '/../api/controllers/NewsController.php';
    $newsController = new NewsController($conn ?? null);
    $newsList = $newsController->getAll(false);

    // 12. Fetch Our Partners & Sponsors
    require_once __DIR__ . '/../api/controllers/PartnerController.php';
    $partnerController = new PartnerController($conn ?? null);
    $partners = $partnerController->getAll(false);

    // 13. Fetch League Management Committee
    require_once __DIR__ . '/../api/controllers/CommitteeController.php';
    $committeeController = new CommitteeController($conn ?? null);
    $committeeMembers = $committeeController->getAll(false);

    // 14. Fetch Live Broadcast Partners & Season Videos
    require_once __DIR__ . '/../api/controllers/LivePartnerController.php';
    $livePartnerController = new LivePartnerController($conn ?? null);
    $livePartners = $livePartnerController->getAll(false, true)['partners'] ?? [];

    require_once __DIR__ . '/../api/controllers/LiveVideoController.php';
    $liveVideoController = new LiveVideoController($conn ?? null);
    $liveVideoRes = $liveVideoController->getAll('', '', false, true);
    $liveVideos = $liveVideoRes['videos'] ?? [];
    $liveVideoSeasons = $liveVideoRes['seasons'] ?? ['Season 1'];
    if (empty($liveVideoSeasons)) {
        $liveVideoSeasons = ['Season 1', 'Season 2'];
    }

    // 15. Fetch Marquee Announcements
    require_once __DIR__ . '/../api/controllers/AnnouncementController.php';
    $announcementController = new AnnouncementController($conn ?? null);
    $announcements = $announcementController->getAll(false);

    // Normalize arrays
    $players          = array_values($players);
    $messages         = array_values($messages);
    $banners          = array_values($banners);
    $galleryPhotos    = array_values($galleryPhotos);
    $standings        = array_values($standings);
    $fixtures         = array_values($fixtures);
    $mvpPlayers       = array_values($mvpPlayers);
    $winners          = array_values($winners);
    $teams            = array_values($teams);
    $newsList         = array_values($newsList);
    $partners         = array_values($partners);
    $committeeMembers = array_values($committeeMembers);
    $livePartners     = array_values($livePartners);
    $liveVideos       = array_values($liveVideos);
    $announcements    = array_values($announcements);

    // Calculate Stats
    $totalCount        = count($players);
    $verifiedCount     = count(array_filter($players, function($p) {
        if (!empty($p['paymentStatus']) && strtolower($p['paymentStatus']) === 'paid') return true;
        if (!empty($p['paymentId']) && trim($p['paymentId']) !== '') return true;
        if (($p['status'] ?? '') === 'Approved') return true;
        return false;
    }));
    $pendingCount      = $totalCount - $verifiedCount;
    $messageCount      = count($messages);
    $bannerCount       = count($banners);
    $pageBannerCount   = count($pageBanners);
    $galleryCount      = count($galleryPhotos);
    $standingsCount    = count($standings);
    $fixturesCount     = count($fixtures);
    $mvpCount          = count($mvpPlayers);
    $winnersCount      = count($winners);
    $teamCount         = count($teams);
    $newsCount         = count($newsList);
    $partnerCount      = count($partners);
    $committeeCount    = count($committeeMembers);
    $livePartnerCount  = count($livePartners);
    $liveVideoCount    = count($liveVideos);
    $announcementCount = count($announcements);

    $totalRevenue = 0;
    foreach ($players as $p) {
        $isPaid = false;
        if (!empty($p['paymentStatus']) && strtolower($p['paymentStatus']) === 'paid') {
            $isPaid = true;
        } elseif (!empty($p['paymentId']) && trim($p['paymentId']) !== '') {
            $isPaid = true;
        }
        
        if ($isPaid) {
            $amt = 0;
            if (!empty($p['amountPaid'])) {
                $cleanAmt = preg_replace('/[^0-9.]/', '', (string)$p['amountPaid']);
                $amt = (float)$cleanAmt;
            }
            if ($amt <= 0) {
                $amt = 1000;
            }
            $totalRevenue += $amt;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPPHL Admin Dashboard</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/images/upphl-logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/images/upphl-logo.png">
    <link rel="apple-touch-icon" href="../assets/images/upphl-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0a2a6b;
            --orange: #ff6a13;
            --green: #10b981;
            --purple: #8b5cf6;
            --cyan: #06b6d4;
            --red: #ef4444;
            --bg: #f1f5f9;
            --card: #ffffff;
            --sidebar-width: 260px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg); color: #0f172a; min-height: 100vh; }

        /* LOGIN SCREEN */
        .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #fff; width: 100%; max-width: 400px; padding: 35px 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); text-align: center; border: 1px solid #e2e8f0; }
        .login-card img { width: 80px; margin-bottom: 15px; }
        .login-card h2 { color: var(--primary); font-size: 22px; margin-bottom: 6px; }
        .login-card p { color: #64748b; font-size: 13.5px; margin-bottom: 25px; }
        .input-group { text-align: left; margin-bottom: 16px; }
        .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155; }
        .input-group input, .input-group textarea, .input-group select { width: 100%; padding: 12px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #fff; }
        .input-group input:focus, .input-group textarea:focus, .input-group select:focus { border-color: var(--primary); outline: none; }
        .login-btn { width: 100%; background: var(--primary); color: #fff; border: none; padding: 13px; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; transition: 0.2s; }
        .login-btn:hover { background: #071e4f; }
        .error-alert { background: #fef2f2; color: #991b1b; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 15px; border: 1px solid #fecaca; }

        /* ADMIN LAYOUT WITH SIDEBAR */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            color: #fff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) rgba(0, 0, 0, 0.15);
        }
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.15);
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        .sidebar-header {
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
            position: sticky;
            top: 0;
            background: var(--primary);
            z-index: 2;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-close-btn {
            display: none;
            background: rgba(255,255,255,0.1);
            border: none;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .sidebar-close-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        .sidebar-menu {
            list-style: none;
            padding: 16px 0;
            flex: 1;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 22px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
            border-left: 4px solid transparent;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            color: #fff;
            background: rgba(255,255,255,0.08);
            border-left-color: var(--orange);
        }
        .sidebar-menu li a .nav-badge {
            background: var(--orange);
            color: #fff;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 11px;
            margin-left: auto;
        }
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            bottom: 0;
            background: var(--primary);
            z-index: 2;
        }
        .logout-btn-side {
            width: 100%;
            background: rgba(239,68,68,0.15);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.3);
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.2s;
        }
        .logout-btn-side:hover {
            background: #ef4444;
            color: #fff;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        header.top-header {
            background: #fff;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        }
        .sidebar-toggle-btn {
            display: none;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: var(--primary);
            width: 38px;
            height: 38px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .sidebar-toggle-btn:hover {
            background: #e2e8f0;
        }
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }
        .page-title { font-size: 20px; font-weight: 700; color: #0f172a; }
        .content-body { padding: 30px; flex: 1; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 22px 20px; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; user-select: none; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-color: #cbd5e1; }
        .stat-icon { width: 54px; height: 54px; border-radius: 14px; display: grid; place-items: center; font-size: 22px; flex-shrink: 0; }
        .stat-card.total .stat-icon { background: #eff6ff; color: #2563eb; }
        .stat-card.revenue .stat-icon { background: #ecfdf5; color: #059669; }
        .stat-card.pending .stat-icon { background: #fff7ed; color: #ea580c; }
        .stat-card.messages .stat-icon { background: #f0fdf4; color: #16a34a; }
        .stat-info h3 { font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 3px; }
        .stat-info p { font-size: 13.5px; color: #64748b; font-weight: 600; margin: 0; }

        /* Dashboard Date Filter Header */
        .dashboard-filter-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; background: #ffffff; padding: 18px 24px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02); }
        .dashboard-title-group h2 { font-size: 20px; font-weight: 800; color: var(--primary); margin-bottom: 3px; display: flex; align-items: center; gap: 10px; }
        .dashboard-title-group p { color: #64748b; font-size: 13.5px; margin: 0; }
        .dashboard-filter-controls { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .filter-group-label { font-size: 13px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 6px; }
        .filter-pills-group { display: inline-flex; background: #f1f5f9; padding: 4px; border-radius: 10px; gap: 4px; border: 1px solid #e2e8f0; }
        .dash-filter-pill { background: transparent; border: none; padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; color: #64748b; cursor: pointer; transition: all 0.2s ease; }
        .dash-filter-pill:hover { color: var(--primary); }
        .dash-filter-pill.active { background: #ffffff; color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

        .section-panel { display: none; }
        .section-panel.active { display: block; }

        .table-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: visible; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .table-head { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; gap: 15px; }
        .table-head h2 { font-size: 18px; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .export-btn { background: #10b981; color: #fff; text-decoration: none; padding: 9px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: 0.2s; }
        .export-btn:hover { opacity: 0.9; }

        table.admin-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
        table.admin-table th { background: #f8fafc; padding: 14px 18px; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
        table.admin-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .p-avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; }
        .badge-status.Pending { background: #ffedd5; color: #c2410c; }
        .badge-status.Approved, .badge-status.Active { background: #d1fae5; color: #047857; }
        .badge-status.Rejected, .badge-status.Inactive { background: #fee2e2; color: #b91c1c; }

        .btn-act { padding: 7px 14px; border-radius: 6px; border: none; font-weight: 700; font-size: 12px; cursor: pointer; transition: 0.15s; margin-right: 4px; display: inline-flex; align-items: center; gap: 5px; }
        .btn-act.approve { background: #10b981; color: #fff; }
        .btn-act.approve:hover { background: #059669; }
        .btn-act.reject { background: #ef4444; color: #fff; }
        .btn-act.reject:hover { background: #dc2626; }
        .btn-act.edit-btn { background: #2563eb; color: #fff; }
        .btn-act.edit-btn:hover { background: #1d4ed8; }

        /* Media Thumbnail Styling */
        .banner-media-thumb { width: 80px; height: 50px; border-radius: 8px; object-fit: cover; border: 2px solid #e2e8f0; background: #000; }
        .gallery-media-thumb { width: 65px; height: 50px; border-radius: 8px; object-fit: cover; border: 1.5px solid #cbd5e1; }
        .media-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; }
        .media-badge.image { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .media-badge.video { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
        .season-badge { display: inline-block; background: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11.5px; border: 1px solid #fde68a; }
        .category-badge { display: inline-block; background: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 6px; font-weight: 600; font-size: 11.5px; }

        /* 3-Dots Action Dropdown Menu */
        .action-dropdown-wrapper { position: relative; display: inline-block; }
        .three-dots-btn { background: #f1f5f9; border: 1px solid #cbd5e1; width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #475569; font-size: 15px; transition: 0.2s; }
        .three-dots-btn:hover { background: #e2e8f0; color: #0f172a; }
        .action-dropdown-menu { display: none; position: absolute; right: 0; top: 40px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); width: 180px; z-index: 9999; text-align: left; padding: 6px 0; overflow: hidden; }
        .action-dropdown-menu.show { display: block; animation: fadeIn 0.15s ease-out; }
        .action-dropdown-menu a { display: flex; align-items: center; gap: 10px; padding: 10px 16px; color: #334155; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.15s; }
        .action-dropdown-menu a:hover { background: #f8fafc; color: var(--primary); }

        /* Generic Modals */
        .admin-modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
        .admin-modal-backdrop.show { display: flex; }
        .admin-modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 760px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; }
        .admin-modal-head { padding: 20px 24px; background: #0f172a; color: #fff; display: flex; justify-content: space-between; align-items: center; border-top-left-radius: 16px; border-top-right-radius: 16px; position: sticky; top: 0; z-index: 10; }
        .admin-modal-head h3 { font-size: 18px; margin: 0; color: #fff; display: flex; align-items: center; gap: 10px; }
        .admin-modal-close { background: rgba(255,255,255,0.1); border: none; color: #fff; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 16px; display: inline-flex; align-items: center; justify-content: center; }
        .admin-modal-close:hover { background: rgba(255,255,255,0.2); }
        .admin-modal-body { padding: 24px; }
        .modal-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .modal-info-box { background: #f8fafc; padding: 12px 16px; border-radius: 10px; border: 1px solid #e2e8f0; }
        .modal-info-box label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px; }
        .modal-info-box span { font-size: 14px; font-weight: 600; color: #1e293b; word-break: break-word; }

        /* Media Upload Dropzone */
        .upload-dropzone { border: 2px dashed #cbd5e1; background: #f8fafc; border-radius: 12px; padding: 25px 20px; text-align: center; cursor: pointer; transition: 0.2s; position: relative; margin-bottom: 18px; }
        .upload-dropzone:hover, .upload-dropzone.dragover { border-color: var(--primary); background: #f0fdf4; }
        .upload-dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .upload-preview-box { margin-top: 14px; border-radius: 10px; overflow: hidden; background: #0f172a; max-height: 220px; display: flex; align-items: center; justify-content: center; position: relative; }
        .upload-preview-box img, .upload-preview-box video { max-width: 100%; max-height: 220px; object-fit: contain; }

        /* Settings Card Box */
        .settings-form-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); max-width: 850px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

        /* Pagination Button Styles */
        .admin-pagin-btn { background: #f8fafc; border: 1.5px solid #cbd5e1; color: #334155; padding: 6px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; transition: 0.15s; display: inline-flex; align-items: center; gap: 5px; }
        .admin-pagin-btn:hover { background: #e2e8f0; color: #0f172a; }
        .admin-pagin-btn.active { background: var(--primary); color: #ffffff; border-color: var(--primary); }
        .admin-pagin-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        @media (max-width: 991px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                height: 100vh;
                z-index: 1050;
                transform: translateX(-100%);
                box-shadow: 0 0 25px rgba(0,0,0,0.25);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .sidebar-close-btn {
                display: inline-flex;
            }
            .sidebar-toggle-btn {
                display: inline-flex;
            }
            header.top-header {
                padding: 14px 20px;
            }
            .content-body {
                padding: 20px;
            }
            .modal-grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 576px) {
            .stats-grid { grid-template-columns: 1fr; }
            .top-header { padding: 12px 16px; }
            .content-body { padding: 15px; }
        }
    </style>
</head>
<body>

<?php if (!$isLogged): ?>
    <!-- ADMIN LOGIN -->
    <div class="login-wrap">
        <div class="login-card">
            <img src="../assets/images/upphl-logo.png" alt="UPPHL Logo" onerror="this.style.display='none'">
            <h2>UPPHL Admin Login</h2>
            <p>Enter credentials to access admin portal</p>
            
            <?php if ($errorMsg): ?>
                <div class="error-alert"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="admin" value="Arnavdigitalfoundation@gmail.com">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••" value="Arnav#Digital26!admin">
                </div>
                <button type="submit" name="login_submit" class="login-btn">
                    <i class="fa-solid fa-lock"></i> Login to Dashboard
                </button>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- ADMIN DASHBOARD LAYOUT -->
    <div class="admin-wrapper">
        <!-- SIDEBAR BACKDROP (Mobile) -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

        <!-- SIDEBAR MENU -->
        <aside class="sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fa-solid fa-shield-halved" style="color: var(--orange);"></i>
                    UPPHL Admin
                </div>
                <button type="button" class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="Close Sidebar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li class="active" id="navItemDashboard">
                    <a href="#" onclick="switchTab('dashboard', event)">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                </li>
                <li id="navItemPlayers">
                    <a href="#" onclick="switchTab('players', event)">
                        <i class="fa-solid fa-users"></i> Player Registrations
                        <span class="nav-badge"><?= $totalCount ?></span>
                    </a>
                </li>
                <li id="navItemTeams">
                    <a href="#" onclick="switchTab('teams', event)">
                        <i class="fa-solid fa-shield-halved"></i> Franchise Teams
                        <span class="nav-badge" style="background: #ea580c;" id="sideTeamsBadge"><?= $teamCount ?></span>
                    </a>
                </li>
                <li id="navItemStandings">
                    <a href="#" onclick="switchTab('standings', event)">
                        <i class="fa-solid fa-table-list"></i> Points Table
                        <span class="nav-badge" style="background: #eab308;" id="sideStandingsBadge"><?= $standingsCount ?></span>
                    </a>
                </li>
                <li id="navItemFixtures">
                    <a href="#" onclick="switchTab('fixtures', event)">
                        <i class="fa-solid fa-calendar-days"></i> Match Fixtures
                        <span class="nav-badge" style="background: #3b82f6;" id="sideFixturesBadge"><?= $fixturesCount ?></span>
                    </a>
                </li>
                <li id="navItemMvp">
                    <a href="#" onclick="switchTab('mvp', event)">
                        <i class="fa-solid fa-award"></i> MVP Players
                        <span class="nav-badge" style="background: #f59e0b;" id="sideMvpBadge"><?= $mvpCount ?></span>
                    </a>
                </li>
                <li id="navItemWinners">
                    <a href="#" onclick="switchTab('winners', event)">
                        <i class="fa-solid fa-trophy"></i> Winners &amp; Runners
                        <span class="nav-badge" style="background: #eab308;" id="sideWinnersBadge"><?= $winnersCount ?></span>
                    </a>
                </li>
                <li id="navItemBanners">
                    <a href="#" onclick="switchTab('banners', event)">
                        <i class="fa-solid fa-panorama"></i> Hero Banners
                        <span class="nav-badge" style="background: var(--purple);" id="sideBannerBadge"><?= $bannerCount ?></span>
                    </a>
                </li>
                <li id="navItemPageBanners">
                    <a href="#" onclick="switchTab('page-banners', event)">
                        <i class="fa-solid fa-images"></i> Page Banners
                        <span class="nav-badge" style="background: #0ea5e9;" id="sidePageBannerBadge"><?= $pageBannerCount ?></span>
                    </a>
                </li>
                <li id="navItemAnnouncements">
                    <a href="#" onclick="switchTab('announcements', event)">
                        <i class="fa-solid fa-bullhorn"></i> Announcement Strip
                        <span class="nav-badge" style="background: #f97316;" id="sideAnnouncementBadge"><?= $announcementCount ?></span>
                    </a>
                </li>
                <li id="navItemGallery">
                    <a href="#" onclick="switchTab('gallery', event)">
                        <i class="fa-solid fa-camera-retro"></i> Gallery Photos
                        <span class="nav-badge" style="background: var(--cyan);" id="sideGalleryBadge"><?= $galleryCount ?></span>
                    </a>
                </li>
                <li id="navItemNews">
                    <a href="#" onclick="switchTab('news', event)">
                        <i class="fa-solid fa-newspaper"></i> Latest News &amp; Updates
                        <span class="nav-badge" style="background: #e11d48;" id="sideNewsBadge"><?= $newsCount ?></span>
                    </a>
                </li>
                <li id="navItemPartners">
                    <a href="#" onclick="switchTab('partners', event)">
                        <i class="fa-solid fa-handshake"></i> Partners &amp; Sponsors
                        <span class="nav-badge" style="background: #059669;" id="sidePartnerBadge"><?= $partnerCount ?></span>
                    </a>
                </li>
                <li id="navItemCommittee">
                    <a href="#" onclick="switchTab('committee', event)">
                        <i class="fa-solid fa-users-gear"></i> Management Committee
                        <span class="nav-badge" style="background: #6366f1;" id="sideCommitteeBadge"><?= $committeeCount ?></span>
                    </a>
                </li>
                <li id="navItemLiveHub">
                    <a href="#" onclick="switchTab('live-hub', event)">
                        <i class="fa-solid fa-tower-broadcast"></i> Live &amp; Video Hub
                        <span class="nav-badge" style="background: #dc2626;" id="sideLiveHubBadge"><?= $livePartnerCount + $liveVideoCount ?></span>
                    </a>
                </li>
                <li id="navItemMessages">
                    <a href="#" onclick="switchTab('messages', event)">
                        <i class="fa-solid fa-envelope"></i> Contact Messages
                        <span class="nav-badge" style="background: #10b981;"><?= $messageCount ?></span>
                    </a>
                </li>
                <li id="navItemHomeStats">
                    <a href="#" onclick="switchTab('home-stats', event)">
                        <i class="fa-solid fa-chart-simple"></i> Home Stats Counter
                        <span class="nav-badge" style="background: #0ea5e9;">4</span>
                    </a>
                </li>
                <li id="navItemSettings">
                    <a href="#" onclick="switchTab('contact-settings', event)">
                        <i class="fa-solid fa-sliders"></i> Contact Settings
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="?action=logout" class="logout-btn-side">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="main-content">
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title" id="headerTitle">Dashboard Overview</h1>
                </div>
                <span style="font-weight: 600; font-size: 13.5px; color: #64748b;">
                    <i class="fa-solid fa-circle" style="color: #10b981; font-size: 10px;"></i> Auto-Sync Active
                </span>
            </header>

            <div class="content-body">
                <!-- SECTION 0: DASHBOARD (STAT CARDS WITH AUTO-CALCULATION & DATE FILTERS) -->
                <div class="section-panel active" id="sectionDashboard">
                    <div class="dashboard-filter-header">
                        <div class="dashboard-title-group">
                            <h2>
                                <i class="fa-solid fa-chart-pie" style="color: var(--orange);"></i> Key Performance &amp; Statistics
                            </h2>
                            <p>Real-time auto-calculated metrics across players, payments, and contact inquiries.</p>
                        </div>
                        <div class="dashboard-filter-controls">
                            <span class="filter-group-label"><i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i> Time Period:</span>
                            <div class="filter-pills-group">
                                <button type="button" class="dash-filter-pill" data-period="today" onclick="setDashboardPeriod('today')">Today</button>
                                <button type="button" class="dash-filter-pill" data-period="7days" onclick="setDashboardPeriod('7days')">Last 7 Days</button>
                                <button type="button" class="dash-filter-pill" data-period="month" onclick="setDashboardPeriod('month')">This Month</button>
                                <button type="button" class="dash-filter-pill active" data-period="all" onclick="setDashboardPeriod('all')">All Time</button>
                            </div>
                        </div>
                    </div>

                    <!-- STATS GRID: 4 TARGETED METRICS -->
                    <div class="stats-grid">
                        <div class="stat-card total" onclick="switchTab('players', event)" style="cursor: pointer;" title="Manage Players">
                            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="stat-info">
                                <h3 id="statTotal"><?= $totalCount ?></h3>
                                <p id="lblTotalPlayers">Total Players</p>
                            </div>
                        </div>
                        <div class="stat-card revenue" onclick="switchTab('players', event)" style="cursor: pointer;" title="View Payment Details">
                            <div class="stat-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                            <div class="stat-info">
                                <h3 id="statRevenue">₹<?= number_format($totalRevenue) ?></h3>
                                <p id="lblTotalRevenue">Total Payments</p>
                            </div>
                        </div>
                        <div class="stat-card pending" onclick="switchTab('players', event)" style="cursor: pointer; background: #f0fdf4; border-color: #bbf7d0;" title="Verified Paid Players">
                            <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i class="fa-solid fa-circle-check"></i></div>
                            <div class="stat-info">
                                <h3 id="statVerified" style="color: #15803d;"><?= $verifiedCount ?></h3>
                                <p id="lblVerifiedPlayers" style="color: #166534;">Verified (Paid)</p>
                            </div>
                        </div>
                        <div class="stat-card messages" onclick="switchTab('messages', event)" style="cursor: pointer;" title="View Contact Messages">
                            <div class="stat-icon"><i class="fa-solid fa-comments"></i></div>
                            <div class="stat-info">
                                <h3 id="statMessages"><?= $messageCount ?></h3>
                                <p id="lblTotalMessages">Contact Messages</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1: PLAYER REGISTRATIONS -->
                <div class="section-panel" id="sectionPlayers">
                    <div class="table-card">
                        <div class="table-head" style="gap: 15px;">
                            <div>
                                <h2><i class="fa-solid fa-users-viewfinder" style="color: var(--primary);"></i> Registered Players List</h2>
                                <span id="playerTotalMatchPill" style="font-size: 12px; font-weight: 700; color: #64748b; background: #f1f5f9; padding: 3px 10px; border-radius: 20px; border: 1px solid #e2e8f0; margin-top: 4px; display: inline-block;">
                                    Total: <?= $totalCount ?> Players
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <a href="../api/export-csv.php" class="export-btn">
                                    <i class="fa-solid fa-file-excel"></i> Export Approved CSV (WP Import)
                                </a>
                            </div>
                        </div>

                        <!-- LIVE SEARCH & FILTER TOOLBAR -->
                        <div style="padding: 14px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex: 1; min-width: 280px;">
                                <div style="position: relative; flex: 1; min-width: 240px; max-width: 460px;">
                                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                                    <input type="text" id="adminPlayerSearchInput" oninput="handlePlayerSearchChange()" placeholder="Search by Player ID, Name, Mobile, District, Position..." style="width: 100%; padding: 8px 32px 8px 34px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; background: #fff; outline: none;">
                                    <button type="button" id="adminPlayerSearchClearBtn" onclick="clearPlayerSearch()" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 13px;" title="Clear Search">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>

                                <select id="adminPlayerStatusFilter" onchange="handlePlayerSearchChange()" style="padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Payment Statuses</option>
                                    <option value="paid">Verified (Paid)</option>
                                    <option value="pending">Pending / Unpaid</option>
                                </select>

                                <select id="adminPlayerGenderFilter" onchange="handlePlayerSearchChange()" style="padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Genders</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label style="font-size: 13px; font-weight: 600; color: #64748b;">Per Page:</label>
                                <select id="adminPlayerPageSize" onchange="changePlayerPageSize(this.value)" style="padding: 7px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 13px; color: #0f172a; background: #fff;">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Credentials (ID / Password)</th>
                                    <th>Full Name &amp; Contact</th>
                                    <th>Position &amp; District</th>
                                    <th>Document</th>
                                    <th>Bank Payment &amp; UTR Verification</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="playersTbody">
                                <?php if (empty($players)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">
                                            No player registrations submitted yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($players as $p): 
                                        $isPaid = (!empty($p['paymentStatus']) && strtolower($p['paymentStatus']) === 'paid') 
                                               || (!empty($p['paymentId']) && trim($p['paymentId']) !== '')
                                               || (($p['status'] ?? '') === 'Approved');
                                    ?>
                                        <tr id="row-<?= htmlspecialchars($p['playerId']) ?>">
                                            <td>
                                                <img src="../<?= htmlspecialchars($p['photoUrl'] ?: 'assets/images/default-player.png') ?>" class="p-avatar" alt="Photo">
                                            </td>
                                            <td>
                                                <strong style="color: var(--primary); font-size: 13.5px;"><?= htmlspecialchars($p['playerId']) ?></strong><br>
                                                <small style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700; color: #475569; font-family: monospace;">
                                                    <i class="fa-solid fa-key" style="color: var(--orange); font-size: 10px;"></i> <?= htmlspecialchars($p['password'] ?: 'Auto-Gen') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong style="color: #1e293b;"><?= htmlspecialchars($p['fullName']) ?></strong><br>
                                                <small style="color: #64748b;"><?= htmlspecialchars($p['gender']) ?> · <?= htmlspecialchars($p['age']) ?></small><br>
                                                <small style="color: #2563eb;"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($p['mobile']) ?></small>
                                            </td>
                                            <td>
                                                <strong style="color: #334155;"><?= htmlspecialchars($p['primaryPos']) ?></strong><br>
                                                <small style="color: #64748b;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['district']) ?></small>
                                            </td>
                                            <td>
                                                 <div style="display: flex; flex-direction: column; gap: 4px;">
                                                     <?php if (!empty($p['aadhaarFrontUrl'])): ?>
                                                         <a href="../<?= htmlspecialchars($p['aadhaarFrontUrl']) ?>" target="_blank" style="background: #eff6ff; color: #2563eb; padding: 2px 6px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 700; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 4px;">
                                                             <i class="fa-solid fa-id-card"></i> Aadhaar Front
                                                         </a>
                                                     <?php endif; ?>

                                                     <?php if (!empty($p['aadhaarBackUrl'])): ?>
                                                         <a href="../<?= htmlspecialchars($p['aadhaarBackUrl']) ?>" target="_blank" style="background: #f0fdf4; color: #16a34a; padding: 2px 6px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 700; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                                             <i class="fa-solid fa-id-card"></i> Aadhaar Back
                                                         </a>
                                                     <?php endif; ?>

                                                     <?php if (!empty($p['certUrls']) && is_array($p['certUrls'])): ?>
                                                         <?php foreach ($p['certUrls'] as $cIdx => $cUrl): ?>
                                                             <a href="../<?= htmlspecialchars($cUrl) ?>" target="_blank" style="background: #fff7ed; color: #ea580c; padding: 2px 6px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 700; border: 1px solid #ffedd5; display: inline-flex; align-items: center; gap: 4px;">
                                                                 <i class="fa-solid fa-award"></i> Cert #<?= ($cIdx + 1) ?>
                                                             </a>
                                                         <?php endforeach; ?>
                                                     <?php endif; ?>

                                                     <?php if (empty($p['aadhaarFrontUrl']) && empty($p['aadhaarBackUrl']) && empty($p['docUrl']) && (empty($p['certUrls']) || !is_array($p['certUrls']))): ?>
                                                         <span style="color: #94a3b8; font-size: 11px;">No Documents</span>
                                                     <?php endif; ?>
                                                 </div>
                                            </td>
                                            <td>
                                                <?php if ($isPaid): ?>
                                                    <span style="color: #15803d; font-weight: 800; font-size: 11.5px; background: #dcfce7; padding: 3px 8px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa-solid fa-circle-check"></i> Verified (Paid)
                                                    </span>
                                                    <div style="margin-top: 4px; font-size: 12px; font-weight: 700; color: #0f172a; font-family: monospace;">
                                                        <i class="fa-solid fa-receipt" style="color: var(--orange);"></i> UTR: <?= htmlspecialchars($p['paymentId'] ?: 'N/A') ?>
                                                    </div>
                                                    <small style="color: #64748b; font-size: 11px;">
                                                        <?= htmlspecialchars($p['paymentMethod'] ?? 'UPI') ?> · <?= htmlspecialchars($p['amountPaid'] ?? '₹1,000') ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span style="color: #b91c1c; font-weight: 700; font-size: 11.5px; background: #fee2e2; padding: 3px 8px; border-radius: 6px; border: 1px solid #fecaca; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fa-solid fa-clock"></i> Payment Unpaid / Pending
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center; position: relative;">
                                                 <div class="action-dropdown-wrapper">
                                                     <button type="button" class="three-dots-btn" onclick="toggleActionMenu('menu-<?= htmlspecialchars($p['playerId']) ?>', event)">
                                                         <i class="fa-solid fa-ellipsis-vertical"></i>
                                                     </button>
                                                     <div class="action-dropdown-menu" id="menu-<?= htmlspecialchars($p['playerId']) ?>">
                                                         <a href="#" onclick="viewFullPlayerDetails(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>, event)">
                                                             <i class="fa-solid fa-eye" style="color: #2563eb;"></i> View Application Details
                                                         </a>
                                                         <a href="#" onclick="deletePlayerRow('<?= htmlspecialchars($p['playerId']) ?>', event)">
                                                             <i class="fa-solid fa-trash" style="color: #dc2626;"></i> Delete Registration
                                                         </a>
                                                     </div>
                                                 </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- 10-PER-PAGE AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="playersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="adminPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 players</span>
                            <div id="adminPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: FRANCHISE TEAMS MANAGEMENT -->
                <div class="section-panel" id="sectionTeams">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <h2><i class="fa-solid fa-shield-halved" style="color: #ea580c;"></i> Franchise Teams</h2>
                                <select id="adminTeamsSeasonFilter" onchange="filterAdminTeams()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <?php foreach ($teamSeasons as $sName): ?>
                                        <option value="<?= htmlspecialchars($sName) ?>"><?= htmlspecialchars($sName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" id="adminTeamSearchInput" onkeyup="filterAdminTeams()" placeholder="Search team, coach, city..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 220px; background: #fff;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openTeamModal()">
                                <i class="fa-solid fa-plus"></i> Add Franchise Team
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">Order</th>
                                    <th>Footer Round Logo</th>
                                    <th>Poster Banner (Home/Teams)</th>
                                    <th>Team Details</th>
                                    <th>Season</th>
                                    <th>Leadership &amp; Coaches</th>
                                    <th style="text-align: center;">Squad</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="teamsTbody">
                                <?php if (empty($teams)): ?>
                                    <tr id="noTeamsRow">
                                        <td colspan="9" style="text-align: center; padding: 35px; color: #94a3b8;">
                                            <i class="fa-solid fa-shield-halved" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No franchise teams added yet. Click <strong>"Add Franchise Team"</strong> above to register teams.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($teams as $tm): 
                                        $tmId = htmlspecialchars($tm['id']);
                                        $tmName = htmlspecialchars($tm['name']);
                                        $tmSeason = htmlspecialchars($tm['season'] ?? 'Season 1');
                                        $tmCity = htmlspecialchars($tm['city'] ?? '');
                                        $tmFooterLogo = !empty($tm['footerLogo']) ? htmlspecialchars($tm['footerLogo']) : (!empty($tm['logo']) ? htmlspecialchars($tm['logo']) : 'assets/images/teams/bhadohi-logo.jpeg');
                                        $tmPoster = !empty($tm['posterImage']) ? htmlspecialchars($tm['posterImage']) : $tmFooterLogo;
                                        $tmCoach = $tm['coach']['name'] ?? 'Not Assigned';
                                        $tmOwner = $tm['owner']['name'] ?? '';
                                        $tmCaptain = $tm['captain']['name'] ?? '';
                                        $tmPlayersCount = count($tm['players'] ?? []);
                                        $tmOrder = (int)($tm['sortOrder'] ?? 0);
                                        $tmStatus = $tm['status'] ?? 'Active';
                                        $isActive = ($tmStatus === 'Active');
                                    ?>
                                        <tr id="team-row-<?= $tmId ?>" data-season="<?= $tmSeason ?>" data-search="<?= strtolower($tmName . ' ' . $tmCity . ' ' . $tmCoach . ' ' . $tmSeason) ?>">
                                            <td style="text-align: center; font-weight: 800; color: #64748b;"><?= $tmOrder ?></td>
                                            <td>
                                                <div style="width: 44px; height: 44px; border-radius: 50%; border: 2px solid #e2e8f0; overflow: hidden; background: #0f172a; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.06);" title="Footer Round Logo">
                                                    <img src="../<?= $tmFooterLogo ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?= $tmName ?> Footer Logo" onerror="this.src='../assets/images/teams/bhadohi-logo.jpeg'">
                                                </div>
                                            </td>
                                            <td>
                                                <div style="width: 64px; height: 42px; border-radius: 6px; border: 1px solid #cbd5e1; overflow: hidden; background: #1e293b;" title="Poster Banner (Home/Teams)">
                                                    <img src="../<?= $tmPoster ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?= $tmName ?> Poster" onerror="this.src='../<?= $tmFooterLogo ?>'">
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong style="color: #0f172a; font-size: 14.5px; display: block;"><?= $tmName ?></strong>
                                                    <span style="font-size: 12px; color: #64748b;">
                                                        <i class="fa-solid fa-location-dot" style="color: #ea580c; font-size: 11px;"></i> <?= $tmCity ?: 'Uttar Pradesh' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="season-badge"><i class="fa-solid fa-trophy"></i> <?= $tmSeason ?></span>
                                            </td>
                                            <td>
                                                <div style="font-size: 12.5px; line-height: 1.4;">
                                                    <div><strong>Coach:</strong> <span style="color: #0284c7;"><?= htmlspecialchars($tmCoach) ?></span></div>
                                                    <?php if (!empty($tmCaptain)): ?>
                                                        <div><strong>Captain:</strong> <span style="color: #16a34a;"><?= htmlspecialchars($tmCaptain) ?></span></div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($tmOwner)): ?>
                                                        <div style="color: #64748b; font-size: 11.5px;">Owner: <?= htmlspecialchars($tmOwner) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; padding: 3px 8px; border-radius: 20px; font-weight: 800; font-size: 12px; background: #e0f2fe; color: #0284c7;">
                                                    <i class="fa-solid fa-users" style="font-size: 10px;"></i> <?= $tmPlayersCount ?> Players
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <button type="button" onclick="toggleTeamStatus('<?= $tmId ?>')" style="border: none; background: transparent; cursor: pointer;" title="Click to toggle status">
                                                    <span id="team-status-badge-<?= $tmId ?>" class="status-badge <?= $isActive ? 'status-approved' : 'status-rejected' ?>" style="cursor: pointer;">
                                                        <i class="fa-solid <?= $isActive ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i> <?= $tmStatus ?>
                                                    </span>
                                                </button>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="display: flex; gap: 6px; justify-content: center;">
                                                    <button type="button" class="btn-act btn-view" title="Edit Team" onclick='editTeam(<?= json_encode($tm, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)'>
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button type="button" class="btn-act btn-delete" title="Delete Team" onclick="deleteTeam('<?= $tmId ?>', '<?= htmlspecialchars($tmName, ENT_QUOTES) ?>')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- TEAMS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="teamsPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="teamsPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 teams</span>
                            <div id="teamsPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: POINTS TABLE / STANDINGS MANAGEMENT -->
                <div class="section-panel" id="sectionStandings">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <h2><i class="fa-solid fa-table-list" style="color: #eab308;"></i> Points Table Standings</h2>
                                <select id="adminStandingsSeasonFilter" onchange="filterAdminStandings(this.value)" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <?php foreach ($standingsSeasons as $sName): ?>
                                        <option value="<?= htmlspecialchars($sName) ?>"><?= htmlspecialchars($sName) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" id="adminStandingsSearchInput" onkeyup="filterAdminStandings()" placeholder="Search team in standings..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px; background: #fff;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openStandingsModal()">
                                <i class="fa-solid fa-plus"></i> Add Team Standings
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Season</th>
                                    <th>Rank / Team</th>
                                    <th style="text-align: center;">P</th>
                                    <th style="text-align: center;">W</th>
                                    <th style="text-align: center;">D</th>
                                    <th style="text-align: center;">L</th>
                                    <th style="text-align: center;">GF</th>
                                    <th style="text-align: center;">GA</th>
                                    <th style="text-align: center;">GD</th>
                                    <th style="text-align: center; color: var(--orange);">PTS</th>
                                    <th>Recent Form</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="standingsTbody">
                                <?php if (empty($standings)): ?>
                                    <tr id="noStandingsRow">
                                        <td colspan="12" style="text-align: center; padding: 30px; color: #94a3b8;">
                                            No team standings added yet. Click <strong>"Add Team Standings"</strong> above to add teams.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($standings as $idx => $st): ?>
                                        <tr id="standing-row-<?= htmlspecialchars($st['id']) ?>" data-season="<?= htmlspecialchars($st['season']) ?>">
                                            <td>
                                                 <span class="season-badge"><i class="fa-solid fa-trophy"></i> <?= htmlspecialchars($st['season']) ?></span>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <img src="../<?= htmlspecialchars($st['teamLogo'] ?: 'assets/images/teams/bhadohi-logo.jpeg') ?>" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 1.5px solid #e2e8f0;" alt="Logo">
                                                    <strong style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($st['teamName']) ?></strong>
                                                </div>
                                            </td>
                                            <td style="text-align: center; font-weight: 700;"><?= $st['played'] ?></td>
                                            <td style="text-align: center; color: #16a34a; font-weight: 700;"><?= $st['won'] ?></td>
                                            <td style="text-align: center; color: #d97706; font-weight: 700;"><?= $st['draw'] ?></td>
                                            <td style="text-align: center; color: #dc2626; font-weight: 700;"><?= $st['lost'] ?></td>
                                            <td style="text-align: center; color: #475569;"><?= $st['gf'] ?></td>
                                            <td style="text-align: center; color: #475569;"><?= $st['ga'] ?></td>
                                            <td style="text-align: center; font-weight: 700; color: <?= $st['gd'] >= 0 ? '#16a34a' : '#dc2626' ?>;">
                                                <?= $st['gd'] > 0 ? '+' . $st['gd'] : $st['gd'] ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="background: #fef08a; color: #854d0e; font-weight: 800; padding: 4px 10px; border-radius: 6px; font-size: 13.5px; border: 1px solid #fde047;">
                                                    <?= $st['pts'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 3px;">
                                                    <?php 
                                                    $fList = explode(',', $st['form'] ?? '');
                                                    foreach ($fList as $fChar): 
                                                        $fChar = strtoupper(trim($fChar));
                                                        $fBg = $fChar === 'W' ? '#16a34a' : ($fChar === 'D' ? '#d97706' : '#dc2626');
                                                    ?>
                                                        <span style="display: inline-block; width: 18px; height: 18px; line-height: 18px; text-align: center; border-radius: 3px; background: <?= $fBg ?>; color: #fff; font-size: 10px; font-weight: 800;"><?= $fChar ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editStandingsRow(<?= htmlspecialchars(json_encode($st), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Team">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteStandingsRow('<?= htmlspecialchars($st['id']) ?>')" title="Delete Team">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- STANDINGS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="standingsPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="standingsPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 standings</span>
                            <div id="standingsPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: MATCH FIXTURES MANAGEMENT -->
                <div class="section-panel" id="sectionFixtures">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <h2><i class="fa-solid fa-calendar-days" style="color: #3b82f6;"></i> Match Fixtures & Schedule</h2>
                                
                                <select id="adminFixturesSeasonFilter" onchange="filterAdminFixtures()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <?php foreach ($fixturesSeasons as $fSName): ?>
                                        <option value="<?= htmlspecialchars($fSName) ?>"><?= htmlspecialchars($fSName) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select id="adminFixturesStatusFilter" onchange="filterAdminFixtures()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Statuses</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="live">Live</option>
                                    <option value="completed">Completed</option>
                                </select>

                                <input type="text" id="adminFixturesSearchInput" onkeyup="filterAdminFixtures()" placeholder="Search team, date, stadium..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px; background: #fff;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openFixtureModal()">
                                <i class="fa-solid fa-plus"></i> Add Match Fixture
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Season / Match</th>
                                    <th>Match Clash (Team 1 vs Team 2)</th>
                                    <th>Date & Time</th>
                                    <th>Stadium / Venue</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Score</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="fixturesTbody">
                                <?php if (empty($fixtures)): ?>
                                    <tr id="noFixturesRow">
                                        <td colspan="7" style="text-align: center; padding: 35px; color: #94a3b8;">
                                            <i class="fa-regular fa-calendar-xmark" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No match fixtures scheduled yet. Click <strong>"Add Match Fixture"</strong> above to schedule matches.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($fixtures as $f): 
                                        $cStatus = $f['computedStatus'] ?? 'Upcoming';
                                        $badgeBg = $cStatus === 'Upcoming' ? '#eff6ff' : ($cStatus === 'Live' ? '#fef2f2' : '#f1f5f9');
                                        $badgeColor = $cStatus === 'Upcoming' ? '#2563eb' : ($cStatus === 'Live' ? '#dc2626' : '#475569');
                                        $badgeBorder = $cStatus === 'Upcoming' ? '#bfdbfe' : ($cStatus === 'Live' ? '#fecaca' : '#cbd5e1');
                                    ?>
                                        <tr id="fixture-row-<?= htmlspecialchars($f['id']) ?>" data-season="<?= htmlspecialchars($f['season']) ?>" data-status="<?= htmlspecialchars(strtolower($cStatus)) ?>">
                                            <td>
                                                <div style="display: flex; gap: 5px; margin-bottom: 4px; flex-wrap: wrap;">
                                                    <span class="season-badge"><i class="fa-solid fa-trophy"></i> <?= htmlspecialchars($f['season']) ?></span>
                                                    <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 4px;"><?= htmlspecialchars($f['matchDay'] ?? 'Day 1') ?></span>
                                                    <span style="background: #fef3c7; color: #b45309; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 4px;"><?= htmlspecialchars($f['matchNumber'] ?? 'Match 1') ?></span>
                                                </div>
                                                <strong style="color: #0f172a; font-size: 13.5px;"><?= htmlspecialchars($f['matchTitle'] ?? 'League Stage') ?></strong>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                                    <div style="display: flex; align-items: center; gap: 6px;">
                                                        <img src="../<?= htmlspecialchars($f['team1Logo'] ?: 'assets/images/teams/bhadohi-logo.jpeg') ?>" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;" alt="Logo">
                                                        <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?= htmlspecialchars($f['team1Name']) ?></span>
                                                    </div>
                                                    <span style="background: #f97316; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px;">VS</span>
                                                    <div style="display: flex; align-items: center; gap: 6px;">
                                                        <img src="../<?= htmlspecialchars($f['team2Logo'] ?: 'assets/images/teams/ghaziabad-logo.jpeg') ?>" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;" alt="Logo">
                                                        <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?= htmlspecialchars($f['team2Name']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php 
                                                    $sT = $f['startTime'] ?? ($f['matchTime'] ?? '04:00 PM');
                                                    $eT = $f['endTime'] ?? '';
                                                    $timeRange = !empty($eT) ? "$sT – $eT" : $sT;
                                                ?>
                                                <span style="color: #0f172a; font-weight: 600; font-size: 13px;"><i class="fa-regular fa-calendar" style="color: var(--primary);"></i> <?= htmlspecialchars(date('d M Y', strtotime($f['matchDate']))) ?></span><br>
                                                <small style="color: #64748b; font-weight: 600;"><i class="fa-regular fa-clock" style="color: #3b82f6;"></i> <?= htmlspecialchars($timeRange) ?></small>
                                            </td>
                                            <td style="color: #475569; font-size: 12.5px; max-width: 200px;">
                                                <i class="fa-solid fa-location-dot" style="color: #ef4444; margin-right: 4px;"></i> <?= htmlspecialchars($f['stadium']) ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; border: 1px solid <?= $badgeBorder ?>; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                                    <?php if ($cStatus === 'Live'): ?>
                                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #dc2626; display: inline-block; animation: pulse 1s infinite;"></span>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($cStatus) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($f['team1Score'] !== null && $f['team1Score'] !== '' && $f['team2Score'] !== null && $f['team2Score'] !== ''): ?>
                                                    <span style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 4px 8px; border-radius: 6px; font-weight: 800; font-size: 13.5px; color: #0f172a;">
                                                        <?= $f['team1Score'] ?> - <?= $f['team2Score'] ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 13px;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editFixtureRow(<?= htmlspecialchars(json_encode($f), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Fixture">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteFixtureRow('<?= htmlspecialchars($f['id']) ?>')" title="Delete Fixture">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- FIXTURES AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="fixturesPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="fixturesPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 fixtures</span>
                            <div id="fixturesPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: MVP PLAYERS MANAGEMENT -->
                <div class="section-panel" id="sectionMvp">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <h2><i class="fa-solid fa-award" style="color: #f59e0b;"></i> MVP Players & Match Stars</h2>
                                
                                <select id="adminMvpSeasonFilter" onchange="filterAdminMvp()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <?php foreach ($mvpSeasons as $mSName): ?>
                                        <option value="<?= htmlspecialchars($mSName) ?>"><?= htmlspecialchars($mSName) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select id="adminMvpDayFilter" onchange="filterAdminMvp()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Match Days</option>
                                    <?php foreach ($mvpDays as $mDName): ?>
                                        <option value="<?= htmlspecialchars($mDName) ?>"><?= htmlspecialchars($mDName) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="text" id="adminMvpSearchInput" onkeyup="filterAdminMvp()" placeholder="Search player, team, award..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px; background: #fff;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openMvpModal()">
                                <i class="fa-solid fa-plus"></i> Add MVP Player
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Season / Day</th>
                                    <th>MVP Player Name</th>
                                    <th>Franchise Team</th>
                                    <th>Award / Title</th>
                                    <th>Jersey / Position</th>
                                    <th>Stats / Rating</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="mvpTbody">
                                <?php if (empty($mvpPlayers)): ?>
                                    <tr id="noMvpRow">
                                        <td colspan="7" style="text-align: center; padding: 35px; color: #94a3b8;">
                                            <i class="fa-solid fa-award" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No MVP Players recorded yet. Click <strong>"Add MVP Player"</strong> above to reward match performers.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mvpPlayers as $mp): ?>
                                        <tr id="mvp-row-<?= htmlspecialchars($mp['id']) ?>" data-season="<?= htmlspecialchars($mp['season']) ?>" data-day="<?= htmlspecialchars($mp['matchDay']) ?>">
                                            <td>
                                                <div style="display: flex; gap: 5px; margin-bottom: 4px; flex-wrap: wrap;">
                                                    <span class="season-badge"><i class="fa-solid fa-trophy"></i> <?= htmlspecialchars($mp['season']) ?></span>
                                                    <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 4px;"><?= htmlspecialchars($mp['matchDay'] ?? 'Day 1') ?></span>
                                                    <span style="background: #fef3c7; color: #b45309; font-size: 11px; font-weight: 800; padding: 2px 7px; border-radius: 4px;"><?= htmlspecialchars($mp['matchNumber'] ?? 'Match 1') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <?php if (!empty($mp['playerPhoto'])): ?>
                                                        <img src="../<?= htmlspecialchars($mp['playerPhoto']) ?>" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid #f59e0b;" alt="MVP">
                                                    <?php else: ?>
                                                        <div style="width: 34px; height: 34px; border-radius: 50%; background: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; border: 2px solid #fde68a;">
                                                            <?= strtoupper(substr($mp['playerName'] ?? 'P', 0, 2)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($mp['playerName']) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 7px;">
                                                    <img src="../<?= htmlspecialchars($mp['teamLogo'] ?: 'assets/images/teams/bhadohi-logo.jpeg') ?>" style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover;" alt="Team">
                                                    <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?= htmlspecialchars($mp['teamName']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                                    <i class="fa-solid fa-award" style="color: #f59e0b;"></i> <?= htmlspecialchars($mp['awardTitle'] ?? 'Player of the Match') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($mp['jerseyNumber'] ?: '-') ?></span>
                                                <small style="color: #64748b; display: block;"><?= htmlspecialchars($mp['position'] ?: 'Player') ?></small>
                                            </td>
                                            <td>
                                                <span style="color: #0f172a; font-weight: 700; font-size: 13px;"><?= htmlspecialchars($mp['goals'] ?: '-') ?></span>
                                                <?php if (!empty($mp['rating'])): ?>
                                                    <small style="background: #ecfdf5; color: #047857; padding: 2px 6px; border-radius: 4px; font-weight: 800; margin-left: 4px;">★ <?= htmlspecialchars($mp['rating']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editMvp(<?= htmlspecialchars(json_encode($mp), ENT_QUOTES, 'UTF-8') ?>)" title="Edit MVP">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteMvp('<?= htmlspecialchars($mp['id']) ?>')" title="Delete MVP">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- MVP AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="mvpPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="mvpPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 MVP players</span>
                            <div id="mvpPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: WINNERS & RUNNERS-UP SLIDER MANAGEMENT -->
                <div class="section-panel" id="sectionWinners">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <h2><i class="fa-solid fa-trophy" style="color: #eab308;"></i> Winners &amp; Runners-Up Slider</h2>
                                
                                <select id="adminWinnerSeasonFilter" onchange="filterAdminWinners()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <?php foreach ($winnerSeasons as $wSName): ?>
                                        <option value="<?= htmlspecialchars($wSName) ?>"><?= htmlspecialchars($wSName) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select id="adminWinnerCategoryFilter" onchange="filterAdminWinners()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 13px; color: #1e293b; background: #fff;">
                                    <option value="all">All Categories</option>
                                    <option value="winner">Winner / Champion</option>
                                    <option value="runner-up">Runner-Up</option>
                                </select>

                                <input type="text" id="adminWinnerSearchInput" onkeyup="filterAdminWinners()" placeholder="Search title, team, description..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px; background: #fff;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openWinnerRunnerModal()">
                                <i class="fa-solid fa-plus"></i> Add Winner / Runner Slide
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Category</th>
                                    <th>Season</th>
                                    <th>Title &amp; Team</th>
                                    <th>Description / Caption</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="winnersTbody">
                                <?php if (empty($winners)): ?>
                                    <tr id="noWinnersRow">
                                        <td colspan="8" style="text-align: center; padding: 35px; color: #94a3b8;">
                                            <i class="fa-solid fa-trophy" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No Winner/Runner slides uploaded yet. Click <strong>"Add Winner / Runner Slide"</strong> to add champions celebration photos.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($winners as $w): 
                                        $cat = $w['category'] ?? 'Winner';
                                        $isWinner = strtolower($cat) === 'winner';
                                    ?>
                                        <tr id="winner-row-<?= htmlspecialchars($w['id']) ?>" data-season="<?= htmlspecialchars($w['season']) ?>" data-category="<?= htmlspecialchars(strtolower($w['category'])) ?>">
                                            <td>
                                                <img src="../<?= htmlspecialchars($w['imageUrl']) ?>" class="gallery-media-thumb" alt="Winner Slide" onclick="previewMediaInPopup('../<?= htmlspecialchars($w['imageUrl']) ?>', 'image')" style="cursor: pointer; width: 75px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            </td>
                                            <td>
                                                <?php if ($isWinner): ?>
                                                    <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                                        <i class="fa-solid fa-crown" style="color: #eab308;"></i> Winner
                                                    </span>
                                                <?php else: ?>
                                                    <span style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                                        <i class="fa-solid fa-medal" style="color: #0284c7;"></i> Runner-Up
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="season-badge"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($w['season']) ?></span>
                                            </td>
                                            <td>
                                                <strong style="color: #0f172a; font-size: 14px; display: block;"><?= htmlspecialchars($w['title']) ?></strong>
                                                <?php if (!empty($w['teamName'])): ?>
                                                    <small style="color: var(--primary); font-weight: 700;"><?= htmlspecialchars($w['teamName']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td style="max-width: 250px; color: #64748b; font-size: 12.5px; line-height: 1.4;">
                                                <?= htmlspecialchars(mb_strimwidth($w['description'] ?? '', 0, 90, '...')) ?>
                                            </td>
                                            <td style="text-align: center; font-weight: 700; color: #1e293b;">
                                                <?= htmlspecialchars($w['displayOrder'] ?? 0) ?>
                                            </td>
                                            <td>
                                                <span class="badge-status <?= htmlspecialchars($w['status'] ?? 'Active') ?>">
                                                    <?= htmlspecialchars($w['status'] ?? 'Active') ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editWinnerRunner(<?= htmlspecialchars(json_encode($w), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Slide">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteWinnerRunner('<?= htmlspecialchars($w['id']) ?>')" title="Delete Slide">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- WINNERS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="winnersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="winnersPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 slides</span>
                            <div id="winnersPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: HERO BANNERS MANAGEMENT -->
                <div class="section-panel" id="sectionBanners">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <h2><i class="fa-solid fa-panorama"></i> Homepage Hero Banners</h2>
                                <input type="text" id="adminBannerSearchInput" onkeyup="filterAdminBanners()" placeholder="Search banner heading, badge..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 220px; background: #fff;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openBannerModal()">
                                <i class="fa-solid fa-plus"></i> Add New Banner
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th>Media Preview</th>
                                    <th>Type</th>
                                    <th>Heading & Badge</th>
                                    <th>Subtitle</th>
                                    <th>Action Buttons</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bannersTbody">
                                <?php if (empty($banners)): ?>
                                    <tr id="noBannersRow">
                                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-regular fa-image" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No custom hero banners added yet. Click <strong>"Add New Banner"</strong> above to upload your first image or video banner.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($banners as $b): ?>
                                        <tr id="banner-row-<?= htmlspecialchars($b['bannerId']) ?>" data-search="<?= strtolower(htmlspecialchars(($b['heading'] ?? '') . ' ' . ($b['badgeText'] ?? '') . ' ' . ($b['subtitle'] ?? '') . ' ' . ($b['mediaType'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #e0e7ff; color: #3730a3; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($b['sortOrder'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (($b['mediaType'] ?? 'image') === 'video'): ?>
                                                    <div style="position: relative; width: 80px; height: 50px; border-radius: 8px; overflow: hidden; background: #0f172a; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="previewMediaInPopup('../<?= htmlspecialchars($b['mediaUrl']) ?>', 'video')">
                                                        <video src="../<?= htmlspecialchars($b['mediaUrl']) ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;"></video>
                                                        <i class="fa-solid fa-play" style="position: absolute; color: #fff; font-size: 16px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <img src="../<?= htmlspecialchars($b['mediaUrl']) ?>" class="banner-media-thumb" alt="Banner" onclick="previewMediaInPopup('../<?= htmlspecialchars($b['mediaUrl']) ?>', 'image')" style="cursor: pointer;">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="media-badge <?= htmlspecialchars($b['mediaType'] ?? 'image') ?>">
                                                    <i class="fa-solid <?= ($b['mediaType'] ?? 'image') === 'video' ? 'fa-video' : 'fa-image' ?>"></i>
                                                    <?= htmlspecialchars($b['mediaType'] ?? 'image') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($b['badgeText'])): ?>
                                                    <span style="font-size: 11px; background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; padding: 2px 6px; border-radius: 4px; font-weight: 700; display: inline-block; margin-bottom: 4px;">
                                                        <?= htmlspecialchars($b['badgeText']) ?>
                                                    </span><br>
                                                <?php endif; ?>
                                                <strong style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($b['heading']) ?></strong>
                                            </td>
                                            <td style="max-width: 250px; color: #64748b; font-size: 12.5px; line-height: 1.5;">
                                                <?= htmlspecialchars(mb_strimwidth($b['subtitle'] ?? '', 0, 100, '...')) ?>
                                            </td>
                                            <td>
                                                <div style="display: flex; flex-direction: column; gap: 4px; font-size: 11.5px;">
                                                    <?php if (!empty($b['btn1Text'])): ?>
                                                        <span style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #334155; font-weight: 600;">
                                                             <strong>Btn 1:</strong> <?= htmlspecialchars($b['btn1Text']) ?> <span style="color: #94a3b8;">(<?= htmlspecialchars($b['btn1Link'] ?: '#') ?>)</span>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($b['btn2Text'])): ?>
                                                        <span style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #334155; font-weight: 600;">
                                                            <strong>Btn 2:</strong> <?= htmlspecialchars($b['btn2Text']) ?> <span style="color: #94a3b8;">(<?= htmlspecialchars($b['btn2Link'] ?: '#') ?>)</span>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if (empty($b['btn1Text']) && empty($b['btn2Text'])): ?>
                                                        <span style="color: #94a3b8; font-style: italic;">No Buttons</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" onclick="toggleBannerStatus('<?= htmlspecialchars($b['bannerId']) ?>', '<?= ($b['status'] ?? 'Active') === 'Active' ? 'Inactive' : 'Active' ?>')" class="badge-status <?= htmlspecialchars($b['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($b['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editBanner(<?= htmlspecialchars(json_encode($b), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Banner">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteBanner('<?= htmlspecialchars($b['bannerId']) ?>')" title="Delete Banner">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- BANNERS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="bannersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="bannersPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 banners</span>
                            <div id="bannersPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: INNER PAGE BANNERS MANAGEMENT -->
                <div class="section-panel" id="sectionPageBanners">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <div>
                                    <h2><i class="fa-solid fa-images" style="color: var(--primary);"></i> Inner Page Banners (All Pages)</h2>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Update top hero banner images, headings, taglines, and descriptions for all pages across the website (except Homepage).</p>
                                </div>
                                <input type="text" id="adminPageBannerSearchInput" onkeyup="filterAdminPageBanners()" placeholder="Search page name, title..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 220px; background: #fff; margin-left: auto;">
                            </div>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 170px;">Page Name</th>
                                    <th>Banner Media</th>
                                    <th>Heading &amp; Tag</th>
                                    <th>Subtitle / Description</th>
                                    <th>Status</th>
                                    <th style="text-align: center; width: 110px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="pageBannersTbody">
                                <?php foreach ($pageBanners as $key => $pb): ?>
                                    <tr id="page-banner-row-<?= htmlspecialchars($key) ?>" data-search="<?= strtolower(htmlspecialchars(($pb['pageName'] ?? $key) . ' ' . ($pb['title'] ?? '') . ' ' . ($pb['badgeText'] ?? '') . ' ' . ($pb['subtitle'] ?? ''))) ?>">
                                        <td>
                                            <strong style="color: #0f172a; font-size: 14px; display: block; margin-bottom: 2px;">
                                                <?= htmlspecialchars($pb['pageName'] ?? $key) ?>
                                            </strong>
                                            <span style="font-size: 11.5px; color: #64748b; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-family: monospace;">
                                                <?= htmlspecialchars($pb['url'] ?? ($key . '.php')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($pb['bannerImage'])): ?>
                                                <img src="../<?= htmlspecialchars($pb['bannerImage']) ?>" class="banner-media-thumb" alt="Banner" onclick="previewMediaInPopup('../<?= htmlspecialchars($pb['bannerImage']) ?>', 'image')" style="cursor: pointer; width: 90px; height: 55px; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;" onerror="this.src='../assets/images/aboutus-banner.jpeg'">
                                            <?php else: ?>
                                                <div style="width: 90px; height: 55px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 12px; border: 1px dashed #cbd5e1;">Default</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($pb['badgeText'])): ?>
                                                <span style="font-size: 11px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 2px 6px; border-radius: 4px; font-weight: 700; display: inline-block; margin-bottom: 4px;">
                                                    <?= htmlspecialchars($pb['badgeText']) ?>
                                                </span><br>
                                            <?php endif; ?>
                                            <strong style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($pb['title'] ?? '') ?></strong>
                                        </td>
                                        <td style="max-width: 280px; color: #64748b; font-size: 12.5px; line-height: 1.5;">
                                            <?= htmlspecialchars(mb_strimwidth($pb['subtitle'] ?? '', 0, 110, '...')) ?>
                                        </td>
                                        <td>
                                            <span class="badge-status <?= htmlspecialchars($pb['status'] ?? 'Active') ?>">
                                                <?= htmlspecialchars($pb['status'] ?? 'Active') ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn-act edit-btn" onclick="editPageBanner(<?= htmlspecialchars(json_encode($pb), ENT_QUOTES, 'UTF-8') ?>)" title="Update Banner">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- PAGE BANNERS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="pageBannersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="pageBannersPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 page banners</span>
                            <div id="pageBannersPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: GALLERY PHOTOS MANAGEMENT -->
                <div class="section-panel" id="sectionGallery">
                    <div class="table-card">
                        <div class="table-head">
                            <div>
                                <h2><i class="fa-solid fa-camera-retro" style="color: var(--cyan);"></i> Gallery Photos &amp; Season Management</h2>
                                <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Upload single or multiple photos at once season-wise into categories (Trails, Announcement, Trophy, Auction, etc.).</p>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <input type="text" id="adminGallerySearchInput" onkeyup="filterAdminGalleryTable()" placeholder="Search gallery title, season..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; width: 190px; background: #fff;">
                                <select id="adminGallerySeasonFilter" onchange="filterAdminGalleryTable()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <option value="Season 2">Season 2</option>
                                    <option value="Season 1">Season 1</option>
                                    <option value="Season 3">Season 3</option>
                                </select>
                                <select id="adminGalleryCatFilter" onchange="filterAdminGalleryTable()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                                    <option value="all">All Categories</option>
                                    <option value="announcement">Announcement Day</option>
                                    <option value="trail">Trails</option>
                                    <option value="trophy">Trophy Launch</option>
                                    <option value="auction">Auction</option>
                                    <option value="glimpses">Match Glimpses</option>
                                    <option value="news">News</option>
                                </select>
                                <button type="button" class="export-btn" style="background: var(--primary);" onclick="openGalleryModal()">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload Photos
                                </button>
                            </div>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Photo</th>
                                    <th>Season</th>
                                    <th>Category Section</th>
                                    <th>Title / Caption (Optional)</th>
                                    <th>Uploaded Date</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="galleryTbody">
                                <?php if (empty($galleryPhotos)): ?>
                                    <tr id="noGalleryRow">
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-regular fa-image" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No gallery photos added yet. Click <strong>"Upload Photos"</strong> to add photos.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($galleryPhotos as $g): ?>
                                        <tr id="gallery-row-<?= htmlspecialchars($g['id']) ?>" class="gallery-admin-row" data-season="<?= htmlspecialchars(strtolower($g['season'] ?? 'season 2')) ?>" data-cat="<?= htmlspecialchars(strtolower($g['category'] ?? 'glimpses')) ?>" data-search="<?= strtolower(htmlspecialchars(($g['title'] ?? '') . ' ' . ($g['season'] ?? '') . ' ' . ($g['category'] ?? '') . ' ' . ($g['categoryLabel'] ?? ''))) ?>">
                                            <td>
                                                <img src="../<?= htmlspecialchars($g['imageUrl']) ?>" class="gallery-media-thumb" alt="Photo" onclick="previewMediaInPopup('../<?= htmlspecialchars($g['imageUrl']) ?>', 'image')" style="cursor: pointer; width: 70px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            </td>
                                            <td>
                                                <span class="season-badge"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($g['season'] ?? 'Season 2') ?></span>
                                            </td>
                                            <td>
                                                <span class="category-badge"><?= htmlspecialchars($g['categoryLabel'] ?? $g['category']) ?></span>
                                            </td>
                                            <td>
                                                <span style="color: #475569; font-size: 13.5px;"><?= htmlspecialchars($g['title'] ?: '— (No Title)') ?></span>
                                            </td>
                                            <td>
                                                <small style="color: #64748b; font-weight: 600;"><?= htmlspecialchars($g['createdAt'] ?? '') ?></small>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editGalleryPhoto(<?= htmlspecialchars(json_encode($g), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Photo">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteGalleryPhoto('<?= htmlspecialchars($g['id']) ?>')" title="Delete Photo">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- GALLERY AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="galleryPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="galleryPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 gallery photos</span>
                            <div id="galleryPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: LATEST NEWS & UPDATES MANAGEMENT -->
                <div class="section-panel" id="sectionNews">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <div>
                                    <h2><i class="fa-solid fa-newspaper" style="color: #e11d48;"></i> Latest News &amp; Updates Management</h2>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Manage homepage Latest News cards (Latest 3 active items are displayed on the home page).</p>
                                </div>
                                <input type="text" id="adminNewsSearchInput" onkeyup="filterAdminNews()" placeholder="Search news title, category..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px; background: #fff; margin-left: auto;">
                            </div>
                            <button type="button" class="export-btn" style="background: var(--primary);" onclick="openNewsModal()">
                                <i class="fa-solid fa-plus"></i> Add News Update
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th>Image</th>
                                    <th>Title &amp; Category</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Gallery Redirect Link</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="newsTbody">
                                <?php if (empty($newsList)): ?>
                                    <tr id="noNewsRow">
                                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-regular fa-newspaper" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No news updates added yet. Click <strong>"Add News Update"</strong> to publish news.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($newsList as $n): ?>
                                        <tr id="news-row-<?= htmlspecialchars($n['id']) ?>" data-search="<?= strtolower(htmlspecialchars(($n['title'] ?? '') . ' ' . ($n['category'] ?? '') . ' ' . ($n['description'] ?? '') . ' ' . ($n['date'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #ffe4e6; color: #be123c; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($n['sortOrder'] ?? 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <img src="../<?= htmlspecialchars($n['imageUrl'] ?: 'assets/images/ind1.jpg') ?>" class="gallery-media-thumb" alt="News Image" onclick="previewMediaInPopup('../<?= htmlspecialchars($n['imageUrl'] ?: 'assets/images/ind1.jpg') ?>', 'image')" style="cursor: pointer; width: 75px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            </td>
                                            <td>
                                                <?php if (!empty($n['category'])): ?>
                                                    <span style="font-size: 11px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 2px 6px; border-radius: 4px; font-weight: 700; display: inline-block; margin-bottom: 4px;">
                                                        <?= htmlspecialchars($n['category']) ?>
                                                    </span><br>
                                                <?php endif; ?>
                                                <strong style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($n['title']) ?></strong>
                                            </td>
                                            <td>
                                                <span style="color: #64748b; font-size: 12.5px; font-weight: 600; white-space: nowrap;">
                                                    <i class="fa-regular fa-calendar" style="color: var(--primary);"></i> <?= htmlspecialchars($n['date'] ?? '') ?>
                                                </span>
                                            </td>
                                            <td style="max-width: 260px; color: #64748b; font-size: 12.5px; line-height: 1.5;">
                                                <?= htmlspecialchars(mb_strimwidth($n['description'] ?? '', 0, 100, '...')) ?>
                                            </td>
                                            <td>
                                                <a href="../<?= htmlspecialchars($n['galleryLink'] ?: 'gallery.php?cat=news') ?>" target="_blank" style="color: #2563eb; font-size: 12px; font-weight: 600; text-decoration: underline;">
                                                    <?= htmlspecialchars($n['galleryLink'] ?: 'gallery.php?cat=news') ?>
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button" onclick="toggleNewsStatus('<?= htmlspecialchars($n['id']) ?>')" class="badge-status <?= htmlspecialchars($n['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($n['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editNews(<?= htmlspecialchars(json_encode($n), ENT_QUOTES, 'UTF-8') ?>)" title="Edit News">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteNews('<?= htmlspecialchars($n['id']) ?>')" title="Delete News">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- NEWS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="newsPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="newsPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 news updates</span>
                            <div id="newsPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: PARTNERS & SPONSORS -->
                <div class="section-panel" id="sectionPartners">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <div>
                                    <h2><i class="fa-solid fa-handshake" style="color: #059669;"></i> Our Partners &amp; Sponsors Management</h2>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Manage homepage marquee scrolling sponsors and brand partners (Logo/Image, Name &amp; Category badge).</p>
                                </div>
                                <input type="text" id="adminPartnerSearchInput" onkeyup="filterAdminPartners()" placeholder="Search partner name, category..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 220px; background: #fff; margin-left: auto;">
                            </div>
                            <button type="button" class="export-btn" style="background: #059669;" onclick="openPartnerModal()">
                                <i class="fa-solid fa-plus"></i> Add Partner / Sponsor
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th style="width: 120px;">Logo / Icon</th>
                                    <th>Partner / Sponsor Name</th>
                                    <th>Category Badge</th>
                                    <th>Website / Link</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="partnersTbody">
                                <?php if (empty($partners)): ?>
                                    <tr id="noPartnersRow">
                                        <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-solid fa-handshake" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No partners or sponsors added yet. Click <strong>"Add Partner / Sponsor"</strong> to add one.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($partners as $p): ?>
                                        <tr id="partner-row-<?= htmlspecialchars($p['id']) ?>" data-search="<?= strtolower(htmlspecialchars(($p['name'] ?? '') . ' ' . ($p['partnerType'] ?? '') . ' ' . ($p['linkUrl'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #ecfdf5; color: #047857; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($p['sortOrder'] ?? 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="width: 90px; height: 50px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px;">
                                                    <?php if (!empty($p['logoUrl'])): ?>
                                                        <img src="../<?= htmlspecialchars($p['logoUrl']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    <?php else: ?>
                                                        <i class="<?= htmlspecialchars($p['iconClass'] ?: 'fa-solid fa-gem') ?>" style="font-size: 26px; color: <?= htmlspecialchars($p['iconColor'] ?: '#ff6a13') ?>;"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <strong style="color: #0f172a; font-size: 14.5px;"><?= htmlspecialchars($p['name']) ?></strong>
                                            </td>
                                            <td>
                                                <span style="font-size: 12px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 8px; border-radius: 6px; font-weight: 700; display: inline-block;">
                                                    <?= htmlspecialchars($p['partnerType'] ?: 'Official Sponsor') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($p['linkUrl']) && $p['linkUrl'] !== '#'): ?>
                                                    <a href="<?= htmlspecialchars($p['linkUrl']) ?>" target="_blank" style="color: #2563eb; font-size: 12px; font-weight: 600; text-decoration: underline;">
                                                        <?= htmlspecialchars($p['linkUrl']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 12px;">None (#)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" onclick="togglePartnerStatus('<?= htmlspecialchars($p['id']) ?>')" class="badge-status <?= htmlspecialchars($p['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($p['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editPartner(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Partner">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deletePartner('<?= htmlspecialchars($p['id']) ?>')" title="Delete Partner">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- PARTNERS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="partnersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="partnersPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 partners</span>
                            <div id="partnersPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: LEAGUE MANAGEMENT COMMITTEE -->
                <div class="section-panel" id="sectionCommittee">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <div>
                                    <h2><i class="fa-solid fa-users-gear" style="color: #6366f1;"></i> League Management Committee Management</h2>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Manage League Management Committee members displayed on the League Details page (Photo, Name, Badge &amp; Designations).</p>
                                </div>
                                <input type="text" id="adminCommitteeSearchInput" onkeyup="filterAdminCommittee()" placeholder="Search committee member, post..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 220px; background: #fff; margin-left: auto;">
                            </div>
                            <button type="button" class="export-btn" style="background: #6366f1;" onclick="openCommitteeModal()">
                                <i class="fa-solid fa-plus"></i> Add Committee Member
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th style="width: 90px;">Photo</th>
                                    <th>Member Name</th>
                                    <th>Badge</th>
                                    <th>Designation</th>
                                    <th>Sub-Designation</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="committeeTbody">
                                <?php if (empty($committeeMembers)): ?>
                                    <tr id="noCommitteeRow">
                                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-solid fa-users-gear" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No committee members added yet. Click <strong>"Add Committee Member"</strong> to add one.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($committeeMembers as $cm): ?>
                                        <tr id="committee-row-<?= htmlspecialchars($cm['id']) ?>" data-search="<?= strtolower(htmlspecialchars(($cm['name'] ?? '') . ' ' . ($cm['badge'] ?? '') . ' ' . ($cm['designation'] ?? '') . ' ' . ($cm['subDesignation'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #ede9fe; color: #6d28d9; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($cm['sortOrder'] ?? 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <img src="../<?= htmlspecialchars($cm['imageUrl'] ?: 'assets/images/league-details/amit-pandey.jpeg') ?>" class="gallery-media-thumb" alt="<?= htmlspecialchars($cm['name']) ?>" onclick="previewMediaInPopup('../<?= htmlspecialchars($cm['imageUrl'] ?: 'assets/images/league-details/amit-pandey.jpeg') ?>', 'image')" style="cursor: pointer; width: 55px; height: 55px; object-fit: cover; border-radius: 50%; border: 2px solid #e2e8f0;">
                                            </td>
                                            <td>
                                                <strong style="color: #0f172a; font-size: 14.5px;"><?= htmlspecialchars($cm['name']) ?></strong>
                                            </td>
                                            <td>
                                                <?php if (!empty($cm['badge'])): ?>
                                                    <span style="font-size: 11.5px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 3px 8px; border-radius: 6px; font-weight: 700; display: inline-block;">
                                                        <?= htmlspecialchars($cm['badge']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 12px;">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span style="font-size: 13px; font-weight: 600; color: #334155;">
                                                    <?= htmlspecialchars($cm['designation'] ?: '—') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="font-size: 12.5px; color: #64748b;">
                                                    <?= htmlspecialchars($cm['subDesignation'] ?: '—') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" onclick="toggleCommitteeStatus('<?= htmlspecialchars($cm['id']) ?>')" class="badge-status <?= htmlspecialchars($cm['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($cm['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editCommittee(<?= htmlspecialchars(json_encode($cm), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Member">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteCommittee('<?= htmlspecialchars($cm['id']) ?>')" title="Delete Member">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- COMMITTEE AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="committeePaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="committeePageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 committee members</span>
                            <div id="committeePaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: LIVE & VIDEO HUB MANAGEMENT -->
                <div class="section-panel" id="sectionLiveHub">
                    <!-- SUB-TABS NAVIGATION -->
                    <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; flex-wrap: wrap;">
                        <button type="button" id="liveTabBtnPartners" class="tab-btn active" style="padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 14px; border: none; cursor: pointer; background: var(--primary); color: #fff; display: inline-flex; align-items: center; gap: 8px;" onclick="switchLiveSubTab('partners')">
                            <i class="fa-solid fa-tower-broadcast"></i> 1. Official Streaming &amp; TV Partners
                            <span style="background: rgba(255,255,255,0.25); padding: 2px 7px; border-radius: 12px; font-size: 11px;" id="badgeLivePartnersCount"><?= $livePartnerCount ?></span>
                        </button>
                        <button type="button" id="liveTabBtnVideos" class="tab-btn" style="padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 14px; border: 1px solid #cbd5e1; cursor: pointer; background: #fff; color: #334155; display: inline-flex; align-items: center; gap: 8px;" onclick="switchLiveSubTab('videos')">
                            <i class="fa-solid fa-film"></i> 2. Completed Season Videos &amp; Replays
                            <span style="background: #f1f5f9; color: #0f172a; padding: 2px 7px; border-radius: 12px; font-size: 11px;" id="badgeLiveVideosCount"><?= $liveVideoCount ?></span>
                        </button>
                    </div>

                    <!-- SUB-PANEL 1: BROADCAST PARTNERS -->
                    <div id="liveSubPanelPartners" class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                                <div>
                                    <h2><i class="fa-solid fa-tower-broadcast" style="color: #dc2626;"></i> Official Streaming &amp; TV Partners</h2>
                                    <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Manage official broadcast channels, OTT platforms, streaming links, and badges shown on the UPPHL Live page.</p>
                                </div>
                                <input type="text" id="adminLivePartnerSearchInput" onkeyup="filterAdminLivePartners()" placeholder="Search streaming partner..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px; background: #fff; margin-left: auto;">
                            </div>
                            <button type="button" class="export-btn" style="background: #dc2626;" onclick="openLivePartnerModal()">
                                <i class="fa-solid fa-plus"></i> Add Broadcast Partner
                            </button>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th style="width: 90px;">Logo / Icon</th>
                                    <th>Partner Name &amp; Network</th>
                                    <th>Badge &amp; Meta Pill</th>
                                    <th>Watch / Stream Link</th>
                                    <th>Button Text</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="livePartnersTbody">
                                <?php if (empty($livePartners)): ?>
                                    <tr id="noLivePartnersRow">
                                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-solid fa-tower-broadcast" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No broadcast partners added yet. Click <strong>"Add Broadcast Partner"</strong> to add one.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($livePartners as $lp): ?>
                                        <tr id="live-partner-row-<?= htmlspecialchars($lp['id']) ?>" data-search="<?= strtolower(htmlspecialchars(($lp['name'] ?? '') . ' ' . ($lp['platformType'] ?? '') . ' ' . ($lp['badgeText'] ?? '') . ' ' . ($lp['watchUrl'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #fee2e2; color: #b91c1c; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($lp['sortOrder'] ?? 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="width: 65px; height: 45px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 3px;">
                                                    <?php if (!empty($lp['logoUrl'])): ?>
                                                        <img src="../<?= htmlspecialchars($lp['logoUrl']) ?>" alt="<?= htmlspecialchars($lp['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                    <?php else: ?>
                                                        <i class="<?= htmlspecialchars($lp['iconClass'] ?: 'fa-solid fa-tower-broadcast') ?>" style="font-size: 20px; color: #ea580c;"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <strong style="color: #0f172a; font-size: 14.5px; display: block;"><?= htmlspecialchars($lp['name']) ?></strong>
                                                <?php if (!empty($lp['platformType'])): ?>
                                                    <small style="color: #64748b; font-weight: 600;"><?= htmlspecialchars($lp['platformType']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($lp['badgeText'])): ?>
                                                    <span style="font-size: 11px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 2px 7px; border-radius: 4px; font-weight: 800; display: inline-block; margin-bottom: 3px;">
                                                        <?= htmlspecialchars($lp['badgeText']) ?>
                                                    </span><br>
                                                <?php endif; ?>
                                                <?php if (!empty($lp['metaPill'])): ?>
                                                    <small style="color: #059669; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($lp['metaPill']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($lp['watchUrl']) && $lp['watchUrl'] !== '#'): ?>
                                                    <a href="<?= htmlspecialchars($lp['watchUrl']) ?>" target="_blank" style="color: #2563eb; font-size: 12px; font-weight: 600; text-decoration: underline; max-width: 220px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        <?= htmlspecialchars($lp['watchUrl']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 12px;">None (#)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #334155;">
                                                    <?= htmlspecialchars($lp['buttonText'] ?: 'Watch Live') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" onclick="toggleLivePartnerStatus('<?= htmlspecialchars($lp['id']) ?>', '<?= htmlspecialchars($lp['status'] ?? 'Active') ?>')" class="badge-status <?= htmlspecialchars($lp['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($lp['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editLivePartner(<?= htmlspecialchars(json_encode($lp), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Partner">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteLivePartner('<?= htmlspecialchars($lp['id']) ?>')" title="Delete Partner">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- LIVE PARTNERS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="livePartnersPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="livePartnersPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 streaming partners</span>
                            <div id="livePartnersPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>

                    <!-- SUB-PANEL 2: COMPLETED SEASON VIDEOS & REPLAYS -->
                    <div id="liveSubPanelVideos" class="table-card" style="display: none;">
                        <div class="table-head">
                            <div>
                                <h2><i class="fa-solid fa-film" style="color: #f97316;"></i> Completed Season Videos &amp; Match Replays</h2>
                                <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Manage season-wise completed tournament match replays, highlights, and ceremonies with YouTube links &amp; thumbnails.</p>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <input type="text" id="adminLiveVideoSearchInput" onkeyup="filterAdminLiveVideos()" placeholder="Search video title, season..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; width: 190px; background: #fff;">
                                <select id="adminLiveVideoSeasonFilter" onchange="filterAdminLiveVideos()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                                    <option value="all">All Seasons</option>
                                    <?php foreach ($liveVideoSeasons as $vs): ?>
                                        <option value="<?= htmlspecialchars($vs) ?>"><?= htmlspecialchars($vs) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select id="adminLiveVideoCatFilter" onchange="filterAdminLiveVideos()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                                    <option value="all">All Categories</option>
                                    <option value="Full Match">Full Match</option>
                                    <option value="Highlights">Highlights</option>
                                    <option value="Top Moments">Top Moments</option>
                                    <option value="Ceremony">Ceremony</option>
                                </select>
                                <button type="button" class="export-btn" style="background: #f97316;" onclick="openLiveVideoModal()">
                                    <i class="fa-solid fa-plus"></i> Add Season Video
                                </button>
                            </div>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th style="width: 100px;">Thumbnail</th>
                                    <th>Season / Category</th>
                                    <th>Video Title &amp; Date</th>
                                    <th>Duration</th>
                                    <th>Video / YouTube Link</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="liveVideosTbody">
                                <?php if (empty($liveVideos)): ?>
                                    <tr id="noLiveVideosRow">
                                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-solid fa-film" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No season videos added yet. Click <strong>"Add Season Video"</strong> to add video replays.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($liveVideos as $lv): ?>
                                        <tr id="live-video-row-<?= htmlspecialchars($lv['id']) ?>" class="live-video-admin-row" data-season="<?= htmlspecialchars(strtolower($lv['season'] ?? 'season 1')) ?>" data-cat="<?= htmlspecialchars(strtolower($lv['videoCategory'] ?? 'full match')) ?>" data-search="<?= strtolower(htmlspecialchars(($lv['title'] ?? '') . ' ' . ($lv['season'] ?? '') . ' ' . ($lv['videoCategory'] ?? '') . ' ' . ($lv['matchDate'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #ffedd5; color: #c2410c; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($lv['sortOrder'] ?? 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="position: relative; width: 85px; height: 50px; border-radius: 6px; overflow: hidden; background: #000;">
                                                    <img src="<?= htmlspecialchars(strpos($lv['thumbnailUrl'], 'http') === 0 ? $lv['thumbnailUrl'] : ('../' . $lv['thumbnailUrl'])) ?>" alt="<?= htmlspecialchars($lv['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <div style="position: absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.3);">
                                                        <i class="fa-solid fa-play" style="color: #fff; font-size: 12px;"></i>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="season-badge"><i class="fa-solid fa-trophy"></i> <?= htmlspecialchars($lv['season'] ?? 'Season 1') ?></span><br>
                                                <span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: 700; display: inline-block; margin-top: 3px;">
                                                    <?= htmlspecialchars($lv['videoCategory'] ?? 'Full Match') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong style="color: #0f172a; font-size: 14px; display: block; max-width: 280px;"><?= htmlspecialchars($lv['title']) ?></strong>
                                                <small style="color: #64748b; font-weight: 600;"><i class="fa-regular fa-calendar" style="color: #ea580c;"></i> <?= htmlspecialchars($lv['matchDate'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #334155;">
                                                    <i class="fa-regular fa-clock" style="color: #64748b;"></i> <?= htmlspecialchars($lv['duration'] ?: '—') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= htmlspecialchars($lv['videoUrl']) ?>" target="_blank" style="color: #ef4444; font-size: 12px; font-weight: 700; text-decoration: underline; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fa-brands fa-youtube"></i> Watch Video
                                                </a>
                                            </td>
                                            <td>
                                                <button type="button" onclick="toggleLiveVideoStatus('<?= htmlspecialchars($lv['id']) ?>', '<?= htmlspecialchars($lv['status'] ?? 'Active') ?>')" class="badge-status <?= htmlspecialchars($lv['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($lv['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editLiveVideo(<?= htmlspecialchars(json_encode($lv), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Video">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteLiveVideo('<?= htmlspecialchars($lv['id']) ?>')" title="Delete Video">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- LIVE VIDEOS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="liveVideosPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="liveVideosPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 season videos</span>
                            <div id="liveVideosPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: MARQUEE ANNOUNCEMENT STRIP MANAGEMENT -->
                <div class="section-panel" id="sectionAnnouncements">
                    <!-- TABLE CARD -->
                    <div class="table-card">
                        <div class="table-head">
                            <div>
                                <h2><i class="fa-solid fa-bullhorn" style="color: #ea580c;"></i> Marquee Announcement Strip Management</h2>
                                <p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">Manage breaking updates, tournament alerts, registration notices, and clickable action links on the top announcement ticker.</p>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <input type="text" id="adminAnnSearchInput" placeholder="Search announcements..." onkeyup="filterAdminAnnouncements()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; width: 200px;">
                                <select id="adminAnnStatusFilter" onchange="filterAdminAnnouncements()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px; font-weight: 600; background: #fff;">
                                    <option value="all">All Statuses</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                </select>
                                <button type="button" class="export-btn" style="background: #ea580c;" onclick="openAnnouncementModal()">
                                    <i class="fa-solid fa-plus"></i> Add Announcement
                                </button>
                            </div>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Order</th>
                                    <th style="width: 140px;">Tag / Badge</th>
                                    <th>Announcement Message</th>
                                    <th>Action Link &amp; Target</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="announcementsTbody">
                                <?php if (empty($announcements)): ?>
                                    <tr id="noAnnRow">
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                            <i class="fa-solid fa-bullhorn" style="font-size: 32px; display: block; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            No announcements added yet. Click <strong>"Add Announcement"</strong> to add an alert.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($announcements as $ann): ?>
                                        <tr id="ann-row-<?= htmlspecialchars($ann['id']) ?>" class="ann-admin-row" data-status="<?= htmlspecialchars(strtolower($ann['status'] ?? 'active')) ?>" data-search="<?= strtolower(htmlspecialchars(($ann['badgeText'] ?? '') . ' ' . ($ann['text'] ?? '') . ' ' . ($ann['linkUrl'] ?? '') . ' ' . ($ann['linkText'] ?? ''))) ?>">
                                            <td style="text-align: center;">
                                                <span style="display: inline-block; min-width: 28px; padding: 3px 8px; border-radius: 6px; background: #ffedd5; color: #c2410c; font-weight: 700; font-size: 13px;">
                                                    <?= (int)($ann['sortOrder'] ?? 1) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 11.5px; display: inline-block; text-transform: uppercase;">
                                                    <?= htmlspecialchars($ann['badgeText'] ?? 'ANNOUNCEMENT') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong style="color: #0f172a; font-size: 14px; line-height: 1.5; display: block; max-width: 480px;"><?= htmlspecialchars($ann['text']) ?></strong>
                                                <small style="color: #64748b; font-size: 11.5px;"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($ann['createdAt'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <?php if (!empty($ann['linkUrl'])): ?>
                                                    <div style="display: flex; flex-direction: column; gap: 3px;">
                                                        <a href="<?= htmlspecialchars($ann['linkUrl']) ?>" target="_blank" style="color: #2563eb; font-size: 12px; font-weight: 600; text-decoration: underline; max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                            <i class="fa-solid fa-link"></i> <?= htmlspecialchars($ann['linkUrl']) ?>
                                                        </a>
                                                        <div style="display: flex; gap: 6px; align-items: center;">
                                                            <span style="background: #f1f5f9; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 700; color: #334155;">
                                                                Btn: <?= htmlspecialchars($ann['linkText'] ?: 'View Details') ?>
                                                            </span>
                                                            <?php if (!empty($ann['openInNewTab'])): ?>
                                                                <span style="font-size: 10.5px; color: #059669; font-weight: 700;"><i class="fa-solid fa-arrow-up-right-from-square"></i> New Tab</span>
                                                            <?php else: ?>
                                                                <span style="font-size: 10.5px; color: #64748b;">Same Tab</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 12px;">No Link</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" onclick="toggleAnnouncementStatus('<?= htmlspecialchars($ann['id']) ?>', '<?= htmlspecialchars($ann['status'] ?? 'Active') ?>')" class="badge-status <?= htmlspecialchars($ann['status'] ?? 'Active') ?>" style="cursor: pointer; border: none;" title="Click to Toggle Status">
                                                    <?= htmlspecialchars($ann['status'] ?? 'Active') ?>
                                                </button>
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <button type="button" class="btn-act edit-btn" onclick="editAnnouncement(<?= htmlspecialchars(json_encode($ann), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Announcement">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </button>
                                                <button type="button" class="btn-act reject" onclick="deleteAnnouncement('<?= htmlspecialchars($ann['id']) ?>')" title="Delete Announcement">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- ANNOUNCEMENTS AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="announcementsPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="announcementsPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 announcements</span>
                            <div id="announcementsPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: CONTACT MESSAGES -->
                <div class="section-panel" id="sectionMessages">
                    <div class="table-card">
                        <div class="table-head">
                            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap; width: 100%;">
                                <h2><i class="fa-solid fa-inbox"></i> Contact Us Form Submissions</h2>
                                <input type="text" id="adminMessageSearchInput" onkeyup="filterAdminMessages()" placeholder="Search sender name, email, phone, subject..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 260px; background: #fff; margin-left: auto;">
                            </div>
                        </div>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Contact Details</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="messagesTbody">
                                <?php if (empty($messages)): ?>
                                    <tr id="noMsgRow">
                                        <td colspan="6" style="text-align: center; padding: 30px; color: #94a3b8;">
                                            No contact messages submitted yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($messages as $m): ?>
                                        <tr id="msg-row-<?= htmlspecialchars($m['id']) ?>" data-search="<?= strtolower(htmlspecialchars(($m['fullName'] ?? '') . ' ' . ($m['email'] ?? '') . ' ' . ($m['phone'] ?? '') . ' ' . ($m['subject'] ?? '') . ' ' . ($m['message'] ?? ''))) ?>">
                                            <td><small style="color: #64748b; font-weight: 600;"><?= htmlspecialchars($m['submittedAt']) ?></small></td>
                                            <td><strong><?= htmlspecialchars($m['fullName']) ?></strong></td>
                                            <td>
                                                <i class="fa-solid fa-envelope" style="color: var(--primary);"></i> <?= htmlspecialchars($m['email']) ?><br>
                                                <?php if (!empty($m['phone'])): ?>
                                                    <i class="fa-solid fa-phone" style="color: #10b981;"></i> <?= htmlspecialchars($m['phone']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><span style="font-weight: 600; color: #334155;"><?= htmlspecialchars($m['subject']) ?></span></td>
                                            <td style="max-width: 300px; line-height: 1.5; color: #475569;">
                                                <div style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word; font-size: 13.5px; color: #334155; line-height: 1.45; margin-bottom: 4px;">
                                                    <?= htmlspecialchars($m['message'] ?? '') ?>
                                                </div>
                                                <button type="button" onclick="viewMessageDetails(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" style="background: transparent; border: none; padding: 0; color: #2563eb; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fa-regular fa-eye"></i> View Full Message
                                                </button>
                                            </td>
                                             <td style="text-align: center; white-space: nowrap;">
                                                 <button type="button" onclick="viewMessageDetails(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" class="btn-act" style="background: #0284c7; color: #fff;" title="View Details">
                                                     <i class="fa-solid fa-eye"></i> View
                                                 </button>
                                                 <button type="button" onclick="openReplyComposer(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)" class="btn-act" style="background: #2563eb; color: #fff;" title="Reply via Email / Gmail">
                                                     <i class="fa-solid fa-reply"></i> Reply
                                                 </button>
                                                 <button class="btn-act reject" onclick="deleteMessage('<?= htmlspecialchars($m['id']) ?>')" title="Delete Message">
                                                     <i class="fa-solid fa-trash"></i> Delete
                                                 </button>
                                             </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- MESSAGES AUTOMATIC PAGINATION BAR -->
                        <div class="pagination-wrapper" id="messagesPaginationWrapper" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; flex-wrap: wrap; gap: 10px;">
                            <span id="messagesPageInfoText" style="font-size: 13px; color: #64748b; font-weight: 600;">Showing 1-10 messages</span>
                            <div id="messagesPaginationButtons" style="display: flex; gap: 6px; align-items: center;"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: CONTACT INFO SETTINGS -->
                <div class="section-panel" id="sectionContactSettings">
                    <div class="settings-form-card">
                        <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                            <h2 style="font-size: 18px; color: var(--primary);"><i class="fa-solid fa-sliders"></i> Contact Us Page Settings</h2>
                            <p style="color: #64748b; font-size: 13.5px; margin-top: 4px;">Update contact details, official office address, map embed, and social links displayed across the website.</p>
                        </div>

                        <form id="contactSettingsForm" onsubmit="saveContactSettings(event)">
                            <div class="modal-grid-2">
                                <div class="input-group">
                                    <label><i class="fa-solid fa-phone" style="color: #10b981;"></i> Official Phone Number</label>
                                    <input type="text" name="phone" id="settingPhone" value="<?= htmlspecialchars($contactSettings['phone'] ?? '') ?>" placeholder="+91 7084900009" required>
                                </div>
                                <div class="input-group">
                                    <label><i class="fa-solid fa-envelope" style="color: #2563eb;"></i> Official Email Address</label>
                                    <input type="email" name="email" id="settingEmail" value="<?= htmlspecialchars($contactSettings['email'] ?? '') ?>" placeholder="uphandballleague@gmail.com" required>
                                </div>
                            </div>

                            <div class="input-group">
                                <label><i class="fa-solid fa-location-dot" style="color: var(--orange);"></i> Office Location / Address</label>
                                <textarea name="address" id="settingAddress" rows="3" required placeholder="D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI..."><?= htmlspecialchars($contactSettings['address'] ?? '') ?></textarea>
                            </div>

                            <div class="input-group">
                                <label><i class="fa-solid fa-map-location-dot" style="color: #8b5cf6;"></i> Google Map Embed URL (iframe src)</label>
                                <input type="text" name="mapEmbed" id="settingMapEmbed" value="<?= htmlspecialchars($contactSettings['mapEmbed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                                <small style="color: #64748b; font-size: 12px;">Paste the 'src' link from Google Maps Embed iframe code.</small>
                            </div>

                            <h4 style="color: var(--primary); margin: 20px 0 12px; font-size: 15px;"><i class="fa-solid fa-share-nodes"></i> Official Social Media Channels</h4>
                            
                            <div class="modal-grid-2">
                                <div class="input-group">
                                    <label><i class="fa-brands fa-facebook" style="color: #1877f2;"></i> Facebook URL</label>
                                    <input type="url" name="facebook" id="settingFacebook" value="<?= htmlspecialchars($contactSettings['facebook'] ?? '') ?>" placeholder="https://facebook.com/upprohandballleague">
                                </div>
                                <div class="input-group">
                                    <label><i class="fa-brands fa-instagram" style="color: #e4405f;"></i> Instagram URL</label>
                                    <input type="url" name="instagram" id="settingInstagram" value="<?= htmlspecialchars($contactSettings['instagram'] ?? '') ?>" placeholder="https://instagram.com/upprohandballleague">
                                </div>
                            </div>

                            <div class="input-group">
                                <label><i class="fa-brands fa-youtube" style="color: #ff0000;"></i> YouTube Channel URL</label>
                                <input type="url" name="youtube" id="settingYoutube" value="<?= htmlspecialchars($contactSettings['youtube'] ?? '') ?>" placeholder="https://www.youtube.com/@sportscastindia">
                            </div>

                            <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                                <button type="submit" id="saveContactSettingsBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 12px 28px; font-size: 14px;">
                                    <i class="fa-solid fa-floppy-disk"></i> Save Contact Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- SECTION: HOME HERO STATS COUNTER -->
                <div class="section-panel" id="sectionHomeStats">
                    <div class="settings-form-card" style="max-width: 900px;">
                        <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h2 style="font-size: 18px; color: var(--primary);"><i class="fa-solid fa-chart-simple" style="color: #0ea5e9;"></i> Home Hero Stats Counter</h2>
                                <p style="color: #64748b; font-size: 13.5px; margin-top: 4px;">Customize the 4 animated statistics counters shown directly beneath the home page hero banner.</p>
                            </div>
                        </div>

                        <form id="homeStatsForm" onsubmit="saveHomeStatsSettings(event)">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                <?php foreach ($homeStats as $idx => $st): 
                                    $sNum = $idx + 1;
                                    $targetVal = htmlspecialchars($st['target'] ?? '0');
                                    $suffixVal = htmlspecialchars($st['suffix'] ?? '');
                                    $decimalVal = (int)($st['decimals'] ?? 0);
                                    $labelVal = htmlspecialchars($st['label'] ?? '');
                                    $iconVal = htmlspecialchars($st['icon'] ?? 'fa-solid fa-chart-simple');
                                ?>
                                <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px; position: relative;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #cbd5e1; padding-bottom: 10px;">
                                        <span style="font-weight: 800; font-size: 14px; color: var(--primary); display: inline-flex; align-items: center; gap: 8px;">
                                            <span style="background: var(--primary); color: #fff; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;"><?= $sNum ?></span>
                                            Counter #<?= $sNum ?>
                                        </span>
                                        <i class="<?= $iconVal ?>" style="color: #64748b; font-size: 16px;"></i>
                                    </div>

                                    <div class="input-group">
                                        <label>Label / Title <span style="color: var(--red);">*</span></label>
                                        <input type="text" name="label_<?= $sNum ?>" value="<?= $labelVal ?>" placeholder="e.g. Franchise Teams" required>
                                    </div>

                                    <div class="modal-grid-2">
                                        <div class="input-group">
                                            <label>Target Number <span style="color: var(--red);">*</span></label>
                                            <input type="text" name="target_<?= $sNum ?>" value="<?= $targetVal ?>" placeholder="e.g. 6 or 120 or 1.5" required>
                                        </div>
                                        <div class="input-group">
                                            <label>Suffix (e.g. +, L+, %)</label>
                                            <input type="text" name="suffix_<?= $sNum ?>" value="<?= $suffixVal ?>" placeholder="e.g. + or L+">
                                        </div>
                                    </div>

                                    <div class="modal-grid-2">
                                        <div class="input-group">
                                            <label>Decimals (0 or 1)</label>
                                            <select name="decimals_<?= $sNum ?>">
                                                <option value="0" <?= $decimalVal === 0 ? 'selected' : '' ?>>0 (Integer, e.g. 120)</option>
                                                <option value="1" <?= $decimalVal === 1 ? 'selected' : '' ?>>1 (Decimal, e.g. 1.5)</option>
                                            </select>
                                        </div>
                                        <div class="input-group">
                                            <label>FontAwesome Icon Class</label>
                                            <input type="text" name="icon_<?= $sNum ?>" value="<?= $iconVal ?>" placeholder="fa-solid fa-trophy">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                                <button type="submit" id="saveHomeStatsBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 12px 28px; font-size: 14px;">
                                    <i class="fa-solid fa-floppy-disk"></i> Save Home Statistics
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

<!-- ADD / EDIT STANDINGS TEAM MODAL -->
<div class="admin-modal-backdrop" id="standingsModal">
    <div class="admin-modal-card">
        <div class="admin-modal-head">
            <h3 id="standingsModalTitle"><i class="fa-solid fa-table-list" style="color: #eab308;"></i> Add Team Standings</h3>
            <button class="admin-modal-close" onclick="closeStandingsModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="standingsForm" onsubmit="saveStandingsForm(event)">
                <input type="hidden" name="rowId" id="standingsRowId">

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="standingsSeason" required list="standingsSeasonList" placeholder="e.g. Season 2" value="Season 2">
                        <datalist id="standingsSeasonList">
                            <option value="Season 2">
                            <option value="Season 1">
                            <option value="Season 3">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Team Name <span style="color: var(--red);">*</span></label>
                        <input type="text" name="teamName" id="standingsTeamName" required list="teamListOptions" placeholder="Select or type team name">
                        <datalist id="teamListOptions">
                            <option value="Barbarik Warriors">
                            <option value="Ghaziabad Panthers">
                            <option value="Gorakhpur Rowdies">
                            <option value="Kashi Kings">
                            <option value="Mathura Brij Star">
                            <option value="Noida Blasters">
                        </datalist>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Played (P)</label>
                        <input type="number" name="played" id="standingsPlayed" min="0" value="0" oninput="autoCalcPts()">
                    </div>
                    <div class="input-group">
                        <label>Won (W)</label>
                        <input type="number" name="won" id="standingsWon" min="0" value="0" oninput="autoCalcPts()">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Draw / Tied (D)</label>
                        <input type="number" name="draw" id="standingsDraw" min="0" value="0" oninput="autoCalcPts()">
                    </div>
                    <div class="input-group">
                        <label>Lost (L)</label>
                        <input type="number" name="lost" id="standingsLost" min="0" value="0" oninput="autoCalcPts()">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Goals For (GF)</label>
                        <input type="number" name="gf" id="standingsGf" min="0" value="0">
                    </div>
                    <div class="input-group">
                        <label>Goals Against (GA)</label>
                        <input type="number" name="ga" id="standingsGa" min="0" value="0">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Points (PTS) <small style="color: #64748b;">(Auto: W*2 + D)</small></label>
                        <input type="number" name="pts" id="standingsPts" min="0" value="0">
                    </div>
                    <div class="input-group">
                        <label>Recent Form (comma-separated)</label>
                        <input type="text" name="form" id="standingsFormStreak" placeholder="e.g. W,W,L,D,W" value="W,W,W,D,W">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeStandingsModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveStandingsSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Team Standings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT MATCH FIXTURE MODAL -->
<div class="admin-modal-backdrop" id="fixtureModal">
    <div class="admin-modal-card" style="max-width: 700px;">
        <div class="admin-modal-head">
            <h3 id="fixtureModalTitle"><i class="fa-solid fa-calendar-plus" style="color: #3b82f6;"></i> Add New Match Fixture</h3>
            <button class="admin-modal-close" onclick="closeFixtureModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="fixtureForm" onsubmit="saveFixtureForm(event)">
                <input type="hidden" name="fixtureId" id="fixtureId">

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="fixtureSeason" required list="fixtureSeasonSuggestions" placeholder="e.g. Season 2" value="Season 2">
                        <datalist id="fixtureSeasonSuggestions">
                            <option value="Season 2">
                            <option value="Season 1">
                            <option value="Season 3">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Match Day <span style="color: var(--red);">*</span></label>
                        <input type="text" name="matchDay" id="fixtureMatchDay" required list="fixtureDaySuggestions" placeholder="e.g. Day 1, Day 2" value="Day 1">
                        <datalist id="fixtureDaySuggestions">
                            <option value="Day 1">
                            <option value="Day 2">
                            <option value="Day 3">
                            <option value="Day 4">
                            <option value="Day 5">
                            <option value="Day 6">
                            <option value="Day 7">
                            <option value="Semi-Final Day">
                            <option value="Final Day">
                        </datalist>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Match Number <span style="color: var(--red);">*</span></label>
                        <input type="text" name="matchNumber" id="fixtureMatchNumber" required list="fixtureNumberSuggestions" placeholder="e.g. Match 1, Match 2" value="Match 1">
                        <datalist id="fixtureNumberSuggestions">
                            <option value="Match 1">
                            <option value="Match 2">
                            <option value="Match 3">
                            <option value="Match 4">
                            <option value="Semi-Final 1">
                            <option value="Semi-Final 2">
                            <option value="Grand Final">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Match Stage / Title</label>
                        <input type="text" name="matchTitle" id="fixtureMatchTitle" placeholder="e.g. League Stage" value="League Stage">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Team 1 <span style="color: var(--red);">*</span></label>
                        <input type="text" name="team1Name" id="fixtureTeam1Name" required list="team1Options" placeholder="Select or type Team 1">
                        <datalist id="team1Options">
                            <option value="Barbarik Warriors">
                            <option value="Ghaziabad Panthers">
                            <option value="Gorakhpur Rowdies">
                            <option value="Kashi Kings">
                            <option value="Mathura Brij Star">
                            <option value="Noida Blasters">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Team 2 <span style="color: var(--red);">*</span></label>
                        <input type="text" name="team2Name" id="fixtureTeam2Name" required list="team2Options" placeholder="Select or type Team 2">
                        <datalist id="team2Options">
                            <option value="Ghaziabad Panthers">
                            <option value="Barbarik Warriors">
                            <option value="Gorakhpur Rowdies">
                            <option value="Kashi Kings">
                            <option value="Mathura Brij Star">
                            <option value="Noida Blasters">
                        </datalist>
                    </div>
                </div>

                <div class="modal-grid-3" style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 15px;">
                    <div class="input-group">
                        <label>Match Date <span style="color: var(--red);">*</span></label>
                        <input type="date" name="matchDate" id="fixtureMatchDate" required>
                    </div>

                    <div class="input-group">
                        <label>Start Time <span style="color: var(--red);">*</span></label>
                        <input type="text" name="startTime" id="fixtureStartTime" required placeholder="e.g. 02:15 PM" value="02:15 PM">
                    </div>

                    <div class="input-group">
                        <label>End Time</label>
                        <input type="text" name="endTime" id="fixtureEndTime" placeholder="e.g. 03:30 PM" value="03:30 PM">
                    </div>
                </div>

                <div class="input-group">
                    <label>Stadium / Venue Location <span style="color: var(--red);">*</span></label>
                    <input type="text" name="stadium" id="fixtureStadium" required list="stadiumSuggestions" placeholder="e.g. K.D. Singh Babu Stadium, Lucknow" value="K.D. Singh Babu Stadium, Lucknow">
                    <datalist id="stadiumSuggestions">
                        <option value="K.D. Singh Babu Stadium, Lucknow">
                        <option value="Green Park Stadium, Kanpur">
                        <option value="Babu Banarasi Das Indoor Stadium, Lucknow">
                        <option value="Varanasi Sports Complex, Varanasi">
                    </datalist>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Match Status</label>
                        <select name="status" id="fixtureStatus">
                            <option value="Auto">Auto (Auto-detect by Date & Time)</option>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Live">Live (Ongoing)</option>
                            <option value="Completed">Completed</option>
                            <option value="Postponed">Postponed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <small style="color: #64748b; font-size: 11.5px;">Auto will automatically switch match to 'Completed' once the date/time passes.</small>
                    </div>

                    <div class="input-group">
                        <label>Scores (Optional)</label>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <input type="number" name="team1Score" id="fixtureTeam1Score" placeholder="T1 Score" min="0" style="flex: 1;">
                            <span style="font-weight: 800; color: #94a3b8;">-</span>
                            <input type="number" name="team2Score" id="fixtureTeam2Score" placeholder="T2 Score" min="0" style="flex: 1;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeFixtureModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveFixtureSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Match Fixture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT MVP PLAYER MODAL -->
<div class="admin-modal-backdrop" id="mvpModal">
    <div class="admin-modal-card" style="max-width: 700px;">
        <div class="admin-modal-head">
            <h3 id="mvpModalTitle"><i class="fa-solid fa-award" style="color: #f59e0b;"></i> Add New MVP Player</h3>
            <button class="admin-modal-close" onclick="closeMvpModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="mvpForm" onsubmit="saveMvpForm(event)" enctype="multipart/form-data">
                <input type="hidden" name="mvpId" id="mvpId">
                <input type="hidden" name="existingPhoto" id="mvpExistingPhoto">

                <div class="modal-grid-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="mvpSeason" required list="mvpSeasonSuggestions" placeholder="e.g. Season 1" value="Season 1">
                        <datalist id="mvpSeasonSuggestions">
                            <option value="Season 1">
                            <option value="Season 2">
                            <option value="Season 3">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Match Day <span style="color: var(--red);">*</span></label>
                        <input type="text" name="matchDay" id="mvpMatchDay" required list="mvpDaySuggestions" placeholder="e.g. Day 1, Day 2" value="Day 1">
                        <datalist id="mvpDaySuggestions">
                            <option value="Day 1">
                            <option value="Day 2">
                            <option value="Day 3">
                            <option value="Day 4">
                            <option value="Day 5">
                            <option value="Semi-Final Day">
                            <option value="Final Day">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Match Number <span style="color: var(--red);">*</span></label>
                        <input type="text" name="matchNumber" id="mvpMatchNumber" required list="mvpNumberSuggestions" placeholder="e.g. Match 1, Match 2" value="Match 1">
                        <datalist id="mvpNumberSuggestions">
                            <option value="Match 1">
                            <option value="Match 2">
                            <option value="Match 3">
                            <option value="Match 4">
                            <option value="Semi-Final 1">
                            <option value="Semi-Final 2">
                            <option value="Grand Final">
                        </datalist>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Player Name <span style="color: var(--red);">*</span></label>
                        <input type="text" name="playerName" id="mvpPlayerName" required placeholder="e.g. Aditya Pratap">
                    </div>

                    <div class="input-group">
                        <label>Player's Franchise Team <span style="color: var(--red);">*</span></label>
                        <input type="text" name="teamName" id="mvpTeamName" required list="mvpTeamOptions" placeholder="Select or type Franchise Team">
                        <datalist id="mvpTeamOptions">
                            <option value="Barbarik Warriors">
                            <option value="Ghaziabad Panthers">
                            <option value="Gorakhpur Rowdies">
                            <option value="Kashi Kings">
                            <option value="Mathura Brij Star">
                            <option value="Noida Blasters">
                        </datalist>
                    </div>
                </div>

                <div class="modal-grid-3" style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 15px;">
                    <div class="input-group">
                        <label>Award / Title</label>
                        <input type="text" name="awardTitle" id="mvpAwardTitle" list="mvpAwardSuggestions" placeholder="e.g. Player of the Match" value="Player of the Match">
                        <datalist id="mvpAwardSuggestions">
                            <option value="Player of the Match">
                            <option value="Tournament MVP">
                            <option value="Golden Arm (Top Scorer)">
                            <option value="Golden Glove (Best GK)">
                            <option value="Best Defender (Wall of UP)">
                            <option value="Playmaker of Match">
                            <option value="Fast Break Specialist">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Jersey Number</label>
                        <input type="text" name="jerseyNumber" id="mvpJerseyNumber" placeholder="e.g. #07">
                    </div>

                    <div class="input-group">
                        <label>Position / Role</label>
                        <input type="text" name="position" id="mvpPosition" placeholder="e.g. Centre Back">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Goals / Points Scored</label>
                        <input type="text" name="goals" id="mvpGoals" placeholder="e.g. 11 Goals or 8 Blocks">
                    </div>

                    <div class="input-group">
                        <label>Performance Rating</label>
                        <input type="text" name="rating" id="mvpRating" placeholder="e.g. 9.4">
                    </div>
                </div>

                <div class="input-group">
                    <label>Player Photo (Optional)</label>
                    <input type="file" name="photo" id="mvpPhotoInput" accept="image/*" onchange="handleMvpPhotoSelect(this)">
                    <div id="mvpPhotoPreviewWrap" style="display: none; align-items: center; gap: 10px; margin-top: 8px;">
                        <img id="mvpPhotoPreview" src="" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #f59e0b;" alt="Preview">
                        <span style="font-size: 12px; color: #64748b;">Photo selected for upload</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeMvpModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveMvpSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save MVP Player
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT GALLERY PHOTO MODAL -->
<div class="admin-modal-backdrop" id="galleryModal">
    <div class="admin-modal-card" style="max-width: 620px;">
        <div class="admin-modal-head">
            <h3 id="galleryModalTitle"><i class="fa-solid fa-camera-retro" style="color: var(--orange);"></i> Add Gallery Photos</h3>
            <button class="admin-modal-close" onclick="closeGalleryModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="galleryForm" onsubmit="saveGalleryForm(event)" enctype="multipart/form-data">
                <input type="hidden" name="photoId" id="galleryPhotoId">
                <input type="hidden" name="existingImageUrl" id="galleryExistingImageUrl">

                <!-- Image Upload Area (Single or Multiple) -->
                <div class="input-group">
                    <label id="galleryUploadLabel">Select Photo(s) <span style="color: var(--red);">*</span> <small style="color: #64748b; font-weight: normal;">(Upload single or multiple photos at once)</small></label>
                    <div class="upload-dropzone" style="position: relative;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary); margin-bottom: 8px;"></i>
                        <p style="font-weight: 600; font-size: 14px; color: #334155;">Click or Drag &amp; Drop photo(s) here</p>
                        <small style="color: #64748b; font-size: 12px;">Supports JPG, PNG, WEBP, AVIF, GIF (Single or Multiple selection)</small>
                        <input type="file" name="photoFiles[]" id="galleryPhotoFiles" multiple accept="image/*" onchange="handleGalleryMultiFileSelect(this)">
                    </div>

                    <!-- Multi Image Preview Container -->
                    <div id="galleryMultiPreviewContainer" style="display: none; margin-top: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 13px; font-weight: 700; color: #334155;">Selected Photos Preview:</span>
                            <span id="gallerySelectedCount" class="badge" style="background: rgba(30,58,138,0.1); color: var(--primary); font-size: 12px;">0 Selected</span>
                        </div>
                        <div id="galleryThumbsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 8px; max-height: 200px; overflow-y: auto; padding: 8px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>Photo Title / Caption <small style="color: #64748b; font-weight: normal;">(Optional - leave empty for clean image cards)</small></label>
                    <input type="text" name="title" id="galleryTitle" placeholder="Optional caption (e.g. Trophy Unveiling)">
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="gallerySeason" required list="seasonSuggestions" placeholder="e.g. Season 2" value="Season 2">
                        <datalist id="seasonSuggestions">
                            <option value="Season 2">
                            <option value="Season 1">
                            <option value="Season 3">
                        </datalist>
                        <small style="color: #64748b; font-size: 11.5px;">Type or select season (e.g. Season 1, Season 2).</small>
                    </div>

                    <div class="input-group">
                        <label>Category / Section <span style="color: var(--red);">*</span></label>
                        <select name="category" id="galleryCategory" required>
                            <option value="announcement">Announcement Day</option>
                            <option value="trail">Trails</option>
                            <option value="trophy">Trophy Launch</option>
                            <option value="auction">Auction</option>
                            <option value="glimpses" selected>Match Glimpses</option>
                            <option value="news">News</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeGalleryModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveGallerySubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload &amp; Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT BANNER MODAL -->
<div class="admin-modal-backdrop" id="bannerModal">
    <div class="admin-modal-card">
        <div class="admin-modal-head">
            <h3 id="bannerModalTitle"><i class="fa-solid fa-panorama" style="color: var(--orange);"></i> Add New Hero Banner</h3>
            <button class="admin-modal-close" onclick="closeBannerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="bannerForm" onsubmit="saveBannerForm(event)">
                <input type="hidden" name="bannerId" id="bannerId">
                <input type="hidden" name="existingMediaUrl" id="existingMediaUrl">
                <input type="hidden" name="existingMediaType" id="existingMediaType">

                <!-- Media Upload Area -->
                <div class="input-group">
                    <label>Banner Media (Image or Video - All Formats Supported) <span style="color: var(--red);">*</span></label>
                    <div class="upload-dropzone" id="uploadDropzone">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary); margin-bottom: 8px;"></i>
                        <p style="font-weight: 600; font-size: 14px; color: #334155;">Click or Drag & Drop media file here</p>
                        <small style="color: #64748b; font-size: 12px;">Supports JPG, PNG, WEBP, GIF, SVG, MP4, WEBM, MOV, MKV, AVI, etc.</small>
                        <input type="file" name="mediaFile" id="bannerMediaFile" accept="image/*,video/*" onchange="handleBannerFileSelect(this)">
                    </div>

                    <!-- Media Live Preview Box -->
                    <div id="mediaPreviewContainer" class="upload-preview-box" style="display: none;">
                        <img id="imagePreview" src="" alt="Preview" style="display: none;">
                        <video id="videoPreview" src="" controls style="display: none;"></video>
                    </div>
                </div>

                <div class="input-group">
                    <label>Badge / Tag Text (Optional)</label>
                    <input type="text" name="badgeText" id="badgeText" placeholder="e.g. SEASON 2 • UP PRO HANDBALL">
                </div>

                <div class="input-group">
                    <label>Heading Text <span style="color: var(--red);">*</span></label>
                    <input type="text" name="heading" id="bannerHeading" required placeholder="e.g. UTTAR PRADESH PRO HANDBALL LEAGUE">
                </div>

                <div class="input-group">
                    <label>Subtitle / Description Text</label>
                    <textarea name="subtitle" id="bannerSubtitle" rows="3" placeholder="e.g. Experience the thrill, energy, and passion of the biggest handball tournament in Uttar Pradesh."></textarea>
                </div>

                <div class="modal-grid-2" style="margin-bottom: 0;">
                    <div class="input-group">
                        <label>Button 1 Name (Optional)</label>
                        <input type="text" name="btn1Text" id="btn1Text" placeholder="e.g. View Fixtures">
                    </div>
                    <div class="input-group">
                        <label>Button 1 Link (Optional)</label>
                        <input type="text" name="btn1Link" id="btn1Link" placeholder="e.g. fixtures.php">
                    </div>
                </div>

                <div class="modal-grid-2" style="margin-bottom: 0;">
                    <div class="input-group">
                        <label>Button 2 Name (Optional)</label>
                        <input type="text" name="btn2Text" id="btn2Text" placeholder="e.g. League Details">
                    </div>
                    <div class="input-group">
                        <label>Button 2 Link (Optional)</label>
                        <input type="text" name="btn2Link" id="btn2Link" placeholder="e.g. league-details.php">
                    </div>
                </div>

                <div class="modal-grid-2" style="margin-bottom: 0;">
                    <div class="input-group">
                        <label>Sort Order / Display Position</label>
                        <input type="number" name="sortOrder" id="bannerSortOrder" value="0" min="0" placeholder="e.g. 1, 2, 3">
                        <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">Lower numbers appear first on the website (e.g. 1, 2, 3).</small>
                    </div>
                    <div class="input-group">
                        <label>Status</label>
                        <select name="status" id="bannerStatus">
                            <option value="Active">Active (Visible on Homepage)</option>
                            <option value="Inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeBannerModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveBannerSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT INNER PAGE BANNER MODAL -->
<div class="admin-modal-backdrop" id="pageBannerModal">
    <div class="admin-modal-card">
        <div class="admin-modal-head">
            <h3 id="pageBannerModalTitle"><i class="fa-solid fa-image" style="color: var(--primary);"></i> Update Page Banner</h3>
            <button class="admin-modal-close" onclick="closePageBannerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="pageBannerForm" onsubmit="savePageBannerForm(event)">
                <input type="hidden" name="pageKey" id="pbPageKey">
                <input type="hidden" name="existingBannerImage" id="pbExistingBannerImage">

                <div class="input-group">
                    <label>Target Page</label>
                    <input type="text" id="pbPageDisplayName" readonly style="background: #f8fafc; font-weight: 700; color: #0f172a; cursor: not-allowed;">
                </div>

                <!-- Media Upload Area -->
                <div class="input-group">
                    <label>Banner Background Image <span style="color: var(--red);">*</span></label>
                    <div class="upload-dropzone" id="pbUploadDropzone">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 30px; color: var(--primary); margin-bottom: 6px;"></i>
                        <p style="font-weight: 600; font-size: 13.5px; color: #334155;">Click or Drag &amp; Drop new banner image here</p>
                        <small style="color: #64748b; font-size: 12px;">Supports JPG, PNG, WEBP, GIF, SVG (Recommended: 1920x600 or 1600x500)</small>
                        <input type="file" name="bannerImageFile" id="pbBannerImageFile" accept="image/*" onchange="handlePageBannerFileSelect(this)">
                    </div>

                    <!-- Media Live Preview Box -->
                    <div id="pbMediaPreviewContainer" class="upload-preview-box" style="display: none; margin-top: 10px;">
                        <img id="pbImagePreview" src="" alt="Preview" style="max-height: 180px; width: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                    </div>
                </div>

                <div class="input-group">
                    <label>Badge / Tag Text (Optional)</label>
                    <input type="text" name="badgeText" id="pbBadgeText" placeholder="e.g. OFFICIAL LEAGUE DETAILS">
                </div>

                <div class="input-group">
                    <label>Banner Heading / Title <span style="color: var(--red);">*</span></label>
                    <input type="text" name="title" id="pbTitle" required placeholder="e.g. League Details">
                </div>

                <div class="input-group">
                    <label>Banner Subtitle / Description Text</label>
                    <textarea name="subtitle" id="pbSubtitle" rows="3" placeholder="Enter page banner subtitle or description..."></textarea>
                </div>

                <div class="input-group">
                    <label>Status</label>
                    <select name="status" id="pbStatus">
                        <option value="Active">Active (Display Custom Banner)</option>
                        <option value="Inactive">Inactive (Use Site Defaults)</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closePageBannerModal()">
                        Cancel
                    </button>
                    <button type="submit" id="savePageBannerSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Update Page Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT LATEST NEWS UPDATE MODAL -->
<div class="admin-modal-backdrop" id="newsModal">
    <div class="admin-modal-card" style="max-width: 650px;">
        <div class="admin-modal-head">
            <h3 id="newsModalTitle"><i class="fa-solid fa-newspaper" style="color: #e11d48;"></i> Add News Update</h3>
            <button class="admin-modal-close" onclick="closeNewsModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="newsForm" onsubmit="saveNewsForm(event)">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="newsId" id="newsId">
                <input type="hidden" name="existingImage" id="newsExistingImage">

                <div class="input-group">
                    <label>News Title <span style="color: var(--red);">*</span></label>
                    <input type="text" name="title" id="newsTitle" required placeholder="e.g. Season-2 Auctions Concluded">
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Display Date <span style="color: var(--red);">*</span></label>
                        <input type="text" name="date" id="newsDate" required placeholder="e.g. 6th August 2026" value="<?= date('jS F Y') ?>">
                    </div>
                    <div class="input-group">
                        <label>Category Badge (Optional)</label>
                        <input type="text" name="category" id="newsCategory" placeholder="e.g. Auctions, Scouting Trails, Trophy" list="newsCategorySuggestions" value="News">
                        <datalist id="newsCategorySuggestions">
                            <option value="News">
                            <option value="Auctions">
                            <option value="Scouting Trails">
                            <option value="Trophy Launch">
                            <option value="Announcement">
                            <option value="Match Glimpses">
                        </datalist>
                    </div>
                </div>

                <div class="input-group">
                    <label>News Description / Excerpt <span style="color: var(--red);">*</span></label>
                    <textarea name="description" id="newsDescription" rows="3" required placeholder="Short summary displayed on the card..." style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-family: inherit; font-size: 13.5px;"></textarea>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Gallery Redirect Link <span style="color: var(--red);">*</span></label>
                        <input type="text" name="galleryLink" id="newsGalleryLink" required placeholder="gallery.php?cat=news" value="gallery.php?cat=news" list="galleryLinkSuggestions">
                        <datalist id="galleryLinkSuggestions">
                            <option value="gallery.php?cat=news">
                            <option value="gallery.php?cat=trail">
                            <option value="gallery.php?cat=trophy">
                            <option value="gallery.php?cat=auction">
                            <option value="gallery.php?cat=glimpses">
                            <option value="gallery.php?cat=announcement">
                        </datalist>
                        <small style="color: #64748b; font-size: 11.5px;">Clicking "View Gallery" redirects to this link.</small>
                    </div>

                    <div class="input-group">
                        <label>Display Sort Order</label>
                        <input type="number" name="sortOrder" id="newsSortOrder" value="1" min="0">
                        <small style="color: #64748b; font-size: 11.5px;">Lowest number (e.g. 1) displays first.</small>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Status</label>
                        <select name="status" id="newsStatus">
                            <option value="Active">Active (Visible on Home Page)</option>
                            <option value="Inactive">Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Card Image <span id="newsImageRequiredStar" style="color: var(--red);">*</span></label>
                        <div class="upload-dropzone" onclick="document.getElementById('newsImageInput').click()" style="padding: 12px; cursor: pointer;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 22px; color: var(--primary); margin-bottom: 4px;"></i>
                            <p style="margin: 0; font-size: 12px; font-weight: 600; color: #1e293b;">Click or drop image file</p>
                            <small style="color: #64748b; font-size: 11px;">JPG, PNG, WEBP</small>
                            <input type="file" name="newsImage" id="newsImageInput" accept="image/*" onchange="previewNewsImageFile(this)" style="display: none;">
                        </div>
                    </div>
                </div>

                <div id="newsImagePreviewContainer" style="display: none; margin-top: 10px; padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                    <img id="newsImagePreviewImg" src="" style="max-height: 120px; border-radius: 6px; object-fit: cover; border: 1px solid #cbd5e1;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeNewsModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveNewsSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save News Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT PARTNER & SPONSOR MODAL -->
<div class="admin-modal-backdrop" id="partnerModal">
    <div class="admin-modal-card" style="max-width: 650px;">
        <div class="admin-modal-head">
            <h3 id="partnerModalTitle"><i class="fa-solid fa-handshake" style="color: #059669;"></i> Add Partner / Sponsor</h3>
            <button class="admin-modal-close" onclick="closePartnerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="partnerForm" onsubmit="savePartnerForm(event)">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="partnerId" id="partnerId">
                <input type="hidden" name="existingLogo" id="partnerExistingLogo">

                <div class="input-group">
                    <label>Partner / Sponsor Name <span style="color: var(--red);">*</span></label>
                    <input type="text" name="name" id="partnerName" required placeholder="e.g. Energetic Drinks, Sporting Goods Co.">
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Partner Category / Badge <span style="color: var(--red);">*</span></label>
                        <input type="text" name="partnerType" id="partnerType" required placeholder="e.g. Official Kit Partner, Title Sponsor" list="partnerTypeSuggestions" value="Official Sponsor">
                        <datalist id="partnerTypeSuggestions">
                            <option value="Title Sponsor">
                            <option value="Official Kit Partner">
                            <option value="Official Beverage">
                            <option value="Broadcasting Partner">
                            <option value="Nutrition Partner">
                            <option value="Equipment Partner">
                            <option value="Official Sponsor">
                            <option value="Associate Partner">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Display Sort Order</label>
                        <input type="number" name="sortOrder" id="partnerSortOrder" value="1" min="0">
                        <small style="color: #64748b; font-size: 11.5px;">Lowest number (e.g. 1) displays first in marquee.</small>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Website / Link URL (Optional)</label>
                        <input type="text" name="linkUrl" id="partnerLinkUrl" placeholder="https://example.com or #" value="#">
                    </div>

                    <div class="input-group">
                        <label>Status</label>
                        <select name="status" id="partnerStatus">
                            <option value="Active">Active (Visible on Home Page)</option>
                            <option value="Inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Partner Logo / Image <span id="partnerLogoRequiredStar" style="color: var(--red);">*</span></label>
                    <div class="upload-dropzone" onclick="document.getElementById('partnerLogoInput').click()" style="padding: 16px; cursor: pointer;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 26px; color: #059669; margin-bottom: 6px;"></i>
                        <p style="margin: 0; font-size: 13px; font-weight: 600; color: #1e293b;">Click or drop sponsor logo / image</p>
                        <small style="color: #64748b; font-size: 11.5px;">PNG, SVG, JPG, WEBP (Recommended transparent PNG / SVG)</small>
                        <input type="file" name="partnerLogo" id="partnerLogoInput" accept="image/*" onchange="previewPartnerLogoFile(this)" style="display: none;">
                    </div>
                </div>

                <div id="partnerLogoPreviewContainer" style="display: none; margin-top: 10px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                    <img id="partnerLogoPreviewImg" src="" style="max-height: 90px; border-radius: 6px; object-fit: contain;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closePartnerModal()">
                        Cancel
                    </button>
                    <button type="submit" id="savePartnerSubmitBtn" class="btn-act" style="background: #059669; color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Partner / Sponsor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT COMMITTEE MEMBER MODAL -->
<div class="admin-modal-backdrop" id="committeeModal">
    <div class="admin-modal-card" style="max-width: 650px;">
        <div class="admin-modal-head">
            <h3 id="committeeModalTitle"><i class="fa-solid fa-users-gear" style="color: #6366f1;"></i> Add Committee Member</h3>
            <button class="admin-modal-close" onclick="closeCommitteeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="committeeForm" onsubmit="saveCommitteeForm(event)">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="memberId" id="committeeMemberId">
                <input type="hidden" name="existingImage" id="committeeExistingImage">

                <div class="input-group">
                    <label>Member Full Name <span style="color: var(--red);">*</span></label>
                    <input type="text" name="name" id="committeeName" required placeholder="e.g. Amit Pandey">
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Badge Title (e.g. Founder &amp; MD, CEO, President)</label>
                        <input type="text" name="badge" id="committeeBadge" placeholder="e.g. Founder & MD, CEO, President" list="committeeBadgeSuggestions">
                        <datalist id="committeeBadgeSuggestions">
                            <option value="Founder & MD">
                            <option value="CEO">
                            <option value="Co-Founder">
                            <option value="Chief Patron">
                            <option value="President">
                            <option value="Chairman">
                            <option value="Trustee">
                            <option value="Vice President">
                            <option value="Secretary">
                            <option value="Member">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Display Sort Order</label>
                        <input type="number" name="sortOrder" id="committeeSortOrder" value="1" min="0">
                        <small style="color: #64748b; font-size: 11.5px;">Lowest number (e.g. 1) displays first.</small>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Designation <span style="color: var(--red);">*</span></label>
                        <input type="text" name="designation" id="committeeDesignation" required placeholder="e.g. Founder & Managing Director">
                    </div>

                    <div class="input-group">
                        <label>Sub-Designation / Organization</label>
                        <input type="text" name="subDesignation" id="committeeSubDesignation" placeholder="e.g. UP Pro Handball League" value="UP Pro Handball League">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Status</label>
                        <select name="status" id="committeeStatus">
                            <option value="Active">Active (Visible on Page)</option>
                            <option value="Inactive">Inactive (Hidden)</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Member Photo <span id="committeePhotoRequiredStar" style="color: var(--red);">*</span></label>
                        <div class="upload-dropzone" onclick="document.getElementById('committeePhotoInput').click()" style="padding: 14px; cursor: pointer;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #6366f1; margin-bottom: 4px;"></i>
                            <p style="margin: 0; font-size: 12px; font-weight: 600; color: #1e293b;">Click or drop member photo</p>
                            <small style="color: #64748b; font-size: 11px;">JPG, PNG, WEBP</small>
                            <input type="file" name="committeePhoto" id="committeePhotoInput" accept="image/*" onchange="previewCommitteePhotoFile(this)" style="display: none;">
                        </div>
                    </div>
                </div>

                <div id="committeePhotoPreviewContainer" style="display: none; margin-top: 10px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                    <img id="committeePhotoPreviewImg" src="" style="max-height: 120px; border-radius: 50%; width: 120px; height: 120px; object-fit: cover; border: 2px solid #cbd5e1;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeCommitteeModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveCommitteeSubmitBtn" class="btn-act" style="background: #6366f1; color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT LIVE BROADCAST PARTNER MODAL -->
<div class="admin-modal-backdrop" id="livePartnerModal">
    <div class="admin-modal-card" style="max-width: 680px;">
        <div class="admin-modal-head">
            <h3 id="livePartnerModalTitle"><i class="fa-solid fa-tower-broadcast" style="color: #dc2626;"></i> Add Broadcast Partner</h3>
            <button class="admin-modal-close" onclick="closeLivePartnerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="livePartnerForm" onsubmit="saveLivePartnerForm(event)" enctype="multipart/form-data">
                <input type="hidden" name="partnerId" id="livePartnerId">
                <input type="hidden" name="existingLogoUrl" id="livePartnerExistingLogo">

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Partner / Channel Name <span style="color: var(--red);">*</span></label>
                        <input type="text" name="name" id="livePartnerName" required placeholder="e.g. YouTube Live, DD Sports, FanCode">
                    </div>

                    <div class="input-group">
                        <label>Platform / Network Subtitle</label>
                        <input type="text" name="platformType" id="livePartnerPlatformType" placeholder="e.g. SportsCast India, National TV Network, Official OTT Partner">
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Badge Text</label>
                        <input type="text" name="badgeText" id="livePartnerBadgeText" placeholder="e.g. Live Stream, TV Partner, OTT Partner">
                    </div>

                    <div class="input-group">
                        <label>Meta Pill / Tag</label>
                        <input type="text" name="metaPill" id="livePartnerMetaPill" placeholder="e.g. Free HD Live, Free-To-Air TV, Mobile & App">
                    </div>
                </div>

                <div class="input-group">
                    <label>Watch / Stream URL <span style="color: var(--red);">*</span></label>
                    <input type="url" name="watchUrl" id="livePartnerWatchUrl" required placeholder="e.g. https://www.youtube.com/@sportscastindia">
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Button Text</label>
                        <input type="text" name="buttonText" id="livePartnerButtonText" placeholder="e.g. Watch Live, TV Guide, Watch Now" value="Watch Live">
                    </div>

                    <div class="input-group">
                        <label>Theme Card Style</label>
                        <select name="cardStyle" id="livePartnerCardStyle">
                            <option value="youtube-card">YouTube Red Theme</option>
                            <option value="ddsports-card">DD Sports Blue Theme</option>
                            <option value="fancode-card">FanCode Orange Theme</option>
                            <option value="custom-card">Neutral Dark Theme</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Short Description</label>
                    <textarea name="description" id="livePartnerDescription" rows="2" placeholder="e.g. Official high-definition live streaming of matches, analysis & highlights."></textarea>
                </div>

                <div class="modal-grid-3">
                    <div class="input-group">
                        <label>FontAwesome Icon Class</label>
                        <input type="text" name="iconClass" id="livePartnerIconClass" placeholder="fa-brands fa-youtube" value="fa-solid fa-tower-broadcast">
                    </div>

                    <div class="input-group">
                        <label>Display Sort Order</label>
                        <input type="number" name="sortOrder" id="livePartnerSortOrder" value="1" min="0">
                    </div>

                    <div class="input-group">
                        <label>Status</label>
                        <select name="status" id="livePartnerStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label>Partner Logo (Optional - upload if image preferred over icon)</label>
                    <div class="upload-dropzone" onclick="document.getElementById('livePartnerLogoInput').click()" style="padding: 14px; cursor: pointer;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #dc2626; margin-bottom: 4px;"></i>
                        <p style="margin: 0; font-size: 12px; font-weight: 600; color: #1e293b;">Click or drop partner logo</p>
                        <small style="color: #64748b; font-size: 11px;">PNG, JPG, SVG, WEBP</small>
                        <input type="file" name="logoFile" id="livePartnerLogoInput" accept="image/*" onchange="previewLivePartnerLogo(this)" style="display: none;">
                    </div>
                </div>

                <div id="livePartnerLogoPreviewContainer" style="display: none; margin-top: 10px; padding: 10px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                    <img id="livePartnerLogoPreviewImg" src="" style="max-height: 55px; max-width: 140px; object-fit: contain;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeLivePartnerModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveLivePartnerSubmitBtn" class="btn-act" style="background: #dc2626; color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Partner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT COMPLETED SEASON VIDEO MODAL -->
<div class="admin-modal-backdrop" id="liveVideoModal">
    <div class="admin-modal-card" style="max-width: 680px;">
        <div class="admin-modal-head">
            <h3 id="liveVideoModalTitle"><i class="fa-solid fa-film" style="color: #f97316;"></i> Add Completed Season Video</h3>
            <button class="admin-modal-close" onclick="closeLiveVideoModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="liveVideoForm" onsubmit="saveLiveVideoForm(event)" enctype="multipart/form-data">
                <input type="hidden" name="videoId" id="liveVideoId">
                <input type="hidden" name="existingThumbnailUrl" id="liveVideoExistingThumb">

                <div class="input-group">
                    <label>Video Title / Match Details <span style="color: var(--red);">*</span></label>
                    <input type="text" name="title" id="liveVideoTitle" required placeholder="e.g. UPPHL Season 1 Grand Finale: Barbarik Warriors vs Ghaziabad Panthers">
                </div>

                <div class="input-group">
                    <label>Video Link (YouTube URL / Share Link / MP4 Link) <span style="color: var(--red);">*</span></label>
                    <input type="text" name="videoUrl" id="liveVideoUrl" required placeholder="e.g. https://www.youtube.com/watch?v=dQw4w9WgXcQ or https://youtu.be/..." oninput="handleLiveVideoUrlInput(this.value)">
                    <small style="color: #64748b; font-size: 11.5px;">Supports full YouTube URLs or short share links (auto-extracts HD thumbnail &amp; embed player).</small>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="liveVideoSeason" required list="liveVideoSeasonList" placeholder="e.g. Season 1" value="Season 1">
                        <datalist id="liveVideoSeasonList">
                            <option value="Season 1">
                            <option value="Season 2">
                            <option value="Season 3">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Category <span style="color: var(--red);">*</span></label>
                        <select name="videoCategory" id="liveVideoCategory" required>
                            <option value="Full Match">Full Match Replay</option>
                            <option value="Highlights">Match Highlights</option>
                            <option value="Top Moments">Top Moments &amp; Saves</option>
                            <option value="Ceremony">Trophy / Opening Ceremony</option>
                        </select>
                    </div>
                </div>

                <div class="modal-grid-3">
                    <div class="input-group">
                        <label>Duration</label>
                        <input type="text" name="duration" id="liveVideoDuration" placeholder="e.g. 1h 45m or 14:20">
                    </div>

                    <div class="input-group">
                        <label>Match / Event Date</label>
                        <input type="date" name="matchDate" id="liveVideoMatchDate" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="input-group">
                        <label>Sort Order</label>
                        <input type="number" name="sortOrder" id="liveVideoSortOrder" value="1" min="0">
                    </div>
                </div>

                <div class="input-group">
                    <label>Status</label>
                    <select name="status" id="liveVideoStatus">
                        <option value="Active">Active (Visible on Live Page)</option>
                        <option value="Inactive">Inactive (Hidden)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Custom Thumbnail (Optional - auto-generated if YouTube URL is used)</label>
                    <div class="upload-dropzone" onclick="document.getElementById('liveVideoThumbInput').click()" style="padding: 14px; cursor: pointer;">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #f97316; margin-bottom: 4px;"></i>
                        <p style="margin: 0; font-size: 12px; font-weight: 600; color: #1e293b;">Click or drop custom thumbnail</p>
                        <small style="color: #64748b; font-size: 11px;">16:9 Landscape JPG, PNG, WEBP</small>
                        <input type="file" name="thumbnailFile" id="liveVideoThumbInput" accept="image/*" onchange="previewLiveVideoThumb(this)" style="display: none;">
                    </div>
                </div>

                <div id="liveVideoThumbPreviewContainer" style="display: none; margin-top: 10px; padding: 10px; background: #000; border-radius: 8px; text-align: center;">
                    <img id="liveVideoThumbPreviewImg" src="" style="max-height: 140px; border-radius: 6px; max-width: 100%; object-fit: cover;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeLiveVideoModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveLiveVideoSubmitBtn" class="btn-act" style="background: #f97316; color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Season Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT MARQUEE ANNOUNCEMENT MODAL -->
<div class="admin-modal-backdrop" id="announcementModal">
    <div class="admin-modal-card" style="max-width: 680px;">
        <div class="admin-modal-head">
            <h3 id="announcementModalTitle"><i class="fa-solid fa-bullhorn" style="color: #ea580c;"></i> Add Marquee Announcement</h3>
            <button class="admin-modal-close" onclick="closeAnnouncementModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="announcementForm" onsubmit="saveAnnouncementForm(event)">
                <input type="hidden" name="id" id="announcementId">

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Tag / Badge Text <span style="color: var(--red);">*</span></label>
                        <input type="text" name="badgeText" id="annBadgeText" required list="annBadgeSuggestions" placeholder="e.g. 📢 LATEST UPDATE" value="📢 LATEST UPDATE">
                        <datalist id="annBadgeSuggestions">
                            <option value="📢 LATEST UPDATE">
                            <option value="✦ TRIAL ALERT">
                            <option value="🏆 REGISTRATION OPEN">
                            <option value="⚡ MATCH NOTICE">
                            <option value="★ LEAGUE ANNOUNCEMENT">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Sort Order / Sequence</label>
                        <input type="number" name="sortOrder" id="annSortOrder" value="1" min="1">
                    </div>
                </div>

                <div class="input-group">
                    <label>Announcement Message Text <span style="color: var(--red);">*</span></label>
                    <textarea name="text" id="annText" rows="3" required placeholder="Type the announcement ticker message here (e.g. UP Pro Handball League Season 2 Registrations are now LIVE!)..."></textarea>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Action Link URL (Optional)</label>
                        <input type="text" name="linkUrl" id="annLinkUrl" placeholder="e.g. player-registration.php or https://...">
                        <small style="color: #64748b; font-size: 11px;">Leave empty if no clickable link is needed</small>
                    </div>

                    <div class="input-group">
                        <label>Link Button Text</label>
                        <input type="text" name="linkText" id="annLinkText" placeholder="e.g. Register Now, View Details">
                    </div>
                </div>

                <div class="modal-grid-2" style="align-items: center;">
                    <div class="input-group">
                        <label>Target Window</label>
                        <select name="openInNewTab" id="annOpenInNewTab">
                            <option value="0">Open in Same Tab</option>
                            <option value="1">Open in New Tab (_blank)</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Visibility Status</label>
                        <select name="status" id="annStatus">
                            <option value="Active">Active (Visible on Homepage)</option>
                            <option value="Inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeAnnouncementModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveAnnSubmitBtn" class="btn-act" style="background: #ea580c; color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADD / EDIT WINNER & RUNNER SLIDE MODAL -->
<div class="admin-modal-backdrop" id="winnerRunnerModal">
    <div class="admin-modal-card" style="max-width: 680px;">
        <div class="admin-modal-head">
            <h3 id="winnerRunnerModalTitle"><i class="fa-solid fa-trophy" style="color: #eab308;"></i> Add Winner / Runner Slide</h3>
            <button class="admin-modal-close" onclick="closeWinnerRunnerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body">
            <form id="winnerRunnerForm" onsubmit="saveWinnerRunnerForm(event)">
                <input type="hidden" name="slideId" id="winnerSlideId">
                <input type="hidden" name="existingImageUrl" id="winnerExistingImageUrl">

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Category <span style="color: var(--red);">*</span></label>
                        <select name="category" id="winnerCategory" required>
                            <option value="Winner">Winner (Champion 🏆)</option>
                            <option value="Runner-Up">Runner-Up (Finalist 🥈)</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="winnerSeason" required list="winnerSeasonSuggestions" placeholder="e.g. Season 1" value="Season 1">
                        <datalist id="winnerSeasonSuggestions">
                            <option value="Season 1">
                            <option value="Season 2">
                            <option value="Season 3">
                        </datalist>
                    </div>
                </div>

                <div class="input-group">
                    <label>Title / Slide Heading <span style="color: var(--red);">*</span></label>
                    <input type="text" name="title" id="winnerTitle" required placeholder="e.g. Season 1 Champions - Barbarik Warriors">
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Team Name (Optional)</label>
                        <input type="text" name="teamName" id="winnerTeamName" list="winnerTeamListOptions" placeholder="Select or type team name">
                        <datalist id="winnerTeamListOptions">
                            <option value="Barbarik Warriors">
                            <option value="Ghaziabad Panthers">
                            <option value="Gorakhpur Rowdies">
                            <option value="Kashi Kings">
                            <option value="Mathura Brij Star">
                            <option value="Noida Blasters">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Display Order / Slide Priority</label>
                        <input type="number" name="displayOrder" id="winnerDisplayOrder" value="1" min="0">
                    </div>
                </div>

                <div class="input-group">
                    <label>Description / Caption (Optional)</label>
                    <textarea name="description" id="winnerDescription" rows="2" placeholder="e.g. Lifting the championship trophy in grand finale celebration..."></textarea>
                </div>

                <div class="input-group">
                    <label>Status</label>
                    <select name="status" id="winnerStatus">
                        <option value="Active">Active (Visible on Homepage)</option>
                        <option value="Inactive">Inactive (Hidden)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Slide Image <span style="color: var(--red);">*</span></label>
                    <div class="upload-dropzone" onclick="document.getElementById('winnerImageFileInput').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary); margin-bottom: 8px;"></i>
                        <p style="margin: 0; font-weight: 600; color: #1e293b;">Click or drop image file here</p>
                        <small style="color: #64748b;">Supported: JPG, PNG, WEBP, GIF (Recommended: Landscape 16:9 or 4:3)</small>
                        <input type="file" name="imageFile" id="winnerImageFileInput" accept="image/*" onchange="handleWinnerImageSelect(this)" style="display: none;">
                    </div>
                    <div id="winnerImagePreviewContainer" class="upload-preview-box" style="display: none;">
                        <img id="winnerImagePreview" src="" alt="Preview">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeWinnerRunnerModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveWinnerSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Winner Slide
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FRANCHISE TEAM MODAL -->
<div class="admin-modal-backdrop" id="teamModal">
    <div class="admin-modal-card" style="max-width: 860px; max-height: 92vh; display: flex; flex-direction: column;">
        <div class="admin-modal-head">
            <h3 id="teamModalTitle"><i class="fa-solid fa-shield-halved" style="color: #ea580c;"></i> Add Franchise Team</h3>
            <button class="admin-modal-close" onclick="closeTeamModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body" style="overflow-y: auto; padding: 20px 24px;">
            <form id="teamForm" onsubmit="saveTeamForm(event)">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="isEditing" id="teamIsEditing" value="0">

                <!-- 1. BASIC INFORMATION -->
                <div style="font-weight: 800; font-size: 14px; color: #0f172a; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 2px solid #ea580c; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-info" style="color: #ea580c;"></i> Basic Information
                </div>
                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Team Name <span style="color: var(--red);">*</span></label>
                        <input type="text" name="name" id="teamNameInput" required placeholder="e.g. Barbarik Warriors" onkeyup="handleTeamNameChange(this.value)">
                    </div>

                    <div class="input-group">
                        <label>Team Slug / Identifier <span style="color: var(--red);">*</span></label>
                        <input type="text" name="id" id="teamSlugInput" required placeholder="e.g. barbarik-warriors">
                        <small style="color: #64748b; font-size: 11px;">Unique identifier used in URL query links</small>
                    </div>
                </div>

                <div class="modal-grid-3" style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 15px;">
                    <div class="input-group">
                        <label>Season <span style="color: var(--red);">*</span></label>
                        <input type="text" name="season" id="teamSeasonInput" required list="teamSeasonSuggestions" placeholder="e.g. Season 1, Season 2" value="Season 1">
                        <datalist id="teamSeasonSuggestions">
                            <option value="Season 1">
                            <option value="Season 2">
                            <option value="Season 3">
                        </datalist>
                    </div>

                    <div class="input-group">
                        <label>Home District / City</label>
                        <input type="text" name="city" id="teamCityInput" placeholder="e.g. Bhadohi, Varanasi, Noida">
                    </div>

                    <div class="input-group">
                        <label>Sort Order</label>
                        <input type="number" name="sortOrder" id="teamSortOrderInput" placeholder="e.g. 1, 2, 3" value="1" min="1">
                    </div>
                </div>

                <!-- 2. BRANDING & MEDIA ASSETS -->
                <div style="font-weight: 800; font-size: 14px; color: #0f172a; margin: 20px 0 12px 0; padding-bottom: 6px; border-bottom: 2px solid #0284c7; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-palette" style="color: #0284c7;"></i> Visuals: Team Poster (Home/Teams Page) &amp; Footer Logo
                </div>
                <div class="modal-grid-2">
                    <div class="input-group" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1.5px solid #e2e8f0;">
                        <label style="font-weight: 800; color: #0f172a;"><i class="fa-solid fa-image" style="color: #ea580c;"></i> 1. Team Poster / Banner <span style="display: block; font-size: 11px; font-weight: normal; color: #64748b;">(Used on Home Page 'Meet Our Teams' Slider &amp; 'Teams &amp; Stats' Cards)</span></label>
                        <input type="file" name="posterFile" id="teamPosterFileInput" accept="image/*" onchange="previewTeamImage(this, 'teamPosterPreview', 'teamPosterPreviewWrap')">
                        <input type="hidden" name="posterUrl" id="teamPosterUrlInput">
                        <div id="teamPosterPreviewWrap" style="display: none; align-items: center; gap: 10px; margin-top: 8px;">
                            <img id="teamPosterPreview" src="" style="width: 80px; height: 50px; border-radius: 6px; object-fit: cover; border: 2px solid #cbd5e1;" alt="Poster Preview">
                            <span style="font-size: 12px; color: #64748b;">Current Team Poster Banner</span>
                        </div>
                    </div>

                    <div class="input-group" style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1.5px solid #e2e8f0;">
                        <label style="font-weight: 800; color: #0f172a;"><i class="fa-solid fa-circle" style="color: #0284c7;"></i> 2. Footer Circular Team Logo <span style="display: block; font-size: 11px; font-weight: normal; color: #64748b;">(Used specifically in the Website Footer top row)</span></label>
                        <input type="file" name="footerLogoFile" id="teamFooterLogoFileInput" accept="image/*" onchange="previewTeamImage(this, 'teamFooterLogoPreview', 'teamFooterLogoPreviewWrap')">
                        <input type="hidden" name="footerLogoUrl" id="teamFooterLogoUrlInput">
                        <div id="teamFooterLogoPreviewWrap" style="display: none; align-items: center; gap: 10px; margin-top: 8px;">
                            <img id="teamFooterLogoPreview" src="" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #0284c7;" alt="Footer Logo Preview">
                            <span style="font-size: 12px; color: #64748b;">Current Footer Circular Logo</span>
                        </div>
                    </div>
                </div>

                <div class="modal-grid-2">
                    <div class="input-group">
                        <label>Accent Color</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="color" id="teamAccentColorPicker" value="#ea580c" style="width: 44px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;" oninput="document.getElementById('teamAccentColorInput').value = this.value">
                            <input type="text" name="accentColor" id="teamAccentColorInput" value="#ea580c" placeholder="#ea580c" style="flex: 1;" oninput="document.getElementById('teamAccentColorPicker').value = this.value">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Display Status</label>
                        <select name="status" id="teamStatusSelect" style="padding: 9px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; width: 100%; background: #fff;">
                            <option value="Active">Active (Visible on Website &amp; Footer)</option>
                            <option value="Inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <!-- 3. TEAM LEADERSHIP & COACHES -->
                <div style="font-weight: 800; font-size: 14px; color: #0f172a; margin: 20px 0 12px 0; padding-bottom: 6px; border-bottom: 2px solid #16a34a; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-crown" style="color: #16a34a;"></i> Team Leadership &amp; Coaching Staff
                </div>
                <div class="modal-grid-3" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <!-- Head Coach -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <h5 style="color: #0284c7; margin-bottom: 8px; font-size: 13px;"><i class="fa-solid fa-clipboard-user"></i> Head Coach</h5>
                        <div class="input-group" style="margin-bottom: 8px;">
                            <label style="font-size: 11px;">Coach Full Name</label>
                            <input type="text" name="coachName" id="teamCoachNameInput" placeholder="e.g. Balwant Singh">
                        </div>
                        <div class="input-group" style="margin-bottom: 8px;">
                            <label style="font-size: 11px;">Role / Designation</label>
                            <input type="text" name="coachRole" id="teamCoachRoleInput" placeholder="Head Coach" value="Head Coach">
                        </div>
                        <div class="input-group">
                            <label style="font-size: 11px;">Coach Photo</label>
                            <input type="file" name="coachPhotoFile" id="teamCoachPhotoFileInput" accept="image/*" style="font-size: 11px;" onchange="previewLeadershipPhoto(this, 'teamCoachPhotoPreview', 'teamCoachPhotoPreviewWrap', 'teamCoachPhotoRemoveInput')">
                            <input type="hidden" name="coachPhotoUrl" id="teamCoachPhotoUrlInput">
                            <input type="hidden" name="coachPhotoRemove" id="teamCoachPhotoRemoveInput" value="0">
                            <div id="teamCoachPhotoPreviewWrap" style="display: none; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; background: #fff; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img id="teamCoachPhotoPreview" src="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #0284c7;" alt="Coach Photo">
                                    <span style="font-size: 11px; color: #475569; font-weight: 600;">Coach Photo</span>
                                </div>
                                <button type="button" onclick="clearLeadershipPhoto('Coach')" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; padding: 3px 8px; font-size: 11px; cursor: pointer; display: flex; align-items: center; gap: 4px;" title="Remove Photo">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Franchise Owner -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <h5 style="color: #b45309; margin-bottom: 8px; font-size: 13px;"><i class="fa-solid fa-award"></i> Franchise Owner</h5>
                        <div class="input-group" style="margin-bottom: 8px;">
                            <label style="font-size: 11px;">Owner / Group Name</label>
                            <input type="text" name="ownerName" id="teamOwnerNameInput" placeholder="e.g. Jeevan Sanchay Trust">
                        </div>
                        <div class="input-group" style="margin-bottom: 8px;">
                            <label style="font-size: 11px;">Role / Designation</label>
                            <input type="text" name="ownerRole" id="teamOwnerRoleInput" placeholder="Franchise Owner" value="Franchise Owner">
                        </div>
                        <div class="input-group">
                            <label style="font-size: 11px;">Owner Photo</label>
                            <input type="file" name="ownerPhotoFile" id="teamOwnerPhotoFileInput" accept="image/*" style="font-size: 11px;" onchange="previewLeadershipPhoto(this, 'teamOwnerPhotoPreview', 'teamOwnerPhotoPreviewWrap', 'teamOwnerPhotoRemoveInput')">
                            <input type="hidden" name="ownerPhotoUrl" id="teamOwnerPhotoUrlInput">
                            <input type="hidden" name="ownerPhotoRemove" id="teamOwnerPhotoRemoveInput" value="0">
                            <div id="teamOwnerPhotoPreviewWrap" style="display: none; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; background: #fff; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img id="teamOwnerPhotoPreview" src="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #b45309;" alt="Owner Photo">
                                    <span style="font-size: 11px; color: #475569; font-weight: 600;">Owner Photo</span>
                                </div>
                                <button type="button" onclick="clearLeadershipPhoto('Owner')" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; padding: 3px 8px; font-size: 11px; cursor: pointer; display: flex; align-items: center; gap: 4px;" title="Remove Photo">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Team Captain -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <h5 style="color: #15803d; margin-bottom: 8px; font-size: 13px;"><i class="fa-solid fa-star"></i> Team Captain</h5>
                        <div class="input-group" style="margin-bottom: 8px;">
                            <label style="font-size: 11px;">Captain Name</label>
                            <input type="text" name="captainName" id="teamCaptainNameInput" placeholder="e.g. Aditya Pratap">
                        </div>
                        <div class="input-group" style="margin-bottom: 8px;">
                            <label style="font-size: 11px;">Role / Designation</label>
                            <input type="text" name="captainRole" id="teamCaptainRoleInput" placeholder="Team Captain" value="Team Captain">
                        </div>
                        <div class="input-group">
                            <label style="font-size: 11px;">Captain Photo</label>
                            <input type="file" name="captainPhotoFile" id="teamCaptainPhotoFileInput" accept="image/*" style="font-size: 11px;" onchange="previewLeadershipPhoto(this, 'teamCaptainPhotoPreview', 'teamCaptainPhotoPreviewWrap', 'teamCaptainPhotoRemoveInput')">
                            <input type="hidden" name="captainPhotoUrl" id="teamCaptainPhotoUrlInput">
                            <input type="hidden" name="captainPhotoRemove" id="teamCaptainPhotoRemoveInput" value="0">
                            <div id="teamCaptainPhotoPreviewWrap" style="display: none; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; background: #fff; padding: 6px 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <img id="teamCaptainPhotoPreview" src="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #15803d;" alt="Captain Photo">
                                    <span style="font-size: 11px; color: #475569; font-weight: 600;">Captain Photo</span>
                                </div>
                                <button type="button" onclick="clearLeadershipPhoto('Captain')" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; padding: 3px 8px; font-size: 11px; cursor: pointer; display: flex; align-items: center; gap: 4px;" title="Remove Photo">
                                    <i class="fa-solid fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. SQUAD ATHLETES (PLAYERS LIST) -->
                <div style="margin: 20px 0 12px 0; padding-bottom: 6px; border-bottom: 2px solid #8b5cf6; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="font-weight: 800; font-size: 14px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-hand-fist" style="color: #8b5cf6;"></i> Squad Athletes (Players)
                    </div>
                    <button type="button" class="btn-act" style="background: #8b5cf6; color: #fff; padding: 4px 12px; font-size: 12px;" onclick="addTeamPlayerRow()">
                        <i class="fa-solid fa-plus"></i> Add Player
                    </button>
                </div>

                <div id="teamPlayersContainer" style="display: flex; flex-direction: column; gap: 8px; max-height: 240px; overflow-y: auto; padding-right: 4px;">
                    <!-- Populated via JS -->
                </div>

                <!-- SUBMIT BUTTONS -->
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 10px 20px; font-size: 14px;" onclick="closeTeamModal()">
                        Cancel
                    </button>
                    <button type="submit" id="saveTeamSubmitBtn" class="btn-act" style="background: var(--primary); color: #fff; padding: 10px 24px; font-size: 14px;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Franchise Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MEDIA POPUP VIEWER MODAL -->
<div class="admin-modal-backdrop" id="mediaPopupModal" onclick="closeMediaPopup(event)">
    <div style="background: #000; border-radius: 12px; padding: 10px; max-width: 90vw; max-height: 90vh; position: relative; display: flex; align-items: center; justify-content: center;">
        <button onclick="document.getElementById('mediaPopupModal').classList.remove('show')" style="position: absolute; top: -14px; right: -14px; width: 32px; height: 32px; border-radius: 50%; background: #ef4444; color: #fff; border: none; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 20;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div id="mediaPopupContent"></div>
    </div>
</div>

<!-- PLAYER DETAILS MODAL -->
<div class="admin-modal-backdrop" id="playerDetailsModal">
    <div class="admin-modal-card" style="max-width: 800px;">
        <div class="admin-modal-head">
            <h3><i class="fa-solid fa-user-gear" style="color: var(--orange);"></i> Complete Player Registration Profile</h3>
            <button class="admin-modal-close" onclick="closePlayerModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body" id="modalPlayerContent">
            <!-- Dynamic Content Injected Here -->
        </div>
    </div>
</div>

<!-- CONTACT MESSAGE DETAILS & REPLY MODAL -->
<div class="admin-modal-backdrop" id="contactMessageModal">
    <div class="admin-modal-card" style="max-width: 720px;">
        <div class="admin-modal-head" style="background: #0f172a;">
            <h3><i class="fa-solid fa-envelope-open-text" style="color: #0ea5e9;"></i> Contact Inquiry Details &amp; Reply</h3>
            <button type="button" class="admin-modal-close" onclick="closeContactMessageModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="admin-modal-body" id="modalContactMessageContent">
            <!-- Dynamic Content Injected Here -->
        </div>
    </div>
</div>

<script>
function toggleActionMenu(menuId, e) {
    if (e) e.stopPropagation();
    document.querySelectorAll('.action-dropdown-menu').forEach(m => {
        if (m.id !== menuId) m.classList.remove('show');
    });
    const targetMenu = document.getElementById(menuId);
    if (targetMenu) targetMenu.classList.toggle('show');
}

document.addEventListener('click', () => {
    document.querySelectorAll('.action-dropdown-menu').forEach(m => m.classList.remove('show'));
});

function viewFullPlayerDetails(p, e) {
    if (e) e.preventDefault();
    document.querySelectorAll('.action-dropdown-menu').forEach(m => m.classList.remove('show'));

    const paid = isPlayerPaid(p);
    const content = document.getElementById('modalPlayerContent');
    content.innerHTML = `
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap;">
            <img src="../${p.photoUrl || 'assets/images/default-player.png'}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--orange);" alt="Photo">
            <div>
                <h2 style="margin: 0; color: #0f172a; font-size: 22px;">${p.fullName || 'N/A'}</h2>
                <div style="display: flex; gap: 10px; margin-top: 6px; flex-wrap: wrap; align-items: center;">
                    <span style="background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 12px; color: var(--primary); font-family: monospace;">ID: ${p.playerId}</span>
                    <span style="background: #fef3c7; color: #d97706; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 12px; font-family: monospace;"><i class="fa-solid fa-key"></i> ${p.password || 'N/A'}</span>
                    ${paid 
                        ? `<span style="background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #bbf7d0;"><i class="fa-solid fa-circle-check"></i> Bank Verified (Paid)</span>` 
                        : `<span style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 12px; border: 1px solid #fecaca;"><i class="fa-solid fa-clock"></i> Payment Unpaid</span>`}
                </div>
            </div>
        </div>

        <h4 style="color: var(--primary); margin-bottom: 12px; font-size: 15px;"><i class="fa-solid fa-building-columns" style="color: var(--orange);"></i> Bank Payment &amp; UTR Verification</h4>
        <div class="modal-grid-2" style="background: #f8fafc; border: 1.5px solid #cbd5e1; padding: 14px; border-radius: 10px; margin-bottom: 20px;">
            <div class="modal-info-box" style="background: #fff;"><label>Bank UTR / Txn Reference</label><span style="font-family: monospace; font-size: 13.5px; font-weight: 800; color: #0f172a;"><i class="fa-solid fa-receipt" style="color: var(--orange);"></i> ${p.paymentId || 'Unpaid / Pending'}</span></div>
            <div class="modal-info-box" style="background: #fff;"><label>Payment Verification Status</label><span style="font-weight: 800; color: ${paid ? '#16a34a' : '#dc2626'};">${paid ? 'Verified & Paid' : 'Unpaid / Pending'}</span></div>
            <div class="modal-info-box" style="background: #fff;"><label>Amount Paid</label><span style="font-weight: 700; color: #1e293b;">${p.amountPaid || '₹1,000'}</span></div>
            <div class="modal-info-box" style="background: #fff;"><label>Payment Gateway / Method</label><span style="font-weight: 600; color: #1e293b;">${p.paymentMethod || 'ICICI Orange PG / UPI'}</span></div>
            <div class="modal-info-box" style="grid-column: span 2; background: #fff;"><label>Payment Date &amp; Registration Time</label><span>${p.paidAt || p.submittedAt || 'N/A'}</span></div>
        </div>

        <h4 style="color: var(--primary); margin-bottom: 12px; font-size: 15px;"><i class="fa-solid fa-id-card"></i> Identity &amp; Contact Information</h4>
        <div class="modal-grid-2">
            <div class="modal-info-box"><label>Aadhaar Number</label><span>${p.aadhaar || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Mobile Number</label><span>${p.mobile || 'N/A'}</span></div>
            <div class="modal-info-box"><label>WhatsApp Number</label><span>${p.whatsapp || p.mobile || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Email Address</label><span>${p.email || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Father's Name / Guardian</label><span>${p.fatherName || p.emgName || 'N/A'} ${p.emgRel ? '(' + p.emgRel + ')' : ''}</span></div>
            <div class="modal-info-box"><label>Mother's Name</label><span>${p.motherName || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Date of Birth / Age</label><span>${p.dob || 'N/A'} (${p.age || 'N/A'})</span></div>
            <div class="modal-info-box"><label>Gender / Blood Group</label><span>${p.gender || 'N/A'} · ${p.bloodGroup || 'N/A'}</span></div>
        </div>

        <h4 style="color: var(--primary); margin-bottom: 12px; font-size: 15px;"><i class="fa-solid fa-person-running"></i> Playing &amp; Physical Specifications</h4>
        <div class="modal-grid-2">
            <div class="modal-info-box"><label>Primary Position</label><span>${p.primaryPos || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Secondary Position</label><span>${p.secondaryPos || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Playing Hand</label><span>${p.hand || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Height &amp; Weight</label><span>${p.height || 'N/A'} cm · ${p.weight || 'N/A'} kg</span></div>
            <div class="modal-info-box"><label>Total Experience</label><span>${p.totalExp || p.exp || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Highest Playing Level</label><span>${p.level || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Club / Academy Name</label><span>${p.club || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Season 1 Participant</label><span>${p.season1 || 'No'}</span></div>
            <div class="modal-info-box" style="grid-column: span 2;"><label>Major Tournaments &amp; Achievements</label><span>${p.achievements || p.achieve || p.certsNote || p.tournaments || 'N/A'}</span></div>
        </div>

        <h4 style="color: var(--primary); margin-bottom: 12px; font-size: 15px;"><i class="fa-solid fa-location-dot"></i> Address &amp; Emergency Details</h4>
        <div class="modal-grid-2">
            <div class="modal-info-box" style="grid-column: span 2;"><label>Full Address</label><span>${p.address || 'N/A'}, ${p.district || ''}, ${p.state || ''}</span></div>
            <div class="modal-info-box"><label>Emergency Contact</label><span>${p.emgName || ''} (${p.emgRel || 'Guardian'}) - ${p.emgNo || 'N/A'}</span></div>
            <div class="modal-info-box"><label>Fitness &amp; Injury Status</label><span>${p.fitness || 'Fully Fit'} (${p.injDetails || p.injHistory || 'No Major Injury'})</span></div>
        </div>

        <h4 style="color: var(--primary); margin-bottom: 12px; font-size: 15px;"><i class="fa-solid fa-file-invoice"></i> Uploaded Documents</h4>
        <div class="modal-grid-2">
            <div class="modal-info-box">
                <label>Aadhaar Card Front</label>
                <span>${p.aadhaarFrontUrl ? `<a href="../${p.aadhaarFrontUrl}" target="_blank" style="color: #2563eb; font-weight:700;"><i class="fa-solid fa-eye"></i> View Front Image</a>` : 'Not Uploaded'}</span>
            </div>
            <div class="modal-info-box">
                <label>Aadhaar Card Back</label>
                <span>${p.aadhaarBackUrl ? `<a href="../${p.aadhaarBackUrl}" target="_blank" style="color: #16a34a; font-weight:700;"><i class="fa-solid fa-eye"></i> View Back Image</a>` : 'Not Uploaded'}</span>
            </div>
            <div class="modal-info-box" style="grid-column: span 2;">
                <label>Achievement Certificates</label>
                <span>${(p.certUrls && Array.isArray(p.certUrls) && p.certUrls.length > 0) 
                    ? p.certUrls.map((c, i) => `<a href="../${c}" target="_blank" style="color: #ea580c; font-weight:700; display:inline-block; margin-right:8px;"><i class="fa-solid fa-award"></i> Cert #${i+1}</a>`).join(' ')
                    : (p.docUrl ? `<a href="../${p.docUrl}" target="_blank" style="color: #ea580c; font-weight:700;"><i class="fa-solid fa-file"></i> View Document</a>` : 'No Certificates Uploaded')}</span>
            </div>
        </div>
    `;

    document.getElementById('playerDetailsModal').classList.add('show');
}

function deletePlayerRow(playerId, e) {
    if (e) e.preventDefault();
    document.querySelectorAll('.action-dropdown-menu').forEach(m => m.classList.remove('show'));
    if (!confirm('Are you sure you want to delete registration for player ' + playerId + '? This cannot be undone.')) return;

    const formData = new FormData();
    formData.append('playerId', playerId);

    fetch('../api/delete-player.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('row-' + playerId);
            if (row) row.remove();
            rawDashboardPlayers = rawDashboardPlayers.filter(p => p.playerId !== playerId);
            updateDashboardMetrics();
            setupAdminPagination();
            alert('Player registration deleted successfully.');
        } else {
            alert('Failed to delete player: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function closePlayerModal() {
    document.getElementById('playerDetailsModal').classList.remove('show');
}

// ==========================================
// DASHBOARD FILTERING & AUTO-CALCULATION
// ==========================================
let currentDashPeriod = 'all';
let rawDashboardPlayers = <?= json_encode($players) ?>;
let rawDashboardMessages = <?= json_encode($messages) ?>;

function getRecordDateObj(record) {
    if (!record) return null;
    const raw = record.submittedAt || record.created_at || record.signDate || '';
    if (!raw) return null;
    let d = new Date(raw.replace(' ', 'T'));
    if (isNaN(d.getTime())) {
        d = new Date(raw);
    }
    return isNaN(d.getTime()) ? null : d;
}

function checkPeriodMatch(dateObj, period) {
    if (period === 'all') return true;
    if (!dateObj) return false;

    const now = new Date();
    const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
    const todayEnd = todayStart + (24 * 60 * 60 * 1000) - 1;
    const time = dateObj.getTime();
    const dayStart = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate()).getTime();

    if (period === 'today') {
        return dayStart === todayStart;
    } else if (period === '7days') {
        const sevenDaysAgo = todayStart - (7 * 24 * 60 * 60 * 1000);
        return time >= sevenDaysAgo && time <= todayEnd;
    } else if (period === 'month') {
        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1).getTime();
        return time >= startOfMonth && time <= todayEnd;
    }
    return true;
}

function isPlayerPaid(p) {
    if (!p) return false;
    if (p.paymentStatus && String(p.paymentStatus).toLowerCase() === 'paid') return true;
    if (p.paymentId && String(p.paymentId).trim() !== '') return true;
    if (p.status && String(p.status).toLowerCase() === 'approved') return true;
    return false;
}

function calcPlayerAmount(p) {
    if (!p || !isPlayerPaid(p)) return 0;
    if (p.amountPaid) {
        const clean = String(p.amountPaid).replace(/[^0-9.]/g, '');
        const num = parseFloat(clean);
        if (!isNaN(num) && num > 0) return num;
    }
    return 1000;
}

function updateDashboardMetrics() {
    const filteredPlayers = rawDashboardPlayers.filter(p => checkPeriodMatch(getRecordDateObj(p), currentDashPeriod));
    const filteredMessages = rawDashboardMessages.filter(m => checkPeriodMatch(getRecordDateObj(m), currentDashPeriod));

    const totalP = filteredPlayers.length;
    const verifiedP = filteredPlayers.filter(p => isPlayerPaid(p)).length;
    let totalRev = 0;
    filteredPlayers.forEach(p => {
        totalRev += calcPlayerAmount(p);
    });
    const totalMsg = filteredMessages.length;

    // Update DOM elements
    const elTotal = document.getElementById('statTotal');
    const elVerified = document.getElementById('statVerified');
    const elRevenue = document.getElementById('statRevenue');
    const elMessages = document.getElementById('statMessages');

    if (elTotal) elTotal.textContent = totalP;
    if (elVerified) elVerified.textContent = verifiedP;
    if (elRevenue) elRevenue.textContent = '₹' + totalRev.toLocaleString('en-IN');
    if (elMessages) elMessages.textContent = totalMsg;

    // Update dynamic sub-labels
    const lblP = document.getElementById('lblTotalPlayers');
    const lblR = document.getElementById('lblTotalRevenue');
    const lblV = document.getElementById('lblVerifiedPlayers');
    const lblM = document.getElementById('lblTotalMessages');

    let suffix = '';
    if (currentDashPeriod === 'today') suffix = ' (Today)';
    else if (currentDashPeriod === '7days') suffix = ' (7 Days)';
    else if (currentDashPeriod === 'month') suffix = ' (This Month)';

    if (lblP) lblP.textContent = 'Total Players' + suffix;
    if (lblR) lblR.textContent = 'Total Payments' + suffix;
    if (lblV) lblV.textContent = 'Verified (Paid)' + suffix;
    if (lblM) lblM.textContent = 'Contact Messages' + suffix;
}

function setDashboardPeriod(period) {
    currentDashPeriod = period;
    document.querySelectorAll('.dash-filter-pill').forEach(pill => {
        if (pill.getAttribute('data-period') === period) {
            pill.classList.add('active');
        } else {
            pill.classList.remove('active');
        }
    });
    updateDashboardMetrics();
}

function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) sidebar.classList.toggle('mobile-open');
    if (backdrop) backdrop.classList.toggle('show');
}

function closeSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (backdrop) backdrop.classList.remove('show');
}

function switchTab(tab, e) {
    if (e) e.preventDefault();
    closeSidebar();
    const sections = ['sectionDashboard', 'sectionPlayers', 'sectionTeams', 'sectionStandings', 'sectionFixtures', 'sectionMvp', 'sectionWinners', 'sectionBanners', 'sectionPageBanners', 'sectionAnnouncements', 'sectionGallery', 'sectionNews', 'sectionPartners', 'sectionCommittee', 'sectionLiveHub', 'sectionMessages', 'sectionHomeStats', 'sectionContactSettings'];
    const navItems = ['navItemDashboard', 'navItemPlayers', 'navItemTeams', 'navItemStandings', 'navItemFixtures', 'navItemMvp', 'navItemWinners', 'navItemBanners', 'navItemPageBanners', 'navItemAnnouncements', 'navItemGallery', 'navItemNews', 'navItemPartners', 'navItemCommittee', 'navItemLiveHub', 'navItemMessages', 'navItemHomeStats', 'navItemSettings'];

    // Persist active tab in localStorage & URL hash so refreshes and updates stay on current page
    try {
        localStorage.setItem('upphl_admin_active_tab', tab);
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, '#' + tab);
        } else {
            window.location.hash = tab;
        }
    } catch(err) {}

    sections.forEach(s => {
        const el = document.getElementById(s);
        if (el) el.classList.remove('active');
    });

    navItems.forEach(n => {
        const el = document.getElementById(n);
        if (el) el.classList.remove('active');
    });

    if (tab === 'dashboard') {
        document.getElementById('sectionDashboard')?.classList.add('active');
        document.getElementById('navItemDashboard')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Dashboard Overview';
    } else if (tab === 'players') {
        document.getElementById('sectionPlayers')?.classList.add('active');
        document.getElementById('navItemPlayers')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Player Registrations';
    } else if (tab === 'teams') {
        document.getElementById('sectionTeams')?.classList.add('active');
        document.getElementById('navItemTeams')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Franchise Teams Management';
    } else if (tab === 'standings') {
        document.getElementById('sectionStandings')?.classList.add('active');
        document.getElementById('navItemStandings')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Points Table Standings';
    } else if (tab === 'fixtures') {
        document.getElementById('sectionFixtures')?.classList.add('active');
        document.getElementById('navItemFixtures')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Match Fixtures & Schedule';
    } else if (tab === 'mvp') {
        document.getElementById('sectionMvp')?.classList.add('active');
        document.getElementById('navItemMvp')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'MVP Players & Match Stars';
    } else if (tab === 'winners') {
        document.getElementById('sectionWinners')?.classList.add('active');
        document.getElementById('navItemWinners')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Winners & Runners-Up Slider';
    } else if (tab === 'banners') {
        document.getElementById('sectionBanners')?.classList.add('active');
        document.getElementById('navItemBanners')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Hero Banners Management';
    } else if (tab === 'page-banners') {
        document.getElementById('sectionPageBanners')?.classList.add('active');
        document.getElementById('navItemPageBanners')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Inner Page Banners Management';
    } else if (tab === 'announcements') {
        document.getElementById('sectionAnnouncements')?.classList.add('active');
        document.getElementById('navItemAnnouncements')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Announcement Strip Management';
    } else if (tab === 'gallery') {
        document.getElementById('sectionGallery')?.classList.add('active');
        document.getElementById('navItemGallery')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Gallery Photos & Seasons';
    } else if (tab === 'news') {
        document.getElementById('sectionNews')?.classList.add('active');
        document.getElementById('navItemNews')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Latest News & Updates';
    } else if (tab === 'partners') {
        document.getElementById('sectionPartners')?.classList.add('active');
        document.getElementById('navItemPartners')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Partners & Sponsors Management';
    } else if (tab === 'committee') {
        document.getElementById('sectionCommittee')?.classList.add('active');
        document.getElementById('navItemCommittee')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'League Management Committee';
    } else if (tab === 'live-hub') {
        document.getElementById('sectionLiveHub')?.classList.add('active');
        document.getElementById('navItemLiveHub')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Live Stream & Video Vault Hub';
        const savedLiveSub = localStorage.getItem('upphl_admin_live_subtab') || 'partners';
        switchLiveSubTab(savedLiveSub);
    } else if (tab === 'home-stats') {
        document.getElementById('sectionHomeStats')?.classList.add('active');
        document.getElementById('navItemHomeStats')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Home Hero Stats Counter';
    } else if (tab === 'contact-settings') {
        document.getElementById('sectionContactSettings')?.classList.add('active');
        document.getElementById('navItemSettings')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Contact Us Page Settings';
    } else {
        document.getElementById('sectionMessages')?.classList.add('active');
        document.getElementById('navItemMessages')?.classList.add('active');
        document.getElementById('headerTitle').textContent = 'Contact Messages';
    }
}

// ==========================================
// POINTS TABLE / STANDINGS HANDLERS
// ==========================================
function openStandingsModal() {
    document.getElementById('standingsForm').reset();
    document.getElementById('standingsRowId').value = '';
    document.getElementById('standingsModalTitle').innerHTML = '<i class="fa-solid fa-table-list" style="color: #eab308;"></i> Add Team Standings';
    document.getElementById('standingsModal').classList.add('show');
}

function closeStandingsModal() {
    document.getElementById('standingsModal').classList.remove('show');
}

function autoCalcPts() {
    const w = parseInt(document.getElementById('standingsWon').value || 0);
    const d = parseInt(document.getElementById('standingsDraw').value || 0);
    document.getElementById('standingsPts').value = (w * 2) + d;
}

function editStandingsRow(s) {
    document.getElementById('standingsForm').reset();
    document.getElementById('standingsRowId').value = s.id || '';
    document.getElementById('standingsSeason').value = s.season || 'Season 2';
    document.getElementById('standingsTeamName').value = s.teamName || '';
    document.getElementById('standingsPlayed').value = s.played || 0;
    document.getElementById('standingsWon').value = s.won || 0;
    document.getElementById('standingsDraw').value = s.draw || 0;
    document.getElementById('standingsLost').value = s.lost || 0;
    document.getElementById('standingsGf').value = s.gf || 0;
    document.getElementById('standingsGa').value = s.ga || 0;
    document.getElementById('standingsPts').value = s.pts || 0;
    document.getElementById('standingsFormStreak').value = s.form || 'W,W,W,D,W';

    document.getElementById('standingsModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #eab308;"></i> Edit Team Standings';
    document.getElementById('standingsModal').classList.add('show');
}

function saveStandingsForm(e) {
    e.preventDefault();
    const form = document.getElementById('standingsForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveStandingsSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/standings.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Team Standings';
        if (res.success) {
            alert(res.message);
            closeStandingsModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving standings row.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Team Standings';
        alert('Server communication error.');
    });
}

function deleteStandingsRow(rowId) {
    if (!confirm('Are you sure you want to delete this team from the points table?')) return;

    const formData = new FormData();
    formData.append('rowId', rowId);

    fetch('../api/standings.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('standing-row-' + rowId);
            if (row) row.remove();
            const badge = document.getElementById('sideStandingsBadge');
            const stat = document.getElementById('statStandings');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) {
                    badge.textContent = c - 1;
                    if (stat) stat.textContent = c - 1;
                }
            }
        } else {
            alert(res.message || 'Failed to delete row.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminStandings(season) {
    const rows = document.querySelectorAll('#standingsTbody tr[data-season]');
    rows.forEach(r => {
        const rowSeason = r.getAttribute('data-season') || '';
        if (season === 'all' || rowSeason.toLowerCase() === season.toLowerCase()) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

// ==========================================
// MATCH FIXTURES HANDLERS
// ==========================================
function openFixtureModal() {
    document.getElementById('fixtureForm').reset();
    document.getElementById('fixtureId').value = '';
    document.getElementById('fixtureStartTime').value = '02:15 PM';
    document.getElementById('fixtureEndTime').value = '03:30 PM';
    document.getElementById('fixtureModalTitle').innerHTML = '<i class="fa-solid fa-calendar-plus" style="color: #3b82f6;"></i> Add New Match Fixture';
    document.getElementById('fixtureModal').classList.add('show');
}

function closeFixtureModal() {
    document.getElementById('fixtureModal').classList.remove('show');
}

function editFixtureRow(f) {
    document.getElementById('fixtureForm').reset();
    document.getElementById('fixtureId').value = f.id || '';
    document.getElementById('fixtureSeason').value = f.season || 'Season 2';
    document.getElementById('fixtureMatchDay').value = f.matchDay || 'Day 1';
    document.getElementById('fixtureMatchNumber').value = f.matchNumber || 'Match 1';
    document.getElementById('fixtureMatchTitle').value = f.matchTitle || 'League Stage';
    document.getElementById('fixtureTeam1Name').value = f.team1Name || '';
    document.getElementById('fixtureTeam2Name').value = f.team2Name || '';
    document.getElementById('fixtureMatchDate').value = f.matchDate || '';
    document.getElementById('fixtureStartTime').value = f.startTime || f.matchTime || '02:15 PM';
    document.getElementById('fixtureEndTime').value = f.endTime || '';
    document.getElementById('fixtureStadium').value = f.stadium || 'K.D. Singh Babu Stadium, Lucknow';
    document.getElementById('fixtureStatus').value = f.status || 'Auto';
    document.getElementById('fixtureTeam1Score').value = f.team1Score !== null ? f.team1Score : '';
    document.getElementById('fixtureTeam2Score').value = f.team2Score !== null ? f.team2Score : '';

    document.getElementById('fixtureModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #3b82f6;"></i> Edit Match Fixture';
    document.getElementById('fixtureModal').classList.add('show');
}

function saveFixtureForm(e) {
    e.preventDefault();
    const form = document.getElementById('fixtureForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveFixtureSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/fixtures.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Match Fixture';
        if (res.success) {
            alert(res.message);
            closeFixtureModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving fixture.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Match Fixture';
        alert('Server communication error.');
    });
}

function deleteFixtureRow(fixtureId) {
    if (!confirm('Are you sure you want to delete this match fixture?')) return;

    const formData = new FormData();
    formData.append('fixtureId', fixtureId);

    fetch('../api/fixtures.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('fixture-row-' + fixtureId);
            if (row) row.remove();
            const badge = document.getElementById('sideFixturesBadge');
            const stat = document.getElementById('statFixtures');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) {
                    badge.textContent = c - 1;
                    if (stat) stat.textContent = c - 1;
                }
            }
        } else {
            alert(res.message || 'Failed to delete fixture.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminFixtures() {
    const season = (document.getElementById('adminFixturesSeasonFilter').value || 'all').toLowerCase();
    const status = (document.getElementById('adminFixturesStatusFilter').value || 'all').toLowerCase();

    const rows = document.querySelectorAll('#fixturesTbody tr[data-season]');
    rows.forEach(r => {
        const rowSeason = (r.getAttribute('data-season') || '').toLowerCase();
        const rowStatus = (r.getAttribute('data-status') || '').toLowerCase();

        const matchSeason = (season === 'all' || rowSeason === season);
        const matchStatus = (status === 'all' || rowStatus === status);

        if (matchSeason && matchStatus) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

// ==========================================
// MVP PLAYERS MANAGEMENT HANDLERS
// ==========================================
function openMvpModal() {
    document.getElementById('mvpForm').reset();
    document.getElementById('mvpId').value = '';
    document.getElementById('mvpExistingPhoto').value = '';
    document.getElementById('mvpSeason').value = 'Season 1';
    document.getElementById('mvpMatchDay').value = 'Day 1';
    document.getElementById('mvpMatchNumber').value = 'Match 1';
    document.getElementById('mvpAwardTitle').value = 'Player of the Match';
    document.getElementById('mvpPhotoPreviewWrap').style.display = 'none';
    document.getElementById('mvpModalTitle').innerHTML = '<i class="fa-solid fa-award" style="color: #f59e0b;"></i> Add New MVP Player';
    document.getElementById('mvpModal').classList.add('show');
}

function closeMvpModal() {
    document.getElementById('mvpModal').classList.remove('show');
}

function editMvp(p) {
    document.getElementById('mvpForm').reset();
    document.getElementById('mvpId').value = p.id || '';
    document.getElementById('mvpExistingPhoto').value = p.playerPhoto || '';
    document.getElementById('mvpSeason').value = p.season || 'Season 1';
    document.getElementById('mvpMatchDay').value = p.matchDay || 'Day 1';
    document.getElementById('mvpMatchNumber').value = p.matchNumber || 'Match 1';
    document.getElementById('mvpPlayerName').value = p.playerName || '';
    document.getElementById('mvpTeamName').value = p.teamName || '';
    document.getElementById('mvpAwardTitle').value = p.awardTitle || 'Player of the Match';
    document.getElementById('mvpJerseyNumber').value = p.jerseyNumber || '';
    document.getElementById('mvpPosition').value = p.position || '';
    document.getElementById('mvpGoals').value = p.goals || '';
    document.getElementById('mvpRating').value = p.rating || '';

    const prevWrap = document.getElementById('mvpPhotoPreviewWrap');
    const prevImg = document.getElementById('mvpPhotoPreview');
    if (p.playerPhoto) {
        prevWrap.style.display = 'flex';
        prevImg.src = '../' + p.playerPhoto;
    } else {
        prevWrap.style.display = 'none';
    }

    document.getElementById('mvpModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #f59e0b;"></i> Edit MVP Player';
    document.getElementById('mvpModal').classList.add('show');
}

function handleMvpPhotoSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const prevWrap = document.getElementById('mvpPhotoPreviewWrap');
        const prevImg = document.getElementById('mvpPhotoPreview');
        prevWrap.style.display = 'flex';
        prevImg.src = URL.createObjectURL(file);
    }
}

function saveMvpForm(e) {
    e.preventDefault();
    const form = document.getElementById('mvpForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveMvpSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/mvp.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save MVP Player';
        if (res.success) {
            alert(res.message);
            closeMvpModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving MVP player.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save MVP Player';
        alert('Server communication error.');
    });
}

function deleteMvp(mvpId) {
    if (!confirm('Are you sure you want to delete this MVP player? This cannot be undone.')) return;

    const formData = new FormData();
    formData.append('mvpId', mvpId);

    fetch('../api/mvp.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('mvp-row-' + mvpId);
            if (row) row.remove();
            const badge = document.getElementById('sideMvpBadge');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert(res.message || 'Failed to delete MVP player.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminMvp() {
    const season = (document.getElementById('adminMvpSeasonFilter').value || 'all').toLowerCase();
    const day = (document.getElementById('adminMvpDayFilter').value || 'all').toLowerCase();

    const rows = document.querySelectorAll('#mvpTbody tr[data-season]');
    rows.forEach(r => {
        const rowSeason = (r.getAttribute('data-season') || '').toLowerCase();
        const rowDay = (r.getAttribute('data-day') || '').toLowerCase();

        const matchSeason = (season === 'all' || rowSeason === season);
        const matchDay = (day === 'all' || rowDay === day);

        if (matchSeason && matchDay) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

// ==========================================
// WINNERS & RUNNERS-UP HANDLERS
// ==========================================
function openWinnerRunnerModal() {
    document.getElementById('winnerRunnerForm').reset();
    document.getElementById('winnerSlideId').value = '';
    document.getElementById('winnerExistingImageUrl').value = '';
    document.getElementById('winnerImagePreviewContainer').style.display = 'none';
    document.getElementById('winnerImagePreview').src = '';
    document.getElementById('winnerRunnerModalTitle').innerHTML = '<i class="fa-solid fa-trophy" style="color: #eab308;"></i> Add Winner / Runner Slide';
    document.getElementById('winnerRunnerModal').classList.add('show');
}

function closeWinnerRunnerModal() {
    document.getElementById('winnerRunnerModal').classList.remove('show');
}

function handleWinnerImageSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const prevBox = document.getElementById('winnerImagePreviewContainer');
        const imgPrev = document.getElementById('winnerImagePreview');
        prevBox.style.display = 'flex';
        imgPrev.src = URL.createObjectURL(file);
    }
}

function editWinnerRunner(w) {
    document.getElementById('winnerRunnerForm').reset();
    document.getElementById('winnerSlideId').value = w.id || '';
    document.getElementById('winnerCategory').value = w.category || 'Winner';
    document.getElementById('winnerSeason').value = w.season || 'Season 1';
    document.getElementById('winnerTitle').value = w.title || '';
    document.getElementById('winnerTeamName').value = w.teamName || '';
    document.getElementById('winnerDescription').value = w.description || '';
    document.getElementById('winnerDisplayOrder').value = w.displayOrder || 1;
    document.getElementById('winnerStatus').value = w.status || 'Active';
    document.getElementById('winnerExistingImageUrl').value = w.imageUrl || '';

    const prevBox = document.getElementById('winnerImagePreviewContainer');
    const imgPrev = document.getElementById('winnerImagePreview');
    if (w.imageUrl) {
        prevBox.style.display = 'flex';
        imgPrev.src = '../' + w.imageUrl;
    } else {
        prevBox.style.display = 'none';
        imgPrev.src = '';
    }

    document.getElementById('winnerRunnerModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #eab308;"></i> Edit Winner / Runner Slide';
    document.getElementById('winnerRunnerModal').classList.add('show');
}

function saveWinnerRunnerForm(e) {
    e.preventDefault();
    const form = document.getElementById('winnerRunnerForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveWinnerSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/winners.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Winner Slide';
        if (res.success) {
            alert(res.message);
            closeWinnerRunnerModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving slide.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Winner Slide';
        alert('Server communication error.');
    });
}

function deleteWinnerRunner(slideId) {
    if (!confirm('Are you sure you want to delete this Winner/Runner slide?')) return;

    const formData = new FormData();
    formData.append('slideId', slideId);

    fetch('../api/winners.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('winner-row-' + slideId);
            if (row) row.remove();
            const badge = document.getElementById('sideWinnersBadge');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert(res.message || 'Failed to delete slide.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminWinners() {
    const season = (document.getElementById('adminWinnerSeasonFilter')?.value || 'all').toLowerCase();
    const category = (document.getElementById('adminWinnerCategoryFilter')?.value || 'all').toLowerCase();
    const rows = document.querySelectorAll('#winnersTbody tr[data-season]');

    rows.forEach(r => {
        const s = (r.getAttribute('data-season') || '').toLowerCase();
        const c = (r.getAttribute('data-category') || '').toLowerCase();

        const sMatch = (season === 'all' || s === season);
        const cMatch = (category === 'all' || c === category);

        if (sMatch && cMatch) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}

// ==========================================
// BANNER MANAGEMENT HANDLERS
// ==========================================
function openBannerModal() {
    document.getElementById('bannerForm').reset();
    document.getElementById('bannerId').value = '';
    document.getElementById('existingMediaUrl').value = '';
    document.getElementById('existingMediaType').value = 'image';
    document.getElementById('bannerSortOrder').value = '0';
    document.getElementById('bannerModalTitle').innerHTML = '<i class="fa-solid fa-panorama" style="color: var(--orange);"></i> Add New Hero Banner';
    
    document.getElementById('mediaPreviewContainer').style.display = 'none';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('videoPreview').style.display = 'none';
    
    document.getElementById('bannerModal').classList.add('show');
}

function closeBannerModal() {
    document.getElementById('bannerModal').classList.remove('show');
}

function editBanner(b) {
    document.getElementById('bannerForm').reset();
    document.getElementById('bannerId').value = b.bannerId || '';
    document.getElementById('existingMediaUrl').value = b.mediaUrl || '';
    document.getElementById('existingMediaType').value = b.mediaType || 'image';
    document.getElementById('bannerSortOrder').value = (b.sortOrder !== undefined && b.sortOrder !== null) ? b.sortOrder : 0;
    document.getElementById('badgeText').value = b.badgeText || '';
    document.getElementById('bannerHeading').value = b.heading || '';
    document.getElementById('bannerSubtitle').value = b.subtitle || '';
    document.getElementById('btn1Text').value = b.btn1Text || '';
    document.getElementById('btn1Link').value = b.btn1Link || '';
    document.getElementById('btn2Text').value = b.btn2Text || '';
    document.getElementById('btn2Link').value = b.btn2Link || '';
    document.getElementById('bannerStatus').value = b.status || 'Active';

    document.getElementById('bannerModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: var(--orange);"></i> Edit Hero Banner';

    const prevBox = document.getElementById('mediaPreviewContainer');
    const imgPrev = document.getElementById('imagePreview');
    const vidPrev = document.getElementById('videoPreview');

    if (b.mediaUrl) {
        prevBox.style.display = 'flex';
        if (b.mediaType === 'video') {
            imgPrev.style.display = 'none';
            vidPrev.style.display = 'block';
            vidPrev.src = '../' + b.mediaUrl;
        } else {
            vidPrev.style.display = 'none';
            imgPrev.style.display = 'block';
            imgPrev.src = '../' + b.mediaUrl;
        }
    } else {
        prevBox.style.display = 'none';
    }

    document.getElementById('bannerModal').classList.add('show');
}

function handleBannerFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const isVideo = file.type.startsWith('video/') || /\.(mp4|webm|ogg|mov|mkv|avi|wmv)$/i.test(file.name);
        const prevBox = document.getElementById('mediaPreviewContainer');
        const imgPrev = document.getElementById('imagePreview');
        const vidPrev = document.getElementById('videoPreview');

        prevBox.style.display = 'flex';
        const fileUrl = URL.createObjectURL(file);

        if (isVideo) {
            imgPrev.style.display = 'none';
            vidPrev.style.display = 'block';
            vidPrev.src = fileUrl;
        } else {
            vidPrev.style.display = 'none';
            imgPrev.style.display = 'block';
            imgPrev.src = fileUrl;
        }
    }
}

function saveBannerForm(e) {
    e.preventDefault();
    const form = document.getElementById('bannerForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveBannerSubmitBtn');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/save-banner.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Banner';

        if (res.success) {
            alert(res.message);
            closeBannerModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving banner.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Banner';
        alert('Server connection error.');
    });
}

function deleteBanner(bannerId) {
    if (!confirm('Are you sure you want to delete this hero banner? This cannot be undone.')) return;

    const formData = new FormData();
    formData.append('bannerId', bannerId);

    fetch('../api/delete-banner.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('banner-row-' + bannerId);
            if (row) row.remove();

            const badge = document.getElementById('sideBannerBadge');
            const stat = document.getElementById('statBanners');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) {
                    badge.textContent = c - 1;
                    if (stat) stat.textContent = c - 1;
                }
            }
        } else {
            alert('Failed to delete banner: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleBannerStatus(bannerId, newStatus) {
    const formData = new FormData();
    formData.append('bannerId', bannerId);
    formData.append('status', newStatus);

    fetch('../api/toggle-banner-status.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to update status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

// ==========================================
// INNER PAGE BANNERS HANDLERS
// ==========================================
function editPageBanner(pb) {
    document.getElementById('pageBannerForm').reset();
    document.getElementById('pbPageKey').value = pb.pageKey || '';
    document.getElementById('pbExistingBannerImage').value = pb.bannerImage || '';
    document.getElementById('pbPageDisplayName').value = (pb.pageName || pb.pageKey) + ' (' + (pb.url || (pb.pageKey + '.php')) + ')';
    document.getElementById('pbBadgeText').value = pb.badgeText || '';
    document.getElementById('pbTitle').value = pb.title || '';
    document.getElementById('pbSubtitle').value = pb.subtitle || '';
    document.getElementById('pbStatus').value = pb.status || 'Active';

    document.getElementById('pageBannerModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> Update Banner: ' + (pb.pageName || pb.pageKey);

    const prevBox = document.getElementById('pbMediaPreviewContainer');
    const imgPrev = document.getElementById('pbImagePreview');

    if (pb.bannerImage) {
        prevBox.style.display = 'block';
        imgPrev.src = '../' + pb.bannerImage;
    } else {
        prevBox.style.display = 'none';
    }

    document.getElementById('pageBannerModal').classList.add('show');
}

function closePageBannerModal() {
    document.getElementById('pageBannerModal').classList.remove('show');
}

function handlePageBannerFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const prevBox = document.getElementById('pbMediaPreviewContainer');
        const imgPrev = document.getElementById('pbImagePreview');

        prevBox.style.display = 'block';
        imgPrev.src = URL.createObjectURL(file);
    }
}

function savePageBannerForm(e) {
    e.preventDefault();
    const form = document.getElementById('pageBannerForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('savePageBannerSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';

    fetch('../api/page-banners.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update Page Banner';

        if (res.success) {
            alert(res.message);
            closePageBannerModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error updating page banner.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update Page Banner';
        alert('Server communication error.');
    });
}

// ==========================================
// ==========================================
// GALLERY MANAGEMENT HANDLERS
// ==========================================
function openGalleryModal() {
    document.getElementById('galleryForm').reset();
    document.getElementById('galleryPhotoId').value = '';
    document.getElementById('galleryExistingImageUrl').value = '';
    document.getElementById('galleryModalTitle').innerHTML = '<i class="fa-solid fa-camera-retro" style="color: var(--orange);"></i> Add Gallery Photos';
    document.getElementById('galleryMultiPreviewContainer').style.display = 'none';
    document.getElementById('galleryThumbsGrid').innerHTML = '';
    document.getElementById('galleryUploadLabel').innerHTML = 'Select Photo(s) <span style="color: var(--red);">*</span> <small style="color: #64748b; font-weight: normal;">(Upload single or multiple photos at once)</small>';
    document.getElementById('saveGallerySubmitBtn').innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Upload &amp; Save';
    document.getElementById('galleryModal').classList.add('show');
}

function closeGalleryModal() {
    document.getElementById('galleryModal').classList.remove('show');
}

function editGalleryPhoto(p) {
    document.getElementById('galleryForm').reset();
    document.getElementById('galleryPhotoId').value = p.id || '';
    document.getElementById('galleryExistingImageUrl').value = p.imageUrl || '';
    document.getElementById('galleryTitle').value = p.title || '';
    document.getElementById('gallerySeason').value = p.season || 'Season 2';
    document.getElementById('galleryCategory').value = p.category || 'glimpses';

    document.getElementById('galleryModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: var(--orange);"></i> Edit Gallery Photo';
    document.getElementById('galleryUploadLabel').innerHTML = 'Change Photo <small style="color: #64748b; font-weight: normal;">(Leave empty to keep existing image)</small>';
    document.getElementById('saveGallerySubmitBtn').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update Photo';

    const prevBox = document.getElementById('galleryMultiPreviewContainer');
    const thumbsGrid = document.getElementById('galleryThumbsGrid');
    const countBadge = document.getElementById('gallerySelectedCount');

    if (p.imageUrl) {
        prevBox.style.display = 'block';
        countBadge.textContent = '1 Existing Photo';
        thumbsGrid.innerHTML = `
            <div style="position: relative; border-radius: 6px; overflow: hidden; border: 2px solid var(--primary); height: 70px; background: #000;">
                <img src="../${p.imageUrl}" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview">
            </div>
        `;
    } else {
        prevBox.style.display = 'none';
        thumbsGrid.innerHTML = '';
    }

    document.getElementById('galleryModal').classList.add('show');
}

function handleGalleryMultiFileSelect(input) {
    const prevBox = document.getElementById('galleryMultiPreviewContainer');
    const thumbsGrid = document.getElementById('galleryThumbsGrid');
    const countBadge = document.getElementById('gallerySelectedCount');

    thumbsGrid.innerHTML = '';
    if (input.files && input.files.length > 0) {
        prevBox.style.display = 'block';
        countBadge.textContent = input.files.length + ' Photo' + (input.files.length > 1 ? 's' : '') + ' Selected';

        Array.from(input.files).forEach((file, index) => {
            const thumbDiv = document.createElement('div');
            thumbDiv.style.cssText = 'position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #cbd5e1; height: 70px; background: #000;';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
            img.alt = file.name;

            const numTag = document.createElement('span');
            numTag.textContent = (index + 1);
            numTag.style.cssText = 'position: absolute; bottom: 2px; right: 2px; background: rgba(0,0,0,0.7); color: #fff; font-size: 10px; font-weight: 700; padding: 1px 4px; border-radius: 3px;';

            thumbDiv.appendChild(img);
            thumbDiv.appendChild(numTag);
            thumbsGrid.appendChild(thumbDiv);
        });
    } else {
        prevBox.style.display = 'none';
    }
}

function saveGalleryForm(e) {
    e.preventDefault();
    const form = document.getElementById('galleryForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveGallerySubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Uploading &amp; Saving...';

    fetch('../api/gallery.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Upload &amp; Save';

        if (res.success) {
            alert(res.message || 'Gallery photos uploaded successfully!');
            closeGalleryModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving photo(s).');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Upload &amp; Save';
        alert('Server communication error.');
    });
}

function deleteGalleryPhoto(photoId) {
    if (!confirm('Are you sure you want to delete this gallery photo?')) return;

    const formData = new FormData();
    formData.append('photoId', photoId);

    fetch('../api/gallery.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('gallery-row-' + photoId);
            if (row) row.remove();

            const badge = document.getElementById('sideGalleryBadge');
            const stat = document.getElementById('statGallery');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) {
                    badge.textContent = c - 1;
                    if (stat) stat.textContent = c - 1;
                }
            }
        } else {
            alert(res.message || 'Failed to delete photo.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminGalleryTable() {
    const seasonVal = (document.getElementById('adminGallerySeasonFilter')?.value || 'all').toLowerCase();
    const catVal = (document.getElementById('adminGalleryCatFilter')?.value || 'all').toLowerCase();
    const rows = document.querySelectorAll('.gallery-admin-row');

    rows.forEach(row => {
        const rowSeason = (row.getAttribute('data-season') || '').toLowerCase();
        const rowCat = (row.getAttribute('data-cat') || '').toLowerCase();

        const matchSeason = (seasonVal === 'all' || rowSeason === seasonVal);
        const matchCat = (catVal === 'all' || rowCat === catVal);

        if (matchSeason && matchCat) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ==========================================
// CONTACT SETTINGS HANDLERS
// ==========================================
function saveContactSettings(e) {
    e.preventDefault();
    const form = document.getElementById('contactSettingsForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveContactSettingsBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/save-contact-settings.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Contact Settings';

        if (res.success) {
            alert(res.message);
        } else {
            alert(res.message || 'Error saving settings.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Contact Settings';
        alert('Server communication error.');
    });
}

function previewMediaInPopup(url, type) {
    const content = document.getElementById('mediaPopupContent');
    if (type === 'video') {
        content.innerHTML = `<video src="${url}" controls autoplay style="max-width: 85vw; max-height: 80vh; border-radius: 8px;"></video>`;
    } else {
        content.innerHTML = `<img src="${url}" style="max-width: 85vw; max-height: 80vh; border-radius: 8px; object-fit: contain;">`;
    }
    document.getElementById('mediaPopupModal').classList.add('show');
}

function closeMediaPopup(e) {
    if (e.target.id === 'mediaPopupModal') {
        document.getElementById('mediaPopupModal').classList.remove('show');
    }
}

// Instant AJAX Status Update without Page Reload
function updatePlayerStatus(playerId, newStatus, e) {
    if (e) e.preventDefault();
    document.querySelectorAll('.action-dropdown-menu').forEach(m => m.classList.remove('show'));

    const formData = new FormData();
    formData.append('playerId', playerId);
    formData.append('status', newStatus);

    fetch('../api/update-status.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const badge = document.getElementById('status-' + playerId);
            if (badge) {
                badge.className = 'badge-status ' + newStatus;
                badge.textContent = newStatus;
            }
        } else {
            alert('Failed to update status: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('Server communication error.');
    });
}

// Instant Delete Contact Message via AJAX
function deleteMessage(msgId) {
    if (!confirm('Are you sure you want to delete this message?')) return;

    const formData = new FormData();
    formData.append('id', msgId);

    fetch('../api/delete-message.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('msg-row-' + msgId);
            if (row) {
                row.remove();
            }
            // Update counter badge
            const statMsg = document.getElementById('statMessages');
            if (statMsg) {
                let count = parseInt(statMsg.textContent || '1');
                if (count > 0) statMsg.textContent = count - 1;
            }
        } else {
            alert('Failed to delete message: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('Server error while deleting message.');
    });
}

// Contact Message Full Details Modal & Email Reply Handler
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

function copyEmailToClipboard(email) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(email).then(() => {
            alert('Email address copied to clipboard: ' + email);
        });
    } else {
        const temp = document.createElement('input');
        temp.value = email;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        alert('Email address copied to clipboard: ' + email);
    }
}

let currentMsgData = null;

function viewMessageDetails(m, focusReply = false) {
    if (typeof m === 'string') {
        try { m = JSON.parse(m); } catch(e) {}
    }
    currentMsgData = m;

    const defaultReplyBody = `Hi ${m.fullName || 'User'},\n\nThank you for contacting UP Pro Handball League (UPPHL).\n\nRegarding your inquiry:\n"${m.message || ''}"\n\n-------------------------\n[Type your response here]\n\nBest regards,\nUPPHL Support Team\nOfficial Website: www.upphl.in`;
    const defaultSubject = `Re: ${m.subject || 'UPPHL Support Inquiry'}`;

    const content = document.getElementById('modalContactMessageContent');
    content.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 52px; height: 52px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: grid; place-items: center; font-size: 22px; font-weight: 700; border: 2px solid #bfdbfe;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <h3 style="margin: 0; color: #0f172a; font-size: 19px; font-weight: 800;">${escapeHtml(m.fullName || 'Anonymous')}</h3>
                    <span style="font-size: 13px; color: #64748b; font-weight: 600;"><i class="fa-regular fa-clock"></i> Received: ${escapeHtml(m.submittedAt || 'N/A')}</span>
                </div>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" onclick="sendReplyVia('gmail')" class="btn-act" style="background: #ea4335; color: #fff; padding: 9px 16px; font-size: 13px; border-radius: 8px; box-shadow: 0 2px 8px rgba(234, 67, 53, 0.3);">
                    <i class="fa-brands fa-google"></i> Reply via Gmail
                </button>
            </div>
        </div>

        <div class="modal-grid-2" style="margin-bottom: 18px;">
            <div class="modal-info-box">
                <label><i class="fa-solid fa-envelope" style="color: #2563eb;"></i> Email Address</label>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <span style="color: #2563eb; font-weight: 700; word-break: break-all;">${escapeHtml(m.email || 'N/A')}</span>
                    <button type="button" onclick="copyEmailToClipboard('${escapeHtml(m.email || '')}')" style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; padding: 3px 8px; font-size: 11px; cursor: pointer; color: #475569;" title="Copy Email">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="modal-info-box">
                <label><i class="fa-solid fa-phone" style="color: #10b981;"></i> Phone Number</label>
                <span>${m.phone ? `<a href="tel:${escapeHtml(m.phone)}" style="color: #10b981; text-decoration: none; font-weight: 700;">${escapeHtml(m.phone)}</a>` : '<em style="color:#94a3b8">Not provided</em>'}</span>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 18px;">
            <label style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; display: block; margin-bottom: 5px;"><i class="fa-solid fa-tag" style="color: var(--orange);"></i> Subject</label>
            <div style="font-size: 15px; font-weight: 700; color: #0f172a;">${escapeHtml(m.subject || 'No Subject')}</div>
        </div>

        <div style="background: #f8fafc; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 22px;">
            <label style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; display: block; margin-bottom: 10px;"><i class="fa-solid fa-message" style="color: #0ea5e9;"></i> Full Message Received</label>
            <div style="font-size: 14.5px; line-height: 1.7; color: #1e293b; background: #ffffff; padding: 16px; border-radius: 8px; border: 1px solid #cbd5e1; white-space: pre-wrap; word-break: break-word;">${escapeHtml(m.message || 'No message content.')}</div>
        </div>

        <!-- Interactive Email Reply Box -->
        <div id="replyComposerSection" style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                <h4 style="margin: 0; color: #15803d; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-paper-plane"></i> Compose &amp; Send Reply
                </h4>
                <div style="display: flex; gap: 6px; align-items: center;">
                    <span style="font-size: 12px; font-weight: 600; color: #475569;">Quick Template:</span>
                    <select onchange="applyReplyTemplate(this.value)" style="padding: 4px 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; background: #fff; cursor: pointer;">
                        <option value="custom">Custom Reply</option>
                        <option value="login_issue">Password / Login Help</option>
                        <option value="payment_verify">Payment Verification Done</option>
                        <option value="general_thanks">General Inquiry Response</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">Reply Subject</label>
                <input type="text" id="replySubjectInput" value="${escapeHtml(defaultSubject)}" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; background: #fff;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 4px;">Reply Body Message</label>
                <textarea id="replyBodyInput" rows="7" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; line-height: 1.5; background: #fff; resize: vertical;">${escapeHtml(defaultReplyBody)}</textarea>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <button type="button" onclick="sendReplyVia('gmail')" class="btn-act" style="background: #ea4335; color: #fff; padding: 10px 20px; font-size: 14px; border-radius: 8px; font-weight: 700; box-shadow: 0 4px 12px rgba(234, 67, 53, 0.25);">
                    <i class="fa-brands fa-google"></i> Open in Gmail (Web)
                </button>
                <button type="button" onclick="sendReplyVia('outlook')" class="btn-act" style="background: #0078d4; color: #fff; padding: 10px 18px; font-size: 14px; border-radius: 8px; font-weight: 700;">
                    <i class="fa-brands fa-microsoft"></i> Open in Outlook (Web)
                </button>
                <button type="button" onclick="sendReplyVia('mailto')" class="btn-act" style="background: #2563eb; color: #fff; padding: 10px 18px; font-size: 14px; border-radius: 8px;">
                    <i class="fa-solid fa-envelope"></i> Default Mail App
                </button>
                <button type="button" onclick="copyReplyText()" class="btn-act" style="background: #fff; color: #334155; border: 1px solid #cbd5e1; padding: 10px 14px; font-size: 13.5px;">
                    <i class="fa-regular fa-copy"></i> Copy Message
                </button>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" class="btn-act" style="background: #e2e8f0; color: #334155; padding: 9px 18px; font-size: 13.5px;" onclick="closeContactMessageModal()">
                Close
            </button>
            <button type="button" class="btn-act reject" style="padding: 9px 18px; font-size: 13.5px;" onclick="closeContactMessageModal(); deleteMessage('${escapeHtml(m.id)}');">
                <i class="fa-solid fa-trash"></i> Delete Message
            </button>
        </div>
    `;
    document.getElementById('contactMessageModal').classList.add('show');

    if (focusReply) {
        setTimeout(() => {
            const sec = document.getElementById('replyComposerSection');
            if (sec) sec.scrollIntoView({ behavior: 'smooth' });
        }, 150);
    }
}

function openReplyComposer(m) {
    viewMessageDetails(m, true);
}

function applyReplyTemplate(tpl) {
    if (!currentMsgData) return;
    const bodyEl = document.getElementById('replyBodyInput');
    const name = currentMsgData.fullName || 'User';

    if (tpl === 'login_issue') {
        bodyEl.value = `Dear ${name},\n\nGreetings from UP Pro Handball League!\n\nWe have checked your registration details. Your Player ID and login access are active.\nPlease use the Player Login portal at www.upphl.in/players.php with your registered email/ID.\n\nIf you still face any issue, please reply to this email with your registered mobile number.\n\nBest regards,\nUPPHL Technical Support Team`;
    } else if (tpl === 'payment_verify') {
        bodyEl.value = `Dear ${name},\n\nThank you for registering with UP Pro Handball League (UPPHL).\n\nYour registration fee payment and application documents have been verified and approved successfully by the league management committee.\nYou can view your profile and download your receipt by logging into your account.\n\nBest regards,\nUPPHL Registration Desk`;
    } else if (tpl === 'general_thanks') {
        bodyEl.value = `Dear ${name},\n\nThank you for contacting UP Pro Handball League.\n\nWe have received your message and our support team is looking into it. We will get back to you shortly.\n\nBest regards,\nUPPHL Support Team\nOfficial Website: www.upphl.in`;
    } else {
        bodyEl.value = `Hi ${name},\n\nThank you for reaching out to UP Pro Handball League (UPPHL).\n\nRegarding your inquiry:\n"${currentMsgData.message || ''}"\n\n-------------------------\n[Type your response here]\n\nBest regards,\nUPPHL Support Team\nWebsite: www.upphl.in`;
    }
}

function sendReplyVia(platform) {
    if (!currentMsgData) return;
    const email = (currentMsgData.email || '').trim();
    if (!email) {
        alert('No sender email address found.');
        return;
    }

    const subject = (document.getElementById('replySubjectInput')?.value || `Re: ${currentMsgData.subject || 'UPPHL Support'}`).trim();
    const body = (document.getElementById('replyBodyInput')?.value || '').trim();

    if (platform === 'gmail') {
        // Direct Gmail Web Compose in new tab
        const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(email)}&su=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.open(gmailUrl, '_blank');
    } else if (platform === 'outlook') {
        // Direct Outlook Web Compose in new tab
        const outlookUrl = `https://outlook.live.com/mail/0/deeplink/compose?to=${encodeURIComponent(email)}&subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.open(outlookUrl, '_blank');
    } else {
        // Desktop / Mobile default mail handler
        const mailtoUrl = `mailto:${encodeURIComponent(email)}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.location.href = mailtoUrl;
    }
}

function copyReplyText() {
    const body = document.getElementById('replyBodyInput')?.value || '';
    if (navigator.clipboard) {
        navigator.clipboard.writeText(body).then(() => {
            alert('Reply message text copied to clipboard!');
        });
    } else {
        const temp = document.createElement('textarea');
        temp.value = body;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        alert('Reply message text copied to clipboard!');
    }
}

function closeContactMessageModal() {
    const modal = document.getElementById('contactMessageModal');
    if (modal) modal.classList.remove('show');
}

// ==========================================
// 10-PER-PAGE AUTOMATIC PAGINATION (ADMIN)
// ==========================================
let adminCurrentPage = 1;
const ADMIN_PER_PAGE = 10;

function changeAdminPage(p) {
    adminCurrentPage = parseInt(p) || 1;
    setupAdminPagination();
    const tableEl = document.querySelector('.table-card');
    if (tableEl) {
        tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function setupAdminPagination() {
    const tbody = document.getElementById('playersTbody');
    if (!tbody) return;
    const allRows = Array.from(tbody.querySelectorAll('tr[id^="row-"]'));
    const rows = allRows.filter(r => r.style.display !== 'none' || r.getAttribute('data-hidden-by-search') !== '1');
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / ADMIN_PER_PAGE) || 1;

    if (adminCurrentPage > totalPages) adminCurrentPage = totalPages;
    if (adminCurrentPage < 1) adminCurrentPage = 1;

    const startIdx = (adminCurrentPage - 1) * ADMIN_PER_PAGE;
    const endIdx = startIdx + ADMIN_PER_PAGE;

    rows.forEach((row, idx) => {
        if (idx >= startIdx && idx < endIdx) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    const info = document.getElementById('adminPageInfoText');
    if (info) {
        info.textContent = totalRows > 0 
            ? `Showing ${startIdx + 1}–${Math.min(endIdx, totalRows)} of ${totalRows} registered players (Page ${adminCurrentPage} of ${totalPages})`
            : `Showing 0 players`;
    }

    const btnsContainer = document.getElementById('adminPaginationButtons');
    if (btnsContainer) {
        if (totalPages <= 1) {
            btnsContainer.innerHTML = '';
            return;
        }
        let html = '';
        html += `<button type="button" onclick="changeAdminPage(${adminCurrentPage - 1})" ${adminCurrentPage === 1 ? 'disabled' : ''} class="admin-pagin-btn"><i class="fa-solid fa-chevron-left"></i> Prev</button>`;

        // Smart sliding window: show maximum 5 page numbers
        const MAX_PAGES = 5;
        let startPage = Math.max(1, adminCurrentPage - Math.floor(MAX_PAGES / 2));
        let endPage = startPage + MAX_PAGES - 1;

        if (endPage > totalPages) {
            endPage = totalPages;
            startPage = Math.max(1, endPage - MAX_PAGES + 1);
        }

        // First page and leading ellipsis if needed
        if (startPage > 1) {
            html += `<button type="button" onclick="changeAdminPage(1)" class="admin-pagin-btn">1</button>`;
            if (startPage > 2) {
                html += `<span style="padding: 0 4px; color: #94a3b8; font-weight: 700; user-select: none;">...</span>`;
            }
        }

        // Middle 5 pages window
        for (let p = startPage; p <= endPage; p++) {
            html += `<button type="button" onclick="changeAdminPage(${p})" class="admin-pagin-btn ${p === adminCurrentPage ? 'active' : ''}">${p}</button>`;
        }

        // Trailing ellipsis and last page if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<span style="padding: 0 4px; color: #94a3b8; font-weight: 700; user-select: none;">...</span>`;
            }
            html += `<button type="button" onclick="changeAdminPage(${totalPages})" class="admin-pagin-btn">${totalPages}</button>`;
        }

        html += `<button type="button" onclick="changeAdminPage(${adminCurrentPage + 1})" ${adminCurrentPage === totalPages ? 'disabled' : ''} class="admin-pagin-btn">Next <i class="fa-solid fa-chevron-right"></i></button>`;
        btnsContainer.innerHTML = html;
    }
}

// ==========================================
// FRANCHISE TEAMS MANAGEMENT JAVASCRIPT
// ==========================================
let allTeamsCache = <?= json_encode($teams, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;

function filterAdminTeams() {
    const seasonFilter = (document.getElementById('adminTeamsSeasonFilter') ? document.getElementById('adminTeamsSeasonFilter').value : 'all').toLowerCase();
    const query = (document.getElementById('adminTeamSearchInput') ? document.getElementById('adminTeamSearchInput').value : '').toLowerCase().trim();

    const rows = document.querySelectorAll('#teamsTbody tr[data-season]');
    let visibleCount = 0;

    rows.forEach(r => {
        const rSeason = (r.getAttribute('data-season') || '').toLowerCase();
        const rSearch = (r.getAttribute('data-search') || '').toLowerCase();

        const matchSeason = (seasonFilter === 'all' || rSeason === seasonFilter);
        const matchQuery = (query === '' || rSearch.includes(query));

        if (matchSeason && matchQuery) {
            r.style.display = '';
            visibleCount++;
        } else {
            r.style.display = 'none';
        }
    });

    const noRow = document.getElementById('noTeamsRow');
    if (noRow) {
        noRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }
}

function handleTeamNameChange(val) {
    const isEditing = document.getElementById('teamIsEditing').value === '1';
    if (!isEditing) {
        const slugInput = document.getElementById('teamSlugInput');
        if (slugInput) {
            slugInput.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        }
    }
}

function previewTeamImage(input, imgId, wrapId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            const wrap = document.getElementById(wrapId);
            if (img) img.src = e.target.result;
            if (wrap) wrap.style.display = 'flex';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function addTeamPlayerRow(name = '', pos = 'Pivot', no = '01', photoUrl = '') {
    const container = document.getElementById('teamPlayersContainer');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'team-player-row-item';
    row.style = 'display: grid; grid-template-columns: 46px 2fr 1.3fr 70px 36px; gap: 10px; align-items: center; background: #fff; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1;';

    const safeName = String(name || '').replace(/"/g, '&quot;');
    const safeNo = String(no || '').replace(/"/g, '&quot;');
    const safePhoto = String(photoUrl || '').replace(/"/g, '&quot;');
    const hasPhoto = Boolean(photoUrl && photoUrl.trim() !== '');
    const thumbSrc = hasPhoto ? (photoUrl.startsWith('http') || photoUrl.startsWith('data:') ? photoUrl : ('../' + photoUrl)) : '';

    row.innerHTML = `
        <div class="player-photo-picker-box" style="position: relative; width: 42px; height: 42px;">
            <input type="hidden" name="playerPhotos[]" class="player-photo-url-input" value="${safePhoto}">
            <input type="file" name="playerPhotoFiles[]" class="player-photo-file-input" accept="image/*" style="display: none;" onchange="previewSquadPlayerPhoto(this)">
            
            <div class="player-thumb-wrap" onclick="this.previousElementSibling.click()" style="width: 42px; height: 42px; border-radius: 50%; border: 2px solid ${hasPhoto ? '#8b5cf6' : '#cbd5e1'}; overflow: hidden; background: #f1f5f9; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative;" title="Click to choose player photo">
                <img class="player-thumb-img" src="${thumbSrc}" style="width: 100%; height: 100%; object-fit: cover; display: ${hasPhoto ? 'block' : 'none'};" alt="Player Photo" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                <div class="player-thumb-placeholder" style="display: ${hasPhoto ? 'none' : 'flex'}; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px;">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
            <button type="button" class="player-thumb-remove-btn" onclick="clearSquadPlayerPhoto(this)" style="display: ${hasPhoto ? 'flex' : 'none'}; position: absolute; top: -3px; right: -3px; width: 18px; height: 18px; border-radius: 50%; background: #ef4444; color: #fff; border: 1.5px solid #fff; font-size: 9px; cursor: pointer; align-items: center; justify-content: center; z-index: 5;" title="Remove Photo">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <input type="text" name="playerNames[]" value="${safeName}" placeholder="Player Name" required style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; font-weight: 600;">
        <select name="playerPositions[]" style="padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; font-weight: 600; background: #fff;">
            <option value="Goalkeeper" ${pos === 'Goalkeeper' ? 'selected' : ''}>Goalkeeper</option>
            <option value="Centre Back" ${pos === 'Centre Back' ? 'selected' : ''}>Centre Back</option>
            <option value="Left Wing" ${pos === 'Left Wing' ? 'selected' : ''}>Left Wing</option>
            <option value="Right Wing" ${pos === 'Right Wing' ? 'selected' : ''}>Right Wing</option>
            <option value="Pivot / Line" ${pos === 'Pivot / Line' || pos === 'Pivot' ? 'selected' : ''}>Pivot / Line</option>
            <option value="Left Back" ${pos === 'Left Back' ? 'selected' : ''}>Left Back</option>
            <option value="Right Back" ${pos === 'Right Back' ? 'selected' : ''}>Right Back</option>
            <option value="Defender" ${pos === 'Defender' ? 'selected' : ''}>Defender</option>
            <option value="Player" ${pos === 'Player' ? 'selected' : ''}>Player</option>
        </select>
        <input type="text" name="playerNumbers[]" value="${safeNo}" placeholder="#00" style="padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; text-align: center; font-weight: 700;">
        <button type="button" onclick="this.closest('.team-player-row-item').remove()" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 4px; width: 32px; height: 32px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Remove player">
            <i class="fa-solid fa-trash" style="font-size: 12px;"></i>
        </button>
    `;
    container.appendChild(row);
}

function previewSquadPlayerPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const box = input.closest('.player-photo-picker-box');
        if (!box) return;
        const img = box.querySelector('.player-thumb-img');
        const placeholder = box.querySelector('.player-thumb-placeholder');
        const removeBtn = box.querySelector('.player-thumb-remove-btn');
        const thumbWrap = box.querySelector('.player-thumb-wrap');
        const urlInput = box.querySelector('.player-photo-url-input');

        reader.onload = function(e) {
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (placeholder) placeholder.style.display = 'none';
            if (removeBtn) removeBtn.style.display = 'flex';
            if (thumbWrap) thumbWrap.style.borderColor = '#8b5cf6';
            if (urlInput) urlInput.value = '';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearSquadPlayerPhoto(btn) {
    const box = btn.closest('.player-photo-picker-box');
    if (!box) return;
    const fileInput = box.querySelector('.player-photo-file-input');
    const urlInput = box.querySelector('.player-photo-url-input');
    const img = box.querySelector('.player-thumb-img');
    const placeholder = box.querySelector('.player-thumb-placeholder');
    const thumbWrap = box.querySelector('.player-thumb-wrap');

    if (fileInput) fileInput.value = '';
    if (urlInput) urlInput.value = '';
    if (img) {
        img.src = '';
        img.style.display = 'none';
    }
    if (placeholder) placeholder.style.display = 'flex';
    if (thumbWrap) thumbWrap.style.borderColor = '#cbd5e1';
    btn.style.display = 'none';
}

function previewLeadershipPhoto(input, imgId, wrapId, removeInputId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            const wrap = document.getElementById(wrapId);
            const removeInput = document.getElementById(removeInputId);
            if (img) img.src = e.target.result;
            if (wrap) wrap.style.display = 'flex';
            if (removeInput) removeInput.value = '0';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearLeadershipPhoto(type) {
    const fileInput = document.getElementById(`team${type}PhotoFileInput`);
    const urlInput = document.getElementById(`team${type}PhotoUrlInput`);
    const removeInput = document.getElementById(`team${type}PhotoRemoveInput`);
    const wrap = document.getElementById(`team${type}PhotoPreviewWrap`);
    const img = document.getElementById(`team${type}PhotoPreview`);

    if (fileInput) fileInput.value = '';
    if (urlInput) urlInput.value = '';
    if (removeInput) removeInput.value = '1';
    if (img) img.src = '';
    if (wrap) wrap.style.display = 'none';
}

function openTeamModal(team = null) {
    const form = document.getElementById('teamForm');
    if (!form) return;
    form.reset();

    const container = document.getElementById('teamPlayersContainer');
    if (container) container.innerHTML = '';

    const titleEl = document.getElementById('teamModalTitle');
    const isEditEl = document.getElementById('teamIsEditing');
    const slugInput = document.getElementById('teamSlugInput');
    const footerLogoWrap = document.getElementById('teamFooterLogoPreviewWrap');
    const posterWrap = document.getElementById('teamPosterPreviewWrap');

    if (team) {
        // Edit Mode
        if (titleEl) titleEl.innerHTML = `<i class="fa-solid fa-pen-to-square" style="color: #ea580c;"></i> Edit Franchise Team: ${team.name}`;
        if (isEditEl) isEditEl.value = '1';
        if (slugInput) {
            slugInput.value = team.id || '';
            slugInput.readOnly = true;
        }

        document.getElementById('teamNameInput').value = team.name || '';
        document.getElementById('teamSeasonInput').value = team.season || 'Season 1';
        document.getElementById('teamCityInput').value = team.city || '';
        document.getElementById('teamSortOrderInput').value = team.sortOrder || 1;
        document.getElementById('teamAccentColorInput').value = team.accentColor || '#ea580c';
        document.getElementById('teamAccentColorPicker').value = team.accentColor || '#ea580c';
        document.getElementById('teamStatusSelect').value = team.status || 'Active';

        // Images preview
        const curFooterLogo = team.footerLogo || team.logo || '';
        const curPoster = team.posterImage || curFooterLogo || '';

        document.getElementById('teamFooterLogoUrlInput').value = curFooterLogo;
        document.getElementById('teamPosterUrlInput').value = curPoster;

        if (curFooterLogo && footerLogoWrap) {
            document.getElementById('teamFooterLogoPreview').src = curFooterLogo.startsWith('http') ? curFooterLogo : ('../' + curFooterLogo);
            footerLogoWrap.style.display = 'flex';
        } else if (footerLogoWrap) {
            footerLogoWrap.style.display = 'none';
        }

        if (curPoster && posterWrap) {
            document.getElementById('teamPosterPreview').src = curPoster.startsWith('http') ? curPoster : ('../' + curPoster);
            posterWrap.style.display = 'flex';
        } else if (posterWrap) {
            posterWrap.style.display = 'none';
        }

        // Leadership Data
        document.getElementById('teamCoachNameInput').value = team.coach ? (team.coach.name || '') : '';
        document.getElementById('teamCoachRoleInput').value = team.coach ? (team.coach.role || 'Head Coach') : 'Head Coach';

        document.getElementById('teamOwnerNameInput').value = team.owner ? (team.owner.name || '') : '';
        document.getElementById('teamOwnerRoleInput').value = team.owner ? (team.owner.role || 'Franchise Owner') : 'Franchise Owner';

        document.getElementById('teamCaptainNameInput').value = team.captain ? (team.captain.name || '') : '';
        document.getElementById('teamCaptainRoleInput').value = team.captain ? (team.captain.role || 'Team Captain') : 'Team Captain';

        // Leadership Photos & Previews
        ['Coach', 'Owner', 'Captain'].forEach(type => {
            const fileIn = document.getElementById(`team${type}PhotoFileInput`);
            const urlIn = document.getElementById(`team${type}PhotoUrlInput`);
            const remIn = document.getElementById(`team${type}PhotoRemoveInput`);
            const wrap = document.getElementById(`team${type}PhotoPreviewWrap`);
            const img = document.getElementById(`team${type}PhotoPreview`);

            if (fileIn) fileIn.value = '';
            if (remIn) remIn.value = '0';

            const photoUrl = (team && team[type.toLowerCase()]) ? (team[type.toLowerCase()].photoUrl || '') : '';
            if (urlIn) urlIn.value = photoUrl;

            if (photoUrl && wrap && img) {
                img.src = photoUrl.startsWith('http') || photoUrl.startsWith('data:') ? photoUrl : ('../' + photoUrl);
                wrap.style.display = 'flex';
            } else if (wrap) {
                wrap.style.display = 'none';
            }
        });

        // Squad Players
        if (team.players && Array.isArray(team.players) && team.players.length > 0) {
            team.players.forEach(p => {
                addTeamPlayerRow(p.name || '', p.pos || 'Pivot', p.no || '01', p.photoUrl || p.photo || '');
            });
        } else {
            addTeamPlayerRow('', 'Centre Back', '07', '');
            addTeamPlayerRow('', 'Goalkeeper', '01', '');
            addTeamPlayerRow('', 'Left Wing', '09', '');
            addTeamPlayerRow('', 'Right Wing', '11', '');
        }
    } else {
        // Add New Mode
        if (titleEl) titleEl.innerHTML = `<i class="fa-solid fa-shield-halved" style="color: #ea580c;"></i> Add Franchise Team`;
        if (isEditEl) isEditEl.value = '0';
        if (slugInput) {
            slugInput.value = '';
            slugInput.readOnly = false;
        }

        document.getElementById('teamSeasonInput').value = 'Season 1';
        document.getElementById('teamSortOrderInput').value = document.querySelectorAll('#teamsTbody tr[data-season]').length + 1;
        document.getElementById('teamAccentColorInput').value = '#ea580c';
        document.getElementById('teamAccentColorPicker').value = '#ea580c';
        document.getElementById('teamStatusSelect').value = 'Active';

        if (footerLogoWrap) footerLogoWrap.style.display = 'none';
        if (posterWrap) posterWrap.style.display = 'none';

        ['Coach', 'Owner', 'Captain'].forEach(type => {
            const fileIn = document.getElementById(`team${type}PhotoFileInput`);
            const urlIn = document.getElementById(`team${type}PhotoUrlInput`);
            const remIn = document.getElementById(`team${type}PhotoRemoveInput`);
            const wrap = document.getElementById(`team${type}PhotoPreviewWrap`);
            if (fileIn) fileIn.value = '';
            if (urlIn) urlIn.value = '';
            if (remIn) remIn.value = '0';
            if (wrap) wrap.style.display = 'none';
        });

        // Default empty squad rows
        addTeamPlayerRow('', 'Centre Back', '07', '');
        addTeamPlayerRow('', 'Goalkeeper', '01', '');
        addTeamPlayerRow('', 'Left Wing', '09', '');
        addTeamPlayerRow('', 'Right Wing', '11', '');
        addTeamPlayerRow('', 'Pivot / Line', '14', '');
        addTeamPlayerRow('', 'Left Back', '04', '');
        addTeamPlayerRow('', 'Right Back', '08', '');
        addTeamPlayerRow('', 'Defender', '18', '');
    }

    document.getElementById('teamModal').classList.add('show');
}

function editTeam(team) {
    openTeamModal(team);
}

function closeTeamModal() {
    const modal = document.getElementById('teamModal');
    if (modal) modal.classList.remove('show');
}

function saveTeamForm(e) {
    e.preventDefault();
    const form = document.getElementById('teamForm');
    const submitBtn = document.getElementById('saveTeamSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving Team...';
    }

    const formData = new FormData(form);

    fetch('../api/teams.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.message || 'Franchise team saved successfully!');
            window.location.reload();
        } else {
            alert('Failed to save team: ' + (res.message || 'Unknown error'));
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Franchise Team';
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Server communication error.');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Franchise Team';
        }
    });
}

function deleteTeam(teamId, teamName) {
    if (!confirm(`Are you sure you want to delete the franchise team "${teamName}"? This will remove the team from the website and footer.`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', teamId);

    fetch('../api/teams.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('team-row-' + teamId);
            if (row) row.remove();
            alert('Team deleted successfully.');
            const badge = document.getElementById('sideTeamsBadge');
            if (badge) {
                const cur = parseInt(badge.textContent) || 0;
                badge.textContent = Math.max(0, cur - 1);
            }
        } else {
            alert('Failed to delete team: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleTeamStatus(teamId) {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('id', teamId);

    fetch('../api/teams.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const badge = document.getElementById('team-status-badge-' + teamId);
            if (badge) {
                const isActive = (res.status === 'Active');
                badge.className = 'status-badge ' + (isActive ? 'status-approved' : 'status-rejected');
                badge.innerHTML = `<i class="fa-solid ${isActive ? 'fa-circle-check' : 'fa-circle-xmark'}"></i> ${res.status}`;
            }
        } else {
            alert('Failed to update status: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

// ==========================================
// LATEST NEWS & UPDATES HANDLERS
// ==========================================
function openNewsModal() {
    const form = document.getElementById('newsForm');
    if (form) form.reset();
    document.getElementById('newsId').value = '';
    document.getElementById('newsExistingImage').value = '';
    document.getElementById('newsModalTitle').innerHTML = '<i class="fa-solid fa-newspaper" style="color: #e11d48;"></i> Add News Update';
    document.getElementById('newsImageRequiredStar').style.display = 'inline';
    document.getElementById('newsImageInput').required = true;
    document.getElementById('newsImagePreviewContainer').style.display = 'none';
    document.getElementById('newsImagePreviewImg').src = '';
    document.getElementById('newsDate').value = '<?= date("jS F Y") ?>';
    document.getElementById('newsGalleryLink').value = 'gallery.php?cat=news';
    document.getElementById('newsCategory').value = 'News';
    document.getElementById('newsSortOrder').value = '1';
    document.getElementById('newsStatus').value = 'Active';
    document.getElementById('newsModal').classList.add('show');
}

function closeNewsModal() {
    const m = document.getElementById('newsModal');
    if (m) m.classList.remove('show');
}

function previewNewsImageFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const preview = document.getElementById('newsImagePreviewImg');
        const box = document.getElementById('newsImagePreviewContainer');
        preview.src = URL.createObjectURL(file);
        box.style.display = 'block';
    }
}

function editNews(n) {
    const form = document.getElementById('newsForm');
    if (form) form.reset();
    document.getElementById('newsId').value = n.id || '';
    document.getElementById('newsExistingImage').value = n.imageUrl || '';
    document.getElementById('newsTitle').value = n.title || '';
    document.getElementById('newsDate').value = n.date || '';
    document.getElementById('newsCategory').value = n.category || '';
    document.getElementById('newsDescription').value = n.description || '';
    document.getElementById('newsGalleryLink').value = n.galleryLink || 'gallery.php?cat=news';
    document.getElementById('newsSortOrder').value = n.sortOrder !== undefined ? n.sortOrder : 1;
    document.getElementById('newsStatus').value = n.status || 'Active';

    document.getElementById('newsImageRequiredStar').style.display = 'none';
    document.getElementById('newsImageInput').required = false;

    const box = document.getElementById('newsImagePreviewContainer');
    const preview = document.getElementById('newsImagePreviewImg');
    if (n.imageUrl) {
        preview.src = '../' + n.imageUrl;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }

    document.getElementById('newsModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #e11d48;"></i> Edit News Update';
    document.getElementById('newsModal').classList.add('show');
}

function saveNewsForm(e) {
    e.preventDefault();
    const form = document.getElementById('newsForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveNewsSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/news.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save News Update';

        if (res.success) {
            alert(res.message || 'News update saved successfully!');
            closeNewsModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving news update.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save News Update';
        alert('Server communication error.');
    });
}

function deleteNews(newsId) {
    if (!confirm('Are you sure you want to delete this news update?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('newsId', newsId);

    fetch('../api/news.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('news-row-' + newsId);
            if (row) row.remove();
            const badge = document.getElementById('sideNewsBadge');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert('Failed to delete news: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleNewsStatus(newsId) {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('newsId', newsId);

    fetch('../api/news.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to toggle status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

// ==========================================
// PARTNERS & SPONSORS HANDLERS
// ==========================================
function openPartnerModal() {
    const form = document.getElementById('partnerForm');
    if (form) form.reset();
    document.getElementById('partnerId').value = '';
    document.getElementById('partnerExistingLogo').value = '';
    document.getElementById('partnerModalTitle').innerHTML = '<i class="fa-solid fa-handshake" style="color: #059669;"></i> Add Partner / Sponsor';
    document.getElementById('partnerLogoRequiredStar').style.display = 'inline';
    document.getElementById('partnerLogoInput').required = true;
    document.getElementById('partnerLogoPreviewContainer').style.display = 'none';
    document.getElementById('partnerLogoPreviewImg').src = '';
    document.getElementById('partnerType').value = 'Official Sponsor';
    document.getElementById('partnerLinkUrl').value = '#';
    document.getElementById('partnerSortOrder').value = '1';
    document.getElementById('partnerStatus').value = 'Active';
    document.getElementById('partnerModal').classList.add('show');
}

function closePartnerModal() {
    const m = document.getElementById('partnerModal');
    if (m) m.classList.remove('show');
}

function previewPartnerLogoFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const preview = document.getElementById('partnerLogoPreviewImg');
        const box = document.getElementById('partnerLogoPreviewContainer');
        preview.src = URL.createObjectURL(file);
        box.style.display = 'block';
    }
}

function editPartner(p) {
    const form = document.getElementById('partnerForm');
    if (form) form.reset();
    document.getElementById('partnerId').value = p.id || '';
    document.getElementById('partnerExistingLogo').value = p.logoUrl || '';
    document.getElementById('partnerName').value = p.name || '';
    document.getElementById('partnerType').value = p.partnerType || 'Official Sponsor';
    document.getElementById('partnerLinkUrl').value = p.linkUrl || '#';
    document.getElementById('partnerSortOrder').value = p.sortOrder !== undefined ? p.sortOrder : 1;
    document.getElementById('partnerStatus').value = p.status || 'Active';

    document.getElementById('partnerLogoRequiredStar').style.display = 'none';
    document.getElementById('partnerLogoInput').required = false;

    const box = document.getElementById('partnerLogoPreviewContainer');
    const preview = document.getElementById('partnerLogoPreviewImg');
    if (p.logoUrl) {
        preview.src = '../' + p.logoUrl;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }

    document.getElementById('partnerModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #059669;"></i> Edit Partner / Sponsor';
    document.getElementById('partnerModal').classList.add('show');
}

function savePartnerForm(e) {
    e.preventDefault();
    const form = document.getElementById('partnerForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('savePartnerSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/partners.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Partner / Sponsor';

        if (res.success) {
            alert(res.message || 'Partner / Sponsor saved successfully!');
            closePartnerModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving partner / sponsor.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Partner / Sponsor';
        alert('Server communication error.');
    });
}

function deletePartner(partnerId) {
    if (!confirm('Are you sure you want to delete this partner / sponsor?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('partnerId', partnerId);

    fetch('../api/partners.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('partner-row-' + partnerId);
            if (row) row.remove();
            const badge = document.getElementById('sidePartnerBadge');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert('Failed to delete partner: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function togglePartnerStatus(partnerId) {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('partnerId', partnerId);

    fetch('../api/partners.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to toggle status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

// ==========================================
// LEAGUE MANAGEMENT COMMITTEE HANDLERS
// ==========================================
function openCommitteeModal() {
    const form = document.getElementById('committeeForm');
    if (form) form.reset();
    document.getElementById('committeeMemberId').value = '';
    document.getElementById('committeeExistingImage').value = '';
    document.getElementById('committeeModalTitle').innerHTML = '<i class="fa-solid fa-users-gear" style="color: #6366f1;"></i> Add Committee Member';
    document.getElementById('committeePhotoRequiredStar').style.display = 'inline';
    document.getElementById('committeePhotoInput').required = true;
    document.getElementById('committeePhotoPreviewContainer').style.display = 'none';
    document.getElementById('committeePhotoPreviewImg').src = '';
    document.getElementById('committeeBadge').value = '';
    document.getElementById('committeeSubDesignation').value = 'UP Pro Handball League';
    document.getElementById('committeeSortOrder').value = '1';
    document.getElementById('committeeStatus').value = 'Active';
    document.getElementById('committeeModal').classList.add('show');
}

function closeCommitteeModal() {
    const m = document.getElementById('committeeModal');
    if (m) m.classList.remove('show');
}

function previewCommitteePhotoFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const preview = document.getElementById('committeePhotoPreviewImg');
        const box = document.getElementById('committeePhotoPreviewContainer');
        preview.src = URL.createObjectURL(file);
        box.style.display = 'block';
    }
}

function editCommittee(cm) {
    const form = document.getElementById('committeeForm');
    if (form) form.reset();
    document.getElementById('committeeMemberId').value = cm.id || '';
    document.getElementById('committeeExistingImage').value = cm.imageUrl || '';
    document.getElementById('committeeName').value = cm.name || '';
    document.getElementById('committeeBadge').value = cm.badge || '';
    document.getElementById('committeeDesignation').value = cm.designation || '';
    document.getElementById('committeeSubDesignation').value = cm.subDesignation || '';
    document.getElementById('committeeSortOrder').value = cm.sortOrder !== undefined ? cm.sortOrder : 1;
    document.getElementById('committeeStatus').value = cm.status || 'Active';

    document.getElementById('committeePhotoRequiredStar').style.display = 'none';
    document.getElementById('committeePhotoInput').required = false;

    const box = document.getElementById('committeePhotoPreviewContainer');
    const preview = document.getElementById('committeePhotoPreviewImg');
    if (cm.imageUrl) {
        preview.src = '../' + cm.imageUrl;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }

    document.getElementById('committeeModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #6366f1;"></i> Edit Committee Member';
    document.getElementById('committeeModal').classList.add('show');
}

function saveCommitteeForm(e) {
    e.preventDefault();
    const form = document.getElementById('committeeForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveCommitteeSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/committee.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Member';

        if (res.success) {
            alert(res.message || 'Committee member saved successfully!');
            closeCommitteeModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving committee member.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Member';
        alert('Server communication error.');
    });
}

function deleteCommittee(memberId) {
    if (!confirm('Are you sure you want to delete this committee member?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('memberId', memberId);

    fetch('../api/committee.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('committee-row-' + memberId);
            if (row) row.remove();
            const badge = document.getElementById('sideCommitteeBadge');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert('Failed to delete committee member: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleCommitteeStatus(memberId) {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('memberId', memberId);

    fetch('../api/committee.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to toggle status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

// ==========================================
// LIVE & VIDEO HUB HANDLERS
// ==========================================
function switchLiveSubTab(tabName) {
    try {
        localStorage.setItem('upphl_admin_live_subtab', tabName);
    } catch(e) {}

    const btnPartners = document.getElementById('liveTabBtnPartners');
    const btnVideos = document.getElementById('liveTabBtnVideos');
    const panelPartners = document.getElementById('liveSubPanelPartners');
    const panelVideos = document.getElementById('liveSubPanelVideos');
    if (!btnPartners || !btnVideos || !panelPartners || !panelVideos) return;

    if (tabName === 'partners') {
        btnPartners.classList.add('active');
        btnPartners.style.background = 'var(--primary)';
        btnPartners.style.color = '#fff';
        btnPartners.style.border = 'none';

        btnVideos.classList.remove('active');
        btnVideos.style.background = '#fff';
        btnVideos.style.color = '#334155';
        btnVideos.style.border = '1px solid #cbd5e1';

        panelPartners.style.display = 'block';
        panelVideos.style.display = 'none';
    } else {
        btnVideos.classList.add('active');
        btnVideos.style.background = '#f97316';
        btnVideos.style.color = '#fff';
        btnVideos.style.border = 'none';

        btnPartners.classList.remove('active');
        btnPartners.style.background = '#fff';
        btnPartners.style.color = '#334155';
        btnPartners.style.border = '1px solid #cbd5e1';

        panelVideos.style.display = 'block';
        panelPartners.style.display = 'none';
    }
}

// --- Live Broadcast Partners JS ---
function openLivePartnerModal() {
    document.getElementById('livePartnerForm').reset();
    document.getElementById('livePartnerId').value = '';
    document.getElementById('livePartnerExistingLogo').value = '';
    document.getElementById('livePartnerModalTitle').innerHTML = '<i class="fa-solid fa-tower-broadcast" style="color: #dc2626;"></i> Add Broadcast Partner';
    document.getElementById('livePartnerLogoPreviewContainer').style.display = 'none';
    document.getElementById('livePartnerModal').classList.add('show');
}

function closeLivePartnerModal() {
    document.getElementById('livePartnerModal').classList.remove('show');
}

function editLivePartner(p) {
    document.getElementById('livePartnerForm').reset();
    document.getElementById('livePartnerId').value = p.id || '';
    document.getElementById('livePartnerExistingLogo').value = p.logoUrl || '';
    document.getElementById('livePartnerName').value = p.name || '';
    document.getElementById('livePartnerPlatformType').value = p.platformType || '';
    document.getElementById('livePartnerBadgeText').value = p.badgeText || '';
    document.getElementById('livePartnerMetaPill').value = p.metaPill || '';
    document.getElementById('livePartnerWatchUrl').value = p.watchUrl || '';
    document.getElementById('livePartnerButtonText').value = p.buttonText || 'Watch Live';
    document.getElementById('livePartnerCardStyle').value = p.cardStyle || 'youtube-card';
    document.getElementById('livePartnerDescription').value = p.description || '';
    document.getElementById('livePartnerIconClass').value = p.iconClass || 'fa-solid fa-tower-broadcast';
    document.getElementById('livePartnerSortOrder').value = p.sortOrder || 1;
    document.getElementById('livePartnerStatus').value = p.status || 'Active';

    document.getElementById('livePartnerModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #dc2626;"></i> Edit Broadcast Partner';

    const prevBox = document.getElementById('livePartnerLogoPreviewContainer');
    const prevImg = document.getElementById('livePartnerLogoPreviewImg');

    if (p.logoUrl) {
        prevBox.style.display = 'block';
        prevImg.src = '../' + p.logoUrl;
    } else {
        prevBox.style.display = 'none';
    }

    document.getElementById('livePartnerModal').classList.add('show');
}

function previewLivePartnerLogo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const prevBox = document.getElementById('livePartnerLogoPreviewContainer');
        const prevImg = document.getElementById('livePartnerLogoPreviewImg');
        prevBox.style.display = 'block';
        prevImg.src = URL.createObjectURL(file);
    }
}

function saveLivePartnerForm(e) {
    e.preventDefault();
    const form = document.getElementById('livePartnerForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveLivePartnerSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/live-partners.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Partner';

        if (res.success) {
            alert(res.message || 'Partner saved successfully!');
            closeLivePartnerModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving partner.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Partner';
        alert('Server communication error.');
    });
}

function deleteLivePartner(partnerId) {
    if (!confirm('Are you sure you want to delete this broadcast partner?')) return;

    const formData = new FormData();
    formData.append('partnerId', partnerId);

    fetch('../api/live-partners.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('live-partner-row-' + partnerId);
            if (row) row.remove();
            const badge = document.getElementById('badgeLivePartnersCount');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert('Failed to delete partner: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleLivePartnerStatus(partnerId, currStatus) {
    const nextStatus = (currStatus === 'Active') ? 'Inactive' : 'Active';
    const formData = new FormData();
    formData.append('partnerId', partnerId);
    formData.append('status', nextStatus);

    fetch('../api/live-partners.php?action=toggle-status', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to toggle status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

// --- Live Season Videos JS ---
function openLiveVideoModal() {
    document.getElementById('liveVideoForm').reset();
    document.getElementById('liveVideoId').value = '';
    document.getElementById('liveVideoExistingThumb').value = '';
    document.getElementById('liveVideoModalTitle').innerHTML = '<i class="fa-solid fa-film" style="color: #f97316;"></i> Add Completed Season Video';
    document.getElementById('liveVideoThumbPreviewContainer').style.display = 'none';
    document.getElementById('liveVideoModal').classList.add('show');
}

function closeLiveVideoModal() {
    document.getElementById('liveVideoModal').classList.remove('show');
}

function extractYoutubeIdJs(url) {
    if (!url) return '';
    const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i);
    return match ? match[1] : '';
}

function handleLiveVideoUrlInput(url) {
    const ytId = extractYoutubeIdJs(url);
    const prevBox = document.getElementById('liveVideoThumbPreviewContainer');
    const prevImg = document.getElementById('liveVideoThumbPreviewImg');

    if (ytId) {
        prevBox.style.display = 'block';
        prevImg.src = `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`;
    }
}

function previewLiveVideoThumb(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const prevBox = document.getElementById('liveVideoThumbPreviewContainer');
        const prevImg = document.getElementById('liveVideoThumbPreviewImg');
        prevBox.style.display = 'block';
        prevImg.src = URL.createObjectURL(file);
    }
}

function editLiveVideo(v) {
    document.getElementById('liveVideoForm').reset();
    document.getElementById('liveVideoId').value = v.id || '';
    document.getElementById('liveVideoExistingThumb').value = v.thumbnailUrl || '';
    document.getElementById('liveVideoTitle').value = v.title || '';
    document.getElementById('liveVideoUrl').value = v.videoUrl || '';
    document.getElementById('liveVideoSeason').value = v.season || 'Season 1';
    document.getElementById('liveVideoCategory').value = v.videoCategory || 'Full Match';
    document.getElementById('liveVideoDuration').value = v.duration || '';
    document.getElementById('liveVideoMatchDate').value = v.matchDate || '';
    document.getElementById('liveVideoSortOrder').value = v.sortOrder || 1;
    document.getElementById('liveVideoStatus').value = v.status || 'Active';

    document.getElementById('liveVideoModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #f97316;"></i> Edit Completed Season Video';

    const prevBox = document.getElementById('liveVideoThumbPreviewContainer');
    const prevImg = document.getElementById('liveVideoThumbPreviewImg');

    if (v.thumbnailUrl) {
        prevBox.style.display = 'block';
        prevImg.src = (v.thumbnailUrl.startsWith('http') ? v.thumbnailUrl : ('../' + v.thumbnailUrl));
    } else {
        handleLiveVideoUrlInput(v.videoUrl);
    }

    document.getElementById('liveVideoModal').classList.add('show');
}

function saveLiveVideoForm(e) {
    e.preventDefault();
    const form = document.getElementById('liveVideoForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveLiveVideoSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/live-videos.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Season Video';

        if (res.success) {
            alert(res.message || 'Video replay saved successfully!');
            closeLiveVideoModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving video replay.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Season Video';
        alert('Server communication error.');
    });
}

function deleteLiveVideo(videoId) {
    if (!confirm('Are you sure you want to delete this season video?')) return;

    const formData = new FormData();
    formData.append('videoId', videoId);

    fetch('../api/live-videos.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('live-video-row-' + videoId);
            if (row) row.remove();
            const badge = document.getElementById('badgeLiveVideosCount');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
        } else {
            alert('Failed to delete video: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleLiveVideoStatus(videoId, currStatus) {
    const nextStatus = (currStatus === 'Active') ? 'Inactive' : 'Active';
    const formData = new FormData();
    formData.append('videoId', videoId);
    formData.append('status', nextStatus);

    fetch('../api/live-videos.php?action=toggle-status', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to toggle status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminLiveVideos() {
    const seasonVal = (document.getElementById('adminLiveVideoSeasonFilter')?.value || 'all').toLowerCase();
    const catVal = (document.getElementById('adminLiveVideoCatFilter')?.value || 'all').toLowerCase();
    const rows = document.querySelectorAll('.live-video-admin-row');

    rows.forEach(row => {
        const rowSeason = (row.getAttribute('data-season') || '').toLowerCase();
        const rowCat = (row.getAttribute('data-cat') || '').toLowerCase();

        const matchSeason = (seasonVal === 'all' || rowSeason === seasonVal);
        const matchCat = (catVal === 'all' || rowCat === catVal);

        if (matchSeason && matchCat) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// ==========================================
// MARQUEE ANNOUNCEMENT STRIP HANDLERS
// ==========================================
function openAnnouncementModal() {
    document.getElementById('announcementForm').reset();
    document.getElementById('announcementId').value = '';
    document.getElementById('annBadgeText').value = '📢 LATEST UPDATE';
    document.getElementById('annSortOrder').value = '1';
    document.getElementById('annOpenInNewTab').value = '0';
    document.getElementById('annStatus').value = 'Active';
    document.getElementById('announcementModalTitle').innerHTML = '<i class="fa-solid fa-bullhorn" style="color: #ea580c;"></i> Add Marquee Announcement';
    document.getElementById('announcementModal').classList.add('show');
}

function closeAnnouncementModal() {
    document.getElementById('announcementModal').classList.remove('show');
}

function editAnnouncement(ann) {
    document.getElementById('announcementForm').reset();
    document.getElementById('announcementId').value = ann.id || '';
    document.getElementById('annBadgeText').value = ann.badgeText || '📢 LATEST UPDATE';
    document.getElementById('annText').value = ann.text || '';
    document.getElementById('annLinkUrl').value = ann.linkUrl || '';
    document.getElementById('annLinkText').value = ann.linkText || '';
    document.getElementById('annOpenInNewTab').value = (ann.openInNewTab) ? '1' : '0';
    document.getElementById('annSortOrder').value = ann.sortOrder || 1;
    document.getElementById('annStatus').value = ann.status || 'Active';

    document.getElementById('announcementModalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color: #ea580c;"></i> Edit Marquee Announcement';
    document.getElementById('announcementModal').classList.add('show');
}

function saveAnnouncementForm(e) {
    e.preventDefault();
    const form = document.getElementById('announcementForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('saveAnnSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    fetch('../api/announcements.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Announcement';

        if (res.success) {
            alert(res.message || 'Announcement saved successfully!');
            closeAnnouncementModal();
            window.location.reload();
        } else {
            alert(res.message || 'Error saving announcement.');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Announcement';
        alert('Server communication error.');
    });
}

function deleteAnnouncement(annId) {
    if (!confirm('Are you sure you want to delete this announcement?')) return;

    const formData = new FormData();
    formData.append('id', annId);

    fetch('../api/announcements.php?action=delete', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const row = document.getElementById('ann-row-' + annId);
            if (row) row.remove();
            const badge = document.getElementById('sideAnnouncementBadge');
            if (badge) {
                let c = parseInt(badge.textContent || '1');
                if (c > 0) badge.textContent = c - 1;
            }
            window.location.reload();
        } else {
            alert('Failed to delete announcement: ' + (res.message || 'Unknown error'));
        }
    })
    .catch(err => alert('Server communication error.'));
}

function toggleAnnouncementStatus(annId, currStatus) {
    const formData = new FormData();
    formData.append('id', annId);

    fetch('../api/announcements.php?action=toggle_status', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.reload();
        } else {
            alert(res.message || 'Failed to toggle status.');
        }
    })
    .catch(err => alert('Server communication error.'));
}

function filterAdminAnnouncements() {
    const searchVal = (document.getElementById('adminAnnSearchInput')?.value || '').toLowerCase().trim();
    const statusVal = (document.getElementById('adminAnnStatusFilter')?.value || 'all').toLowerCase();
    const rows = document.querySelectorAll('.ann-admin-row');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();

        const matchSearch = (!searchVal || text.includes(searchVal));
        const matchStatus = (statusVal === 'all' || rowStatus === statusVal);

        if (matchSearch && matchStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function saveHomeStatsSettings(e) {
    e.preventDefault();
    const btn = document.getElementById('saveHomeStatsBtn');
    const oldText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    const formData = new FormData(document.getElementById('homeStatsForm'));
    fetch('../api/home-stats.php?action=save', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = oldText;
        if (res.success) {
            alert(res.message || 'Home stats updated successfully!');
        } else {
            alert('Error: ' + (res.message || 'Failed to save stats.'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = oldText;
        alert('Server communication error.');
    });
}

// ==========================================
// UNIVERSAL PAGINATION & SEARCH ENGINE
// ==========================================

// --- 1. PLAYER REGISTRATION SEARCH & LIVE PAGINATION ---
let playerCurrentPage = 1;
let playerPageSize = 10;

function changePlayerPageSize(size) {
    playerPageSize = (size === 'all') ? 999999 : (parseInt(size) || 10);
    playerCurrentPage = 1;
    setupAdminPagination();
}

function handlePlayerSearchChange() {
    playerCurrentPage = 1;
    setupAdminPagination();
}

function clearPlayerSearch() {
    const sInput = document.getElementById('adminPlayerSearchInput');
    const sFilter = document.getElementById('adminPlayerStatusFilter');
    const gFilter = document.getElementById('adminPlayerGenderFilter');
    if (sInput) sInput.value = '';
    if (sFilter) sFilter.value = 'all';
    if (gFilter) gFilter.value = 'all';
    playerCurrentPage = 1;
    setupAdminPagination();
}

function setPlayerPage(page) {
    playerCurrentPage = page;
    setupAdminPagination();
}

function setupAdminPagination() {
    const tbody = document.getElementById('playersTbody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr[id^="row-"]'));
    if (!rows.length) {
        const infoEl = document.getElementById('adminPageInfoText');
        const btnEl = document.getElementById('adminPaginationButtons');
        if (infoEl) infoEl.textContent = 'No player registrations found';
        if (btnEl) btnEl.innerHTML = '';
        return;
    }

    const searchVal = (document.getElementById('adminPlayerSearchInput')?.value || '').toLowerCase().trim();
    const statusVal = (document.getElementById('adminPlayerStatusFilter')?.value || 'all').toLowerCase();
    const genderVal = (document.getElementById('adminPlayerGenderFilter')?.value || 'all').toLowerCase();

    // Filter rows based on search text, payment status, and gender
    const visibleRows = rows.filter(row => {
        const rowText = (row.getAttribute('data-search') || row.textContent).toLowerCase();
        const rowPaid = (row.getAttribute('data-paid') || (rowText.includes('verified') || rowText.includes('utr') ? 'paid' : 'pending')).toLowerCase();
        const rowGender = (row.getAttribute('data-gender') || (rowText.includes('female') ? 'female' : 'male')).toLowerCase();

        const matchSearch = !searchVal || rowText.includes(searchVal);
        const matchStatus = (statusVal === 'all') 
            || (statusVal === 'paid' && rowPaid === 'paid') 
            || (statusVal === 'pending' && rowPaid !== 'paid');
        const matchGender = (genderVal === 'all') || rowGender === genderVal;

        return matchSearch && matchStatus && matchGender;
    });

    const totalVisible = visibleRows.length;
    const totalPages = Math.max(1, Math.ceil(totalVisible / playerPageSize));
    if (playerCurrentPage > totalPages) playerCurrentPage = totalPages;
    if (playerCurrentPage < 1) playerCurrentPage = 1;

    const startIndex = (playerCurrentPage - 1) * playerPageSize;
    const endIndex = Math.min(startIndex + playerPageSize, totalVisible);

    // Hide all rows first
    rows.forEach(r => { r.style.display = 'none'; });

    // Show only the slice of matching rows
    for (let i = startIndex; i < endIndex; i++) {
        if (visibleRows[i]) {
            visibleRows[i].style.display = '';
        }
    }

    // Update Info Text
    const infoEl = document.getElementById('adminPageInfoText');
    if (infoEl) {
        if (totalVisible === 0) {
            infoEl.textContent = 'No matching players found';
        } else {
            infoEl.textContent = `Showing ${startIndex + 1}–${endIndex} of ${totalVisible} player${totalVisible !== 1 ? 's' : ''}`;
        }
    }

    // Build Pagination Buttons
    const btnEl = document.getElementById('adminPaginationButtons');
    if (!btnEl) return;
    btnEl.innerHTML = '';

    if (totalPages <= 1) return;

    // Previous Button
    const prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'btn-act';
    prevBtn.style.cssText = 'padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 6px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; cursor: pointer;';
    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i> Prev';
    prevBtn.disabled = (playerCurrentPage === 1);
    if (playerCurrentPage === 1) prevBtn.style.opacity = '0.5';
    prevBtn.onclick = () => setPlayerPage(playerCurrentPage - 1);
    btnEl.appendChild(prevBtn);

    // Page Numbers (with smart truncation)
    let pagesToShow = [];
    if (totalPages <= 7) {
        for (let p = 1; p <= totalPages; p++) pagesToShow.push(p);
    } else {
        pagesToShow = [1];
        if (playerCurrentPage > 3) pagesToShow.push('...');
        const startP = Math.max(2, playerCurrentPage - 1);
        const endP = Math.min(totalPages - 1, playerCurrentPage + 1);
        for (let p = startP; p <= endP; p++) pagesToShow.push(p);
        if (playerCurrentPage < totalPages - 2) pagesToShow.push('...');
        pagesToShow.push(totalPages);
    }

    pagesToShow.forEach(p => {
        if (p === '...') {
            const dots = document.createElement('span');
            dots.style.cssText = 'padding: 4px 6px; font-size: 13px; color: #94a3b8; font-weight: bold;';
            dots.textContent = '...';
            btnEl.appendChild(dots);
        } else {
            const pBtn = document.createElement('button');
            pBtn.type = 'button';
            pBtn.className = 'btn-act';
            const isActive = (p === playerCurrentPage);
            pBtn.style.cssText = `padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 6px; cursor: pointer; ${
                isActive 
                ? 'background: var(--primary); color: #fff; border: 1px solid var(--primary); box-shadow: 0 2px 4px rgba(0,0,0,0.1);' 
                : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'
            }`;
            pBtn.textContent = p;
            pBtn.onclick = () => setPlayerPage(p);
            btnEl.appendChild(pBtn);
        }
    });

    // Next Button
    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'btn-act';
    nextBtn.style.cssText = 'padding: 6px 12px; font-size: 12px; font-weight: 700; border-radius: 6px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; cursor: pointer;';
    nextBtn.innerHTML = 'Next <i class="fa-solid fa-chevron-right"></i>';
    nextBtn.disabled = (playerCurrentPage === totalPages);
    if (playerCurrentPage === totalPages) nextBtn.style.opacity = '0.5';
    nextBtn.onclick = () => setPlayerPage(playerCurrentPage + 1);
    btnEl.appendChild(nextBtn);
}


// --- 2. UNIVERSAL TABLE PAGINATION ENGINE ---
const tablePaginatorStates = {
    teams: { page: 1, pageSize: 10 },
    standings: { page: 1, pageSize: 10 },
    fixtures: { page: 1, pageSize: 10 },
    mvp: { page: 1, pageSize: 10 },
    winners: { page: 1, pageSize: 10 },
    banners: { page: 1, pageSize: 10 },
    pageBanners: { page: 1, pageSize: 10 },
    gallery: { page: 1, pageSize: 10 },
    news: { page: 1, pageSize: 10 },
    partners: { page: 1, pageSize: 10 },
    committee: { page: 1, pageSize: 10 },
    livePartners: { page: 1, pageSize: 10 },
    liveVideos: { page: 1, pageSize: 10 },
    announcements: { page: 1, pageSize: 10 },
    messages: { page: 1, pageSize: 10 }
};

const tablePaginatorConfigs = {
    teams: {
        tbodyId: 'teamsTbody',
        rowSelector: 'tr[id^="team-row-"]',
        infoId: 'teamsPageInfoText',
        btnId: 'teamsPaginationButtons',
        itemLabel: 'teams',
        filterFn: (row) => {
            const season = (document.getElementById('adminTeamsSeasonFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminTeamSearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchSeason = (season === 'all' || rSeason.includes(season));
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchSearch;
        }
    },
    standings: {
        tbodyId: 'standingsTbody',
        rowSelector: 'tr[id^="standing-row-"]',
        infoId: 'standingsPageInfoText',
        btnId: 'standingsPaginationButtons',
        itemLabel: 'standings',
        filterFn: (row) => {
            const season = (document.getElementById('standingsSeasonFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminStandingsSearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchSeason = (season === 'all' || rSeason.includes(season));
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchSearch;
        }
    },
    fixtures: {
        tbodyId: 'fixturesTbody',
        rowSelector: 'tr[id^="fixture-row-"]',
        infoId: 'fixturesPageInfoText',
        btnId: 'fixturesPaginationButtons',
        itemLabel: 'match fixtures',
        filterFn: (row) => {
            const season = (document.getElementById('adminFixturesSeasonFilter')?.value || 'all').toLowerCase();
            const status = (document.getElementById('adminFixturesStatusFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminFixturesSearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rStatus = (row.getAttribute('data-status') || '').toLowerCase();
            const rSearch = row.textContent.toLowerCase();
            const matchSeason = (season === 'all' || rSeason.includes(season));
            const matchStatus = (status === 'all' || rStatus === status);
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchStatus && matchSearch;
        }
    },
    mvp: {
        tbodyId: 'mvpTbody',
        rowSelector: 'tr[id^="mvp-row-"]',
        infoId: 'mvpPageInfoText',
        btnId: 'mvpPaginationButtons',
        itemLabel: 'MVPs',
        filterFn: (row) => {
            const season = (document.getElementById('adminMvpSeasonFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminMvpSearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchSeason = (season === 'all' || rSeason.includes(season));
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchSearch;
        }
    },
    winners: {
        tbodyId: 'winnersTbody',
        rowSelector: 'tr[id^="winner-row-"]',
        infoId: 'winnersPageInfoText',
        btnId: 'winnersPaginationButtons',
        itemLabel: 'winners',
        filterFn: (row) => {
            const season = (document.getElementById('adminWinnerSeasonFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminWinnerSearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchSeason = (season === 'all' || rSeason.includes(season));
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchSearch;
        }
    },
    banners: {
        tbodyId: 'bannersTbody',
        rowSelector: 'tr[id^="banner-row-"]',
        infoId: 'bannersPageInfoText',
        btnId: 'bannersPaginationButtons',
        itemLabel: 'hero banners',
        filterFn: (row) => {
            const search = (document.getElementById('adminBannerSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    },
    pageBanners: {
        tbodyId: 'pageBannersTbody',
        rowSelector: 'tr[id^="page-banner-row-"]',
        infoId: 'pageBannersPageInfoText',
        btnId: 'pageBannersPaginationButtons',
        itemLabel: 'page banners',
        filterFn: (row) => {
            const search = (document.getElementById('adminPageBannerSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    },
    gallery: {
        tbodyId: 'galleryTbody',
        rowSelector: 'tr[id^="gallery-row-"]',
        infoId: 'galleryPageInfoText',
        btnId: 'galleryPaginationButtons',
        itemLabel: 'gallery photos',
        filterFn: (row) => {
            const season = (document.getElementById('adminGallerySeasonFilter')?.value || 'all').toLowerCase();
            const cat = (document.getElementById('adminGalleryCatFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminGallerySearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rCat = (row.getAttribute('data-cat') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchSeason = (season === 'all' || rSeason === season);
            const matchCat = (cat === 'all' || rCat === cat);
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchCat && matchSearch;
        }
    },
    news: {
        tbodyId: 'newsTbody',
        rowSelector: 'tr[id^="news-row-"]',
        infoId: 'newsPageInfoText',
        btnId: 'newsPaginationButtons',
        itemLabel: 'news updates',
        filterFn: (row) => {
            const search = (document.getElementById('adminNewsSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    },
    partners: {
        tbodyId: 'partnersTbody',
        rowSelector: 'tr[id^="partner-row-"]',
        infoId: 'partnersPageInfoText',
        btnId: 'partnersPaginationButtons',
        itemLabel: 'partners & sponsors',
        filterFn: (row) => {
            const search = (document.getElementById('adminPartnerSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    },
    committee: {
        tbodyId: 'committeeTbody',
        rowSelector: 'tr[id^="committee-row-"]',
        infoId: 'committeePageInfoText',
        btnId: 'committeePaginationButtons',
        itemLabel: 'committee members',
        filterFn: (row) => {
            const search = (document.getElementById('adminCommitteeSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    },
    livePartners: {
        tbodyId: 'livePartnersTbody',
        rowSelector: 'tr[id^="live-partner-row-"]',
        infoId: 'livePartnersPageInfoText',
        btnId: 'livePartnersPaginationButtons',
        itemLabel: 'streaming partners',
        filterFn: (row) => {
            const search = (document.getElementById('adminLivePartnerSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    },
    liveVideos: {
        tbodyId: 'liveVideosTbody',
        rowSelector: 'tr[id^="live-video-row-"]',
        infoId: 'liveVideosPageInfoText',
        btnId: 'liveVideosPaginationButtons',
        itemLabel: 'season videos',
        filterFn: (row) => {
            const season = (document.getElementById('adminLiveVideoSeasonFilter')?.value || 'all').toLowerCase();
            const cat = (document.getElementById('adminLiveVideoCatFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminLiveVideoSearchInput')?.value || '').toLowerCase().trim();
            const rSeason = (row.getAttribute('data-season') || '').toLowerCase();
            const rCat = (row.getAttribute('data-cat') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchSeason = (season === 'all' || rSeason === season);
            const matchCat = (cat === 'all' || rCat === cat);
            const matchSearch = (!search || rSearch.includes(search));
            return matchSeason && matchCat && matchSearch;
        }
    },
    announcements: {
        tbodyId: 'announcementsTbody',
        rowSelector: 'tr[id^="ann-row-"]',
        infoId: 'announcementsPageInfoText',
        btnId: 'announcementsPaginationButtons',
        itemLabel: 'announcements',
        filterFn: (row) => {
            const status = (document.getElementById('adminAnnStatusFilter')?.value || 'all').toLowerCase();
            const search = (document.getElementById('adminAnnSearchInput')?.value || '').toLowerCase().trim();
            const rStatus = (row.getAttribute('data-status') || '').toLowerCase();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            const matchStatus = (status === 'all' || rStatus === status);
            const matchSearch = (!search || rSearch.includes(search));
            return matchStatus && matchSearch;
        }
    },
    messages: {
        tbodyId: 'messagesTbody',
        rowSelector: 'tr[id^="msg-row-"]',
        infoId: 'messagesPageInfoText',
        btnId: 'messagesPaginationButtons',
        itemLabel: 'messages',
        filterFn: (row) => {
            const search = (document.getElementById('adminMessageSearchInput')?.value || '').toLowerCase().trim();
            const rSearch = (row.getAttribute('data-search') || row.textContent).toLowerCase();
            return !search || rSearch.includes(search);
        }
    }
};

function paginateAdminTable(tableKey, targetPage = null) {
    const cfg = tablePaginatorConfigs[tableKey];
    const st = tablePaginatorStates[tableKey];
    if (!cfg || !st) return;

    if (targetPage !== null) st.page = targetPage;

    const tbody = document.getElementById(cfg.tbodyId);
    if (!tbody) return;

    const allRows = Array.from(tbody.querySelectorAll(cfg.rowSelector));
    if (!allRows.length) {
        const infoEl = document.getElementById(cfg.infoId);
        const btnEl = document.getElementById(cfg.btnId);
        if (infoEl) infoEl.textContent = `No ${cfg.itemLabel} found`;
        if (btnEl) btnEl.innerHTML = '';
        return;
    }

    // Filter matching rows
    const visibleRows = cfg.filterFn ? allRows.filter(cfg.filterFn) : allRows;
    const totalVisible = visibleRows.length;
    const totalPages = Math.max(1, Math.ceil(totalVisible / st.pageSize));

    if (st.page > totalPages) st.page = totalPages;
    if (st.page < 1) st.page = 1;

    const startIndex = (st.page - 1) * st.pageSize;
    const endIndex = Math.min(startIndex + st.pageSize, totalVisible);

    // Hide all
    allRows.forEach(r => { r.style.display = 'none'; });

    // Show current page slice
    for (let i = startIndex; i < endIndex; i++) {
        if (visibleRows[i]) visibleRows[i].style.display = '';
    }

    // Update info
    const infoEl = document.getElementById(cfg.infoId);
    if (infoEl) {
        if (totalVisible === 0) {
            infoEl.textContent = `No matching ${cfg.itemLabel} found`;
        } else {
            infoEl.textContent = `Showing ${startIndex + 1}–${endIndex} of ${totalVisible} ${cfg.itemLabel}`;
        }
    }

    // Update buttons
    const btnEl = document.getElementById(cfg.btnId);
    if (!btnEl) return;
    btnEl.innerHTML = '';

    if (totalPages <= 1) return;

    // Prev Button
    const prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'btn-act';
    prevBtn.style.cssText = 'padding: 5px 10px; font-size: 12px; font-weight: 700; border-radius: 6px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; cursor: pointer;';
    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i> Prev';
    prevBtn.disabled = (st.page === 1);
    if (st.page === 1) prevBtn.style.opacity = '0.5';
    prevBtn.onclick = () => paginateAdminTable(tableKey, st.page - 1);
    btnEl.appendChild(prevBtn);

    // Page Buttons
    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7 && Math.abs(p - st.page) > 2 && p !== 1 && p !== totalPages) {
            if (p === 2 || p === totalPages - 1) {
                const dots = document.createElement('span');
                dots.style.cssText = 'padding: 2px 4px; color: #94a3b8; font-size: 12px;';
                dots.textContent = '...';
                btnEl.appendChild(dots);
            }
            continue;
        }

        const pBtn = document.createElement('button');
        pBtn.type = 'button';
        pBtn.className = 'btn-act';
        const isActive = (p === st.page);
        pBtn.style.cssText = `padding: 5px 10px; font-size: 12px; font-weight: 700; border-radius: 6px; cursor: pointer; ${
            isActive 
            ? 'background: var(--primary); color: #fff; border: 1px solid var(--primary);' 
            : 'background: #fff; color: #334155; border: 1px solid #cbd5e1;'
        }`;
        pBtn.textContent = p;
        pBtn.onclick = () => paginateAdminTable(tableKey, p);
        btnEl.appendChild(pBtn);
    }

    // Next Button
    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'btn-act';
    nextBtn.style.cssText = 'padding: 5px 10px; font-size: 12px; font-weight: 700; border-radius: 6px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; cursor: pointer;';
    nextBtn.innerHTML = 'Next <i class="fa-solid fa-chevron-right"></i>';
    nextBtn.disabled = (st.page === totalPages);
    if (st.page === totalPages) nextBtn.style.opacity = '0.5';
    nextBtn.onclick = () => paginateAdminTable(tableKey, st.page + 1);
    btnEl.appendChild(nextBtn);
}

// Global Filter Functions mapped directly to Paginators
function filterAdminTeams() { paginateAdminTable('teams', 1); }
function filterAdminStandings() { paginateAdminTable('standings', 1); }
function filterAdminFixtures() { paginateAdminTable('fixtures', 1); }
function filterAdminMvp() { paginateAdminTable('mvp', 1); }
function filterAdminWinners() { paginateAdminTable('winners', 1); }
function filterAdminBanners() { paginateAdminTable('banners', 1); }
function filterAdminPageBanners() { paginateAdminTable('pageBanners', 1); }
function filterAdminGalleryTable() { paginateAdminTable('gallery', 1); }
function filterAdminNews() { paginateAdminTable('news', 1); }
function filterAdminPartners() { paginateAdminTable('partners', 1); }
function filterAdminCommittee() { paginateAdminTable('committee', 1); }
function filterAdminLivePartners() { paginateAdminTable('livePartners', 1); }
function filterAdminLiveVideos() { paginateAdminTable('liveVideos', 1); }
function filterAdminAnnouncements() { paginateAdminTable('announcements', 1); }
function filterAdminMessages() { paginateAdminTable('messages', 1); }

function runAllAdminPaginations() {
    setupAdminPagination();
    Object.keys(tablePaginatorConfigs).forEach(k => {
        paginateAdminTable(k);
    });
}


// ==========================================
// PERSISTENT TAB INITIALIZATION
// ==========================================
function initAdminActiveTab() {
    let hashTab = (window.location.hash || '').replace('#', '').trim();
    let savedTab = '';
    try {
        savedTab = localStorage.getItem('upphl_admin_active_tab') || '';
    } catch(e) {}

    let targetTab = hashTab || savedTab || 'dashboard';
    const validTabs = ['dashboard', 'players', 'teams', 'standings', 'fixtures', 'mvp', 'winners', 'banners', 'page-banners', 'announcements', 'gallery', 'news', 'partners', 'committee', 'live-hub', 'messages', 'home-stats', 'contact-settings'];

    if (!validTabs.includes(targetTab)) {
        targetTab = 'dashboard';
    }

    switchTab(targetTab);
    runAllAdminPaginations();
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminActiveTab();
    runAllAdminPaginations();
});

window.addEventListener('hashchange', () => {
    let hashTab = (window.location.hash || '').replace('#', '').trim();
    if (hashTab) switchTab(hashTab);
    runAllAdminPaginations();
});

// Run immediately for instant tab restore before repaint
initAdminActiveTab();

// ==========================================
// AUTO-SYNC DASHBOARD (Real-Time Live Updates)
// ==========================================
setInterval(() => {
    const pModal = document.getElementById('playerDetailsModal');
    const bModal = document.getElementById('bannerModal');
    const gModal = document.getElementById('galleryModal');
    const cModal = document.getElementById('contactMessageModal');
    if ((pModal && pModal.classList.contains('show')) || (bModal && bModal.classList.contains('show')) || (gModal && gModal.classList.contains('show')) || (cModal && cModal.classList.contains('show'))) return;

    fetch(window.location.href)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update stats
            ['statTotal', 'statPending', 'statApproved', 'statStandings', 'statFixtures', 'statBanners', 'statGallery', 'statMessages'].forEach(id => {
                const newElem = doc.getElementById(id);
                const currElem = document.getElementById(id);
                if (newElem && currElem) {
                    currElem.textContent = newElem.textContent;
                }
            });

            // Update Players Table
            const newTbody = doc.getElementById('playersTbody');
            const currTbody = document.getElementById('playersTbody');
            if (newTbody && currTbody) {
                currTbody.innerHTML = newTbody.innerHTML;
                setupAdminPagination();
            }
        })
        .catch(err => console.log('Auto-sync quiet error:', err));
}, 5000);

</script>

<?php endif; ?>

</body>
</html>

