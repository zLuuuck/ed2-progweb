<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function conectarBanco($tabela)
{
    try {
        $db = new PDO('sqlite:../db/database.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica se a tabela existe e cria, se necessário
        if ($tabela === 'login') {
            $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='login'");
            if (!$stmt->fetch()) {
                $db->exec("CREATE TABLE login (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT NOT NULL UNIQUE,
                    nome TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    birth DATE NOT NULL,
                    password TEXT NOT NULL
                )");
            }
        } else if ($tabela === 'produtos') {
            $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='produtos'");
            if (!$stmt->fetch()) {
                $db->exec("CREATE TABLE produtos (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    nome TEXT NOT NULL,
                    modelo TEXT NOT NULL UNIQUE,
                    cor TEXT NOT NULL,
                    quantidade INTEGER NOT NULL,
                    imagem TEXT
                )");
            }
        }

        return $db;
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }
}
