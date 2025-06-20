<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>testes</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <h1>Home</h1>
    <?php
    echo "<p>Hello, World!</p>";
    session_start();

    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        // Usuário está logado
        $nomeUsuario = $_SESSION['username'];
        
        echo "Bem-vindo, " . htmlspecialchars($nomeUsuario) . "!"; // Evitar XSS
        echo "<br>";
        echo "Seu ID de usuário é: " . htmlspecialchars($_SESSION['user_id']); //
        echo "<br>";
        echo "Seu nome é: " . htmlspecialchars($_SESSION['nome']); // Evitar XSS
        echo "<br>";
    } else {
        // Usuário não está logado
        echo "Você não está logado. Por favor, faça login.";
        echo "<br>";
        echo "<a href='./login/login.php'><button>login</button></a>";
        exit;
    }
    ?>
    <form action="./login/logout.php" method="post" style="display: inline;">
        <button type="submit">Sair</button>
    </form>

</body>

</html>