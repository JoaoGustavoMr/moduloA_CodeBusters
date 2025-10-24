<?php
include('nav.php');
include_once('conexao.php');

$id_usuario = $_SESSION['id_usuario'] ?? 0;

// Pega as notificações do usuário (mais recentes primeiro)
$notificacoes_sql = "SELECT * 
                     FROM notificacoes 
                     WHERE usuario_id = ? 
                     ORDER BY data_envio DESC";
$stmt = $conexao->prepare($notificacoes_sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$notificacoes_result = $stmt->get_result();

// Pega ativos cuja garantia expira em até 7 dias (dinâmico)
$garantia_sql = "SELECT nome, garantia_fim, localizacao 
                 FROM ativos 
                 WHERE garantia_fim IS NOT NULL 
                 AND DATEDIFF(garantia_fim, CURDATE()) BETWEEN 0 AND 7
                 ORDER BY garantia_fim ASC";
$garantia_result = $conexao->query($garantia_sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificações</title>
<link rel="stylesheet" href="../css/notificacao.css">
<script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>

<main class="main-content notificacoes-container">
    <h2>Notificações</h2>

    <?php if ($notificacoes_result->num_rows > 0): ?>
        <?php while($n = $notificacoes_result->fetch_assoc()): ?>
            <div class="notificacao-card">
                <h4>
                    <?php 
                    // Ícone por tipo
                    if ($n['tipo'] === 'ativo') echo '<i class="fa-solid fa-boxes"></i> ';
                    elseif ($n['tipo'] === 'manutencao') echo '<i class="fa-solid fa-tools"></i> ';
                    elseif ($n['tipo'] === 'garantia') echo '<i class="fa-solid fa-triangle-exclamation"></i> ';
                    ?>
                    <?= htmlspecialchars($n['titulo']) ?>
                </h4>
                <p><?= htmlspecialchars($n['mensagem']) ?></p>
                <p><strong>Enviado em:</strong> <?= date('d/m/Y H:i', strtotime($n['data_envio'])) ?></p>
                <p><strong>Status:</strong> <?= $n['lida'] ? 'Lida' : '<span class="badge-alarme">Não lida</span>' ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Você não possui notificações no momento.</p>
    <?php endif; ?>

    <?php if ($garantia_result->num_rows > 0): ?>
        <h3>Ativos Próximos da Garantia</h3>
        <?php while($g = $garantia_result->fetch_assoc()): ?>
            <div class="notificacao-card">
                <h4><i class="fa-solid fa-triangle-exclamation"></i> Garantia Próxima</h4>
                <p>O ativo <strong><?= htmlspecialchars($g['nome']) ?></strong> possui garantia que expira em <span class="badge-alarme"><?= (new DateTime($g['garantia_fim']))->diff(new DateTime())->days ?> dias</span>.</p>
                <p><strong>Localização:</strong> <?= htmlspecialchars($g['localizacao']) ?></p>
                <p><strong>Status:</strong> <span class="badge-alarme">Não lida</span></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php include('footer.php'); ?>
</body>
</html>
