<?php
require __DIR__ . '/config.php';

// READ: listagem com busca
$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $stmt = db()->prepare(
        'SELECT * FROM clientes WHERE nome LIKE :b OR email LIKE :b ORDER BY id DESC'
    );
    $stmt->execute([':b' => "%$busca%"]);
} else {
    $stmt = db()->query('SELECT * FROM clientes ORDER BY id DESC');
}
$clientes = $stmt->fetchAll();
$mensagem = flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Clientes</h1>

    <?php if ($mensagem): ?>
        <div class="alerta sucesso"><?= e($mensagem) ?></div>
    <?php endif; ?>

    <div class="barra">
        <form method="get" class="busca">
            <input type="text" name="busca" placeholder="Buscar por nome ou e-mail" value="<?= e($busca) ?>">
            <button type="submit">Buscar</button>
        </form>
        <a class="btn verde" href="form.php">+ Novo cliente</a>
    </div>

    <table>
        <thead>
            <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php if (!$clientes): ?>
            <tr><td colspan="5" class="vazio">Nenhum cliente encontrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($clientes as $c): ?>
            <tr>
                <td><?= (int)$c['id'] ?></td>
                <td><?= e($c['nome']) ?></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['telefone']) ?></td>
                <td class="acoes">
                    <a class="btn azul" href="form.php?id=<?= (int)$c['id'] ?>">Editar</a>
                    <form method="post" action="excluir.php"
                          onsubmit="return confirm('Excluir este cliente?')">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <button type="submit" class="btn vermelho">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
