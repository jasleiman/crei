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
                        		$id_planes_dias='';
                        		//$id_planes='';
								$id_clases_profesionales='';
								$cantidad_horas='';
								
								
                        if (isset($planesdias)) {
                        	foreach ($planesdias as $dias) {
                        		$id_planes_dias=$dias->id_planes_dias;
                        		$id_planes=$dias->id_planes;
								$id_clases_profesionales=$dias->id_clases_profesionales;
								$cantidad_horas=$dias->cantidad_horas;
								
								
                        	}
                        }
						$attributes = array('class' => 'form-horizontal','id' => 'formcarga');
						echo form_open( 'administracion/amPlanesDias', $attributes ); ?>
    
    					<?php echo form_fieldset( ); ?>
						
				        	
				        <?php 
				            if (isset($id_planes_dias)){
					            $valor=  $id_planes_dias;
								
								echo form_hidden( 'id_planes_dias',$valor );
							}
							echo form_hidden( 'id_planes',$id_planes );
				        ?>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
				            <?php 
								
								$options = array(
									
									'1' => 'Maestra Integradora',
									'2' => 'Equipo',
									'3' => 'Maestra de apoyo'
									
								
								);
				                
								//echo form_input( $data ); 
								echo form_dropdown('id_clases_profesionales',$options,$id_clases_profesionales,'class="form-control" data-rel="chosen"')?>
				        </div>
        				<formspan></formspan>
						<div class="clearfix"></div><br>
						<div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
				            <?php 
							$data = array(
				              'name'        => 'cantidad_horas',
				              'id'          => 'cantidad_horas',
				              'maxlength'   => '100',
				              'size'        => '50',
							  'placeholder'	=> 'Cantidad de horas',
							  'class'		=> 'form-control',
							  'value'		=> $cantidad_horas
				            );
							echo form_input( $data ); ?>
				        </div>
				        
				        
				        <div class="clearfix"></div><br>
				        <div id="boton">
					        <p class="center col-md-5">
					            <?php $data = array(
									  'name'        => 'btenviar',
									  'id'          => 'btenviar',
									  'maxlength'   => '100',
									  'size'        => '50',
									  'type'		=>	'button',
									  'value'	  => 'Guardar',
									  'class'		=> 'btn btn-primary',
					            	);
									echo form_input( $data ); ?>
					        <formspan></formspan>
				        </div>
						
        			</div>

    				<?php echo form_fieldset_close(); ?>
					<?php echo form_close(); ?>
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
    <script>
    	var ejecutado=false;
	    $(document).on('click','#btenviar', function (e) {
			
			e.preventDefault();
	        var id = $('#formcarga').serialize();
			$('#boton').html('<img src="<?php echo base_url();?>static/img/ajax-loader-1.gif">');
			if (ejecutado===false) { //verifico que se haya ejecutado para que no se repita en forma automática.
				$.ajax({
					type : "POST",
					url : "<?php echo site_url("administracion/amPlanesDias?uid="); ?>"+Math.random(),
					data: id,
					success: function(data){
						var id2="id_planes="+<?php echo $id_planes;?>;
						$.ajax({
						type : "POST",
						url : "<?php echo site_url("administracion/planesdias?uid="); ?>"+Math.random(),
						data: id2,
						success: function(data){
							
						 	$('#formulario').html(data);
							
						 },
						 error: function(){
						 	$('#formulario').html('Error al editar');
						 }
						});
	
						
					 },
					 error: function(){
					 	$('#formulario').html('Error al editar');
					 }
				});
				ejecutado=true;
			}
			
	
	    	return true;
	
		});
	</script>
    
    