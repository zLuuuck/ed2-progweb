<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['user_id'])) {
    echo "<script>alert('Você já está logado!');</script>";
    header("Refresh: 0;url=../index.php");
    exit();
}

function conectarBanco()
{
    try {
        $db = new PDO('sqlite:login.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}

$mensagem = '';
$mensagem_cor = 'darkred';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']) ?? '';
    $password = $_POST['password'] ?? '';

    $db = conectarBanco();

    // Verifica se o usuário existe
    $stmt = $db->prepare("SELECT * FROM login WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['birth'] = $usuario['birth'];
        $mensagem = "✅ Login realizado com sucesso! Redirecionando. . .";
        $mensagem_cor = "green";
        header("Refresh: 2; url=../index.php");
    } else {
        $mensagem = "⚠️ Usuário ou senha incorretos.";
        $mensagem_cor = "red";
    }
} else {
    $mensagem = "";
    $mensagem_cor = "black";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../styles/login.css">
</head>

<body>
    <a href="../index.php"><button>Voltar</button></a>
    <div id="login-form">
        <h1>Login</h1>
        <!-- Mostrando a mensagem de erro ou sucesso ~ Lucas -->
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
            <p style="color: <?= $mensagem_cor ?>; font-weight: bold;"><?= $mensagem ?></p>
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