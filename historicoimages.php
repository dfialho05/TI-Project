<?php
require 'api/Accounts.php';

session_start();

if (!isset($_SESSION['account'])) {
    header("refresh:2;url=index.php");
    die("Acesso restrito.");
}

$_SESSION['account']['permission'] = str_replace("\n", "", $_SESSION['account']['permission']);

if ($_SESSION['account']['permission'] != "root") {
    header("refresh:2;url=dashboard.php");
    die("Permission not granted.");
}

$fileTheme = 'api/files/theme/theme.txt';

if (file_exists($fileTheme)) {
    $file = fopen($fileTheme, 'r');
    $theme = fgets($file);
    fclose($file);
}

$imageFolder = 'api/files/historicoImages/';

// Obtem todas as imagens da pasta
$images = glob($imageFolder . '*.{jpg,png}', GLOB_BRACE);

// Ordena as imagens por data de modificação
usort($images, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
    <style>
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .gallery div {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .gallery img {
            max-width: 200px;
            max-height: 200px;
            border: 5px;
            padding: 5px;
        }
    </style>
  </head>
  <body>
    <?php
      if ($theme == 'dark') {
        echo '<input type="checkbox" id="darkmode" checked hidden>';
      }
    ?>

  <!-- Navbar -->

    <nav class="navbar navbar-expand-lg bg-body-tertiary navbar-light">
      <div class="container-fluid">
        <a class="navbar-brand">Dashboard EI-TI</a>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="dashboard.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="historico.php">Histórico</a>
            </li>       
            <li class="nav-item">
              <a class="nav-link active">Histórico Imagens</a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" href="grafico.php">Gráfico</a>
            </li>     
            <li class="nav-item">
              <a class="nav-link" href="clima.php">Clima</a>
            </li>     
          </ul>
          

          <div class="button-container">
            <a href="theme.php" id="themeButton" class="button-link">
              <img src="assets/imgs/moon.png" alt="Light Mode" class="btn-img">
            </a>

            <a href="logout.php" class="btn btn-outline-success">Logout</a>
          </div>

        </div>
      </div>
    </nav>

    <div class="container">
        <br>
        <h1>Histórico de Imagens</h1>
        <div class="gallery">
            <?php
            if (count($images) > 0) {
              foreach ($images as $image) {
                echo '<div><img src="' . $image . '" alt="Imagem"></div>';
              }
            } else {
                echo '<p>Não foram encontradas imagens na pasta</p>';
            }
            ?>
        </div>
    </div>

    <script src="scripts/scriptHTTP.js"></script>
    <script src="scripts/scriptTheme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
  </body>
</html>
