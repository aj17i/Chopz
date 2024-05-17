<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
require_once '../php/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chopz | Posted Recipes</title>
    <link rel="stylesheet" href="../css/posted-page.css" />
</head>

<body>
    <div class="header">
        <h2>Posted Recipes</h2>
        <a href="profile-page.php" class="back-btn">Back</a>
    </div>
    <div class="third-row-recent">
        <div class="carousel-container">
            <section class="product">
                <div class="product-container">
                    <?php
                    $userId = $_SESSION["UserID"];

                    $titleQuery = "SELECT RecipeID, title 
              FROM recipe 
              WHERE UserID = $userId 
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
    <script>
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