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
    $username = $_POST['username'];

    $stmt = $mysqli->prepare("SELECT username FROM user WHERE UserID = ? AND username = ?");
    if (!$stmt) {
        $response['status'] = 'error';
        $response['message'] = 'Prepare statement failed: ' . $mysqli->error;
        echo json_encode($response);
        exit();
    }
    $stmt->bind_param("is", $userId, $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $response['status'] = 'match';
    } else {
        $response['status'] = 'no-match';
        $response['username'] = $username;
    }
}


echo json_encode($response);
exit();
