<!DOCTYPE html>
<html lang="pt-br">
    <?php 
        require_once "config/header.php";
    ?>
<body class="login">
<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>

    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                <form method="POST" action="">
                    <h1>  Formulário de Acesso  </h1>
                    <div>
                        <input type="text" class="form-control" placeholder="Username" required=""/>
                    </div>
                    <div>
                        <input type="password" class="form-control" placeholder="Password" required=""/>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-outline-success">Enviar</button>
                        </div>
                </form>
            </section>
        </div>
    </div>
</div>
</body>
</html>
