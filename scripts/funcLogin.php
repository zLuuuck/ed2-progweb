<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../scripts/conectarBanco.php");

function verificarSeEstaLogado($opcao)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($opcao === "Deslogado") {
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('Você precisa estar logado para acessar esta página!');</script>";
            header("Refresh: 0; url=./login.php");
            exit();
        }
    } elseif ($opcao === "Logado") {
        if (isset($_SESSION['user_id'])) {
            echo "<script>alert('Você já está logado!');</script>";
            header("Refresh: 0; url=./index.php");
            exit();
        }
    }
}

$mensagem = '';
function login()
{
    $mensagem = '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = conectarBanco('login');

    $stmt = $db->prepare("SELECT * FROM login WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['birth'] = $usuario['birth'];

        // Retorna mensagem de sucesso
        return mensagem("Login realizado com sucesso! Redirecionando...", "SUCCESS");
    } else {
        return mensagem("Usuário ou senha incorretos.", "ERROR");
    }
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

function cadastro()
{
    function validarDados($username, $nome, $email, $birth, $password, $passwordConfirm)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Email inválido.";
        if (strlen($username) < 3) return "O usuário precisa ter ao menos 3 caracteres.";
        if (strlen($username) > 50) return "O usuário não pode ter mais de 50 caracteres.";
        if (strlen($nome) < 3) return "O nome precisa ter ao menos 3 caracteres.";
        if (strlen($nome) > 30) return "O nome não pode ter mais de 30 caracteres.";

        $dataNascimentoObj = DateTime::createFromFormat('Y-m-d', $birth);
        if (!$dataNascimentoObj || $dataNascimentoObj->format('Y-m-d') !== $birth) {
            return "Data de nascimento inválida. Use o formato DD-MM-AAAA.";
        }
        $idade = (new DateTime())->diff($dataNascimentoObj)->y;
        if ($idade < 18) return "Você deve ter ao menos 18 anos para se cadastrar.";

        if ($password !== $passwordConfirm) return "As senhas não coincidem.";
        if (strlen($password) < 8) return "A senha deve ter ao menos 8 caracteres.";
        if (!preg_match("/[A-Z]/", $password)) return "A senha precisa de ao menos uma letra maiúscula.";
        if (!preg_match("/[a-z]/", $password)) return "A senha precisa de ao menos uma letra minúscula.";
        if (!preg_match("/[0-9]/", $password)) return "A senha precisa de ao menos um número.";
        if (!preg_match("/[^a-zA-Z0-9\s]/", $password)) return "A senha precisa de ao menos um caractere especial (ex: !, @, #, $).";

        return '';
    }

    function cadastrarUsuario($db, $username, $nome, $email, $birth, $password)
    {
        $senhaCriptografada = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("SELECT id FROM login WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        if ($stmt->fetch()) return "Nome de usuário já está em uso.";

        $stmt = $db->prepare("SELECT id FROM login WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) return "Este e-mail já está cadastrado.";

        try {
            $stmt = $db->prepare("INSERT INTO login (username, nome, email, birth, password) VALUES (:username, :nome, :email, :birth, :password)");
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':birth', $birth);
            $stmt->bindValue(':password', $senhaCriptografada);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Erro ao cadastrar: " . $e->getMessage();
        }
    }
    $db = conectarBanco('login');
    $username = trim($_POST["username"]);
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $birth = trim($_POST["birth"]);
    $password = $_POST["password"];
    $passwordConfirm = $_POST["password-confirm"];

    $validacao = validarDados($username, $nome, $email, $birth, $password, $passwordConfirm);

    if ($validacao !== '') {
        $mensagem = mensagem($validacao, "ERROR");
    } else {
        $resultadoCadastro = cadastrarUsuario($db, $username, $nome, $email, $birth, $password);
        if ($resultadoCadastro === true) {
            $mensagem = mensagem("Cadastro realizado com sucesso! Redirecionando...", "SUCCESS");
            return $mensagem;
        } else {
            $mensagem = mensagem($resultadoCadastro, "ERROR");
            return $mensagem;
        }
    }

}
