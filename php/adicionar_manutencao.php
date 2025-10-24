<?php
include('nav.php');
include('../php/conexao.php');

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$nivel = $_SESSION['nivel_permissao'] ?? 'colaborador';

// Consulta ativos disponíveis para seleção (todos)
$ativos_result = $conexao->query("SELECT ativo_id, nome FROM ativos ORDER BY nome ASC");

$alerta = '';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ativo_id = intval($_POST['ativo_id'] ?? 0);
    $tipo = $conexao->real_escape_string(trim($_POST['tipo'] ?? ''));
    $data = $conexao->real_escape_string($_POST['data'] ?? '');
    $responsavel_tecnico = $conexao->real_escape_string(trim($_POST['responsavel_tecnico'] ?? ''));
    $custo = floatval($_POST['custo'] ?? 0);
    $descricao = $conexao->real_escape_string(trim($_POST['descricao'] ?? ''));

    if (!$ativo_id || !$tipo || !$data || !$responsavel_tecnico || $custo < 0 || !$descricao) {
        $alerta = 'erro';
        $mensagem = 'Todos os campos devem ser preenchidos corretamente.';
    } else {
        $sql = "INSERT INTO manutencoes (ativo_id, tipo, data, responsavel_tecnico, custo, descricao, criado_por, data_registro)
                VALUES ($ativo_id, '$tipo', '$data', '$responsavel_tecnico', $custo, '$descricao', $id_usuario, NOW())";

        if ($conexao->query($sql)) {
            $alerta = 'sucesso';
            $mensagem = 'Manutenção adicionada com sucesso!';
        } else {
            $alerta = 'erro';
            $mensagem = 'Erro ao adicionar manutenção: ' . $conexao->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Manutenção</title>
    <link rel="stylesheet" href="../css/gerenciar_usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<main class="main-container">
    <div class="title-wrapper">
        <h2><i class="fas fa-user-cog"></i>  Adicionar Manutenção</h2>
        <p class="subtitle">Preencha os dados da nova manutenção.</p>
    </div>

    <form id="formManutencao" action="" method="POST" class="form-usuario">
        <div class="form-group">
            <label for="ativo_id">Ativo:</label>
            <select name="ativo_id" id="ativo_id" required>
                <option value="">Selecione o ativo</option>
                <?php while ($a = $ativos_result->fetch_assoc()): ?>
                    <option value="<?= $a['ativo_id'] ?>"><?= htmlspecialchars($a['nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de Manutenção:</label>
            <select name="tipo" id="tipo" required>
                <option value="">Selecione</option>
                <option value="Corretiva">Corretiva</option>
                <option value="Preditiva">Preditiva</option>
            </select>
        </div>

        <div class="form-group">
            <label for="data">Data da Manutenção:</label>
            <input type="date" name="data" id="data" required>
        </div>

        <div class="form-group">
            <label for="responsavel_tecnico">Técnico Responsável:</label>
            <input type="text" name="responsavel_tecnico" id="responsavel_tecnico" required>
        </div>

        <div class="form-group">
            <label for="custo">Custo (R$):</label>
            <input type="number" name="custo" id="custo" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" rows="4" required></textarea>
        </div>

        <button type="submit" class="botaoadd"><i class="fas fa-plus"></i> Adicionar Manutenção</button>
    </form>
</main>

<?php include('footer.php'); ?>

<script>
document.getElementById('formManutencao').addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
        title: 'Confirmação',
        text: "Deseja realmente adicionar esta manutenção?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, adicionar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});

<?php if($alerta === 'sucesso'): ?>
Swal.fire({
    icon: 'success',
    title: 'Sucesso!',
    text: '<?= $mensagem ?>',
    confirmButtonColor: '#0d6efd'
}).then(() => {
    window.location.href = 'manutencao.php';
});
<?php elseif($alerta === 'erro'): ?>
Swal.fire({
    icon: 'error',
    title: 'Erro!',
    text: '<?= $mensagem ?>',
    confirmButtonColor: '#d33'
});
<?php endif; ?>
</script>
</body>
</html>
