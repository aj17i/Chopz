<?php
// Start the session and ensure user is logged in
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}

// Require the database connection
$mysqli = require_once '../php/database.php';

// Get the search term from the POST data
$recipeName = $_POST['recipeName'];
$searchTerm = "%" . $recipeName . "%";

// Prepare the SQL query to fetch recipes matching the search term
$query = "SELECT 
            r.RecipeID,
            r.title,
            ri.thumbnail
          FROM 
            recipe r
          LEFT JOIN 
            (SELECT RecipeID, MIN(thumbnail) AS thumbnail FROM recipe_images GROUP BY RecipeID) ri ON r.RecipeID = ri.RecipeID
          WHERE 
            r.title LIKE ?";


$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if there are any recipes found
if (mysqli_num_rows($result) > 0) {
    // Open the product container
    echo '<div class="product-container">';

    // Loop through the results and display each recipe
    while ($row = mysqli_fetch_assoc($result)) {
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
} else {
    // If no recipes found, display a message
    echo "No recipes found.";
}

// Close the statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>