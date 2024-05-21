<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit;
}
include_once 'database.php';

$userId = $_SESSION['UserID'];

if (isset($_POST['recipe_id'])) {
    $recipeId = $_POST['recipe_id'];
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Recipe ID not provided.'));
    exit;
}

$stmt = $conn->prepare("SELECT * FROM saved_recipes WHERE UserID = ? AND RecipeID = ?");
$stmt->bind_param('ii', $userId, $recipeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Recipe is not saved.'));
    exit;
}

$stmt = $conn->prepare("DELETE FROM saved_recipes WHERE UserID = ? AND RecipeID = ?");
$stmt->bind_param('ii', $userId, $recipeId);

if ($stmt->execute()) {
    echo json_encode(array('status' => 'success', 'message' => 'Recipe unsaved successfully.'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Failed to unsave recipe.'));
}

$stmt->close();
$conn->close();
?>