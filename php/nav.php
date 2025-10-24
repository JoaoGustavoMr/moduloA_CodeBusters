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

$stmt = $conexao->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario_nav = $result->fetch_assoc();

$nome_completo = $usuario_nav['nome'];
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
            <a href="inicio.php"><img src="../img/image-removebg-preview (9).png" alt="Logo"></a>
        </div>

        <div class="hamburger" id="hamburger">
            <i class="fa-solid fa-bars"></i>
        </div>

        <ul class="navegacao" id="menu-nav">
            <li><a href="inicio.php">Início</a></li>
            <li><a href="cursos.php">Cursos</a></li>

            <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
                <li><a href="../php/gerenciar_usuarios.php">Usuários</a></li>
            <?php endif; ?>

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
