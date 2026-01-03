<?php
require_once '../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $new_pass, $token);
 if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "<script>alert('Password updated! Please login.'); window.location.href='../index.php?action=signin';</script>";
}
}
?>