<?php
session_start();
require_once '../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        if ($user['is_verified'] == 0) die("Verify your email first.");
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../dashboard.php"); // [cite: 37]
    } else {
        echo "<script>alert('Invalid Credentials'); window.history.back();</script>"; // [cite: 37]
    }
}
?>