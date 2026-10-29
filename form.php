<?php
require __DIR__ . '/config.php';

// Se vier ?id=, é edição; senão, é cadastro
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$cliente = ['nome' => '', 'email' => '', 'telefone' => ''];
$erros = [];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM clientes WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $cliente = $stmt->fetch();
    if (!$cliente) {
        flash('Cliente não encontrado.');
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        http_response_code(403);
        exit('Requisição inválida (CSRF).');
    }

    $cliente['nome']     = trim($_POST['nome'] ?? '');
    $cliente['email']    = trim($_POST['email'] ?? '');
    $cliente['telefone'] = trim($_POST['telefone'] ?? '');

    // Validações
    if ($cliente['nome'] === '' || mb_strlen($cliente['nome']) > 100) {
        $erros[] = 'Informe um nome (até 100 caracteres).';
    }
    if (!filter_var($cliente['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if ($cliente['telefone'] !== '' && !preg_match('/^[0-9()\s+\-]{8,20}$/', $cliente['telefone'])) {
        $erros[] = 'Telefone inválido.';
    }

    // E-mail único
    if (!$erros) {
        $stmt = db()->prepare('SELECT id FROM clientes WHERE email = :e AND id <> :id');
        $stmt->execute([':e' => $cliente['email'], ':id' => $id ?? 0]);
        if ($stmt->fetch()) {
            $erros[] = 'Este e-mail já está cadastrado.';
        }
    }

    if (!$erros) {
        $dados = [
            ':nome'     => $cliente['nome'],
            ':email'    => $cliente['email'],
            ':telefone' => $cliente['telefone'] ?: null,
        ];

        if ($id) { // UPDATE
            $dados[':id'] = $id;
            db()->prepare('UPDATE clientes SET nome=:nome, email=:email, telefone=:telefone WHERE id=:id')
                ->execute($dados);
            flash('Cliente atualizado com sucesso!');
        } else {   // CREATE
            db()->prepare('INSERT INTO clientes (nome, email, telefone) VALUES (:nome, :email, :telefone)')
                ->execute($dados);
            flash('Cliente cadastrado com sucesso!');
        }
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $id ? 'Editar' : 'Novo' ?> cliente</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container estreito">
    <h1><?= $id ? 'Editar cliente' : 'Novo cliente' ?></h1>

    <?php if ($erros): ?>
        <div class="alerta erro">
            <?php foreach ($erros as $erro): ?><div><?= e($erro) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_campo() ?>

        <label>Nome
            <input type="text" name="nome" value="<?= e($cliente['nome']) ?>" required maxlength="100">
        </label>
        <label>E-mail
            <input type="email" name="email" value="<?= e($cliente['email']) ?>" required maxlength="120">
        </label>
        <label>Telefone
            <input type="text" name="telefone" value="<?= e($cliente['telefone']) ?>" maxlength="20">
        </label>

        <div class="barra">
            <a class="btn cinza" href="index.php">Cancelar</a>
            <button type="submit" class="btn verde">Salvar</button>
        </div>
    </form>
</div>
</body>
</html>
