<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}
require_once 'database.php';
$tag_name = $_POST['tag'];

$query = "SELECT 
            RecipeID
       FROM 
            tag
       WHERE 
            Tag_name LIKE ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $tag_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$TagResults = array();
while ($row = mysqli_fetch_assoc($result)) {
    $TagResults[] = $row['RecipeID'];
}


$query2 = "SELECT 
            r.RecipeID,
            r.title,
            ri.thumbnail
        FROM 
            recipe r
        LEFT JOIN 
            (SELECT RecipeID, MIN(thumbnail) AS thumbnail FROM recipe_images GROUP BY RecipeID) ri ON r.RecipeID = ri.RecipeID
        WHERE 
            r.RecipeID IN (" . implode(',', array_fill(0, count($TagResults), '?')) . ")";
$stmt2 = mysqli_prepare($conn, $query2);
$params = $TagResults;
mysqli_stmt_bind_param($stmt2, str_repeat('i', count($TagResults)), ...$params);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);


$query3 = "SELECT 
            r.RecipeID,
            r.title,
            ri.thumbnail
        FROM 
            recipe r
        LEFT JOIN 
            (SELECT RecipeID, MIN(thumbnail) AS thumbnail FROM recipe_images GROUP BY RecipeID) ri ON r.RecipeID = ri.RecipeID
        WHERE 
            r.RecipeID IN (" . implode(',', array_fill(0, count($TagResults), '?')) . ")";
$stmt3 = mysqli_prepare($conn, $query3);
$params = $TagResults;
mysqli_stmt_bind_param($stmt3, str_repeat('i', count($TagResults)), ...$params);
mysqli_stmt_execute($stmt3);
$result3 = mysqli_stmt_get_result($stmt3);


$searchResults = array();
while ($row = mysqli_fetch_assoc($result3)) {
    $searchResults[] = $row['RecipeID'];
}
$_SESSION['searchResults'] = $searchResults;

if (mysqli_num_rows($result2) > 0) {
    // Open the product container
    echo '<section class = "product">';
    echo '<div class="product-container">';

    // Loop through the results and display each recipe
    while ($row = mysqli_fetch_assoc($result2)) {
        $recipeID = $row['RecipeID'];
        $title = $row['title'];
        $thumbnail = $row['thumbnail'];

        // Output HTML for the recipe card
        ?>
        <div class="product-card">
            <div class="product-image">
                <?php if ($thumbnail) { ?>
                    <img src="<?php echo $thumbnail; ?>" class="product-thumb" alt="Thumbnail" />
                    <a href="recipe-view.php?RecipeID=<?php echo $recipeID; ?>">
                        <button class="card-btn">View Recipe</button>
                    </a>
                <?php } ?>
            </div>
            <div class="product-info">
                <h2 class="product-brand"><?php echo $title; ?></h2>
            </div>
        </div>
        <?php
    }

    // Close the product container
    echo '</div>';
    echo '</section>';
} else {
    // If no recipes found, display a message
    echo "No recipes found.";
}

// Close the statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>