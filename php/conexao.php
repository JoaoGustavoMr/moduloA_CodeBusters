<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$db_banco = 'gestao_cursos';

$conexao = new mysqli($dbhost, $dbuser, $dbpassword, $db_banco);

if ($conexao->connect_error) {
    die("CONEXAO FALHOU: " . $conexao->connect_error);
}

?>