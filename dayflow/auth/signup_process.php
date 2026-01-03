<?php
require_once '../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = trim($_POST['employee_id']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $role = $_POST['role'];

    // Duplicate Check
    $check = $conn->prepare("SELECT id FROM users WHERE employee_id = ? OR email = ?");
    $check->bind_param("ss", $emp_id, $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        die("<script>alert('ID or Email already exists!'); window.history.back();</script>");
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $v_code = bin2hex(random_bytes(16));

    $sql = "INSERT INTO users (employee_id, email, password, role, verification_code) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $emp_id, $email, $hashed, $role, $v_code);

    if ($stmt->execute()) {
        $link = "http://localhost:81/odoo_hackathon_gcet_2026/dayflow/auth/verify.php?code=$v_code";
        echo "Account created! Verify here: <a href='$link'>$link</a>";
    }
}
?>