<?php
include('nav.php');

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$nivel = $_SESSION['nivel_permissao'] ?? 'colaborador';

// Consulta ativos que o usuário pode ver
$ativos_sql = $nivel === 'admin'
    ? "SELECT ativo_id, nome FROM ativos ORDER BY nome ASC"
    : "SELECT ativo_id, nome FROM ativos WHERE responsavel_id = $id_usuario ORDER BY nome ASC";
$ativos_result = $conexao->query($ativos_sql);

// Inicialmente, nenhuma manutenção selecionada
$manutencoes = [];
$ativo_selecionado = intval($_GET['ativo_id'] ?? 0);

if ($ativo_selecionado) {
    $sql = "SELECT m.*, u.nome AS colaborador_nome
            FROM manutencoes m
            LEFT JOIN usuarios u ON m.criado_por = u.usuario_id
            WHERE m.ativo_id = $ativo_selecionado
            ORDER BY m.data DESC";
    $res = $conexao->query($sql);
    if($res) {
        while($row = $res->fetch_assoc()) {
            $manutencoes[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Histórico de Manutenção</title>
<link rel="stylesheet" href="../css/ativos.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<main class="main-container">
    <div class="title-wrapper">
        <h2><i class="fas fa-tools"></i> Histórico de Manutenção</h2>
        <p class="subtitle">Selecione um ativo para visualizar seu histórico de manutenções</p>
    </div>

    <div class="form-group mb-3">
        <label for="ativoSelect">Ativo:</label>
        <select id="ativoSelect">
            <option value="">Selecione o ativo</option>
            <?php while($a = $ativos_result->fetch_assoc()): ?>
                <option value="<?= $a['ativo_id'] ?>" <?= $ativo_selecionado == $a['ativo_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nome']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <a href="adicionar_manutencao.php" class="btn btn-success ml-2">+ Adicionar Manutenção</a>
    </div>

    <div class="card-container">
        <?php if($manutencoes): ?>
            <?php foreach($manutencoes as $m): ?>
                <div class="card">
                    <h3><i class="fas fa-wrench"></i> <?= htmlspecialchars($m['tipo']) ?></h3>
                    <div class="info">
                        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($m['data'])) ?></p>
                        <p><strong>Técnico Responsável:</strong> <?= htmlspecialchars($m['responsavel_tecnico']) ?></p>
                        <p><strong>Custo:</strong> R$ <?= number_format($m['custo'],2,',','.') ?></p>
                        <p><strong>Descrição:</strong> <?= htmlspecialchars($m['descricao']) ?></p>
                        <p><strong>Criado por:</strong> <?= htmlspecialchars($m['colaborador_nome'] ?? '—') ?></p>
                        <p><strong>Data Registro:</strong> <?= date('d/m/Y H:i', strtotime($m['data_registro'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif($ativo_selecionado): ?>
            <p class="no-data">Nenhuma manutenção registrada para este ativo.</p>
        <?php else: ?>
            <p class="no-data">Selecione um ativo para visualizar o histórico.</p>
        <?php endif; ?>
    </div>
</main>

<?php include('footer.php'); ?>

<script>
document.getElementById('ativoSelect').addEventListener('change', function() {
    const id = this.value;
    if(id) {
        window.location.href = `manutencao.php?ativo_id=${id}`;
    } else {
        window.location.href = 'manutencao.php';
    }
});
</script>
</body>
</html>
