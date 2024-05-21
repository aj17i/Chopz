<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php';

if (isset($_POST['check'])) {
    $userId = $_SESSION["UserID"];

    $username = $_POST['username'];

    $userid_sql = "SELECT userID FROM user WHERE username = ?";
    $stmt = $mysqli->prepare($userid_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $RatedUserID = $user['userID'];


    // Query to check if the user has rated this recipe before
    $checkQuery = "SELECT rating FROM user_ratings WHERE UserID = ? AND RatedUserID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $RatedUserID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ratedIndex = $row['rating'];

        echo json_encode(array('ratedIndex' => $ratedIndex));
    } else {
        echo json_encode(array('ratedIndex' => null));
    }

    $stmt->close();
    $conn->close();
}
