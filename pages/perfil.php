<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Você precisa estar logado para acessar esta página!');</script>";
    header("Refresh: 0;url=login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
</head>
<body>
    <h1> Bem vindo <?php $_SESSION['username'] ?> </h1>
</body>
</html>