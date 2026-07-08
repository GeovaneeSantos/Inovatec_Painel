<?php
$idProj = 0;
$centroDeCusto = '';
$nomeDoProjeto = '';
$cliente = '';
$dataPrev = '';
$datasAlt = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idProj = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($idProj > 0) {
        require_once 'config/conexao.php';

        $sqlSelect = "SELECT id, centro_Cust, nome_proj, cliente, dt_termino FROM projetos WHERE id = :id";
        $stmtSelect = $conexao->prepare($sqlSelect);
        $stmtSelect->bindValue(':id', $idProj, PDO::PARAM_INT);
        $stmtSelect->execute();
        $projeto = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        $sqlSelectDatas = "SELECT * FROM dtaltproj WHERE idProj = :id";
        $stmtSelectDatas = $conexao->prepare($sqlSelectDatas);
        $stmtSelectDatas->bindParam(':id', $idProj, PDO::PARAM_INT);
        $stmtSelectDatas->execute();
        $datasAlt = $stmtSelectDatas->fetchAll();

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

    $conexao->exec("CREATE TABLE IF NOT EXISTS comentarios (
        comentario TEXT NOT NULL,
        etapa VARCHAR(100) NOT NULL,
        id_proj INT NOT NULL,
        id INT PRIMARY KEY AUTO_INCREMENT,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user VARCHAR(100)
    )");

    if (isset($_POST['acao']) && trim((string) $_POST['acao']) !== '') {
        $acao = trim((string) $_POST['acao']);
        $idProjComentario = isset($_POST['id_proj']) ? (int) $_POST['id_proj'] : 0;
        $etapaComentario = isset($_POST['etapa']) ? trim((string) $_POST['etapa']) : '';
        $usuario = isset($_SESSION['user_name']) ? trim((string) $_SESSION['user_name']) : '';

        if ($idProjComentario <= 0) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do projeto inválido']);
            exit;
        }

        if ($acao === 'criar-comentario') {
            $comentario = isset($_POST['comentario']) ? trim((string) $_POST['comentario']) : '';

            if ($comentario === '') {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos para criar comentário']);
                exit;
            }

            $sqlInsert = "INSERT INTO comentarios (comentario, etapa, id_proj, user) VALUES (:comentario, :etapa, :id_proj, :user)";
            $stmtInsert = $conexao->prepare($sqlInsert);
            $stmtInsert->bindParam(':comentario', $comentario, PDO::PARAM_STR);
            $stmtInsert->bindParam(':etapa', $etapaComentario, PDO::PARAM_STR);
            $stmtInsert->bindParam(':id_proj', $idProjComentario, PDO::PARAM_INT);
            $stmtInsert->bindParam(':user', $usuario, PDO::PARAM_STR);
            $stmtInsert->execute();

            $commentId = $conexao->lastInsertId();

            echo json_encode([
                'ok' => true,
                'comentario' => [
                    'id' => $commentId,
                    'comentario' => $comentario,
                    'etapa' => $etapaComentario,
                    'id_proj' => $idProjComentario
                ]
            ]);
            exit;
        }

        if ($acao === 'editar-comentario') {
            $comentario = isset($_POST['comentario']) ? trim((string) $_POST['comentario']) : '';
            $commentId = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;

            if ($comentario === '' || $commentId <= 0) {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos para editar comentário']);
                exit;
            }

            $sqlUpdate = "UPDATE comentarios SET comentario = :comentario WHERE id = :comment_id AND id_proj = :id_proj";
            $stmtUpdate = $conexao->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':comentario', $comentario, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':id_proj', $idProjComentario, PDO::PARAM_INT);
            $stmtUpdate->execute();

            echo json_encode(['ok' => true, 'comentario' => $comentario, 'comment_id' => $commentId]);
            exit;
        }

        if ($acao === 'excluir-comentario') {
            $commentId = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;

            if ($commentId <= 0) {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos para excluir comentário']);
                exit;
            }

            $sqlDelete = "DELETE FROM comentarios WHERE id = :comment_id AND id_proj = :id_proj";
            $stmtDelete = $conexao->prepare($sqlDelete);
            $stmtDelete->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
            $stmtDelete->bindParam(':id_proj', $idProjComentario, PDO::PARAM_INT);
            $stmtDelete->execute();

            echo json_encode(['ok' => true]);
            exit;
        }

        http_response_code(400);
        echo json_encode(['erro' => 'Ação de comentário inválida']);
        exit;
    }

    $camposObrigatorios = ['centroDeCustos', 'nomeProjeto', 'cliente', 'id'];
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
    $idProjeto = (int) $_POST['id'];
    $novaDataPrev = trim($_POST['newDataPrev']);

    if ($idProjeto <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID do projeto inválido']);
        exit;
    }

    try {
        $sqlUpdate = "UPDATE projetos SET centro_Cust = :centro_Cust, nome_proj = :nome_proj, cliente = :cliente WHERE id = :id";
        $stmtUpdate = $conexao->prepare($sqlUpdate);

        $stmtUpdate->bindParam(':centro_Cust', $centroDeCusto, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':nome_proj', $nomeDoProjeto, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':id', $idProjeto, PDO::PARAM_INT);

        if ($novaDataPrev != '') {
            $sqlNovaDataPrev = "INSERT INTO dtaltproj (novaData, idProj) VALUES (:dtNova, :idProj)";
            $stmtNovaData = $conexao->prepare($sqlNovaDataPrev);
            $stmtNovaData->bindParam(':dtNova', $novaDataPrev, PDO::PARAM_STR);
            $stmtNovaData->bindParam(':idProj', $idProjeto, PDO::PARAM_INT);
            $stmtNovaData->execute();

            $sqlUpdateDt = "UPDATE projetos SET dataNova = 1 WHERE ID = :id";
            $stmtUpdateDt = $conexao->prepare($sqlUpdateDt);
            $stmtUpdateDt->bindParam(":id", $idProjeto, PDO::PARAM_INT);
            $stmtUpdateDt->execute();
        }

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


// Parte da Lógica referente aos Comentários
$idProjeto = $idProj;
$etapa = '';

$result = [];
$comentarios = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $idProjeto > 0) {
    $conexao->exec("CREATE TABLE IF NOT EXISTS comentarios (
        comentario TEXT NOT NULL,
        etapa VARCHAR(100) NOT NULL,
        id_proj INT NOT NULL,
        id INT PRIMARY KEY AUTO_INCREMENT,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user VARCHAR(100)
    )");

    $sqlSelectComentarios = "SELECT * FROM comentarios WHERE id_proj = :id_proj ORDER BY criado_em DESC";
    $stmtSelectComentarios = $conexao->prepare($sqlSelectComentarios);
    $stmtSelectComentarios->bindValue(':id_proj', $idProjeto, PDO::PARAM_INT);
    $stmtSelectComentarios->execute();
    $comentarios = $stmtSelectComentarios->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
<?php
require_once "config/header.php";
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

        .date-changes-panel {
            background: #ffffff;
            border: 1px solid #e6e9ed;
            border-radius: 12px;
            padding: 16px;
            margin-top: 6px;
        }

        .date-changes-panel h6 {
            margin-bottom: 16px;
        }

        .date-change-item {
            margin-bottom: 12px;
            padding: 10px;
            background: #f7f8fb;
            border-radius: 8px;
        }

        .date-change-item p {
            margin: 0;
            font-size: 0.95rem;
        }
    </style>
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

                        <!-- Body da Pagina -->
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
                                                <div class="row">
                                                    <div class="col-lg-8 col-md-7 col-sm-12">
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
                                                                <label class="control-label col-md-3 col-sm-3 col-12" for="dataPrev">Primeira Data Prevista</label>
                                                                <div class="col-md-2 col-sm-2 col-12">
                                                                    <input disabled type="text" class="form-control"
                                                                        data-inputmask="'mask': '99/99/9999'" name="dataPrev" id="dataPrev" value="<?php echo htmlspecialchars($dataPrev, ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <span class="fa fa-calendar form-control-feedback right"
                                                                        aria-hidden="true"></span>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="control-label col-md-3 col-sm-3 col-12" for="newDataPrev">Nova Data Prevista</label>
                                                                <div class="col-md-2 col-sm-2 col-12">
                                                                    <input type="text" class="form-control"
                                                                        data-inputmask="'mask': '99/99/9999'" name="newDataPrev" id="newDataPrev">
                                                                    <span class="fa fa-calendar form-control-feedback right"
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
                                                    <div class="col-lg-4 col-md-5 col-sm-12">
                                                        <div class="date-changes-panel">
                                                            <div class="x_title text-center mb-3">
                                                                <h6>Lista de alteração de Datas</h6>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <?php if (!empty($datasAlt)) : ?>
                                                                <?php foreach ($datasAlt as $data) : ?>
                                                                    <div class="date-change-item">
                                                                        <p>
                                                                            <?php
                                                                                echo "Data: " . $data['novaData'];
                                                                                echo " Alterada em: " . $data['timestamp'];
                                                                            ?>
                                                                        </p>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php else : ?>
                                                                <div class="date-change-item">
                                                                    <p>Nenhuma alteração de data registrada.</p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="comments-panel">
                                    <div class="comments-header">
                                        <strong>Comentários da etapa</strong>
                                    </div>
                
                                    <div class="comments-list" id="comments-list">
                                        <?php if (!empty($comentarios)) : ?>
                                            <?php foreach ($comentarios as $comentario) : ?>
                                                <div class="comment-bubble" data-comment-id="<?php echo htmlspecialchars($comentario['id'] ?? 0, ENT_QUOTES); ?>">
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
<script>
    
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

        if (!commentId) {
            alert('ID do comentário inválido.');
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'editar-comentario');
        formData.append('id_proj', '<?php echo htmlspecialchars((string) $idProjeto); ?>');
        formData.append('comment_id', commentId);
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

        const commentId = bubble.dataset.commentId;
        if (!commentId) {
            alert('ID do comentário inválido.');
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'excluir-comentario');
        formData.append('id_proj', '<?php echo htmlspecialchars((string) $idProjeto); ?>');
        formData.append('comment_id', commentId);

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