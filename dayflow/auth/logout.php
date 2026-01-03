<?php
session_start(); // Session start karna zaroori hai taaki use khatam kiya ja sake

// Saare session variables ko khali kar dein
$_SESSION = array();

// Agar session cookie exist karti hai toh use bhi delete karein
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Poora session destroy karein
session_destroy();

// User ko wapas login page par bhej dein aur message dikhayein
header("Location: ../index.php?action=signin&message=logged_out");
exit();
?>