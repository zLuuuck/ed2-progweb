<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$isAdmin = isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1;

$db = conectarBanco(); // Mover a conexão para o início, para usar antes de editarProduto

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar' && $isAdmin) {
    header('Content-Type: application/json; charset=utf-8');  // *** [1] Cabeçalho JSON para resposta AJAX ***

    // Recebe os dados da edição
    $id = $_POST['id'] ?? null;
    $nome = trim($_POST['nome'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $cor = trim($_POST['cor'] ?? '');
    $quantidade = intval($_POST['quantidade'] ?? 0);
    $imagemFile = $_FILES['imagem'] ?? null;

    if (!$id) {
        echo json_encode(['status' => 'error', 'msg' => 'ID do produto ausente.']);
        exit;
    }

    $resultado = editarProduto($db, $id, $nome, $modelo, $cor, $quantidade, $imagemFile);
    echo json_encode($resultado);
    exit;
}

function conectarBanco()
{
    $db = new PDO('sqlite:../db/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function buscarProdutos($db)
{
    $stmt = $db->query("SELECT * FROM produtos ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deletarProduto($db, $id)
{
    // Buscar imagem antes de deletar
    $stmt = $db->prepare("SELECT imagem FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto && file_exists($produto['imagem'])) {
        unlink($produto['imagem']); // Remove a imagem do servidor
    }

    $stmt = $db->prepare("DELETE FROM produtos WHERE id = ?");
    return $stmt->execute([$id]);
}

function editarProduto($db, $id, $nome, $modelo, $cor, $quantidade, $imagemFile = null)
{
    // Validações básicas
    if (
        strlen($nome) < 2 || strlen($modelo) < 2 ||
        strlen($cor) < 2 || !is_numeric($quantidade) || $quantidade <= 0
    ) {
        return ['status' => 'error', 'msg' => "Dados inválidos para edição."];
    }

    // Verifica se o modelo já existe em outro produto
    $stmt = $db->prepare("SELECT COUNT(*) FROM produtos WHERE modelo = ? AND id != ?");
    $stmt->execute([$modelo, $id]);
    if ($stmt->fetchColumn() > 0) {
        return ['status' => 'error', 'msg' => "Já existe outro produto com esse modelo."];
    }

    $imagemPath = null;

    // Se enviou arquivo, processa a imagem
    $imagemPath = null;

    // Se enviou arquivo, processa a imagem
    if ($imagemFile && $imagemFile['error'] === UPLOAD_ERR_OK) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($imagemFile['tmp_name']);
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime, $tiposPermitidos)) {
            return ['status' => 'error', 'msg' => "Tipo de imagem inválido. Use JPG, PNG, GIF ou WEBP."];
        }

        if (!getimagesize($imagemFile['tmp_name'])) {
            return ['status' => 'error', 'msg' => "Arquivo enviado não é uma imagem válida."];
        }

        $tamanhoMaxMB = 2;
        if ($imagemFile['size'] > $tamanhoMaxMB * 1024 * 1024) {
            return ['status' => 'error', 'msg' => "Imagem muito grande. Máximo de {$tamanhoMaxMB}MB."];
        }

        // Criar pasta, gerar nome seguro e salvar
        if (!is_dir('./uploads')) {
            mkdir('./uploads', 0777, true);
        }

        $ext = pathinfo($imagemFile['name'], PATHINFO_EXTENSION);
        $novoNome = 'uploads/produto_' . $id . '_' . time() . '.' . strtolower($ext);
        if (!move_uploaded_file($imagemFile['tmp_name'], $novoNome)) {
            return ['status' => 'error', 'msg' => "Falha ao mover a imagem."];
        }

        // Apaga a imagem antiga
        $stmt = $db->prepare("SELECT imagem FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $antiga = $stmt->fetchColumn();
        if ($antiga && file_exists($antiga)) {
            unlink($antiga);
        }

        $imagemPath = $novoNome;
    }

    // Monta SQL com ou sem imagem
    if ($imagemPath) {
        $sql = "UPDATE produtos SET nome = ?, modelo = ?, cor = ?, quantidade = ?, imagem = ? WHERE id = ?";
        $params = [$nome, $modelo, $cor, $quantidade, $imagemPath, $id];
    } else {
        $sql = "UPDATE produtos SET nome = ?, modelo = ?, cor = ?, quantidade = ? WHERE id = ?";
        $params = [$nome, $modelo, $cor, $quantidade, $id];
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    return ['status' => 'success', 'msg' => "Produto atualizado com sucesso."];
}



$db = conectarBanco();

// Processar exclusão se for admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin && isset($_POST['excluir_id'])) {
    deletarProduto($db, (int)$_POST['excluir_id']);
    header("Location:produtos.php");
    exit();
}

$produtos = buscarProdutos($db);

$editar_id = $_POST['editar_id'] ?? null;
$atualizar_id = $_POST['atualizar_id'] ?? null;
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin && $atualizar_id) {
    $novoNome = trim($_POST['novo_nome']);
    $novoModelo = trim($_POST['novo_modelo']);
    $novaCor = trim($_POST['nova_cor']);
    $novaQtd = (int)$_POST['nova_quantidade'];

    $mensagem = editarProduto($db, $atualizar_id, $novoNome, $novoModelo, $novaCor, $novaQtd);
    $editar_id = null; // fecha o modo de edição após salvar
}

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link rel="stylesheet" href="../styles/produtos.css">
    <link rel="stylesheet" href="../styles/navbar.css">

</head>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Produtos</title>
    <link rel="stylesheet" href="../styles/produtos.css" />
</head>

<body>
    <?php
    include_once '../components/navbar.php';
    ?>
    <h1>Produtos Cadastrados</h1>
    <?php if ($mensagem): ?>
        <p style="color: green; font-weight: bold;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <?php if (empty($produtos)): ?>
        <p>Nenhum produto cadastrado.</p>
    <?php else: ?>
        <?php foreach ($produtos as $produto): ?>
            <div class="produto" data-id="<?= $produto['id'] ?>">
                <div class="view-mode">
                    <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                    <p><strong>Modelo:</strong> <?= htmlspecialchars($produto['modelo']) ?></p>
                    <p><strong>Cor:</strong> <?= htmlspecialchars($produto['cor']) ?></p>
                    <p><strong>Quantidade:</strong> <?= $produto['quantidade'] ?></p>
                    <?php if (!empty($produto['imagem'])): ?>
                        <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem do produto" width="150" />
                    <?php else: ?>
                        <p><em>Imagem não disponível</em></p>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <button class="btn-editar">Editar</button>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Deseja excluir?');">
                            <input type="hidden" name="excluir_id" value="<?= $produto['id'] ?>" />
                            <button type="submit" style="background-color: red; color: white;">Excluir</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php if ($isAdmin): ?>
                    <form class="edit-mode" style="display:none;" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $produto['id'] ?>" />
                        <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required /></label><br />
                        <label>Modelo: <input type="text" name="modelo" value="<?= htmlspecialchars($produto['modelo']) ?>" required /></label><br />
                        <label>Cor: <input type="text" name="cor" value="<?= htmlspecialchars($produto['cor']) ?>" required /></label><br />
                        <label>Quantidade: <input type="number" name="quantidade" value="<?= $produto['quantidade'] ?>" required /></label><br />
                        <label>Imagem: <input type="file" name="imagem" accept="image/*" /></label><br />
                        <button type="submit">Salvar</button>
                        <button type="button" class="btn-cancelar">Cancelar</button>
                        <div class="msg-resultado" style="color:red; font-weight:bold;"></div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Função para alternar modo edição e visualização
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', () => {
                const produtoDiv = btn.closest('.produto');
                produtoDiv.querySelector('.view-mode').style.display = 'none';
                produtoDiv.querySelector('.edit-mode').style.display = 'block';
            });
        });

        document.querySelectorAll('.btn-cancelar').forEach(btn => {
            btn.addEventListener('click', () => {
                const produtoDiv = btn.closest('.produto');
                produtoDiv.querySelector('.edit-mode').style.display = 'none';
                produtoDiv.querySelector('.view-mode').style.display = 'block';
            });
        });

        // Enviar formulário de edição via AJAX
        document.querySelectorAll('.edit-mode').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();

                const formData = new FormData(form);
                formData.append('action', 'editar'); // necessário para o PHP saber que é edição

                fetch('produtos.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json()) // *** [2] Correto parse do JSON ***
                    .then(data => {
                        const msgDiv = form.querySelector('.msg-resultado');
                        if (data.status === 'success') {
                            msgDiv.style.color = 'green';
                            msgDiv.textContent = data.msg;

                            // Atualiza view-mode com os dados editados
                            const produtoDiv = form.closest('.produto');
                            produtoDiv.querySelector('.view-mode h3').textContent = form.nome.value;
                            produtoDiv.querySelector('.view-mode p:nth-of-type(1)').innerHTML = `<strong>Modelo:</strong> ${form.modelo.value}`;
                            produtoDiv.querySelector('.view-mode p:nth-of-type(2)').innerHTML = `<strong>Cor:</strong> ${form.cor.value}`;
                            produtoDiv.querySelector('.view-mode p:nth-of-type(3)').innerHTML = `<strong>Quantidade:</strong> ${form.quantidade.value}`;

                            // Atualiza imagem se nova for enviada
                            if (form.imagem.files.length > 0) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    let img = produtoDiv.querySelector('.view-mode img');
                                    if (!img) {
                                        img = document.createElement('img');
                                        img.width = 150;
                                        produtoDiv.querySelector('.view-mode').appendChild(img);
                                    }
                                    img.src = e.target.result;
                                }
                                reader.readAsDataURL(form.imagem.files[0]);
                            }

                            // Fecha o modo edição após 1.5s
                            setTimeout(() => {
                                form.style.display = 'none';
                                produtoDiv.querySelector('.view-mode').style.display = 'block';
                                msgDiv.textContent = '';
                            }, 1500);
                        } else {
                            msgDiv.style.color = 'red';
                            msgDiv.textContent = data.msg;
                        }
                    })
                    .catch(() => {
                        const msgDiv = form.querySelector('.msg-resultado');
                        msgDiv.style.color = 'red';
                        msgDiv.textContent = 'Erro na comunicação com o servidor.';
                    });
            });
        });
    </script>
</body>


</html>