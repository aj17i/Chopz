function myMenuFunction() {
    var i = document.getElementById("navMenu");
    if (i.className === "nav-menu") {
      i.className += " responsive";
    } else {
      i.className = "nav-menu";
    }
  }

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

    return true;
  }
  function removeErrorMessage() {
  if (window.location.search.includes('error=')) {
    var url = window.location.href.split('?')[0];
    window.history.replaceState({}, document.title, url);
  }
}

window.onload = removeErrorMessage;