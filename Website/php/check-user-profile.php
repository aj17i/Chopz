<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}
include_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['UserID'];
    $username = $_POST['username'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE UserID = ? AND username = ?");
    $stmt->bind_param("is", $userId, $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "match";
        exit();
    } else {
        echo "no-match";
        exit();
    }
}
