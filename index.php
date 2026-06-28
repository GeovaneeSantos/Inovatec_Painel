<?php
require_once "config/conexao.php";

function getStageProgress($conexao, $projectId, $stageName) {
    $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'CONCLUIDO' THEN 1 ELSE 0 END) as concluidas FROM tarefas WHERE id_proj = :id AND etapa = :etapa";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $projectId, PDO::PARAM_INT);
    $stmt->bindParam(':etapa', $stageName, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $total = (int) ($data['total'] ?? 0);
    $concluidas = (int) ($data['concluidas'] ?? 0);
    
    if ($total === 0) {
        return 0;
    }
    
    return round(($concluidas / $total) * 100);
}

$sql = "SELECT * FROM projetos ORDER BY id";
$stmtSelect = $conexao->prepare($sql);
$stmtSelect->execute();
$result = $stmtSelect->fetchAll();

$allowedProjectStatuses = ['EM-ANDAMENTO', 'FINALIZADO', 'EM-ANALISE', 'PENDENTE'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    require_once 'config/conexao.php';

    if ($_POST['acao'] === 'excluir') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id > 0) {
            try {
                $sqlDeleteComentarios = "DELETE FROM comentarios WHERE id_proj = :id";
                $stmtDeleteComentarios = $conexao->prepare($sqlDeleteComentarios);
                $stmtDeleteComentarios->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtDeleteComentarios->execute();

                $sqlDeleteTarefas = "DELETE FROM tarefas WHERE id_proj = :id";
                $stmtDeleteTarefas = $conexao->prepare($sqlDeleteTarefas);
                $stmtDeleteTarefas->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtDeleteTarefas->execute();

                $sqlDeleteProjeto = "DELETE FROM projetos WHERE id = :id";
                $stmtDeleteProjeto = $conexao->prepare($sqlDeleteProjeto);
                $stmtDeleteProjeto->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmtDeleteProjeto->execute()) {
                    header('Location: index.php');
                    exit();
                } else {
                    $err = $stmtDeleteProjeto->errorInfo();
                    error_log('Erro ao executar DELETE: ' . implode(' | ', $err));
                    header("HTTP/1.1 500 Internal Server Error");
                    exit();
                }
            } catch (Exception $e) {
                error_log('Erro ao deletar projeto: ' . $e->getMessage());
                header("HTTP/1.1 500 Internal Server Error");
                exit();
            }
        }
    }

    if ($_POST['acao'] === 'atualizar_status') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? strtoupper(trim((string) $_POST['status'])) : '';

        if ($id > 0 && in_array($status, $allowedProjectStatuses, true)) {
            try {
                $sqlUpdateStatus = "UPDATE projetos SET status = :status WHERE id = :id";
                $stmtUpdateStatus = $conexao->prepare($sqlUpdateStatus);
                $stmtUpdateStatus->bindParam(':status', $status, PDO::PARAM_STR);
                $stmtUpdateStatus->bindParam(':id', $id, PDO::PARAM_INT);
                $stmtUpdateStatus->execute();

                echo json_encode(['ok' => true, 'status' => $status]);
                exit();
            } catch (Exception $e) {
                error_log('Erro ao atualizar status do projeto: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['erro' => 'Erro interno']);
                exit();
            }
        }

        http_response_code(400);
        echo json_encode(['erro' => 'Dados inválidos']);
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
                        require_once "views/topNavigation.php";
                        ?>
                        <!-- /top navigation -->

                        <!-- page content -->
                        <div class="right_col col-md-12" role="main">
                            <div class="x_panel">

                                <div class="x_title" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                                    <h2 style="margin-bottom: 0;">
                                        Projetos
                                    </h2>
                                </div>

                                <div class="x_content">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome do Projeto</th>
                                                <th>#</th>
                                                <th>INICIO DO PROJETO</th>
                                                <th>PRODUÇÃO</th>
                                                <th>SOFTWARE E TESTES</th>
                                                <th>FINALIZAÇÃO E ENTREGA</th>
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

                                                    <tr class="project-row" data-id="<?php echo (int) $result[$i]['id']; ?>" data-status="<?php echo htmlspecialchars(strtoupper((string) ($result[$i]['status'] ?? 'PENDENTE'))); ?>">
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
                                                            echo $i;
                                                            ?>
                                                        </th>
                                                        <td>
                                                            <a href="tabelaTarefas.php?<?= http_build_query([
                                                                'idProj' => $result[$i]['id'],
                                                                'nomeProj' => $result[$i]['nome_proj'],
                                                                'cliente' => $result[$i]['cliente'],
                                                                'centroCust' => $result[$i]['centro_Cust'],
                                                                'etapa' => 'INICIAL'
                                                            ]) ?>">
                                                                <div style="cursor: pointer" id="teste">
                                                                    <?php $progressInicial = getStageProgress($conexao, $result[$i]['id'], 'INICIAL'); ?>
                                                                    <div class="progress progress_sm" style="width: 70%;">
                                                                        <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php echo $progressInicial; ?>" style="width: <?php echo $progressInicial; ?>%;"></div>
                                                                    </div>
                                                                    <span style="font-size: 12px;"><?php echo $progressInicial; ?>%</span>
                                                                </div>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <a href="tabelaTarefas.php?<?= http_build_query([
                                                                'idProj' => $result[$i]['id'],
                                                                'nomeProj' => $result[$i]['nome_proj'],
                                                                'cliente' => $result[$i]['cliente'],
                                                                'centroCust' => $result[$i]['centro_Cust'],
                                                                'etapa' => 'PRODUCAO'
                                                            ]) ?>">
                                                                <div style="cursor: pointer" id="teste">
                                                                    <?php $progressProducao = getStageProgress($conexao, $result[$i]['id'], 'PRODUCAO'); ?>
                                                                    <div class="progress progress_sm" style="width: 70%;">
                                                                        <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php echo $progressProducao; ?>" style="width: <?php echo $progressProducao; ?>%;"></div>
                                                                    </div>
                                                                    <span style="font-size: 12px;"><?php echo $progressProducao; ?>%</span>
                                                                </div>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <a href="tabelaTarefas.php?<?= http_build_query([
                                                                'idProj' => $result[$i]['id'],
                                                                'nomeProj' => $result[$i]['nome_proj'],
                                                                'cliente' => $result[$i]['cliente'],
                                                                'centroCust' => $result[$i]['centro_Cust'],
                                                                'etapa' => 'SOFTWARE'
                                                            ]) ?>">
                                                                <div style="cursor: pointer" id="teste">
                                                                    <?php $progressSoftware = getStageProgress($conexao, $result[$i]['id'], 'SOFTWARE'); ?>
                                                                    <div class="progress progress_sm" style="width: 70%;">
                                                                        <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php echo $progressSoftware; ?>" style="width: <?php echo $progressSoftware; ?>%;"></div>
                                                                    </div>
                                                                    <span style="font-size: 12px;"><?php echo $progressSoftware; ?>%</span>
                                                                </div>
                                                            </a>
                                                        </td>

                                                        <td>
                                                            <a href="tabelaTarefas.php?<?= http_build_query([
                                                                'idProj' => $result[$i]['id'],
                                                                'nomeProj' => $result[$i]['nome_proj'],
                                                                'cliente' => $result[$i]['cliente'],
                                                                'centroCust' => $result[$i]['centro_Cust'],
                                                                'etapa' => 'FINALIZACAO'
                                                            ]) ?>">
                                                                <div style="cursor: pointer" id="teste">
                                                                    <?php $progressFinalizacao = getStageProgress($conexao, $result[$i]['id'], 'FINALIZACAO'); ?>
                                                                    <div class="progress progress_sm" style="width: 70%;">
                                                                        <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="<?php echo $progressFinalizacao; ?>" style="width: <?php echo $progressFinalizacao; ?>%;"></div>
                                                                    </div>
                                                                    <span style="font-size: 12px;"><?php echo $progressFinalizacao; ?>%</span>
                                                                </div>
                                                            </a>
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
                                                                class="btn btn-info btn-sm btn-atualizar-status-projeto"
                                                                title="Alterar status"
                                                                data-toggle="modal"
                                                                data-target="#modalAtualizarStatusProjeto"
                                                                data-id="<?php echo (int) $result[$i]['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($result[$i]['centro_Cust'] . ' ' . $result[$i]['cliente'] . ' ' . $result[$i]['nome_proj']); ?>"
                                                                data-status="<?php echo htmlspecialchars(strtoupper((string) ($result[$i]['status'] ?? 'PENDENTE'))); ?>">
                                                                <i class="fa fa-flag"></i>
                                                                <span class="status-label" style="margin-left: 4px;"><?php echo htmlspecialchars(strtoupper((string) ($result[$i]['status'] ?? 'PENDENTE'))); ?></span>
                                                            </button>
                                                            <button
                                                                type="button"
                                                                class="btn btn-danger btn-sm btn-excluir-projeto"
                                                                title="Excluir"
                                                                data-toggle="modal"
                                                                data-target="#modalExcluirProjeto"
                                                                data-id="<?php echo (int) $result[$i]['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($result[$i]['centro_Cust'] . ' ' . $result[$i]['cliente'] . ' ' . $result[$i]['nome_proj']); ?>">
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

                <div class="modal fade" id="modalAtualizarStatusProjeto" tabindex="-1" role="dialog" aria-labelledby="modalAtualizarStatusProjetoLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAtualizarStatusProjetoLabel">Definir status do projeto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Selecione o novo status para <strong id="nomeProjetoStatus"></strong>:</p>
                                <select class="form-control" id="selectStatusProjeto">
                                    <option value="EM-ANDAMENTO">EM-ANDAMENTO</option>
                                    <option value="FINALIZADO">FINALIZADO</option>
                                    <option value="EM-ANALISE">EM-ANALISE</option>
                                    <option value="PENDENTE">PENDENTE</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="confirmarStatusProjeto">Confirmar</button>
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

                        for (var i = 0; i < botoesExcluir.length; i++) {
                            botoesExcluir[i].addEventListener('click', function() {
                                var id = this.getAttribute('data-id');
                                var nome = this.getAttribute('data-name');
                                campoIdExcluir.value = id;
                                nomeProjetoExcluir.textContent = nome;
                            });
                        }

                        var botoesStatus = document.querySelectorAll('.btn-atualizar-status-projeto');
                        var campoIdStatus = document.getElementById('idProjetoStatus');
                        var nomeProjetoStatus = document.getElementById('nomeProjetoStatus');
                        var selectStatusProjeto = document.getElementById('selectStatusProjeto');
                        var confirmarStatusProjeto = document.getElementById('confirmarStatusProjeto');
                        var projetoAtualId = null;

                        for (var j = 0; j < botoesStatus.length; j++) {
                            botoesStatus[j].addEventListener('click', function() {
                                projetoAtualId = this.getAttribute('data-id');
                                var nome = this.getAttribute('data-name');
                                var statusAtual = this.getAttribute('data-status') || 'PENDENTE';
                                nomeProjetoStatus.textContent = nome;
                                if (selectStatusProjeto) {
                                    selectStatusProjeto.value = statusAtual;
                                }
                            });
                        }

                        if (confirmarStatusProjeto && selectStatusProjeto) {
                            confirmarStatusProjeto.addEventListener('click', function() {
                                if (!projetoAtualId) {
                                    return;
                                }

                                var statusSelecionado = selectStatusProjeto.value;

                                fetch('index.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                                    },
                                    body: new URLSearchParams({
                                        acao: 'atualizar_status',
                                        id: projetoAtualId,
                                        status: statusSelecionado
                                    })
                                })
                                .then(function(response) {
                                    return response.json();
                                })
                                .then(function(data) {
                                    if (data.ok) {
                                        var linhaProjeto = document.querySelector('.project-row[data-id="' + projetoAtualId + '"]');
                                        if (linhaProjeto) {
                                            linhaProjeto.setAttribute('data-status', statusSelecionado);
                                        }

                                        var botaoStatus = document.querySelector('.btn-atualizar-status-projeto[data-id="' + projetoAtualId + '"]');
                                        if (botaoStatus) {
                                            botaoStatus.setAttribute('data-status', statusSelecionado);
                                            var labelStatus = botaoStatus.querySelector('.status-label');
                                            if (labelStatus) {
                                                labelStatus.textContent = statusSelecionado;
                                            }
                                        }

                                        if (window.jQuery) {
                                            jQuery('#modalAtualizarStatusProjeto').modal('hide');
                                        }
                                    } else {
                                        alert('Não foi possível atualizar o status do projeto.');
                                    }
                                })
                                .catch(function() {
                                    alert('Erro ao atualizar o status do projeto.');
                                });
                            });
                        }
                    })();
                </script>
</body>

</html>