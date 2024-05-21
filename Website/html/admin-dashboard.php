<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: loginpage.php");
    exit();
}
require_once '../php/database.php';

$user_id = $_SESSION['UserID'];

$query = "SELECT isAdmin FROM user WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($isAdmin);
$stmt->fetch();
$stmt->close();

if ($isAdmin != 1) {
    header("Location: Homepage.php");
    exit();
}

$recipesOverTimeQuery = "SELECT DATE(creation_date) AS date, COUNT(*) AS count FROM recipe GROUP BY DATE(creation_date)";
$recipesOverTimeResult = $conn->query($recipesOverTimeQuery);
$recipesOverTimeData = [];
while ($row = $recipesOverTimeResult->fetch_assoc()) {
    $recipesOverTimeData[] = $row;
}

$usersOverTimeQuery = "SELECT DATE(join_date) AS date, COUNT(*) AS count FROM user GROUP BY DATE(join_date)";
$usersOverTimeResult = $conn->query($usersOverTimeQuery);
$usersOverTimeData = [];
while ($row = $usersOverTimeResult->fetch_assoc()) {
    $usersOverTimeData[] = $row;
}

$topCuisinesQuery = "SELECT Cuisine_name AS cuisine, COUNT(*) AS count FROM recipe GROUP BY Cuisine_name ORDER BY count DESC LIMIT 10";
$topCuisinesResult = $conn->query($topCuisinesQuery);
$topCuisinesData = [];
while ($row = $topCuisinesResult->fetch_assoc()) {
    $topCuisinesData[] = $row;
}

$mostUsedTagsQuery = "SELECT tag_name AS tag, COUNT(*) AS count FROM tag GROUP BY tag_name ORDER BY count DESC LIMIT 15";
$mostUsedTagsResult = $conn->query($mostUsedTagsQuery);
$mostUsedTagsData = [];
while ($row = $mostUsedTagsResult->fetch_assoc()) {
    $mostUsedTagsData[] = $row;
}

$recentUsersQuery = "SELECT username, email, join_date FROM user ORDER BY join_date DESC LIMIT 10";
$recentUsersResult = $conn->query($recentUsersQuery);
$recentUsersData = [];
while ($row = $recentUsersResult->fetch_assoc()) {
    $recentUsersData[] = $row;
}

$reportedUsersQuery = "
    SELECT u.username, u.email, r.count, r.ReportedID 
    FROM report r 
    JOIN user u ON r.ReportedID = u.UserID 
    ORDER BY r.count DESC
";
$reportedUsersResult = $conn->query($reportedUsersQuery);
$reportedUsersData = [];
while ($row = $reportedUsersResult->fetch_assoc()) {
    $reportedUsersData[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chopz | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/admin-dashboard.css">
</head>

<body>
    <header>
        <div class="navbar">
            <a href="Homepage.php" class="logo">
                <img src="../css/images/logo.png" alt="Logo" style="height: 70px; width: 150px" />
            </a>
            <a href="profile-page.php" class="profilePic">
                <img src="../css/images/user.png" alt="" />
            </a>
        </div>
    </header>
    <div class="container">
        <div class="dashboard-container">
            <h1>Welcome to the Admin Dashboard</h1>

            <div class="table-container">
                <h2>Reported Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportedUsersData as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['count']); ?></td>
                                <td>
                                    <a href="view-user-profile.php?username=<?php echo urlencode($user['username']); ?>"
                                        class="btn-view">View User</a>
                                    <button class="btn-remove"
                                        onclick="confirmRemove('<?php echo $user['ReportedID']; ?>', '<?php echo htmlspecialchars($user['username']); ?>')">Remove
                                        User</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <!-----------------confirm delete user div------------------------>



            <br><br>
            <hr>
            <br><br>
            <div class="table-container">
                <h2>Recently Joined Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Join Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsersData as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['join_date']); ?></td>
                                <td><a href="view-user-profile.php?username=<?php echo urlencode($user['username']); ?>"
                                        class="btn-view">View User</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br><br>
            <hr>
            <br><br>
            <div class="chart-row">
                <div class="chart-container">
                    <h2>Recipes Posted Over Time</h2>
                    <canvas id="recipesOverTimeChart"></canvas>
                </div>

                <div class="chart-container">
                    <h2>Users Joining Over Time</h2>
                    <canvas id="usersOverTimeChart"></canvas>
                </div>
            </div>
            <br><br><br><br><br><br><br><br>
            <hr>
            <br><br>
            <div class="chart-row">
                <div class="chart-container">
                    <h2>Top 10 Cuisines</h2>
                    <canvas id="topCuisinesChart"></canvas>
                </div>

                <div class="chart-container">
                    <h2>Most Used Tags</h2>
                    <canvas id="mostUsedTagsChart"></canvas>
                </div>
            </div>
            <br><br><br><br><br><br>
            <hr>
            <br><br>
        </div>
    </div>
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Chopz. All rights reserved.</p>
    </div>
    <div id="confirmationModal" class="modal" >
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalText">Are you sure you want to remove this user?</p>
            <div class="modal-buttons">
                <form id="removeUserForm" action="../php/remove-user.php" method="POST">
                    <input type="hidden" id="removeUserID" name="user_id" value="">
                    <button type="submit" class="confirm">Yes</button>
                </form>
                <button class="cancel" onclick="closeModal()">No</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function confirmRemove(userId, username) {
            document.getElementById('removeUserID').value = userId;
            document.getElementById('modalText').textContent = 'Are you sure you want to remove ' + username + '?';
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('confirmationModal')) {
                closeModal();
            }
        }

        $(document).ready(function () {
            $('.usernamelink').click(function (e) {
                e.preventDefault();
                var username = $(this).data('username');

                $.ajax({
                    type: 'POST',
                    url: '../php/check-user-profile.php',
                    data: { username: username },
                    success: function (response) {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        if (response.status === "match") {
                            window.location.href = "profile-page.php";
                        } else if (response.status === "no-match") {
                            window.location.href = "view-user-profile.php?username=" + encodeURIComponent(response.username);
                        } else {
                            alert(response.message || 'An unknown error occurred.');
                        }
                    },
                    error: function () {
                        alert('An error occurred while processing your request.');
                    }
                });
            });
        });
        var recipesOverTimeData = <?php echo json_encode($recipesOverTimeData); ?>;
        var usersOverTimeData = <?php echo json_encode($usersOverTimeData); ?>;
        var topCuisinesData = <?php echo json_encode($topCuisinesData); ?>;
        var mostUsedTagsData = <?php echo json_encode($mostUsedTagsData); ?>;

        var recipesOverTimeLabels = recipesOverTimeData.map(data => data.date);
        var recipesOverTimeCounts = recipesOverTimeData.map(data => data.count);

        var recipesOverTimeChartCtx = document.getElementById('recipesOverTimeChart').getContext('2d');
        new Chart(recipesOverTimeChartCtx, {
            type: 'line',
            data: {
                labels: recipesOverTimeLabels,
                datasets: [{
                    label: 'Number of Recipes',
                    data: recipesOverTimeCounts,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        var usersOverTimeLabels = usersOverTimeData.map(data => data.date);
        var usersOverTimeCounts = usersOverTimeData.map(data => data.count);

        var usersOverTimeChartCtx = document.getElementById('usersOverTimeChart').getContext('2d');
        new Chart(usersOverTimeChartCtx, {
            type: 'line',
            data: {
                labels: usersOverTimeLabels,
                datasets: [{
                    label: 'Number of Users',
                    data: usersOverTimeCounts,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        var topCuisinesLabels = topCuisinesData.map(data => data.cuisine);
        var topCuisinesCounts = topCuisinesData.map(data => data.count);

        var topCuisinesChartCtx = document.getElementById('topCuisinesChart').getContext('2d');
        new Chart(topCuisinesChartCtx, {
            type: 'bar',
            data: {
                labels: topCuisinesLabels,
                datasets: [{
                    label: 'Number of Recipes',
                    data: topCuisinesCounts,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        var mostUsedTagsLabels = mostUsedTagsData.map(data => data.tag);
        var mostUsedTagsCounts = mostUsedTagsData.map(data => data.count);

        var mostUsedTagsChartCtx = document.getElementById('mostUsedTagsChart').getContext('2d');
        new Chart(mostUsedTagsChartCtx, {
            type: 'pie',
            data: {
                labels: mostUsedTagsLabels,
                datasets: [{
                    label: 'Number of Tags',
                    data: mostUsedTagsCounts,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>

</html>