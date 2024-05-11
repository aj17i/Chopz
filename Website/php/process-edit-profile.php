<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
}

require_once 'database.php';
include "database.php";

$UserID = $_SESSION['UserID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fields = array();
    $update_query = "UPDATE user SET ";

    if (!empty($_FILES['profilePic']['name'])) {
        $img_name = $_FILES['profilePic']['name'];
        $img_size = $_FILES['profilePic']['size'];
        $tmp_name = $_FILES['profilePic']['tmp_name'];
        $error = $_FILES['profilePic']['error'];

        if ($error === 0) {
            if ($img_size > 125000) {
                $em = "Sorry, your file is too large!";
                header("Location: ../html/edit-profile.php?error=$em");
                exit();
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "png", "jpeg");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = uniqid("IMG-", true) . "." . $img_ex_lc;
                    $img_upload_path = "../css/images/" . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);

                    $fields[] = "profilePic = '$new_img_name'";
                } else {
                    $em = "Only images are accepted";
                    header("Location: ../html/edit-profile.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Unknown error occurred!";
            header("Location: ../html/edit-profile.php?error=$em");
            exit();
        }
    }

    // Process other form fields
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];
        $check_query = "SELECT * FROM user WHERE username = ? AND UserID != ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("si", $username, $UserID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $em = "Username already taken. Please try another one.";
            header("Location: ../html/edit-profile.php?error=$em");
            exit();
        }

        $fields[] = "username = '$username'";
    }
    if (!empty($_POST['email'])) {
        $email = $_POST['email'];
        $fields[] = "email = '$email'";
    }
    if (!empty($_POST['bio'])) {
        $bio = $_POST['bio'];
        $fields[] = "bio = '$bio'";
    }
    if (!empty($_POST['full_name'])) {
        $full_name = $_POST['full_name'];
        $fields[] = "full_name = '$full_name'";
    }
    if (!empty($_POST['nationality'])) {
        $nationality = $_POST['nationality'];
        $fields[] = "nationality = '$nationality'";
    }

    if (!empty($fields)) {
        $update_query .= implode(", ", $fields) . " WHERE UserID = $UserID";
        mysqli_query($conn, $update_query);
    }

    header("Location: ../html/profile-page.php");
    exit();
} else {
    header("Location: ../html/edit-profile.php");
    exit();
}
