    <!-- topbar ends -->

      <link href='<?php echo base_url(); ?>static/charisma/bower_components/datepickerjquery/jquery-ui.css' rel='stylesheet'>

	<?php
		$id_actividades='';
		$fecha='';
		$hora_inicio='';
		$hora_fin='';
		$id_personas='';
		$id_alumnos='';
		$observaciones='';
		$id_tipo_actividades='';
 		if (isset($horas)) {
			foreach ($horas as $hora){
				$id_actividades=$hora->id_actividades;
				$fecha=date('d/m/Y',strtotime($hora->fecha_inicio));
				$hora_inicio=date('H:i',strtotime($hora->fecha_inicio));
				$hora_fin=date('H:i',strtotime($hora->fecha_fin));
				$id_personas=$hora->id_personas;
				$id_alumnos=$hora->id_alumnos;
				$observaciones=$hora->observaciones;
				$id_tipo_actividades=$hora->id_tipo_actividades;
			}
		}
	?>

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
					$attributes = array('class' => 'form-horizontal','id' => 'formulario-carga');
					echo form_open('principal/cargaActividades', $attributes);
					 if (isset($id_actividades)){


								echo form_hidden( 'id_actividades',$id_actividades );
							}
 ?>


    <?php echo form_fieldset(); ?>
		<label class="control-label" for="fecha">Fecha</label>
        <div class="input-group input-group-lg">

        	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green "></i></span>
            <?php
				$data = array('name' => 'fecha', 'id' => 'datepicker', 'maxlength' => '10','minlength' => '10', 'size' => '50', 'placeholder' => 'Fecha', 'class' => 'form-control datepicker','required' => '','value' => $fecha);
				echo form_input($data);
 ?>
        </div>
        <formspan class='error'></formspan>
        <div class="clearfix"></div><br>
        <label class="control-label" for="hora_inicio">Hora de inicio (Formato 00:00)</label>
        <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-time green"></i></span>
            <?php
				$data = array('name' => 'hora_inicio', 'id' => 'hora_inicio', 'maxlength' => '5','minlength' => '5', 'size' => '50', 'placeholder' => 'Inicio de Actividad', 'class' => 'form-control','required' => '', 'value' => $hora_inicio);
				echo form_input($data);
 ?>
        </div>
        <formspan class='error'></formspan>
         <div class="clearfix"></div><br>
         <label class="control-label" for="hora_fin">Hora de fin (Formato 00:00)</label>
        <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-time red"></i></span>
            <?php
				$data = array('name' => 'hora_fin', 'id' => 'hora_fin', 'maxlength' => '5','minlength' => '5', 'size' => '50', 'placeholder' => 'Fin de Actividad ', 'class' => 'form-control','required' => '', 'value' => $hora_fin);
				echo form_input($data);
 ?>
        </div>
		<formspan class='error'></formspan>
        <div class="clearfix"></div><br>
        <label class="control-label" for="id_personas">Maestra</label>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
            <?php

				$options = array();
                if (is_array($personas))
                foreach ($personas as $persona)
                {
	                $options[$persona->id_personas]=$persona->nombre;

                }
				//echo form_input( $data );
				echo form_dropdown('id_personas',$options,$id_personas,'class="form-control" data-rel="chosen" id="id_personas"')?>
        </div>
        <formspan class='error'></formspan>
        <div class="clearfix"></div><br>
		<label class="control-label" for="id_alumnos">Alumno</label>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>

            <?php

				$options = array();
                foreach ($alumnos as $alumno)
                {
	                $options[$alumno->id_alumnos]=$alumno->nombre;

                }
				//echo form_input( $data );
				echo form_dropdown('id_alumnos',$options,$id_alumnos,'class="form-control" data-rel="chosen" id="id_alumnos"')?>

        </div>
        <formspan class='error'></formspan>
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
				echo form_dropdown('id_tipo_actividades',$options,$id_tipo_actividades,'class="form-control" data-rel="chosen" id="id_tipo_actividades"')?>
        </div>
        <formspan class='error'></formspan>
        <div class="clearfix"></div><br>
        <label class="control-label" for="observaciones">Observaciones</label>
        <div class="input-group fuente-grande">
        <span class="input-group-addon"><i class="glyphicon glyphicon-eye-open green"></i></span>
            <?php
			$data = array('name' => 'observaciones', 'id' => 'observaciones', 'placeholder' => 'Observaciones', 'class' => 'form-control', 'rows' => '5','value'=>$observaciones);
			echo form_textarea($data);
 ?>
        </div>


		<formspan class='error'></formspan>
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
        <!-- <script src="<?php echo base_url(); ?>static/charisma/bower_components/clockpicker/clockpicker.js"></script> -->
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
	$(document).on('change', '#id_personas', function(e) {
	//	$("#id_alumnos").empty();
	//$("#id_alumnos").html('<img src="<?php //echo base_url(); ?>static/img/ajax-loader-1.gif">');

	/*dropdown post *///
	$.ajax({
	url:"<?php echo base_url(); ?>index.php/principal/selectalumno",
		data: {id:	$(this).val()},
		type: "POST",
		success:function(data){
			$("#id_alumnos").empty();
			$("#id_alumnos").html(data);
			$("#id_alumnos").trigger("chosen:updated");

		}
		});
	});
	jQuery.validator.addMethod("greaterThan",
	function(value, element, params) {
	    if (!/Invalid|NaN/.test(new Date(value))) {
	        return new Date(value) >= new Date(params);
	    }

	    return isNaN(value) && isNaN($(params).val())
	        || (Number(value) >= Number($(params).val()));
	},'La fecha debe ser al menos <?php echo date('01-m-Y'); ?>.');

	$('#formulario-carga').validate({
					rules : {
						fecha : {dateES:true},
						hora_inicio : {time24: true},
						hora_fin : {time24: true},


					},
					errorPlacement : function(label, elem) {
						elem.parent().next("formspan").append(label);
						//$(this).parent().removeClass('has-error');
						elem.parent().addClass('has-error');
					},
					unhighlight : function(element, errorClass, validClass) {
						$(element).parent().removeClass('has-error').addClass('has-success');

					}
				});

</script>
<?php

	if (isset($_SESSION["mensaje"])) {


		echo '<div class="noty" data-noty-options= "'.$_SESSION["mensaje"].'" aria-hidden="true"></div>';
		echo "<script>$(document).ready( function () {var options = $.parseJSON($('.noty').attr('data-noty-options'));noty(options);});</script>";
	}


?>
    </div>
