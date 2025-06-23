<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../scripts/conectarBanco.php";
require_once "../scripts/funcLogin.php";
verificarSeEstaLogado('Logado');

$mensagem = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensagem = login();
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/login.css">
    <link rel="stylesheet" href="../styles/navbar.css">
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>

    <a href="../index.php"><button>Voltar</button></a>
    <div id="login-form">
        <h1>Login</h1>
        <!-- Mostrando a mensagem de erro ou sucesso ~ Lucas -->
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
            <?= $mensagem ?>
            <?php if (strpos($mensagem, 'sucesso') !== false || strpos($mensagem, 'logado') !== false): ?>
                <script>
                    setTimeout(() => {
                        window.location.href = './index.php';
                    }, 3000)
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="float-label">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="float-label">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
        <p>Não tem uma conta? <a href="./registro.php">Cadastre-se</a></p>
    </div>
</body>

</html>