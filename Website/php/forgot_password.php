<?php 
$email = $_POST['email'];
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$mysqli = require __DIR__ . "/database.php";

$sql = "UPDATE user 
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($mysqli->affected_rows) {
    $mail = require __DIR__ . "/mailer.php";
    $mail->setFrom("noreply@Chopz.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->isHTML(true); // Ensure HTML format
    $resetLink = "http://localhost:3000/Website/php/reset_forgotten_password.php?token=$token";
    $mail->Body = "Click <a href=\"$resetLink\">here</a> to reset your password"; // Using variable for link
    $mail->AltBody = "Click the following link to reset your password: $resetLink"; // Plain text alternative

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
}

echo "Message sent. Please check your inbox.";
