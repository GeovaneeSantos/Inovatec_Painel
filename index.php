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
                                                    <div class="col-sm-9 col-9 text-center gap-2 d-grid">
                                                        <a href="novoProjeto.php" class="btn btn-success btn-sm">
                                                            Criar Projeto
                                                        </a>
                                                    </div>
                                                </th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <?php 
                                            if(sizeof($result) > 0){
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
                                                        <div style="cursor: pointer">
                                                            <div class="progress progress_sm" style="width: 70%;">
                                                                <div class="progress-bar bg-green" role="progressbar"
                                                                    data-transitiongoal="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="cursor: pointer">
                                                            <div class="progress progress_sm" style="width: 70%;">
                                                                <div class="progress-bar bg-green" role="progressbar"
                                                                    data-transitiongoal="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="cursor: pointer">
                                                            <div class="progress progress_sm" style="width: 70%;">
                                                                <div class="progress-bar bg-green" role="progressbar"
                                                                    data-transitiongoal="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="cursor: pointer">
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
                                            <?php }}
                                            else{
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
                <script>
                    (function() {
                        var botoesExcluir = document.querySelectorAll('.btn-excluir-projeto');
                        var campoIdExcluir = document.getElementById('idProjetoExcluir');
                        var nomeProjetoExcluir = document.getElementById('nomeProjetoExcluir');

                        for (var i = 0; i < botoesExcluir.length; i++) {
                            botoesExcluir[i].addEventListener('click', function() {
                                campoIdExcluir.value = this.getAttribute('data-id');
                                nomeProjetoExcluir.textContent = this.getAttribute('data-name');
                            });
                        }
                    })();
                </script>
</body>

</html>