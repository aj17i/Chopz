<?php
session_start();
if (!isset($_SESSION["UserID"])) {
    header("Location: loginpage.php");
    exit();
} else {
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chopz | edit profile</title>
</head>

<body>
    <h1>edit profile</h1>
    <a href="profile-page.php">back</a>
</body>

</html>