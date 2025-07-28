<?php
    require 'database.php';
    require 'functions.php';

    $sql = "SELECT * FROM series";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
    include 'head.php';
?>
    <title>home</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="container_series">
        <?php foreach ($series as $serie) { 
            $serie_id = $serie['id'];

            $sql2 = "SELECT * FROM books WHERE serie_id = :serie_id ORDER BY booknumber ASC";

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
                        <a class="book" href="book.php?id=<?php echo $book['id'] ?>">
                            <img src="image/<?php echo $serie['path'] ?>/<?php echo $book['image'] ?>.png">
                            <div>
                                <p><?php echo $serie['name'] ?> - Boek <?php echo $book['booknumber'] ?> - <?php echo $book['name'] ?></p>
                            </div>
                        </a>
                    <?php } else {
                        $books = $stmt2->fetchall(PDO::FETCH_ASSOC);
                        foreach ($books as $book) { ?>
                            <a class="book" href="book.php?id=<?php echo $book['id'] ?>">
                                <img src="image/<?php echo $serie['path'] ?>/<?php echo $book['image'] ?>.png">
                                <div>
                                    <p><?php echo $serie['name'] ?> - Boek <?php echo $book['booknumber'] ?> - <?php echo $book['name'] ?></p>
                                </div>
                            </a>
                    <?php }} ?>
                </div>
            </div>
        <?php } ?>
    </section>
<?php include 'footer.php'; ?>