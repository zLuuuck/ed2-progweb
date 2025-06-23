<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>testes</title>
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/navbar.css">
</head>

<body>

<?php
include_once '../components/navbar.php';
?>

<h1>Home</h1>
    
    <?php
    echo "<p>Hello, World!</p>";
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        // Usuário está logado
        $nomeUsuario = $_SESSION['username'];
        
        echo "Bem-vindo, " . htmlspecialchars($nomeUsuario) . "!"; // Evitar XSS
        echo "<br>";
        echo "Seu ID de usuário é: " . htmlspecialchars($_SESSION['user_id']); //
        echo "<br>";
        echo "Seu nome é: " . htmlspecialchars($_SESSION['nome']); // Evitar XSS
        echo "<br>";
        echo "<form action='./login/logout.php' method='post' style='display: inline;'> <button type='submit'>Sair</button></form>";
    } else {
        // Usuário não está logado
        echo "Você não está logado. Por favor, faça login.";
        echo "<br>";
        echo "<a href='./login/login.php'><button>login</button></a>";
    }
    ?>


    <h2> Botões de ação - testes - </h2>
    <a href="./perfil.php"><button>Perfil</button></a><br>
    <a href="./produtos.php"><button>Produtos</button></a><br>
    <a href="./add-produtos.php"><button>Adicionar produtos</button></a><br>
    <a href="./sobre.php"><button>Sobre</button></a><br>
    <a href="./login/login.php"><button>Login</button></a><br>
    <a href="./login/registro.php"><button>Registro</button></a><br>

    <?php
    include_once '../components/footer.php';
    ?>
</body>

</html>