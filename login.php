<!DOCTYPE html>
<html lang="pt-br">
<?php
require_once "config/header.php";
?>

<body class="login">
    <div id="alert-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 1rem; font-size: 0.8rem;">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size: 0.5rem;"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 1rem; font-size: 0.8rem; background: var(--green-dim); border-color: var(--green); color: var(--green);">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size: 0.5rem;"></button>
            </div>
        <?php endif; ?>
    </div>
    <div class="login_wrapper">
        <div>
            <a class="hiddenanchor" id="signup"></a>
            <a class="hiddenanchor" id="signin"></a>
            <div class="animate form login_form">
                <section class="login_content">
                    <form method="POST" action="scripts/valida_login.php">
                        <h1>Formulário de Acesso</h1>
                        <div>
                            <input type="text" class="form-control" placeholder="Username" required="" id="nome" name="nome" />
                        </div>
                        <div>
                            <input type="password" class="form-control" placeholder="Password" required="" id="senha" name="senha" />
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-outline-success">Enviar</button>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</body>

</html>