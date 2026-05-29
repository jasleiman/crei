<div class="ch-container">
	<div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-edit"></i>Crear Nuevo Plan</h2>
           
                </div>
            	<div class="box-content">
                    <div class="control-group">
                        <label class="control-label" for="selectError">Plan</label>
                   
                    </div>
                    <br/>
                    <div class="form-group">
                        <?php 
                        		$id_planes='';
                        		$id_alumnos='';
								$fecha_inicio='';
								$fecha_fin='';
								$acta_acuerdo='';
								$orientacion='';
								$escuela='';
                        if (isset($planes)) {
                        	foreach ($planes as $plan) {
                        		$id_planes=$plan->id_planes;
                        		$id_alumnos=$plan->id_alumnos;
								$fecha_inicio=$plan->fecha_inicio;
								$fecha_fin=$plan->fecha_fin;
								$acta_acuerdo=$plan->acta_acuerdo;
								$orientacion=$plan->orientacion;
								$escuela=$plan->escuela;
								
                        	}
                        }
						$attributes = array('class' => 'form-horizontal');
						echo form_open( 'administracion/amPlanes', $attributes ); ?>
    
    					<?php echo form_fieldset( ); ?>
						
				        	
				        <?php 
				            if (isset($id_planes)){
				            $valor=  $id_planes;
							
							echo form_hidden( 'id_planes',$valor );
							}
				        ?>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
				            <?php 
								
								$options = array();
				                foreach ($alumnos as $alumno)
				                {
					                $options[$alumno->id_alumnos]=$alumno->nombre;
					                
				                }
								//echo form_input( $data ); 
								echo form_dropdown('id_alumnos',$options,$id_alumnos,'class="form-control" data-rel="chosen"')?>
				        </div>
        				<formspan></formspan>
						<div class="clearfix"></div><br>
						<div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'fecha_inicio',
				              'id'          => 'fecha_inicio',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Fecha de inicio del proyecto',
							  'class'		=> 'form-control datepicker',
							  'value'		=> $fecha_inicio
				            );
							echo form_input( $data ); ?>
				        </div>
				        <formspan></formspan>
						<div class="clearfix"></div><br>
						<div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'fecha_fin',
				              'id'          => 'fecha_fin',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Fecha de fin del proyecto',
							  'class'		=> 'form-control datepicker',
							  'value'		=> $fecha_fin
				            );
							echo form_input( $data ); ?>
				        </div>
				        <formspan></formspan>
						<div class="clearfix"></div><br>
						<div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'acta_acuerdo',
				              'id'          => 'acta_acuerdo',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Acta acuerdo',
							  'class'		=> 'form-control',
							  'value'		=> $acta_acuerdo
				            );
							echo form_input( $data ); ?>
				        </div>
				        <formspan></formspan>
						<div class="clearfix"></div><br>
						<div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'escuela',
				              'id'          => 'escuela',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Escuela',
							  'class'		=> 'form-control',
							  'value'		=> $escuela
				            );
							echo form_input( $data ); ?>
				        </div>
				        <formspan></formspan>
						<div class="clearfix"></div><br>
						<div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'orientacion',
				              'id'          => 'orientacion',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Orientacion',
							  'class'		=> 'form-control',
							  'value'		=> $orientacion
				            );
							echo form_input( $data ); ?>
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
    
    