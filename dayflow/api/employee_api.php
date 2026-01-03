
<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) die("Unauthorized");
$uid = $_SESSION['user_id'];

// --- GET REQUESTS ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    
    // 1. Profile Data Fetch (For "My Profile" Tab)
    if ($_GET['action'] == 'get_profile') {
        $stmt = $conn->prepare("SELECT employee_id, email, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // JSON format mein data bhejna zaroori hai
        header('Content-Type: application/json');
        echo json_encode($user);
        exit;
    }

    // 2. Attendance History (For "Attendance" Tab)
    if ($_GET['action'] == 'get_attendance_history') {
        $stmt = $conn->prepare("SELECT check_in, check_out, status FROM attendance WHERE user_id = ? ORDER BY check_in DESC");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            echo "<table><tr><th>Date</th><th>Check-In</th><th>Check-Out</th><th>Status</th></tr>";
            while($row = $res->fetch_assoc()) {
                $date = date('d M Y', strtotime($row['check_in']));
                $in = date('H:i', strtotime($row['check_in']));
                $out = $row['check_out'] ? date('H:i', strtotime($row['check_out'])) : '--:--';
                echo "<tr>
                        <td>$date</td>
                        <td>$in</td>
                        <td>$out</td>
                        <td><span class='badge' style='background:#D1FAE5; color:#065F46;'>{$row['status']}</span></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='padding:20px; text-align:center; color:#64748B;'>No attendance records found.</p>";
        }
        exit;
    }

    // 3. Current Attendance Status (For Main Dashboard Card)
    if ($_GET['action'] == 'get_att') {
        $today = date('Y-m-d');
        $stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND DATE(check_in) = ?");
        $stmt->bind_param("is", $uid, $today);
        $stmt->execute();
        $att = $stmt->get_result()->fetch_assoc();

        if (!$att) {
            echo '<p>You are not checked in today.</p>';
            echo '<button class="btn-att in" onclick="markAttendance(\'check_in\')">Check In Now</button>';
        } elseif ($att['check_out'] == NULL) {
            echo '<p style="color:#10B981; font-weight:bold;">Working Since: '.date('H:i', strtotime($att['check_in'])).'</p>';
            echo '<button class="btn-att out" onclick="markAttendance(\'check_out\')">Check Out</button>';
        } else {
            echo '<p style="color:#64748B;">Day Ended at '.date('H:i', strtotime($att['check_out'])).'</p>';
        }
        exit;
    }

    // 4. Leave History (For Main Dashboard Card)
    if ($_GET['action'] == 'get_leaves') {
        $stmt = $conn->prepare("SELECT * FROM leaves WHERE user_id = ? ORDER BY id DESC LIMIT 5");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        echo "<table><tr><th>Leave Dates</th><th>Type</th><th>Status</th></tr>";
        while($row = $res->fetch_assoc()) {
            echo "<tr><td>".date('d M', strtotime($row['start_date']))." - ".date('d M', strtotime($row['end_date']))."</td><td>{$row['leave_type']}</td><td><span class='badge {$row['status']}'>{$row['status']}</span></td></tr>";
        }
        echo "</table>";
        exit;
    }
}

// --- POST ACTIONS (Check-in, Check-out, Apply Leave) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] == 'check_in') {
        $stmt = $conn->prepare("INSERT INTO attendance (user_id, status) VALUES (?, 'Present')");
        $stmt->bind_param("i", $uid);
        echo $stmt->execute() ? "Check-in successful!" : "Error";
    }

    if ($_POST['action'] == 'check_out') {
        $stmt = $conn->prepare("UPDATE attendance SET check_out = CURRENT_TIMESTAMP WHERE user_id = ? AND check_out IS NULL");
        $stmt->bind_param("i", $uid);
        echo $stmt->execute() ? "Check-out successful!" : "Error";
    }

    if ($_POST['action'] == 'apply_leave') {
        $stmt = $conn->prepare("INSERT INTO leaves (user_id, leave_type, start_date, end_date, remarks, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("issss", $uid, $_POST['leave_type'], $_POST['start_date'], $_POST['end_date'], $_POST['remarks']);
        echo $stmt->execute() ? "Leave application submitted!" : "Error: " . $conn->error;
    }
}
// --- GET REQUESTS ---
if ($_GET['action'] == 'get_payroll') {
    $stmt = $conn->prepare("SELECT * FROM payroll WHERE user_id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode($data ? $data : ["message" => "No payroll record found"]);
    exit;
}

// --- POST REQUESTS ---
if ($_POST['action'] == 'edit_profile') {
    $phone = $_POST['phone']; // Ensure these columns exist in 'users' table
    $address = $_POST['address'];
    
    // Admin can edit all, employee limited [cite: 65]
    $stmt = $conn->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssi", $phone, $address, $uid);
    
    if($stmt->execute()) echo "Profile updated successfully!";
    else echo "Error updating profile.";
    exit;
}
?>