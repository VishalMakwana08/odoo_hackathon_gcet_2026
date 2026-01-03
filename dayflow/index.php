<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dayflow | Authentication</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #F8FAFC; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { width: 380px; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #E5E7EB; }
        .brand { text-align: center; margin-bottom: 25px; }
        .brand h1 { color: #2563EB; margin: 0; font-size: 28px; }
        label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-top: 15px; }
        input, select { width: 100%; padding: 12px; margin: 6px 0; border: 1.5px solid #E5E7EB; border-radius: 8px; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        button.submit { width: 100%; padding: 12px; background: #2563EB; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; margin-top: 20px; }
        .form { display: none; }
        .active { display: block; }
        /* Real-time validation colors */
        .invalid-input { border-color: #EF4444 !important; }
        .valid-input { border-color: #10B981 !important; }
        .error-msg { color: #EF4444; font-size: 11px; margin-top: -5px; display: block; height: 15px; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; color: #64748B; }
        .footer-link a { color: #2563EB; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="box">
    <div class="brand">
        <h1>Dayflow</h1>
        <p id="titleText">Create your account</p>
    </div>

    <div id="signupDiv" class="form active">
        <form action="auth/signup_process.php" method="POST" onsubmit="return validateFinal()">
            <label>Employee ID</label>
            <input type="text" name="employee_id" required>
            
            <label>Email Address</label>
            <input type="email" name="email" id="regEmail" onkeyup="checkEmailRealtime(this)" placeholder="name@company.com" required>
            <span id="emailError" class="error-msg"></span>

            <label>Password (Min 8 chars)</label>
            <input type="password" name="password" id="regPass" required>
            
            <label>Role</label>
            <select name="role" required>
                <option value="employee">Employee</option>
                <option value="admin">HR / Admin</option>
            </select>
            <button type="submit" class="submit">Create Account</button>
        </form>
        <div class="footer-link">Already have an account? <a href="#" onclick="toggleForm('signin')">Sign In</a></div>
    </div>

    <div id="signinDiv" class="form">
        <form action="auth/signin_process.php" method="POST">
            <label>Email Address</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <div style="text-align: right;"><a href="#" onclick="toggleForm('forgot')" style="font-size:12px; color:#2563EB;">Forgot Password?</a></div>
            <button type="submit" class="submit">Sign In</button>
        </form>
        <div class="footer-link">New to Dayflow? <a href="#" onclick="toggleForm('signup')">Create Account</a></div>
    </div>

    <div id="forgotDiv" class="form">
        <form action="auth/forgot_password_process.php" method="POST">
            <label>Registered Email</label>
            <input type="email" name="email" required>
            <button type="submit" class="submit">Send Reset Link</button>
        </form>
        <div class="footer-link"><a href="#" onclick="toggleForm('signin')">Back to Login</a></div>
    </div>
</div>

<script>
    // Real-time Email Validation using Regex
    function checkEmailRealtime(input) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const errorSpan = document.getElementById('emailError');
        if (input.value === "") {
            input.classList.remove('invalid-input', 'valid-input');
            errorSpan.innerText = "";
        } else if (regex.test(input.value)) {
            input.classList.add('valid-input');
            input.classList.remove('invalid-input');
            errorSpan.innerText = "";
        } else {
            input.classList.add('invalid-input');
            input.classList.remove('valid-input');
            errorSpan.innerText = "Please enter a valid email address";
        }
    }

    function toggleForm(type) {
        document.getElementById('signupDiv').style.display = (type === 'signup') ? 'block' : 'none';
        document.getElementById('signinDiv').style.display = (type === 'signin') ? 'block' : 'none';
        document.getElementById('forgotDiv').style.display = (type === 'forgot') ? 'block' : 'none';
        
        let titles = {signup: "Create your account", signin: "Sign in to Dayflow", forgot: "Reset Password"};
        document.getElementById('titleText').innerText = titles[type];
    }

    // Check URL parameters on load to show correct form
    window.onload = function() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('action') === 'signin') {
            toggleForm('signin');
        }
    };

    function validateFinal() {
        const pass = document.getElementById('regPass').value;
        if (pass.length < 8) {
            alert("Password must be at least 8 characters");
            return false;
        }
        return true;
    }
</script>
</body>
</html>