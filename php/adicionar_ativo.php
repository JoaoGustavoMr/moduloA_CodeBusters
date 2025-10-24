<?php
include('nav.php');
include('../php/conexao.php');

if ($_SESSION['nivel_permissao'] !== 'admin') {
    header("Location: inicio.php");
    exit;
}

$nome = $categoria = $valor = $data_aquisicao = $numero_serie = $status = $localizacao = $garantia_fim = $responsavel_id = "";
$alerta = "";
$mensagem = "";

$colaboradores = $conexao->query("SELECT usuario_id, nome FROM usuarios WHERE nivel_permissao = 'colaborador' ORDER BY nome ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $conexao->real_escape_string($_POST['nome']);
    $categoria = $conexao->real_escape_string($_POST['categoria']);
    $valor = $conexao->real_escape_string($_POST['valor']);
    $data_aquisicao = $conexao->real_escape_string($_POST['data_aquisicao']);
    $numero_serie = $conexao->real_escape_string($_POST['numero_serie']);
    $status = $conexao->real_escape_string($_POST['status']);
    $localizacao = $conexao->real_escape_string($_POST['localizacao']);
    $responsavel_id = intval($_POST['responsavel_id']);
    $garantia_fim = $conexao->real_escape_string($_POST['garantia_fim']);

    $sql = "INSERT INTO ativos (nome, categoria, valor, data_aquisicao, numero_serie, status, localizacao, responsavel_id, garantia_fim)
            VALUES ('$nome', '$categoria', '$valor', '$data_aquisicao', '$numero_serie', '$status', '$localizacao', '$responsavel_id', '$garantia_fim')";

    if ($conexao->query($sql)) {
        $alerta = "sucesso";
        $mensagem = "Ativo adicionado com sucesso!";
    } else {
        $alerta = "erro";
        $mensagem = "Erro ao adicionar ativo: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Ativo</title>
    <link rel="stylesheet" href="../css/gerenciar_usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<main class="main-container">
    <div class="title-wrapper">
        <h2><i class="fas fa-box"></i> Adicionar Ativo</h2>
        <p class="subtitle">Preencha as informações do novo ativo.</p>
    </div>

    <form id="formAtivo" action="" method="POST" class="form-usuario">
        <div class="form-group">
            <label for="nome">Nome do Ativo:</label>
            <input type="text" name="nome" id="nome" placeholder="Ex: Computador, Impressora, Mesa" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoria:</label>
            <input type="text" name="categoria" id="categoria" placeholder="Ex: Eletrônico, Mobiliário, Máquinário" required>
        </div>

        <div class="form-group">
            <label for="valor">Valor (R$):</label>
            <input type="number" name="valor" id="valor" step="0.01" min="0" required>
        </div>

        <div class="form-group">
            <label for="data_aquisicao">Data de Aquisição:</label>
            <input type="date" name="data_aquisicao" id="data_aquisicao" required>
        </div>

        <div class="form-group">
            <label for="numero_serie">Número de Série:</label>
            <input type="text" name="numero_serie" id="numero_serie" required>
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="">Selecione</option>
                <option value="em uso">Em uso</option>
                <option value="em manutenção">Em manutenção</option>
                <option value="disponível">Disponível</option>
                <option value="baixado">Baixado</option>
            </select>
        </div>

        <div class="form-group">
            <label for="localizacao">Localização:</label>
            <input type="text" name="localizacao" id="localizacao" placeholder="Ex: Sala 12, Escritório Central" required>
        </div>

        <div class="form-group">
            <label for="responsavel_id">Responsável:</label>
            <select name="responsavel_id" id="responsavel_id" required>
                <option value="">Selecione o colaborador</option>
                <?php while ($col = $colaboradores->fetch_assoc()): ?>
                    <option value="<?= $col['usuario_id'] ?>"><?= htmlspecialchars($col['nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="garantia_fim">Garantia até:</label>
            <input type="date" name="garantia_fim" id="garantia_fim">
        </div>

        <button type="submit" class="botaoadd">Adicionar Ativo</button>
    </form>
</main>

<?php include('footer.php'); ?>

<script>
document.getElementById('formAtivo').addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
        title: 'Confirmação',
        text: "Deseja realmente adicionar este ativo?",
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

<?php if($alerta === "sucesso"): ?>
Swal.fire({
    icon: 'success',
    title: 'Sucesso',
    text: '<?= $mensagem ?>',
    confirmButtonColor: '#0d6efd'
}).then(() => {
    window.location.href = 'ativos.php';
});
<?php elseif($alerta === "erro"): ?>
Swal.fire({
    icon: 'error',
    title: 'Erro',
    text: '<?= $mensagem ?>',
    confirmButtonColor: '#d33'
});
<?php endif; ?>
</script>
</body>
</html>
