<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
require_once '../php/database.php';
$userID = $_SESSION['UserID'];

// Query to get followers
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="../css/view-follower-list.css">
    <title>Chopz | Follower List</title>
    
</head>

<body>
    <div class="container">
        <button><a href='profile-page.php'>back</a></button>
        <?php
        $sqlFollowers = "SELECT user.Username 
                FROM follower_list 
                INNER JOIN user ON follower_list.FollowingAccountID = user.UserID 
                WHERE follower_list.FollowedAccountID = ?";

        // Prepare and bind parameters
        $stmtFollowers = $conn->prepare($sqlFollowers);
        $stmtFollowers->bind_param("i", $userID);

        $stmtFollowers->execute();
        $resultFollowers = $stmtFollowers->get_result();
        echo "<div class='row'>";
        echo "<div class='column'>";
        echo "<table>";
        echo "<tr><th>Followers</th></tr>";
        if ($resultFollowers->num_rows > 0) {
            while ($row = $resultFollowers->fetch_assoc()) {
                echo "<tr><td>" . $row["Username"] . "</td></tr>";
            }
        } else {
            echo "<tr><td>No followers</td></tr>";
        }
        echo "</table>";
        echo "</div>";
        // Query to get followed accounts
        $sqlFollowed = "SELECT user.Username 
                FROM follower_list 
                INNER JOIN user ON follower_list.FollowedAccountID = user.UserID 
                WHERE follower_list.FollowingAccountID = ?";

        $stmtFollowed = $conn->prepare($sqlFollowed);
        $stmtFollowed->bind_param("i", $userID);

        $stmtFollowed->execute();
        $resultFollowed = $stmtFollowed->get_result();
        echo "<div class = 'column'>";
        echo "<table>";
        echo "<tr><th>Following</th></tr>";
        if ($resultFollowed->num_rows > 0) {
            while ($row = $resultFollowed->fetch_assoc()) {
                echo "<tr><td>" . $row["Username"] . "</td></tr>";
            }
        } else {
            echo "<tr><td>Not following anyone</td></tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "</div>";

        $stmtFollowers->close();
        $stmtFollowed->close();
        $conn->close();
        ?>
    </div>

</body>

</html>