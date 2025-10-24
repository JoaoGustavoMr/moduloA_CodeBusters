<?php
include('conexao.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'professor'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Permissão negada.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);

    if (empty($titulo) || empty($descricao)) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha todos os campos.']);
        exit;
    }

    $sql = "UPDATE cursos SET titulo = ?, descricao = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssi", $titulo, $descricao, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Curso atualizado com sucesso!']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar o curso.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método inválido.']);
}
?>
