<!-- topbar ends -->

<div class="ch-container">
	<div class="row">
		<div class="box col-md-12">
			<div class="box-inner">
				<div class="box-header well" data-original-title="">
					<h2><i class="glyphicon glyphicon-edit"></i>Administración de Usuarios</h2>

					<div class="box-icon">

						<a href="#" class="btn btn-minimize btn-round btn-default"><i
						class="glyphicon glyphicon-chevron-up"></i></a>

					</div>
				</div>
				<div class="box-content">

					<div class="control-group">
						<label class="control-label" for="selectError">Perfil de Usuario</label>

						<div class="controls">

						</div>
					</div>
					<br/>
					<div class="form-group">
						<?php
								$id_personas='';
                        		$nombre='';
								$direccion='';
								$localidad='';
								$telefono='';
								$partido='';
								$provincia='';
								$codigo_postal='';
								$email='';
								$id_perfiles='';
								$id_profesionales='';
								$id_padre='';
								$nombre_usuario='';
								$padre='Ninguno';
                        if (isset($personas)) {
                        	foreach ($personas as $persona) {
                        		$id_personas=$persona->id_personas;
                        		$nombre=$persona->nombre;
								$direccion=$persona->direccion;
								$telefono=$persona->telefono;
								$localidad=$persona->localidad;
								$partido=$persona->partido;
								$provincia=$persona->provincia;
								$codigo_postal=$persona->codigo_postal;
								$email=$persona->email;
								$id_padre=$persona->id_padre;
								$id_profesionales=$persona->id_profesionales;
								
                        	}
							foreach ($usuarios as $usuario) {
                        		$id_usuarios=$usuario->id_usuarios;
                        		
								$id_perfiles=$usuario->id_perfiles;
								$nombre_usuario=$usuario->nombre;
								
                        	}
							
                        }
						
						$attributes = array('class' => 'form-horizontal', 'id' => 'formulario-carga');
						echo form_open('administracion/ampersonas', $attributes);
						?>

						<?php echo form_fieldset(); ?>
						<?php 
				            if (isset($personas)){
					            $valor=  $id_personas;
								
								echo form_hidden( 'id_personas_carga',$valor);
								echo form_hidden( 'id_personas',$valor);
								echo form_hidden( 'id_usuarios',$id_usuarios );
							}
				        ?>
						<label class="control-label" for="nombre">Nombre y apellido</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user blue"></i></span>
							<?php
							$data = array('name' => 'nombre', 'id' => 'nombre', 'maxlength' => '100', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Nombre y Apellido', 'class' => 'form-control', 'required' => '', 'value'=> $nombre );
							echo form_input($data);
							?>

						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="id_profesionales">Tipo de profesional</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user blue"></i></span>
							<?php

							$options = array();
							foreach ($profesionales as $profesional)
							{
							$options[$profesional->id_profesionales]=$profesional->descripcion;

							}
							//echo form_input( $data );
							echo form_dropdown('id_profesionales',$options,$id_profesionales,'class="form-control" data-rel="chosen" id="id_profesionales"')?>
						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="id_padre">Superior. (Deje presionada la tecla Control para seleccionar más de uno)</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user blue"></i></span>
							<?php
								
								
      							echo form_dropdown('id_padre[]',$padre,$id_padre,'class="form-control" data-rel="chosen" id="id_padre" style="height:120px;"')?>   
						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="telefono">Teléfono</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-earphone blue"></i></span>
							<?php
							$data = array('name' => 'telefono', 'id' => 'telefono', 'maxlength' => '100', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Teléfono', 'class' => 'form-control', 'value'=> $telefono );
							echo form_input($data);
							?>
						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="direccion">Dirección</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
							<?php
							$data = array('name' => 'direccion', 'id' => 'direccion', 'maxlength' => '100', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Dirección', 'class' => 'form-control', 'value'=> $direccion );
							echo form_input($data);
							?>
						</div>
						<formspan></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="localidad">Localidad</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
							<?php
							$data = array('name' => 'localidad', 'id' => 'localidad', 'maxlength' => '100', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Localidad', 'class' => 'form-control', 'value'=> $localidad );
							echo form_input($data);
							?>
						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="partido">Partido</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
							<?php
							$data = array('name' => 'partido', 'id' => 'partido', 'maxlength' => '100', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Partido', 'class' => 'form-control', 'value'=> $partido );
							echo form_input($data);
							?>
						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="codigo_postal">Código postal</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>
							<?php
							$data = array('name' => 'codigo_postal', 'id' => 'codigo_postal', 'maxlength' => '10', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Código Postal', 'class' => 'form-control', 'value'=> $codigo_postal );
							echo form_input($data);
							?>
						</div>
						<formspan class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="provincia">Provincia</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-globe blue"></i></span>
							<?php
							$data = array('name' => 'provincia', 'id' => 'provincia', 'maxlength' => '100', 'minlength' => '4', 'size' => '50', 'placeholder' => 'Provincia', 'class' => 'form-control', 'value'=> $provincia );
							echo form_input($data);
							?>
						</div>
						<formspan></formspan>
						<div class="clearfix"></div>
						<br>
						
						<div class="control-group">
							<label class="control-label" for="selectError">Datos de inicio de sesión</label>

							<div class="controls">

							</div>
						</div>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="email">Email</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>
							<?php
							$data = array('name' => 'email', 'id' => 'email', 'maxlength' => '100', 'size' => '50', 'type' => 'text', 'placeholder' => 'Correo electrónico (nombre de usuario)', 'class' => 'form-control', 'required' => '', 'value'=> $nombre_usuario );
							echo form_input($data);
							?>
						</div>
						<formspan  class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="clave">Clave</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock blue"></i></span>
							<?php
							$clave='';
							if (isset($id_personas)) $clave='******';
							$data = array('name' => 'clave', 'id' => 'clave','value' => $clave, 'maxlength' => '100', 'size' => '50', 'type' => 'password', 'placeholder' => 'Contraseña', 'class' => 'form-control', 'required' => '', );
							echo form_input($data);
							?>
						</div>
						<formspan  class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="clave2">Repetir clave</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock blue"></i></span>
							<?php
							
							$data = array('name' => 'clave2', 'id' => 'clave2','value' => $clave, 'maxlength' => '100', 'size' => '50', 'type' => 'password', 'placeholder' => 'Repertir contraseña', 'class' => 'form-control', 'required' => '', );
							echo form_input($data);
							?>
						</div>
						<formspan  class='error'></formspan>
						<div class="clearfix"></div>
						<br>
						<label class="control-label" for="id_perfiles">Perfil</label>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user blue"></i></span>
							<?php

							$options = array();
							foreach ($perfiles as $perfil)
							{
							$options[$perfil->id_perfiles]=$perfil->descripcion;

							}
							//echo form_input( $data );
							echo form_dropdown('id_perfiles',$options,$id_perfiles,'class="form-control" data-rel="chosen"')?>
						</div>
						<formspan class='error'></formspan>

						<div class="clearfix"></div>
						<br>
						<p class="center col-md-5">
							<?php $data = array('name' => 'enviar', 'id' => 'enviar', 'maxlength' => '100', 'size' => '50', 'type' => 'submit', 'value' => 'Guardar', 'class' => 'btn btn-primary', );
							echo form_input($data);
							?>
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
function actualizarSelectSuperior(idProfesional, idPersonas) {
	var $padre = $('#id_padre');
	$padre.html('<option value="">Cargando...</option>');
	if (idProfesional === '2') {
		$padre.prop('multiple', 'multiple');
	} else {
		$padre.prop('multiple', false);
	}
	$.ajax({
		url: "<?php echo site_url('administracion/selectPadre'); ?>",
		data: { id: idProfesional, id_personas: idPersonas || '' },
		type: "POST",
		success: function(data) {
			$padre.html(data);
			if ($padre.data('chosen')) {
				$padre.trigger('chosen:updated');
			}
		},
		error: function() {
			$padre.html('<option value="">Error al cargar superiores</option>');
			if ($padre.data('chosen')) {
				$padre.trigger('chosen:updated');
			}
		}
	});
}

$(document).on('change', '#id_profesionales', function() {
	actualizarSelectSuperior($(this).val(), $("input[name='id_personas_carga']").val());
});

$(function() {
	var valor = $("#id_profesionales").val();
	if (valor) {
		actualizarSelectSuperior(valor, $("input[name='id_personas_carga']").val());
	}
});
</script>

