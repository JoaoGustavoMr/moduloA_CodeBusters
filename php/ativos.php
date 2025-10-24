<?php
include('nav.php');

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$nivel = $_SESSION['nivel_permissao'] ?? 'colaborador';

// Consulta ativos com LEFT JOIN para pegar nome do responsável
$sql = "SELECT a.*, u.nome AS responsavel_nome
        FROM ativos a
        LEFT JOIN usuarios u ON a.responsavel_id = u.usuario_id
        ORDER BY a.ativo_id DESC";
$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Ativos</title>
<link rel="stylesheet" href="../css/ativos.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<main class="main-container">
    <div class="title-wrapper">
        <h2><i class="fas fa-boxes"></i> Ativos</h2>
        <p class="subtitle">Visualize e gerencie os ativos cadastrados no sistema</p>
    </div>

    <?php if ($nivel === 'admin'): ?>
        <a href="adicionar_ativo.php" class="btn btn-success mb-3">+ Adicionar Ativo</a>
    <?php endif; ?>

    <div class="card-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($ativo = $result->fetch_assoc()): ?>
                <div class="card" data-id="<?= $ativo['ativo_id'] ?>">
                    <h3><i class="fas fa-box"></i> <?= htmlspecialchars($ativo['nome']) ?></h3>

                    <div class="info">
                        <p><strong>Categoria:</strong> <?= htmlspecialchars($ativo['categoria']) ?></p>
                        <p><strong>Valor:</strong> R$ <?= number_format($ativo['valor'], 2, ',', '.') ?></p>
                        <p><strong>Data de Aquisição:</strong> <?= date('d/m/Y', strtotime($ativo['data_aquisicao'])) ?></p>
                        <p><strong>Nº de Série:</strong> <?= htmlspecialchars($ativo['numero_serie']) ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($ativo['status']) ?></p>
                        <p><strong>Localização:</strong> <?= htmlspecialchars($ativo['localizacao']) ?></p>
                        <p><strong>Responsável:</strong> <?= htmlspecialchars($ativo['responsavel_nome'] ?? '—') ?></p>
                        <p><strong>Garantia até:</strong> <?= $ativo['garantia_fim'] ? date('d/m/Y', strtotime($ativo['garantia_fim'])) : '—' ?></p>
                    </div>

                    <div class="card-actions">
                        <?php if ($nivel === 'admin' || ($nivel === 'colaborador' && $ativo['responsavel_id'] == $id_usuario)): ?>
                            <a href="#" class="btn btn-danger btn-sm btn-delete"
                               data-id="<?= $ativo['ativo_id'] ?>"
                               data-nome="<?= htmlspecialchars($ativo['nome']) ?>">
                               Excluir
                            </a>

                            <a href="#" class="btn btn-primary btn-sm btn-edit" id="botaoeditar"
                               data-id="<?= $ativo['ativo_id'] ?>"
                               data-nome="<?= htmlspecialchars($ativo['nome']) ?>"
                               data-categoria="<?= htmlspecialchars($ativo['categoria']) ?>"
                               data-valor="<?= $ativo['valor'] ?>"
                               data-status="<?= htmlspecialchars($ativo['status']) ?>"
                               data-localizacao="<?= htmlspecialchars($ativo['localizacao']) ?>">
                               Editar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-data">Nenhum ativo cadastrado ainda.</p>
        <?php endif; ?>
    </div>
</main>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3><i class="fas fa-edit"></i> Editar Ativo</h3>
        <form id="formEditarAtivo" action="editar_ativo_logic.php" method="POST">
            <input type="hidden" name="ativo_id" id="modalId">
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="nome" id="modalNome" required>
            </div>
            <div class="form-group">
                <label>Categoria:</label>
                <input type="text" name="categoria" id="modalCategoria" required>
            </div>
            <div class="form-group">
                <label>Valor (R$):</label>
                <input type="number" name="valor" step="0.01" id="modalValor" required>
            </div>
            <div class="form-group">
                <label>Status:</label>
                <select name="status" id="modalStatus">
                    <option value="em uso">Em uso</option>
                    <option value="em manutenção">Em manutenção</option>
                    <option value="disponível">Disponível</option>
                    <option value="baixado">Baixado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Localização:</label>
                <input type="text" name="localizacao" id="modalLocalizacao" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();
        const id = this.dataset.id;
        const nome = this.dataset.nome;
        const card = this.closest('.card');

        Swal.fire({
            title: `Excluir ativo "${nome}"?`,
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then(result => {
            if(result.isConfirmed){
                fetch(`excluir_ativo.php?id=${id}`, { method: 'GET' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'sucesso') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Excluído!',
                            text: data.mensagem,
                            showConfirmButton: false,
                            timer: 1200
                        });
                        card.style.opacity = "0";
                        setTimeout(() => card.remove(), 400);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Erro', text: data.mensagem });
                    }
                });
            }
        });
    });
});

const modal = document.getElementById("editModal");
const closeBtn = modal.querySelector(".close");

document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", e => {
        e.preventDefault();
        modal.style.display = "block";
        document.getElementById("modalId").value = btn.dataset.id;
        document.getElementById("modalNome").value = btn.dataset.nome;
        document.getElementById("modalCategoria").value = btn.dataset.categoria;
        document.getElementById("modalValor").value = btn.dataset.valor;
        document.getElementById("modalStatus").value = btn.dataset.status;
        document.getElementById("modalLocalizacao").value = btn.dataset.localizacao;
    });
});

closeBtn.onclick = () => modal.style.display = "none";
window.onclick = e => { if (e.target === modal) modal.style.display = "none"; };

document.getElementById("formEditarAtivo").addEventListener("submit", function(e){
    e.preventDefault();
    const formData = new FormData(this);

    fetch("editar_ativo_logic.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'sucesso') {
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: data.mensagem, showConfirmButton: false, timer: 1500 });
            modal.style.display = "none";
            setTimeout(() => window.location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'Erro!', text: data.mensagem });
        }
    });
});
</script>
</body>
</html>
