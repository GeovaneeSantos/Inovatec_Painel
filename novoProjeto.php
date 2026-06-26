<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $etapaUm = array("TASK1","TASK2","TASK3");



    require_once 'config/conexao.php';

    $centroDeCusto = isset($_POST['centroDeCustos']) ? trim($_POST['centroDeCustos']) : '';
    $nomeDoProjeto = isset($_POST['nomeProjeto']) ? trim($_POST['nomeProjeto']) : '';
    $cliente = isset($_POST['cliente']) ? trim($_POST['cliente']) : '';
    $dataPrev = isset($_POST['dataPrev']) ? trim($_POST['dataPrev']) : '';

    if ($centroDeCusto !== '' && $nomeDoProjeto !== '' && $cliente !== '' && $dataPrev !== '') {
        try {
            $sqlInsert = "INSERT INTO projetos (centro_Cust, nome_proj, cliente, dt_termino) VALUES (:centro_Cust, :nome_proj, :cliente, :dataPrev)";
            $stmtInsert = $conexao->prepare($sqlInsert);

            $stmtInsert->bindParam(':centro_Cust', $centroDeCusto, PDO::PARAM_STR);
            $stmtInsert->bindParam(':nome_proj', $nomeDoProjeto, PDO::PARAM_STR);
            $stmtInsert->bindParam(':cliente', $cliente, PDO::PARAM_STR);
            $stmtInsert->bindParam(':dataPrev', $dataPrev, PDO::PARAM_STR);

            if ($stmtInsert->execute()) {
                $rows = $stmtInsert->rowCount();
                header('Location: index.php');
                exit();
            } else {
                $err = $stmtInsert->errorInfo();
                error_log('Erro ao executar INSERT: ' . implode(' | ', $err));
                header("HTTP/1.1 500 Internal Server Error");
                exit();
            }


        } catch (PDOException $e) {
            header("HTTP/1.1 404 Not Found");
            error_log("Erro no registro: " . $e->getMessage());
            header('Location: ../index.php');
            exit();
        }
    }
}else{
    header("HTTP/1.1 404 Not Found");
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
                        require_once "views/topNavigation.php";
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