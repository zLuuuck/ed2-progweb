<?php
function conectarBanco()
{
    $db = new PDO('sqlite:login.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='login'");
    $tabelaExiste = $stmt->fetch();

    if (!$tabelaExiste) {
        $db->exec("CREATE TABLE login (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            birth DATE NOT NULL,
            password TEXT NOT NULL
        )");
    }
    return $db;
}

function validarDados($username, $email, $birth, $password, $passwordConfirm)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "⚠️ Email inválido.";
    }
    if (strlen($username) < 3) {
        return "⚠️ O usuário precisa ter ao menos 3 caracteres.";
    }
    if (strlen($username) > 50) { // Adicionar limite máximo para username
        return "⚠️ O usuário não pode ter mais de 50 caracteres.";
    }

    // Validação da data de nascimento (melhorada)
    $dataNascimentoObj = DateTime::createFromFormat('Y-m-d', $birth);
    if (!$dataNascimentoObj || $dataNascimentoObj->format('Y-m-d') !== $birth) {
        return "⚠️ Data de nascimento inválida. Use o formato DD-MM-AAAA.";
    }
    // Opcional: Verificar se a pessoa tem idade mínima (ex: 13 anos)
    $hoje = new DateTime();
    $idade = $hoje->diff($dataNascimentoObj)->y;
    if ($idade < 13) {
        return "⚠️ Você deve ter ao menos 13 anos para se cadastrar.";
    }

    if ($password !== $passwordConfirm) {
        return "❌ As senhas não coincidem.";
    }

    // Validação de força da senha
    if (strlen($password) < 8) { // Senha com no mínimo 8 caracteres
        return "⚠️ A senha deve ter ao menos 8 caracteres.";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        return "⚠️ A senha precisa de ao menos uma letra maiúscula.";
    }
    if (!preg_match("/[a-z]/", $password)) {
        return "⚠️ A senha precisa de ao menos uma letra minúscula.";
    }
    if (!preg_match("/[0-9]/", $password)) {
        return "⚠️ A senha precisa de ao menos um número.";
    }
    if (!preg_match("/[^a-zA-Z0-9\s]/", $password)) { // Caracteres especiais
        return "⚠️ A senha precisa de ao menos um caractere especial (ex: !, @, #, $).";
    }

    return '';
}

function cadastrarUsuario($db, $username, $email, $birth, $password)
{
    $senhaCriptografada = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $db->prepare("INSERT INTO login (username, email, birth, password) VALUES (:username, :email, :birth, :password)");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':birth', $birth);
        $stmt->bindValue(':password', $senhaCriptografada);
        $stmt->execute();
        return "✅ Cadastro realizado com sucesso! Redirecionando. . .";
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { // conflito UNIQUE
            return "⚠️ Este e-mail já está cadastrado.";
        }
        return "Erro ao cadastrar: " . $e->getMessage();
    }
}

// Fluxo principal
$mensagem = '';
$mensagem_cor = 'darkred';

$db = conectarBanco();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $birth = trim($_POST["birth"]);
    $password = $_POST["password"];
    $passwordConfirm = $_POST["password-confirm"];

    $validacao = validarDados($username, $email, $birth, $password, $passwordConfirm);

    if ($validacao !== '') { 
        $mensagem = $validacao; 
        $mensagem_cor = strpos($mensagem, '❌') === 0 ? 'darkred' : 'orange';
    } else {
        $resultadoCadastro = cadastrarUsuario($db, $username, $email, $birth, $password);
        if (strpos($resultadoCadastro, '✅') === 0) {
            $mensagem = "✅ Cadastro realizado com sucesso! Redirecionando...";
            $mensagem_cor = 'green';

            header("Refresh: 2; url=login.php");        
        } else {
            $mensagem = $resultadoCadastro;
            $mensagem_cor = strpos($mensagem, '❌') === 0 ? 'darkred' : 'orange';
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
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
            <p style="color: <?= $mensagem_cor ?>; font-weight: bold;"><?= $mensagem ?></p>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="float-label">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required minlength="3" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"/>
            </div>
            <div class="float-label">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
            </div>
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