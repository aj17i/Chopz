<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php'; // Include your database connection file

if (isset($_POST['check'])) {
    // Fetch user ID from session
    $userId = $_SESSION["UserID"];

    // Fetch recipe ID from wherever you have it
    $recipeId = $_POST['recipe_id']; // You need to pass this through AJAX from the recipe page

    // Query to check if the user has rated this recipe before
    $checkQuery = "SELECT rating FROM ratings WHERE UserID = ? AND RecipeID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If user has rated before, fetch the rating
        $row = $result->fetch_assoc();
        $ratedIndex = $row['rating'];

        // Return the rated index
        echo json_encode(array('ratedIndex' => $ratedIndex));
    } else {
        // If user hasn't rated before, return null
        echo json_encode(array('ratedIndex' => null));
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
