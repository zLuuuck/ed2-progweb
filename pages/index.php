<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>testes</title>
    <link rel="stylesheet" href="../styles/navbar.css">
    <link rel="stylesheet" href="../styles/style.css">
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
</head>

<body>

    <?php
    include_once '../components/navbar.php';
    ?>
    <main>
        <div class="welcome-box">
            <h1>Bem vindo ao Guri Games!</h1>
            <p>Este é nosso site!</p>
            <a href="../produtos.php" class="cta-button">Ver Produtos</a>
        </div>
    </main>

    <?php
    include_once '../components/footer.php';
    ?>
</body>

</html>