<?php
    header('Content-Type: text/html; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Diretoria onde os ficheiros serão armazenados
        $diretoria = __DIR__ . '/files/images/';

        function uploadFicheiro($arrayFiles, $diretoria, $allowed_types, $max_size_kb) {
            if (isset($_FILES[$arrayFiles]) && $_FILES[$arrayFiles]['error'] == 0) {
                $file_name = basename($_FILES[$arrayFiles]['name']);
                $destinoArquivo = $diretoria . $file_name;

                // Validação do tipo de ficheiro
                $file_type = strtolower(pathinfo($destinoArquivo, PATHINFO_EXTENSION));
                $file_size_kb = $_FILES[$arrayFiles]['size'] / 1024;

                if (in_array($file_type, $allowed_types) && $file_size_kb <= $max_size_kb) {
                    // Move o ficheiro para a diretoria de destino
                    if (move_uploaded_file($_FILES[$arrayFiles]['tmp_name'], $destinoArquivo)) {
                        echo "Ficheiro ". htmlspecialchars($file_name) . " enviado com sucesso!<br>";

                    } else {
                        echo "Erro ao enviar o ficheiro ". htmlspecialchars($file_name) . ".<br>";
                    }
                } else {
                    if (!in_array($file_type, $allowed_types)) {
                        echo "Tipo de ficheiro não permitido para o ficheiro ". htmlspecialchars($file_name) . ".<br>";
                    }
                    if ($file_size_kb > $max_size_kb) {
                        echo "O ficheiro ". htmlspecialchars($file_name) . " excede o tamanho máximo permitido de 1000kB.<br>";
                    }
                }
            } else {
                echo "Nenhum ficheiro foi enviado ou ocorreu um erro no envio para o ficheiro ". htmlspecialchars($arrayFiles) . ".<br>";
            }
            file_put_contents("files/images/valor.txt", 0);
        }

        // Tipos permitidos para a imagem
        $image_allowed_types = array('jpg', 'png');
        $max_size_kb = 1000; // Tamanho máximo em kB

        // Processa o upload da imagem
        uploadFicheiro('file', $diretoria, $image_allowed_types, $max_size_kb);

        // Tipos permitidos para o ficheiro de texto
        $text_allowed_types = array('txt');

        // Processa o upload do ficheiro de texto
        uploadFicheiro('hora', $diretoria, $text_allowed_types, $max_size_kb);
    }elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
        // confirma se o nome existe e se o ficheira existe e vai buscar o valor ao ficheiro

        if ($_GET['nome'] == "Impulso"){
            echo file_get_contents("files/images/valor.txt");
        }
    }else {
        http_response_code(403);
        echo "Método não permitido!";
    }
?>
