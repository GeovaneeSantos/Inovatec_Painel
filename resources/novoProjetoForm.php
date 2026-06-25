                    <!-- page content -->
                    <div class="col-lg-12 col-md-12 right_col" role="main">
                        <div class="">
                            <div class="page-title row">
                                <div class="col-sm-6 col-12 text-left">
                                    <h3>Novo Projeto</h3>
                                </div>

                                <div class="col-sm-6 col-12 text-right">
                                    <div class="row">
                                        <div class="offset-xl-7 col-xl-5 col-lg-12 col-md-12 col-sm-5 col-12 form-group pull-right top_search mt-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Procurar por...">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-light" type="button">Ir!</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h2>Cadastro de Projetos</h2>
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </li>
                                                <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                        role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="#">Configuração 1</a>
                                                        </li>
                                                        <li><a href="#">Configuração 2</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br>
                                            <form class="form-horizontal form-label-left" method="post" action="novoProjeto.php">
                                                <div class="form-group row">
                                                    <label class="control-label col-md-3 col-sm-3 col-12" for="centroDeCustos">Centro de Custos</label>
                                                    <div class="col-md-6 col-sm-9 col-12">
                                                        <input type="text" id="centroDeCustos" name="centroDeCustos" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="control-label col-md-3 col-sm-3 col-12" for="nomeProjeto">Nome do Projeto</label>
                                                    <div class="col-md-6 col-sm-9 col-12">
                                                        <input type="text" id="nomeProjeto" name="nomeProjeto" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="control-label col-md-3 col-sm-3 col-12" for="cliente">Cliente</label>
                                                    <div class="col-md-6 col-sm-9 col-12">
                                                        <input type="text" id="cliente" name="cliente" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="control-label col-md-3 col-sm-3 col-12" for="dataPrev">Data Prevista</label>
                                                    <div class="col-md-6 col-sm-9 col-12">
                                                        <input type="text" class="form-control"
                                                            data-inputmask="'mask': '99/99/9999'" name="dataPrev" id="dataPrev">
                                                        <span class="fa fa-user form-control-feedback right"
                                                            aria-hidden="true"></span>
                                                    </div>
                                                </div>
                                                <div class="ln_solid"></div>

                                                <div class="form-group row">
                                                    <div class="col-md-6 col-sm-9 col-12 offset-md-3">
                                                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fa fa-save"></i> Salvar
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target=".bs-example-modal-lg">Large modal
                                            </button>

                                            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal"><span
                                                                    aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h4>Text in a modal</h4>
                                                            <p>Praesent commodo cursus magna, vel scelerisque nisl
                                                                consectetur et.
                                                                Vivamus sagittis lacus vel augue laoreet rutrum faucibus
                                                                dolor
                                                                auctor.</p>
                                                            <p>Aenean lacinia bibendum nulla sed consectetur. Praesent
                                                                commodo
                                                                cursus magna, vel scelerisque nisl consectetur et. Donec
                                                                sed
                                                                odio
                                                                dui. Donec ullamcorper nulla non metus auctor
                                                                fringilla.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-dismiss="modal">
                                                                Close
                                                            </button>
                                                            <button type="button" class="btn btn-primary">Save changes
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /page content -->