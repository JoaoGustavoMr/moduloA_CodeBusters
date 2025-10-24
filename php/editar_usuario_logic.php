<?php
include('nav.php');
include('../conexao.php');

// Permite apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = intval($_POST['usuario_id']);
    $nome = $conexao->real_escape_string($_POST['nome']);
    $nivel_permissao = $conexao->real_escape_string($_POST['nivel_permissao']);
    $setor = $conexao->real_escape_string($_POST['setor']);

    // ⚠️ Impede que um colaborador altere informações
    if ($_SESSION['nivel_permissao'] === 'colaborador') {
        header("Location: gerenciar_usuarios.php?status=acesso_negado");
        exit;
    }

    // Atualiza os campos
    $sql = "UPDATE usuarios 
            SET nome = '$nome', nivel_permissao = '$nivel_permissao', setor = '$setor' 
            WHERE usuario_id = $usuario_id";

    if ($conexao->query($sql)) {
        header("Location: gerenciar_usuarios.php?status=sucesso");
        exit;
    } else {
        header("Location: gerenciar_usuarios.php?status=erro");
        exit;
    }

} else {
    header("Location: gerenciar_usuarios.php");
    exit;
}
?>
