<?php

    header("refresh:60;url=dashboard.php");

    session_start();  
    if(!isset($_SESSION['account']) ){  
        header( "refresh:2;url=index.php");
        die("Acesso restrito.");
    }
    
    else {
      $fileSettings = 'api/files/accounts/accountsSettings.txt';
  
      if (file_exists($fileSettings)) {
          $file = fopen($fileSettings, 'r');
  
          while (!feof($file)) {
              $line = fgets($file);
              $parts = explode(';', $line);
  
              if ($_SESSION['account']['username'] == $parts[0]) {
                $_SESSION['account']['permission'] = $parts[1];
                  break;
              }
          }
  
          fclose($file);
      }
  }

    $_SESSION['account']['permission'] = str_replace("\n", "", $_SESSION['account']['permission']);

    $fileTheme = 'api/files/theme/theme.txt';

    if (file_exists($fileTheme)) {
      $file = fopen($fileTheme, 'r');

      $theme = fgets($file);
    }
    fclose($file);

?>

<?php
    $valor_temperatura = file_get_contents("api/files/temperatura/valor.txt");
    $hora_temperatura = file_get_contents("api/files/temperatura/hora.txt");
    $nome_temperatura = file_get_contents("api/files/temperatura/nome.txt");

    $valor_humidade = file_get_contents("api/files/humidade/valor.txt");
    $hora_humidade = file_get_contents("api/files/humidade/hora.txt");
    $nome_humidade = file_get_contents("api/files/humidade/nome.txt");

    $valor_sensorNo3 = file_get_contents("api/files/sensorNo3/valor.txt");
    $hora_sensorNo3 = file_get_contents("api/files/sensorNo3/hora.txt");
    $nome_sensorNo3 = file_get_contents("api/files/sensorNo3/nome.txt");
  
    $valor_ledArduino1 = file_get_contents("api/files/ledArduino1/valor.txt");
    $hora_ledArduino1 = file_get_contents("api/files/ledArduino1/hora.txt");
    $nome_ledArduino1 = file_get_contents("api/files/ledArduino1/nome.txt");

    $valor_ledArduino2 = file_get_contents("api/files/ledArduino2/valor.txt");
    $hora_ledArduino2 = file_get_contents("api/files/ledArduino2/hora.txt");
    $nome_ledArduino2 = file_get_contents("api/files/ledArduino2/nome.txt");

    $valor_atuadorNo1 = file_get_contents("api/files/atuadorNo1/valor.txt");
    $hora_atuadorNo1 = file_get_contents("api/files/atuadorNo1/hora.txt");
    $nome_atuadorNo1 = file_get_contents("api/files/atuadorNo1/nome.txt");

    $valor_atuadorNo2 = file_get_contents("api/files/atuadorNo2/valor.txt");
    $hora_atuadorNo2 = file_get_contents("api/files/atuadorNo2/hora.txt");
    $nome_atuadorNo2 = file_get_contents("api/files/atuadorNo2/nome.txt");

    $hora_image = file_get_contents("api/files/images/hora.txt");
    
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plataforma IoT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
  </head>
  <body >
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
              <span class="nav-link active" aria-current="page">Home</span>
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

    <!-- Titulo -->

    <div class="container d-flex justify-content-around align-items-center">
        <div id="title-header">
            <h1>Servidor IoT</h1>
            <h6><?php echo "User: " . $_SESSION['account']['username'] ?></h6>
        </div>
        <div class="darkImgs">
          <img src="imgs/estg.png" alt="" width="300">
        </div>
    </div>

    <hr>

    <!--                         SENSORES                        -->

    <div class="container d-flex justify-content-around align-items-center">      
      <div>
          <h2>Sensores</h2>
          <br>
      </div>
    </div>

    <div class="container">
          
        <div class="row justify-content-center" >
            <div class="col-sm-4" >
                <div class="card text-center">
                    <div class="card-header sensor"><b>Temperatura: <?php echo $valor_temperatura; ?>º</b></div>
                    <div class="card-body">
                      <?php
                        if($valor_temperatura < 60 && $valor_temperatura > 30){
                          echo '<img src="imgs/temperature-low.png" alt="Low temperature" class="darkImgs">';
                        }else{
                          echo '<img src="imgs/temperature-high.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_temperatura); ?> - <a href="historico.php?nome=temperatura">Histórico</a></div>
                  </div>
            </div>
            <div class="col-sm-4" >
                <div class="card text-center">
                    <div class="card-header sensor"><b>Humidade: <?php echo $valor_humidade; ?>%</b></div>
                    <div class="card-body">
                      <?php
                        if($valor_humidade < 60 && $valor_humidade > 30){
                          echo '<img src="imgs/humidity-low.png" alt="" class="darkImgs">';
                        }else{
                          echo '<img src="imgs/humidity-high.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_humidade); ?> - <a href="historico.php?nome=humidade">Histórico</a></div>
                  </div>
            </div>
            <div class="col-sm-4" >
                <div class="card text-center">
                    <div class="card-header sensor"><b>Botão: <?php echo $valor_sensorNo3; ?></b></div>
                    <div class="card-body">
                      <?php
                        if($valor_sensorNo3 == 0){
                          echo '<img src="imgs/TODO-OFF.png" alt="">';
                        }else{
                          echo '<img src="imgs/TODO-ON.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_sensorNo3); ?>- <a href="historico.php?nome=sensorNo3">Histórico</a></div>
                  </div>
            </div>
        </div>

    </div>

      <br>

    <!-- Tabela de Sensores-->


    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">Tabela de Sensores</div>
            <div class="card-body">
              <table class="table table-bordered;table">
                <tr>
                  <th>Tipo de dispositivo IoT  </th>
                  <th>Valor</th>
                  <th>Data de atualização</th>
                  <th>Estado Alertas</th>
                </tr>
                <tr>
                  <td><?php echo($nome_temperatura)?></td>
                  <td><?php echo($valor_temperatura)?>º</td>
                  <td><?php echo($hora_temperatura)?></td>
                  <td>
                    <?php
                      if($valor_temperatura < 60 && $valor_temperatura > 30){
                        echo "<span class='badge rounded-pill text-bg-primary'>Normal</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-danger'>Elevado</span>";
                      }
                    ?>
                  </td>
                </tr>
      
                <tr>
                  <td><?php echo($nome_humidade)?></td>
                  <td><?php echo($valor_humidade)?>%</td>
                  <td><?php echo($hora_humidade)?></td>
                  <td>
                    <?php
                      if($valor_humidade < 60 && $valor_humidade > 30){
                        echo "<span class='badge rounded-pill text-bg-primary'>Normal</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-danger'>Elevado</span>";
                      }
                    ?>
                  </td>
                </tr>
    
                <tr>
                  <td><?php echo($nome_sensorNo3)?></td>
                  <td><?php echo($valor_sensorNo3)?></td>
                  <td><?php echo($hora_sensorNo3)?></td>
                  <td>
                    <?php
                      if($valor_sensorNo3 == 0){
                        echo "<span class='badge rounded-pill text-bg-primary'>Normal</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-danger'>Elevado</span>";
                      }
                    ?>
                  </td>
                </tr>
              </table>
            </div>
            
          </div>
        </div>
      </div>
    </div>

    <br>

    <!--                         ATUADORES                        -->

    <div class="container d-flex justify-content-around align-items-center">      
      <div>
          <h2>Atuadores</h2>
          <br>
      </div>
    </div>

    <div class="container">
      <div class="row justify-content-center" >
            <div class="col-sm-3" >
                <div class="card text-center">
                    <div class="card-header atuador"><b>Ar Condicionado: <?php echo $valor_atuadorNo1; ?></b></div>
                    <div class="card-body">
                      <?php
                        if($valor_atuadorNo1 == "Desligado"){
                          echo '<img src="imgs/light-off.png" alt="" class="darkImgs">';
                        }else{
                          echo '<img src="imgs/light-on.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_atuadorNo1); ?> - <a href="historico.php?nome=atuadorNo1">Histórico</a></div>
                  </div>
            </div>
            <div class="col-sm-3" >
                <div class="card text-center">
                  <div class="card-header atuador"><b id="valorAtuador2" data-valor="<?php echo $valor_atuadorNo2; ?>">Portas: <?php echo $valor_atuadorNo2; ?></b></div>
                    <div class="card-body">
                      <?php
                        if($valor_atuadorNo2 == "Desligado"){
                          echo '<img src="imgs/light-off.png" alt="" class="darkImgs">';
                        }else{
                          echo '<img src="imgs/light-on.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_atuadorNo2); ?> - <a href="historico.php?nome=atuadorNo2">Histórico</a></div>
                    <?php
                        if ($_SESSION['account']['permission'] != "viewer") {
                          echo '<div class="card-footer">';
                          echo '<button type="button" class="btn btn-primary btn-lg btn-block" id="btnAtuador2">TOGGLE</button>';
                          echo '</div>';
                      }
                    ?>
                  </div>
            </div>
            <div class="col-sm-3" >
                <div class="card text-center">
                    <div class="card-header atuador"><b>Alarme: <?php echo $valor_ledArduino1; ?></b></div>
                    <div class="card-body">
                      <?php
                        if($valor_ledArduino1 == "Desligado"){
                          echo '<img src="imgs/light-off.png" alt="" class="darkImgs">';
                        }else{
                          echo '<img src="imgs/light-on.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_ledArduino1); ?>- <a href="historico.php?nome=ledArduino1">Histórico</a></div>
                  </div>
            </div>
            <div class="col-sm-3" >
                <div class="card text-center">
                  <div class="card-header atuador"><b id="valorLedArduino2" data-valor="<?php echo $valor_ledArduino2; ?>">Rega: <?php echo $valor_ledArduino2; ?></b></div>
                    <div class="card-body">
                      <?php
                        if($valor_ledArduino2 == "Desligado"){
                          echo '<img src="imgs/light-off.png" alt="" class="darkImgs">';
                        }else{
                          echo '<img src="imgs/light-on.png" alt="">';
                        }
                      ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_ledArduino2); ?> - <a href="historico.php?nome=ledArduino2">Histórico</a></div>
                    
                    <?php
                        if ($_SESSION['account']['permission'] != "viewer") {
                          echo '<div class="card-footer">';
                          echo '<button type="button" class="btn btn-primary btn-lg btn-block" id="btnLedArduino2">TOGGLE</button>';
                          echo '</div>';
                      }
                    ?>
                  </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Tabela de Atuadores-->


    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">Tabela de Atuadores</div>
            <div class="card-body">
              <table class="table table-bordered;table">
                <tr>
                  <th>Tipo de dispositivo IoT  </th>
                  <th>Valor</th>
                  <th>Data de atualização</th>
                  <th>Estado Alertas</th>
                </tr>
                <tr>
                  <td><?php echo($nome_atuadorNo1)?></td>
                  <td><?php echo($valor_atuadorNo1)?></td>
                  <td><?php echo($hora_atuadorNo1)?></td>
                  <td>
                    <?php
                      if($valor_atuadorNo1 == "Ligado"){
                        echo "<span class='badge rounded-pill text-bg-success'>Ativo</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-warning'>Inativo</span>";
                      }  
                    ?>
                  </td>
                </tr>
      
                <tr>
                  <td><?php echo($nome_atuadorNo2)?></td>
                  <td><?php echo($valor_atuadorNo2)?></td>
                  <td><?php echo($hora_atuadorNo2)?></td>
                  <td>
                    <?php
                      if($valor_atuadorNo2 == "Ligado"){
                        echo "<span class='badge rounded-pill text-bg-success'>Ativo</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-warning'>Inativo</span>";
                      }  
                    ?>
                  </td>
                </tr>
    
                <tr>
                  <td><?php echo($nome_ledArduino1)?></td>
                  <td><?php echo($valor_ledArduino1)?></td>
                  <td><?php echo($hora_ledArduino1)?></td>
                  <td>
                    <?php
                      if($valor_ledArduino1 == "Ligado"){
                        echo "<span class='badge rounded-pill text-bg-success'>Ativo</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-warning'>Inativo</span>";
                      }  
                    ?>
                  </td>
                </tr>
                    
                <tr>
                  <td><?php echo($nome_ledArduino2)?></td>
                  <td><?php echo($valor_ledArduino2)?></td>
                  <td><?php echo($hora_ledArduino2)?></td>
                  <td>
                    <?php
                      if($valor_ledArduino2 == "Ligado"){
                        echo "<span class='badge rounded-pill text-bg-success'>Ativo</span>";
                      }else{
                        echo "<span class='badge rounded-pill text-bg-warning'>Inativo</span>";
                      }  
                    ?>
                  </td>
                </tr>
                
              </table>
            </div>
            
          </div>
        </div>
      </div>
    </div>

    <br>

    <!-- Camera-->

    <div class="container d-flex justify-content-around align-items-center">      
      <div>
          <h2>Camera</h2>
          <br>
      </div>
    </div>

    <div class="container">
          
        <div class="row justify-content-center" >
            <div class="col-sm-4" >
                <div class="card text-center">
                    <div class="card-header sensor"><b>Webcam</b></div>
                    <div class="card-body">
                    <?php echo "<img src='api/files/images/webcam.jpg?id=".time()."' style='width:100%'>"; ?>
                    </div>
                    <div class="card-footer"><b>Atualização: </b><?php echo ($hora_image); ?> - <a href="historicoimages.php">Histórico</a></div>
                    
                    <?php
                        if ($_SESSION['account']['permission'] != "viewer") {
                          echo '<div class="card-footer">';
                          echo '<a href="usarCamera.php" class="btn btn-primary btn-lg btn-block">TOGGLE</a>';
                          echo '</div>';
                      }
                    ?>
                  </div>
            </div>
        </div>

    <script src="scripts/scriptHTTP.js"></script>
    <script src="scripts/scriptTheme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    </script>
    
  </body>
</html>
