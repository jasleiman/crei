<div class="ch-container">
	<div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-edit"></i>Crear Nuevo tipo de actividad</h2>
           
                </div>
            	<div class="box-content">
                    <div class="control-group">
                        <label class="control-label" for="selectError">Tipo de actividad</label>
                   
                    </div>
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
						$attributes = array('class' => 'form-horizontal');
						echo form_open( 'administracion/amActividades', $attributes ); ?>
    
    					<?php echo form_fieldset( ); ?>
						
				        	
				        <?php 
				            if (isset($id_tipo_actividades)){
				            $valor=  $id_tipo_actividades;
							
							echo form_hidden( 'id_tipo_actividades',$valor );
							}
				        ?>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
				            <?php 
				            $valor= (isset($descripcion) ? $descripcion : '');
							$data = array(
				              'name'        => 'descripcion',
				              'id'          => 'descripcion',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Descripción',
							  'class'		=> 'form-control',
							  'value'		=> $valor
				            );
							echo form_input( $data ); ?>
				        </div>
        				<formspan></formspan>
						<div class="clearfix"></div><br>
        				<div class="input-group input-group-lg">
					        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope green"></i></span>
					            <?php 
									$data = array(
									  '1'        => 'Directa',
									  '0'        => 'Indirecta',
									  
					            	);
					            	$directa =(isset($directa) ? $directa : 1);
									echo form_dropdown('directa',$data,$directa,'class="form-control" data-rel="chosen"'); ?>
        				</div>
       
        				<div class="clearfix"></div><br>
				        <p class="center col-md-5">
				            <?php $data = array(
								  'name'        => 'enviar',
								  'id'          => 'enviar',
								  'maxlength'   => '100',
								  'size'        => '50',
								  'type'		=>	'submit',
								  'value'	  => 'Guardar',
								  'class'		=> 'btn btn-primary',
				            	);
								echo form_input( $data ); ?>
				        <formspan></formspan>
        			</div>

    				<?php echo form_fieldset_close(); ?>
					<?php echo form_close(); ?>
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
    