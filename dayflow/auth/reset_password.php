<?php $token = $_GET['token']; ?>
<form action="update_password.php" method="POST">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="password" name="new_pass" placeholder="New Password" required>
    <button type="submit">Update Password</button>
</form>