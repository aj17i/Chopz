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

      <label for="ingredients">Ingredients:</label><br />
      <ul id="ingredients-list">
        <li>
          <input type="text" name="ingredients[]" placeholder="Ingredient 1" required />
          <button type="button" onclick="removeItem(this)">Remove</button>
        </li>
      </ul>
      <button type="button" onclick="addIngredient()">Add Ingredient</button><br /><br />

      <label for="instructions">Instructions:</label><br />
      <ol id="instructions-list">
        <li>
          <textarea name="instructions[]" rows="2" cols="50" placeholder="Step 1 instruction" required></textarea>
          <button type="button" onclick="removeItem(this)">Remove</button>
        </li>
      </ol>
      <button type="button" onclick="addInstruction()">Add Instruction</button><br /><br />

      <label for="images">Images:</label><br />
      <input type="file" id="images" name="images[]" accept="image/*" multiple required /><br />
      <ul id="images-list"></ul>
      <button type="button" onclick="addImage()">Add Image</button><br /><br />

      <label for="cuisine_name">Cuisine Name:</label><br />
      <select id="cuisine_name" name="cuisine_name" required>
        <?php echo $cuisineOptions; ?>
      </select><br /><br />


      <label for="tags">Tags:</label><br />
      <ul id="tags-list">
        <li>
          <input type="text" name="tags[]" placeholder="Tag 1" required />
          <button type="button" onclick="removeItem(this)">Remove</button>
        </li>
      </ul>
      <button type="button" onclick="addTag()">Add Tag</button><br /><br />

      <input type="submit" name="submit" value="Post Recipe" />
    </form>
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
      addItem("ingredients-list", "Ingredient");
    }

    // Function to add a new instruction field
    function addInstruction() {
      addItem("instructions-list", "Step instruction");
    }

    // Function to add a new tag field
    function addTag() {
      addItem("tags-list", "Tag");
    }

    // Function to add a new image field
    function addImage() {
      var list = document.getElementById("images-list");
      var newItem = document.createElement("li");
      newItem.innerHTML =
        '<input type="file" name="images[]" accept="image/*"> <button type="button" onclick="removeItem(this)">Remove</button>';
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