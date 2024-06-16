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

// Definir a API
$apiKey = "a8f813a25eeb4a1fa4e174014240906";
$city = "Leiria";
$apiUrl = "http://api.weatherapi.com/v1/forecast.json?key={$apiKey}&q={$city}&days=1&aqi=no&alerts=no";

// Faz a solicitação para a API
$response = file_get_contents($apiUrl);

// Verifique se a solicitação foi bem-sucedida
if ($response === FALSE) {
    die('Error occurred while fetching weather data.');
}

// Decodifica a resposta JSON
$data = json_decode($response, true);

// Verifique se os dados foram recebidos corretamente
if (!isset($data['forecast'])) {
    die('Failed to retrieve data: ' . $data['error']['message']);
}

// Faz a previsao do tempo
$forecastList = $data['forecast']['forecastday'][0]['hour'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

    <?php
    if ($theme == 'dark') {
        echo '<input type="checkbox" id="darkmode" checked hidden>';
    }
    ?>
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
                        <a class="nav-link" aria-current="page" href="historicoimages.php">Histórico Imagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="grafico.php">Gráfico</a>
                    </li>      
                    <li class="nav-item">
                        <a class="nav-link active">Clima</a>
                    </li>   
                </ul>

                <div class="button-container">
                    <a href="theme.php" id="themeButton" class="button-link">
                        <?php
                        if ($theme == "dark") {
                            echo '<img src="assets/imgs/sun.png" alt="Dark Mode" class="btn-img">';
                        } else {
                            echo '<img src="assets/imgs/moon.png" alt="Dark Mode" class="btn-img">';
                        }
                        ?>
                    </a>
                    <a href="logout.php" class="btn btn-outline-success">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">

        <br>
        <h1>Previsão do Tempo para <?php echo $city; ?></h1>
        <br>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Temperatura (°C)</th>
                    <th>Descrição</th>
                    <th>Humidade (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($forecastList as $forecast) {
                    $time = date("H:i", strtotime($forecast['time']));
                    $temp = $forecast['temp_c'];
                    $description = ucfirst($forecast['condition']['text']);
                    $humidity = $forecast['humidity'];

                    echo "<tr>";
                    echo "<td>{$time}</td>";
                    echo "<td>{$temp}</td>";
                    echo "<td>{$description}</td>";
                    echo "<td>{$humidity}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="scripts/scriptTheme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
