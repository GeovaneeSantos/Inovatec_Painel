                    <!-- page content -->
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
                                            <h2>Cadastro de Projetos</h2>
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

                                        </div>
                                    </div>
                                </div>

                    
                    </div>
                    </div>
                    </div>
                    <!-- /page content -->