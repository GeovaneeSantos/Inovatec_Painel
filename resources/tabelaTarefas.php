<?php
require_once "config/conexao.php";

$allowedStatuses = ['EM-EXECUCAO', 'CONCLUIDO', 'PENDENTE', ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $taskId = isset($_POST['taskId']) ? (int) $_POST['taskId'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    $status = strtoupper(trim($status));

    if ($taskId > 0 && in_array($status, $allowedStatuses, true)) {
        $sqlUpdate = "UPDATE tarefas SET status = :status WHERE id = :id";
        $stmtUpdate = $conexao->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':status', $status, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':id', $taskId, PDO::PARAM_INT);
        $stmtUpdate->execute();

        echo json_encode(['ok' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados inválidos']);
    }

    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $idProjeto = isset($_GET['idProj']) ? trim($_GET['idProj']) : '';
    $centroCust = isset($_GET['centroCust']) ? trim($_GET['centroCust']) : '';
    $nomeDoProjeto = isset($_GET['nomeProj']) ? trim($_GET['nomeProj']) : '';
    $cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';
    $etapa = isset($_GET['etapa']) ? trim($_GET['etapa']) : '';

    if ($idProjeto === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'idProj ausente']);
        exit;
    }

    $sql = "SELECT A.id, A.tarefa, A.etapa, A.area, A.status, B.nome_proj
            FROM tarefas AS A
            JOIN projetos AS B ON A.id_proj = B.id
            WHERE B.id = :id AND A.etapa = :etapa";
    $stmtSelect = $conexao->prepare($sql);
    $stmtSelect->bindParam(":id", $idProjeto, PDO::PARAM_INT);
    $stmtSelect->bindParam(":etapa", $etapa, PDO::PARAM_STR);
    $stmtSelect->execute();
    $result = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);
}

?>



<!DOCTYPE html>
<html lang="en">

<?php require_once "config/header.php" ?>

<body>
    <?php
    require_once "views/topNavigationNoFilters.php";
    ?>

    <div class="col-md-12 col-sm-12 col-12">
        <div class="x_panel">
            <div class="x_title" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                <h2 style="margin-bottom: 0;">
                    <?php
                    echo $etapa
                    ?>
                </h2>
            </div>
            <div class="x_content">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Area</th>
                            <th>Atividade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($result)) {
                            foreach ($result as $row) {
                                $status = strtoupper((string) ($row['status'] ?? ''));
                                $rowClass = 'alert-warning';

                                if ($status === 'EM-EXECUCAO') {
                                    $rowClass = 'alert-info';
                                } elseif ($status === 'CONCLUIDO') {
                                    $rowClass = 'alert-success';
                                }
                        ?>
                                <tr class="alert <?php echo $rowClass; ?> task-row" data-status="<?php echo htmlspecialchars($status); ?></tr>" role="alert" style="padding: 15px 0; line-height: 1.8;">
                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['area'] ?? ''); ?></td>
                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['tarefa'] ?? ''); ?></td>
                                    <td style="vertical-align: middle;">
                                        <select class="form-control status-select" data-task-id="<?php echo (int) $row['id']; ?>" onchange="atualizarStatus(this)">
                                            <option value="PENDENTE" <?php echo ($status === 'PENDENTE') ? 'selected' : ''; ?>>PENDENTE</option>
                                            <option value="EM-EXECUCAO" <?php echo ($status === 'EM-EXECUCAO') ? 'selected' : ''; ?>>EM-EXECUCAO</option>
                                            <option value="CONCLUIDO" <?php echo ($status === 'CONCLUIDO') ? 'selected' : ''; ?>>CONCLUIDO</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="3" class="text-center">Nenhuma tarefa cadastrada para esta etapa.</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</body>

<script>
    function getTaskRowClass(status) {
        const normalizedStatus = String(status || '').toUpperCase();

        if (normalizedStatus === 'EM-EXECUCAO') {
            return 'alert-info';
        }

        if (normalizedStatus === 'CONCLUIDO') {
            return 'alert-success';
        }

        return 'alert-warning';
    }

    function atualizarStatus(select) {
        const taskId = select.dataset.taskId;
        const status = select.value;
        const row = select.closest('tr');

        if (row) {
            row.className = row.className.replace(/alert-(warning|info|success)/g, '').trim();
            row.classList.add('alert', getTaskRowClass(status));
            row.setAttribute('data-status', status);
        }

        fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                },
                body: new URLSearchParams({
                    taskId,
                    status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.ok) {
                    alert('Não foi possível atualizar o status.');
                }
            })
            .catch(() => {
                alert('Erro ao atualizar o status.');
            });
    }
</script>

</html>