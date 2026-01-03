<!DOCTYPE html>
<html>
<head>
  <title>Dayflow | Login</title>
  <style>
    body{
      font-family: Arial;
      background:#F8FAFC;
    }
    .box{
      width:350px;
      margin:80px auto;
      background:white;
      padding:20px;
      border-radius:6px;
      box-shadow:0 0 10px #ccc;
    }
    .tabs{
      display:flex;
      margin-bottom:20px;
    }
    .tabs button{
      flex:1;
      padding:10px;
      border:none;
      cursor:pointer;
      background:#E5E7EB;
    }
    .tabs button.active{
      background:#2563EB;
      color:white;
    }
    input, select{
      width:100%;
      padding:8px;
      margin:8px 0;
    }
    button.submit{
      width:100%;
      padding:10px;
      background:#2563EB;
      color:white;
      border:none;
      cursor:pointer;
    }
    .form{
      display:none;
    }
    .form.active{
      display:block;
    }
    small{color:red;}
  </style>
</head>
<body>

<div class="box">
  <div class="tabs">
    <button id="signupTab" class="active" onclick="showSignup()">Sign Up</button>
    <button id="signinTab" onclick="showSignin()">Sign In</button>
  </div>

  <!-- SIGN UP FORM -->
  <div id="signupForm" class="form active">
    <form action="auth/signup_process.php" method="post">
      <input type="text" name="employee_id" placeholder="Employee ID" required>

      <input type="email" name="email" id="email" placeholder="Email"
             onkeyup="validateEmail()" required>
      <small id="emailMsg"></small>

      <input type="password" name="password" placeholder="Password" required>

      <select name="role" required>
        <option value="">Select Role</option>
        <option value="employee">Employee</option>
        <option value="admin">HR / Admin</option>
      </select>

      <button class="submit">Create Account</button>
    </form>

    <p style="text-align:center;">
      Already registered?
      <a href="#" onclick="showSignin()">Sign In</a>
    </p>
  </div>

  <!-- SIGN IN FORM -->
  <div id="signinForm" class="form">
    <form action="auth/signin_process.php" method="post">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button class="submit">Sign In</button>
    </form>

    <p style="text-align:center;">
      Don’t have an account?
      <a href="#" onclick="showSignup()">Sign Up</a>
    </p>
  </div>
</div>

<script>
function showSignup(){
  document.getElementById("signupForm").classList.add("active");
  document.getElementById("signinForm").classList.remove("active");
  document.getElementById("signupTab").classList.add("active");
  document.getElementById("signinTab").classList.remove("active");
}

function showSignin(){
  document.getElementById("signinForm").classList.add("active");
  document.getElementById("signupForm").classList.remove("active");
  document.getElementById("signinTab").classList.add("active");
  document.getElementById("signupTab").classList.remove("active");
}

function validateEmail(){
  let email = document.getElementById("email").value;
  let msg = document.getElementById("emailMsg");
  let pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  msg.innerHTML = pattern.test(email) ? "Valid email" : "Invalid email";
}
</script>

</body>
</html>
