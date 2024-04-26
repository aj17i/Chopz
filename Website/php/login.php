<?php
require_once 'database.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["password"], $user["password"])) {
            session_start();
            session_regenerate_id();
            // Set session variables
            $_SESSION["UserID"] = $user["UserID"];
            $_SESSION["username"] = $user["username"];

            // Redirect to the homepage
            header("Location: ../html/Homepage.html");
            exit(); // Stop further execution
        } else {
            // Password is incorrect
            header("Location: ../html/loginpage.php?error=invalid_credentials");
            exit();
        }
    } else {
        // User not found
        header("Location: ../html/loginpage.php?error=invalid_credentials");
        exit();
    }
}
?>
