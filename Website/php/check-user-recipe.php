<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}
include_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['UserID'];
    $recipeId = $_POST['recipeId'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM recipe WHERE UserID = ? AND RecipeID = ?");
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Check if the UserID matches
    if ($count > 0) {
        echo "match";
        exit();
    } else {
        echo "no-match";
        exit();
    }
}
