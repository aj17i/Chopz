<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
  header("Location: ../html/loginpage.php");
  exit();
}
$mysqli = require_once '../php/database.php';

$recipeName = $_POST['recipeName'];
$searchTerm = "%" . $recipeName . "%";

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

$query2 = "SELECT 
            r.RecipeID,
            r.title,
            ri.thumbnail
          FROM 
            recipe r
          LEFT JOIN 
            (SELECT RecipeID, MIN(thumbnail) AS thumbnail FROM recipe_images GROUP BY RecipeID) ri ON r.RecipeID = ri.RecipeID
          WHERE 
            r.title LIKE ?";


$stmt2 = mysqli_prepare($conn, $query2);
mysqli_stmt_bind_param($stmt2, "s", $searchTerm);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);


$searchResults = array();
while ($row = mysqli_fetch_assoc($result2)) {
  $searchResults[] = $row['RecipeID'];
}
$_SESSION['searchResults'] = $searchResults;

if (mysqli_num_rows($result) > 0) {

  echo '<section class = "product">';
  echo '<div class="product-container">';

  while ($row = mysqli_fetch_assoc($result)) {
    $recipeID = $row['RecipeID'];
    $title = $row['title'];
    $thumbnail = $row['thumbnail'];

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

  echo '</div>';
  echo '</section>';
} else {
  echo "No recipes found.";
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>