<?php
include('conexao.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $nivel_selecionado = $_POST["nivel"]; 

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario["senha_hash"])) {
            if ($usuario["nivel_permissao"] === $nivel_selecionado) {
                $_SESSION["id_usuario"] = $usuario["usuario_id"];
                $_SESSION["nome_usuario"] = $usuario["nome"];
                $_SESSION["email_usuario"] = $usuario["email"];
                $_SESSION["nivel_permissao"] = $usuario["nivel_permissao"];
                $_SESSION["setor"] = $usuario["setor"];

                $sucesso = true;
                $nivel_usuario = $usuario["nivel_permissao"];
            } else {
                $nivel_incorreto = true;
                $nivel_correto = $usuario["nivel_permissao"];
            }
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
        <img src="../img/logo youtan.png" alt="Logo do Sistema" class="logo">
        <h2>Entrar</h2>

        <form method="POST" action="">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <label for="nivel">Nível de Permissão:</label>
            <select id="nivel" name="nivel" required>
                <option value="colaborador">Colaborador</option>
                <option value="admin">Administrador</option>
            </select>

            <button type="submit">Entrar</button>
        </form>
    </div>

    <?php if (isset($sucesso) && $sucesso): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Bem-vindo, <?= $_SESSION["nome_usuario"] ?>!',
                html: 'Login realizado com sucesso!<br><b>Tipo de usuário:</b> <?= ucfirst($nivel_usuario) ?>',
                confirmButtonColor: '#007bff'
            }).then(() => {
                window.location.href = 'inicio.php';
            });
        </script>

    <?php elseif (isset($nivel_incorreto)): ?>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Tipo de usuário incorreto!',
                html: 'Você selecionou <b><?= ucfirst($nivel_selecionado) ?></b>, mas sua conta é do tipo <b><?= ucfirst($nivel_correto) ?></b>.',
                confirmButtonColor: '#007bff'
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
