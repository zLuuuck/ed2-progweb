<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 1) {
    echo "<script>alert('Você precisa estar logado para acessar esta página!');</script>";
    header("Refresh: 0;url=./login.php");
    exit();
}

$nome = $_POST['nome'] ?? '';
$modelo = $_POST['modelo'] ?? '';
$cor = $_POST['cor'] ?? '';
$quantidade = $_POST['quantidade'] ?? '';

require_once '../scripts/conectarBanco.php';
require_once '../scripts/func_produtos.php';
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

    $uploadDir = __DIR__ . '/../uploads/'; // Pasta fora de /pages
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $nomeSeguro = uniqid('produto_', true) . "." . $ext;
    $destinoAbsoluto = $uploadDir . $nomeSeguro;

    if (!move_uploaded_file($arquivo['tmp_name'], $destinoAbsoluto)) {
        return "Erro ao mover a imagem.";
    }

    // Caminho relativo para acesso via HTML a partir da pasta /pages
    return '../uploads/' . $nomeSeguro;
}

function cadastrarProduto($db, $nome, $modelo, $cor, $quantidade, $imagemPath)
{
    $stmt = $db->prepare("INSERT INTO produtos (nome, modelo, cor, quantidade, imagem) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$nome, $modelo, $cor, $quantidade, $imagemPath]);
}

$mensagem = "";
$db = conectarBanco('produtos');

$mensagem_cor = 'darkred';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $modelo = trim($_POST['modelo']);
    $cor = trim($_POST['cor']);
    $quantidade = trim($_POST['quantidade']);
    $imagemFile = $_FILES['imagem'];

    $erroValidacao = verificarDados($db, $nome, $modelo, $cor, $quantidade, $imagemFile);
    if ($erroValidacao !== '') {
        $mensagem = mensagem($erroValidacao, 'ERROR');
    } else {
        $imagemPath = salvarImagem($_FILES['imagem']);
        if (str_contains($imagemPath, 'Erro')) {
            $mensagem = mensagem($imagemPath, 'ERROR');
        } else {
            if (cadastrarProduto($db, $nome, $modelo, $cor, (int)$quantidade, $imagemPath)) {
                $mensagem = mensagem('Produto adicionado com sucesso!', 'SUCCESS');
                $nome = $modelo = $cor = $quantidade = '';
            } else {
                $mensagem = mensagem('Erro ao adicionar produto', 'ERROR');
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
    <script src="https://kit.fontawesome.com/0dc50eaa4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>
    <main>
        <div class="form-box">
            <h1>Adicione um produto à loja!</h1>
            <h2>Seu produto:</h2>

            <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && $mensagem): ?>
                <?= $mensagem ?>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" required value="<?= htmlspecialchars($nome) ?>">
                </div>

                <div class="form-group">
                    <label for="modelo">Modelo:</label>
                    <input type="text" name="modelo" required value="<?= htmlspecialchars($modelo) ?>">
                </div>

                <div class="form-group">
                    <label for="cor">Cor:</label>
                    <input type="text" name="cor" required value="<?= htmlspecialchars($cor) ?>">
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" name="quantidade" min="1" required value="<?= htmlspecialchars($quantidade) ?>">
                </div>

                <div class="form-group">
                    <label for="imagem">Imagem (JPG, PNG, GIF):</label>
                    <input type="file" name="imagem" accept="image/*" required>
                </div>

                <button type="submit">Adicionar Produto</button>
            </form>
        </div>
    </main>

    <?php
    include_once '../components/footer.php';
    ?>
</body>

</html>