<?php
// api/controllers/ContactController.php
// Controller handling Contact Settings and Contact Form Message Submissions

class ContactController {
    private $conn;
    private $settingsFile;
    private $messagesFile;

    private $defaultSettings = [
        'phone'     => '+91 7084900009',
        'email'     => 'uphandballleague@gmail.com',
        'address'   => 'D5 SHIV NAGAR COLONY GRAM CHANDPUR VARANASI UTTAR PRADESH INDIA 221006',
        'mapEmbed'  => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8577.971649410943!2d82.97167766165312!3d25.317881457351188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x398e2df211e6302d%3A0x71a3709ed12c0baa!2sChandpur%2C%20Chandua%20Chhittupur%2C%20Shivpurwa%2C%20Varanasi%2C%20Uttar%20Pradesh%20221002!5e0!3m2!1sen!2sin!4v1787729110796!5m2!1sen!2sin',
        'facebook'  => 'https://facebook.com/upprohandballleague',
        'instagram' => 'https://instagram.com/upprohandballleague',
        'youtube'   => 'https://www.youtube.com/@sportscastindia'
    ];

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->settingsFile = __DIR__ . '/../../uploads/contact_settings.json';
        $this->messagesFile = __DIR__ . '/../../uploads/messages.json';
    }

    // GET /api/contact.php?action=settings
    public function getSettings() {
        $settings = $this->defaultSettings;
        if (file_exists($this->settingsFile)) {
            $custom = json_decode(file_get_contents($this->settingsFile), true);
            if (is_array($custom)) {
                $settings = array_merge($this->defaultSettings, $custom);
            }
        }
        echo json_encode(['success' => true, 'settings' => $settings]);
        exit;
    }

    // POST /api/contact.php?action=save-settings
    public function saveSettings() {
        $phone     = trim($_POST['phone'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $address   = trim($_POST['address'] ?? '');
        $mapEmbed  = trim($_POST['mapEmbed'] ?? '');
        $facebook  = trim($_POST['facebook'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $youtube   = trim($_POST['youtube'] ?? '');

        // Extract clean URL from pasted iframe code if user pasted raw iframe HTML
        if (preg_match('/src=["\']([^"\']+)["\']/', $mapEmbed, $match)) {
            $mapEmbed = $match[1];
        }

        $settings = [
            'phone'     => $phone ?: $this->defaultSettings['phone'],
            'email'     => $email ?: $this->defaultSettings['email'],
            'address'   => $address ?: $this->defaultSettings['address'],
            'mapEmbed'  => $mapEmbed ?: $this->defaultSettings['mapEmbed'],
            'facebook'  => $facebook ?: $this->defaultSettings['facebook'],
            'instagram' => $instagram ?: $this->defaultSettings['instagram'],
            'youtube'   => $youtube ?: $this->defaultSettings['youtube'],
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        echo json_encode(['success' => true, 'message' => 'Contact Us settings updated successfully!', 'settings' => $settings]);
        exit;
    }

    // POST /api/contact.php?action=submit-message
    public function submitMessage() {
        $fullName = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $subject  = trim($_POST['subject'] ?? '');
        $message  = trim($_POST['message'] ?? '');

        if (empty($fullName) || empty($email) || empty($subject) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Email, Subject, Message).']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
            exit;
        }

        $submittedAt = date('Y-m-d H:i:s');
        $msgId = 'msg_' . time() . '_' . rand(100, 999);

        $msgRecord = [
            'id'          => $msgId,
            'fullName'    => $fullName,
            'email'       => $email,
            'phone'       => $phone,
            'subject'     => $subject,
            'message'     => $message,
            'submittedAt' => $submittedAt
        ];

        // Save to JSON
        $messages = [];
        if (file_exists($this->messagesFile)) {
            $messages = json_decode(file_get_contents($this->messagesFile), true) ?? [];
        }
        array_unshift($messages, $msgRecord);
        file_put_contents($this->messagesFile, json_encode($messages, JSON_PRETTY_PRINT));

        // Save to Database
        if ($this->conn !== null) {
            $this->ensureMessagesTable();
            $stmt = $this->conn->prepare("INSERT INTO contact_messages (full_name, email, phone, subject, message, submitted_at) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param('ssssss', $fullName, $email, $phone, $subject, $message, $submittedAt);
                $stmt->execute();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Thank you for reaching out! Your message has been sent successfully. We will get back to you shortly.'
        ]);
        exit;
    }

    // POST /api/contact.php?action=delete-message
    public function deleteMessage() {
        $id = trim($_POST['id'] ?? '');
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Message ID required.']);
            exit;
        }

        // Remove from JSON
        if (file_exists($this->messagesFile)) {
            $messages = json_decode(file_get_contents($this->messagesFile), true) ?? [];
            $messages = array_values(array_filter($messages, fn($m) => ($m['id'] ?? '') != $id));
            file_put_contents($this->messagesFile, json_encode($messages, JSON_PRETTY_PRINT));
        }

        // Remove from DB
        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM contact_messages WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('s', $id);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true, 'message' => 'Message deleted successfully.']);
        exit;
    }

    private function ensureMessagesTable() {
        $this->conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(30),
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}
