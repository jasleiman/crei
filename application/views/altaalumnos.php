    <!-- topbar ends -->
  	<?php 
  	
  		//seteo las variable de alumno
  		
  		$alumno_nombre='';
		$alumno_servicio='';
		$alumno_email='';
		$alumno_telefono='';
		$alumno_direccion='';
		$alumno_email='';
		$alumno_dni='';
		$alumno_codigo_postal='';
		$alumno_provincia='';
		$alumno_localidad='';
		$alumno_padre='';
		$alumno_partido='';
		$alumno_diagnostico='';
		$alumno_id_obras_sociales='';
		$alumno_id_personas='';
		$alumno_id_coordinador='';
		if (isset($alumnos)) {
		foreach ($alumnos as $alumno) {
			$alumno_nombre = $alumno -> nombre;
			$alumno_servicio=$alumno -> servicio;
			$alumno_email = $alumno -> email;
			$alumno_telefono = $alumno -> telefono;
			$alumno_direccion = $alumno -> direccion;
			$alumno_email = $alumno -> email;
			$alumno_dni = $alumno -> dni;
			$alumno_codigo_postal = $alumno -> codigo_postal;
			$alumno_provincia = $alumno -> provincia;
			$alumno_localidad = $alumno -> localidad;
			$alumno_padre = $alumno -> padre;
			$alumno_partido = $alumno -> partido;
			$alumno_diagnostico = $alumno -> diagnostico;
			$alumno_id_obras_sociales = $alumno -> id_obras_sociales;
			$alumno_id_personas = $alumno -> id_personas;
			$alumno_id_coordinador = $alumno -> id_coordinador;
			

		}
		}
	  	$id_planes = '';
		$id_alumnos = '';
		$fecha_inicio = '';
		$fecha_fin = '';
		$acta_acuerdo = '';
		$orientacion = '';
		$escuela = '';
		if (isset($planes)) {
		foreach ($planes as $plan) {
			$id_planes = $plan -> id_planes;
			$id_alumnos = $plan -> id_alumnos;
			$fecha_inicio = $plan -> fecha_inicio;
			$fecha_fin = $plan -> fecha_fin;
			$acta_acuerdo = $plan -> acta_acuerdo;
			$orientacion = $plan -> orientacion;
			$escuela = $plan -> escuela;

		}
	}
	?>

<div class="ch-container">
	<div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well" data-original-title="">
                    <h2><i class="glyphicon glyphicon-edit"></i>Crear Nuevo Alumno</h2>
            
                    <div class="box-icon">
                       
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                class="glyphicon glyphicon-chevron-up"></i></a>
                       
                    </div>
                </div>
            	<div class="box-content">
                	
                   
                    <div class="control-group">
                        <label class="control-label" for="selectError">Perfil del Alumno</label>
    
                        <div class="controls">
                            
                        </div>
                    </div>
                    <br/>
                   <div class="form-group">
                               <?php 
	$attributes = array('class' => 'form-horizontal');
	echo form_open( 'administracion/amAlumnos', $attributes ); ?>
    
    <?php echo form_fieldset( ); ?>
		<?php 
				            if (isset($id_alumnos)){
					            
								
								echo form_hidden( 'id_alumnos',$id_alumnos );
							}
				        ?>
        <div class="input-group input-group-lg">
        	<span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
            <?php 
			$data = array(
              'name'        => 'nombre',
              'id'          => 'nombre',
              'maxlength'   => '100',
              'size'        => '50',
              'title'		=> 'Nombre y Apellido',
              'data-toggle' => 'tooltip',
			  'placeholder'	=> 'Nombre y Apellido',
			  'class'		=> 'form-control',
			  'value'		=> $alumno_nombre
            );
			echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
		<div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope green"></i></span>
            <?php 
				$data = array(
				  'name'        => 'email',
				  'id'          => 'email',
				  'maxlength'   => '100',
				  'size'        => '50',
                  'type'        => 'email',
				  'placeholder'	=> 'Email',
				  'title'		=> 'Email',
              		'data-toggle' => 'tooltip',
				  'class'		=> 'form-control',
				  'value'		=> $alumno_email
            	);
				echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-earphone green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'telefono',
                  'id'          => 'telefono',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Teléfono',
                  'title'		=> 'Teléfono',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_telefono
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-home green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'direccion',
                  'id'          => 'direccion',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Dirección',
                  'title'		=> 'Dirección',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_direccion
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'localidad',
                  'id'          => 'localidad',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Localidad',
                  'title'		=> 'Localidad',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_localidad
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'partido',
                  'id'          => 'partido',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Partido',
                  'title'		=> 'Partido',
                  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_partido
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'codigo_postal',
                  'id'          => 'codigo_postal',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Código Postal',
                  'title'		=> 'Código Postal',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_codigo_postal
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-globe green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'provincia',
                  'id'          => 'provincia',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Provincia',
                  'title'		=> 'Provincia',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_provincia
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
             <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'padre',
                  'id'          => 'padre',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Nombre de Padre, Madre o Tutor',
                  'title'		=> 'Nombre de Padre, Madre o Tutor',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_padre
                );
                echo form_input( $data ); ?>
        </div>
          <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'diagnostico',
                  'id'          => 'diagnostico',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Diagnóstico',
                  'title'		=> 'Diagnóstico',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_diagnostico
                );
                echo form_input( $data ); ?>
        </div>
              <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign green"></i></span>
            <?php 
                $data = array(
                  'name'        => 'dni',
                  'id'          => 'dni',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'DNI',
                  'title'		=> 'DNI',
              	  'data-toggle' => 'tooltip',
                  'class'       => 'form-control',
                  'value'		=> $alumno_dni
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg fuente-grande">
        
        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign green"></i></span>
        
            <?php 
                $options = array();
                $options['INICIAL']='INICIAL';
	            $options['PRIMARIO']='PRIMARIO';
	            $options['SECUNDARIO']='SECUNDARIO';
	            
                echo form_dropdown('servicio', $options,$alumno_servicio,'class="form-control" title="Servicio" data-rel="chosen"');
            ?>
        
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg fuente-grande">
        
        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign green"></i></span>
        
            <?php 
                $options = array('' => 'Seleccione coordinador');
                if (!empty($coordinadores)) {
                foreach ($coordinadores as $coordinador)
                {
	                $options[$coordinador->id_personas]=$coordinador->nombre;
	                
                }
                }
                echo form_dropdown('id_coordinador', $options,$alumno_id_coordinador,'class="form-control" id="id_coordinador" title="Coordinador" data-rel="chosen"');
            ?>
        
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg fuente-grande">
        
        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign green"></i></span>
        
            <?php 
                $options = array();
                foreach ($personas as $persona)
                {
	                $options[$persona->id_personas]=$persona->nombre;
	                
                }
                echo form_dropdown('id_personas', $options,$alumno_id_personas,'class="form-control" id="id_personas" title="Maestra Integradora" data-rel="chosen"');
            ?>
        
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg fuente-grande">
        
        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign green"></i></span>
        
            <?php 
                $options = array();
                foreach ($obrassociales as $obrasocial)
                {
	                $options[$obrasocial->id_obras_sociales]=$obrasocial->descripcion;
	                
                }
                echo form_dropdown('id_obras_sociales', $options,$alumno_id_obras_sociales,'class="form-control" title="Obra Social" data-rel="chosen"');
            ?>
        
        </div>
        <br>
        <div class="control-group">
                        <label class="control-label" for="selectError">Datos del plan</label>
    
                        <div class="controls">
                            
                        </div>
                    </div>
                    <br/>
        <formspan></formspan>
        				<?php 
				            if (isset($id_planes)){
				            $valor=  $id_planes;
							
							echo form_hidden( 'id_planes',$valor );
							}
				        ?>
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
							  'title'		=> 'Fecha de inicio del proyecto',
              	  			  'data-toggle' => 'tooltip',
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
							  'title'		=> 'Fecha de fin del proyecto',
              	  			  'data-toggle' => 'tooltip',
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
							  'title'		=> 'Acta acuerdo',
              	  			  'data-toggle' => 'tooltip',
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
							  'title'		=> 'Escuela',
              	  			  'data-toggle' => 'tooltip',
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
							  'title'		=> 'Orientación',
              	  			  'data-toggle' => 'tooltip',
							  'class'		=> 'form-control',
							  'value'		=> $orientacion
				            );
							echo form_input( $data ); ?>
				        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <p class="center col-md-5">
            <?php $data = array(
				  'name'        => 'enviar',
				  'id'          => 'enviar',
				  'maxlength'   => '100',
				  'size'        => '50',
				  'type'		=>	'submit',
				  'value'	  => 'Agregar',
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
<script type="text/javascript">
$(document).on('change', '#id_personas', function() {
	var idMaestra = $(this).val();
	$('#id_coordinador').html('<option value="">Cargando...</option>');
	if (!idMaestra) {
		$('#id_coordinador').html('<option value="">Seleccione coordinador</option>');
		return;
	}
	$.ajax({
		type: 'POST',
		url: '<?php echo site_url('administracion/selectCoordinadoresAlumno'); ?>',
		data: { id: idMaestra },
		success: function(html) {
			$('#id_coordinador').html(html).trigger('chosen:updated');
		}
	});
});
</script>
