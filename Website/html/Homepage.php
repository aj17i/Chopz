<?php
session_start();
$mysqli = require_once '../php/database.php';
if (!isset($_SESSION["UserID"])) {
  header("Location: loginpage.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chopz | Homepage</title>
  <link rel="stylesheet" href="../css/homepage.css" />
</head>

<body>
  <header>
    <div class="navbar">
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
              <button type="button" onclick="searcRecipe()">Search</button>
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

        <div class="search-Image">
          <img src="../css/images/magnifier.png" alt="" />
        </div>
      </div>
      <a href="profile-page.php" class="profilePic">
        <img src="../css/images/user.png" alt="" />
      </a>
    </div>
  </header>

  <!-- -------------------------------------body--------------------------------------  -->

  <div id="searchResult"></div>

  <!-- -------------------------------------Scripts--------------------------------------  -->

  <script src="../javascript/nav-bar.js"></script>
</body>

</html>