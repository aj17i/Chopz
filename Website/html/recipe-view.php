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
$recipe_details_sql = "SELECT title FROM recipe WHERE RecipeID = ?";
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
                            <?php
                            if (mysqli_num_rows($recipe_times_res) > 0) {
                                // Fetch the first row
                                $recipe_times_row = mysqli_fetch_assoc($recipe_times_res);

                                // Display the recipe details
                            
                                echo "<span class='label'>Prep Time:</span> <span class='value'>" . $recipe_times_row['prep_time'] . "</span>";
                                echo "<span class='label'> Cooking Time:</span> <span class='value'>" . $recipe_times_row['cooking_time'] . "</span>";
                                echo "<span class='label'> Serving Size:</span> <span class='value'>" . $recipe_times_row['serving_size'] . "</span>";
                                echo "<span class='label'> Skill Level:</span> <span class='value'>" . $recipe_times_row['skill_level'] . "</span>";

                            } else {
                                echo "Recipe not found.";
                            }

                            // Close the statement
                            mysqli_stmt_close($recipe_details_stmt);
                            ?>
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

                    </div>
                    <hr>
                    <div class="carousel-container">
                        <section class="product">
                            <button class="pre-btn"><img src="images/arrow.png" alt="" /></button>
                            <button class="nxt-btn"><img src="images/arrow.png" alt="" /></button>
                            <div class="product-container">
                                <?php
                                // Assuming you have already connected to your database
                                

                                // First query to retrieve titles and recipe IDs
                                $images_sql = "SELECT image FROM recipe_images WHERE RecipeID = ? ORDER BY imageNumber";
                                $images_stmt = mysqli_prepare($conn, $images_sql);
                                mysqli_stmt_bind_param($images_stmt, 'i', $recipeId);
                                mysqli_stmt_execute($images_stmt);
                                $images_res = mysqli_stmt_get_result($images_stmt);

                                // Loop through each title and recipe ID
                                while ($imageRow = mysqli_fetch_assoc($images_res)) {


                                    // Second query to retrieve thumbnails using the recipe ID
                                

                                    // Check if thumbnail exists
                                    if ($imageRow) {
                                        ?>
                                        <div class="product-card">
                                            <div class="product-image">
                                                <img src="<?php echo $imageRow['image']; ?>" class="product-thumb" alt="" />

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
                            // Fetch the first row
                            $recipe_nutrition_row = mysqli_fetch_assoc($recipe_nutrition_res);

                            // Display the recipe details
                        
                            echo "<span class='label'>Calories:</span> <span class='value'>" . $recipe_nutrition_row['calories'] . "g</span>";
                            echo "<span class='label'> Carbs:</span> <span class='value'>" . $recipe_nutrition_row['carbs'] . "g</span>";
                            echo "<span class='label'> Protein:</span> <span class='value'>" . $recipe_nutrition_row['protein'] . "g</span>";
                            echo "<span class='label'> Fat:</span> <span class='value'>" . $recipe_nutrition_row['fat'] . "g</span>";

                        } else {
                            echo "Recipe not found.";
                        }

                        // Close the statement
                        mysqli_stmt_close($recipe_nutrition_stmt);
                        ?>
                    </div>

                    <hr>
                    <h2>FAQ, Inspo and more:</h2>
                    <div class="inspo">
                        <?php
                        if (mysqli_num_rows($recipe_inspo_res) > 0) {
                            // Fetch the first row
                            $recipe_inspo_row = mysqli_fetch_assoc($recipe_inspo_res);

                            echo "<p>" . $recipe_inspo_row['inspo'] . "</p>";


                        } else {
                            echo "Recipe not found.";
                        }

                        // Close the statement
                        mysqli_stmt_close($recipe_inspo_stmt);
                        ?>

                    </div>
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