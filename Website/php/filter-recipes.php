<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
require_once 'database.php';

$searchResults = isset($_SESSION['searchResults']) ? $_SESSION['searchResults'] : array();

$quickFilter = $_POST['quickFilter'] ?? null;
$skillLevel = $_POST['skillLevel'] ?? null;
$cuisine = $_POST['cuisine'] ?? null;

$query = "SELECT r.RecipeID, r.title, ri.thumbnail 
          FROM recipe r 
          LEFT JOIN (SELECT RecipeID, MIN(thumbnail) AS thumbnail FROM recipe_images GROUP BY RecipeID) ri 
          ON r.RecipeID = ri.RecipeID ";

$whereConditions = array();

// Initialize an array to hold the parameters for binding
$params = array();
if (!empty($searchResults)) {
    // Add condition to filter by RecipeID
    $whereConditions[] = "r.RecipeID IN (" . implode(',', $searchResults) . ")";
}

// Check if quick filter (tags) is provided
if (isset($_POST['quickFilter']) && !empty($_POST['quickFilter'])) {
    // Split the quick filter tags into an array
    $tags = explode(",", $_POST['quickFilter']);

    // Prepare placeholders for tag parameters
    $tagPlaceholders = array_fill(0, count($tags), "?");

    // Construct the WHERE condition for tags
    $whereConditions[] = "r.RecipeID IN (
        SELECT RecipeID FROM tag WHERE LOWER(Tag_name) IN (" . implode(",", $tagPlaceholders) . ")
    )";

    // Bind tag parameters
    foreach ($tags as $tag) {
        $params[] = strtolower(trim($tag)); // Assuming tag names are stored in lowercase
    }
}

// Check if skill level filter is provided
if (isset($_POST['skillLevel']) && !empty($_POST['skillLevel'])) {
    $whereConditions[] = "r.skill_level = ?";
    $params[] = $_POST['skillLevel'];
}

// Check if cuisine filter is provided
if (isset($_POST['cuisine']) && !empty($_POST['cuisine'])) {
    // Assuming cuisine is not provided along with quick filter
    $whereConditions[] = "r.Cuisine_name = ?";
    $params[] = $_POST['cuisine'];
}

// If there are any WHERE conditions, append them to the query
if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

// Prepare the statement
$stmt = mysqli_prepare($conn, $query);


// Bind parameters
if (!empty($params)) {
    // Generate type string for binding parameters dynamically
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

// Execute the query
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
// Check if there are any recipes found
if (mysqli_num_rows($result) > 0) {
    echo '<div class="product-container">';
    // Loop through the results and display each recipe
    while ($row = mysqli_fetch_assoc($result)) {
        $recipeID = $row['RecipeID'];
        $title = $row['title'];
        $thumbnail = $row['thumbnail'];

        // Output HTML for the recipe card
        ?>
        <section class="product">
            <div class="product-container">
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
            </div>
        </section>
        <?php
    }
    echo '</div>';
} else {
    // If no recipes found, display a message
    echo "No recipes found.";
}

// Close the statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>