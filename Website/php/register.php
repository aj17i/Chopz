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

    //activation code generate and hash
    $activation_token = bin2hex(random_bytes(16));
    $activation_token_hash = hash("sha256", $activation_token);

    // Check if the user already exists
    $query = "SELECT * FROM user WHERE email = ? OR username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../html/loginpage.php?error=existing_user");
        exit();
    }

    // Insert new user into the database
    $insertQuery = "INSERT INTO user (username, email, password, join_date, account_activation_hash) VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $mysqli->prepare($insertQuery);
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $activation_token_hash);

    if ($stmt->execute()) {
        $mail = require __DIR__ . "/mailer.php";
        $mail->setFrom("noreply@Chopz.com");
        $mail->addAddress($_POST['email']);
        $mail->Subject = "Account Activation";
        $mail->isHTML(true); // Ensure HTML format
        $resetLink = "http://localhost:3000/Website/php/activate_account.php?token=$activation_token";
        $mail->Body = "Click <a href=\"$resetLink\">here</a> to activate your account"; // Using variable for link
        $mail->AltBody = "Click the following link to activate your account: $resetLink"; // Plain text alternative

        try {
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            exit;
        }

        header("Location: ../html/loginpage.php?error=Signup_Successful");
        exit;
    } else {
        header("Location: ../html/loginpage.php?error=Unsuccessfull");
        exit();
    }
}
?>