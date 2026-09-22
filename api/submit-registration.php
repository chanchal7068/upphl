<?php
// api/submit-registration.php
// Handles Player Registration Submissions, Status Initialization (Pending), Photo Uploads & DB/JSON Storage

require_once 'config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid Request Method']);
    exit;
}

// Data Sanitization
$fullName     = trim($_POST['fullName'] ?? '');
$fatherName   = trim($_POST['fatherName'] ?? '');
$motherName   = trim($_POST['motherName'] ?? '');
$dob          = trim($_POST['dob'] ?? '');
$age          = trim($_POST['age'] ?? '');
$gender       = trim($_POST['gender'] ?? '');
$bloodGroup   = trim($_POST['bloodGroup'] ?? '');
$height       = trim($_POST['height'] ?? '');
$weight       = trim($_POST['weight'] ?? '');
$hand         = trim($_POST['hand'] ?? '');
$primaryPos   = trim($_POST['pos1'] ?? '');
$secondaryPos = trim($_POST['pos2'] ?? '');
$totalExp     = trim($_POST['totalExp'] ?? '');
$level        = trim($_POST['level'] ?? '');
$club         = trim($_POST['club'] ?? '');
$certsNote    = trim($_POST['certsNote'] ?? '');
$achievements = trim($_POST['achievements'] ?? '');
$season1      = trim($_POST['season1'] ?? 'No');
$fitness      = trim($_POST['fitness'] ?? 'Fully Fit');
$injHistory   = trim($_POST['injHistory'] ?? 'No');
$injDetails   = trim($_POST['injuryDetail'] ?? '');
$address      = trim($_POST['address'] ?? '');
$district     = trim($_POST['district'] ?? '');
$state        = trim($_POST['state'] ?? 'Uttar Pradesh');
$aadhaar      = trim($_POST['aadhaar'] ?? '');
$mobile       = trim($_POST['mobile'] ?? '');
$whatsapp     = trim($_POST['whatsapp'] ?? '');
$email        = trim($_POST['email'] ?? '');
$emgName      = trim($_POST['emgName'] ?? '');
$emgRel       = trim($_POST['emgRel'] ?? '');
$emgNo        = trim($_POST['emgNo'] ?? '');
$signName     = trim($_POST['signName'] ?? '');
$signDate     = trim($_POST['signDate'] ?? '');
$signPlace    = trim($_POST['signPlace'] ?? '');
$status       = 'Approved'; // Instant automatic approval upon payment

if (empty($fullName) || empty($mobile)) {
    echo json_encode(['success' => false, 'message' => 'Full Name and Mobile Number are required fields.']);
    exit;
}

// Accept front-end generated Player ID and Password if provided
$playerId = trim($_POST['playerId'] ?? '');
if (empty($playerId)) {
    $uniqueNum = rand(1000, 9999);
    $playerId  = "UPPHL-S2-" . $uniqueNum;
}

$password = trim($_POST['password'] ?? '');
if (empty($password)) {
    $password = "UP" . rand(100000, 999999);
}

// Photo Upload Handling
$photoUrl = "assets/images/default-player.png";
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/photos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileExt   = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $fileName  = $playerId . "_" . time() . "." . strtolower($fileExt);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
        $photoUrl = 'uploads/photos/' . $fileName;
    }
}

// Aadhaar Front Upload Handling
$aadhaarFrontUrl = "";
if (isset($_FILES['aadhaarFront']) && $_FILES['aadhaarFront']['error'] === UPLOAD_ERR_OK) {
    $docDir = '../uploads/documents/';
    if (!is_dir($docDir)) mkdir($docDir, 0755, true);
    $ext = pathinfo($_FILES['aadhaarFront']['name'], PATHINFO_EXTENSION);
    $name = "AF_" . $playerId . "_" . time() . "." . strtolower($ext);
    if (move_uploaded_file($_FILES['aadhaarFront']['tmp_name'], $docDir . $name)) {
        $aadhaarFrontUrl = 'uploads/documents/' . $name;
    }
}

// Aadhaar Back Upload Handling
$aadhaarBackUrl = "";
if (isset($_FILES['aadhaarBack']) && $_FILES['aadhaarBack']['error'] === UPLOAD_ERR_OK) {
    $docDir = '../uploads/documents/';
    if (!is_dir($docDir)) mkdir($docDir, 0755, true);
    $ext = pathinfo($_FILES['aadhaarBack']['name'], PATHINFO_EXTENSION);
    $name = "AB_" . $playerId . "_" . time() . "." . strtolower($ext);
    if (move_uploaded_file($_FILES['aadhaarBack']['tmp_name'], $docDir . $name)) {
        $aadhaarBackUrl = 'uploads/documents/' . $name;
    }
}

// Achievement Certificates Upload Handling
$certUrls = [];
if (isset($_FILES['certs'])) {
    $certDir = '../uploads/documents/';
    if (!is_dir($certDir)) mkdir($certDir, 0755, true);
    if (is_array($_FILES['certs']['name'])) {
        foreach ($_FILES['certs']['name'] as $idx => $cName) {
            if ($_FILES['certs']['error'][$idx] === UPLOAD_ERR_OK) {
                $ext = pathinfo($cName, PATHINFO_EXTENSION);
                $name = "CERT_" . $playerId . "_" . time() . "_" . $idx . "." . strtolower($ext);
                if (move_uploaded_file($_FILES['certs']['tmp_name'][$idx], $certDir . $name)) {
                    $certUrls[] = 'uploads/documents/' . $name;
                }
            }
        }
    }
}

// Document URL for database storage
$docUrl     = $aadhaarFrontUrl ?: ($aadhaarBackUrl ?: (!empty($certUrls) ? $certUrls[0] : ''));

// Generate Password
$password   = trim($_POST['password'] ?? '') ?: ("UP" . rand(100000, 999999));

// Save Option 1: MySQL Database Storage
if ($conn !== null) {
    $stmt = $conn->prepare("INSERT INTO registered_players (player_id, password, full_name, dob, age, gender, height, weight, playing_hand, primary_pos, district, state, mobile, email, photo_url, doc_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssssssssssssss", $playerId, $password, $fullName, $dob, $age, $gender, $height, $weight, $hand, $primaryPos, $district, $state, $mobile, $email, $photoUrl, $docUrl, $status);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Player registered successfully!',
                'data'    => [
                    'playerId'   => $playerId,
                    'password'   => $password,
                    'fullName'   => $fullName,
                    'photoUrl'   => $photoUrl,
                    'district'   => $district,
                    'primaryPos' => $primaryPos,
                    'status'     => $status
                ]
            ]);
            exit;
        }
    }
}

// Save Option 2: JSON Storage (Easy Admin Review & Fallback)
$jsonDir = '../uploads/';
if (!is_dir($jsonDir)) {
    mkdir($jsonDir, 0755, true);
}

$jsonFile = $jsonDir . 'players.json';
$players  = [];

if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $players = json_decode($content, true) ?? [];
}

$newPlayer = [
    'playerId'        => $playerId,
    'password'        => $password,
    'fullName'        => $fullName,
    'fatherName'      => $fatherName,
    'motherName'      => $motherName,
    'dob'             => $dob,
    'age'             => $age,
    'gender'          => $gender,
    'bloodGroup'      => $bloodGroup,
    'height'          => $height,
    'weight'          => $weight,
    'hand'            => $hand,
    'primaryPos'      => $primaryPos,
    'secondaryPos'    => $secondaryPos,
    'totalExp'        => $totalExp,
    'level'           => $level,
    'club'            => $club,
    'certsNote'       => $certsNote,
    'achievements'    => $achievements,
    'season1'         => $season1,
    'fitness'         => $fitness,
    'injHistory'      => $injHistory,
    'injDetails'      => $injDetails,
    'address'         => $address,
    'district'        => $district,
    'state'           => $state,
    'aadhaar'         => $aadhaar,
    'mobile'          => $mobile,
    'whatsapp'        => $whatsapp,
    'email'           => $email,
    'emgName'         => $emgName,
    'emgRel'          => $emgRel,
    'emgNo'           => $emgNo,
    'signName'        => $signName,
    'signDate'        => $signDate,
    'signPlace'       => $signPlace,
    'photoUrl'        => $photoUrl,
    'aadhaarFrontUrl' => $aadhaarFrontUrl,
    'aadhaarBackUrl'  => $aadhaarBackUrl,
    'certUrls'        => $certUrls,
    'paymentId'       => trim($_POST['paymentId'] ?? '') ?: ('TXN_' . time()),
    'paymentMethod'   => trim($_POST['paymentMethod'] ?? 'ICICI Orange PG'),
    'amountPaid'      => trim($_POST['amountPaid'] ?? '₹1000'),
    'paymentStatus'   => 'Paid',
    'status'          => 'Approved',
    'submittedAt'     => date('Y-m-d H:i:s')
];

array_unshift($players, $newPlayer);
file_put_contents($jsonFile, json_encode($players, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'message' => 'Player registered successfully!',
    'data'    => [
        'playerId'   => $playerId,
        'fullName'   => $fullName,
        'photoUrl'   => $photoUrl,
        'district'   => $district,
        'primaryPos' => $primaryPos,
        'status'     => $status
    ]
]);
