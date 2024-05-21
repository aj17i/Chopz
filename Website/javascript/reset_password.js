function validateForm() {

    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("password_confirmation").value;

    if (password.trim() == "" || confirmPassword.trim() == "") {
      alert("All fields are required");
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