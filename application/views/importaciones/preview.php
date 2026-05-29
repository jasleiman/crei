<div class="ch-container">
    <div class="row">

        <div class="col-sm-2 col-lg-2">
            <div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked"></div>
                    <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="nav-header"><?php echo $this->lang->line('mensajes_titulo_menus'); ?></li>
                        <?php echo $menu; ?>
                    </ul>
                </div>
            </div>
        </div>

        <noscript>
            <div class="alert alert-block col-md-12">
                <h4 class="alert-heading">Atención</h4>
                <p>Necesita tener JavaScript habilitado para usar esta sección.</p>
            </div>
        </noscript>

        <div id="content" class="col-lg-10 col-sm-10">
            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url($url_volver); ?>">Listado</a></li>
                    <li class="active">Importar <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></li>
                </ul>
            </div>

            <div class="ch-container">
                <div class="row">
                    <div class="box col-md-12">
                        <div class="box-inner">
                            <div class="box-header well" data-original-title="">
                                <h2><i class="glyphicon glyphicon-eye-open"></i> Vista previa — <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
                                <div class="box-icon">
                                    <a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="box-content">
                                <p>
                                    <strong><?php echo (int) $validas; ?></strong> fila(s) válida(s) de
                                    <strong><?php echo (int) $total; ?></strong>.
                                    Solo las filas válidas se importarán al confirmar.
                                </p>

                                <div class="table-wrapper">
                                    <div class="scrollable">
                                        <table class="table table-striped table-bordered bootstrap-datatable datatable responsive dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Fila</th>
                                                    <th>Resumen</th>
                                                    <th>Estado</th>
                                                    <th>Detalle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($preview as $row) : ?>
                                                <tr class="<?php echo $row['valido'] ? 'success' : 'danger'; ?>">
                                                    <td><?php echo (int) $row['fila']; ?></td>
                                                    <td><?php echo htmlspecialchars($row['resumen'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo $row['valido'] ? 'Válida' : 'Con errores'; ?></td>
                                                    <td><?php echo htmlspecialchars($row['mensaje'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12">
                                        <?php if ($validas > 0) : ?>
                                        <?php echo form_open('importaciones/confirmar/' . $tipo, array('style' => 'display:inline;', 'class' => 'form-import-confirmar')); ?>
                                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('¿Importar <?php echo (int) $validas; ?> registro(s)?');">
                                            <i class="glyphicon glyphicon-ok icon-white"></i> Confirmar importación (<?php echo (int) $validas; ?>)
                                        </button>
                                        <?php echo form_close(); ?>
                                        <?php endif; ?>
                                        <a href="<?php echo site_url('importaciones/cancelar/' . $tipo); ?>" class="btn btn-default btn-lg">Cancelar</a>
                                        <a href="<?php echo site_url($url_volver); ?>" class="btn btn-link btn-lg">Volver al listado</a>
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
