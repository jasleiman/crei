<div class="ch-container">
    <div class="row">
        
        <!-- left menu starts -->
        <div class="col-sm-2 col-lg-2">
            <div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked">
                    </div>
                    <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="nav-header"><?php echo $this -> lang -> line('mensajes_titulo_menus'); ?></li>
                        <?php echo $menu; ?>
                    </ul>
                </div>
            </div>
        </div>
        <!--/span-->
        <!-- left menu ends -->

        <noscript>
            <div class="alert alert-block col-md-12">
                <h4 class="alert-heading">Warning!</h4>

                <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
                    enabled to use this site.</p>
            </div>
        </noscript>

        <div id="content" class="col-lg-10 col-sm-10">
            <!-- content starts -->
            <div>
    <ul class="breadcrumb">
        <li>
            <a href="#">Inicio</a>
        </li>
    </ul>
</div>

<div class="ch-container">
	<div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-edit"></i> Filtro</h2>
           
                </div>
            	<div class="box-content">
                    
                    <br/>
                    <div class="form-group">
                        <?php 
                        
                        if (isset($tipo_actividades)) {
                        	foreach ($tipo_actividades as $actividad) {
                        		$id_tipo_actividades=$actividad->id_tipo_actividades;
                        		$descripcion=$actividad->descripcion;
								$directa=$actividad->directa;
                        	}
                        }
						$attributes = array('class' => 'form-horizontal','method' => 'get');
						echo form_open( 'reportes/porPersona', $attributes ); ?>
    
    					<?php echo form_fieldset( ); ?>
						
				        	
				        
				        
						<div class="clearfix"></div><br>
        				<div class="controls">
        				<div class="input-group">
					        <span class="input-group-addon"><i class="green"></i></span>
					            <?php 
									$data = array(
									  '1'        => 'Enero',
									  '2'        => 'Febrero',
									  '3'        => 'Marzo',
									  '4'        => 'Abril',
									  '5'        => 'Mayo',
									  '6'        => 'Junio',
									  '7'        => 'Julio',
									  '8'        => 'Agosto',
									  '9'        => 'Septiembre',
									  '10'        => 'Octubre',
									  '11'        => 'Noviembre',
									  '12'        => 'Diciembre'
									  
									  
					            	);
					            	$mes =(isset($mes) ? $mes : date('m'));
									echo form_dropdown('mes',$data,$mes,'class="form-control" data-rel="chosen" style="width: 183px;"'); ?>
        				</div>
        				</div>
       					<div class="clearfix"></div><br>
       					<p class="center col-md-5">
        				<?php $this->load->view('componentes/anio_en_curso'); ?>
        				</p>
        				<div class="clearfix"></div><br>
				        <p class="center col-md-5">
				            <?php $data = array(
								  'name'        => 'enviar',
								  'id'          => 'enviar',
								  'maxlength'   => '100',
								  'size'        => '50',
								  'type'		=>	'submit',
								  'value'	  => 'Filtrar',
								  'class'		=> 'btn btn-primary',
				            	);
								echo form_input( $data ); ?>
						</p>
				        <formspan></formspan>
        			</div>

    				<?php echo form_fieldset_close(); ?>
					<?php echo form_close(); ?>
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
    
<div class="ch-container">
	<div class="row">
        <div class="box col-md-12">
			<div class="box-inner">
				<div class="box-header well" data-original-title="">
					<h2><i class="glyphicon glyphicon-user"></i> Informe de horas</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
							<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
						</div>
				</div>
			<div class="box-content">
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
					
						<div class="table-wrapper">
							<div class="scrollable">
								<table class="table table-striped table-bordered bootstrap-datatable datatable responsive dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
									<thead>
										<tr role="row">
										
										<th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Username: activate to sort column descending" style="width: 196px;">Persona</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 88px;">Horas Cumplidas</th>
										
										
										</tr>
									</thead>
			<!--impresión de la tabla -->
									
									<?php
									
									if ($datos==false) {
										echo "<h2>Lo siento, no se puede mostrar su consulta</h2>";
										//exit();
									} else {
										echo '<tbody role="alert" aria-live="polite" aria-relevant="all">';
										$i = 1;
						
										foreach ($datos as $plan) {
						
											if ($i % 2 == 0) {
												echo '<tr class="even actual">';
											} else {
												echo '<tr class="odd actual">';
											}
											
											echo '<td class=" sorting_1"><a href="' . site_url('reportes/detallepersonas?id_personas=' . $plan->id_personas . '&mes=' . $mes . '&anio=' . $anio) . '">' . $plan -> nombre . '</a></td>';
											echo '<td class="center ">' . $plan -> horas . '</td>';
											
						
											echo '</tr>';
											$i++;
										}
						
									}
									?>


				
				</tbody>
				</table>
							</div>
							<?php echo '<a href="' . site_url('reportes/exportarExcel?mes=' . $mes . '&anio=' . $anio) . '" class="btn btn-success btn-lg">Exportar a excel</a>'; ?>
					</div>
					</div>
					</div>
				</div>
				</div>
				</div>	

	

