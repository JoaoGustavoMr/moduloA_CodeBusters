<?php
include('nav.php');

if ($_SESSION['tipo_usuario'] === 'aluno') {
    echo "<script>
        alert('Você não tem permissão para acessar esta página.');
        window.location.href = 'gerenciar_cursos.php';
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $id_professor = $_SESSION['tipo_usuario'] === 'professor' ? $_SESSION['id_usuario'] : $_POST['id_professor'];

    $stmt = $conexao->prepare("INSERT INTO cursos (titulo, descricao, id_professor, data_criacao) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssi", $titulo, $descricao, $id_professor);

    if ($stmt->execute()) {
        $status = 'sucesso';
    } else {
        $status = 'erro';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Adicionar Curso</title>
<link rel="stylesheet" href="../css/cursos.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
        <main class="main-container">
            <a href="cursos.php" class="btn btn-secondary btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <div class="title-wrapper">
                <h2><i class="fas fa-plus-circle"></i> Adicionar Curso</h2>
                <p class="subtitle">Preencha as informações do curso abaixo.</p>
            </div>

            <form action="" method="POST" class="form-curso">
                <div class="form-group">
                    <label>Título:</label>
                    <input type="text" name="titulo" required>
                </div>

                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao" required></textarea>
                </div>

                <?php if($_SESSION['tipo_usuario'] === 'admin'): ?>
                    <div class="form-group">
                        <label>Professor:</label>
                        <select name="id_professor" required>
                            <option value="">Selecione</option>
                            <?php
                            $professores = $conexao->query("SELECT id, nome FROM usuarios WHERE tipo IN ('professor','admin') ORDER BY nome ASC");
                            while($prof = $professores->fetch_assoc()):
                            ?>
                                <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-success">Adicionar Curso</button>
            </form>
        </main>

<?php include('footer.php'); ?>

<?php if(isset($status) && $status === 'sucesso'): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Sucesso',
    text: 'Curso adicionado com sucesso!',
    confirmButtonColor: '#0d6efd'
}).then(()=>{ window.location.href = 'cursos.php'; });
</script>
<?php elseif(isset($status) && $status === 'erro'): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Erro',
    text: 'Não foi possível adicionar o curso.',
    confirmButtonColor: '#d33'
});
</script>
<?php endif; ?>
</body>
</html>
