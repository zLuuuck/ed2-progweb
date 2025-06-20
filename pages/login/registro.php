<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['user_id'])) {
    echo "<script>alert('Você já está logado!');</script>";
    header("Refresh: 0;url=../index.php");
    exit();
}
// Função para conectar ao banco de dados SQLite e criar a tabela se não existir ~Lucas
function conectarBanco()
{
    $db = new PDO('sqlite:login.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='login'");
    $tabelaExiste = $stmt->fetch();

    if (!$tabelaExiste) {
        $db->exec("CREATE TABLE login (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            nome TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            birth DATE NOT NULL,
            password TEXT NOT NULL
        )");
    }
    return $db;
}

// Função para validar os dados do formulário de cadastro ~Lucas
function validarDados($username, $nome, $email, $birth, $password, $passwordConfirm)
{

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validação de email
        return "Email inválido.";
    }
    if (strlen($username) < 3) { // Mínimo de 3 char per user
        return "O usuário precisa ter ao menos 3 caracteres.";
    }
    if (strlen($username) > 50) { // Limite de username
        return "O usuário não pode ter mais de 50 caracteres.";
    }
    if (strlen($nome) < 3) { // Mínimo de 3 char per nome
        return "O nome precisa ter ao menos 3 caracteres.";
    }
    if (strlen($nome) > 30) { // Limite de nome
        return "O nome não pode ter mais de 30 caracteres.";
    }
    // Validação da data de nascimento
    $dataNascimentoObj = DateTime::createFromFormat('Y-m-d', $birth);
    if (!$dataNascimentoObj || $dataNascimentoObj->format('Y-m-d') !== $birth) {
        return "Data de nascimento inválida. Use o formato DD-MM-AAAA.";
    }
    // Verificar se a pessoa tem idade mínima (18 anos)
    $hoje = new DateTime();
    $idade = $hoje->diff($dataNascimentoObj)->y;
    if ($idade < 18) {
        return "Você deve ter ao menos 18 anos para se cadastrar.";
    }

    if ($password !== $passwordConfirm) { // Verifica se as senhas coincidem
        return "As senhas não coincidem.";
    }

    // Validação de força da senha
    if (strlen($password) < 8) { // Senha com no mínimo 8 caracteres
        return "A senha deve ter ao menos 8 caracteres.";
    }
    if (!preg_match("/[A-Z]/", $password)) { // Verifica se tem pelo menos uma letra maiúscula
        return "A senha precisa de ao menos uma letra maiúscula.";
    }
    if (!preg_match("/[a-z]/", $password)) { // Verifica se tem pelo menos uma letra minúscula
        return "A senha precisa de ao menos uma letra minúscula.";
    }
    if (!preg_match("/[0-9]/", $password)) { // Verifica se tem pelo menos um número
        return "A senha precisa de ao menos um número.";
    }
    if (!preg_match("/[^a-zA-Z0-9\s]/", $password)) { // Caracteres especiais
        return "A senha precisa de ao menos um caractere especial (ex: !, @, #, $).";
    }

    return ''; // Retorna vazio se todas as validações passarem
}

// Função para cadastrar o usuário no banco de dados ~Lucas
function cadastrarUsuario($db, $username, $nome, $email, $birth, $password)
{
        $senhaCriptografada = password_hash($password, PASSWORD_DEFAULT);

    // Verifica se o username já existe
    $stmt = $db->prepare("SELECT id FROM login WHERE username = :username");
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    if ($stmt->fetch()) {
        return "Nome de usuário já está em uso.";
    }

    // Verifica se o email já existe
    $stmt = $db->prepare("SELECT id FROM login WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->fetch()) {
        return "Este e-mail já está cadastrado.";
    }

    try {
        $stmt = $db->prepare("INSERT INTO login (username, nome, email, birth, password) VALUES (:username, :nome, :email, :birth, :password)");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':birth', $birth);
        $stmt->bindValue(':password', $senhaCriptografada);
        $stmt->execute();
        return "sucess!";
    } catch (PDOException $e) {
        return "Erro ao cadastrar: " . $e->getMessage();
    }
}

// Fluxo principal
$mensagem = '';
$mensagem_cor = 'darkred';

$db = conectarBanco();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $birth = trim($_POST["birth"]);
    $password = $_POST["password"];
    $passwordConfirm = $_POST["password-confirm"];

    $validacao = validarDados($username, $nome,$email, $birth, $password, $passwordConfirm);

    if ($validacao !== '') { 
        $mensagem = $validacao; 
        $mensagem_cor = 'orange';
    } else {
        $resultadoCadastro = cadastrarUsuario($db, $username, $nome, $email, $birth, $password);
        if ($resultadoCadastro === 'sucess') {
            $mensagem = "Cadastro realizado com sucesso! Redirecionando...";
            $mensagem_cor = 'green';

            header("Refresh: 2; url=login.php");        
        } else {
            $mensagem = $resultadoCadastro;
            $mensagem_cor = 'orange';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastrar-se</title>
    <link rel="stylesheet" href="../../styles/registro.css" />

</head>

<body>
    <div id="cadastro-form">
        <h1>Cadastro</h1>
        <!-- Mostrando a mensagem de erro ou sucesso ~ Lucas -->
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
            <p style="color: <?= $mensagem_cor ?>; font-weight: bold;"><?= $mensagem ?></p>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="float-label">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required minlength="3" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"/>
            </div>

            <div class="float-label">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required minlength="3" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"/>
            </div>

            <div class="float-label">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
            </div>
            
            <!-- Favor fazer um input mais bonito, não sei como faz, se vira ~Lucas -->
            <div class="float-label">
                <label for="birth">Data de nascimento:</label>
                <input type="date" id="birth" name="birth" required value="<?= htmlspecialchars($_POST['birth'] ?? '') ?>"/>
            </div>

            <div class="float-label">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required minlength="6" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>"/>
            </div>

            <div class="float-label">
                <label for="password-confirm">Confirme a senha:</label>
                <input type="password" id="password-confirm" name="password-confirm" required minlength="6" value="<?= htmlspecialchars($_POST['password-confirm'] ?? '') ?>"/>
            </div>

            <button type="submit">Cadastrar</button>
        </form>
        <p>Já tem uma conta? <a href="./login.php">Faça login</a></p>
    </div>
</body>

</html>