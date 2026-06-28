<?php
require_once "config/conexao.php";

$allowedStatuses = ['EM-EXECUCAO', 'CONCLUIDO', 'PENDENTE', ''];

$conexao->exec("CREATE TABLE IF NOT EXISTS comentarios (
    comentario TEXT NOT NULL,
    etapa VARCHAR(100) NOT NULL,
    id_proj INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$idProjeto = isset($_GET['idProj']) ? trim($_GET['idProj']) : '';
$centroCust = isset($_GET['centroCust']) ? trim($_GET['centroCust']) : '';
$nomeDoProjeto = isset($_GET['nomeProj']) ? trim($_GET['nomeProj']) : '';
$cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';
$etapa = isset($_GET['etapa']) ? trim($_GET['etapa']) : '';

$result = [];
$comentarios = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = isset($_POST['acao']) ? trim($_POST['acao']) : '';

    if ($acao === 'criar-comentario') {
        $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
        $idProjComentario = isset($_POST['id_proj']) ? (int) $_POST['id_proj'] : 0;
        $etapaComentario = isset($_POST['etapa']) ? trim($_POST['etapa']) : '';

        if ($comentario === '' || $idProjComentario <= 0 || $etapaComentario === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos para criar comentário']);
            exit;
        }

        $sqlInsert = "INSERT INTO comentarios (comentario, etapa, id_proj) VALUES (:comentario, :etapa, :id_proj)";
        $stmtInsert = $conexao->prepare($sqlInsert);
        $stmtInsert->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $stmtInsert->bindParam(':etapa', $etapaComentario, PDO::PARAM_STR);
        $stmtInsert->bindParam(':id_proj', $idProjComentario, PDO::PARAM_INT);
        $stmtInsert->execute();

        echo json_encode([
            'ok' => true,
            'comentario' => [
                'comentario' => $comentario,
                'etapa' => $etapaComentario,
                'id_proj' => $idProjComentario
            ]
        ]);
        exit;
    }

    if ($acao === 'editar-comentario') {
        $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
        $idProjComentario = isset($_POST['id_proj']) ? (int) $_POST['id_proj'] : 0;
        $etapaComentario = isset($_POST['etapa']) ? trim($_POST['etapa']) : '';
        $textoOriginal = isset($_POST['texto_original']) ? trim($_POST['texto_original']) : '';

        if ($comentario === '' || $idProjComentario <= 0 || $etapaComentario === '' || $textoOriginal === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos para editar comentário']);
            exit;
        }

        $sqlUpdate = "UPDATE comentarios SET comentario = :comentario WHERE comentario = :texto_original AND id_proj = :id_proj AND etapa = :etapa";
        $stmtUpdate = $conexao->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':texto_original', $textoOriginal, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':id_proj', $idProjComentario, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':etapa', $etapaComentario, PDO::PARAM_STR);
        $stmtUpdate->execute();

        echo json_encode(['ok' => true, 'comentario' => $comentario]);
        exit;
    }

    if ($acao === 'excluir-comentario') {
        $comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
        $idProjComentario = isset($_POST['id_proj']) ? (int) $_POST['id_proj'] : 0;
        $etapaComentario = isset($_POST['etapa']) ? trim($_POST['etapa']) : '';

        if ($comentario === '' || $idProjComentario <= 0 || $etapaComentario === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos para excluir comentário']);
            exit;
        }

        $sqlDelete = "DELETE FROM comentarios WHERE comentario = :comentario AND id_proj = :id_proj AND etapa = :etapa";
        $stmtDelete = $conexao->prepare($sqlDelete);
        $stmtDelete->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $stmtDelete->bindParam(':id_proj', $idProjComentario, PDO::PARAM_INT);
        $stmtDelete->bindParam(':etapa', $etapaComentario, PDO::PARAM_STR);
        $stmtDelete->execute();

        echo json_encode(['ok' => true]);
        exit;
    }

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

    $sqlComentarios = "SELECT id, comentario, etapa, id_proj FROM comentarios WHERE id_proj = :id_proj AND etapa = :etapa ORDER BY id DESC";
    $stmtComentarios = $conexao->prepare($sqlComentarios);
    $stmtComentarios->bindParam(':id_proj', $idProjeto, PDO::PARAM_INT);
    $stmtComentarios->bindParam(':etapa', $etapa, PDO::PARAM_STR);
    $stmtComentarios->execute();
    $comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
}

?>



<!DOCTYPE html>
<html lang="en">

<?php require_once "config/header.php" ?>

<body>
    <?php
    require_once "views/topNavigationNoFilters.php";
    ?>

    <style>
        .comments-panel {
            margin-top: 24px;
            border: 1px solid #e6e9ed;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .comments-header {
            padding: 14px 16px;
            border-bottom: 1px solid #f0f0f0;
            background: #f8f9fa;
        }

        .comments-list {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 320px;
            overflow-y: auto;
        }

        .comment-bubble {
            max-width: 80%;
            padding: 10px 12px;
            border-radius: 14px;
            background: #f1f3f5;
            color: #222;
            position: relative;
            align-self: flex-start;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .comment-bubble.mine {
            background: #dcf8c6;
            align-self: flex-end;
        }

        .comment-bubble p {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .comment-actions {
            position: absolute;
            top: 6px;
            right: 8px;
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .comment-bubble:hover .comment-actions {
            opacity: 1;
        }

        .comment-actions button {
            border: none;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            color: #555;
            line-height: 1;
        }

        .comment-compose {
            border-top: 1px solid #f0f0f0;
            padding: 12px 16px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            background: #fcfcfd;
        }

        .comment-compose textarea {
            flex: 1;
            resize: none;
            min-height: 70px;
            border-radius: 10px;
            border: 1px solid #dfe3e8;
            padding: 10px 12px;
        }

        .comment-compose button {
            border: none;
            background: #26b99a;
            color: #fff;
            border-radius: 10px;
            padding: 10px 14px;
        }
    </style>

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
                                <tr class="alert <?php echo $rowClass; ?> task-row" data-status="<?php echo htmlspecialchars($status); ?>" role="alert" style="padding: 15px 0; line-height: 1.8;">
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

                <div class="comments-panel">
                    <div class="comments-header">
                        <strong>Comentários da etapa</strong>
                    </div>

                    <div class="comments-list" id="comments-list">
                        <?php if (!empty($comentarios)) : ?>
                            <?php foreach ($comentarios as $comentario) : ?>
                                <div class="comment-bubble" data-comment-text="<?php echo htmlspecialchars($comentario['comentario'] ?? '', ENT_QUOTES); ?>">
                                    <div class="comment-actions">
                                        <button type="button" onclick="editarComentario(this)" title="Editar">✎</button>
                                        <button type="button" onclick="excluirComentario(this)" title="Excluir">×</button>
                                    </div>
                                    <p><?php echo htmlspecialchars($comentario['comentario'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="text-muted" id="comments-empty-state">Nenhum comentário ainda para esta etapa.</div>
                        <?php endif; ?>
                    </div>

                    <form id="form-comentario" class="comment-compose">
                        <textarea name="comentario" id="input-comentario" placeholder="Escreva um comentário para esta etapa..."></textarea>
                        <input type="hidden" name="acao" value="criar-comentario">
                        <input type="hidden" name="id_proj" value="<?php echo htmlspecialchars((string) $idProjeto); ?>">
                        <input type="hidden" name="etapa" value="<?php echo htmlspecialchars($etapa); ?>">
                        <button type="submit">Enviar</button>
                    </form>
                </div>
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

    function buildCommentBubble(comment) {
        const bubble = document.createElement('div');
        bubble.className = 'comment-bubble mine';
        bubble.dataset.commentId = comment.id;
        bubble.innerHTML = `
            <div class="comment-actions">
                <button type="button" onclick="editarComentario(this)" title="Editar">✎</button>
                <button type="button" onclick="excluirComentario(this)" title="Excluir">×</button>
            </div>
            <p>${comment.comentario}</p>
        `;
        return bubble;
    }

    function editarComentario(button) {
        const bubble = button.closest('.comment-bubble');
        if (!bubble) {
            return;
        }

        const text = bubble.querySelector('p').textContent.trim();
        const textarea = document.createElement('textarea');
        textarea.className = 'form-control';
        textarea.rows = 3;
        textarea.value = text;

        bubble.querySelector('p').replaceWith(textarea);
        bubble.querySelector('.comment-actions').innerHTML = `
            <button type="button" onclick="salvarComentario(this)" title="Salvar">✓</button>
            <button type="button" onclick="cancelarEdicao(this)" title="Cancelar">↺</button>
        `;
    }

    function salvarComentario(button) {
        const bubble = button.closest('.comment-bubble');
        if (!bubble) {
            return;
        }

        const textarea = bubble.querySelector('textarea');
        const comentario = textarea ? textarea.value.trim() : '';
        const commentId = bubble.dataset.commentId;

        if (!comentario) {
            alert('Escreva um comentário antes de salvar.');
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'editar-comentario');
        formData.append('id_proj', '<?php echo htmlspecialchars((string) $idProjeto); ?>');
        formData.append('etapa', '<?php echo htmlspecialchars($etapa); ?>');
        formData.append('texto_original', bubble.dataset.commentText || '');
        formData.append('comentario', comentario);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const paragraph = document.createElement('p');
                paragraph.textContent = comentario;
                textarea.replaceWith(paragraph);
                bubble.querySelector('.comment-actions').innerHTML = `
                    <button type="button" onclick="editarComentario(this)" title="Editar">✎</button>
                    <button type="button" onclick="excluirComentario(this)" title="Excluir">×</button>
                `;
            } else {
                alert('Não foi possível editar o comentário.');
            }
        })
        .catch(() => {
            alert('Erro ao editar o comentário.');
        });
    }

    function cancelarEdicao(button) {
        const bubble = button.closest('.comment-bubble');
        if (!bubble) {
            return;
        }

        const textarea = bubble.querySelector('textarea');
        const originalText = textarea ? textarea.value : '';
        const paragraph = document.createElement('p');
        paragraph.textContent = originalText;
        textarea.replaceWith(paragraph);
        bubble.querySelector('.comment-actions').innerHTML = `
            <button type="button" onclick="editarComentario(this)" title="Editar">✎</button>
            <button type="button" onclick="excluirComentario(this)" title="Excluir">×</button>
        `;
    }

    function excluirComentario(button) {
        const bubble = button.closest('.comment-bubble');
        if (!bubble) {
            return;
        }

        if (!confirm('Deseja excluir este comentário?')) {
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'excluir-comentario');
        formData.append('id_proj', '<?php echo htmlspecialchars((string) $idProjeto); ?>');
        formData.append('etapa', '<?php echo htmlspecialchars($etapa); ?>');
        formData.append('comentario', bubble.dataset.commentText || '');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                bubble.remove();
            } else {
                alert('Não foi possível excluir o comentário.');
            }
        })
        .catch(() => {
            alert('Erro ao excluir o comentário.');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-comentario');
        const textarea = document.getElementById('input-comentario');

        if (!form || !textarea) {
            return;
        }

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const comentario = textarea.value.trim();

            if (!comentario) {
                alert('Escreva um comentário antes de enviar.');
                return;
            }

            const formData = new FormData(form);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok && data.comentario) {
                    const list = document.getElementById('comments-list');
                    const emptyState = document.getElementById('comments-empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }
                    if (list) {
                        list.prepend(buildCommentBubble(data.comentario));
                    }
                    form.reset();
                } else {
                    alert('Não foi possível enviar o comentário.');
                }
            })
            .catch(() => {
                alert('Erro ao enviar o comentário.');
            });
        });
    });
</script>

</html>