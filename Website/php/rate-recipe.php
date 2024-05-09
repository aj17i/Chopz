<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php';// Include your database connection file

if (isset($_POST['save'])) {
    // Fetch user ID from session
    $userId = $_SESSION["UserID"];

    // Fetch recipe ID from wherever you have it
    $recipeId = $_POST['recipe_id']; // You need to pass this through AJAX from the recipe page

    // Fetch rated index from AJAX
    $ratedIndex = $_POST['ratedIndex'];

    // Query to save the rating to the database
    $saveQuery = "INSERT INTO ratings (UserID, RecipeID, rating) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($saveQuery);
    $stmt->bind_param("iii", $userId, $recipeId, $ratedIndex);
    $stmt->execute();

    // Prepare the SQL statement


    // Check if the statement was executed successfully
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Retrieve the current average rating of the recipe from the database
        $result = mysqli_query($conn, "SELECT ROUND(AVG(rating), 2) AS average_rating FROM ratings WHERE RecipeID = $recipeId");
        $row = mysqli_fetch_assoc($result);
        $average_rating = $row['average_rating'];

        // Update the recipe table with the new average rating
        $update_query = "UPDATE recipe SET average_rating = ? WHERE RecipeID = ?";
        $statement = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($statement, "di", $average_rating, $recipeId);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        // Close the database connection
        mysqli_close($conn);

        // Send a success response
        echo json_encode(array("success" => true));
    } else {
        // Send an error response
        echo json_encode(array("error" => "Failed to save rating."));
    }

    // Return success response
    echo json_encode(array('success' => true));

    // Close statement and connection
    $stmt->close();
}
