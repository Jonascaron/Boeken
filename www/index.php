<?php
    require 'database.php';
    require 'functions.php';

    $sql = "SELECT * FROM series";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->rowCount();
    $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/book_container.css">

    <title>Boeken</title>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <p>Flanagan</p>
        </div>
    </nav>
    <div class="spacer"></div>
    <section class="container_series">
        <?php foreach ($series as $serie) { 
            $serie_id = $serie['id'];

            $sql2 = "SELECT * FROM books WHERE serie_id = :serie_id";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindParam(':serie_id', $serie_id);
            $stmt2->execute();
        ?>
            <div class="container_serie">
                <div class="logo">
                    <p><?php echo $serie['name'] ?></p>
                </div>
                <div class="books">
                    <?php if ($stmt2->rowCount() == 1) { 
                        $book = $stmt2->fetch(PDO::FETCH_ASSOC);
                    ?>
                        <a class="book" href="">
                            <img src="image/<?php echo $serie['path'] ?>/<?php echo $book['image'] ?>.png">
                            <div>
                                <p><?php echo $serie['name'] ?> - boek <?php echo $book['booknumber'] ?> - <?php echo $book['name'] ?></p>
                            </div>
                        </a>
                    <?php } else {
                        $books = $stmt2->fetchall(PDO::FETCH_ASSOC);
                        foreach ($books as $book) { ?>
                            <a class="book" href="">
                                <img src="image/<?php echo $serie['path'] ?>/<?php echo $book['image'] ?>.png">
                                <div>
                                    <p><?php echo $serie['name'] ?> - boek <?php echo $book['booknumber'] ?> - <?php echo $book['name'] ?></p>
                                </div>
                            </a>
                    <?php }} ?>
                </div>
            </div>
        <?php } ?>
    </section>
<?php include 'footer.php'; ?>