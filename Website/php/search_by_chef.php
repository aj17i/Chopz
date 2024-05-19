<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}
require_once 'database.php';

$chef_name = $_POST['chef'];

$query = "SELECT username, profilePic, average_Rating FROM user WHERE username LIKE ?";
$stmt = mysqli_prepare($conn, $query);
$searchTerm = "%" . $chef_name . "%";
mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    ?>
    <section class="product">
        <div class="profile-container">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                $username = htmlspecialchars($row['username']);
                $profilePic = htmlspecialchars($row['profilePic']);
                $averageRating = htmlspecialchars($row['average_Rating']);
                ?>

                <div class="single_advisor_profile wow fadeInUp usernamelink" data-wow-delay="0.2s"
                    data-username="<?php echo $username; ?>"
                    style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                    <div class="advisor_thumb">
                        <img src="../css/images/<?php echo $profilePic; ?>" alt="" />
                    </div>
                    <div class="single_advisor_details_info">
                        <h4><?php echo $username; ?></h4>
                        <p class="designation">Rating: <?php echo $averageRating; ?></p>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>
    </section>

    <?php
} else {
    echo "No chefs found.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>