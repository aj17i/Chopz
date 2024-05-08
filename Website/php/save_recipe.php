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

// Check if the recipe is already saved
$stmt = $conn->prepare("SELECT * FROM saved_recipes WHERE UserID = ? AND RecipeID = ?");
$stmt->bind_param('ii', $userId, $recipeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Recipe is already saved
    echo json_encode(array('status' => 'error', 'message' => 'Recipe is already saved.'));
    exit;
}

// Save the recipe
$stmt = $conn->prepare("INSERT INTO saved_recipes (UserID, RecipeID, save_date) VALUES (?, ?, CURRENT_TIMESTAMP)");
$stmt->bind_param('ii', $userId, $recipeId);

if ($stmt->execute()) {
    echo json_encode(array('status' => 'success', 'message' => 'Recipe saved successfully.'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Failed to save recipe.'));
}

$stmt->close();
$conn->close();
