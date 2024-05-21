<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php';

if (isset($_POST['save'])) {
    $userId = $_SESSION["UserID"];
    $username = $_POST['username']; 
    $ratedIndex = $_POST['ratedIndex'];

    $userid_sql = "SELECT userID FROM user WHERE username = ?";
    $stmt = $mysqli->prepare($userid_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $RatedUserID = $user['userID'];

    $saveQuery = "INSERT INTO user_ratings (UserID, RatedUserID, rating) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($saveQuery);
    $stmt->bind_param("iii", $userId, $RatedUserID, $ratedIndex);
    $stmt->execute();

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        
        $result = mysqli_query($conn, "SELECT ROUND(AVG(rating), 2) AS average_rating FROM user_ratings WHERE RatedUserID = $RatedUserID");
        $row = mysqli_fetch_assoc($result);
        $average_rating = $row['average_rating'];


        $update_query = "UPDATE user SET average_rating = ? WHERE UserID = ?";
        $statement = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($statement, "di", $average_rating, $RatedUserID);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);


        mysqli_close($conn);


        echo json_encode(array("success" => true));
    } else {

        echo json_encode(array("error" => "Failed to save rating."));
    }


    echo json_encode(array('success' => true));


    $stmt->close();
}
