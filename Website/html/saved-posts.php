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
        <h2>Posted Recipe</h2>
        <a href="profile-page.php" class="back-btn">Back</a>
    </div>
    <div class="second-row-favourites">
        <div class="carousel-container">
            <section class="product">
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
                                        <img src="<?php echo $thumbnailRow['thumbnail']; ?>" class="product-thumb"
                                            alt="Thumbnail" />
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
</body>

</html>