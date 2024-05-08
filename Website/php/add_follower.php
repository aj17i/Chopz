<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}

// Check if the profile ID is provided
if (!isset($_POST['profile_id'])) {
    $response = array(
        "status" => "error",
        "message" => "Profile ID not provided"
    );
    echo json_encode($response);
    exit();
}

// Get the logged-in user's ID from the session
$loggedInUserId = $_SESSION['UserID'];
$followedAccountId = $_POST['profile_id'];

// Include database connection
require_once 'database.php';


// User is not following the profile person, so add the follow record to the database
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


// Close the statement and database connection
$stmt->close();
$conn->close();
?>