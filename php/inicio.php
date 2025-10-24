<?php
include('nav.php'); 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Gestão de Ativos</title>
    <link rel="stylesheet" href="../css/inicio.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>

<main class="main-content">

    <section class="hero-banner">
        <div class="hero-text">
            <h1>Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome_usuario']); ?>!</h1>
            <p>Visualize seus ativos e manutenções, acompanhe o histórico e mantenha tudo organizado de forma prática.</p>
            <a href="ativos.php" class="btn-hero"><i class="fa-solid fa-boxes"></i>   Ver Meus Ativos</a>
        </div>
        <div class="hero-image">
            <img src="../img/cursos livres com certificado online.webp" alt="Banner de Ativos">
        </div>
    </section>

    <h2 class="info-sections-title">Acessos Rápidos</h2> 
    <section class="cards-grid">
        <div class="card">
            <i class="fa-solid fa-boxes"></i>
            <h3>Meus Ativos</h3>
            <p>Veja todos os ativos que você está responsável ou que estão disponíveis no sistema.</p>
            <a href="ativos.php" class="btn-card">Acessar</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-tools"></i>
            <h3>Histórico de Manutenção</h3>
            <p>Consulte o histórico de manutenções dos ativos que você acompanha.</p>
            <a href="manutencao.php" class="btn-card">Visualizar</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-user"></i>
            <h3>Perfil</h3>
            <p>Atualize suas informações, senha e preferências de visualização do sistema.</p>
            <a href="perfil.php" class="btn-card">Editar</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-bell"></i>
            <h3>Notificações e Alertas</h3>
            <p>Fique informado sobre status críticos de ativos, manutenção pendente ou avisos importantes.</p>
            <a href="notificacoes.php" class="btn-card">Visualizar</a>
        </div>
    </section>

    <h2 class="info-sections-title">Últimas Atualizações</h2> 
    <section class="info-sections">
        <div class="info-section">
            <h4>Novos Ativos</h4>
            <div class="card-divider"></div>
            <p>Veja os ativos recentemente cadastrados ou atualizados no sistema.</p>
        </div>
        <div class="info-section">
            <h4>Manutenções Recentes</h4>
            <div class="card-divider"></div>
            <p>Acompanhe as manutenções finalizadas recentemente e seus detalhes.</p>
        </div>
        <div class="info-section">
            <h4>Alertas Gerais</h4>
            <div class="card-divider"></div>
            <p>Receba notificações sobre ativos com manutenção pendente ou status crítico.</p>
        </div>
        <div class="info-section">
            <h4>Notificações do Sistema</h4>
            <div class="card-divider"></div>
            <p>Visualize avisos importantes e mensagens gerais relacionadas aos ativos que você acompanha.</p>
        </div>
    </section>
</main>

<?php include('footer.php');  ?>

</body>
</html>
