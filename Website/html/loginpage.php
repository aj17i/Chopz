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
        <?php
        // Check if error message exists in the URL
        if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials') {
          echo "<p style='color: white;'>Invalid login. Please retry with correct email and password.</p>";
        }
        ?>
        <div class="top">
          <span>Don't have an account?
            <a href="#" onclick="register()">Sign Up</a></span>
          <header>Login</header>
        </div>

        <form action="../php/login.php" method="post">

          <div class="input-box">
            <input type="text" class="input-field" placeholder="Email" name="email" />
            <i class="bx bx-user"></i>
          </div>

          <div class="input-box">
            <input type="password" class="input-field" placeholder="Password" name="password" />
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
        <form action="../php/register.php" method="post">
          <div class="top">
            <span>Have an account? <a href="#" onclick="login()">Login</a></span>
            <header>Sign Up</header>
          </div>
          <div>

            <div class="input-box">
              <input type="text" class="input-field" placeholder="Username" id="username" name="username" />
              <i class="bx bx-user"></i>
            </div>

            <div class="input-box">
              <input type="email" id="emailInput" class="input-field" placeholder="Email" name="email" />
              <i class="bx bx-envelope"></i>
            </div>

            <div class="input-box">
              <input type="password" class="input-field" placeholder="Password" id="password" name="password" />
              <i class="bx bx-lock-alt"></i>
            </div>

            <div class="input-box">
              <input type="password" class="input-field" placeholder="Confirm Password" id="password_confirmation"
                name="password_confirmation" />
              <i class="bx bx-lock-alt"></i>
            </div>

            <div class="input-box">
              <input type="submit" class="submit" value="Register" onclick="return validateEmail()" />
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
  </script>
  <script>
    // Function to remove the error message from the URL when page is refreshed
    function removeErrorMessage() {
      if (window.location.search.includes('error=invalid_credentials')) {
        // Remove the error message from the URL without reloading the page
        history.replaceState({}, document.title, window.location.pathname);
      }
    }

    // Call the function when the page is loaded
    window.onload = removeErrorMessage;
  </script>
</body>

</html>