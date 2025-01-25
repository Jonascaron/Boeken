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

function book_picture($item, $book_id) {
    require 'database.php'; // Include database connection

    // Check if a file is uploaded
    if (isset($_FILES[$item])) {
        $img_name = $_FILES[$item]['name'];
        $tmp_name = $_FILES[$item]['tmp_name'];
        
        // Get the file extension and convert it to lowercase
        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        
        // Define allowed file types
        $allowed_exs = array('jpg', 'jpeg', 'png');
        
        if (in_array($img_ex_lc, $allowed_exs)) {
            try {
                // Single-line SQL query for readability
                $sql = "SELECT books.id AS book_id, books.name AS book_name, books.booknumber AS book_booknumber, series.id AS series_id, series.name AS series_name, series.path AS series_path 
                    FROM books 
                    LEFT JOIN series ON books.serie_id = series.id 
                    WHERE books.id = :id;";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
                $stmt->execute();
                $book = $stmt->fetch(PDO::FETCH_ASSOC);

                // Validate if the book exists
                if ($book) {
                    $path = $book['series_path'];
                    $db_name = 'Book'.$book['book_booknumber'];
                    $new_name = $db_name.'.'.$img_ex_lc;
                    $img_upload_path = 'image/' . $path . '/' . $new_name;

                    // Create the directory if it doesn't exist
                    if (!is_dir('image/' . $path)) {
                        mkdir('image/' . $path, 0777, true);
                    }

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($tmp_name, $img_upload_path)) {
                        // Optionally, update the database with the image name or path
                        $update_sql = "UPDATE books SET image = :image WHERE id = :id";
                        $update_stmt = $conn->prepare($update_sql);
                        $update_stmt->bindParam(':image', $db_name, PDO::PARAM_STR);
                        $update_stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
                        $update_stmt->execute();

                        return "File uploaded successfully!";
                    } else {
                        return "Failed to upload the file.";
                    }
                } else {
                    return "Book not found.";
                }
            } catch (PDOException $e) {
                return "Database error: " . $e->getMessage();
            }
        } else {
            return "Not an allowed file type. Only JPG, JPEG, and PNG are accepted.";
        }
    } else {
        return "No file uploaded.";
    }
}

function vardump($item) {
    var_dump($item);
    die;
}