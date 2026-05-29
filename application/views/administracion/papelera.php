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

        <div id="content" class="col-lg-10 col-sm-10">
            <div>
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url($url_volver); ?>">Listado</a></li>
                    <li class="active">Papelera</li>
                </ul>
            </div>

            <div class="box col-md-12">
                <div class="box-inner">
                    <div class="box-header well">
                        <h2><i class="glyphicon glyphicon-trash"></i> <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h2>
                    </div>
                    <div class="box-content">
                        <p class="text-muted">
                            Registros dados de baja (no visibles en el listado principal). Podés restaurarlos o eliminarlos definitivamente.
                            La eliminación definitiva no se puede deshacer.
                        </p>

                        <?php if (empty($registros)) : ?>
                            <div class="alert alert-info">La papelera está vacía.</div>
                            <a href="<?php echo site_url($url_volver); ?>" class="btn btn-default">Volver al listado</a>
                        <?php else : ?>
                            <?php
                            $this->load->view('componentes/papelera_toolbar', array(
                                'entidad' => $tipo,
                                'tabla' => 'tabla-papelera',
                            ));
                            ?>
                            <table class="table table-striped table-bordered datatable tabla-papelera" id="tabla-papelera" data-entidad="<?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?>">
                                <thead>
                                    <tr>
                                        <th style="width:36px;"><input type="checkbox" class="papelera-th-todos" title="Seleccionar todos"></th>
                                        <?php foreach ($columnas as $col) : ?>
                                            <th><?php echo htmlspecialchars($col['label'], ENT_QUOTES, 'UTF-8'); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registros as $row) : ?>
                                        <tr>
                                            <td><input type="checkbox" class="papelera-fila" name="ids[]" value="<?php echo (int) $row->id; ?>"></td>
                                            <?php foreach ($columnas as $col) : ?>
                                                <td><?php echo htmlspecialchars(isset($row->{$col['campo']}) ? $row->{$col['campo']} : '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <a href="<?php echo site_url($url_volver); ?>" class="btn btn-default">Volver al listado</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
