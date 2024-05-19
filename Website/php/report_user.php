<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
if (!isset($_POST['username'])) {
    $response = array(
        "status" => "error",
        "message" => "username not provided"
    );
    echo json_encode($response);
    exit();
}
require_once 'database.php';

$username = trim($_POST['username']);
$userid_sql = "SELECT userID FROM user WHERE username = ?";
$stmt = $mysqli->prepare($userid_sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $response = array(
        "status" => "error",
        "message" => "User not found"
    );
    echo json_encode($response);
    exit();
}

$ReportedID = $user['userID'];

$check_sql = "SELECT count FROM report WHERE ReportedID = ?";
$stmt = $mysqli->prepare($check_sql);
$stmt->bind_param("i", $ReportedID);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();
$stmt->close();

if ($report) {
    
    $update_sql = "UPDATE report SET count = count + 1 WHERE ReportedID = ?";
    $stmt = $mysqli->prepare($update_sql);
    $stmt->bind_param("i", $ReportedID);
    $stmt->execute();
    $stmt->close();
    $response = array(
        "status" => "success",
        "message" => "User Reported"
    );
} else {
    $insert_sql = "INSERT INTO report (ReportedID, count) VALUES (?, 1)";
    $stmt = $mysqli->prepare($insert_sql);
    $stmt->bind_param("i", $ReportedID);
    $stmt->execute();
    $stmt->close();
    $response = array(
        "status" => "success",
        "message" => "User Reported"
    );
}

echo json_encode($response);