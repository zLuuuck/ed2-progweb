<?php

$companyName = "Guri Games";
$currentYear = date('Y');

$socialMedia = [
    "Instagram" => [
        "url" => "https://www.instagram.com/z_luuuck/",
        "icon_class" => "fab fa-instagram"
    ],
    "YouTube"   => [
        "url" => "https://www.youtube.com/@zLuuck",
        "icon_class" => "fab fa-youtube"
    ],
    "WhatsApp"  => [
        "url" => "https://wa.me/5541999016605",
        "icon_class" => "fab fa-whatsapp"
    ],
    "LinkedIn"  => [
        "url" => "https://www.linkedin.com/in/lucastoterol/",
        "icon_class" => "fab fa-linkedin"
    ],
    "GitHub"    => [
        "url" => "https://github.com/zLuuuck",
        "icon_class" => "fab fa-github"
    ]
];
?>
    <footer>
        <p>© <?php echo $currentYear; ?> <?php echo $companyName; ?>. Todos os direitos reservados.</p>
        
        <div class="social-icons">
            <?php foreach ($socialMedia as $platform => $data): ?>
                <a href="<?php echo htmlspecialchars($data['url']); ?>" target="_blank" aria-label="Visite nosso <?php echo $platform; ?>">
                    <i class="<?php echo htmlspecialchars($data['icon_class']); ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </footer>

    <script src="/site/scripts/navbar.js"></script>

    <?php
    if (isset($pageScripts) && is_array($pageScripts)) {
        foreach ($pageScripts as $script) {
            echo '<script src="' . htmlspecialchars($script) . '"></script>';
        }
    }
    ?>

</body>
</html>