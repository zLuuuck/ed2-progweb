<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../scripts/funcLogin.php";

verificarSeEstaLogado("Deslogado");

// para ver outras informações do usuário, acessa o arquivo login/login.php, a partir da linha 40
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../styles/navbar.css">
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>
    <h1> Bem vindo <?php echo htmlspecialchars($_SESSION['username']); ?> </h1>
</body>

</html>