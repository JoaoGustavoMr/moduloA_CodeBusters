<?php
include('nav.php');

$id_aluno = $_SESSION['id_usuario'] ?? 0;

$sql = "SELECT c.*, u.nome AS professor_nome 
        FROM cursos c 
        LEFT JOIN usuarios u ON c.id_professor = u.id 
        ORDER BY c.id ASC";
$result = $conexao->query($sql);

$inscricoes = [];
if ($_SESSION['tipo_usuario'] === 'aluno') {
    $res = $conexao->query("SELECT id_curso FROM inscricoes WHERE id_aluno = $id_aluno");
    while ($row = $res->fetch_assoc()) {
        $inscricoes[] = $row['id_curso'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Cursos</title>
<link rel="stylesheet" href="../css/cursos.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<main class="main-container">
    <div class="title-wrapper">
        <h2><i class="fas fa-book"></i> Cursos</h2>
        <p class="subtitle">Cursos disponíveis no nosso sistema</p>
    </div>

    <?php if($_SESSION['tipo_usuario'] !== 'aluno'): ?>
        <a href="adicionar_curso.php" class="btn btn-success mb-3">Adicionar Curso</a>
    <?php endif; ?>

    <div class="card-container">
        <?php if($result->num_rows > 0): ?>
            <?php while($curso = $result->fetch_assoc()): ?>
                <div class="card" data-id="<?= $curso['id'] ?>">
                    <h3><?= htmlspecialchars($curso['titulo']) ?></h3>
                    <p><?= htmlspecialchars($curso['descricao']) ?></p>
                    <p class="professor">Professor: <?= htmlspecialchars($curso['professor_nome']) ?></p>
                    <div class="card-actions">
                        <?php if($_SESSION['tipo_usuario'] === 'aluno'): ?>
                            <?php if(in_array($curso['id'], $inscricoes)): ?>
                                <button class="btn btn-success btn-sm" disabled>Inscrito ✓</button>
                            <?php else: ?>
                                <a href="#" class="btn btn-primary btn-sm btn-inscrever" 
                                   data-id="<?= $curso['id'] ?>" 
                                   data-titulo="<?= htmlspecialchars($curso['titulo']) ?>">Inscrever-se</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="#" class="btn btn-primary btn-sm btn-edit" 
                               data-id="<?= $curso['id'] ?>"
                               data-titulo="<?= htmlspecialchars($curso['titulo']) ?>"
                               data-descricao="<?= htmlspecialchars($curso['descricao']) ?>">Editar</a>
                            <a href="#" class="btn btn-danger btn-sm btn-delete"
                               data-id="<?= $curso['id'] ?>"
                               data-titulo="<?= htmlspecialchars($curso['titulo']) ?>">Excluir</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhum curso encontrado.</p>
        <?php endif; ?>
    </div>

    <!-- Modal Editar Curso -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Editar Curso</h3>
            <form id="formEditarCurso" action="editar_curso_logic.php" method="POST">
                <input type="hidden" name="id" id="modalId">
                <div class="form-group">
                    <label>Título:</label>
                    <input type="text" name="titulo" id="modalTitulo" required>
                </div>
                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao" id="modalDescricao" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Salvar Alterações</button>
            </form>
        </div>
    </div>
</main>

<?php include('footer.php'); ?>

<script>
// === MODAL EDITAR CURSO ===
const modal = document.getElementById("editModal");
const closeBtn = modal.querySelector(".close");

// Abrir modal
document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", e => {
        e.preventDefault();
        modal.style.display = "block";
        document.getElementById("modalId").value = btn.dataset.id;
        document.getElementById("modalTitulo").value = btn.dataset.titulo;
        document.getElementById("modalDescricao").value = btn.dataset.descricao;
    });
});

// Fechar modal
closeBtn.onclick = () => modal.style.display = "none";
window.onclick = e => { if (e.target === modal) modal.style.display = "none"; };

// Enviar edição via AJAX
document.getElementById("formEditarCurso").addEventListener("submit", function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('id');
    const titulo = formData.get('titulo');
    const descricao = formData.get('descricao');

    fetch("editar_curso_logic.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'sucesso') {
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: data.mensagem, showConfirmButton: false, timer: 1500 });
            const card = document.querySelector(`.card[data-id="${id}"]`);
            if (card) {
                card.querySelector("h3").textContent = titulo;
                card.querySelector("p").textContent = descricao;
            }
            modal.style.display = "none";
        } else {
            Swal.fire({ icon: 'error', title: 'Erro!', text: data.mensagem });
        }
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Erro!', text: 'Erro ao conectar com o servidor.' }));
});

// Exclusão via AJAX
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();
        const id = this.dataset.id;
        const titulo = this.dataset.titulo;
        const card = this.closest('.card');

        Swal.fire({
            title: `Deseja excluir o curso "${titulo}"?`,
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if(result.isConfirmed){
                fetch(`excluir_curso.php?id=${id}`, { method: 'GET' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'sucesso') {
                        Swal.fire({ icon: 'success', title: 'Curso excluído!', text: data.mensagem, showConfirmButton: false, timer: 1500 });
                        card.style.transition = "all 0.4s ease";
                        card.style.opacity = "0";
                        card.style.transform = "scale(0.95)";
                        setTimeout(() => card.remove(), 400);
                    } else {
                        Swal.fire({ icon: 'error', title: 'Erro ao excluir', text: data.mensagem || 'Tente novamente mais tarde.' });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Erro de conexão', text: 'Não foi possível se conectar ao servidor.' }));
            }
        });
    });
});

// Inscrição via AJAX com confirmação
document.querySelectorAll('.btn-inscrever').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();

        const id = this.dataset.id;
        const titulo = this.dataset.titulo;
        const button = this;

        Swal.fire({
            title: `Quer se inscrever no curso "${titulo}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, inscrever',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (!result.isConfirmed) return;

            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Processando...';

            fetch(`inscrever_curso.php?id=${encodeURIComponent(id)}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'sucesso' || data.status === 'ja_inscrito') {
                    Swal.fire({
                        icon: data.status === 'sucesso' ? 'success' : 'info',
                        title: data.status === 'sucesso' ? 'Inscrito!' : 'Já inscrito',
                        text: data.mensagem,
                        showConfirmButton: false,
                        timer: 1400
                    });

                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                    button.textContent = 'Inscrito ✓';
                    button.disabled = true;
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: data.mensagem || 'Não foi possível inscrever-se no curso.' });
                    button.disabled = false;
                    button.textContent = originalText;
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Erro de conexão', text: 'Não foi possível se conectar ao servidor.' });
                button.disabled = false;
                button.textContent = originalText;
            });
        });
    });
});
</script>
</body>
</html>
