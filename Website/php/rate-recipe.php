<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php';

if (isset($_POST['save'])) {
    $userId = $_SESSION["UserID"];

    $recipeId = $_POST['recipe_id'];

    $ratedIndex = $_POST['ratedIndex'];

    $saveQuery = "INSERT INTO ratings (UserID, RecipeID, rating) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($saveQuery);
    $stmt->bind_param("iii", $userId, $recipeId, $ratedIndex);
    $stmt->execute();




    if (mysqli_stmt_affected_rows($stmt) > 0) {

        $result = mysqli_query($conn, "SELECT ROUND(AVG(rating), 2) AS average_rating FROM ratings WHERE RecipeID = $recipeId");
        $row = mysqli_fetch_assoc($result);
        $average_rating = $row['average_rating'];

        $update_query = "UPDATE recipe SET average_rating = ? WHERE RecipeID = ?";
        $statement = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($statement, "di", $average_rating, $recipeId);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        mysqli_close($conn);

        echo json_encode(array("success" => true));
    } else {

        echo json_encode(array("error" => "Failed to save rating."));
    }


    echo json_encode(array('success' => true));


    $stmt->close();
}
