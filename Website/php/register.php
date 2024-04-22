<?php
require_once 'database.php';

if (
    isset($_POST['username']) && $_POST['username'] != ""
    && isset($_POST['email']) && $_POST['email'] != ""
    && isset($_POST['password']) && $_POST['password'] != ""
    && isset($_POST['password_confirmation']) && $_POST['password_confirmation'] != ""
) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Validate password length
    if (strlen($password) < 8) {
        die("Password must be at least 8 characters long");
    }

    // Validate password complexity (at least one letter and one number)
    if (!preg_match("/[a-zA-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        die("Password must contain at least one letter and one number");
    }

    if ($_POST["password"] !== $_POST["password_confirmation"]) {
        die("Passwords must match");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user already exists
    $query = "SELECT * FROM user WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("User already exists. Please try another email or login.");
    }

    // Insert new user into the database
    $insertQuery = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        echo"signup successful";
        header("Location: ../html/login.html");
        exit;
    } else {
        die("Error in registration: " . $mysqli->error);
    }
} else {
    die("All fields are required");
}
?>
