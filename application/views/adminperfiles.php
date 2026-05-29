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
					<h2><i class="glyphicon glyphicon-user"></i> Administración de Perfiles</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
							<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
						</div>
				</div>
			<div class="box-content">
				<div class="row" style="text-align: right;">
						
							<div class="col-md-12">
								<a class="btn btn-success altaperfil" href="#"><i class="glyphicon glyphicon-plus-sign icon-white"></i> Nuevo Perfil</a>
							</div>
							<br>
					</div>
					<?php $this->load->view('componentes/baja_masiva', array('entidad' => 'perfiles')); ?>
				
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
					
						<div class="table-wrapper">
							<div class="scrollable">
								<table class="table table-striped table-bordered bootstrap-datatable datatable responsive dataTable tabla-baja-masiva" id="DataTables_Table_0" data-entidad="perfiles" aria-describedby="DataTables_Table_0_info">
									<thead>
										<tr role="row">
										<th style="width:36px;"><input type="checkbox" class="baja-masiva-th-todos" title="Seleccionar todos"></th>
										<th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ID: activate to sort column descending" style="width: 88px;">ID</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Descripción: activate to sort column ascending" style="width: 250px;">Descripción</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 374px;">Acciones</th>
										</tr>
									</thead>
			<!-- impresión de la tabla -->
			
			<?php 
					if (!isset($perfiles)) {
						echo "<h2>Lo siento, no se puede mostrar su consulta</h2>";
						exit();
					}else{
						echo '<tbody role="alert" aria-live="polite" aria-relevant="all">';
						$i = 1;
						

							foreach ($perfiles as $perfil) {
							
								if ($i%2==0) {
									echo '<tr class="even">';
								}else{
									echo '<tr class="odd">';
								}

								
								echo '<td class="center"><input type="checkbox" class="baja-masiva-fila" value="'.$perfil->id_perfiles.'"></td>';
								echo '<td class=" sorting_1">'.$perfil->id_perfiles.'</td>';
								echo '<td class="center ">'.$perfil->descripcion.'</td>';
								echo '<td class="center ">
										<a class="btn btn-primary btn-sm" href="#">
										<i class="glyphicon glyphicon-lock icon-white"></i>
										Asignar Menu
										</a>
										<a class="btn btn-info btn-sm" id="'.$perfil->id_perfiles.'"  href="#">
										<i class="glyphicon glyphicon-edit icon-white"></i>
										Editar
										</a>
										<a class="btn btn-deleterow btn-danger btn-sm" id="'.$perfil->id_perfiles.'" href="#">
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
                	<?php foreach ($perfiles as $perfil){

                		echo '<p class = "line '.$perfil->id_perfiles.'"> Está a punto de eliminar el Perfil <span>'.$perfil->descripcion.'</span>.</p>';
                	}
                  ?>
                </div>
                <div class="modal-footer">
                	<?php foreach ($perfiles as $perfil){
                	echo '
						<div class = "line '.$perfil->id_perfiles.'">
                	<form action="'.base_url().'index.php/administracion/bajaperfiles" class="form-horizontal" method="post" accept-charset="utf-8">
                    	<a href="#" class="btn btn-default cancelar" data-dismiss="modal">Cancelar</a>
						 <input type="text" id="id_perfiles" name="id_perfiles" value="'.$perfil->id_perfiles.'" maxlength="100" size="50" placeholder="'.$perfil->id_perfiles.'" class="form-control hided">
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
				      
				   	foreach ($perfiles as $perfil){
				      echo


				      	'<div class = "editar '.$perfil->id_perfiles.'">

				      	<form action="'.base_url().'index.php/administracion/amperfiles" class="form-horizontal " method="post" accept-charset="utf-8">

				        <fieldset>

						<div class="input-group  input-group-lg">
				        	
				            <input type="text" id="id_perfiles" name="id_perfiles" value="'.$perfil->id_perfiles.'" maxlength="100" size="50" placeholder="'.$perfil->id_perfiles.'" class="form-control hided">
				        </div>
				         <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-briefcase green"></i></span>
				            <input type="text" name="descripcion" value="'.$perfil->descripcion.'" id="descripcion" maxlength="100" size="50" placeholder="'.$perfil->descripcion.'" class="form-control">
				        </div>
				        <div class="clearfix"></div><br>
				        <p class="center col-md-5">
				            <input type="submit" name="enviar" value="Guardar" id="enviar" maxlength="100" size="50" class="btn btn-primary Guardar">
				            <formspan></formspan>
				        </p></fieldset></form>
				                </div> '; } ?>
                
                <div class="modal-footer">
                    	<a href="#" class="btn btn-default cancelar" data-dismiss="modal">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="modal fade" id="alta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h3>Nuevo Perfil</h3>
                </div>
                <div class="modal-body">
                       <div class = "alta">

                       <?php echo


				      	'<div class = "alta">

				      	<form action="'.base_url().'index.php/administracion/amperfiles" class="form-horizontal " method="post" accept-charset="utf-8">

				        <fieldset>

				         <div class="input-group input-group-lg">
				        	<span class="input-group-addon"><i class="glyphicon glyphicon-briefcase green"></i></span>
				            <input type="text" name="descripcion" value="descripcion" id="descripcion" maxlength="100" size="50" placeholder="descrpicion" class="form-control">
				        </div>
				        <div class="clearfix"></div><br>
				        <p class="center col-md-5">
				            <input type="submit" name="enviar" value="Guardar" id="enviar" maxlength="100" size="50" class="btn btn-primary Guardar">
				            <formspan></formspan>
				        </p></fieldset></form>
				                </div> '; ?>
                
                <div class="modal-footer">
                    	<a href="#" class="btn btn-default cancelar" data-dismiss="modal">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
  </div>





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

  	$('.btn-info').click(function() {  // acá le decimos que al clickear editar, le pase el id, que se generó también como clase CSS en los <p> del popup y lo muestre
    	var id = $(this).attr('id');
		$('.editar.' + id).show();
    	$('#edit').modal('show');
    });
    $('.Guardar').click(function() {
    	$('.line').hide();
    	$('.editar').hide();
    });
	
	$('.altaperfil').click(function() {  // acá le decimos que al clickear editar, le pase el id, que se generó también como clase CSS en los <p> del popup y lo muestre
		$('.alta').show();
    	$('#alta').modal('show');
    });
</script>

<script src="<?php echo base_url();?>/static/crei/js/crei.js"></script>