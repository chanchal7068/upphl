<?php
header('Content-Type: application/json');
require_once 'config.php';

$messages = [];

// Try DB first
if ($conn !== null) {
    $result = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'id'          => $row['id'],
                'fullName'    => $row['full_name'],
                'email'       => $row['email'],
                'phone'       => $row['phone'],
                'subject'     => $row['subject'],
                'message'     => $row['message'],
                'submittedAt' => $row['submitted_at']
            ];
        }
        echo json_encode(['success' => true, 'messages' => $messages]);
        exit;
    }
}

// Fallback to JSON File
$jsonFile = '../uploads/messages.json';
if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $messages = json_decode($content, true) ?? [];
}

echo json_encode(['success' => true, 'messages' => $messages]);
