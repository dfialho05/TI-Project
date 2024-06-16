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

$nomesSensoresAtuadores = array('temperatura', 'humidade', 'sensorNo3', 'atuadorNo1', 'atuadorNo2', 'ledArduino1', 'ledArduino2');

// Coloca no array $dadosGrafico os valores do sensor ou atuador para serem exibidos no gráfico
$dadosGrafico = [];
if (isset($_GET['nome']) && in_array($_GET['nome'], $nomesSensoresAtuadores)) {
    $nome = $_GET['nome'];
    $arquivo = "api/files/$nome/log.txt";
    
    if (file_exists($arquivo)) {
        $dados = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($dados as $linha) {
            list($data, $valor) = explode(';', $linha);
            // Convertendo "Ligado" e "Desligado" para 1 e 0 respectivamente
            if (trim(strtolower($valor)) == 'ligado') {
                $valor = 1;
            } elseif (trim(strtolower($valor)) == 'desligado') {
                $valor = 0;
            }
            $dadosGrafico[] = ['data' => $data, 'valor' => $valor];
        }
    } else {
        die("Arquivo não encontrado.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a class="nav-link active">Gráfico</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="clima.php">Clima</a>
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
        <h1>Grafico</h1>
        <br>
        <hr>
        <div class="container d-flex justify-content-center">
            <a href="grafico.php?nome=temperatura" class="btn btn-outline-info">Temperatura</a>
            <a href="grafico.php?nome=humidade" class="btn btn-outline-info button-margin">Humidade</a>
            <a href="grafico.php?nome=sensorNo3" class="btn btn-outline-info button-margin">Botão</a>
            <a href="grafico.php?nome=atuadorNo1" class="btn btn-outline-info button-margin">Ar Condicionado</a>
            <a href="grafico.php?nome=atuadorNo2" class="btn btn-outline-info button-margin">Portas</a>
            <a href="grafico.php?nome=ledArduino1" class="btn btn-outline-info button-margin">Alarme</a>
            <a href="grafico.php?nome=ledArduino2" class="btn btn-outline-info button-margin">Rega</a>
        </div>
        <hr>
        <br>

        <?php
        if (isset($_GET['nome'])) {
            $nome = $_GET['nome'];
            echo '<h2>';

            switch ($nome) {
                case 'temperatura':
                    echo 'Temperatura';
                    break;
                case 'humidade':
                    echo 'Humidade';
                    break;
                case 'sensorNo3':
                    echo 'Botão';
                    break;
                case 'atuadorNo1':
                    echo 'Ar Condicionado';
                    break;
                case 'atuadorNo2':
                    echo 'Portas';
                    break;
                case 'ledArduino1':
                    echo 'Alarme';
                    break;
                case 'ledArduino2':
                    echo 'Rega';
                    break;
            }

            echo '</h2>';
        }
        ?>
        
        <?php if (!empty($dadosGrafico)): ?>
            <canvas id="meuGrafico" width="400" height="200"></canvas>
            <script>
                var ctx = document.getElementById('meuGrafico').getContext('2d');
                var meuGrafico = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode(array_column($dadosGrafico, 'data')); ?>, // Datas no eixo X
                        datasets: [{
                            label: 'Valores',
                            data: <?php echo json_encode(array_column($dadosGrafico, 'valor')); ?>, // Valores no eixo Y
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                beginAtZero: true
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>

    <script src="scripts/scriptTheme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
