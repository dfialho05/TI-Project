<?php
    ob_start();
    
    // Caminhos dos arquivos
    $horaFile = __DIR__ . '/api/files/images/hora.txt';
    $webcamFile = __DIR__ . '/api/files/images/webcam.jpg';
    $historicoPasta = __DIR__ . '/api/files/historicoImages/';
    $valorFile = __DIR__ . '/api/files/images/valor.txt';
    
    // Verificar se os arquivos existem
    if (file_exists($horaFile) && file_exists($webcamFile)) {  
        echo "Arquivos encontrados.<br>";

        // Ler o conteúdo do arquivo hora.txt
        $hora = trim(file_get_contents($horaFile));
        
        // Substituir caracteres inválidos
        $hora = preg_replace('/[^\w\-]/', '_', $hora);

        // Novo nome da imagem
        $novoNomeImagem = $historicoPasta . $hora . '.jpg';
        
        // Verificar se a pasta historicoImages existe
        if (!is_dir($historicoPasta)) {
            mkdir($historicoPasta, 0777, true);
        }

        // Verificar se a imagem já existe
        if (file_exists($novoNomeImagem)) {
            echo "O arquivo $novoNomeImagem já existe.<br>";
        } else {
            // Copiar e renomear a imagem
            if (copy($webcamFile, $novoNomeImagem)) {
                echo "Imagem copiada e renomeada com sucesso para $novoNomeImagem";
            } else {
                echo "Erro ao copiar e renomear a imagem";
            }
        }
    } else {
        echo "Arquivo hora.txt ou imagem webcam.jpg não existem.<br>";
        // Verificar quais arquivos não existem
        if (!file_exists($horaFile)) {
            echo "Arquivo hora.txt não existe.<br>";
        }
        if (!file_exists($webcamFile)) {
            echo "Imagem webcam.jpg não existe.<br>";
        }
    }

    
    file_put_contents($valorFile, 1);

    ob_end_clean();
    header("Location: dashboard.php");
    exit;
?>
