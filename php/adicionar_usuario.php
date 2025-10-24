<?php
include('nav.php');

$nome = $email = $senha = $tipo = "";
$alerta = ""; // variável para o tipo de alerta
$mensagem = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $conexao->real_escape_string($_POST['nome']);
    $email = $conexao->real_escape_string($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $conexao->real_escape_string($_POST['tipo']);

    // Verificar se o email já existe
    $check = $conexao->query("SELECT id FROM usuarios WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $alerta = "erro";
        $mensagem = "Email já cadastrado!";
    } else {
        // Inserir no banco
        $sql = "INSERT INTO usuarios (nome, email, senha, tipo, data_cadastro) VALUES ('$nome', '$email', '$senha', '$tipo', NOW())";
        if ($conexao->query($sql)) {
            $alerta = "sucesso";
            $mensagem = "Usuário adicionado com sucesso!";
        } else {
            $alerta = "erro";
            $mensagem = "Erro ao adicionar usuário: " . $conexao->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Usuário</title>
    <link rel="stylesheet" href="../css/gerenciar_usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <main class="main-container">
    <a href="gerenciar_usuarios.php" class="btn btn-secondary btn-voltar">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
        <div class="title-wrapper">
            <h2><i class="fas fa-user-plus"></i> Adicionar Usuário</h2>
            <p class="subtitle">Preencha os dados para criar um novo usuário.</p>
        </div>

        <!-- Botão de voltar -->

        <form id="formUsuario" action="" method="POST" class="form-usuario">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" required>
                    <option value="">Selecione</option>
                    <option value="admin" <?= $tipo==='admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="professor" <?= $tipo==='professor' ? 'selected' : '' ?>>Professor</option>
                    <option value="aluno" <?= $tipo==='aluno' ? 'selected' : '' ?>>Aluno</option>
                </select>
            </div>

            <button type="submit" class="botaoadd">Adicionar Usuário</button>
        </form>
    </main>

    <?php include('footer.php'); ?>

<script>
// Confirmação antes de enviar
document.getElementById('formUsuario').addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
        title: 'Confirmação',
        text: "Deseja realmente adicionar este usuário?",
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

// Exibir alertas de PHP
<?php if($alerta === "sucesso"): ?>
    Swal.fire({
        icon: 'success',
        title: 'Sucesso',
        text: '<?= $mensagem ?>',
        confirmButtonColor: '#0d6efd'
    }).then(() => {
        window.location.href = 'gerenciar_usuarios.php';
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
