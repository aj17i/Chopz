<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}


include_once 'database.php'; 


if (isset($_POST['username'])) {
    $username = $_POST['username'];
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Profile ID not provided.'));
    exit;
}


$userId = $_SESSION['UserID'];


$userid_sql = "SELECT userID FROM user WHERE username = ?";
$stmt = $mysqli->prepare($userid_sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$OtherUserID = $user['userID'];


$query = "SELECT * FROM follower_list WHERE FollowedAccountID = ? AND FollowingAccountID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $OtherUserID, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(array('status' => 'following'));
} else {
    echo json_encode(array('status' => 'not_following'));
}

$stmt->close();
$conn->close();