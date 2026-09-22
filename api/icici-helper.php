<?php
// api/icici-helper.php
// Helper utility for ICICI Bank Orange PG API Integration per Official Bank Specification (V0.4)

require_once 'config.php';

class ICICIOrangePG {

    /**
     * Generate HMAC-SHA256 Secure Hash over sorted parameters
     *
     * Rules per Bank Document:
     * 1. Sort the request packet in ascending alphabetical order based on keys.
     * 2. Concatenate the values of all sorted parameters (excluding null, empty strings and secureHash itself).
     * 3. Calculate HMAC-SHA256 using the Secret Key.
     * 4. Convert result to lowercase hexadecimal.
     *
     * @param array $params Request or Response parameters
     * @param string $secretKey Secret key from ICICI Bank
     * @return string 64-character lowercase hex hash
     */
    public static function generateSecureHash(array $params, $secretKey) {
        unset($params['secureHash']);

        // Filter out null or empty string values
        $filtered = array_filter($params, function($v) {
            return $v !== null && $v !== '';
        });

        // Sort keys in ascending alphabetical order
        ksort($filtered);

        // Concatenate all values without any delimiter
        $plainHashText = implode('', $filtered);

        return hash_hmac('sha256', $plainHashText, (string)$secretKey);
    }

    /**
     * Verify Secure Hash from ICICI Orange PG callback / response
     *
     * @param array $responseParams Response parameters
     * @param string $receivedHash Hash received from server
     * @param string $secretKey Secret key
     * @return bool
     */
    public static function verifySecureHash(array $responseParams, $receivedHash, $secretKey) {
        if (empty($receivedHash) || empty($secretKey)) {
            return false;
        }
        $computed = self::generateSecureHash($responseParams, $secretKey);
        return hash_equals(strtolower($computed), strtolower((string)$receivedHash));
    }

    /**
     * Call ICICI Orange PG initiateSale API to create payment session (Standard Mode / payType = 0)
     *
     * @param string $referenceNo Unique Transaction / Player ID (e.g. UPPHL-S2-1001)
     * @param float|int $amount Transaction amount in INR
     * @param string $mobile Payer mobile number
     * @param string $email Payer email address
     * @param string $name Payer full name
     * @return array [success => bool, paymentUrl => string, message => string, raw => array]
     */
    public static function initiateSale($referenceNo, $amount, $mobile = '', $email = '', $name = '') {
        $url          = ICICI_INITIATE_URL;
        $aggregatorId = defined('ICICI_AGGREGATOR_ID') ? ICICI_AGGREGATOR_ID : '';
        $merchantId   = ICICI_MERCHANT_ID;
        $secretKey    = ICICI_SECRET_KEY;
        $returnUrl    = ICICI_RETURN_URL;

        // Clean amount format e.g. 1000.00 (numeric 9,2)
        $formattedAmount = number_format((float)$amount, 2, '.', '');

        // Format mobile number (ensure 10-12 digits, with 91 prefix if 10 digits)
        $cleanMobile = preg_replace('/\D/', '', (string)$mobile);
        if (strlen($cleanMobile) === 10) {
            $cleanMobile = '91' . $cleanMobile;
        } elseif (empty($cleanMobile)) {
            $cleanMobile = '919999999999';
        }

        // Clean customer name
        $cleanName = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', (string)$name)) ?: 'Registered Player';

        // Prepare request parameters per ICICI Specification Chapter 3 & Step Wise Document
        $params = [
            'merchantId'       => (string)$merchantId,
            'merchantTxnNo'    => substr(preg_replace('/[^a-zA-Z0-9]/', '', (string)$referenceNo), 0, 20),
            'amount'           => $formattedAmount,
            'currencyCode'     => '356', // 356 = INR
            'payType'          => '0',   // 0 = Standard Hosted Redirection
            'customerEmailID'  => $email ?: 'support@upphl.in',
            'transactionType'  => 'SALE',
            'returnURL'        => $returnUrl,
            'txnDate'          => date('YmdHis'), // Format: YYYYMMDDHHMISS
            'customerMobileNo' => $cleanMobile,
            'customerName'     => $cleanName,
            'addlParam1'       => 'UPPHL Season 2',
            'addlParam2'       => 'Player Registration'
        ];

        if (!empty($aggregatorId)) {
            $params['aggregatorID'] = (string)$aggregatorId;
        }

        // Generate HMAC-SHA256 Secure Hash
        $params['secureHash'] = self::generateSecureHash($params, $secretKey);

        // Send server-to-server JSON POST request
        $postData = json_encode($params);
        $response = false;
        $curlError = '';

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            if ($response === false) {
                $curlError = curl_error($ch);
            }
            curl_close($ch);
        } else {
            $opts = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content' => $postData,
                    'timeout' => 30,
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false
                ]
            ];
            $context = stream_context_create($opts);
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                $err = error_get_last();
                $curlError = $err['message'] ?? 'Network communication error';
            }
        }

        if ($response === false) {
            return [
                'success'    => false,
                'message'    => 'Connection to ICICI Payment Gateway failed: ' . $curlError,
                'paymentUrl' => null,
                'raw'        => null
            ];
        }

        $json = json_decode($response, true);
        if (!$json) {
            return [
                'success'    => false,
                'message'    => 'Invalid response received from ICICI Gateway server.',
                'paymentUrl' => null,
                'raw'        => $response
            ];
        }

        $responseCode = $json['responseCode'] ?? '';
        $responseDesc = $json['responseDescription'] ?? $json['respDescription'] ?? 'Unknown status';
        $redirectUri  = $json['redirectURI'] ?? $json['paymentUrl'] ?? null;
        $tranCtx      = $json['tranCtx'] ?? null;

        // Step 6 Logic from Bank Doc: redirectURI?tranCtx=value
        if (!empty($redirectUri)) {
            $finalPaymentUrl = $redirectUri;
            if (!empty($tranCtx) && strpos($finalPaymentUrl, 'tranCtx') === false) {
                $separator = (strpos($finalPaymentUrl, '?') !== false) ? '&' : '?';
                $finalPaymentUrl .= $separator . 'tranCtx=' . urlencode($tranCtx);
            }

            return [
                'success'    => true,
                'paymentUrl' => $finalPaymentUrl,
                'message'    => 'Session created successfully.',
                'raw'        => $json
            ];
        }

        // Handle error response from ICICI Gateway
        return [
            'success'    => false,
            'message'    => "ICICI Bank Gateway [{$responseCode}]: {$responseDesc}",
            'paymentUrl' => null,
            'raw'        => $json
        ];
    }

    /**
     * Check Transaction Status using Command API (Chapter 12)
     *
     * @param string $merchantTxnNo Merchant Transaction Reference
     * @param string $originalTxnNo Original Transaction ID (if available)
     * @return array Status result
     */
    public static function checkStatus($merchantTxnNo, $originalTxnNo = '') {
        $url          = ICICI_STATUS_URL;
        $merchantId   = ICICI_MERCHANT_ID;
        $aggregatorId = defined('ICICI_AGGREGATOR_ID') ? ICICI_AGGREGATOR_ID : '';
        $secretKey    = ICICI_SECRET_KEY;

        $params = [
            'merchantId'      => (string)$merchantId,
            'merchantTxnNo'   => (string)$merchantTxnNo,
            'originalTxnNo'   => (string)($originalTxnNo ?: $merchantTxnNo),
            'transactionType' => 'STATUS'
        ];

        if (!empty($aggregatorId)) {
            $params['aggregatorID'] = (string)$aggregatorId;
        }

        $params['secureHash'] = self::generateSecureHash($params, $secretKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        return $json ?: ['responseCode' => 'ERR', 'responseDescription' => 'No response'];
    }

    /**
     * Backward-compatible decrypt helper
     */
    public static function decrypt($cipherText, $key) {
        if (empty($cipherText) || empty($key)) return '';
        $key16 = substr(str_pad($key, 16, "\0"), 0, 16);
        $rawCipher = @hex2bin($cipherText) ?: base64_decode($cipherText, true);
        if (!$rawCipher) return '';
        $dec = @openssl_decrypt($rawCipher, 'AES-128-CBC', $key16, OPENSSL_RAW_DATA, $key16);
        if ($dec === false) $dec = @openssl_decrypt($rawCipher, 'AES-128-ECB', $key16, OPENSSL_RAW_DATA);
        return $dec !== false ? trim($dec) : '';
    }
}

// Backward-compatibility class alias
class ICICIEazypay extends ICICIOrangePG {}
