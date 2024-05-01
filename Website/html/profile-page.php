<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged']!== true ) {
  header("Location: loginpage.php");
  exit();
} 
  $mysqli = require_once '../php/database.php';//require __DIR__ . "..\php\database.php";
  $sql = "SELECT * FROM user 
          WHERE UserID = {$_SESSION["UserID"]}";

  $result = $mysqli->query($sql);
  $user = $result->fetch_assoc();

  // Query to count followers
  $sql_followers = "SELECT COUNT(*) AS num_followers FROM `follower list` WHERE FollowedAccountID = {$_SESSION["UserID"]}";
  $result_followers = $mysqli->query($sql_followers);
  $row_followers = $result_followers->fetch_assoc();
  $num_followers = $row_followers['num_followers'];

  // Query to count following
  $sql_following = "SELECT COUNT(*) AS num_following FROM `follower list` WHERE FollowingAccountID = {$_SESSION["UserID"]}";
  $result_following = $mysqli->query($sql_following);
  $row_following = $result_following->fetch_assoc();
  $num_following = $row_following['num_following'];

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
      <a href="Homepage.php" class="logo">
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
            <p style="font-weight: bolder;"> <?= htmlspecialchars($user["username"]) ?> </p>
          <?php endif; ?>
          <div class="user-info">
            <div class="user-name">Followers</div>
            <div class="user-role">Following</div>
          </div>
          <div class="user-info-number">
            <div class="user-num"><?= $num_followers ?></div>
            <div class="user-rum"><?= $num_following ?></div>
          </div>
          <a href=""><button>Posts</button></a>
          <a href=""><button>Saved Recipes</button></a>
          <a href="edit-profile.php"><button>Edit Profile</button></a>
          <form action="../php/logout.php">
            <button>Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div>

  </div>
</body>

</html>