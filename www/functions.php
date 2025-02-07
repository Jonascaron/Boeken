<?php

function profile_picture($item) {
    require 'database.php';
    if (isset($_FILES[$item])) {
        $img_name = $_FILES[$item]['name'];
        $tmp_name = $_FILES[$item]['tmp_name'];
        $id = $_SESSION['id']; // Check if a valid user session exists.

        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array('jpg', 'jpeg', 'png');

        if (in_array($img_ex_lc, $allowed_exs)) {
            $db_name = uniqid("IMG-", true);
            $new_name = $db_name.'.'.$img_ex_lc;
            $img_upload_path = 'image/users/';

            // Check if the directory exists, and create it if not
            if (!is_dir($img_upload_path)) {
                mkdir($img_upload_path, 0777, true); // Recursively create directories with full permissions
            }

            $full_path = $img_upload_path.$new_name;
            if (move_uploaded_file($tmp_name, $full_path)) {
                $sql = "UPDATE users SET image = :image WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindparam(':image', $db_name);
                $stmt->bindparam(':id', $id);
                if ($stmt->execute()) {
                    echo "Image uploaded successfully!";
                } else {
                    echo "An error occurred while updating the database.";
                }
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        }
    } else {
        echo "No file uploaded.";
    }
}

function vardump($item) {
    var_dump($item);
    die;
}