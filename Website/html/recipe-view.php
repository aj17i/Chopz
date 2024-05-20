<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
$mysqli = require_once '../php/database.php';
$recipeId = $_GET['RecipeID'];
$thumbnail_sql = "SELECT thumbnail FROM recipe_images WHERE RecipeID = ? LIMIT 1";
$thumbnail_stmt = mysqli_prepare($conn, $thumbnail_sql);
mysqli_stmt_bind_param($thumbnail_stmt, 'i', $recipeId);
mysqli_stmt_execute($thumbnail_stmt);
$thumbnail_res = mysqli_stmt_get_result($thumbnail_stmt);

// Prepare the images SQL query
$images_sql = "SELECT image FROM recipe_images WHERE RecipeID = ? ORDER BY imageNumber";
$images_stmt = mysqli_prepare($conn, $images_sql);
mysqli_stmt_bind_param($images_stmt, 'i', $recipeId);
mysqli_stmt_execute($images_stmt);
$images_res = mysqli_stmt_get_result($images_stmt);

// Prepare the recipe details SQL query
$recipe_details_sql = "SELECT title, Cuisine_name FROM recipe WHERE RecipeID = ?";
$recipe_details_stmt = mysqli_prepare($conn, $recipe_details_sql);
mysqli_stmt_bind_param($recipe_details_stmt, 'i', $recipeId);
mysqli_stmt_execute($recipe_details_stmt);
$recipe_details_res = mysqli_stmt_get_result($recipe_details_stmt);


$ingredient_detailes_sql = "SELECT * FROM ingredient WHERE RecipeID = ?";
$ingredient_details_stmt = mysqli_prepare($conn, $ingredient_detailes_sql);
mysqli_stmt_bind_param($ingredient_details_stmt, 'i', $recipeId);
mysqli_stmt_execute($ingredient_details_stmt);
$ingredient_details_res = mysqli_stmt_get_result($ingredient_details_stmt);

$instruction_detailes_sql = "SELECT * FROM instruction WHERE RecipeID = ? ORDER BY StepNumber";
$instruction_details_stmt = mysqli_prepare($conn, $instruction_detailes_sql);
mysqli_stmt_bind_param($instruction_details_stmt, 'i', $recipeId);
mysqli_stmt_execute($instruction_details_stmt);
$instruction_details_res = mysqli_stmt_get_result($instruction_details_stmt);


$recipe_times_sql = "SELECT prep_time, cooking_time, serving_size, skill_level FROM recipe WHERE RecipeID = ?";
$recipe_times_stmt = mysqli_prepare($conn, $recipe_times_sql);
mysqli_stmt_bind_param($recipe_times_stmt, 'i', $recipeId);
mysqli_stmt_execute($recipe_times_stmt);
$recipe_times_res = mysqli_stmt_get_result($recipe_times_stmt);

$recipe_inspo_sql = "SELECT inspo FROM recipe WHERE RecipeID = ?";
$recipe_inspo_stmt = mysqli_prepare($conn, $recipe_inspo_sql);
mysqli_stmt_bind_param($recipe_inspo_stmt, 'i', $recipeId);
mysqli_stmt_execute($recipe_inspo_stmt);
$recipe_inspo_res = mysqli_stmt_get_result($recipe_inspo_stmt);

$recipe_nutrition_sql = "SELECT calories, carbs, protein, fat FROM recipe WHERE RecipeID = ?";
$recipe_nutrition_stmt = mysqli_prepare($conn, $recipe_nutrition_sql);
mysqli_stmt_bind_param($recipe_nutrition_stmt, 'i', $recipeId);
mysqli_stmt_execute($recipe_nutrition_stmt);
$recipe_nutrition_res = mysqli_stmt_get_result($recipe_nutrition_stmt);

$recipeQuery_id = "SELECT UserID FROM recipe WHERE RecipeID = ?";
$userid_stmt = mysqli_prepare($conn, $recipeQuery_id);
mysqli_stmt_bind_param($userid_stmt, 'i', $recipeId);
mysqli_stmt_execute($userid_stmt);
$id_result = mysqli_stmt_get_result($userid_stmt);

$id_row = mysqli_fetch_assoc($id_result);
$followed_id = $id_row['UserID'];
echo "<script>var followedId = $followed_id;</script>";

$comment_display = "SELECT c.comment, u.username FROM comment c JOIN user u ON c.CommentingUserID = u.UserID WHERE c.RecipeID = ? ORDER BY c.date DESC";
$comment_stmt = mysqli_prepare($conn, $comment_display);
mysqli_stmt_bind_param($comment_stmt, 'i', $recipeId);
mysqli_stmt_execute($comment_stmt);
$comment_result = mysqli_stmt_get_result($comment_stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chopz | Recipe Page</title>
    <link rel="stylesheet" href="../css/recipe-view.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Merienda:wght@300&family=Poetsen+One&family=Roboto+Slab&display=swap"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/18ace14423.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <div class="navbar">
            <a href="Homepage.php" class="logo">
                <img src="../css/images/logo.png" alt="Logo" style="height: 70px; width: 150px" />
            </a>

            <a href="profile-page.php" class="profilePic">
                <img src="../css/images/user.png" alt="" />
            </a>
        </div>
    </header>

    <div class="container">
        <div class="recipe-display">
            <?php if (mysqli_num_rows($thumbnail_res) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($thumbnail_res)) { ?>
                    <div class="thumbnail">
                        <img src="<?= $row['thumbnail'] ?>" alt="Recipe thumbnail">
                    </div>
                <?php }
            } ?>
            <br>
            <div class="recipe">

                <?php
                if (mysqli_num_rows($recipe_details_res) > 0) {
                    $recipe_details_row = mysqli_fetch_assoc($recipe_details_res);

                    echo "<div class = 'title'>";
                    echo "<h1>" . $recipe_details_row['title'] . "       </h1>";
                    echo "<h3>-----Cuisine: " . $recipe_details_row['Cuisine_name'] . "</h3>";
                    echo "</div>";

                } else {
                    echo "Recipe not found.";
                }
                ?>
                <div class="info-container">


                    <div class="ingredients">
                        <hr>

                        <div class="times">
                            <?php
                            if (mysqli_num_rows($recipe_times_res) > 0) {

                                $recipe_times_row = mysqli_fetch_assoc($recipe_times_res);

                                echo "<span class='label'>Prep Time:</span> <span class='value'>" . $recipe_times_row['prep_time'] . "</span>";
                                echo "<span class='label'> Cooking Time:</span> <span class='value'>" . $recipe_times_row['cooking_time'] . "</span>";
                                echo "<span class='label'> Serving Size:</span> <span class='value'>" . $recipe_times_row['serving_size'] . "</span>";
                                echo "<span class='label'> Skill Level:</span> <span class='value'>" . $recipe_times_row['skill_level'] . "</span>";

                            } else {
                                echo "Recipe not found.";
                            }

                            mysqli_stmt_close($recipe_times_stmt);
                            ?>
                        </div>

                        <hr>
                        <h2>Ingredients:</h2>
                        <?php

                        while ($row = mysqli_fetch_assoc($ingredient_details_res)) {

                            $ingredientName = $row['IngredientName'];
                            $quantity = $row['Quantity'];
                            $unit = $row['Unit'];

                            echo '<label>';
                            echo '<input type="checkbox" name="ingredients[]" value=""' . htmlspecialchars($ingredientName) . '">';
                            echo '<span class="ingredient-text">' . $quantity . ' ' . $unit . ' ' . $ingredientName . '</span>';
                            echo '</label>';
                            echo '<br>';
                        }
                        ?>
                        <br><br>

                    </div>
                    <hr>
                    <div class="carousel-container">
                        <section class="product">
                            <button class="pre-btn"><img src="images/arrow.png" alt="" /></button>
                            <button class="nxt-btn"><img src="images/arrow.png" alt="" /></button>
                            <div class="product-container">
                                <?php

                                $images_sql = "SELECT image FROM recipe_images WHERE RecipeID = ? ORDER BY imageNumber";
                                $images_stmt = mysqli_prepare($conn, $images_sql);
                                mysqli_stmt_bind_param($images_stmt, 'i', $recipeId);
                                mysqli_stmt_execute($images_stmt);
                                $images_res = mysqli_stmt_get_result($images_stmt);

                                while ($imageRow = mysqli_fetch_assoc($images_res)) {
                                    if ($imageRow) {
                                        ?>
                                        <div class="product-card">
                                            <div class="product-image">
                                                <img src="<?php echo $imageRow['image']; ?>" class="product-thumb" alt="" />

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
                    <hr>
                    <h2>Instructions:</h2>
                    <div class="instructions">
                        <ol>
                            <?php
                            while ($instruction_row = mysqli_fetch_assoc($instruction_details_res)) {
                                $intruction = $instruction_row['InstructionText'];
                                echo "<li>";
                                echo $intruction;
                                echo "</li>";
                            }
                            ?>
                        </ol>
                    </div>
                    <hr>
                    <div class=times>
                        <?php
                        if (mysqli_num_rows($recipe_nutrition_res) > 0) {
                            $recipe_nutrition_row = mysqli_fetch_assoc($recipe_nutrition_res);

                            echo "<span class='label'>Calories:</span> <span class='value'>" . $recipe_nutrition_row['calories'] . "g</span>";
                            echo "<span class='label'> Carbs:</span> <span class='value'>" . $recipe_nutrition_row['carbs'] . "g</span>";
                            echo "<span class='label'> Protein:</span> <span class='value'>" . $recipe_nutrition_row['protein'] . "g</span>";
                            echo "<span class='label'> Fat:</span> <span class='value'>" . $recipe_nutrition_row['fat'] . "g</span>";

                        } else {
                            echo "Recipe not found.";
                        }
                        mysqli_stmt_close($recipe_nutrition_stmt);
                        ?>
                    </div>

                    <hr>
                    <h2>FAQ, Inspo and more:</h2>
                    <div class="inspo">
                        <?php
                        if (mysqli_num_rows($recipe_inspo_res) > 0) {
                            $recipe_inspo_row = mysqli_fetch_assoc($recipe_inspo_res);

                            echo "<p>" . $recipe_inspo_row['inspo'] . "</p>";


                        } else {
                            echo "Recipe not found.";
                        }
                        mysqli_stmt_close($recipe_inspo_stmt);
                        ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="creator-info">
            <h2>Welcome to our food blog!</h2>
            <hr>
            <div class="recipe-rating">
                <?php $result = mysqli_query($conn, "SELECT ROUND(AVG(rating), 2) AS average_rating FROM ratings WHERE RecipeID = $recipeId");
                $row = mysqli_fetch_assoc($result);
                $average_rating = $row['average_rating']; ?>
                <h2>Rating:</h2><img src="../css/images/star.png" alt=""><?= $row['average_rating']; ?>
            </div>
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
            <div class="profile-image">
                <?php
                if ($id_row) {
                    $userId = $id_row['UserID'];

                    $user_profile_Query = "SELECT * FROM user WHERE UserID = ?";
                    $profile_stmt = mysqli_prepare($conn, $user_profile_Query);
                    mysqli_stmt_bind_param($profile_stmt, 'i', $userId);
                    mysqli_stmt_execute($profile_stmt);
                    $user_profile_Result = mysqli_stmt_get_result($profile_stmt);


                    if ($user_profile_Row = mysqli_fetch_assoc($user_profile_Result)) {

                        echo "<img src='../css/images/" . $user_profile_Row['profilePic'] . "' alt=''>";
                        echo "</div>";
                        echo "<a href='#' class = 'usernamelink'><h3>" . $user_profile_Row['username'] . "</h3></a>";
                        echo "<hr>";
                    }
                    $recipe_description_Query = "SELECT description FROM recipe WHERE RecipeID = ?";
                    $description_stmt = mysqli_prepare($conn, $recipe_description_Query);
                    mysqli_stmt_bind_param($description_stmt, 'i', $recipeId);
                    mysqli_stmt_execute($description_stmt);
                    $recipe_description_Result = mysqli_stmt_get_result($description_stmt);

                    if ($recipe_description_Row = mysqli_fetch_assoc($recipe_description_Result)) {
                        echo "<p>" . $recipe_description_Row['description'] . "</p>";
                        echo "<hr>";
                    }
                }
                ?>

                <div class="follow-container">
                    <div class="follow" id="followBtnContainer" style="display: none;">
                        <img src="../css/images/add-user.png" alt="">
                        <button id="followBtn">Follow</button>
                    </div>
                    <div class="unfollow" id="unfollowBtnContainer" style="display: none;">
                        <img src="../css/images/remove-user.png" alt="">
                        <button id="unfollowBtn">Unfollow</button>
                    </div>
                    <div id="message"></div>
                </div>
                <br>
                <div class="save-container">
                    <div class="save" id="saveBtnContainer" style="display: none;">
                        <img src="../css/images/save.png" alt="">
                        <button id="saveBtn">Save</button>
                    </div>
                    <div class="unsave" id="unsaveBtnContainer" style="display: none;">
                        <img src="../css/images/unsave.png" alt="">
                        <button id="unsaveBtn">Unsave</button>
                    </div>
                    <div id="message2"></div> <!-- Container for displaying messages -->
                </div>
                <br>
                <div>
                    <button id="shareButton">Share this page</button>
                    <div id="overlay"></div>

                    <div id="popup">
                        <p>Copy this link:</p>
                        <input type="text" id="pageUrl" readonly>
                        <button id="closeButton">Close</button>
                    </div>
                </div>

                <hr>
                <div class="comment-section">
                    <h2>Comment Section:</h2>
                    <form id="comment-form" action="../php/manage-comments.php" method="post">
                        <label for="comment">Comment</label>
                        <input type="text" name="comment" id="comment" placeholder="Any Opinions?!"><br>
                        <input type="submit" name="submit" value="submit">
                    </form>
                    <div id="comments-container" class="comments-container">
                        <?php while ($comment_row = mysqli_fetch_assoc($comment_result)) {

                            echo '<div class="comment">';
                            echo '<p class="username">' . $comment_row['username'] . '</p>';
                            echo '<p>' . $comment_row['comment'] . '</p>';
                            echo '<hr>';
                            echo '</div>';

                        } ?>
                    </div>
                </div>
                <hr>
                <div class="tags-content">
                    <h2>Tags</h2>
                    <table>
                        <?php
                        $tags_sql = "SELECT * FROM tag WHERE RecipeID = ?";
                        $tags_stmt = mysqli_prepare($conn, $tags_sql);
                        mysqli_stmt_bind_param($tags_stmt, 'i', $recipeId);
                        mysqli_stmt_execute($tags_stmt);
                        $tags_res = mysqli_stmt_get_result($tags_stmt);
                        while ($tag_row = mysqli_fetch_assoc($tags_res)) {
                            $tag = $tag_row['Tag_name'];
                            echo "<tr> <td>" . $tag . "</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        document.getElementById('shareButton').addEventListener('click', function () {
            var popup = document.getElementById('popup');
            var overlay = document.getElementById('overlay');
            var pageUrlInput = document.getElementById('pageUrl');

            pageUrlInput.value = window.location.href;

            popup.style.display = 'block';
            overlay.style.display = 'block';
        });

        document.getElementById('closeButton').addEventListener('click', function () {
            var popup = document.getElementById('popup');
            var overlay = document.getElementById('overlay');

            popup.style.display = 'none';
            overlay.style.display = 'none';
        });

        document.getElementById('overlay').addEventListener('click', function () {
            var popup = document.getElementById('popup');
            var overlay = document.getElementById('overlay');

            popup.style.display = 'none';
            overlay.style.display = 'none';
        });

        function getRecipeIdFromUrl() {
            var urlParams = new URLSearchParams(window.location.search);
            var recipeId = urlParams.get('RecipeID');
            return recipeId;
        }

        $(document).ready(function () {
            var recipeId = getRecipeIdFromUrl();

            function updateButtonVisibility(status) {
                if (status === 'saved') {
                    $('#saveBtnContainer').hide();
                    $('#unsaveBtnContainer').show();
                } else {
                    $('#saveBtnContainer').show();
                    $('#unsaveBtnContainer').hide();
                }
            }
            $.ajax({
                type: 'POST',
                url: '../php/check-saving.php',
                data: { recipe_id: recipeId },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'saved' || response.status === 'not_saved') {
                        updateButtonVisibility(response.status);
                    } else {
                        $('#message2').text('Error: Invalid response from server.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#message2').text('Error: ' + error);
                }
            });

            // Save button click event
            $('#saveBtn').click(function () {
                $.ajax({
                    type: 'POST',
                    url: '../php/save_recipe.php',
                    data: { recipe_id: recipeId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            updateButtonVisibility('saved');
                            $('#message2').text(response.message);
                        } else {
                            $('#message2').text(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#message2').text('Error: ' + error);
                    }
                });
            });

            // Unsave button click event
            $('#unsaveBtn').click(function () {
                $.ajax({
                    type: 'POST',
                    url: '../php/unsave_recipe.php',
                    data: { recipe_id: recipeId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            updateButtonVisibility('not_saved');
                            $('#message2').text(response.message);
                        } else {
                            $('#message2').text(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#message2').text('Error: ' + error);
                    }
                });
            });
        });



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
                url: '../php/check-following.php',
                dataType: 'json',
                data: { profile_id: followedId },
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
                var profileId = followedId;

                $.ajax({
                    type: 'POST',
                    url: '../php/add_follower.php',
                    data: { profile_id: profileId },
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
                var profileId = followedId;

                $.ajax({
                    type: 'POST',
                    url: '../php/remove_follower.php',
                    data: { profile_id: profileId },
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


        var ratedIndex = -1;
        var recipeId = getRecipeIdFromUrl();
        var confirmRatingBtn = $('#confirmRating');

        $(document).ready(function () {
            resetStarColors();
            $.ajax({
                url: "../php/check-rate.php",
                method: "POST",
                dataType: 'json',
                data: {
                    check: 1,
                    recipe_id: recipeId
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
                            saveToTheDB(recipeId);
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

        function saveToTheDB(recipeId) {
            ratedIndex++;
            $.ajax({
                url: "../php/rate-recipe.php",
                method: "POST",
                dataType: 'json',
                data: {
                    save: 1,
                    ratedIndex: ratedIndex,
                    recipe_id: recipeId
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

        $(document).ready(function () {

            $('#comment-form').submit(function (event) {
                event.preventDefault();
                var recipeId = getRecipeIdFromUrl();
                $.ajax({
                    type: 'POST',
                    url: '../php/manage-comments.php',
                    data: $(this).serialize() + '&recipe_id=' + recipeId,
                    success: function (response) {
                        // Clear input field
                        $('#comment').val('');
                        // Append new comment to comments container
                        $('#comments-container').append(response);
                    }
                });
            });
        });
        $(document).ready(function () {
            $('#comment-form').submit(function () {
                location.reload();
            });
        });

        $(document).ready(function () {
            $('.usernamelink').click(function (e) {
                e.preventDefault();
                var recipeId = getRecipeIdFromUrl();

                $.ajax({
                    type: 'POST',
                    url: '../php/check-user-recipe.php',
                    data: { recipeId: recipeId },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === "match") {
                            window.location.href = "profile-page.php";
                        } else if (response.status === "no-match") {
                            window.location.href = "view-user-profile.php?username=" + encodeURIComponent(response.username);
                        } else {
                            alert(response.message || 'An unknown error occurred.');
                        }
                    },
                    error: function () {
                        alert('An error occurred while processing your request.');
                    }
                });
            });
        });
    </script>
</body>

</html>