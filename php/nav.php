<?php
include_once('conexao.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$stmt = $conexao->prepare("SELECT nome, nivel_permissao FROM usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario_nav = $result->fetch_assoc();

$nome_completo = $usuario_nav['nome'];
$nivel_usuario = $usuario_nav['nivel_permissao'];

$partes = explode(' ', trim($nome_completo));
$iniciais = '';
foreach ($partes as $parte) {
    if (!empty($parte)) {
        $iniciais .= strtoupper($parte[0]);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/8417e3dabe.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/nav.css">
</head>

<body>
    <nav>
    <div class="logo">
        <a href="inicio.php"><img src="../img/logo-youtan-branco.png" alt="Logo"></a>
    </div>

    <div class="hamburger" id="hamburger">
        <i class="fa-solid fa-bars"></i>
    </div>

    <ul class="navegacao" id="menu-nav">
        <li><a href="inicio.php">Início</a></li>
        <li><a href="ativos.php">Ativos</a></li>
        <li><a href="manutencao.php">Manutenção</a></li>
        <?php if ($nivel_usuario === 'admin'): ?>
            <li><a href="adicionar_ativo.php">Adicionar Ativo</a></li>
            <li><a href="adicionar_manutencao.php">Adicionar Manutenção</a></li>
            <li><a href="gerenciar_usuarios.php">Área Administrativa</a></li>
        <?php endif; ?>

        <li>
            <a href="notificacoes.php" class="notificacao">
                <i class="fa-solid fa-bell"></i>
            </a>
        </li>

        <li><a href="perfil.php" id="perfil"><?php echo htmlspecialchars($iniciais); ?></a></li>
    </ul>
</nav>


    <script>
        const hamburger = document.getElementById("hamburger");
        const menuNav = document.getElementById("menu-nav");

        hamburger.addEventListener("click", () => {
            menuNav.classList.toggle("active");
        });
    </script>
</body>
</html>
