<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
require_once 'database.php';
$user_id = $_SESSION['UserID'];

$query = "SELECT isAdmin FROM user WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($isAdmin);
$stmt->fetch();
$stmt->close();

if ($isAdmin != 1) {
    header("Location: Homepage.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $removeUserId = intval($_POST['user_id']);


    $deleteQuery = "DELETE FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $removeUserId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "User removed successfully.";
    } else {
        $_SESSION['message'] = "Failed to remove user.";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../html/admin-dashboard.php");
    exit();
}