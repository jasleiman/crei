<div class="ch-container">
    <div class="row">
        
        <!-- left menu starts -->
        <div class="col-sm-2 col-lg-2">
            <div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked">
                    </div>
                    <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="nav-header"><?php echo $this->lang->line('mensajes_titulo_menus'); ?></li>
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
					<h2><i class="glyphicon glyphicon-user"></i> Administración de Usuarios</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
							<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
						</div>
				</div>
			<div class="box-content">
				<div class="row" style="text-align: right;">
						
							<div class="col-md-12">
								<a class="btn btn-success alta" href="#"><i class="glyphicon glyphicon-plus-sign icon-white"></i> Nuevo Usuario</a>
								<?php $this->load->view('componentes/boton_papelera', array('entidad' => 'personas')); ?>
							</div>
							<br>
					</div>
					<?php
					$this->load->view('importaciones/panel', array(
						'tipo' => 'maestras',
						'titulo' => 'Importar maestras integradoras desde Excel',
					));
					?>
					<?php $this->load->view('componentes/baja_masiva', array('entidad' => 'personas')); ?>
				
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
					
						<div class="table-wrapper">
							<div class="scrollable">
								<table class="table table-striped table-bordered bootstrap-datatable datatable responsive dataTable tabla-baja-masiva" id="DataTables_Table_0" data-entidad="personas" aria-describedby="DataTables_Table_0_info">
									<thead>
										<tr role="row">
										<th style="width:36px;"><input type="checkbox" class="baja-masiva-th-todos" title="Seleccionar todos"></th>
										<th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Username: activate to sort column descending" style="width: 196px;">Nombre</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 88px;">Teléfono</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 88px;">Email</th>
										<!--<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 88px;">Dirección</th>-->
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Date registered: activate to sort column ascending" style="width: 165px;">Partido</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 374px;">Acciones</th>
										</tr>
									</thead>
			<!-- impresión de la tabla -->
			
			<?php 
					
					if (!isset($personas)) {
						echo "<h2>Lo siento, no se puede mostrar su consulta</h2>";
						exit();
					}else{
						echo '<tbody role="alert" aria-live="polite" aria-relevant="all">';
						$i = 1;
						

							foreach ($personas as $persona) {
							
								if ($i%2==0) {
									echo '<tr class="even">';
								}else{
									echo '<tr class="odd">';
								}

								
								echo '<td class="center"><input type="checkbox" class="baja-masiva-fila" value="'.$persona->id_personas.'"></td>';
								echo '<td class=" sorting_1">'.$persona->nombre.'</td>';
								echo '<td class="center ">'.$persona->telefono.'</td>';
								echo '<td class="center ">'.$persona->email.'</td>';
								//echo '<td class="center ">'.$persona->direccion.'</td>';
								echo '<td class="center ">'.$persona->localidad.'</td>';
								echo '<td class="center ">
										
										
										<a class="btn btn-info btn-editrow" id="'.$persona->id_personas.'"  href="#">
										<i class="glyphicon glyphicon-edit icon-white"></i>
										Editar
										</a>
										<a class="btn btn-deleterow btn-danger btn-sm" id="'.$persona->id_personas.'" href="#">
										<i class="glyphicon glyphicon-trash icon-white"></i>
										Eliminar
										</a>
										</td>
										</tr>';
							$i++;
							}
							
						
					}

					 ?>

							</tbody>
</table></div><div class="pinned">



<?php 

	if (isset($_SESSION["mensaje"])) {
		

		echo '<div class="noty" data-noty-options= "'.$_SESSION["mensaje"].'" aria-hidden="true"></div>';
		echo "<script>$(document).ready( function () {var options = $.parseJSON($('.noty').attr('data-noty-options'));noty(options);});</script>";
	}


?>
  
               
 




   	<div class="modal fade modal-warning" id="modal-warning">
          <div class="modal-dialog">
            <div class="modal-content ">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Eliminación de Usuarios</h4>
              </div>
              <div class="modal-body">
              <div id="formulario-baja">
              Seguro que desea eliminar este usuario?
              <?php 
	              $attributes = array('class' => 'form-horizontal', 'id' => 'form-eliminar');
				  echo form_open( 'administracion/bajapersonas', $attributes ); 
				  $data = array(
					        'type'  => 'hidden',
					        'name'  => 'id_personas',
					        'id'    => 'id_personas',
					        'value' => '0',
					        
					);
					
					echo form_input($data);
				  echo form_close(); ?>
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger btn-eliminar">Eliminar</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

    
    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        
                    </div>
                    <div class="modal-body" id="formulario">
                        
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
                    </div>
                </div>
            </div>
        </div>



<script src="<?php echo base_url(); ?>/static/crei/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url(); ?>/static/crei/js/localization/messages_es_AR.min.js"></script>

<script> 
	$('.line').hide(); //acá escondemos todas las os que renderizó php dentro del popup
	$('.editar').hide();
	$('.hided').hide();
    
    
	
	$(document).on('click','.btn-deleterow', function (e) {
		
		e.preventDefault();
        $('#modal-warning').modal('show');
        var id = $(this).attr('id');
       
		$('#id_personas').val(id);
		

    	return true;

	});
	
	$(document).on('click','.btn-eliminar', function (e) {
		e.preventDefault();
		$('#form-eliminar').submit();
	});
    $(document).on('click','.alta', function (e) {
		
		e.preventDefault();
        $('#modalEditar').modal('show');
        var id=0;
		$('#formulario').html('<img src="<?php echo base_url();?>static/img/ajax-loader-1.gif">');
		$.ajax({
			type : "POST",
			url : "<?php echo site_url("administracion/altapersonas?uid="); ?>"+Math.random(),
			data: id,
			success: function(data){
				
			 	$('#formulario').html(data);
			 	$('#formulario-carga').validate({
					rules : {
						clave : "required",
						clave2 : {
							equalTo : "#clave"
						}
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
				
			 },
			 error: function(){
			 	$('#formulario').html('Error al editar');
			 }
		});
		

    	return true;

	});
	
	$(document).on('click','.btn-editrow', function (e) {
		
		e.preventDefault();
        $('#modalEditar').modal('show');
        var id = "id_personas="+$(this).attr('id');
		$('#formulario').html('<img src="<?php echo base_url();?>static/img/ajax-loader-1.gif">');
		$.ajax({
			type : "POST",
			url : "<?php echo site_url("administracion/altapersonas?uid="); ?>"+Math.random(),
			data: id,
			success: function(data){
				
			 	$('#formulario').html(data);
				
			 },
			 error: function(){
			 	$('#formulario').html('Error al editar');
			 }
		});
		

    	return true;

	});

    $('.cancelar').click(function() {
    	$('.line').hide();
    	$('.editar').hide();
    });
    
    $('.close').click(function() {
    	$('.line').hide();
    	$('.editar').hide();
    });

    $('.form-horizontal').hide();

  	$('.btn-info').click(function() {  // acá le decimos que al clickear eliminar, le pase el id, que se generó también como clase CSS en los <p> del popup y lo muestre
    	var id = $(this).attr('id');
		$('.editar.' + id).show();
    	$('#edit').modal('show');
    });
    $('.Guardar').click(function() {
    	$('.line').hide();
    	$('.editar').hide();
    });


</script>
