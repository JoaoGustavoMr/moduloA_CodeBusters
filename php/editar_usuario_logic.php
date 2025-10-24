<?php
include('nav.php'); 
include('../conexao.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nome = $conexao->real_escape_string($_POST['nome']);
    $tipo = $conexao->real_escape_string($_POST['tipo']);

    $sql = "UPDATE usuarios SET nome = '$nome', tipo = '$tipo' WHERE id = $id";

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
