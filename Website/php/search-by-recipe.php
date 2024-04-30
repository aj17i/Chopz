<?php
// Retrieve the parameter
$recipeName = $_POST['recipeName'];

// Perform your search or other processing here
// Example: Search the database for recipes by chef name
$searchResult = "Searching for recipes: " . $recipeName;

// Output the search result
echo $searchResult;
