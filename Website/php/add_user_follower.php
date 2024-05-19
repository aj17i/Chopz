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

$followedAccountId = $user['userID'];
$loggedInUserId = $_SESSION['UserID'];

$stmt = $mysqli->prepare("INSERT INTO follower_list (FollowingAccountID, FollowedAccountID) VALUES (?, ?)");
$stmt->bind_param("ii", $loggedInUserId, $followedAccountId);

if ($stmt->execute()) {
    $response = array(
        "status" => "success",
        "message" => "Follower added successfully"
    );
    echo json_encode($response);
} else {
    $response = array(
        "status" => "error",
        "message" => "Error: " . $stmt->error
    );
    echo json_encode($response);
}

$stmt->close();
$mysqli->close();

