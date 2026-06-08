    <link href='<?php echo base_url(); ?>static/charisma/bower_components/datepickerjquery/jquery-ui.css' rel='stylesheet'>
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
                    <h2><i class="glyphicon glyphicon-edit"></i>Carga de horas</h2>
            
                    <div class="box-icon">
                       
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                class="glyphicon glyphicon-chevron-up"></i></a>
                       
                    </div>
                </div>
            	<div class="box-content">
                	
                   
                    
                    <br/>
                   <div class="form-group">
                               <?php
					$attributes = array('class' => 'form-horizontal','id' => 'formAteneo', 'name'=>'formAteneo');
					echo form_open('principal/grabarAteneo', $attributes);
 ?>


    <?php echo form_fieldset(); ?>
		<label class="control-label" for="fecha">Fecha</label>
        <div class="input-group input-group-lg">
        	
        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
            <?php
				$data = array('name' => 'fecha', 'id' => 'datepicker', 'maxlength' => '10','minlength' => '10', 'size' => '50', 'placeholder' => 'Fecha', 'class' => 'form-control datepicker', );
				echo form_input($data);
 ?>
        </div>
        <formspan class='error'></formspan>
        <div class="clearfix"></div><br>
        <label class="control-label" for="hora_inicio">Hora de inicio (Formato 00:00)</label>
        <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-time green"></i></span>
            <?php
				$data = array('name' => 'hora_inicio', 'id' => 'hora_inicio', 'maxlength' => '5','minlength' => '5', 'size' => '50', 'placeholder' => 'Inicio de Actividad', 'class' => 'form-control clockpicker', );
				echo form_input($data);
 ?>
        </div>
        <formspan class='error'></formspan>
         <div class="clearfix"></div><br>
         <label class="control-label" for="hora_fin">Hora de fin (Formato 00:00)</label>
        <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-time red"></i></span>
            <?php
				$data = array('name' => 'hora_fin', 'id' => 'hora_fin', 'maxlength' => '5','minlength' => '5', 'size' => '50', 'placeholder' => 'Fin de Actividad ', 'class' => 'form-control clockpicker', );
				echo form_input($data);
 ?>
        </div>
		<formspan class='error'></formspan>
        <div class="clearfix"></div><br>
        <label class="control-label" for="id_maestra_filtro">Maestra integradora (buscar alumnos)</label>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
            <?php 
				
				$options = array('' => '— Seleccione maestra (opcional) —');
                if (!empty($personas)) {
                foreach ($personas as $persona)
                {
	                $options[$persona->id_personas]=$persona->nombre;
	                
                }
                }
				echo form_dropdown('id_maestra_filtro',$options,'','class="form-control" data-rel="chosen" id="id_maestra_filtro"')?>
        </div>
        
        <div style="display: none;">
        	
        
        
            <?php 
				
				$options = array();
                if (!empty($alumnos)) {
                foreach ($alumnos as $alumno)
                {
	                $options[$alumno->id_alumnos]=$alumno->nombre;
	                
                }
                }
				//echo form_input( $data ); 
				echo form_dropdown('id_alumnos',$options,'','class="form-control" id="id_alumnos"')?>
       
        </div>
        <div class="clearfix"></div><br>
        <?php $data = array('name' => 'agregar', 'id' => 'agregar', 'maxlength' => '100', 'size' => '50', 'type' => 'button', 'value' => 'Agregar alumnos', 'class' => 'btn btn-primary', );
			echo form_input($data);
 ?>	
        <div class="clearfix"></div><br>
		<label class="control-label" for="id_alumnos">Alumno</label>
        <div class="input-group input-group-lg">
        
        <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
        
            <?php 
				
				$options = array();
                if (!empty($alumnostodos)) {
                foreach ($alumnostodos as $alumno)
                {
	                $options[$alumno->id_alumnos]=$alumno->nombre;
	                
                }
                }
				//echo form_input( $data ); 
				echo form_dropdown('id_alumnos_grupo[]',$options,'','class="form-control" data-rel="chosen" multiple id="id_alumnos_grupo" ')?>
       
        </div>
        <div class="clearfix"></div><br>
        <label class="control-label" for="id_tipo_actividades">Actividad</label>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
            <?php 
				
				$options = array();
                foreach ($tipo_actividades as $actividad)
                {
	                $options[$actividad->id_tipo_actividades]=$actividad->descripcion;
	                
                }
				//echo form_input( $data ); 
				echo form_dropdown('id_tipo_actividades',$options,'','class="form-control" data-rel="chosen" id="id_tipo_actividades"')?>
        </div>
        <div class="clearfix"></div><br>
        <label class="control-label" for="observaciones">Observaciones</label>
        <div class="input-group fuente-grande">
        <span class="input-group-addon"><i class="glyphicon glyphicon-eye-open green"></i></span>
            <?php
			$data = array('name' => 'observaciones', 'id' => 'observaciones', 'placeholder' => 'Observaciones', 'class' => 'form-control', 'rows' => '5');
			echo form_textarea($data);
 ?>
        </div>

   

        <div class="clearfix"></div><br>
        <p class="center col-md-5">
            <?php $data = array('name' => 'enviar', 'id' => 'enviar', 'maxlength' => '100', 'size' => '50', 'type' => 'submit', 'value' => 'Guardar', 'class' => 'btn btn-primary', );
			echo form_input($data);
 ?>
        </div>

    <?php echo form_fieldset_close(); ?>
<?php echo form_close(); ?>
                    </div>
                    
                   
                </div>
            </div>

        </div>
        
        <script src="<?php echo base_url(); ?>static/charisma/bower_components/datepickerjquery/jquery-ui.js"></script>
        <script>
			//$('.clockpicker').clockpicker();
        </script>
        <script>
			$.datepicker.regional['es'] = {
				closeText : 'Cerrar',
				prevText : '<Ant',
				nextText : 'Sig>',
				currentText : 'Hoy',
				monthNames : ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				monthNamesShort : ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
				dayNames : ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
				dayNamesShort : ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
				dayNamesMin : ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
				weekHeader : 'Sm',
				dateFormat : 'dd/mm/yy',
				firstDay : 1,
				isRTL : false,
				showMonthAfterYear : false,
				yearSuffix : ''
			};
			$.datepicker.setDefaults($.datepicker.regional['es']);
			$("#datepicker").datepicker();
</script>

<script type="text/javascript">
    	$(document).on('change', '#id_tipo_actividades', function(e) {
                $('#hora_inicio').attr('readonly', false);
                $('#hora_fin').attr('readonly', false);
            if($(this).val() === '12' || $(this).val() === '13'){
                $('#hora_inicio').val('00:00').attr('readonly', 'readonly');
                $('#hora_fin').val('00:00').attr('readonly', 'readonly');
            }
        });
	$(document).on('change', '#id_maestra_filtro', function(e) {
	//$("#id_alumnos").html('<img src="<?php echo base_url(); ?>	static/img/ajax - loader - 1.gif">');
	$('#agregar').hide();
	/*dropdown post *///
	$.ajax({
	url:"<?php echo base_url(); ?>index.php/principal/selectalumno",
		data: {id:	$(this).val()},
		type: "POST",
		success:function(data){
			$("#id_alumnos").empty();
		$("#id_alumnos").html(data);
		$("#id_alumnos").trigger("chosen:updated");
		$('#agregar').show();
		}
		});
		});
	
	$(document).on('click', '#agregar', function(e) {
		
		
		var valores = $("#id_alumnos option").each(function() 
		{
			
			
			$('#id_alumnos_grupo option[value="'+$(this).val()+'"]').attr('selected', 'selected');
			
		    
		});
		
		$("#id_alumnos_grupo").trigger("chosen:updated");
		
});

	$('#formAteneo').validate({
					ignore: ':hidden:not(select)',
					rules : {
						fecha : {required: true, dateES: true},
						hora_inicio : {required: true, time24: true},
						hora_fin : {required: true, time24: true},
						id_tipo_actividades : {required: true},
						id_alumnos_grupo : {required: true}
					},
					messages : {
						fecha : {required: 'Ingrese la fecha'},
						hora_inicio : {required: 'Ingrese la hora de inicio'},
						hora_fin : {required: 'Ingrese la hora de fin'},
						id_tipo_actividades : {required: 'Seleccione una actividad'},
						id_alumnos_grupo : {required: 'Seleccione al menos un alumno'}
					},
					errorPlacement : function(label, elem) {
						var dest = elem.parent().next('formspan');
						if (dest.length) {
							dest.append(label);
						} else {
							label.insertAfter(elem.parent());
						}
						elem.parent().addClass('has-error');
					},
					unhighlight : function(element, errorClass, validClass) {
						$(element).parent().removeClass('has-error').addClass('has-success');
		
					}
				});
</script>
<?php
	if (isset($_SESSION['mensaje'])) {
		echo '<div class="noty" data-noty-options= "' . $_SESSION['mensaje'] . '" aria-hidden="true"></div>';
		echo '<script>$(document).ready(function () {var options = $.parseJSON($(\'.noty\').attr(\'data-noty-options\'));noty(options);});</script>';
	}
?>

    </div>
  

    