<?php
require __DIR__ . '/config.php';

// DELETE: só aceita POST com token CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valido()) {
    http_response_code(403);
    exit('Requisição inválida.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    db()->prepare('DELETE FROM clientes WHERE id = :id')->execute([':id' => $id]);
    flash('Cliente excluído com sucesso!');
}

header('Location: index.php');
exit;
