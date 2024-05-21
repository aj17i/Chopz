<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}

if (!isset($_POST['profile_id'])) {
    $response = array(
        "status" => "error",
        "message" => "Profile ID not provided"
    );
    echo json_encode($response);
    exit();
}

$loggedInUserId = $_SESSION['UserID'];
$followedAccountId = $_POST['profile_id'];

require_once 'database.php';


$stmt = $conn->prepare("INSERT INTO follower_list (FollowingAccountID, FollowedAccountID) VALUES (?, ?)");
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
        "message" => "Error: " . $conn->error
    );
    echo json_encode($response);
}


$stmt->close();
$conn->close();
?>