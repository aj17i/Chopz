<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php'; 

if (isset($_POST['check'])) {
    $userId = $_SESSION["UserID"];

    $recipeId = $_POST['recipe_id']; 

    $checkQuery = "SELECT rating FROM ratings WHERE UserID = ? AND RecipeID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ratedIndex = $row['rating'];

        echo json_encode(array('ratedIndex' => $ratedIndex));
    } else {
        echo json_encode(array('ratedIndex' => null));
    }

    $stmt->close();
    $conn->close();
}
