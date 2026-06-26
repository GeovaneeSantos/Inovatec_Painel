<?php
require_once "config/conexao.php";

$sql = "SELECT * FROM projetos ORDER BY id";
$stmtSelect = $conexao->prepare($sql);
$stmtSelect->execute();
$result = $stmtSelect->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
    require_once 'config/conexao.php';

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id > 0) {
        $sql = ("DELETE FROM projetos WHERE id = :id");
        $stmtDelete = $conexao->prepare($sql);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmtDelete->execute()) {
            $rows = $stmtDelete->rowCount();
            header('Location: index.php');
            exit();
        } else {
            $err = $stmtDelete->errorInfo();
            error_log('Erro ao executar DELETE: ' . implode(' | ', $err));
            header("HTTP/1.1 500 Internal Server Error");
            exit();
        }
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
                        require_once "views/topNavigation.php";
                        ?>
                        <!-- /top navigation -->

                        <!-- page content -->
                        <div class="right_col col-md-12" role="main">
                            <div class="x_panel">

                                <div class="x_title">
                                    <h2>
                                        Projetos
                                    </h2>
                                </div>

                                <div class="x_content">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome do Projeto</th>
                                                <th>#</th>
                                                <th>Etapa 1</th>
                                                <th>Etapa 2</th>
                                                <th>Etapa 3</th>
                                                <th>Etapa 4</th>
                                                <th>Data Limite</th>
                                                <th>
                                                    <div class="col-sm-12 col-12 text-center gap-2 d-grid">
                                                        <a href="novoProjeto.php" class="btn btn-success btn-sm">
                                                            Criar Projeto
                                                        </a>
                                                    </div>
                                                </th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <?php
                                            // Laço de repetição para passar por todos os projetos
                                            if (sizeof($result) > 0) {
                                                for ($i = 0; $i < sizeof($result); $i++) { ?>

                                                    <tr>
                                                        <td>

                                                            <?php
                                                            echo $result[$i]['centro_Cust'] .
                                                                " " .
                                                                $result[$i]['cliente'] .
                                                                " " .
                                                                $result[$i]['nome_proj'];

                                                            ?>

                                                        </td>
                                                        <th scope="row">
                                                            <?php
                                                            echo $result[$i]['id'];
                                                            ?>
                                                        </th>
                                                        <td>
                                                            <div style="cursor: pointer" id="teste">
                                                                <div class="progress progress_sm" style="width: 70%;">
                                                                    <div class="progress-bar bg-green" role="progressbar"
                                                                        data-transitiongoal="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="cursor: pointer"
                                                                project-id="<?php echo $result[$i]['id']; ?>">
                                                                <div class="progress progress_sm" style="width: 70%;">
                                                                    <div class="progress-bar bg-green" role="progressbar"
                                                                        data-transitiongoal="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="cursor: pointer"
                                                                project-id="<?php echo $result[$i]['id']; ?>">
                                                                <div class="progress progress_sm" style="width: 70%;">
                                                                    <div class="progress-bar bg-green" role="progressbar"
                                                                        data-transitiongoal="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="cursor: pointer"
                                                                project-id="<?php echo $result[$i]['id']; ?>">
                                                                <div class="progress progress_sm" style="width: 70%;">
                                                                    <div class="progress-bar bg-green" role="progressbar"
                                                                        data-transitiongoal="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span>
                                                                <?php
                                                                echo $result[$i]['dt_termino'];
                                                                ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button
                                                                type="button"
                                                                class="btn btn-danger btn-sm btn-excluir-projeto"
                                                                title="Excluir"
                                                                data-toggle="modal"
                                                                data-target="#modalExcluirProjeto"
                                                                data-id="<?php echo $result[$i]['id']; ?>"
                                                                data-name="
                                                            <?php

                                                            echo $result[$i]['centro_Cust'] .
                                                                " " .
                                                                $result[$i]['cliente'] .
                                                                " " .
                                                                $result[$i]['nome_proj'];

                                                            ?>
                                                             ">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Nenhum projeto cadastrado.</td>
                                                </tr>

                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

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

                                <form method="post" action="index.php" class="m-0">

                                    <input type="hidden" name="acao" value="excluir">

                                    <input type="hidden" name="id" id="idProjetoExcluir" value="">

                                    <button type="submit" class="btn btn-danger">Excluir</button>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>

                <?php
                require_once "config/scripts.php";
                ?>

                <script>
                    (function() {
                            var botoesExcluir = document.querySelectorAll('.btn-excluir-projeto');
                            var campoIdExcluir = document.getElementById('idProjetoExcluir');
                            var nomeProjetoExcluir = document.getElementById('nomeProjetoExcluir');

                            var teste = document.getElementById('teste');

                            for (var i = 0; i < botoesExcluir.length; i++) {
                                botoesExcluir[i].addEventListener('click', function() {
                                    campoIdExcluir.value = this.getAttribute('data-id');
                                    nomeProjetoExcluir.textContent = this.getAttribute('data-name');
                                });

                                for (var i = 0; i < teste.length; i++) {
                                    teste[i].addEventListener('click', function() {
                                        console.log("teste ok")
                                    });

                                }
                            })();
                </script>
</body>

</html>