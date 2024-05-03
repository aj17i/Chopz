<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
  header("Location: loginpage.php");
  exit();
}
$mysqli = require_once '../php/database.php';
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

$sql_image = "SELECT profilePic FROM user WHERE UserID = {$_SESSION["UserID"]}";
$res = mysqli_query($conn, $sql_image);

$sql_number = "SELECT  COUNT(*) AS num_recipes FROM recipe WHERE UserID = {$_SESSION["UserID"]}";
$result_number = $mysqli->query($sql_number);
$row_number = $result_number->fetch_assoc();
$num_recipes = $row_number['num_recipes'];

$sql_avg = "SELECT AVG(average_Rating) AS average_rating FROM recipe WHERE UserID = {$_SESSION["UserID"]}";
$result_average = $mysqli->query($sql_avg);
$row_average = $result_average->fetch_assoc();
$recipes_rating = $row_average['average_rating'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chopz | Profile</title>
  <link rel="stylesheet" href="../css/profile-page.css" />
</head>

<body>
  <header>
    <div class="navbar">
      <a href="Homepage.php" class="logo">
        <img src="../css/images/logo.png" alt="Logo" style="height: 70px; width: 150px" />
      </a>
      <button class="side-panel-btn" onclick="toggleSidePanel()">Menu</button>
    </div>
  </header>

  <!---------------------------------side panel-------------------------------------------->

  <div class="container">
    <div class="side-panel" id="sidePanel">
      <div class="panel-content">
        <?php if (mysqli_num_rows($res) > 0) {
          while ($images = mysqli_fetch_assoc($res)) { ?>
            <div class="profile-pic">
              <img src="../css/images/<?= $images['profilePic'] ?>" alt="Profile Picture" />
            </div>
          <?php }
        } ?>
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
          <a href=""><button>View Followers</button></a>
          <a href=""><button>Settings</button></a>
          <a href="edit-profile.php"><button>Edit Profile</button></a>
          <form action="../php/logout.php">
            <button>Logout</button>
          </form>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="first-row">
        <div class="statistics">
          <div class="number-of-posted-recipes">
            <img src="../css/images/chart.png" alt="">
            <?= $num_recipes ?>
          </div>
          <div class="average-rating-of-recipes">
            <img src="../css/images/pie-chart.png" alt="">
            <?= $recipes_rating ?>
          </div>
        </div>
        <div class="create-a-post">
          <a href="create-post.php"><button><img src="../css/images/video.png" alt="add recipe"> Add
              Recipe</button></a>
        </div>
      </div>
      <div class="second-row-favourites">
        <h3>Carousel of favorite recipes plus a link</h3>
      </div>
      <div class="third-row-recent">
        <h3>Carousel of recently posted recipes plus a link</h3>
      </div>
    </div>
  </div>

  <script>
    function toggleSidePanel() {
      var sidePanel = document.getElementById('sidePanel');
      sidePanel.classList.toggle('show');
    }
    document.addEventListener('click', function (event) {
      var sidePanel = document.getElementById('sidePanel');
      var sidePanelBtn = document.querySelector('.side-panel-btn');


      if (!sidePanel.contains(event.target) && !sidePanelBtn.contains(event.target)) {
        sidePanel.classList.remove('show');
      }
    });
  </script>
</body>

</html>