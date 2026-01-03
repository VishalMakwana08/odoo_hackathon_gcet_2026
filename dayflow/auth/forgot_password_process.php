<?php
require_once '../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16));
    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $token, $email);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $link = "http://localhost:81/odoo_hackathon_gcet_2026/dayflow/auth/reset_password.php?token=$token";
        echo "Reset link: <a href='$link'>$link</a>";
    } else { echo "Email not found."; }
}
?>