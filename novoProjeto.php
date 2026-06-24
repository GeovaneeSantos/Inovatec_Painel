<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
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
                <div class="col-lg-2 col-md-2 left_col">
                    <div class="left_col">
                        <div class="navbar nav_title" style="border: 0;">
                            <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Gentelella Alela!</span></a>
                        </div>
                        <div class="clearfix"></div>

                        <!-- menu profile quick info -->

                        <div class="profile clearfix">
                            <div class="profile_pic">
                                <img src="images/img.jpg" alt="..." class="img-circle profile_img">
                            </div>
                            <div class="profile_info">
                                <span>Welcome,</span>
                                <h2>John Doe</h2>
                            </div>
                        </div>
                        <!-- /menu profile quick info -->
                        <br />

                        <!-- sidebar menu -->
                        <?php
                        require_once "views/sidemenu.php";
                        ?>
                        <!-- /sidebar menu -->

                        <!-- /menu footer buttons -->
                        <?php
                        require_once "views/menuFooter.php";
                        ?>
                        <!-- /menu footer buttons -->

                    </div>
                </div>
                <div class="col-lg-10 col-md-12 right_col_wrapper">
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

    <div class="modal fade" id="modalExcluirProjeto" tabindex="-1" role="dialog" aria-labelledby="modalExcluirProjetorLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcluirProjetoLabel">Excluir Projeto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Deseja realmente excluir o Projeto <strong id="nomeProjetoExcluir"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                    <form method="post" action="projetos.php" class="m-0">

                        <input type="hidden" name="acao" value="excluir">

                        <input type="hidden" name="id" id="idProjetoExcluir" value="">

                        <button type="submit" class="btn btn-danger">Excluir</button>

                    </form>

                </div>
            </div>
        </div>
    </div>
    <div id="modalTarefas" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tarefas</h2>
                <button onclick="fecharModal()" class="close-btn">&times;</button>
            </div>

            <div class="modal-body">
                <div id="loaderTarefas" style="display: none;">Carregando...</div>
                <table id="tabelaTarefas" style="width: 100%; display: none;">
                    <thead>
                        <tr>
                            <th>Tarefa</th>
                            <th>Status</th>
                            <th>Tempo (h)</th>
                        </tr>
                    </thead>
                    <tbody id="listaTarefas"></tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
    require_once "config/scripts.php";
    ?>
</body>

</html>