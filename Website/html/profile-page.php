<?php
session_start();
if (isset($_SESSION["UserID"])) {
  $mysqli = require_once '../php/database.php';//require __DIR__ . "..\php\database.php";
  $sql = "SELECT * FROM user 
          WHERE UserID = {$_SESSION["UserID"]}";

  $result = $mysqli->query($sql);
  $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chopz | Profile</title>
  <link rel="stylesheet" href="../css/homepage.css" />
  <link rel="stylesheet" href="../css/profile-page.css" />
</head>

<body>
  <header>
    <div class="navbar">
      <a href="Homepage.html" class="logo">
        <img src="../css/images/logo.png" alt="Logo" style="height: 70px; width: 150px" />
      </a>
    </div>
  </header>

  <!---------------------------------side panel-------------------------------------------->

  <div class="container">
    <div class="side-panel">
      <div class="panel-content">
        <img src="your-image.jpg" alt="Profile Picture" />
        <div class="side-panel-info">
          <?php if (isset($user)): ?>
            <p> <?= htmlspecialchars($user["username"]) ?> </p>
          <?php endif; ?>
          <div class="user-info">
            <div class="user-name">User Name</div>
            <div class="user-role">User Role</div>
          </div>
          <a href=""><button>Posts</button></a>
          <a href=""><button>Saved Recipes</button></a>
          <a href=""><button>Edit Profile</button></a>
          <form action="../php/logout.php">
            <button>Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>