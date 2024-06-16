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
                        <a class="nav-link active">Histórico</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="historicoimages.php">Histórico Imagens</a>
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
        <h1>Histórico</h1>
        <br>
        <hr>
        <div class="container d-flex justify-content-center">
            <a href="historico.php?nome=temperatura" class="btn btn-outline-info">Temperatura</a>
            <a href="historico.php?nome=humidade" class="btn btn-outline-info button-margin">Humidade</a>
            <a href="historico.php?nome=sensorNo3" class="btn btn-outline-info button-margin">Botão</a>
            <a href="historico.php?nome=atuadorNo1" class="btn btn-outline-info button-margin">Ar condicionado</a>
            <a href="historico.php?nome=atuadorNo2" class="btn btn-outline-info button-margin">Portas</a>
            <a href="historico.php?nome=ledArduino1" class="btn btn-outline-info button-margin">Alarme</a>
            <a href="historico.php?nome=ledArduino2" class="btn btn-outline-info button-margin">Rega</a>
        </div>
        <hr>
        <br>

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
        <br>
        <table class="table">
            <?php
            if (isset($_GET['nome']) && in_array($_GET['nome'], $nomesSensoresAtuadores)) {
                echo "<thead>
                      <tr>
                          <th>#</th>
                          <th>Hora</th>
                          <th>Valor</th>
                      </tr>
                    </thead>";
            }
            ?>

            <tbody>
                <?php

                if (isset($nome)) {
                    $filename = "api/files/{$nome}/log.txt";
                    $times = 0;

                    if (file_exists($filename) && ($file = fopen($filename, "r"))) {
                        while (($line = fgets($file))) {

                            $data = explode(";", trim($line));
                            if (count($data) == 2) {
                                $time = $data[0];
                                $temperature = $data[1];
                                $times++;
                                echo "<tr>";
                                echo "<td>$times</td>";
                                echo "<td>$time</td>";
                                echo "<td>$temperature</td>";
                                echo "</tr>";
                            } else {
                                echo "<tr><td colspan='3' class='text-danger'>Itens não introduzidos na sua totalidade.</td></tr>";
                            }
                        }
                        fclose($file);
                    }
                } else {
                    foreach ($nomesSensoresAtuadores as $nomeSensorAtuador) {
                        $filename = "api/files/{$nomeSensorAtuador}/log.txt";
                        $times = 0;

                        if (file_exists($filename) && ($file = fopen($filename, "r"))) {
                            echo "<tr>";
                            echo '<th class="custom-header" colspan="3">'; // Ajuste aqui, adicionando colspan="3"
                            switch ($nomeSensorAtuador) {
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
                            echo "</th>";
                            echo "</tr>";

                            echo "<tr>";
                            echo "<th>#</th>";
                            echo "<th>Hora</th>";
                            echo "<th>Valor</th>";
                            echo "</tr>";

                            while (($line = fgets($file))) {
                                $data = explode(";", trim($line));
                                if (count($data) == 2) {
                                    $time = $data[0];
                                    $value = $data[1];
                                    $times++;
                                    echo "<tr>";
                                    echo "<td>$times</td>";
                                    echo "<td>$time</td>";
                                    echo "<td>$value</td>";
                                    echo "</tr>";
                                } else {
                                    echo "<tr><td colspan='3' class='text-danger'>Itens não introduzidos na sua totalidade.</td></tr>";
                                }
                            }
                            fclose($file);
                        }
                    }
                }

                ?>
            </tbody>
        </table>
    </div>

    <!-- Espaço entre as tabelas -->
    <div style="margin-bottom: 20px;"></div>

    <script src="scripts/scriptTheme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
