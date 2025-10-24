<?php
include('conexao.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] === 'aluno') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Permissão negada.']);
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conexao->prepare("DELETE FROM cursos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'O curso foi excluído com sucesso!']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao excluir o curso.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID do curso não informado.']);
}
?>
