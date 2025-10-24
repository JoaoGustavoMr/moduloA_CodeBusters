<?php
include('conexao.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'aluno') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Apenas alunos podem se inscrever em cursos.']);
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID de curso inválido.']);
    exit;
}

$id_curso = intval($_GET['id']);
$id_aluno = intval($_SESSION['id_usuario']);

$checkCurso = $conexao->prepare("SELECT id FROM cursos WHERE id = ?");
$checkCurso->bind_param("i", $id_curso);
$checkCurso->execute();
$checkCurso->store_result();

if ($checkCurso->num_rows === 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Curso não encontrado.']);
    $checkCurso->close();
    exit;
}
$checkCurso->close();

$checkInscricao = $conexao->prepare("SELECT id FROM inscricoes WHERE id_aluno = ? AND id_curso = ?");
$checkInscricao->bind_param("ii", $id_aluno, $id_curso);
$checkInscricao->execute();
$checkInscricao->store_result();

if ($checkInscricao->num_rows > 0) {
    echo json_encode(['status' => 'ja_inscrito', 'mensagem' => 'Você já está inscrito neste curso.']);
    $checkInscricao->close();
    exit;
}
$checkInscricao->close();

$stmt = $conexao->prepare("INSERT INTO inscricoes (id_aluno, id_curso, data_inscricao) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $id_aluno, $id_curso);

if ($stmt->execute()) {
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Inscrição realizada com sucesso.']);
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao realizar a inscrição.']);
}

$stmt->close();
$conexao->close();
