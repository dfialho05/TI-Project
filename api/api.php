<?php
    header('Content-Type: text/html; charset=utf-8');

    // Recebe o request do servidor verifica o tipo de request

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // confirma se o nome existe e confirma se a pasta com esse nome existe e se sim coloca nos ficheiros

        if(isset($_POST['nome'])){
            $filename="files/{$_POST['nome']}";
            if (file_exists($filename)){
                file_put_contents("files/{$_POST['nome']}/valor.txt", $_POST['valor']);
                file_put_contents("files/{$_POST['nome']}/hora.txt", $_POST['hora']);
                file_put_contents("files/{$_POST['nome']}/log.txt", $_POST['hora'] . ";" . $_POST['valor'] . "\n", FILE_APPEND);
            }else{
                echo "Ficheiro nao existe. Sensor/Atuador não conhecido.";
                http_response_code(404);
            }
        }else{
            http_response_code(400);
            echo "Nome do Sensor/Atuador não definido";
        }
      }
    elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
        // confirma se o nome e o ficheira existe e vai buscar o valor ao ficheiro

        if (!empty($_GET['nome'] )){
            $filename="files/{$_GET['nome']}";
            if (file_exists($filename)){
                echo file_get_contents("files/{$_GET['nome']}/valor.txt");
            }else{
                echo "Ficheiro nao existe. Sensor/Atuador não conhecido.";
                http_response_code(404);

            }
        } else {
            http_response_code(400);
            echo "Nome do Sensor/Atuador não definido";
        }
    }
    else{
        http_response_code(403);
        echo "Método não permitido!";
    }
?>