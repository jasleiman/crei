<div class="ch-container">
	<div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-edit"></i>Crear Nuevo Tipo de profesional</h2>
           
                </div>
            	<div class="box-content">
                    <div class="control-group">
                        <label class="control-label" for="selectError">Tipo de Profesional</label>
                   
                    </div>
                    <br/>
                    <div class="form-group">
                        <?php 
                        		$id_profesionales='';
                        		$descripcion='';
								$id_padre='';
								$id_clases_profesionales='';
								
                        if (isset($profesionales)) {
                        	
                        	foreach ($profesionales as $profesional) {
                        		$id_profesionales=$profesional->id_profesionales;
                        		$descripcion=$profesional->descripcion;
								$id_padre=$profesional->id_padre;
								$id_clases_profesionales=$profesional->id_clases_profesionales;
								
								
                        	}
                        }
						$attributes = array('class' => 'form-horizontal');
						echo form_open( 'administracion/amProfesionales', $attributes ); ?>
    
    					<?php echo form_fieldset( ); ?>
						
				        	
				        <?php 
				            if (isset($id_profesionales)){
				            $valor=  $id_profesionales;
							
							echo form_hidden( 'id_profesionales',$valor );
							}
				        ?>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'descripcion',
				              'id'          => 'descripcion',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Descripción',
							  'class'		=> 'form-control',
							  'value'		=> $descripcion
				            );
							echo form_input( $data ); ?>
				        </div>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
				            <?php 
								
								$options = array();
				                foreach ($clases_profesionales as $lista)
				                {
					                $options[$lista->id_clases_profesionales]=$lista->descripcion;
					                
				                }
								//echo form_input( $data ); 
								echo form_dropdown('id_clases_profesionales',$options,$id_clases_profesionales,'class="form-control" data-rel="chosen"')?>
				        </div>
        				<formspan></formspan>
						<div class="clearfix"></div><br>
				        <div class="control-group">
                        	<label class="control-label" for="selectError">Depende de</label>
                   
	                    </div>
	                    <br/>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
				            <?php 
								
								$options = array();
				                foreach ($profesionales_todos as $lista)
				                {
				                	$options[]='Ninguno';
					                $options[$lista->id_profesionales]=$lista->descripcion;
					                
				                }
								//echo form_input( $data ); 
								echo form_dropdown('id_padre',$options,$id_padre,'class="form-control" data-rel="chosen"')?>
				        </div>
        				<formspan></formspan>
						<div class="clearfix"></div><br>
						
						
       
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
    
    