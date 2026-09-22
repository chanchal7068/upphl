<?php
// api/controllers/PlayerController.php
// Controller handling player registration, status updates, public approved list, and CSV export

class PlayerController {
    private $conn;
    private $jsonFile;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
        $this->jsonFile = __DIR__ . '/../../uploads/players.json';
    }

    // GET /api/players.php?action=approved
    public function getApproved() {
        $approved = [];

        // 1. Check Database
        if ($this->conn !== null) {
            $res = @$this->conn->query("SELECT player_id as playerId, password, full_name as fullName, dob, age, gender, primary_pos as primaryPos, secondary_pos as secondaryPos, playing_hand as hand, height, weight, total_exp as totalExp, level, club, address, district, state, mobile, whatsapp, email, photo_url as photoUrl, aadhaar_front_url as aadhaarFrontUrl, aadhaar_back_url as aadhaarBackUrl, payment_id as paymentId, payment_method as paymentMethod, amount_paid as amountPaid, status, created_at as submittedAt FROM registered_players WHERE (payment_id IS NOT NULL AND payment_id != '') ORDER BY id DESC");
            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if (!empty($row['paymentId']) && trim($row['paymentId']) !== '') {
                        $approved[] = $row;
                    }
                }
                if (!empty($approved)) {
                    echo json_encode(['success' => true, 'players' => $approved]);
                    exit;
                }
            }
        }

        // 2. Check JSON file
        if (file_exists($this->jsonFile)) {
            $all = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            foreach ($all as $p) {
                $status = strtolower($p['status'] ?? 'approved');
                if ($status !== 'rejected') {
                    $approved[] = $p;
                }
            }
        }

        echo json_encode(['success' => true, 'players' => $approved]);
        exit;
    }

    // POST /api/players.php?action=delete
    public function deletePlayer() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['upphl_admin_logged'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            exit;
        }

        $playerId = trim($_POST['playerId'] ?? '');
        if (empty($playerId)) {
            echo json_encode(['success' => false, 'message' => 'Player ID is required.']);
            exit;
        }

        // 1. Delete from Database
        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("DELETE FROM registered_players WHERE player_id = ?");
            if ($stmt) {
                $stmt->bind_param("s", $playerId);
                $stmt->execute();
            }
        }

        // 2. Delete from JSON
        if (file_exists($this->jsonFile)) {
            $players = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            $players = array_filter($players, function($p) use ($playerId) {
                return ($p['playerId'] ?? '') !== $playerId;
            });
            file_put_contents($this->jsonFile, json_encode(array_values($players), JSON_PRETTY_PRINT));
        }

        echo json_encode(['success' => true, 'message' => 'Player record deleted successfully.']);
        exit;
    }

    // POST /api/players.php?action=update-status
    public function updateStatus() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['upphl_admin_logged'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            exit;
        }

        $playerId  = trim($_POST['playerId'] ?? '');
        $newStatus = trim($_POST['status'] ?? '');

        if (empty($playerId) || !in_array($newStatus, ['Approved', 'Rejected', 'Pending'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
            exit;
        }

        // 1. Update in Database
        if ($this->conn !== null) {
            $stmt = $this->conn->prepare("UPDATE registered_players SET status = ? WHERE player_id = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $newStatus, $playerId);
                $stmt->execute();
            }
        }

        // 2. Update in JSON
        if (file_exists($this->jsonFile)) {
            $players = json_decode(file_get_contents($this->jsonFile), true) ?? [];
            foreach ($players as &$p) {
                if ($p['playerId'] === $playerId) {
                    $p['status'] = $newStatus;
                    break;
                }
            }
            file_put_contents($this->jsonFile, json_encode($players, JSON_PRETTY_PRINT));
        }

        echo json_encode(['success' => true, 'message' => 'Player status updated to ' . $newStatus]);
        exit;
    }

    // GET /api/players.php?action=export-csv
    public function exportCsv() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['upphl_admin_logged'])) {
            die('Access Denied');
        }

        $players = [];
        if (file_exists($this->jsonFile)) {
            $players = json_decode(file_get_contents($this->jsonFile), true) ?? [];
        }

        $filename = "UPPHL_Registered_Players_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        fputcsv($output, [
            'Player ID', 'Password', 'Full Name', 'Father Name', 'Mother Name',
            'DOB', 'Age', 'Gender', 'Blood Group', 'Mobile', 'WhatsApp', 'Email',
            'Aadhaar No', 'Height (cm)', 'Weight (kg)', 'Playing Hand',
            'Primary Position', 'Secondary Position', 'Total Experience',
            'Highest Playing Level', 'Club / Academy', 'Achievements / Tournaments',
            'Address', 'District', 'State', 'Emergency Contact',
            'Fitness Status', 'Payment ID', 'Payment Method', 'Amount Paid', 'Status', 'Registration Date'
        ]);

        foreach ($players as $p) {
            fputcsv($output, [
                $p['playerId'] ?? '',
                $p['password'] ?? '',
                $p['fullName'] ?? '',
                $p['fatherName'] ?? '',
                $p['motherName'] ?? '',
                $p['dob'] ?? '',
                $p['age'] ?? '',
                $p['gender'] ?? '',
                $p['bloodGroup'] ?? '',
                $p['mobile'] ?? '',
                $p['whatsapp'] ?? '',
                $p['email'] ?? '',
                $p['aadhaar'] ?? '',
                $p['height'] ?? '',
                $p['weight'] ?? '',
                $p['hand'] ?? '',
                $p['primaryPos'] ?? '',
                $p['secondaryPos'] ?? '',
                $p['totalExp'] ?? '',
                $p['level'] ?? '',
                $p['club'] ?? '',
                $p['achievements'] ?? '',
                $p['address'] ?? '',
                $p['district'] ?? '',
                $p['state'] ?? '',
                ($p['emgName'] ?? '') . ' (' . ($p['emgRel'] ?? '') . ' - ' . ($p['emgNo'] ?? '') . ')',
                $p['fitness'] ?? '',
                $p['paymentId'] ?? '',
                $p['paymentMethod'] ?? '',
                $p['amountPaid'] ?? '',
                $p['status'] ?? 'Pending',
                $p['submittedAt'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }
}
