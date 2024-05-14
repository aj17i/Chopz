<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
require_once 'database.php';
if (isset($_POST['recipeId'])) {
    $recipeId = intval($_POST['recipeId']);


    $sql_delete = "DELETE FROM recipe WHERE RecipeID = ?";
    $delete_stmt = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($delete_stmt, 'i', $recipeId);
    if (mysqli_stmt_execute($delete_stmt)) {
        $response['success'] = true;
        $response['message'] = "Recipe deleted successfully.";
    } else {
        $response['success'] = false;
        $response['message'] = "Oops! Something went wrong. Please try again later.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Recipe ID not provided.";
}
echo json_encode($response);