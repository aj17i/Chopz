<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
  header("Location: loginpage.php");
  exit();
}
$mysqli = require_once '../php/database.php';
$tags_sql = "SELECT LOWER(Tag_name) AS tag_name, COUNT(*) AS tag_count
             FROM tag
             GROUP BY LOWER(Tag_name)
             ORDER BY tag_count DESC
             LIMIT 15";
$tags_stmt = mysqli_prepare($conn, $tags_sql);
mysqli_stmt_execute($tags_stmt);
$tags_res = mysqli_stmt_get_result($tags_stmt);

$cuisines_sql = "SELECT Cuisine_name, COUNT(*) AS cuisine_count
                 FROM recipe
                 GROUP BY Cuisine_name
                 ORDER BY cuisine_count DESC
                 LIMIT 9";
$cuisines_stmt = mysqli_prepare($conn, $cuisines_sql);
mysqli_stmt_execute($cuisines_stmt);
$cuisines_res = mysqli_stmt_get_result($cuisines_stmt);

$query = "SELECT Cuisine_name FROM cuisines";
$result = $mysqli->query($query);
$cuisineOptions = '';
while ($row = $result->fetch_assoc()) {
  $cuisineOptions .= '<option value="' . $row['Cuisine_name'] . '">' . $row['Cuisine_name'] . '</option>';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chopz | Homepage</title>
  <link rel="stylesheet" href="../css/homepage.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Briem+Hand:wght@100..900&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Merienda:wght@300&family=Poetsen+One&family=Roboto+Slab&display=swap"
    rel="stylesheet">
</head>

<body>
  <div class="background-slideshow"></div>
  <header>
    <div class="navbar">
      <div class="background"></div>
      <a href="Homepage.php" class="logo">
        <img src="../css/images/logo.png" alt="Logo" style="height: 70px; width: 150px" />
      </a>
      <div class="dropdown">
        <button class="dropbtn">Search by...</button>
        <div class="dropdown-content">
          <a href="#" onclick="showSearchBar('recipe')">Recipe Name</a>
          <a href="#" onclick="showSearchBar('cuisine')">Cuisine</a>
          <a href="#" onclick="showSearchBar('tag')">Tag</a>
          <a href="#" onclick="showSearchBar('chef')">Chef/Creator</a>
        </div>
        <div id="searchBars">
          <div id="recipeSearch" class="searchBar">
            <form id="chefSearchForm">
              <input type="text" placeholder="Search by Recipe Name" id="recipeNameInput" name="recipeName" />
              <button type="button" onclick="searchRecipe()">Search</button>
            </form>
          </div>
          <div id="tagSearch" class="searchBar">
            <input type="text" placeholder="Search by Tag" />
            <button onclick="searchTag()">Search</button>
          </div>
          <div id="cuisineSearch" class="searchBar">
            <input type="text" placeholder="Search by Cuisine" />
            <button onclick="searchCuisine()">Search</button>
          </div>
          <div id="chefSearch" class="searchBar">
            <input type="text" placeholder="Search by Chef Name" />
            <button onclick="searchChef()">Search</button>
          </div>
        </div>
      </div>
      <a href="profile-page.php" class="profilePic">
        <img src="../css/images/user.png" alt="" />
      </a>
    </div>
  </header>

  <!-- -------------------------------------body--------------------------------------  -->
  <div class="container">
    <div class="side-panel">
      <div class="panel-content">
        <h2>quick filter...<img src="../css/images/filter.png" alt=""></h2>
        <select name="quick-filter" id="quick-filter">
          <option value="">-- Select Quick Filter --</option>
          <option value="Dairy free">Dairy free</option>
          <option value="Nut Free">Nut Free</option>
          <option value="Gluten Free">Gluten Free</option>
          <option value="Keto friendly">Keto friendly</option>
          <option value="Vegetarian">Vegetarian</option>
          <option value="Vegan">Vegan</option>
          <option value="Allergy friendly">Allergy friendly</option>
          <option value="sugar free">Sugar Free</option>
          <option value="Paleo">Paleo</option>
          <option value="Low-carb">Low-carb</option>
        </select>
        <hr>
        <h2>Skill level:.. <img src="../css/images/chart.png" alt=""></h2>
        <select name="skill-level" id="skill-level">
          <option value="">-- Select Skill Level --</option>
          <option value="Begginer">Begginer</option>
          <option value="Home Cook">Home Cook</option>
          <option value="Chef">Chef</option>
          <option value="Expert">Expert</option>
        </select>
        <hr>
        <h2>Cuisine:... <img src="../css/images/cooking (1).png" alt=""></h2>
        <select name="cuisine" id="cuisine">
          <option value="">-- Select Cuisine --</option>
          <?php echo $cuisineOptions; ?>
        </select>
        <hr>
        <h2>Popular tags:</h2>
        <div class="popular-tags">
          <?php
          while ($tag_row = mysqli_fetch_assoc($tags_res)) {
            $tag = $tag_row['tag_name'];
            echo "<button>" . $tag . "</button>";
          }
          ?>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="first-row">
        <div>
          <h2><img src="../css/images/cooking (1).png" alt=""> ..Popular Cuisines:.. <img src="../css/images/ramen.png"
              alt=""></h2>
        </div>
        <div class="cuisines-line">
          <?php
          while ($cuisine_row = mysqli_fetch_assoc($cuisines_res)) {
            $cuisine = $cuisine_row['Cuisine_name'];
            echo "<button>" . $cuisine . "</button>";
          }
          ?>
        </div>
      </div>
      <div class="second-row-favourites">
        <section class="product">
          <div class="product-container">
            <?php
            $savedRecipesQuery = "SELECT RecipeID FROM recipe ORDER BY RAND() LIMIT 50";
            $stmt1 = mysqli_prepare($conn, $savedRecipesQuery);
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
                      <img src="<?php echo $thumbnailRow['thumbnail']; ?>" class="product-thumb" alt="Thumbnail" />
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

  </div>


  <!-- -------------------------------------Scripts--------------------------------------  -->

  <script src="../javascript/nav-bar.js"></script>
  <script>
    // Function to handle filter application
    function applyFilter() {
      var quickFilter = document.getElementById('quick-filter').value;
      var skillLevel = document.getElementById('skill-level').value;
      var cuisine = document.getElementById('cuisine').value;

      // Send AJAX request to server with selected filters
      fetch("../php/filter-recipes.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "quickFilter=" + encodeURIComponent(quickFilter) + "&skillLevel=" + encodeURIComponent(skillLevel) + "&cuisine=" + encodeURIComponent(cuisine),
      })
        .then((response) => response.text())
        .then((data) => {
          // Update the content of the parent container with filtered results
          var parentContainer = document.querySelector('.second-row-favourites');
          parentContainer.innerHTML = data;
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    }

    // Event listeners for filter dropdown change
    document.getElementById('quick-filter').addEventListener('change', applyFilter);
    document.getElementById('skill-level').addEventListener('change', applyFilter);
    document.getElementById('cuisine').addEventListener('change', applyFilter);

  </script>
</body>

</html>