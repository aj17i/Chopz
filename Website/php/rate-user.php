<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
include 'database.php';// Include your database connection file

if (isset($_POST['save'])) {
    // Fetch user ID from session
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

    // Prepare the SQL statement


    if (mysqli_stmt_affected_rows($stmt) > 0) {
        
        $result = mysqli_query($conn, "SELECT ROUND(AVG(rating), 2) AS average_rating FROM user_ratings WHERE RatedUserID = $RatedUserID");
        $row = mysqli_fetch_assoc($result);
        $average_rating = $row['average_rating'];

        // Update the recipe table with the new average rating
        $update_query = "UPDATE user SET average_rating = ? WHERE UserID = ?";
        $statement = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($statement, "di", $average_rating, $RatedUserID);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        // Close the database connection
        mysqli_close($conn);

        // Send a success response
        echo json_encode(array("success" => true));
    } else {
        // Send an error response
        echo json_encode(array("error" => "Failed to save rating."));
    }

    // Return success response
    echo json_encode(array('success' => true));

    // Close statement and connection
    $stmt->close();
}
