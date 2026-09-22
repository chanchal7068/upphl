<?php
// api/icici-initiate.php
// Initializes Player Registration with Payment Pending state and creates ICICI Eazypay Payment URL

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once 'config.php';
require_once 'icici-helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid Request Method']);
    exit;
}

try {

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
$totalExp     = trim($_POST['exp'] ?? $_POST['totalExp'] ?? '');
$level        = trim($_POST['level'] ?? '');
$club         = trim($_POST['club'] ?? '');
$certsNote    = trim($_POST['tournaments'] ?? $_POST['certsNote'] ?? '');
$achievements = trim($_POST['achieve'] ?? $_POST['achievements'] ?? '');
$dept         = trim($_POST['dept'] ?? '');
$deptName     = trim($_POST['deptName'] ?? '');
$season1      = trim($_POST['season1'] ?? 'No');
$fitness      = trim($_POST['fitness'] ?? 'Fully Fit');
$injHistory   = trim($_POST['injury'] ?? $_POST['injHistory'] ?? 'No');
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
$status       = 'Payment Pending';

if (empty($fullName) || empty($mobile)) {
    echo json_encode(['success' => false, 'message' => 'Full Name and Mobile Number are required.']);
    exit;
}

// Generate / retrieve Globally Unique Server-side Player ID
$jsonFile = '../uploads/players.json';
$existingPlayers = file_exists($jsonFile) ? (json_decode(file_get_contents($jsonFile), true) ?? []) : [];
$pendingFile = '../uploads/pending_registrations.json';
$existingPending = file_exists($pendingFile) ? (json_decode(file_get_contents($pendingFile), true) ?? []) : [];

$allRecords = array_merge($existingPlayers, $existingPending);
$maxSeq = 0;
foreach ($allRecords as $rec) {
    if (!empty($rec['playerId']) && preg_match('/UPPHL-S2-(\d+)/i', $rec['playerId'], $matches)) {
        $seqVal = intval($matches[1]);
        if ($seqVal > $maxSeq) $maxSeq = $seqVal;
    }
}
$nextSeq = $maxSeq + 1;
$calculatedPlayerId = "UPPHL-S2-" . str_pad($nextSeq, 3, "0", STR_PAD_LEFT);

// Check if incoming playerId is already taken in database
$incomingId = trim($_POST['playerId'] ?? '');
$isTaken = false;
if (!empty($incomingId)) {
    foreach ($existingPlayers as $ep) {
        if (($ep['playerId'] ?? '') === $incomingId) {
            $isTaken = true;
            break;
        }
    }
}

$playerId = (!$isTaken && !empty($incomingId)) ? $incomingId : $calculatedPlayerId;

$password = trim($_POST['password'] ?? '');
if (empty($password)) {
    $password = "UP" . rand(100000, 999999);
}

// Photo Upload Handling
$photoUrl = "assets/images/default-player.png";
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/photos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
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

// Achievement Certificates Upload Handling (Supports Single & Multiple files, PDF/JPG/PNG/DOC)
$certUrls = [];
$certsFileObj = $_FILES['certs'] ?? $_FILES['certs[]'] ?? null;

if (!empty($certsFileObj) && !empty($certsFileObj['name'])) {
    $certDir = '../uploads/documents/';
    if (!is_dir($certDir)) mkdir($certDir, 0755, true);

    // Case 1: Multiple files uploaded
    if (is_array($certsFileObj['name'])) {
        foreach ($certsFileObj['name'] as $idx => $cName) {
            if (!empty($cName) && isset($certsFileObj['error'][$idx]) && $certsFileObj['error'][$idx] === UPLOAD_ERR_OK) {
                $ext = pathinfo($cName, PATHINFO_EXTENSION);
                $cleanExt = strtolower($ext ?: 'pdf');
                $name = "CERT_" . preg_replace('/[^a-zA-Z0-9]/', '', $playerId) . "_" . time() . "_" . $idx . "." . $cleanExt;
                if (move_uploaded_file($certsFileObj['tmp_name'][$idx], $certDir . $name)) {
                    $certUrls[] = 'uploads/documents/' . $name;
                }
            }
        }
    } 
    // Case 2: Single file uploaded
    elseif (!empty($certsFileObj['name']) && $certsFileObj['error'] === UPLOAD_ERR_OK) {
        $cName = $certsFileObj['name'];
        $ext = pathinfo($cName, PATHINFO_EXTENSION);
        $cleanExt = strtolower($ext ?: 'pdf');
        $name = "CERT_" . preg_replace('/[^a-zA-Z0-9]/', '', $playerId) . "_" . time() . "_0." . $cleanExt;
        if (move_uploaded_file($certsFileObj['tmp_name'], $certDir . $name)) {
            $certUrls[] = 'uploads/documents/' . $name;
        }
    }
}


// Save in temporary pending registrations JSON file (NOT in main players.json until payment is verified)
$jsonDir = '../uploads/';
if (!is_dir($jsonDir)) mkdir($jsonDir, 0755, true);
$pendingJsonFile = $jsonDir . 'pending_registrations.json';
$pendingList  = [];
if (file_exists($pendingJsonFile)) {
    $pendingList = json_decode(file_get_contents($pendingJsonFile), true) ?? [];
}

// Remove any existing pending record for this ID to prevent duplicate
$pendingList = array_filter($pendingList, function($p) use ($playerId) {
    return ($p['playerId'] ?? '') !== $playerId;
});

// Generate 100% Unique Bank Transaction Reference (Max 20 chars, alphanumeric) to prevent ICICI P1006 error
$merchantTxnNo = "TXN" . date('ymdHis') . rand(10, 99);

$newPlayer = [
    'playerId'        => $playerId,
    'merchantTxnNo'   => $merchantTxnNo,
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
    'paymentId'       => '',
    'paymentMethod'   => 'ICICI Orange PG',
    'amountPaid'      => '₹' . ICICI_PAY_AMOUNT,
    'status'          => 'Payment Pending',
    'submittedAt'     => date('Y-m-d H:i:s')
];

array_unshift($pendingList, $newPlayer);
file_put_contents($pendingJsonFile, json_encode(array_values($pendingList), JSON_PRETTY_PRINT), LOCK_EX);

// Call ICICI Orange PG initiateSale API with Guaranteed Unique merchantTxnNo
$initiateResult = ICICIOrangePG::initiateSale($merchantTxnNo, ICICI_PAY_AMOUNT, $mobile, $email, $fullName);

if ($initiateResult['success'] && !empty($initiateResult['paymentUrl'])) {
    echo json_encode([
        'success'       => true,
        'playerId'      => $playerId,
        'merchantTxnNo' => $merchantTxnNo,
        'password'      => $password,
        'amount'        => ICICI_PAY_AMOUNT,
        'paymentUrl'    => $initiateResult['paymentUrl'],
        'message'       => 'Redirecting to ICICI Bank Payment Gateway...'
    ]);
} else {
    echo json_encode([
        'success'    => false,
        'playerId'   => $playerId,
        'message'    => $initiateResult['message'] ?? 'Failed to initialize payment with ICICI Gateway.'
    ]);
}
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}

