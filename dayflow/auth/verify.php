<?php
require_once '../db.php';
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verification_code = ?");
    $stmt->bind_param("s", $code);
  if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "<h2>Verified!</h2><p>Redirecting to Login...</p>";
    header("refresh:2;url=../index.php?action=signin");
}
}
?>