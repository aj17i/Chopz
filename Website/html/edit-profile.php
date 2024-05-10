<?php
session_start();
$mysqli = require_once '../php/database.php';
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="../css/edit-profile.css">
    <title>Chopz | edit profile</title>
</head>

<body>
    <h1>Edit profile</h1>

    <?php if (isset($_GET['error'])): ?>
        <p style="color : white;"><?php echo $_GET['error'] ?></p>
    <?php endif ?>


    <form action="..\php\process-edit-profile.php" method="post" enctype="multipart/form-data" class="edit-profile">
        <label for="profilePic">Profile Image</label>
        <input type="file" id="profilePic" name="profilePic">

        <label for="username">Username:</label>
        <input type="text" id="username" name="username">

        <label for="first_name">First name:</label>
        <input type="text" id="first_name" name="first_name">

        <label for="last_name">Last name</label>
        <input type="text" id="last_name" name="last_name">

        <label for="nationality">Nationality</label>
        <input type="text" id="nationality" name="nationality">

        <label for="bio">Bio</label>
        <input type="text" id="bio" name="bio">

        <input type="submit" id="submit" value="upload">

        <button><a href="profile-page.php">Back</a></button>
    </form>
</body>

</html>