<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}

// Include your database connection file
include_once 'database.php'; // Adjust the path as needed

// Get the profile ID from the request
if (isset($_POST['profile_id'])) {
    $profileId = $_POST['profile_id'];
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Profile ID not provided.'));
    exit;
}

// Get the current user's ID
$userId = $_SESSION['UserID'];

// Query to check if the current user is following the profile
$query = "SELECT * FROM follower_list WHERE FollowedAccountID = ? AND FollowingAccountID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $profileId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User is following the profile
    echo json_encode(array('status' => 'following'));
} else {
    // User is not following the profile
    echo json_encode(array('status' => 'not_following'));
}

$stmt->close();
$conn->close();