<?php
    require 'database.php';
    require 'functions.php';

    if ($_POST['serie'] == 'new_serie') {
        $name = $_POST['new_serie_name'];
        $path = $_POST['new_serie_path'];

        $sql = "INSERT INTO series (name, path) VALUES (:name, :path)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':path', $path, PDO::PARAM_STR);
        $stmt->execute();

        $serie_id = $conn->lastInsertId();
    }

    if ($_POST['serie'] == 'new_serie') {
        $serie = $serie_id;
    } else {
        $serie = $_POST['serie'];
    }

    $name = $_POST['name'];
    $booknumber = $_POST['booknumber'];

    $sql = "INSERT INTO books (name, booknumber, serie_id) VALUES (:name, :booknumber, :serie)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':booknumber', $booknumber, PDO::PARAM_INT);
    $stmt->bindParam(':serie', $serie, PDO::PARAM_INT);
    $stmt->execute();

    $book_id = $conn->lastInsertId();
    //echo book_picture($_FILES['image'], $book_id);
    if (!empty($_FILES['image'])) {
        $img_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        
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

                        echo "File uploaded successfully!";
                        header('Location: index.php');
                    } else {
                        echo "Failed to upload the file.";
                        header('Location: book_add.php');
                    }
                } else {
                    echo "Book not found.";
                    header('Location: book_add.php');
                }
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
                header('Location: book_add.php');
            }
        } else {
            echo "Not an allowed file type. Only JPG, JPEG, and PNG are accepted.";
            header('Location: book_add.php');
        }
    } else {
        echo "No file uploaded.";
        header('Location: book_add.php');
    }

    if (isset($_FILES['audioFiles'])) {	
        $sql = "SELECT books.id AS book_id, books.name AS book_name, books.booknumber AS book_booknumber, series.id AS series_id, series.name AS series_name, series.path AS series_path 
            FROM books 
            LEFT JOIN series ON books.serie_id = series.id 
            WHERE books.id = :id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $book_id, PDO::PARAM_INT);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        $path = $book['series_path'];

        $files = $_FILES['audioFiles'];
        $uploadDir = 'audio/' . $path . '/book' . $book['book_booknumber'] . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadResults = [];
        $allowedExtensions = ['mp3'];

        foreach ($files['tmp_name'] as $index => $tmpName) {
            $fileName = basename($files['name'][$index]);
            $filePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            if (!in_array(strtolower($fileExt), $allowedExtensions)) {
                $uploadResults[] = "File '$fileName' has an invalid extension.";
                continue;
            }

            if (move_uploaded_file($tmpName, $filePath)) {
                $uploadResults[] = "File '$fileName' uploaded successfully!";
            } else {
                $uploadResults[] = "Error uploading '$fileName'.";
            }
        }

        return $uploadResults;
    } else {
        return ["No files uploaded."];
    }
?>