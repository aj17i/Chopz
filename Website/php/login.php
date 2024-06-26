<?php
require_once 'database.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($user && $user["account_activation_hash"] === null) {
        if (password_verify($_POST["password"], $user["password"])) {
            session_start();
            session_regenerate_id();

            $_SESSION["UserID"] = $user["UserID"];
            $_SESSION["username"] = $user["username"];
            $_SESSION['logged'] = true;


            header("Location: ../html/Homepage.php");
            exit(); 
        } else {

            header("Location: ../html/loginpage.php?error=invalid_credentials");
            exit();
        }
    } else {

        header("Location: ../html/loginpage.php?error=invalid_credentials");
        exit();
    }
}
?>
