<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Access Denied");

if (isset($_GET['module'])) {
    $m = $_GET['module'];

    // Dashboard Stats
    if ($m == 'dashboard_counts') {
        $emp = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='employee'")->fetch_assoc()['c'];
        $att = $conn->query("SELECT COUNT(*) as c FROM attendance WHERE DATE(check_in) = CURDATE()")->fetch_assoc()['c'];
        $lea = $conn->query("SELECT COUNT(*) as c FROM leaves WHERE status='Pending'")->fetch_assoc()['c'];
        echo json_encode(['employees' => $emp, 'attendance' => $att, 'leaves' => $lea]);
        exit;
    }

    // Modal ke liye purana data fetch karna
    if ($m == 'get_payroll_details') {
        $uid = $_GET['user_id'];
        $stmt = $conn->prepare("SELECT * FROM payroll WHERE user_id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        echo json_encode($res ? $res : ['basic_salary'=>0, 'hra'=>0, 'deductions'=>0]);
        exit;
    }

    // Employee Directory
    if ($m == 'employees') {
        $res = $conn->query("SELECT employee_id, email, is_verified FROM users WHERE role='employee'");
        echo "<table><thead><tr><th>ID</th><th>Email</th><th>Status</th></tr></thead><tbody>";
        while($r = $res->fetch_assoc()) {
            $st = $r['is_verified'] ? 'Verified' : 'Pending';
            echo "<tr><td>{$r['employee_id']}</td><td>{$r['email']}</td><td><span class='badge $st'>$st</span></td></tr>";
        }
        echo "</tbody></table>";
    }

    // Attendance Logs
    if ($m == 'attendance') {
        $sql = "SELECT a.*, u.employee_id FROM attendance a JOIN users u ON a.user_id = u.id ORDER BY a.check_in DESC";
        $res = $conn->query($sql);
        echo "<table><thead><tr><th>Emp ID</th><th>Check-In</th><th>Check-Out</th><th>Status</th></tr></thead><tbody>";
        while($r = $res->fetch_assoc()) {
            $out = $r['check_out'] ? date('d M, H:i', strtotime($r['check_out'])) : '--:--';
            echo "<tr><td>{$r['employee_id']}</td><td>".date('d M, H:i', strtotime($r['check_in']))."</td><td>$out</td><td><span class='badge {$r['status']}'>{$r['status']}</span></td></tr>";
        }
        echo "</tbody></table>";
    }

    // Leave Approvals
    if ($m == 'leaves') {
        $res = $conn->query("SELECT l.*, u.employee_id FROM leaves l JOIN users u ON l.user_id = u.id ORDER BY l.id DESC");
        echo "<table><thead><tr><th>Emp ID</th><th>Type</th><th>Reason</th><th>Status</th><th>Action</th></tr></thead><tbody>";
        while($r = $res->fetch_assoc()) {
            echo "<tr><td>{$r['employee_id']}</td><td>{$r['leave_type']}</td><td><i>{$r['remarks']}</i></td><td><span class='badge {$r['status']}'>{$r['status']}</span></td><td>";
            if($r['status']=='Pending') {
                echo "<button class='btn-action btn-approve' onclick='manageLeave({$r['id']}, \"Approved\")'>Approve</button>";
                echo "<button class='btn-action btn-reject' onclick='manageLeave({$r['id']}, \"Rejected\")'>Reject</button>";
            } else { echo "--"; }
            echo "</td></tr>";
        }
        echo "</tbody></table>";
    }

    // Payroll Management
    if ($m == 'payroll') {
        $res = $conn->query("SELECT u.id, u.employee_id, p.net_salary FROM users u LEFT JOIN payroll p ON u.id = p.user_id WHERE u.role='employee'");
        echo "<table><thead><tr><th>Emp ID</th><th>Net Salary</th><th>Action</th></tr></thead><tbody>";
        while($r = $res->fetch_assoc()) {
            $sal = $r['net_salary'] ? "₹".$r['net_salary'] : "Not Set";
            echo "<tr><td>{$r['employee_id']}</td><td>$sal</td><td><button class='btn-action btn-approve' onclick='openPayrollModal({$r['id']})'>Set Salary</button></td></tr>";
        }
        echo "</tbody></table>";
    }
    exit;
}

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'set_salary') {
        $uid = $_POST['user_id']; $b = $_POST['basic']; $h = $_POST['hra']; $d = $_POST['deductions']; $n = ($b+$h)-$d;
        $stmt = $conn->prepare("INSERT INTO payroll (user_id, basic_salary, hra, deductions, net_salary) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE basic_salary=?, hra=?, deductions=?, net_salary=?");
        $stmt->bind_param("idddddddd", $uid, $b, $h, $d, $n, $b, $h, $d, $n);
        echo $stmt->execute() ? "Payroll Updated Successfully!" : "Error";
    }
    if ($_POST['action'] == 'manage_leave') {
        $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['status'], $_POST['leave_id']);
        echo $stmt->execute() ? "Leave Status Updated!" : "Error";
    }
    exit;
}
?>