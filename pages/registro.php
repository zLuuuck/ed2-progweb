<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Função para conectar ao banco de dados SQLite e criar a tabela se não existir ~Lucas
require_once "../scripts/conectarBanco.php";
require_once "../scripts/funcLogin.php";
verificarSeEstaLogado('Logado');

// Função para validar os dados do formulário de cadastro ~Lucas
$mensagem = '';
$db = conectarBanco('login');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensagem = cadastro();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastrar-se</title>
    <link rel="stylesheet" href="../styles/registro.css" />
    <link rel="stylesheet" href="../styles/navbar.css">
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>

<main>
        <div id="cadastro-form">
            <h1>Cadastro</h1>
            <!-- Mostrando a mensagem de erro ou sucesso ~ Lucas -->
            <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
                <?= $mensagem ?>
                <?php if (strpos($mensagem, 'sucesso') !== false || strpos($mensagem, 'logado') !== false): ?>
                    <script>
                        setTimeout(() => {
                            window.location.href = './login.php';
                        }, 3000)
                    </script>
                <?php endif; ?>
            <?php endif; ?>
    
            <form method="post" novalidate>
                <div class="float-label">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" required minlength="3" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
                </div>
    
                <div class="float-label">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" required minlength="3" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" />
                </div>
    
                <div class="float-label">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                </div>
    
                <!-- Favor fazer um input mais bonito, não sei como faz, se vira ~Lucas -->
                <div class="float-label">
                    <label for="birth">Data de nascimento:</label>
                    <input type="date" id="birth" name="birth" required value="<?= htmlspecialchars($_POST['birth'] ?? '') ?>" />
                </div>
    
                <div class="float-label">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required minlength="6" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>" />
                </div>
    
                <div class="float-label">
                    <label for="password-confirm">Confirme a senha:</label>
                    <input type="password" id="password-confirm" name="password-confirm" required minlength="6" value="<?= htmlspecialchars($_POST['password-confirm'] ?? '') ?>" />
                </div>
    
                <button type="submit">Cadastrar</button>
            </form>
            <p>Já tem uma conta? <a href="./login.php">Faça login</a></p>
        </div>
    </main>
    
    <?php
    include_once '../components/footer.php';
    ?>
</body>

</html>