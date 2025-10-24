<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include('conexao.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método inválido.']);
    exit;
}

// Recebe os dados editáveis
$ativo_id = intval($_POST['ativo_id'] ?? 0);
$nome = $conexao->real_escape_string(trim($_POST['nome'] ?? ''));
$categoria = $conexao->real_escape_string(trim($_POST['categoria'] ?? ''));
$valor = floatval($_POST['valor'] ?? 0);
$status = $conexao->real_escape_string(trim($_POST['status'] ?? ''));
$localizacao = $conexao->real_escape_string(trim($_POST['localizacao'] ?? ''));

// Valida campos obrigatórios
if (!$ativo_id || empty($nome) || empty($categoria) || $valor <= 0 || empty($status) || empty($localizacao)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos corretamente.']);
    exit;
}

$sql = "UPDATE ativos SET 
            nome = '$nome',
            categoria = '$categoria',
            valor = $valor,
            status = '$status',
            localizacao = '$localizacao'
        WHERE ativo_id = $ativo_id";

try {
    if ($conexao->query($sql)) {
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Ativo atualizado com sucesso!']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar ativo: ' . $conexao->error]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
