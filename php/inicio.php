<?php
include('nav.php'); 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Gestão de Cursos</title>
    <link rel="stylesheet" href="../css/inicio.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>

<main class="main-content">

    <section class="hero-banner">
        <div class="hero-text">
            <h1>Bem-vindo(a), <?php echo $_SESSION['nome_usuario']; ?>!</h1>
            <p>Gerencie seus cursos, acompanhe seus alunos e explore novas oportunidades de aprendizado.</p>
            <a href="meus_cursos.php" class="btn-hero"><i class="fa-solid fa-book"></i>   Explorar Cursos</a>
        </div>
        <div class="hero-image">
            <img src="../img/cursos livres com certificado online.webp" alt="Banner">
        </div>
    </section>

    <h2 class="info-sections-title">Informações e Recursos</h2> 
    <section class="cards-grid">
        <div class="card">
            <i class="fa-solid fa-book-open"></i>
            <h3>Meus Cursos</h3>
            <p>Gerencie cursos ativos, edite conteúdos, organize módulos e acompanhe progresso dos alunos.</p>
            <a href="meus_cursos.php" class="btn-card">Acessar</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-user-graduate"></i>
            <h3>Meus Alunos</h3>
            <p>Visualize desempenho individual, histórico de participação e status de inscrição.</p>
            <a href="meus_alunos.php" class="btn-card">Visualizar</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-calendar-days"></i>
            <h3>Agenda</h3>
            <p>Confira próximas aulas, reuniões, deadlines e eventos importantes no calendário.</p>
            <a href="agenda.php" class="btn-card">Abrir</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-bell"></i>
            <h3>Notificações</h3>
            <p>Receba alertas sobre inscrições, prazos, mensagens importantes e avisos do sistema.</p>
            <a href="notificacoes.php" class="btn-card">Abrir</a>
        </div>

        <div class="card">
            <i class="fa-solid fa-user"></i>
            <h3>Perfil</h3>
            <p>Atualize suas informações, senha, foto de perfil e preferências do painel.</p>
            <a href="perfil.php" class="btn-card">Editar</a>
        </div>
    </section>

    <h2 class="info-sections-title">Informações e Recursos</h2> 
    <section class="info-sections">
        <div class="info-section">
            <h4>Novidades</h4>
            <div class="card-divider"></div>
            <p>Descubra novos cursos, atualizações do sistema, workshops e materiais extras disponíveis.</p>
        </div>
        <div class="info-section">
            <h4>Dicas de Estudo</h4>
            <div class="card-divider"></div>
            <p>Explore recomendações de aprendizado, vídeos, artigos e técnicas de estudo para melhorar a performance.</p>
        </div>
        <div class="info-section">
            <h4>Suporte</h4>
            <div class="card-divider"></div>
            <p>Entre em contato com nossa equipe de suporte para tirar dúvidas ou relatar problemas técnicos.</p>
        </div>
        <div class="info-section">
            <h4>Eventos e Webinars</h4>
            <div class="card-divider"></div>
            <p>Fique por dentro de eventos, palestras online e webinars exclusivos para professores e alunos.</p>
        </div>
    </section>
</main>
<?php include('footer.php');  ?>

</body>
</html>
