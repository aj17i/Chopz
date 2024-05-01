<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
} else {
    if (isset($_POST['submit']) || isset($_POST['bio']) || isset($_POST['username']) || isset($_FILES['profilePic'])) {
        require_once 'database.php';
        include "database.php";
        echo "<pre>";
        print_r($_FILES['profilePic']);
        echo "</pre>";
        $UserID = $_SESSION['UserID'];
        $profilePic = isset($_POST['profilePic']) ? $_POST['profilePic'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $bio = isset($_POST['bio']) ? $_POST['bio'] : '';
        $img_name = $_FILES['profilePic']['name'];
        $img_size = $_FILES['profilePic']['size'];
        $tmp_name = $_FILES['profilePic']['tmp_name'];
        $error = $_FILES['profilePic']['error'];

        if ($error === 0) {
            if ($img_size > 125000) {
                $em = "sorry, your file is too large!";
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

                    //insert into database

                    $query = " UPDATE user SET 
                    profilePic = IF(LENGTH(TRIM('$new_img_name')) > 0, '$new_img_name', profilePic),
                    username = IF(LENGTH(TRIM('$username')) > 0, '$username', username),
                    bio = IF(LENGTH(TRIM('$bio')) > 0, '$bio', bio)
                    WHERE UserID = $UserID ";

                    mysqli_query($conn, $query);
                    header("Location: ../html/profile-page.php");

                } else {
                    $em = "only images are accepted";
                    header("Location: ../html/edit-profile.php?error=$em");
                    exit();
                }
            }

        } else {
            $em = "Unknown error occured!";
            header("Location: ../html/edit-profile.php?error=$em");
            exit();
        }
    } else {
        header("Location: ../html/edit-profile.php");
        exit();
    }
}