<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_unset();     // limpa as variáveis da sessão
session_destroy();   // destrói a sessão

header("Location: login.php"); // redireciona para a página de login
exit;
