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

$sql_avg = "SELECT AVG(average_Rating) AS average_rating FROM recipe WHERE UserID = {$_SESSION["UserID"]}";
$result_average = $mysqli->query($sql_avg);
$row_average = $result_average->fetch_assoc();
$recipes_rating = $row_average['average_rating'];

$user_avg = "SELECT AVG(average_rating) AS user_average_rating FROM user WHERE UserID = {$_SESSION["UserID"]}";
$result_user_avg = $mysqli->query($user_avg);
$row_user_avg = $result_user_avg->fetch_assoc();
$user_rating_avg = $row_user_avg['user_average_rating'];
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
              // Assuming you have already connected to your database
              $userId = $_SESSION["UserID"];

              // Query to retrieve saved recipe IDs for the current user
              $savedRecipesQuery = "SELECT RecipeID FROM saved_recipes WHERE UserID = ?";

              // Prepare the statement
              $stmt1 = mysqli_prepare($conn, $savedRecipesQuery);

              // Bind parameters
              mysqli_stmt_bind_param($stmt1, "i", $userId);

              // Execute the statement
              mysqli_stmt_execute($stmt1);

              // Get result
              $savedRecipesResult = mysqli_stmt_get_result($stmt1);

              // Loop through each saved recipe
              while ($savedRecipeRow = mysqli_fetch_assoc($savedRecipesResult)) {
                $recipeId = $savedRecipeRow['RecipeID'];

                // Query to retrieve title based on RecipeID
                $titleQuery = "SELECT title FROM recipe WHERE RecipeID = ?";

                // Prepare the statement
                $stmt2 = mysqli_prepare($conn, $titleQuery);

                // Bind parameters
                mysqli_stmt_bind_param($stmt2, "i", $recipeId);

                // Execute the statement
                mysqli_stmt_execute($stmt2);

                // Get result
                $titleResult = mysqli_stmt_get_result($stmt2);

                // Fetch title
                $titleRow = mysqli_fetch_assoc($titleResult);

                // Close the statement
                mysqli_stmt_close($stmt2);

                // Query to retrieve thumbnail based on RecipeID
                $thumbnailQuery = "SELECT thumbnail FROM recipe_images WHERE RecipeID = ? LIMIT 1";

                // Prepare the statement
                $stmt3 = mysqli_prepare($conn, $thumbnailQuery);

                // Bind parameters
                mysqli_stmt_bind_param($stmt3, "i", $recipeId);

                // Execute the statement
                mysqli_stmt_execute($stmt3);

                // Get result
                $thumbnailResult = mysqli_stmt_get_result($stmt3);

                // Fetch thumbnail
                $thumbnailRow = mysqli_fetch_assoc($thumbnailResult);

                // Close the statement
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

              // Close the statement
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
              // Assuming you have already connected to your database
              $userId = $_SESSION["UserID"];

              // First query to retrieve titles and recipe IDs
              $titleQuery = "SELECT RecipeID, title 
              FROM recipe 
              WHERE UserID = $userId 
              ORDER BY creation_date DESC 
              LIMIT 10";
              $titleResult = mysqli_query($conn, $titleQuery);

              // Loop through each title and recipe ID
              while ($titleRow = mysqli_fetch_assoc($titleResult)) {
                $recipeId = $titleRow['RecipeID'];
                $url = "recipe-view.php?RecipeID=$recipeId";

                // Second query to retrieve thumbnails using the recipe ID
                $thumbnailQuery = "SELECT thumbnail FROM recipe_images WHERE RecipeID = $recipeId";
                $thumbnailResult = mysqli_query($conn, $thumbnailQuery);
                $thumbnailRow = mysqli_fetch_assoc($thumbnailResult);

                // Check if thumbnail exists
                if ($thumbnailRow) {
                  ?>
                  <div class="product-card">
                    <div class="product-image">
                      <img src="<?php echo $thumbnailRow['thumbnail']; ?>" class="product-thumb" alt="" />
                      <a href="<?php echo $url; ?>">
                        <button class="card-btn">View Recipe</button>
                      </a>
                    </div>
                    <div class="product-info">
                      <h2 class="product-brand"><?php echo $titleRow['title']; ?></h2>
                    </div>
                  </div>
                  <?php
                } else {
                  // Handle case where thumbnail is not found
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
    })
  </script>
</body>

</html>