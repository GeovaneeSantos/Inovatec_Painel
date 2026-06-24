<?php
// Configurações
$servidor = "localhost";
$usuario = "root";
$senhaBd = "";
$banco = "inovatec_bd";

// DSN (Data Source Name)
$dsn = "mysql:host=$servidor;dbname=$banco;charset=utf8mb4";

// Criação da conexão
try {
    $conexao = new PDO($dsn, $usuario, $senhaBd);

    // Configurações de segurança e erro
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexao->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Em caso de erro, efetua log do detalhe e paramos a execução
    error_log("Falha na conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados.");
}
