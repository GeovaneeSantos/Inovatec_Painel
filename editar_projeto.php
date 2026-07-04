<?php
$idProj = 0;
$centroDeCusto = '';
$nomeDoProjeto = '';
$cliente = '';
$dataPrev = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idProj = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($idProj > 0) {
        require_once 'config/conexao.php';

        $sqlSelect = "SELECT id, centro_Cust, nome_proj, cliente, dt_termino FROM projetos WHERE id = :id";
        $stmtSelect = $conexao->prepare($sqlSelect);
        $stmtSelect->bindValue(':id', $idProj, PDO::PARAM_INT);
        $stmtSelect->execute();
        $projeto = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($projeto) {
            $centroDeCusto = $projeto['centro_Cust'];
            $nomeDoProjeto = $projeto['nome_proj'];
            $cliente = $projeto['cliente'];
            $dataPrev = $projeto['dt_termino'];
        } else {
            header('Location: index.php');
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/conexao.php';

    $camposObrigatorios = ['centroDeCustos', 'nomeProjeto', 'cliente', 'dataPrev', 'id'];
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
    $idProjeto = (int) $_POST['id'];

    if ($idProjeto <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do projeto inválido']);
        exit;
    }

    try {
        $sqlUpdate = "UPDATE projetos SET centro_Cust = :centro_Cust, nome_proj = :nome_proj, cliente = :cliente, dt_termino = :dataPrev WHERE id = :id";
        $stmtUpdate = $conexao->prepare($sqlUpdate);

        $stmtUpdate->bindParam(':centro_Cust', $centroDeCusto, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':nome_proj', $nomeDoProjeto, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':dataPrev', $dataPrev, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':id', $idProjeto, PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            header('Location: index.php');
            exit();
        } else {
            $err = $stmtUpdate->errorInfo();
            error_log('Erro ao executar UPDATE: ' . implode(' | ', $err));
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar projeto']);
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
                        <div class="col-lg-12 col-md-12 right_col" role="main">
                            <div class="">
                                <div class="page-title row">

                                    <div class="col-sm-6 col-12 text-right">
                                        <div class="row">
                                            <div class="offset-xl-7 col-xl-5 col-lg-12 col-md-12 col-sm-5 col-12 form-group pull-right top_search mt-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="x_panel">
                                            <div class="x_title">
                                                <h2>Editar Projeto</h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <br>
                                                <form class="form-horizontal form-label-left" method="post" action="editar_projeto.php">
                                                    <div class="form-group row">
                                                        <label class="control-label col-md-3 col-sm-3 col-12" for="centroDeCustos">Centro de Custos</label>
                                                        <div class="col-md-6 col-sm-9 col-12">
                                                            <input type="text" id="centroDeCustos" name="centroDeCustos" class="form-control" required value="<?php echo htmlspecialchars($centroDeCusto, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="control-label col-md-3 col-sm-3 col-12" for="nomeProjeto">Nome do Projeto</label>
                                                        <div class="col-md-6 col-sm-9 col-12">
                                                            <input type="text" id="nomeProjeto" name="nomeProjeto" class="form-control" required value="<?php echo htmlspecialchars($nomeDoProjeto, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="control-label col-md-3 col-sm-3 col-12" for="cliente">Cliente</label>
                                                        <div class="col-md-6 col-sm-9 col-12">
                                                            <input type="text" id="cliente" name="cliente" class="form-control" required value="<?php echo htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="control-label col-md-3 col-sm-3 col-12" for="dataPrev">Data Prevista</label>
                                                        <div class="col-md-6 col-sm-9 col-12">
                                                            <input type="text" class="form-control"
                                                                data-inputmask="'mask': '99/99/9999'" name="dataPrev" id="dataPrev" value="<?php echo htmlspecialchars($dataPrev, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <span class="fa fa-user form-control-feedback right"
                                                                aria-hidden="true"></span>
                                                        </div>
                                                    </div>
                                                    <div class="ln_solid"></div>
                                                    <input type="hidden" name="id" id="id" value="<?php echo $idProj; ?>">

                                                    <div class="form-group row">
                                                        <div class="col-md-6 col-sm-9 col-12 offset-md-3">
                                                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fa fa-save"></i> Salvar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

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