<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    require_once 'config/conexao.php';
    require_once "config/criaTarefas.php";

    $camposObrigatorios = ['centroDeCustos', 'nomeProjeto', 'cliente', 'dataPrev'];
    $faltando = [];

    foreach ($camposObrigatorios as $campo) {
        if (!isset($_POST[$campo]) || trim((string) $_POST[$campo]) === '') {
            $faltando[] = $campo;
        }
    }

    if (!empty($faltando)) {
        http_response_code(400);
        echo json_encode([
            'erro' => 'Parâmetro obrigatório ausente',
            'campos' => $faltando
        ]);
        exit;
    }

    $centroDeCusto = trim($_POST['centroDeCustos']);
    $nomeDoProjeto = trim($_POST['nomeProjeto']);
    $cliente = trim($_POST['cliente']);
    $dataPrev = trim($_POST['dataPrev']);

    try {
        $sqlInsert = "INSERT INTO projetos (centro_Cust, nome_proj, cliente, dt_termino) VALUES (:centro_Cust, :nome_proj, :cliente, :dataPrev)";
        $stmtInsert = $conexao->prepare($sqlInsert);

        $stmtInsert->bindParam(':centro_Cust', $centroDeCusto, PDO::PARAM_STR);
        $stmtInsert->bindParam(':nome_proj', $nomeDoProjeto, PDO::PARAM_STR);
        $stmtInsert->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmtInsert->bindParam(':dataPrev', $dataPrev, PDO::PARAM_STR);

        if ($stmtInsert->execute()) {
            $rows = $stmtInsert->rowCount();
            $sqlPegaUltID = "SELECT id FROM projetos where centro_Cust = :centro_Cust AND nome_proj = :nome_proj AND cliente = :cliente";
            $stmtPegaID = $conexao->prepare($sqlPegaUltID);
            $stmtPegaID->bindParam(':centro_Cust', $centroDeCusto, PDO::PARAM_STR);
            $stmtPegaID->bindParam(':nome_proj', $nomeDoProjeto, PDO::PARAM_STR);
            $stmtPegaID->bindParam(':cliente', $cliente, PDO::PARAM_STR);
            $stmtPegaID->execute();
            $resultId = $stmtPegaID->fetchAll();
            $idDoProjetoGerado = $resultId[0]['id'];
            insertTarefas($idDoProjetoGerado, $etapaInicial, "INICIAL");
            insertTarefas($idDoProjetoGerado, $etapaProducao, "PRODUCAO");
            insertTarefas($idDoProjetoGerado, $etapaSoftware, "SOFTWARE");
            insertTarefas($idDoProjetoGerado, $etapaFinalizacao, "FINALIZACAO");
            header('Location: index.php');
            exit();
        } else {
            $err = $stmtInsert->errorInfo();
            error_log('Erro ao executar INSERT: ' . implode(' | ', $err));
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inserir projeto']);
            exit();
        }
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            'erro' => 'Erro ao processar requisição',
            'detalhes' => $e->getMessage()
        ]);
        error_log("Erro no registro: " . $e->getMessage());
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
require_once "config/header.php";
?>

<body class="nav-md">
    <div class="body">
        <div class="main_container container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 right_col_wrapper">
                    <div class="row">

                        <!-- top navigation -->
                        <?php
                        require_once "views/topNavigationNoFilters.php";
                        ?>
                        <!-- /top navigation -->

                        <?php
                            require_once "resources/novoProjetoForm.php";
                        ?>

                        <!-- footer content -->
                        <footer class="col-md-12">
                            <div class="pull-right">
                                Inovatec Automação Industrial - <a href="https://inovatecautomacao.com.br/">Inovatec</a>
                            </div>
                            <div class="clearfix"></div>
                        </footer>
                    </div>
                </div>
                <!-- /footer content -->
            </div>
        </div>
    </div>

    <?php
    require_once "config/scripts.php";
    ?>
    
</body>

</html>