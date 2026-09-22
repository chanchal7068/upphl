<?php
// api/icici-response.php
// Handles ICICI Bank Orange PG Payment Gateway Callback & Verification per Official Bank Specification (V0.4)

require_once 'config.php';
require_once 'icici-helper.php';

// Accept Form POST / GET / JSON callbacks from ICICI Orange PG
$params = $_POST;
if (empty($params)) {
    $params = $_GET;
}

if (empty($params)) {
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $json = json_decode($rawInput, true);
        if (is_array($json)) {
            $params = $json;
        }
    }
}

// Extract ICICI Response fields per Specification Chapter 7 & 12
$responseCode    = $params['responseCode'] ?? $params['Response_Code'] ?? $params['response_code'] ?? $params['status'] ?? '';
$responseDesc    = $params['respDescription'] ?? $params['responseDescription'] ?? $params['txnRespDescription'] ?? '';
$txnStatus       = $params['txnStatus'] ?? '';
$txnResponseCode = $params['txnResponseCode'] ?? '';

$uniqueRefNo     = $params['txnID'] ?? $params['txnAuthID'] ?? $params['paymentID'] ?? $params['txnNo'] ?? $params['pgTxnNo'] ?? $params['Unique_Ref_Number'] ?? $params['transaction_id'] ?? '';
$referenceNo     = $params['merchantTxnNo'] ?? $params['ReferenceNo'] ?? $params['reference_no'] ?? $params['merchant_txn_id'] ?? '';
$amount          = $params['amount'] ?? $params['Amount'] ?? $params['Transaction_Amount'] ?? ICICI_PAY_AMOUNT;
$paymentMode     = $params['paymentMode'] ?? $params['Payment_Mode'] ?? 'ICICI Orange PG';
$receivedHash    = $params['secureHash'] ?? '';

// Fallback if encrypted RS parameter was returned
if (!empty($params['RS'])) {
    $decryptedRS = ICICIEazypay::decrypt($params['RS'], ICICI_SECRET_KEY);
    if (!empty($decryptedRS)) {
        parse_str(str_replace('|', '&', $decryptedRS), $parsedRS);
        if (!empty($parsedRS['Response_Code'])) $responseCode = $parsedRS['Response_Code'];
        if (!empty($parsedRS['Unique_Ref_Number'])) $uniqueRefNo = $parsedRS['Unique_Ref_Number'];
        if (!empty($parsedRS['ReferenceNo'])) $referenceNo = $parsedRS['ReferenceNo'];
    }
}

// Success condition per Bank Document (000 / 0000 / SUC)
$isSuccess = in_array(strtoupper((string)$responseCode), ['000', '0000', 'SUCCESS', 'E000', '00'])
          || strtoupper((string)$txnStatus) === 'SUC'
          || in_array(strtoupper((string)$txnResponseCode), ['000', '0000', '0']);

// Storage Files
$jsonDir = '../uploads/';
$pendingJsonFile = $jsonDir . 'pending_registrations.json';
$mainJsonFile    = $jsonDir . 'players.json';
$updatedPlayer   = null;
$cleanRef        = preg_replace('/[^a-zA-Z0-9]/', '', (string)$referenceNo);

// 1. Look in pending registrations first
$pendingList = file_exists($pendingJsonFile) ? (json_decode(file_get_contents($pendingJsonFile), true) ?? []) : [];
$foundInPending = false;

foreach ($pendingList as $key => $p) {
    $cleanPlayerId = preg_replace('/[^a-zA-Z0-9]/', '', (string)($p['playerId'] ?? ''));
    $pTxnNo = preg_replace('/[^a-zA-Z0-9]/', '', (string)($p['merchantTxnNo'] ?? ''));
    $isMatch = ($pTxnNo && $pTxnNo === $cleanRef)
            || ($cleanPlayerId === $cleanRef) 
            || (!empty($cleanPlayerId) && strpos($cleanRef, $cleanPlayerId) === 0) 
            || (($p['playerId'] ?? '') === $referenceNo);

    if ($isMatch) {
        $foundInPending = true;
        if ($isSuccess) {
            $p['status']        = 'Approved'; // Bank verified & paid
            $p['paymentStatus'] = 'Paid';
            $p['paymentId']     = $uniqueRefNo ?: ('ICICI_' . time());
            $p['paymentMethod'] = $paymentMode;
            $p['amountPaid']    = '₹' . $amount;
            $p['paidAt']        = date('Y-m-d H:i:s');
            $updatedPlayer      = $p;

            // Add to verified main players.json
            $mainList = file_exists($mainJsonFile) ? (json_decode(file_get_contents($mainJsonFile), true) ?? []) : [];
            // Remove any old entry for this player
            $mainList = array_filter($mainList, function($mp) use ($cleanRef, $referenceNo, $cleanPlayerId, $pTxnNo) {
                $cleanMId = preg_replace('/[^a-zA-Z0-9]/', '', (string)($mp['playerId'] ?? ''));
                $cleanMTxn = preg_replace('/[^a-zA-Z0-9]/', '', (string)($mp['merchantTxnNo'] ?? ''));
                return $cleanMId !== $cleanRef && $cleanMId !== $cleanPlayerId && $cleanMTxn !== $cleanRef && ($mp['playerId'] ?? '') !== $referenceNo;
            });
            array_unshift($mainList, $p);
            file_put_contents($mainJsonFile, json_encode(array_values($mainList), JSON_PRETTY_PRINT), LOCK_EX);

            // Remove from pending list
            unset($pendingList[$key]);
            file_put_contents($pendingJsonFile, json_encode(array_values($pendingList), JSON_PRETTY_PRINT), LOCK_EX);
        }
        break;
    }
}

// 2. Fallback check in main players.json if already there
if (!$foundInPending && file_exists($mainJsonFile)) {
    $mainList = json_decode(file_get_contents($mainJsonFile), true) ?? [];
    foreach ($mainList as &$p) {
        $cleanPlayerId = preg_replace('/[^a-zA-Z0-9]/', '', (string)($p['playerId'] ?? ''));
        $pTxnNo = preg_replace('/[^a-zA-Z0-9]/', '', (string)($p['merchantTxnNo'] ?? ''));
        $isMatch = ($pTxnNo && $pTxnNo === $cleanRef)
                || ($cleanPlayerId === $cleanRef) 
                || (!empty($cleanPlayerId) && strpos($cleanRef, $cleanPlayerId) === 0) 
                || (($p['playerId'] ?? '') === $referenceNo);
        if ($isMatch) {
            if ($isSuccess) {
                $p['status']        = 'Approved';
                $p['paymentStatus'] = 'Paid';
                $p['paymentId']     = $uniqueRefNo ?: ('ICICI_' . time());
                $p['paymentMethod'] = $paymentMode;
                $p['amountPaid']    = '₹' . $amount;
                $p['paidAt']        = date('Y-m-d H:i:s');
                $updatedPlayer      = $p;
            }
            break;
        }
    }
    unset($p);
    file_put_contents($mainJsonFile, json_encode(array_values($mainList), JSON_PRETTY_PRINT), LOCK_EX);
}

// Update MySQL DB if configured
if ($conn !== null && $isSuccess && !empty($referenceNo)) {
    $stmt = $conn->prepare("UPDATE registered_players SET status = 'Approved', payment_id = ? WHERE REPLACE(player_id, '-', '') = ? OR player_id = ?");
    if ($stmt) {
        $stmt->bind_param("sss", $uniqueRefNo, $cleanRef, $referenceNo);
        $stmt->execute();
    }
}

// Redirect player back to registration page with formatted Player ID (with hyphen) and credentials
$displayPlayerId = $updatedPlayer['playerId'] ?? $referenceNo;
$playerPassword  = $updatedPlayer['password'] ?? '';
$playerName      = $updatedPlayer['fullName'] ?? '';
$playerEmail     = $updatedPlayer['email'] ?? '';

if ($isSuccess) {
    $redirectUrl = "../player-registration.php?payment=success"
        . "&playerId=" . urlencode($displayPlayerId)
        . "&pw=" . urlencode($playerPassword)
        . "&name=" . urlencode($playerName)
        . "&email=" . urlencode($playerEmail)
        . "&txn=" . urlencode($uniqueRefNo)
        . "&amount=" . urlencode($amount);
} else {
    $redirectUrl = "../player-registration.php?payment=failed&err=" . urlencode($responseDesc ?: ($responseCode ?: 'DECLINED'));
}

header("Location: " . $redirectUrl);
exit;
