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
    <title>Sobre - Guri Games</title>
    <link rel="stylesheet" href="/site/css/sobre.css">
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/navbar.css">
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>

    <main class="main-content">
        <section class="sobre-container">

            <div class="mapa">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3603.3953059451933!2d-49.3212071!3d-25.4250443!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94dce6da131d6d1b%3A0x9b7d03b3efdf4053!2sUniversidade%20Tuiuti%20do%20Paran%C3%A1!5e0!3m2!1spt-BR!2sbr!4v1745708216016!5m2!1spt-BR!2sbr"
                    width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="texto">
                <h1>Quem Somos</h1>
                <p>A <strong>Guri Games</strong> é referência no mercado de computadores gamers montados, levando
                    performance, qualidade e confiança para jogadores de todo o Brasil. Com mais de <strong>15 anos de
                        experiência</strong>, nos dedicamos a entregar máquinas poderosas, configuradas com excelência
                    para proporcionar a melhor experiência gamer.</p>

            </div>
        </section>
    </main>

    <?php
    include_once '../components/footer.php';
    ?>
</body>

</html>