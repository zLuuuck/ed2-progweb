<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../scripts/funcLogin.php";

verificarSeEstaLogado("Deslogado");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../styles/navbar.css">
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/perfil.css">
</head>
<body>
    <?php include_once '../components/navbar.php'; ?>
    <div class="main-content">
        <div class="profile-container">
            <h1>Seu Perfil</h1>
            
            <div class="profile-info">
                <label>ID:</label>
                <span><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
            </div>
            
            <div class="profile-info">
                <label>Nome de Usuário:</label>
                 <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
            
            <div class="profile-info">
                <label>Nome Completo:</label>
                 <span><?php echo htmlspecialchars($_SESSION['nome']); ?></span>
            </div>
            
            <div class="profile-info">
                <label>E-mail:</label>
                 <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
            </div>
            
            <div class="profile-info">
                <label>Data de Nascimento:</label>
                 <span><?php echo htmlspecialchars(date('d/m/Y', strtotime($_SESSION['birth']))); ?> </span>
            </div>
        </div>
    </div>
    <?php
    include_once '../components/footer.php';
    ?>
</body>
</html>
