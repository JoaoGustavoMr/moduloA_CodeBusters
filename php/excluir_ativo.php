<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['nivel_permissao']) || $_SESSION['nivel_permissao'] !== 'admin') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado']);
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID do ativo não informado']);
    exit;
}

$ativo_id = intval($_GET['id']);

$stmt = $conexao->prepare("DELETE FROM ativos WHERE ativo_id = ?");
$stmt->bind_param("i", $ativo_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Ativo excluído com sucesso!']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não foi possível excluir o ativo.']);
}

$stmt->close();
$conexao->close();
