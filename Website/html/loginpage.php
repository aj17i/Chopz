<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="..\css\login.css" />
  <title>Chopz | Login</title>
</head>

<body>
  <div class="wrapper">
    <nav class="nav">
      <div class="nav-logo">
        <img src="..\css\images\logo.png" alt="" />
      </div>
      <div class="nav-menu" id="navMenu">
        <ul>
          <li><a href="landing.html" class="link active">Back</a></li>
        </ul>
      </div>
      <div class="nav-button">
        <button class="btn white-btn" id="loginBtn" onclick="login()">
          Sign In
        </button>
        <button class="btn" id="registerBtn" onclick="register()">
          Sign Up
        </button>
      </div>
      <div class="nav-menu-btn">
        <i class="bx bx-menu" onclick="myMenuFunction()"></i>
      </div>
    </nav>





    <!----------------------------- Form box ----------------------------------->





    <div class="form-box">




      <!------------------- login form -------------------------->





      <div class="login-container" id="login">
        <div class="notify">
          <?php
          // Check if error message exists in the URL
          if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials') {
            echo "<p>Invalid login. Please retry with correct email and password.</p>";
          }
          ?>
        </div>
        <div class="top">
          <span>Don't have an account?
            <a href="#" onclick="register()">Sign Up</a></span>
          <header>Login</header>
        </div>

        <form action="../php/login.php" method="post">

          <div class="input-box">
            <input type="text" class="input-field" placeholder="Email" name="email" required />
            <i class="bx bx-user"></i>
          </div>

          <div class="input-box">
            <input type="password" class="input-field" placeholder="Password" name="password" required />
            <i class="bx bx-lock-alt"></i>
          </div>

          <div class="input-box">
            <input type="submit" class="submit" value="Sign In" />
          </div>

          <div class="two-col">

            <div class="one">
              <input type="checkbox" id="login-check" />
              <label for="login-check"> Remember Me</label>
            </div>
            <div class="two">
              <label><a href="#">Forgot password?</a></label>
            </div>

          </div>

        </form>
      </div>




      <!------------------- registration form -------------------------->






      <div class="register-container" id="register">
        <div class="notify">
          <?php
          // Check if error message exists in the URL
          if (isset($_GET['error']) && $_GET['error'] === 'existing_user') {
            echo "<p>User already exists, try different email / username.</p>";
          } else if (isset($_GET['error']) && $_GET['error'] === 'Unsuccessfull') {
            echo "<p>Unsuccessful, please try again.</p>";
          }
          ?>
        </div>
        <form action="../php/register.php" method="post" onsubmit="return validateForm()">
          <div class="top">
            <span>Have an account? <a href="#" onclick="login()">Login</a></span>
            <header>Sign Up</header>
          </div>
          <div>

            <div class="input-box">
              <input type="text" class="input-field" placeholder="Username" id="username" name="username" required />
              <i class="bx bx-user"></i>
            </div>

            <div class="input-box">
              <input type="email" id="emailInput" class="input-field" placeholder="Email" name="email" required />
              <i class="bx bx-envelope"></i>
            </div>

            <div class="input-box">
              <input type="password" class="input-field" placeholder="Password" id="password" name="password"
                required />
              <i class="bx bx-lock-alt"></i>
            </div>

            <div class="input-box">
              <input type="password" class="input-field" placeholder="Confirm Password" id="password_confirmation"
                name="password_confirmation" required />
              <i class="bx bx-lock-alt"></i>
            </div>

            <div class="input-box">
              <input type="submit" class="submit" value="Register" onclick=validateForm() />
            </div>

          </div>
        </form>
      </div>
    </div>

  </div>


  ---------------------------Scripts------------------------------


  <script>
    function myMenuFunction() {
      var i = document.getElementById("navMenu");
      if (i.className === "nav-menu") {
        i.className += " responsive";
      } else {
        i.className = "nav-menu";
      }
    }
  </script>
  <script>
    var a = document.getElementById("loginBtn");
    var b = document.getElementById("registerBtn");
    var x = document.getElementById("login");
    var y = document.getElementById("register");
    function login() {
      x.style.left = "4px";
      y.style.right = "-520px";
      a.className += " white-btn";
      b.className = "btn";
      x.style.opacity = 1;
      y.style.opacity = 0;
    }
    function register() {
      x.style.left = "-510px";
      y.style.right = "5px";
      a.className = "btn";
      b.className += " white-btn";
      x.style.opacity = 0;
      y.style.opacity = 1;
    }
    function validateForm() {
      var username = document.getElementById("username").value;
      var email = document.getElementById("emailInput").value;
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("password_confirmation").value;

      // Perform validation
      if (username.trim() == "" || email.trim() == "" || password.trim() == "" || confirmPassword.trim() == "") {
        alert("All fields are required");
        return false;
      }

      if (!/\S+@\S+\.\S+/.test(email)) {
        alert("Invalid email format");
        return false;
      }

      if (password.length < 8) {
        alert("Password must be at least 8 characters long");
        return false;
      }

      if (!/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
        alert("Password must contain at least one letter and one number");
        return false;
      }

      if (password !== confirmPassword) {
        alert("Passwords do not match");
        return false;
      }

      return true; // Form is valid
    }
    function removeErrorMessage() {
    if (window.location.search.includes('error=')) {
      // Clear the URL parameters without reloading the page
      var url = window.location.href.split('?')[0];
      window.history.replaceState({}, document.title, url);
    }
  }

  // Call the function when the page is loaded
  window.onload = removeErrorMessage;
  </script>
</body>

</html>