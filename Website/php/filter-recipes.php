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


$params = array();
if (!empty($searchResults)) {
    $whereConditions[] = "r.RecipeID IN (" . implode(',', $searchResults) . ")";
}

if (isset($_POST['quickFilter']) && !empty($_POST['quickFilter'])) {
    $tags = explode(",", $_POST['quickFilter']);
    $tagPlaceholders = array_fill(0, count($tags), "?");
    $whereConditions[] = "r.RecipeID IN (
        SELECT RecipeID FROM tag WHERE LOWER(Tag_name) IN (" . implode(",", $tagPlaceholders) . ")
    )";

    foreach ($tags as $tag) {
        $params[] = strtolower(trim($tag));
    }
}

if (isset($_POST['skillLevel']) && !empty($_POST['skillLevel'])) {
    $whereConditions[] = "r.skill_level = ?";
    $params[] = $_POST['skillLevel'];
}

if (isset($_POST['cuisine']) && !empty($_POST['cuisine'])) {
    $whereConditions[] = "r.Cuisine_name = ?";
    $params[] = $_POST['cuisine'];
}

if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

$stmt = mysqli_prepare($conn, $query);

if (!empty($params)) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<section class = "product">';
    echo '<div class="product-container">';

    while ($row = mysqli_fetch_assoc($result)) {
        $recipeID = $row['RecipeID'];
        $title = $row['title'];
        $thumbnail = $row['thumbnail'];


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
    echo '</section>';
} else {

    echo "No recipes found.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>