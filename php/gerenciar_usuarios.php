<?php
include('nav.php'); // nav.php já deve iniciar a sessão
$loggedUserId = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0;

// Lógica de deletar usuário
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    if ($delete_id == $loggedUserId) {
        header("Location: gerenciar_usuarios.php?status=proibido");
        exit;
    }

    $conexao->query("DELETE FROM usuarios WHERE id = $delete_id");
    header("Location: gerenciar_usuarios.php?status=deletado");
    exit;
}

// Buscar todos os usuários
$result = $conexao->query("SELECT * FROM usuarios ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="../css/gerenciar_usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<main class="main-container">
    <div class="title-wrapper">
        <h2><i class="fas fa-info-circle"></i> Gerenciar Usuários</h2>
        <p class="subtitle">Aqui você pode adicionar, editar ou excluir usuários do sistema.</p>
    </div>

    <div class="table-wrapper">
        <table class="table-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Data de Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['nome']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['tipo']) ?></td>
                            <td><?= $user['data_cadastro'] ?></td>
                            <td>
                                <!-- Botão Editar -->
                                <a href="#" 
                                   class="btn btn-primary btn-sm btn-edit" 
                                   data-id="<?= $user['id'] ?>"
                                   data-nome="<?= htmlspecialchars($user['nome']) ?>"
                                   data-email="<?= htmlspecialchars($user['email']) ?>"
                                   data-tipo="<?= $user['tipo'] ?>"
                                >Editar</a>

                                <!-- Botão Excluir -->
                                <a href="#" 
                                   class="btn btn-danger btn-sm btn-delete" 
                                   data-id="<?= $user['id'] ?>"
                                >Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="adicionar_usuario.php" class="btn btn-success mb-3">Adicionar Usuário</a>

    <!-- Modal de edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Editar Usuário</h3>
            <form id="formEditarModal" action="editar_usuario_logic.php" method="POST">
                <input type="hidden" name="id" id="modalId">

                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" id="modalNome" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" id="modalEmail" disabled>
                </div>

                <div class="form-group">
                    <label>Tipo:</label>
                    <select name="tipo" id="modalTipo" required>
                        <option value="">Selecione</option>
                        <option value="admin">Admin</option>
                        <option value="professor">Professor</option>
                        <option value="aluno">Aluno</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Salvar Alterações</button>
            </form>
        </div>
    </div>
</main>

<?php include('footer.php'); ?>

<script>
// Modal
const modal = document.getElementById("editModal");
const closeBtn = modal.querySelector(".close");

document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", function(e){
        const userId = parseInt(this.dataset.id);
        const loggedUserId = <?= $loggedUserId ?>;

        if(userId === loggedUserId){
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Ação não permitida',
                text: 'Você não pode editar seu próprio perfil.',
                confirmButtonColor: '#007bff'
            });
            return;
        }

        e.preventDefault();
        modal.style.display = "block";
        document.getElementById("modalId").value = this.dataset.id;
        document.getElementById("modalNome").value = this.dataset.nome;
        document.getElementById("modalEmail").value = this.dataset.email;
        document.getElementById("modalTipo").value = this.dataset.tipo;
    });
});

closeBtn.onclick = () => modal.style.display = "none";
window.onclick = e => { if(e.target === modal) modal.style.display = "none"; };

// Exclusão com SweetAlert2
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e){
        const userId = parseInt(this.dataset.id);
        const loggedUserId = <?= $loggedUserId ?>;

        if(userId === loggedUserId){
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Ação não permitida',
                text: 'Você não pode excluir seu próprio perfil.',
                confirmButtonColor: '#007bff'
            });
            return;
        }

        e.preventDefault();
        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja realmente excluir este usuário?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.isConfirmed){
                // Segunda confirmação
                Swal.fire({
                    title: 'Confirme novamente',
                    text: "Esta ação não pode ser desfeita. Tem certeza?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar'
                }).then((final) => {
                    if(final.isConfirmed){
                        window.location.href = `gerenciar_usuarios.php?delete_id=${userId}`;
                    }
                });
            }
        });
    });
});

// SweetAlert para status via GET
<?php if(isset($_GET['status'])): ?>
    <?php if($_GET['status'] === 'sucesso'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Sucesso',
            text: 'Usuário atualizado com sucesso!',
            confirmButtonColor: '#0d6efd'
        }).then(() => { window.location.href = 'gerenciar_usuarios.php'; });
    <?php elseif($_GET['status'] === 'erro'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Erro ao atualizar usuário!',
            confirmButtonColor: '#d33'
        });
    <?php elseif($_GET['status'] === 'proibido'): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Ação não permitida',
            text: 'Você não pode editar ou excluir seu próprio perfil.',
            confirmButtonColor: '#007bff'
        });
    <?php elseif($_GET['status'] === 'deletado'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Usuário excluído',
            text: 'O usuário foi excluído com sucesso.',
            confirmButtonColor: '#0d6efd'
        }).then(() => { window.location.href = 'gerenciar_usuarios.php'; });
    <?php endif; ?>
<?php endif; ?>
</script>
</body>
</html>
