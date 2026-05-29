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
					<h2><i class="glyphicon glyphicon-user"></i> Administración de Obras sociales</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
							<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
						</div>
				</div>
			<div class="box-content">
				<div class="row" style="text-align: right;">
						
							<div class="col-md-12">
								<a class="btn btn-success" href="administracion/altaobrassociales"><i class="glyphicon glyphicon-plus-sign icon-white"></i> Agregar O.S</a>
								<?php $this->load->view('componentes/boton_papelera', array('entidad' => 'obrassociales')); ?>
							</div>
							<br>
					</div>
					<?php
					$this->load->view('importaciones/panel', array(
						'tipo' => 'obrassociales',
						'titulo' => 'Importar obras sociales desde Excel',
					));
					?>
					<?php $this->load->view('componentes/baja_masiva', array('entidad' => 'obrassociales')); ?>
				
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
					
						<div class="table-wrapper">
							<div class="scrollable">
								<table class="table table-striped table-bordered bootstrap-datatable datatable responsive dataTable tabla-baja-masiva" id="DataTables_Table_0" data-entidad="obrassociales" aria-describedby="DataTables_Table_0_info">
									<thead>
										<tr role="row">
										<th style="width:36px;"><input type="checkbox" class="baja-masiva-th-todos" title="Seleccionar todos"></th>
										<th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Username: activate to sort column descending" style="width: 196px;">Nombre</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 88px;">Teléfono</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Role: activate to sort column ascending" style="width: 88px;">Email</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 88px;">Dirección</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Date registered: activate to sort column ascending" style="width: 165px;">Partido</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 286px;">Acciones</th>
										</tr>
									</thead>
			<!-- impresión de la tabla -->
			<?php 
					if (!isset($obrassociales) || !is_array($obrassociales)) {
						$obrassociales = array();
					}
					echo '<tbody role="alert" aria-live="polite" aria-relevant="all">';
					$i = 1;
					foreach ($obrassociales as $obrasocial) {
							
								if ($i%2==0) {
									echo '<tr class="even">';
								}else{
									echo '<tr class="odd">';
								}

								echo '<td class="center"><input type="checkbox" class="baja-masiva-fila" value="'.$obrasocial->id_obras_sociales.'"></td>';
								echo '<td class=" sorting_1">'.$obrasocial->descripcion.'</td>';
								echo '<td class="center ">'.$obrasocial->telefono.'</td>';
								echo '<td class="center ">'.$obrasocial->email.'</td>';
								echo '<td class="center ">'.$obrasocial->contacto.'</td>';
								echo '<td class="center ">'.$obrasocial->direccion.', '.$obrasocial->localidad.', '.$obrasocial->provincia.'</td>';
								echo '<td class="center ">
										<a class="btn btn-info" id="'.$obrasocial->id_obras_sociales.'"  href="#">
										<i class="glyphicon glyphicon-edit icon-white"></i>
										Editar
										</a>
										<a class="btn btn-deleterow btn-danger" id="'.$obrasocial->id_obras_sociales.'" href="#">
										<i class="glyphicon glyphicon-trash icon-white"></i>
										Eliminar
										</a>
										</td>
										</tr>';
							$i++;
					}

					 ?>


</tbody>
</table></div><div class="pinned"><table class="table table-striped table-bordered bootstrap-datatable datatable dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">



<?php 

	if (isset($_SESSION["mensaje"])) {
		

		echo '<div class="noty" data-noty-options= "'.$_SESSION["mensaje"].'" aria-hidden="true"></div>';
		echo "<script>$(document).ready( function () {var options = $.parseJSON($('.noty').attr('data-noty-options'));noty(options);});</script>";
	}


?>
  
               
 




    <div class="modal fade" id="del" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h3>Atención</h3>
                </div>
                <div class="modal-body">
                	<?php foreach ($obrassociales as $obrasocial){

                		echo '<p class = "line '.$obrasocial->id_obras_sociales.'"> Está a punto de eliminar la Obra Social <span>'.$obrasocial->descripcion.'</span>.</p>';
                	}
                  ?>
                </div>
                <div class="modal-footer">
                	<?php foreach ($obrassociales as $obrasocial){
                	echo '
						<div class = "line '.$obrasocial->id_obras_sociales.'">
                	<form action="'.base_url().'index.php/administracion/bajaobrassocial" class="form-horizontal" method="post" accept-charset="utf-8">
                    	<a href="#" class="btn btn-default cancelar" data-dismiss="modal">Cancelar</a>
						 <input type="text" id="id_obras_sociales" name="id_obras_sociales" value="'.$obrasocial->id_obras_sociales.'" maxlength="100" size="50" placeholder="'.$obrasocial->id_obras_sociales.'" class="form-control hided">
                    	<input type="submit" name="enviar" value="Eliminar" id="enviar" maxlength="100" size="50" class="btn btn-danger eliminar">
                    </form></div>'; } ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h3>Modificar Datos</h3>
                </div>
                <div class="modal-body">
    
				   <?php 
				      
				   	foreach ($obrassociales as $obrasocial){
				      echo


				      	'<div class = "editar '.$obrasocial->id_obras_sociales.'">

				      	<form action="'.base_url().'index.php/administracion/amobrassociales" class="form-horizontal " method="post" accept-charset="utf-8">

				        <fieldset>

						<div class="input-group  input-group-lg">
				        	
				            <input type="text" id="id_obras_sociales" name="id_obras_sociales" value="'.$obrasocial->id_obras_sociales.'" maxlength="100" size="50" placeholder="'.$obrasocial->id_obras_sociales.'" class="form-control hided">
				        </div>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-briefcase green"></i></span>
				            <input type="text" name="descripcion" value="'.$obrasocial->descripcion.'" id="descripcion" maxlength="100" size="50" placeholder="'.$obrasocial->descripcion.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				            <span class="input-group-addon"><i class="glyphicon glyphicon-user green"></i></span>
				            <input type="text" name="contacto" value="'.$obrasocial->contacto.'" id="contacto" maxlength="100" size="50" placeholder="'.$obrasocial->contacto.'" class="form-control">
				        </div>
				        <formspan></formspan>
						<div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope green"></i></span>
				            <input type="email" name="email" value="'.$obrasocial->email.'" id="email" maxlength="100" size="50" placeholder="'.$obrasocial->email.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-earphone green"></i></span>
				            <input type="text" name="telefono" value="'.$obrasocial->telefono.'" id="telefono" maxlength="100" size="50" placeholder="'.$obrasocial->telefono.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-home green"></i></span>
				            <input type="text" name="direccion" value="'.$obrasocial->direccion.'" id="direccion" maxlength="100" size="50" placeholder="'.$obrasocial->direccion.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker green"></i></span>
				            <input type="text" name="localidad" value="'.$obrasocial->localidad.'" id="localidad" maxlength="100" size="50" placeholder="'.$obrasocial->localidad.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker green"></i></span>
				            <input type="text" name="partido" value="'.$obrasocial->partido.'" id="partido" maxlength="100" size="50" placeholder="'.$obrasocial->partido.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope green"></i></span>
				            <input type="text" name="codigo_postal" value="'.$obrasocial->codigo_postal.'" id="codigo_postal" maxlength="100" size="50" placeholder="'.$obrasocial->codigo_postal.'" class="form-control">
				        </div>
				        <formspan></formspan>
				        <div class="clearfix"></div><br>
				        <div class="input-group input-group-lg">
				        <span class="input-group-addon"><i class="glyphicon glyphicon-globe green"></i></span>
				            <input type="text" name="provincia" value="'.$obrasocial->provincia.'" id="provincia" maxlength="100" size="50" placeholder="'.$obrasocial->provincia.'" class="form-control">
				        </div>
						<formspan></formspan>
				        <div class="clearfix"></div><br>
				        <p class="center col-md-5">
				            <input type="submit" name="enviar" value="Guardar" id="enviar" maxlength="100" size="50" class="btn btn-primary Guardar">
				        </p></fieldset></form>
				                </div> '; } ?>
                
                <div class="modal-footer">
                    	<a href="#" class="btn btn-default cancelar" data-dismiss="modal">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
  </div>




<!-- no me funcionan los JQuery/ Java /// el  script de abajo selecciona el botón eliminar y muestra un div(modal, esto lo robé de charisma) los botones ya toman como ID, el ID de la fila que corresponde, me faltan los métodos para eliminar, mostrar mas y modificar.
habría que.... en este caso hacer una consulta para ver si la OS no está asociada a nadie, si lo está, no debería poderse eliminar, igual creo que va a haber una excepcion de modelo al intentarlo. DEBERÏA

ahora me doy cuenta, que la tabla funciona " milagrosamente, sola" así que algún JS está cargando bien-->

<script> 
	$('.line').hide(); //acá escondemos todas las os que renderizó php dentro del popup
	$('.editar').hide();
	$('.hided').hide();
    
    $('.btn-deleterow').click(function() {  // acá le decimos que al clickear eliminar, le pase el id, que se generó también como clase CSS en los <p> del popup y lo muestre
    	var id = $(this).attr('id');
		$('.line.' + id).show();
    	$('#del').modal('show');
    	$('.eliminar').click(function() { //este y el cancelar vuelven a escondor todas ls os, sino quedan visibles, se puede pensar algo con esto para acciones masivas
    	$('.line').hide(); });
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

<script src="<?php echo base_url();?>/static/crei/js/crei.js"></script>