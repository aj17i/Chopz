<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}


include_once 'database.php'; 


if (isset($_POST['profile_id'])) {
    $profileId = $_POST['profile_id'];
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Profile ID not provided.'));
    exit;
}

$userId = $_SESSION['UserID'];

$query = "SELECT * FROM follower_list WHERE FollowedAccountID = ? AND FollowingAccountID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $profileId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(array('status' => 'following'));
} else {
    echo json_encode(array('status' => 'not_following'));
}

$stmt->close();
$conn->close();