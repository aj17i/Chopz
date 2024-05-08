<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit;
}

// Include database connection
include_once 'database.php';

// Get user ID from session
$userId = $_SESSION['UserID'];

// Get recipe ID from POST data
if (isset($_POST['recipe_id'])) {
    $recipeId = $_POST['recipe_id'];
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Recipe ID not provided.'));
    exit;
}

// Check if the recipe is saved
$stmt = $conn->prepare("SELECT * FROM saved_recipes WHERE UserID = ? AND RecipeID = ?");
$stmt->bind_param('ii', $userId, $recipeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Recipe is saved
    echo json_encode(array('status' => 'saved'));
} else {
    // Recipe is not saved
    echo json_encode(array('status' => 'not_saved'));
}

$stmt->close();
$conn->close();
