<?php
    require 'database.php';
    require 'functions.php';

    $sql = "SELECT * FROM series";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $series = $stmt->fetchAll(PDO::FETCH_ASSOC);

    include 'head.php';
?>
    <link rel="stylesheet" href="css/book_add.css">
    <title>boeken</title>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="form_container">
        <div class="form_box">
            <h1 class="title">boek toevoegen</h1>
            <form action="book_add_process.php" method="post" enctype="multipart/form-data" class="form" autocomplete="off">
                <div class="input_group">
                    <input type="text" name="name" id="name" placeholder="boek naam" required>
                </div>
    	        <div class="input_group">
                    <input type="number" name="booknumber" id="booknumber" placeholder="boek nummer" required>
                </div>
                <div class="input_group_select">
                    <select name="serie" id="serie" required>
                        <option value="new_serie" id="new_serie" selected>neiuwe boek serie</option>
                        <?php foreach ($series as $serie) { ?>
                            <option value="<?php echo $serie['id'] ?>" id="<?php echo $serie['path'] ?>"><?php echo $serie['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="input_group" id="new_serie_input">
                    <input type="text" name="new_serie_name" id="new_serie_name" placeholder="naam nieuwe serie" style="display: inline;" required>
                </div>
                <div class="input_group" id="new_serie_input">
                    <input type="text" name="new_serie_path" id="new_serie_path" placeholder="pad nieuwe serie" style="display: inline;" required>
                </div>
                <div class="input_group">
                    <input type="file" name="image" id="image" required>
                </div>
                <div class="input_group">
                    <input type="file" name="audioFiles[]" id="audioFiles" webkitdirectory directory multiple required>
                </div>
                <div class="input_group">
                    <button type="submit">submit</button>
                </div>
            </form>
        </div>
    </section>
<?php include 'footer.php'; ?>