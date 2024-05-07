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
$recipe_details_sql = "SELECT * FROM recipe WHERE RecipeID = ?";
$recipe_details_stmt = mysqli_prepare($conn, $recipe_details_sql);
mysqli_stmt_bind_param($recipe_details_stmt, 'i', $recipeId);
mysqli_stmt_execute($recipe_details_stmt);
$recipe_details_res = mysqli_stmt_get_result($recipe_details_stmt);

$ingredient_detailes_sql = "SELECT * FROM ingredient WHERE RecipeID = ?";
$ingredient_details_stmt = mysqli_prepare($conn, $ingredient_detailes_sql);
mysqli_stmt_bind_param($ingredient_details_stmt, 'i', $recipeId);
mysqli_stmt_execute($ingredient_details_stmt);
$ingredient_details_res = mysqli_stmt_get_result($ingredient_details_stmt);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Page</title>
    <link rel="stylesheet" href="../css/recipe-view.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Merienda:wght@300&family=Poetsen+One&family=Roboto+Slab&display=swap"
        rel="stylesheet">
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
                    // Fetch the first row (assuming there's only one row for the given recipe ID)
                    $recipe_details_row = mysqli_fetch_assoc($recipe_details_res);

                    // Display the recipe title
                    echo "<div class = 'title'>";
                    echo "<h1>" . $recipe_details_row['title'] . "</h1>";
                    echo "</div>";

                    // Add the rest of your HTML markup here...
                } else {
                    echo "Recipe not found.";
                }
                ?>
                <div class="info-container">


                    <div class="ingredients">
                        <hr>
                        <div class="times">
                            cooking time
                        </div>
                        <hr>
                        <h2>Ingredients:</h2>
                        <?php
                        // Loop through the ingredient details result set
                        while ($row = mysqli_fetch_assoc($ingredient_details_res)) {
                            // Extract ingredient details
                            $ingredientName = $row['IngredientName'];
                            $quantity = $row['Quantity'];
                            $unit = $row['Unit'];

                            // Display ingredient with checkbox
                            echo '<label>';
                            echo '<input type="checkbox" name="ingredients[]" value="' . htmlspecialchars($ingredientName) . '">'; // Use htmlspecialchars to prevent XSS attacks
                            echo $quantity . ' ' . $unit . ' ' . $ingredientName;
                            echo '</label>';
                            echo '<br>';
                        }
                        ?>
                        <br><br>
                        <hr>
                    </div>
                    <img src="" alt="Recipe Image">
                    <p>Instructions:</p>
                    <ol>
                        <li>Step 1</li>
                        <li>Step 2</li>
                        <!-- Add more steps as needed -->
                    </ol>

                    <p>description</p>
                </div>
            </div>
        </div>
        <div class="creator-info">
            <h2>Creator Information</h2>
            <!-- Creator information and buttons -->
            <p>Creator Name</p>
            <button id="followBtn">Follow</button>
            <div class="rating">
                <!-- Dynamic star rating -->
                <p>Rating:</p>
                <div class="stars">
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                </div>

            </div>
            <button id="saveBtn">Save</button>
            <!-- Comment section -->
            <div class="comments">
                <h3>Comments</h3>
                <div class="comment">
                    <p>User123: This recipe is amazing!</p>
                </div>
                <!-- Add more comments dynamically -->
            </div>
            <!-- Add a form for adding new comments if needed -->
        </div>
    </div>

    <script>
        // script.js

        document.getElementById('saveBtn').addEventListener('click', function () {
            this.classList.toggle('saved');
            if (this.classList.contains('saved')) {
                this.textContent = 'Saved';
            } else {
                this.textContent = 'Save';
            }
        });

        document.getElementById('followBtn').addEventListener('click', function () {
            this.classList.toggle('followed');
            if (this.classList.contains('followed')) {
                this.textContent = 'Following';
            } else {
                this.textContent = 'Follow';
            }
        });
        // script.js

        // Function to handle star rating
        function handleRatingClick(event) {
            if (event.target.classList.contains('star')) {
                const stars = document.querySelectorAll('.star');
                const clickedStarIndex = Array.from(stars).indexOf(event.target) + 1;

                // Highlight clicked star and unhighlight others
                stars.forEach((star, index) => {
                    if (index < clickedStarIndex) {
                        star.classList.add('rated');
                    } else {
                        star.classList.remove('rated');
                    }
                });
            }
        }

        // Event listener for rating stars
        document.querySelector('.stars').addEventListener('click', handleRatingClick);

        document.addEventListener('DOMContentLoaded', function () {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    var label = this.parentElement;
                    label.classList.toggle('completed');
                });
            });
        });

    </script>
</body>

</html>