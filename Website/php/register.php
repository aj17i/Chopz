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

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user already exists
    $query = "SELECT * FROM user WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../html/loginpage.php?error=existing_user");
        exit();
    }

    // Insert new user into the database
    $insertQuery = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        
        header("Location: ../html/loginpage.php");
        exit;
    } else {
        header("Location: ../html/loginpage.php?error=Unsuccessfull");
        exit();
    }
} 
?>