<?php
// Include your database connection file
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
  header("Location: loginpage.php");
  exit();
}
require_once '../php/database.php';

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css" />
  <link rel="stylesheet" href="../css/create-a-post.css">
  <title>Chopz | Create Recipe</title>
</head>

<body>
  <div class="container">
    <h2>Post a Recipe</h2>
    <form action="../php/post-recipe.php" method="post" enctype="multipart/form-data">
      <label for="title">Title:</label><br />
      <input type="text" id="title" name="title" required /><br /><br />

      <label for="description">Description:</label><br />
      <textarea id="description" name="description" rows="4" cols="50" required></textarea><br /><br />

      <label for="inspo">FAQ, Inspiration and others:</label><br />
      <textarea id="inspo" name="inspo" rows="8" cols="50" required></textarea><br />

      <label for="cuisine_name">Cuisine:</label><br />
      <select id="cuisine_name" name="cuisine_name" required>
        <?php echo $cuisineOptions; ?>
      </select><br /><br />

      <label for="skill_level">Skill Level:</label><br />
      <input type="text" id="skill_level" name="skill_level" required /><br />

      <label for="prep_time">Prep Time:</label><br />
      <input type="text" id="prep_time" name="prep_time" required /><br />

      <label for="cooking_time">Cooking Time:</label><br />
      <input type="text" id="cooking_time" name="cooking_time" required /><br />

      <label for="serving_size">Servings:</label><br />
      <input type="number" id="serving_size" name="serving_size" required /><br />

      <label for="ingredients">Ingredients:</label><br />
      <ul id="ingredients-list">
        <li>
          <input type="text" name="ingredients[]" placeholder="Ingredient 1" required />
          <select name="quantities[]" placeholder="Quantity">
            <option value="a">a</option>
            <option value="1/8">1/8</option>
            <option value="1/4">1/4</option>
            <option value="1/3">1/3</option>
            <option value="1/2">1/2</option>
            <option value="2/3">2/3</option>
            <option value="3/4">3/4</option>
            <option value="1">1</option>
            <option value="1 1/3">1 1/3</option>
            <option value="1 1/2">1 1/2</option>
            <option value="1 2/3">1 2/3</option>
            <option value="1 3/4">1 3/4</option>
            <option value="2">2</option>
            <option value="2 1/2">2 1/2</option>
            <option value="3">3</option>
            <option value="3 1/2">3 1/2</option>
            <option value="4">4</option>
            <option value="4 1/2">4 1/2</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <!-- Add more quantities as needed -->
          </select>
          <select name="units[]" placeholder="Unit">
            <option value="cup">cup</option>
            <option value="liter">liter</option>
            <option value="gram">gram</option>
            <option value="ounce">ounce</option>
            <option value="pound">pound</option>
            <option value="teaspoon">tsp</option>
            <option value="tablespoon">tbsp</option>
            <option value="pinch">pinch</option>
            <option value="dash">dash</option>
            <option value="clove">clove</option>
            <option value="slice">slice</option>
            <option value="-">-</option>
            <!-- Add more units as needed -->
          </select>
          <button type="button" onclick="removeItem(this)">Remove</button>
        </li>
      </ul>
      <button type="button" onclick="addIngredient()">Add Ingredient</button><br /><br />

      <label for="calories">Calories:</label><br />
      <input type="number" id="calories" name="calories" required />

      <label for="carbs">Carbs:</label><br />
      <input type="number" id="carbs" name="carbs" required />

      <label for="protein">Protein:</label><br />
      <input type="number" id="protein" name="protein" required />

      <label for="fat">Fat:</label><br />
      <input type="number" id="fat" name="fat" required /><br />


      <label for="instructions">Instructions:</label><br />
      <ol id="instructions-list">
        <li>
          <input type="text" name="instructions[]" placeholder="Step 1 instruction" required>
          <button type="button" onclick="removeItem(this)">Remove</button>
        </li>
      </ol>
      <button type="button" onclick="addInstruction()">Add Instruction</button><br /><br />

      <label for="thumbnail">Thumbnail image:</label>
      <input type="file" id="thumbnail" name="thumbnail" accept="image/*" onchange="return checkImageSize(this)"
        required><br>

      <label for="images">Images:</label><br />
      <input type="file" id="images" name="images[]" accept="image/*" onchange="return checkImageSize(this)" multiple
        required /><br />
      <ul id="images-list"></ul>
      <button type="button" onclick="addImage()">Add Image</button><br /><br />




      <label for="tags">Tags:</label><br />
      <ul id="tags-list">
        <li>
          <input type="text" name="tags[]" placeholder="Tag 1" required />
          <button type="button" onclick="removeItem(this)">Remove</button>
        </li>
      </ul>
      <button type="button" onclick="addTag()">Add Tag</button><br /><br />

      <input type="submit" name="submit" value="Post Recipe" />
      <br>
    </form>
    <a href="profile-page.php"><button>Go Back &rarr;</button></a>
  </div>

  <script>
    // Function to add a new item to the list
    function addItem(listId, placeholder) {
      var list = document.getElementById(listId);
      var newItem = document.createElement("li");
      newItem.innerHTML =
        '<input type="text" name="' +
        listId.slice(0, -5) +
        '[]" placeholder="' +
        placeholder +
        '"> <button type="button" onclick="removeItem(this)">Remove</button>';
      list.appendChild(newItem);
    }

    // Function to remove an item from the list
    function removeItem(button) {
      button.parentNode.remove();
    }

    // Function to add a new ingredient field
    function addIngredient() {
      var list = document.getElementById("ingredients-list");
      var newItem = document.createElement("li");
      newItem.innerHTML =
        '<input type="text" name="ingredients[]" placeholder="Ingredient" required />' +
        '<select name="quantities[]" placeholder="Quantity">' +
        '<option value="a">a</option>' +
        '<option value="1/8">1/8</option>' +
        '<option value="1/4">1/4</option>' +
        '<option value="1/3">1/3</option>' +
        '<option value="1/2">1/2</option>' +
        '<option value="2/3">2/3</option>' +
        '<option value="3/4">3/4</option>' +
        '<option value="1">1</option>' +
        '<option value="1 1/3">1 1/3</option>' +
        '<option value="1 1/2">1 1/2</option>' +
        '<option value="1 2/3">1 2/3</option>' +
        '<option value="1 3/4">1 3/4</option>' +
        '<option value="2">2</option>' +
        '<option value="2 1/2">2 1/2</option>' +
        '<option value="3">3</option>' +
        '<option value="3 1/2">3 1/2</option>' +
        '<option value="4">4</option>' +
        '<option value="4 1/2">4 1/2</option>' +
        '<option value="5">5</option>' +
        '<option value="6">6</option>' +
        '</select>' +
        '<select name="units[]" placeholder="Unit">' +
        '<option value="-">-</option>' +
        '<option value="cup">cup</option>' +
        '<option value="liter">liter</option>' +
        '<option value="gram">gram</option>' +
        '<option value="ounce">ounce</option>' +
        '<option value="pound">pound</option>' +
        '<option value="teaspoon">tsp</option>' +
        '<option value="tablespoon">tbsp</option>' +
        '<option value="pinch">pinch</option>' +
        '<option value="dash">dash</option>' +
        '<option value="clove">clove</option>' +
        '<option value="slice">slice</option>' +
        '</select>' +
        '<button type="button" onclick="removeItem(this)">Remove</button>';
      list.appendChild(newItem);
    }

    // Function to add a new instruction field
    function addInstruction() {
      addItem("instructions-list", "Step instruction");
    }

    // Function to add a new tag field
    function addTag() {
      addItem("tags-list", "Tag");
    }

    // Function to check if the selected image size is too large
    function checkImageSize(input) {
      var files = input.files;
      var maxSize = 4194304; // Maximum allowed size in bytes (adjust as needed)

      for (var i = 0; i < files.length; i++) {
        if (files[i].size > maxSize) {
          alert("Sorry, your file \"" + files[i].name + "\" is too large!");
          input.value = ''; // Clear the file input
          return false; // Prevent form submission
        }
      }
      return true; // Image size is acceptable
    }

    // Function to add a new image field
    function addImage() {
      var list = document.getElementById("images-list");
      var newItem = document.createElement("li");
      newItem.innerHTML =
        '<input type="file" name="images[]" accept="image/*" onchange="return checkImageSize(this)"> <button type="button" onclick="removeItem(this)">Remove</button>';
      list.appendChild(newItem);
    }

    // Function to filter cuisines based on user input
    function filterCuisines(input) {
      var select = document.getElementById("cuisine_name_select");
      var options = select.options;
      for (var i = 0; i < options.length; i++) {
        var option = options[i];
        if (option.text.toLowerCase().startsWith(input.toLowerCase())) {
          option.style.display = "";
        } else {
          option.style.display = "none";
        }
      }
      select.style.display = "block";
    }
  </script>
</body>

</html>