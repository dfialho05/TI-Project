<?php 
    //if (isset($_GET['location'])) {
        session_start();

        
        $fileTheme = 'api/files/theme/theme.txt';

        if (file_exists($fileTheme)) {
            $file = fopen($fileTheme, 'r');
            $theme = fgets($file);
            fclose($file); // Close the file after reading
        }

        if ($theme == 'dark') { // Enclose 'dark' and 'white' in quotes
            file_put_contents($fileTheme, "white");
        } else {
            file_put_contents($fileTheme, "dark");
        }
    
        //$location = $_GET['location'];
        header("location: {$_SERVER['HTTP_REFERER']}");
    //}
?>