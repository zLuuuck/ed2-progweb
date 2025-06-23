<?php
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1;
?>

<nav class="navbar">
    <a href="../pages/index.php" class="logo">Guri Games</a>
    <button class="hamburger" id="hamburger">&#9776;</button>

    <ul class="navbar-list" id="navbar-list">
        <li><a href="../pages/index.php">Home</a></li>
        <li><a href="../pages/produtos.php">Produtos</a></li>
        <?php if ($isLoggedIn): ?>
            <?php if ($isAdmin): ?>
                <li><a href="../pages/add-produtos.php">Adicionar Produtos</a></li>
            <?php endif; ?>
            <li><a href="../pages/perfil.php">Conta</a></li>
            <li><a href="../pages/logout.php" class="btn-sair">Sair</a></li>
        <?php else: ?>
            <li><a href="../pages/login.php">Entrar</a></li>
        <?php endif; ?>
        <li><a href="../pages/sobre.php">Sobre</a></li>

    </ul>
</nav>

<!-- Script para o menu hambúrguer (adicione isso também) -->
<script>
    const hamburger = document.getElementById('hamburger');
    const navbarList = document.getElementById('navbar-list');

    if (hamburger && navbarList) {
        hamburger.addEventListener('click', () => {
            navbarList.classList.toggle('active');
        });
    }
</script>