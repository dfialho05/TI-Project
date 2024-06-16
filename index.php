<?php


require 'api/Accounts.php';

//echo password_hash("0000", PASSWORD_DEFAULT);

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    // Dados de login fornecidos pelo usuário
    $loginUsername = $_POST['username'];
    $loginPassword = $_POST['password'];

    // Caminho para o arquivo de contas
    $accountsFile = 'api/files/accounts/accounts.txt';

    // Verificar se o arquivo existe e é legível
    if (file_exists($accountsFile) && is_readable($accountsFile)) {
        // Array para armazenar contas
        $accounts = [];

        // Abrir o arquivo para leitura
        $fileHandle = fopen($accountsFile, 'r');

        // Ler o arquivo linha por linha
        while (($line = fgets($fileHandle)) !== false && !feof($fileHandle)) {
            // Separar o nome de usuário e senha usando o ponto e vírgula como delimitador
            list($username, $password) = explode(';', trim($line));

            // Criar um novo objeto Account e adicionar ao array
            $accounts[] = ['username' => $username, 'password' => $password];
        }

        // Fechar o arquivo
        fclose($fileHandle);

        // Verificar se as credenciais fornecidas correspondem a alguma conta existente
        $authenticated = false;
        foreach ($accounts as $account) {
            if ($account['username'] === $loginUsername && password_verify($loginPassword, $account['password'])) {
                $authenticated = true;
                $accountAuthenticated = $account;
                break;
            }
        }

        // Se as credenciais estiverem corretas, iniciar a sessão e redirecionar para o dashboard
        if ($authenticated) {
            session_start();
            $_SESSION['account'] = $accountAuthenticated;
            
            header("Location: dashboard.php");
            exit(); // Encerrar o script após redirecionamento
        } else {
            $flag = false;
        }
    } 
} 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style-login.css">
    <title>Plataforma IoT</title>
    
</head>
<body>
    <div class="main-login">
        <!-- lado direito -->
        <div class="right-login">
            <div class="card-login">
                <form action="#" method="post" class="TIform">
                    <!-- titulo do form -->
                    <h1 id="h1-LOGIN">LOGIN</h1>
                    <div class="text-field"><!-- para inserir dados -->
                        <div>
                            <label for="username" class="form-label">Username: </label>
                            <br>
                            <input type="text" id="username" placeholder="Insira o Username" class="input" name="username" required>
                        </div>
                        <br>
                        <div>
                            <label for="password" class="form-label">Password: </label>
                            <br>
                            <input type="password" id="password" placeholder="Insira a Password" class="input" name="password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-login">Submeter</button><!-- fazer login -->
                    <!-- <a href="register.php">Não tem uma conta?</a> -->
                    
                </form>
            </div>
        </div>
    </div>
</body>
</html>
