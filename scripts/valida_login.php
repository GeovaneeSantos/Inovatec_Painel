<?php
session_start();
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camposObrigatorios = ['nome', 'senha'];
    $faltando = [];

    foreach ($camposObrigatorios as $campo) {
        if (!isset($_POST[$campo]) || trim((string) $_POST[$campo]) === '') {
            $faltando[] = $campo;
        }
    }

    if (!empty($faltando)) {
        $_SESSION['error'] = "Parâmetro obrigatório ausente.";
        header('Location: ../login.php');
        exit();
    }

    $nome = trim($_POST['nome']);
    $senha = trim($_POST['senha']);

    try {
        $sqlSelect = "SELECT * FROM usuarios WHERE user = :user AND password = :password LIMIT 1";
        $stmtSelect = $conexao->prepare($sqlSelect);

        $stmtSelect->bindParam(':user', $nome, PDO::PARAM_STR);
        $stmtSelect->bindParam(':password', $senha, PDO::PARAM_STR);

        if ($stmtSelect->execute()) {
            $result = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['user'] === $nome && $result['password'] === $senha) {
                session_regenerate_id(true);
                $_SESSION['success'] = "Usuário Logado";
                $_SESSION['user_name'] = $nome;
                header('Location: ../index.php');
                exit();
            } else {
                $_SESSION['error'] = "Usuário ou senha incorretos.";
                header('Location: ../login.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Erro ao buscar usuário.";
            header('Location: ../login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erro ao processar requisição.";
        header('Location: ../login.php');
        exit();
    }
}
