<?php
include('conexao.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $confirma_senha = $_POST["confirma_senha"];

    // Define tipo de usuário como aluno
    $tipo = "aluno";

    // Verifica se as senhas conferem
    if ($senha !== $confirma_senha) {
        $erro_senha = true;
    } else {
        // Verifica se já existe o email
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $erro_email = true;
        } else {
            // Insere novo usuário (sempre aluno)
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conexao->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nome, $email, $senha_hash, $tipo);
            if ($stmt_insert->execute()) {
                $sucesso = true;
            } else {
                $erro_geral = true;
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Gestão de Cursos</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <img src="../img/logo.png" alt="Logo do Sistema" class="logo">

        <!-- TÍTULO -->
        <h2>Cadastro de Aluno</h2>

        <!-- FORMULÁRIO DE CADASTRO -->
        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <label for="confirma_senha">Confirme a senha:</label>
            <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme sua senha" required>

            <button type="submit">Cadastrar</button>
        </form>

        <div class="links-baixo">
            <p>Já tem uma conta? <a href="login.php">Entrar</a></p>
        </div>
    </div>

    <!-- ALERTAS -->
    <?php if (isset($sucesso) && $sucesso): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Cadastro realizado!',
                text: 'Você já pode entrar no sistema.',
                confirmButtonColor: '#007bff'
            }).then(() => {
                window.location.href = 'login.php';
            });
        </script>
    <?php elseif (isset($erro_senha)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Senhas não conferem',
                text: 'Digite novamente as senhas.',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php elseif (isset($erro_email)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'E-mail já cadastrado',
                text: 'Tente outro e-mail.',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php elseif (isset($erro_geral)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro no cadastro',
                text: 'Tente novamente mais tarde.',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php endif; ?>
</body>
</html>
