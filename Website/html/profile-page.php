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
$sql_followers = "SELECT COUNT(*) AS num_followers FROM follower_list WHERE FollowedAccountID = {$_SESSION["UserID"]}";
$result_followers = $mysqli->query($sql_followers);
$row_followers = $result_followers->fetch_assoc();
$num_followers = $row_followers['num_followers'];

// Query to count following
$sql_following = "SELECT COUNT(*) AS num_following FROM follower_list WHERE FollowingAccountID = {$_SESSION["UserID"]}";
$result_following = $mysqli->query($sql_following);
$row_following = $result_following->fetch_assoc();
$num_following = $row_following['num_following'];

$sql_image = "SELECT profilePic FROM user WHERE UserID = {$_SESSION["UserID"]}";
$res = mysqli_query($conn, $sql_image);

$sql_number = "SELECT  COUNT(*) AS num_recipes FROM recipe WHERE UserID = {$_SESSION["UserID"]}";
$result_number = $mysqli->query($sql_number);
$row_number = $result_number->fetch_assoc();
$num_recipes = $row_number['num_recipes'];

$sql_avg = "SELECT ROUND(AVG(average_Rating), 2) AS average_rating FROM recipe WHERE UserID = {$_SESSION["UserID"]}";
$result_average = $mysqli->query($sql_avg);
$row_average = $result_average->fetch_assoc();
$recipes_rating = $row_average['average_rating'];

$user_avg = "SELECT ROUND(AVG(average_rating),2) AS user_average_rating FROM user WHERE UserID = {$_SESSION["UserID"]}";
$result_user_avg = $mysqli->query($user_avg);
$row_user_avg = $result_user_avg->fetch_assoc();
$user_rating_avg = $row_user_avg['user_average_rating'];

$user_name = "SELECT full_name, nationality, bio FROM user WHERE UserID = {$_SESSION["UserID"]}";
$result_user_name = $mysqli->query($user_name);
$row_user_name = $result_user_name->fetch_assoc();
$user_name_full = $row_user_name['full_name'];
$user_nationality = $row_user_name['nationality'];
$user_bio = $row_user_name['bio'];



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
          <a href="view-follower-list.php"><button>View Followers</button></a>
          <a href="view-edit-settings.php"><button>Settings</button></a>
          <form action="../php/logout.php">
            <button>Logout</button>
          </form>
        </div>
      </div>
    </div>


    <!--------------------------profile content------------------------------------->


    <div class="content">
      <div class="first-row">
        <div class="statistics">
          <div class="number-of-posted-recipes">
            <img src="../css/images/chart.png" alt="">
            Posted Recipes:
            <?= $num_recipes ?>
          </div>
          <div class="average-rating-of-recipes">
            <img src="../css/images/pie-chart.png" alt="">
            Recipe Ratings:
            <?= $recipes_rating ?>
          </div>
          <div class="average-rating-of-recipes">
            <img src="../css/images/recipe.png" alt="">
            Personal Rating:
            <?= $user_rating_avg ?>
          </div>
        </div>
        <div class="create-a-post">
          <a href="create-post.php"><button><img src="../css/images/video.png" alt="add recipe"> Add
              Recipe</button></a>
        </div>
      </div>
      <div class="row-zero">
        <div class="bio-left">
          <div>
            <img src="../css/images/press-pass.png" alt=""> <!-- image icon of name -->
            <span><?= $user_name_full ?></span>
          </div>
          <br>
          <div>
            <img src="../css/images/united-nations.png" alt=""> <!-- image icon of nationality -->
            <span><?= $user_nationality ?></span>
          </div>
        </div>
        <div class="bio-right">
          <img src="../css/images/information.png" alt=""> <!-- image icon -->
          <br>
          <span><?= $user_bio ?></span>
        </div>
      </div>
      <div class="second-row-favourites">
        <div class="top-part-a">
          <h2>Favorites </h2>
          <a href="saved-posts.php"><img src="../css/images/bookmark.png" alt=""></a>
        </div>
        <div class="carousel-container">
          <section class="product">
            <button class="pre-btn"><img src="images/arrow.png" alt="" /></button>
            <button class="nxt-btn"><img src="images/arrow.png" alt="" /></button>
            <div class="product-container">
              <?php
              $userId = $_SESSION["UserID"];
              $savedRecipesQuery = "SELECT RecipeID FROM saved_recipes WHERE UserID = ?";
              $stmt1 = mysqli_prepare($conn, $savedRecipesQuery);
              mysqli_stmt_bind_param($stmt1, "i", $userId);
              mysqli_stmt_execute($stmt1);
              $savedRecipesResult = mysqli_stmt_get_result($stmt1);

              while ($savedRecipeRow = mysqli_fetch_assoc($savedRecipesResult)) {

                $recipeId = $savedRecipeRow['RecipeID'];

                $titleQuery = "SELECT title FROM recipe WHERE RecipeID = ?";
                $stmt2 = mysqli_prepare($conn, $titleQuery);
                mysqli_stmt_bind_param($stmt2, "i", $recipeId);
                mysqli_stmt_execute($stmt2);
                $titleResult = mysqli_stmt_get_result($stmt2);
                $titleRow = mysqli_fetch_assoc($titleResult);
                mysqli_stmt_close($stmt2);

                $thumbnailQuery = "SELECT thumbnail FROM recipe_images WHERE RecipeID = ? LIMIT 1";
                $stmt3 = mysqli_prepare($conn, $thumbnailQuery);
                mysqli_stmt_bind_param($stmt3, "i", $recipeId);
                mysqli_stmt_execute($stmt3);
                $thumbnailResult = mysqli_stmt_get_result($stmt3);
                $thumbnailRow = mysqli_fetch_assoc($thumbnailResult);
                mysqli_stmt_close($stmt3);

                if ($titleRow) {
                  ?>
                  <div class="product-card">
                    <div class="product-image">
                      <?php if ($thumbnailRow && $thumbnailRow['thumbnail']) { ?>
                        <img src="<?php echo $thumbnailRow['thumbnail']; ?>" class="product-thumb" alt="Thumbnail" />
                        <a href="recipe-view.php?RecipeID=<?php echo $recipeId; ?>">
                          <button class="card-btn">View Recipe</button>
                        </a>
                      </div>
                    <?php } ?>
                    <div class="product-info">
                      <h2 class="product-brand"><?php echo $titleRow['title']; ?></h2>
                    </div>

                  </div>
                  <?php
                }
              }
              mysqli_stmt_close($stmt1);
              ?>
            </div>
          </section>
        </div>
      </div>
      <div class="third-row-recent">
        <div class="top-part-b">
          <h2>Recently Posted</h2>
          <a href="posted-recipes.php"><img src="../css/images/cookbook.png" alt=""></a>
        </div>
        <div class="carousel-container">
          <section class="product">
            <button class="pre-btn"><img src="images/arrow.png" alt="" /></button>
            <button class="nxt-btn"><img src="images/arrow.png" alt="" /></button>
            <div class="product-container">
              <?php
              $userId = $_SESSION["UserID"];

              $titleQuery = "SELECT RecipeID, title 
              FROM recipe 
              WHERE UserID = $userId 
              ORDER BY creation_date DESC 
              LIMIT 10";
              $titleResult = mysqli_query($conn, $titleQuery);

              while ($titleRow = mysqli_fetch_assoc($titleResult)) {
                $recipeId = $titleRow['RecipeID'];
                $url = "recipe-view.php?RecipeID=$recipeId";

                $thumbnailQuery = "SELECT thumbnail FROM recipe_images WHERE RecipeID = $recipeId";
                $thumbnailResult = mysqli_query($conn, $thumbnailQuery);
                $thumbnailRow = mysqli_fetch_assoc($thumbnailResult);

                if ($thumbnailRow) {
                  ?>
                  <div class="product-card">
                    <div class="product-image">
                      <img src="<?php echo $thumbnailRow['thumbnail']; ?>" class="product-thumb" alt="" />
                      <a href="<?php echo $url; ?>">
                        <button class="card-btn-left">View Recipe</button>
                      </a><br>
                      <a href="#" class="deleteRecipeBtn" data-recipeid="<?php echo $recipeId; ?>">
                        <button class="card-btn-right">Delete Recipe</button>
                      </a>

                    </div>
                    <div class="product-info">
                      <h2 class="product-brand"><?php echo $titleRow['title']; ?></h2>
                    </div>
                  </div>
                  <?php
                } else {
                  ?>
                  <div class="product-card">
                    <div class="product-info">
                      <h2 class="product-brand"><?php echo $titleRow['title']; ?></h2>
                      <p>No thumbnail available</p>
                    </div>
                  </div>
                  <?php
                }
              }
              ?>
            </div>
          </section>
        </div>
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



    const productContainers = [...document.querySelectorAll('.product-container')];
    const nxtBtn = [...document.querySelectorAll('.nxt-btn')];
    const preBtn = [...document.querySelectorAll('.pre-btn')];

    productContainers.forEach((item, i) => {
      let containerDimensions = item.getBoundingClientRect();
      let containerWidth = containerDimensions.width;

      nxtBtn[i].addEventListener('click', () => {
        item.scrollLeft += containerWidth;
      })

      preBtn[i].addEventListener('click', () => {
        item.scrollLeft -= containerWidth;
      })
    });

    document.addEventListener("DOMContentLoaded", function () {
      var deleteButtons = document.querySelectorAll(".deleteRecipeBtn");

      deleteButtons.forEach(function (button) {
        button.addEventListener("click", function (e) {
          e.preventDefault();
          var cardToRemove = this.closest('.product-card');

          if (confirm("Are you sure you want to delete this recipe?")) {
            var recipeId = this.getAttribute('data-recipeid');

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/delete-recipe.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
              if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText.trim() !== '') {
                  var response = JSON.parse(xhr.responseText);
                  if (response.success) {
                    alert(response.message);
                    cardToRemove.remove();
                  } else {
                    alert(response.message);
                  }
                } else {
                  alert("Error: Empty response from server.");
                }
              }
            };
            xhr.send("recipeId=" + recipeId);
          }
        });
      });
    });

  </script>
</body>

</html>