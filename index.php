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
                                    <h2>Projetos
                                    </h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">

                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome do Projeto</th>
                                                <th>Etapa 1</th>
                                                <th>Etapa 2</th>
                                                <th>Etapa 3</th>
                                                <th>Etapa 4</th>
                                                <th>Data Limite</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Teste</td>
                                                <th scope="row">2</th>
                                                <td>
                                                    <button class="" data-toggle="modal"
                                                        data-target="#modalExcluirProjeto">
                                                        <div class="progress progress_sm" style="width: 70%;">
                                                            <div class="progress-bar bg-green" role="progressbar"
                                                                data-transitiongoal="">
                                                            </div>
                                                        </div>
                                                    </button>


                                                </td>
                                                <td>
                                                    <div class="progress progress_sm" style="width: 70%;">
                                                        <div class="progress-bar bg-green" role="progressbar"
                                                            data-transitiongoal="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress progress_sm" style="width: 70%;">
                                                        <div class="progress-bar bg-green" role="progressbar"
                                                            data-transitiongoal="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress progress_sm" style="width: 70%;">
                                                        <div class="progress-bar bg-green" role="progressbar"
                                                            data-transitiongoal="">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger btn-sm btn-excluir-celular"
                                                        title="Excluir"
                                                        data-toggle="modal"
                                                        data-target="#modalExcluirProjeto"
                                                        data-id="0"
                                                        data-modelo="0, ENT_QUOTES); ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- /page content -->


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
                    <?php
                    require_once "config/scripts.php";
                    ?>
</body>

</html>