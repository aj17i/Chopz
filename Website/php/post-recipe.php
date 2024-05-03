<?php
// Include your database connection file
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

        // Function to handle file uploads
        function handleFileUploads($files)
        {
            $uploadedPaths = array();
            foreach ($files['name'] as $key => $name) {
                $img_name = sanitize($name);
                $img_size = $files['size'][$key];
                $tmp_name = $files['tmp_name'][$key];
                $error = $files['error'][$key];

                if ($error === 0) {
                    if ($img_size > 125000) {
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
        // Sanitize inputs
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $cuisine_name = sanitize($_POST['cuisine_name']);
        $ingredients = array_map('sanitize', $_POST['ingredients']);
        $instructions = array_map('sanitize', $_POST['instructions']);
        $tags = array_map('sanitize', $_POST['tags']);
        $images = $_FILES['images'];
        $UserID = $_SESSION['UserID'];

        // Check if any field is empty
        if (empty($title) || empty($description) || empty($cuisine_name) || empty($ingredients) || empty($instructions) || empty($tags) || empty($images)) {
            $em = "All fields are required!";
            header("Location: ../html/create-post.php?error=$em");
            exit();
        }

        // Handle file uploads
        $imagePaths = handleFileUploads($images);

        // Insert into database
        $stmt = $mysqli->prepare("INSERT INTO recipe (UserID ,title, description, creation_date, Cuisine_name) VALUES ($UserID, ?, ?, CURRENT_TIMESTAMP, ?)");
        $stmt->bind_param("sss", $title, $description, $cuisine_name);
        $stmt->execute();
        $recipe_id = $mysqli->insert_id;

        // Insert ingredients
        foreach ($ingredients as $ingredient) {
            $stmt = $mysqli->prepare("INSERT INTO ingredient (RecipeID, ingredientName) VALUES (?, ?)");
            $stmt->bind_param("is", $recipe_id, $ingredient);
            $stmt->execute();
        }

        // Insert instructions
        $stepNumber = 1;
        foreach ($instructions as $instruction) {
            $stmt = $mysqli->prepare("INSERT INTO instruction (RecipeID, StepNumber, InstructionText) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $recipe_id, $stepNumber, $instruction);
            $stmt->execute();
            $stepNumber++; // Increment step number for next instruction
        }

        // Insert tags
        foreach ($tags as $tag) {
            $stmt = $mysqli->prepare("INSERT INTO tag (RecipeID, tag_Name) VALUES (?, ?)");
            $stmt->bind_param("is", $recipe_id, $tag);
            $stmt->execute();
        }

        // Insert image paths into the database
        $imageNumber = 1; // Initialize image number
        foreach ($imagePaths as $image_path) {
            $stmt = $mysqli->prepare("INSERT INTO recipe_images (RecipeID, Image, ImageNumber) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $recipe_id, $image_path, $imageNumber);
            $stmt->execute();
            $imageNumber++; // Increment image number for next image
        }

        // Redirect to success page
        header("Location: ../html/profile-page.php");
        exit();
    } else {

        $em = "Form submission error: form data not received.";
        header("Location: ../html/create-post.php?error=$em");
        exit();
    }
}