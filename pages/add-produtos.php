<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Você precisa estar logado para acessar esta página!');</script>";
    header("Refresh: 0;url=./login/login.php");
    exit();
}

$nome = $_POST['nome'] ?? '';
$modelo = $_POST['modelo'] ?? '';
$cor = $_POST['cor'] ?? '';
$quantidade = $_POST['quantidade'] ?? '';

function conectarBanco()
{
    $db = new PDO('sqlite:../db/produtos.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='produtos'");
    $tabelaExiste = $stmt->fetch();

    if (!$tabelaExiste) {
        $db->exec("CREATE TABLE produtos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            modelo TEXT NOT NULL UNIQUE,
            cor TEXT NOT NULL,
            quantidade INTEGER NOT NULL,
            imagem TEXT
        )");
    }
    return $db;
}

function verificarDados($db, $nome, $modelo, $cor, $quantidade, $arquivoImagem)
{
    if (strlen($nome) < 2 || strlen($modelo) < 2 || strlen($cor) < 2) {
        return "Todos os campos devem ter pelo menos 2 caracteres.";
    }
    if (!is_numeric($quantidade) || $quantidade <= 0) {
        return "Quantidade inválida.";
    }
    $stmt = $db->prepare("SELECT COUNT(*) FROM produtos WHERE modelo = ?");
    $stmt->execute([$modelo]);
    if ($stmt->fetchColumn() > 0) {
        return "Já existe um produto com esse modelo.";
    }

    // Validação da imagem
    if ($arquivoImagem['error'] !== UPLOAD_ERR_OK) {
        return "Erro no envio da imagem.";
    }

    $permitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Verificar o tipo MIME real do arquivo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivoImagem['tmp_name']);

    if (!in_array($mime, $permitidas)) {
        return "Tipo de imagem inválido. Use JPG, PNG, GIF ou WEBP.";
    }

    $tamanhoMaxMB = 2;
    if ($arquivoImagem['size'] > $tamanhoMaxMB * 1024 * 1024) {
        return "Imagem muito grande. O limite é {$tamanhoMaxMB}MB.";
    }

    return ''; // sem erros
}


function salvarImagem($arquivo)
{
    if ($arquivo['error'] !== UPLOAD_ERR_OK) return "Erro ao enviar imagem.";

    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $permitidas)) return "Extensão de imagem inválida.";

    $dir = __DIR__ . '/uploads/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $nomeSeguro = uniqid('produto_', true) . "." . $ext;
    $destino = $dir . $nomeSeguro;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        return "Erro ao mover a imagem.";
    }

    return $destino; // Retorna o caminho salvo
}

function cadastrarProduto($db, $nome, $modelo, $cor, $quantidade, $imagemPath)
{
    $stmt = $db->prepare("INSERT INTO produtos (nome, modelo, cor, quantidade, imagem) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$nome, $modelo, $cor, $quantidade, $imagemPath]);
}

$mensagem = "";
$db = conectarBanco();
$mensagem_cor = 'darkred';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $modelo = trim($_POST['modelo']);
    $cor = trim($_POST['cor']);
    $quantidade = trim($_POST['quantidade']);
    $imagemFile = $_FILES['imagem'];

    $erroValidacao = verificarDados($db, $nome, $modelo, $cor, $quantidade, $imagemFile);
    if ($erroValidacao !== '') {
        $mensagem = $erroValidacao;
        $mensagem_cor = 'orange';
    } else {
        $imagemPath = salvarImagem($_FILES['imagem']);
        if (str_contains($imagemPath, 'Erro')) {
            $mensagem = $imagemPath;
            $mensagem_cor = 'orange';
        } else {
            if (cadastrarProduto($db, $nome, $modelo, $cor, (int)$quantidade, $imagemPath)) {
                $mensagem = "Produto cadastrado com sucesso!";
                $mensagem_cor = 'green';
                $nome = $modelo = $cor = $quantidade = '';
            } else {
                $mensagem = "Erro ao cadastrar produto.";
                $mensagem_cor = 'red';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicone Produtos</title>
    <link rel="stylesheet" href="../styles/add-produtos.css">
    <link rel="stylesheet" href="../styles/navbar.css">
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>

    <h1>Adicione um produto à loja!</h1>
    <div id="produto-form">
        <h1>Seu produto:</h1>
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
            <p style="color: <?= $mensagem_cor ?>; font-weight: bold;"><?= $mensagem ?></p>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="float-label">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" required value="<?= htmlspecialchars($nome) ?>">
            </div>
            <div class="float-label">
                <label for="modelo">Modelo:</label>
                <input type="text" name="modelo" required value="<?= htmlspecialchars($modelo) ?>">
            </div>
            <div class="float-label">
                <label for="cor">Cor:</label>
                <input type="text" name="cor" required value="<?= htmlspecialchars($cor) ?>">
            </div>
            <div class="float-label">
                <label for="quantidade">Quantidade:</label>
                <input type="number" name="quantidade" min="1" required value="<?= htmlspecialchars($quantidade) ?>">
            </div>
            <div class="float-label">
                <label for="imagem">Imagem (JPG, PNG, GIF):</label>
                <input type="file" name="imagem" accept="image/*" required>
            </div>
            <button type="submit">Adicionar Produto</button>
        </form>
    </div>
</body>

</html>