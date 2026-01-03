<!DOCTYPE html>
<html>
<head>
  <title>Dayflow | Login</title>
  <style>
    /* Global Reset & Background */
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #F8FAFC;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    /* Main Container */
    .box {
      width: 380px;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      border: 1px solid #E5E7EB;
    }

    /* Brand Header */
    .brand-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .brand-header h1 {
      margin: 0;
      color: #2563EB;
      font-size: 28px;
      letter-spacing: -1px;
    }
    .brand-header p {
      color: #64748B;
      font-size: 14px;
      margin-top: 5px;
    }

    /* Input Styling */
    label {
      font-size: 13px;
      font-weight: 600;
      color: #475569;
      display: block;
      margin-top: 15px;
    }

    input, select {
      width: 100%;
      padding: 12px;
      margin: 6px 0;
      border: 1.5px solid #E5E7EB;
      border-radius: 8px;
      box-sizing: border-box; /* Ensures padding doesn't affect width */
      font-size: 14px;
      transition: all 0.3s ease;
      outline: none;
    }

    input:focus, select:focus {
      border-color: #2563EB;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Button Styling */
    button.submit {
      width: 100%;
      padding: 12px;
      background: #2563EB;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      margin-top: 20px;
      transition: background 0.2s;
    }

    button.submit:hover {
      background: #1D4ED8;
    }

    /* Toggle Forms */
    .form {
      display: none;
    }
    .form.active {
      display: block;
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Footer Text */
    .footer-text {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #64748B;
    }
    .footer-text a {
      color: #2563EB;
      text-decoration: none;
      font-weight: 600;
    }

    /* Validation Message */
    #emailMsg {
      font-size: 11px;
      display: block;
      margin-top: -4px;
      height: 15px;
    }
    .valid { color: #10B981; }
    .invalid { color: #EF4444; }

  </style>
</head>
<body>

<div class="box">
  <div class="brand-header">
    <h1>Dayflow</h1>
    <p id="formTitle">Create your account</p>
  </div>

  <div id="signupForm" class="form active">
    <form action="auth/signup_process.php" method="post">
      <label>Employee Details</label>
      <input type="text" name="employee_id" placeholder="Employee ID" required>

      <label>Email Address</label>
      <input type="email" name="email" id="email" placeholder="name@company.com"
             onkeyup="validateEmail()" required>
      <small id="emailMsg"></small>

      <label>Password</label>
      <input type="password" name="password" placeholder="••••••••" required>

      <label>Account Role</label>
      <select name="role" required>
        <option value="">Select Role</option>
        <option value="employee">Employee</option>
        <option value="admin">HR / Admin</option>
      </select>

      <button type="submit" class="submit">Create Account</button>
    </form>

    <p class="footer-text">
      Already registered? 
      <a href="javascript:void(0)" onclick="showSignin()">Sign In</a>
    </p>
  </div>

  <div id="signinForm" class="form">
    <form action="auth/signin_process.php" method="post">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="name@company.com" required>
      
      <label>Password</label>
      <input type="password" name="password" placeholder="••••••••" required>
      
      <button type="submit" class="submit">Sign In</button>
    </form>

    <p class="footer-text">
      Don’t have an account? 
      <a href="javascript:void(0)" onclick="showSignup()">Sign Up</a>
    </p>
  </div>
</div>

<script>
function showSignup(){
  document.getElementById("signupForm").classList.add("active");
  document.getElementById("signinForm").classList.remove("active");
  document.getElementById("formTitle").innerText = "Create your account";
}

function showSignin(){
  document.getElementById("signinForm").classList.add("active");
  document.getElementById("signupForm").classList.remove("active");
  document.getElementById("formTitle").innerText = "Sign in to your workspace";
}

function validateEmail(){
  let email = document.getElementById("email").value;
  let msg = document.getElementById("emailMsg");
  let pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  
  if(email === "") {
    msg.innerHTML = "";
  } else if(pattern.test(email)) {
    msg.innerHTML = "Email looks correct";
    msg.className = "valid";
  } else {
    msg.innerHTML = "Please enter a valid email";
    msg.className = "invalid";
  }
}
</script>

</body>
</html>