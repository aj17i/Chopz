<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}

include_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $comment = $_POST['comment'];
    $userID = $_SESSION['UserID'];
    $recipeID = $_POST['recipe_id'];


    $stmt = $conn->prepare("INSERT INTO comment (CommentingUserID, RecipeID, comment, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $userID, $recipeID, $comment);
    $stmt->execute();
    $stmt->close();
}

