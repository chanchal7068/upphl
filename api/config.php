<?php
// api/config.php
// Configuration & Database connection with automatic JSON/CSV fallback

if (!headers_sent() && !isset($noJsonHeader)) {
    header('Content-Type: application/json');
}

$dbHost = "localhost";
$dbUser = "u5873_upphladm";          // Update with server DB Username
$dbPass = "Upphl@2026#AdMin";              // Update with server DB Password
$dbName = "u12347_upphldb";      // Update with server DB Name

$conn = null;

mysqli_report(MYSQLI_REPORT_OFF);
try {
    $conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        $conn = null;
    }
} catch (Throwable $e) {
    $conn = null;
}

// -------------------------------------------------------------
// ICICI Bank Orange PG Payment Gateway Configuration
// -------------------------------------------------------------
define('ICICI_AGGREGATOR_ID', '100000000505927');
define('ICICI_MERCHANT_ID', '100000000505928');
define('ICICI_SECRET_KEY', '2c6ad2bf-9162-4dd5-b16d-7781add2b74a');       // Secret key generated from merchant dashboard
define('ICICI_PAY_AMOUNT', 1000);                           // Player Registration Fee in INR

// Environment: 'TEST' or 'LIVE'
define('ICICI_ENV', 'LIVE'); 

// ICICI Orange PG API Endpoints
define('ICICI_INITIATE_URL_TEST', 'https://uat.jiopay.co.in/pg/api/v2/initiateSale');
define('ICICI_INITIATE_URL_LIVE', 'https://pgpay.icicibank.com/pg/api/v2/initiateSale');
define('ICICI_INITIATE_URL', (ICICI_ENV === 'LIVE') ? ICICI_INITIATE_URL_LIVE : ICICI_INITIATE_URL_TEST);

// Status check API Endpoint
define('ICICI_STATUS_URL', 'https://pgpay.icicibank.com/pg/api/command');

// Return / Callback URL after payment completion
define('ICICI_RETURN_URL', 'https://upphl.com/api/icici-response.php');
