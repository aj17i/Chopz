<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php';

if (isset($_POST['check'])) {
    // Fetch user ID from session
    $userId = $_SESSION["UserID"];

    // Fetch recipe ID from wherever you have it
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
        // If user has rated before, fetch the rating
        $row = $result->fetch_assoc();
        $ratedIndex = $row['rating'];

        // Return the rated index
        echo json_encode(array('ratedIndex' => $ratedIndex));
    } else {
        // If user hasn't rated before, return null
        echo json_encode(array('ratedIndex' => null));
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
