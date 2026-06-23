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
                        
                        </div>
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

    <?php
        require_once "config/scripts.php";
    ?>

</body>

</html>