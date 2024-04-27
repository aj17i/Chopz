<?php
$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM user 
        WHERE account_activation_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

$sql = "UPDATE user 
        SET account_activation_hash = NULL
        WHERE UserID = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s",$user["UserID"]);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activated</title>
</head>
<body>
    <h1>Account Activated</h1>
    <p>Account activated successfully. You can <a href="../html/loginpage.php">log in</a> </p>
</body>
</html>