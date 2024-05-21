<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}

include_once 'database.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['UserID'];
    $recipeId = $_POST['recipeId'];

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM recipe WHERE UserID = ? AND RecipeID = ?");
    if (!$stmt) {
        $response['status'] = 'error';
        $response['message'] = 'Prepare statement failed: ' . $mysqli->error;
        echo json_encode($response);
        exit();
    }
    $stmt->bind_param("ii", $userId, $recipeId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $response['status'] = 'match';
    } else {
        $stmt = $mysqli->prepare("SELECT UserID FROM recipe WHERE RecipeID = ?");
        if (!$stmt) {
            $response['status'] = 'error';
            $response['message'] = 'Prepare statement failed: ' . $mysqli->error;
            echo json_encode($response);
            exit();
        }
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        $stmt->bind_result($recipeOwnerId);
        $stmt->fetch();
        $stmt->close();

        if ($recipeOwnerId) {
            $stmt = $mysqli->prepare("SELECT username FROM user WHERE UserID = ?");
            if (!$stmt) {
                $response['status'] = 'error';
                $response['message'] = 'Prepare statement failed: ' . $mysqli->error;
                echo json_encode($response);
                exit();
            }
            $stmt->bind_param("i", $recipeOwnerId);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            $response['status'] = 'no-match';
            $response['username'] = $username;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Recipe owner not found';
        }
    }
}

echo json_encode($response);
exit();
