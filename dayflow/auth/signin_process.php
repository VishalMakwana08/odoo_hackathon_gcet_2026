<?php
session_start();
require_once '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // 1. Credentials Check [cite: 36, 37]
    if ($user && password_verify($pass, $user['password'])) {
        
        // 2. Email Verification Check [cite: 34]
        if ($user['is_verified'] == 0) {
            echo "<script>alert('Please verify your email first!'); window.location.href='../index.php?action=signin';</script>";
            exit();
        }

        // 3. Session data set karein [cite: 9]
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['emp_id'] = $user['employee_id']; // For profile display [cite: 28]

        // 4. Role-Based Redirection [cite: 9, 37]
        if ($user['role'] === 'admin') {
            header("Location: ../dashboard.php"); // Admin/HR Panel [cite: 46]
        } else {
            header("Location: ../employee_dashboard.php"); // Employee Panel [cite: 39]
        }
        exit();

    } else {
        // 5. Error handling for incorrect credentials 
        echo "<script>alert('Invalid Email or Password'); window.location.href='../index.php?action=signin';</script>";
    }
}
?>