<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
  header("Location: loginpage.php");
  exit();
}
require_once '../php/database.php';

$username = $_GET['username'];

$userid_sql = "SELECT userID FROM user WHERE username = ?";
$stmt = $mysqli->prepare($userid_sql);
$stmt->bind_param("s", $_GET["username"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$userID = $user['userID'];

$sql = "SELECT * FROM user WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Query to count followers
$sql_followers = "SELECT COUNT(*) AS num_followers FROM follower_list WHERE FollowedAccountID = $userID";
$result_followers = $mysqli->query($sql_followers);
$row_followers = $result_followers->fetch_assoc();
$num_followers = $row_followers['num_followers'];

// Query to count following
$sql_following = "SELECT COUNT(*) AS num_following FROM follower_list WHERE FollowingAccountID = $userID";
$result_following = $mysqli->query($sql_following);
$row_following = $result_following->fetch_assoc();
$num_following = $row_following['num_following'];

//query for profile pic
$sql_image = "SELECT profilePic FROM user WHERE UserID = $userID";
$res = mysqli_query($conn, $sql_image);

//query for recipe number
$sql_number = "SELECT  COUNT(*) AS num_recipes FROM recipe WHERE UserID = $userID";
$result_number = $mysqli->query($sql_number);
$row_number = $result_number->fetch_assoc();
$num_recipes = $row_number['num_recipes'];

//query for average recipe rating
$sql_avg = "SELECT ROUND(AVG(average_Rating), 2) AS average_rating FROM recipe WHERE UserID = $userID";
$result_average = $mysqli->query($sql_avg);
$row_average = $result_average->fetch_assoc();
$recipes_rating = $row_average['average_rating'];

//query for average person rating
$user_avg = "SELECT ROUND(AVG(average_rating),2) AS user_average_rating FROM user WHERE UserID = $userID";
$result_user_avg = $mysqli->query($user_avg);
$row_user_avg = $result_user_avg->fetch_assoc();
$user_rating_avg = $row_user_avg['user_average_rating'];


//query for user info
$user_name = "SELECT full_name, nationality, bio FROM user WHERE UserID = $userID";
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
  <link rel="stylesheet" href="../css/user-profile-page.css" />
  <script src="https://kit.fontawesome.com/18ace14423.js" crossorigin="anonymous"></script>
</head>

<>
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
        <div>
          <div></div>
          <i class="fa fa-star fa-2x" data-index="0"></i>
          <i class="fa fa-star fa-2x" data-index="1"></i>
          <i class="fa fa-star fa-2x" data-index="2"></i>
          <i class="fa fa-star fa-2x" data-index="3"></i>
          <i class="fa fa-star fa-2x" data-index="4"></i>
        </div>
        <div>
          <button id="confirmRating" style="display: none;">Confirm Rating</button>
        </div>
        <hr>
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
        </div>
        <hr>
        <div class="follow-container">
          <div class="follow" id="followBtnContainer" style="display: none;">
            <img src="../css/images/add-user.png" alt="">
            <button id="followBtn">Follow</button>
          </div>
          <div class="unfollow" id="unfollowBtnContainer" style="display: none;">
            <img src="../css/images/remove-user.png" alt="">
            <button id="unfollowBtn">Unfollow</button>
          </div>
        </div>
        <div class="report">
          <img src="../css/images/block-user.png" alt="">
          <button id="reportBtn">Report</button>
        </div>
        <div id="message" style = "marging-top:10px;"></div>
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
            User Rating:
            <?= $user_rating_avg ?>
          </div>
        </div>
      </div>
      <div class="row-zero">
        <div class="bio-left">
          <div>
            <img src="../css/images/press-pass.png" alt="">
            <span><?= $user_name_full ?></span>
          </div>
          <br>
          <div>
            <img src="../css/images/united-nations.png" alt="">
            <span><?= $user_nationality ?></span>
          </div>
        </div>
        <div class="bio-right">
          <img src="../css/images/information.png" alt="">
          <br>
          <span><?= $user_bio ?></span>
        </div>
      </div>
      <div class="third-row-recent">
        <div class="carousel-container">
          <section class="product">
            <div class="product-container">
              <?php

              $titleQuery = "SELECT RecipeID, title 
              FROM recipe 
              WHERE UserID = $userID
              ORDER BY creation_date DESC";
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
                        <button class="card-btn">View Recipe</button>
                      </a><br>

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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

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

    function getusernameFromUrl() {
      var urlParams = new URLSearchParams(window.location.search);
      var username = urlParams.get('username');
      return username;
    }
    var ratedIndex = -1;
    var username = getusernameFromUrl();
    var confirmRatingBtn = $('#confirmRating');
    $(document).ready(function () {
      resetStarColors();

      $.ajax({
        url: "../php/check-user-rate.php",
        method: "POST",
        dataType: 'json',
        data: {
          check: 1,
          username: username
        },
        success: function (response) {
          if (response.ratedIndex != null) {
            ratedIndex = parseInt(response.ratedIndex);
            setStars(ratedIndex);
            disableRating();
          } else {
            $('.fa-star').on('click', function () {
              ratedIndex = parseInt($(this).data('index'));
              localStorage.setItem('ratedIndex', ratedIndex);
              showConfirmButton();
            });

            confirmRatingBtn.on('click', function () {
              saveToTheDB(username);
              disableRating();
              confirmRatingBtn.hide();
            });

            $('.fa-star').mouseover(function () {
              resetStarColors();
              var currentIndex = parseInt($(this).data('index'));
              setStars(currentIndex);
            });

            $('.fa-star').mouseleave(function () {
              resetStarColors();
              if (ratedIndex != -1)
                setStars(ratedIndex);
            });
          }
        }
      });
    });
    function saveToTheDB(username) {
      ratedIndex++;
      $.ajax({
        url: "../php/rate-user.php",
        method: "POST",
        dataType: 'json',
        data: {
          save: 1,
          ratedIndex: ratedIndex,
          username: username
        },
        success: function (response) {
        }
      });
    }

    function setStars(max) {
      for (var i = 0; i <= max; i++)
        $('.fa-star:eq(' + i + ')').css('color', 'orange');
    }

    function resetStarColors() {
      $('.fa-star').css('color', 'white');
    }

    function disableRating() {
      $('.fa-star').off('click');
      $('.fa-star').off('mouseover');
      $('.fa-star').off('mouseleave');
    }

    function showConfirmButton() {
      confirmRatingBtn.show();
    }





    var username = getusernameFromUrl();

    $(document).ready(function () {
      function updateButtonVisibility(status) {
        if (status === 'following') {
          $('#followBtnContainer').hide();
          $('#unfollowBtnContainer').show();
        } else {
          $('#followBtnContainer').show();
          $('#unfollowBtnContainer').hide();
        }
      }
      $.ajax({
        type: 'POST',
        url: '../php/check-user-following.php',
        dataType: 'json',
        data: { username: username },
        success: function (response) {
          if (response.status === 'following' || response.status === 'not_following') {
            updateButtonVisibility(response.status);
          } else {
            $('#message').text('Error: Invalid response from server.');
          }
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          $('#message').text('Error: ' + error);
        }
      });


      // Follow button click event
      $('#followBtn').click(function () {
        var username = getusernameFromUrl();

        $.ajax({
          type: 'POST',
          url: '../php/add_user_follower.php',
          data: { username: username },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              updateButtonVisibility('following');
              $('#message').text(response.message);
            } else {
              $('#message').text(response.message);
            }
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            $('#message').text('Error: ' + error);
          }
        });
      });

      // Unfollow button click event
      $('#unfollowBtn').click(function () {
        var username = getusernameFromUrl();

        $.ajax({
          type: 'POST',
          url: '../php/remove_user_follower.php',
          data: { username: username },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              updateButtonVisibility('not_following');
              $('#message').text(response.message);
            } else {
              $('#message').text(response.message);
            }
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            $('#message').text('Error: ' + error);
          }
        });
      });
    });

    $(document).ready(function () {
      $('#reportBtn').click(function () {
        $.ajax({
          type: 'POST',
          url: '../php/report_user.php',
          data: { username: username },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              $('#message').text(response.message);
            } else {
              $('#message').text(response.message);
            }
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            $('#message').text('Error: ' + error);
          }
        });
      });
    });
  </script>
  </body>

</html>