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
    <style>
        .profile-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .profile-info {
            margin-bottom: 15px;
        }
        .profile-info label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include_once '../components/navbar.php'; ?>
    
    <div class="profile-container">
        <h1>Seu Perfil</h1>
        
        <div class="profile-info">
            <label>ID:</label>
            <?php echo htmlspecialchars($_SESSION['user_id']); ?>
        </div>
        
        <div class="profile-info">
            <label>Nome de Usuário:</label>
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
        
        <div class="profile-info">
            <label>Nome Completo:</label>
            <?php echo htmlspecialchars($_SESSION['nome']); ?>
        </div>
        
        <div class="profile-info">
            <label>E-mail:</label>
            <?php echo htmlspecialchars($_SESSION['email']); ?>
        </div>
        
        <div class="profile-info">
            <label>Data de Nascimento:</label>
            <?php 
                echo htmlspecialchars(
                    date('d/m/Y', strtotime($_SESSION['birth']))
                ); 
            ?>
        </div>
    </div>
    <?php
    include_once '../components/footer.php';
    ?>
</body>
</html>
