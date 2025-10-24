<?php
include('conexao.php');
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Busca os dados do usuário logado
$stmt = $conexao->prepare("SELECT nome, email, senha_hash, nivel_permissao, setor FROM usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$mensagem = '';
$tipo_alerta = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"]);
    $senha_atual = $_POST["senha_atual"] ?? '';
    $nova_senha = $_POST["nova_senha"] ?? '';
    $setor = trim($_POST["setor"]);

    // Atualiza nome e setor
    $update_stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, setor = ? WHERE usuario_id = ?");
    $update_stmt->bind_param("ssi", $nome, $setor, $id_usuario);
    $update_stmt->execute();

    // Atualiza senha, se foi preenchida
    if (!empty($senha_atual) && !empty($nova_senha)) {
        if (password_verify($senha_atual, $usuario["senha_hash"])) {
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $update_senha = $conexao->prepare("UPDATE usuarios SET senha_hash = ? WHERE usuario_id = ?");
            $update_senha->bind_param("si", $nova_senha_hash, $id_usuario);
            $update_senha->execute();

            $mensagem = 'Informações e senha atualizadas com sucesso!';
            $tipo_alerta = 'success';
        } else {
            $mensagem = 'Informações atualizadas, mas a senha atual está incorreta.';
            $tipo_alerta = 'warning';
        }
    } else {
        $mensagem = 'Informações atualizadas com sucesso!';
        $tipo_alerta = 'success';
    }

    // Atualiza nome da sessão
    $_SESSION["nome_usuario"] = $nome;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Gestão de Cursos</title>
    <link rel="stylesheet" href="../css/perfil.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include('nav.php'); ?>

    <main>
        <div class="titulo">
            <h1><i class="fas fa-user-circle"></i> Meu Perfil</h1>
            <p>Visualize e edite suas informações pessoais.</p>
            <div class="linha"></div>
        </div>

        <section class="container-form">
            <form method="POST">
                <h3>Informações Pessoais</h3>

                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" 
                       value="<?= htmlspecialchars($usuario['nome']); ?>" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" 
                       value="<?= htmlspecialchars($usuario['email']); ?>" readonly>

                <label for="nivel_permissao">Nível de Permissão</label>
                <input type="text" id="nivel_permissao" 
                       value="<?= ucfirst(htmlspecialchars($usuario['nivel_permissao'])); ?>" readonly>

                <label for="setor">Setor</label>
                <input type="text" id="setor" name="setor" 
                       value="<?= htmlspecialchars($usuario['setor']); ?>" placeholder="Ex: RH, TI, Educação">

                <h3>Alterar Senha</h3>

                <label for="senha_atual">Senha Atual</label>
                <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual">

                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite sua nova senha">

                <div class="botoes">
                    <button type="submit">Salvar Alterações</button>
                    <button type="button" id="sair">Sair</button>
                </div>
            </form>
        </section>
    </main>

    <?php include('footer.php'); ?>

    <script>
        // Confirmação de logout
        document.getElementById('sair').addEventListener('click', () => {
            Swal.fire({
                title: 'Deseja sair?',
                text: 'Você será desconectado da sua conta.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#DA020E',
                cancelButtonColor: '#555'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });

        <?php if ($mensagem): ?>
            Swal.fire({
                title: '',
                text: '<?= $mensagem ?>',
                icon: '<?= $tipo_alerta ?>'
            }).then(() => {
                window.location.href = 'perfil.php';
            });
        <?php endif; ?>
    </script>
</body>
</html>
