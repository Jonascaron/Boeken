<?php

    require 'database.php';
    require 'functions.php';

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: index.php');
        exit();
    }

    $book_id = $_GET['id'];
    $sql = "SELECT *, books.name AS book_name, series.name AS serie_name FROM books LEFT JOIN series ON books.serie_id = series.id WHERE books.id = :book_id;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    $audio_dir = "audio/" . $book['path'] . "/book" . $book['booknumber'] . "/";
    $audio_files = glob($audio_dir . "*.mp3");
    $audio_files = array_map('basename', $audio_files);

    include 'head.php';
?>
    <link rel="stylesheet" href="css/book.css">

    <title><?php echo $book['book_name']; ?></title>
</head>
<body> 
    <section class="container_book">
        <div class="book_details">
            <a href="index.php" class="back_link">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none">
                    <path d="M15.41 16.59L10.83 12L15.41 7.41L14 6L8 12L14 18L15.41 16.59Z" fill="var(--clr-logodegrijzejager);"/>
                </svg>
            </a>
            <h1 class="title"><?php echo $book['serie_name']; ?> - <?php echo $book['book_name']; ?> - Boek <?php echo $book['booknumber']; ?></h1>
        </div>
        <div class="spacer"></div>
        <div class="book_player">
            <audio id="audioPlayer"></audio>
            <div id="audioList" class="audio-list"></div>
            <footer class="spacer"></footer>
            <div class="custom_player">
                <input type="range" id="progressBar" class="progressBar" value="0" min="0" max="500" step="1">
                <div class="controls">
                    <div class="control_buttons">
                        <button id="rewindBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.99 1V5C16.41 5 19.99 8.58 19.99 13C19.99 17.42 16.41 21 11.99 21C7.57001 21 3.99001 17.42 3.99001 13H5.99001C5.99001 16.31 8.68001 19 11.99 19C15.3 19 17.99 16.31 17.99 13C17.99 9.69 15.3 7 11.99 7V11L6.99001 6L11.99 1ZM10.04 16H10.89V11.73H10.8L9.03 12.36V13.05L10.04 12.74V16ZM15.17 14.24C15.17 14.56 15.14 14.84 15.07 15.06C15 15.28 14.9 15.48 14.78 15.63C14.66 15.78 14.5 15.89 14.33 15.96C14.16 16.03 13.96 16.06 13.74 16.06C13.52 16.06 13.33 16.03 13.15 15.96C12.97 15.89 12.82 15.78 12.69 15.63C12.56 15.48 12.46 15.29 12.39 15.06C12.32 14.83 12.28 14.56 12.28 14.24V13.5C12.28 13.18 12.31 12.9 12.38 12.68C12.45 12.46 12.55 12.26 12.67 12.11C12.79 11.96 12.95 11.85 13.12 11.78C13.29 11.71 13.49 11.68 13.71 11.68C13.93 11.68 14.12 11.71 14.3 11.78C14.48 11.85 14.63 11.96 14.76 12.11C14.89 12.26 14.99 12.45 15.06 12.68C15.13 12.91 15.17 13.18 15.17 13.5V14.24ZM14.28 12.9C14.31 13.03 14.32 13.19 14.32 13.38H14.31V14.35C14.31 14.54 14.29 14.7 14.27 14.83C14.25 14.96 14.21 15.07 14.16 15.15C14.11 15.23 14.04 15.29 13.97 15.32C13.9 15.35 13.81 15.37 13.72 15.37C13.63 15.37 13.55 15.35 13.47 15.32C13.39 15.29 13.33 15.23 13.28 15.15C13.23 15.07 13.19 14.96 13.16 14.83C13.13 14.7 13.12 14.54 13.12 14.35V13.38C13.12 13.19 13.13 13.03 13.16 12.9C13.19 12.77 13.23 12.67 13.28 12.59C13.33 12.51 13.4 12.45 13.47 12.42C13.54 12.39 13.63 12.37 13.72 12.37C13.81 12.37 13.89 12.39 13.97 12.42C14.05 12.45 14.11 12.51 14.16 12.59C14.21 12.67 14.25 12.77 14.28 12.9Z"/>
                            </svg>
                        </button>
                        <button id="playPauseBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" id="playicon">
                                <path d="M8 5V19L19 12L8 5Z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" id="pauseicon" style="display: none;">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 19H6V5H10V19ZM14 19V5H18V19H14Z"/>
                            </svg>
                        </button>
                        <button id="forwardBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.31 19 18 16.31 18 13H20C20 17.42 16.42 21 12 21C7.58002 21 4 17.42 4 13C4 8.58 7.58002 5 12 5V1L17 6L12 11V7C8.69 7 6 9.69 6 13C6 16.31 8.69 19 12 19ZM10.9 11.73V16H10.05V12.74L9.03998 13.05V12.36L10.81 11.73H10.9ZM14.32 11.78C14.14 11.71 13.95 11.68 13.73 11.68C13.51 11.68 13.32 11.71 13.14 11.78C12.96 11.85 12.81 11.96 12.69 12.11C12.57 12.26 12.46 12.45 12.4 12.68C12.34 12.91 12.3 13.18 12.3 13.5V14.24C12.3 14.56 12.34 14.84 12.41 15.06C12.48 15.28 12.58 15.48 12.71 15.63C12.84 15.78 12.99 15.89 13.17 15.96C13.35 16.03 13.54 16.06 13.76 16.06C13.98 16.06 14.17 16.03 14.35 15.96C14.53 15.89 14.68 15.78 14.8 15.63C14.92 15.48 15.02 15.29 15.09 15.06C15.16 14.83 15.19 14.56 15.19 14.24V13.5C15.19 13.18 15.15 12.9 15.08 12.68C15.01 12.46 14.91 12.26 14.78 12.11C14.65 11.96 14.49 11.85 14.32 11.78ZM14.29 14.83C14.32 14.7 14.33 14.54 14.33 14.35H14.34V13.38C14.34 13.19 14.33 13.03 14.3 12.9C14.27 12.77 14.23 12.67 14.18 12.59C14.13 12.51 14.06 12.45 13.99 12.42C13.92 12.39 13.83 12.37 13.74 12.37C13.65 12.37 13.57 12.39 13.49 12.42C13.41 12.45 13.36 12.51 13.3 12.59C13.24 12.67 13.21 12.77 13.18 12.9C13.15 13.03 13.14 13.19 13.14 13.38V14.35C13.14 14.54 13.15 14.7 13.18 14.83C13.21 14.96 13.25 15.07 13.3 15.15C13.35 15.23 13.42 15.29 13.49 15.32C13.56 15.35 13.65 15.37 13.74 15.37C13.83 15.37 13.91 15.35 13.99 15.32C14.07 15.29 14.13 15.23 14.18 15.15C14.23 15.07 14.26 14.96 14.29 14.83Z"/>
                            </svg>
                        </button>
                        <div>
                            <span id="currentTime">00:00</span> / <span id="totalDuration">00:00</span>
                        </div>
                    </div>
                    <div class="volume_control">
                        <input type="range" id="volumeSlider" class="volumeSlider" min="0" max="1" step="0.01" value="1" style="width:80px;">
                        <label id="volumeLabel" for="volumeSlider" class="volume_icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" id="volumeupicon">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14 5.29001V3.23001C18.01 4.14001 21 7.72 21 12C21 16.28 18.01 19.86 14 20.77V18.71C16.89 17.85 19 15.17 19 12C19 8.83002 16.89 6.15002 14 5.29001ZM3 9V15H7L12 20V4L7 9H3ZM16.5 12C16.5 10.23 15.48 8.70999 14 7.97V16.02C15.48 15.29 16.5 13.77 16.5 12Z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" style="display: none;" id="volumedownicon">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5 15V9H9L14 4V20L9 15H5ZM16 7.97C17.48 8.70999 18.5 10.23 18.5 12C18.5 13.77 17.48 15.29 16 16.02V7.97Z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" style="display: none;" id="volumeofficon">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.92999 4.34L4.33997 2.92999L21.07 19.65L19.66 21.06L17.61 19.01C16.57 19.84 15.34 20.46 14 20.76V18.7C14.8 18.47 15.53 18.08 16.18 17.59L12 13.41V20L7 15H3V9H7L7.28998 8.69998L2.92999 4.34ZM18.59 14.34C18.85 13.61 19 12.82 19 12C19 8.82999 16.89 6.14999 14 5.28998V3.22998C18.01 4.13998 21 7.71997 21 12C21 13.39 20.68 14.7 20.12 15.87L18.59 14.34ZM12 4L10.12 5.88L12 7.76001V4ZM14 7.97C15.48 8.70999 16.5 10.23 16.5 12C16.5 12.08 16.49 12.16 16.48 12.24L14 9.76001V7.97Z"/>
                            </svg>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const audioFiles = <?php echo json_encode($audio_files); ?>;
        const audioDir = "<?php echo $audio_dir; ?>";
    </script>
    <script src="js/book.js"></script>
</body>