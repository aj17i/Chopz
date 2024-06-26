<?php
session_start();
if (!$_SESSION['logged'] || $_SESSION['logged'] !== true) {
    header("Location: ../html/loginpage.php");
    exit();
} else {
    require_once 'database.php';

    if (isset($_POST['submit'])) {
        function sanitize($input)
        {
            return htmlspecialchars($input);
        }

        function handleFileUploads($files)
        {
            $uploadedPaths = array();
            foreach ($files['name'] as $key => $name) {
                $img_name = sanitize($name);
                $img_size = $files['size'][$key];
                $tmp_name = $files['tmp_name'][$key];
                $error = $files['error'][$key];

                if ($error === 0) {
                    if ($img_size > 4194304) {
                        $em = "Sorry, your file is too large!";
                        header("Location: ../html/create-post.php?error=$em");
                        exit();
                    } else {
                        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                        $img_ex_lc = strtolower($img_ex);
                        $allowed_exs = array("jpg", "png", "jpeg");

                        if (in_array($img_ex_lc, $allowed_exs)) {
                            $new_img_name = uniqid("IMG-", true) . "." . $img_ex_lc;
                            $img_upload_path = "../css/images/" . $new_img_name;
                            move_uploaded_file($tmp_name, $img_upload_path);
                            $uploadedPaths[] = $img_upload_path;
                        } else {
                            $em = "Only images are accepted";
                            header("Location: ../html/create-post.php?error=$em");
                            exit();
                        }
                    }
                } else {
                    $em = "Unknown error occurred!";
                    header("Location: ../html/create-post.php?error=$em");
                    exit();
                }
            }
            return $uploadedPaths;
        }
        $thumbnail_path = '';
        if ($_FILES['thumbnail']['error'] === 0) {
            $thumbnail_name = sanitize($_FILES['thumbnail']['name']);
            $thumbnail_size = $_FILES['thumbnail']['size'];
            $thumbnail_tmp_name = $_FILES['thumbnail']['tmp_name'];
            $thumbnail_error = $_FILES['thumbnail']['error'];

            if ($thumbnail_error === 0) {
                if ($thumbnail_size > 4194304) {
                    $em = "Sorry, your thumbnail file is too large!";
                    header("Location: ../html/create-post.php?error=$em");
                    exit();
                } else {
                    $thumbnail_ex = pathinfo($thumbnail_name, PATHINFO_EXTENSION);
                    $thumbnail_ex_lc = strtolower($thumbnail_ex);
                    $allowed_exs = array("jpg", "png", "jpeg");

                    if (in_array($thumbnail_ex_lc, $allowed_exs)) {
                        $thumbnail_new_name = uniqid("THUMB-", true) . "." . $thumbnail_ex_lc;
                        $thumbnail_upload_path = "../css/images/" . $thumbnail_new_name;
                        move_uploaded_file($thumbnail_tmp_name, $thumbnail_upload_path);
                        $thumbnail_path = $thumbnail_upload_path;
                    } else {
                        $em = "Only images are accepted for the thumbnail";
                        header("Location: ../html/create-post.php?error=$em");
                        exit();
                    }
                }
            } else {
                $em = "Unknown error occurred while uploading the thumbnail!";
                header("Location: ../html/create-post.php?error=$em");
                exit();
            }
        }

        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $cuisine_name = sanitize($_POST['cuisine_name']);
        $inspo = sanitize($_POST['inspo']);
        $skill_level = sanitize($_POST['skill_level']);
        $prep_time = sanitize($_POST['prep_time']);
        $cooking_time = sanitize($_POST['cooking_time']);
        $serving_size = sanitize($_POST['serving_size']);
        $calories = sanitize($_POST['calories']);
        $carbs = sanitize($_POST['carbs']);
        $protein = sanitize($_POST['protein']);
        $fat = sanitize($_POST['fat']);
        $ingredients = array_map('sanitize', $_POST['ingredients']);
        $quantities = array_map('sanitize', $_POST['quantities']);
        $units = array_map('sanitize', $_POST['units']);
        $instructions = array_map('sanitize', $_POST['instructions']);
        $tags = array_map('sanitize', $_POST['tags']);
        $images = $_FILES['images'];
        $UserID = $_SESSION['UserID'];

        if (empty($title) || empty($description) || empty($cuisine_name) || empty($ingredients) || empty($instructions) || empty($tags) || empty($images)) {
            $em = "All fields are required!";
            header("Location: ../html/create-post.php?error=$em");
            exit();
        }


        $imagePaths = handleFileUploads($images);


        $stmt = $mysqli->prepare("INSERT INTO recipe (UserID ,title, description, creation_date, Cuisine_name,
        inspo, skill_level, prep_time, cooking_time, serving_size,  calories, carbs,
         protein, fat) VALUES ($UserID, ?, ?, CURRENT_TIMESTAMP, ?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssssss", $title, $description, $cuisine_name,
         $inspo, $skill_level, $prep_time, $cooking_time, $serving_size, $calories,
          $carbs, $protein, $fat);
        $stmt->execute();
        $recipe_id = $mysqli->insert_id;


        foreach ($ingredients as $key => $ingredient) {
 
            $quantity = $quantities[$key];
            $unit = $units[$key];


            $stmt = $mysqli->prepare("INSERT INTO ingredient (RecipeID, ingredientName, Quantity, Unit) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $recipe_id, $ingredient, $quantity, $unit);
            $stmt->execute();
        }


        $stepNumber = 1;
        foreach ($instructions as $instruction) {
            $stmt = $mysqli->prepare("INSERT INTO instruction (RecipeID, StepNumber, InstructionText) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $recipe_id, $stepNumber, $instruction);
            $stmt->execute();
            $stepNumber++; 
        }

        // Insert tags
        foreach ($tags as $tag) {
            $stmt = $mysqli->prepare("INSERT INTO tag (RecipeID, tag_Name) VALUES (?, ?)");
            $stmt->bind_param("is", $recipe_id, $tag);
            $stmt->execute();
        }


        $imageNumber = 1;
        foreach ($imagePaths as $image_path) {

            // Insert thumbnail path
            $stmt = $mysqli->prepare("INSERT INTO recipe_images (RecipeID, thumbnail ,Image, ImageNumber) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $recipe_id, $thumbnail_path, $image_path, $imageNumber);

            $stmt->execute();
            $imageNumber++; 
        }

        header("Location: ../html/profile-page.php");
        exit();
    } else {

        $em = "Form submission error: form data not received.";
        header("Location: ../html/create-post.php?error=$em");
        exit();
    }
}