<?php

$siteName = "Guri Games";
$navLinks = [
    "Home"     => "index.php",
    "Produtos" => "products.php",
    "Conta"    => "conta/conta.php",
    "Sobre"    => "sobre.php"
];
$currentPageTitle = isset($pageTitle) ? $pageTitle . " - " . $siteName : $siteName;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentPageTitle); ?></title>
    

    <link rel="stylesheet" href="../css/sobre.css"> 
    
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="cabeçalho">
        <nav>
            <a class="logo" href="index.php"><?php echo $siteName; ?></a>
            <div class="mobile-menu" onclick="toggleMenu()">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
            <ul class="navbar">
                <?php
                foreach ($navLinks as $name => $url) {
                    echo '<li><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($name) . '</a></li>';
                }
                ?>
                <li>
                    <a href="checkout.php"><i class="fas fa-shopping-cart"></i><span class="cart-count">0</span></a>
                </li>
            </ul>
        </nav>
    </header>