<?php
include('conexao.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($senha, $usuario["senha"])) {
            $_SESSION["id_usuario"] = $usuario["id"];
            $_SESSION["nome_usuario"] = $usuario["nome"];
            $_SESSION["email_usuario"] = $usuario["email"];
            $_SESSION["tipo_usuario"] = $usuario["tipo"];
            $sucesso = true;
        } else {
            $senha_incorreta = true;
        }
    } else {
        $usuario_nao_encontrado = true;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Gestão de Cursos</title>
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <img src="../img/logo.png" alt="Logo do Sistema" class="logo">

        <h2>Entrar</h2>

        <form method="POST" action="">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <a href="recuperar_senha.php" class="opcoes">Esqueceu a senha?</a>

            <button type="submit">Entrar</button>
        </form>

        <div class="links-baixo">
            <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
    </div>

    <?php if (isset($sucesso) && $sucesso): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Bem-vindo!',
                text: 'Login realizado com sucesso!',
                confirmButtonColor: '#007bff'
            }).then(() => {
                window.location.href = 'inicio.php';
            });
        </script>
    <?php elseif (isset($senha_incorreta)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Senha incorreta',
                text: 'Verifique sua senha e tente novamente.',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php elseif (isset($usuario_nao_encontrado)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Usuário não encontrado',
                text: 'Verifique o e-mail informado.',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php endif; ?>
</body>
</html>
