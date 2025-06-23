<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function mensagem(string $mensagem, string $tipo)
{
    $classe = match (strtoupper($tipo)) {
        'SUCCESS' => 'mensagem-sucesso',
        'ERROR' => 'mensagem-erro',
        default => 'mensagem-info'
    };

    return "<div class='$classe'>$mensagem</div>";
}