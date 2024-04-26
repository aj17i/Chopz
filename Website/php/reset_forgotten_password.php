<?php
$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM user 
        WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <title>Chopz | Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <form action="process_reset_password.php" method="post" onsubmit="return validateForm()">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token)?>">

        <label for="password">New Password</label>
        <input type="password" id="password" name = "password">

        <label for="password_confirmation">Confirm Password</label>
        <input type="password_confirmation" id="password_confirmation" name = "password_confirmation">

        <input type="submit" >

    </form>
    <script src="..\javascript\reset_password.js"></script>
</body>
</html>