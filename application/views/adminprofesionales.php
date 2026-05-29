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
					<h2><i class="glyphicon glyphicon-user"></i> Administración de tipo de profesionales</h2>
						<div class="box-icon">
							<a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
							<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
						</div>
				</div>
			<div class="box-content">
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
					<div class="row" style="text-align: right;">
						
							<div class="col-md-12">
								<a class="btn btn-success altaprofesionales" href="#"><i class="glyphicon glyphicon-plus-sign icon-white"></i> Nuevo tipo de profesional</a>
							</div>
					</div>
					<?php $this->load->view('componentes/baja_masiva', array('entidad' => 'profesionales')); ?>
						<div class="table-wrapper">
							<div class="scrollable">
								<table class="table table-striped table-bordered bootstrap-datatable datatable responsive dataTable tabla-baja-masiva" id="DataTables_Table_0" data-entidad="profesionales" aria-describedby="DataTables_Table_0_info">
									<thead>
										<tr role="row">
										<th style="width:36px;"><input type="checkbox" class="baja-masiva-th-todos" title="Seleccionar todos"></th>
										<th class="sorting_asc" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Username: activate to sort column descending" style="width: 196px;">Descripción</th>
										<th class="sorting" role="columnheader" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending" style="width: 286px;">Acciones</th>
										</tr>
									</thead>
			<!impresión de la tabla !>
			
			<?php 
					if (!isset($profesionales)) {
						echo "<h2>Lo siento, no se puede mostrar su consulta</h2>";
						//exit();
					}else{
						echo '<tbody role="alert" aria-live="polite" aria-relevant="all">';
						$i = 1;
						

							foreach ($profesionales as $profesional) {
							
								if ($i%2==0) {
									echo '<tr class="even actual">';
								}else{
									echo '<tr class="odd actual">';
								}

								
								echo '<td class="center"><input type="checkbox" class="baja-masiva-fila" value="'.$profesional->id_profesionales.'"></td>';
								echo '<td class=" sorting_1">'.$profesional->descripcion.'</td>';
								
								
								echo '<td class="center ">
										<a class="btn btn-info btn-editrow" id="'.$profesional->id_profesionales.'"  href="#">
										<i class="glyphicon glyphicon-edit icon-white"></i>
										Editar
										</a>
										<a class="btn btn-deleterow btn-danger" id="'.$profesional->id_profesionales.'" href="#">
										<i class="glyphicon glyphicon-trash icon-white"></i>
										Eliminar
										</a>
										
										</td>
										</tr>';
							$i++;
							}
							
						
					}

					 ?>


</tr></tbody></table></div><div class="pinned"><table class="table table-striped table-bordered bootstrap-datatable datatable dataTable" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">

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
                	

                		<p>Está a punto de eliminar el tipo de profesional <span class="nombre-plan"></span></p>
                	
                  
                </div>
                <div class="modal-footer">
                	
						<div>
                	<form action="<?php echo base_url().'index.php/administracion/bajaprofesionales';?>" class="form-horizontal" method="post" accept-charset="utf-8">
                    	<a href="#" class="btn btn-default cancelar" data-dismiss="modal">Cancelar</a>
						 <input type="hidden" id="id_profesionales" name="id_profesionales"  maxlength="100" size="50"  class="form-control hidden">
                    	<input type="submit" name="enviar" value="Eliminar" id="enviar" maxlength="100" size="50" class="btn btn-danger eliminar">
                    </form></div>
                </div>
            </div>
        </div>
    </div>
    
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

<script> 

    
    $('.btn-deleterow').click(function() {  // acá le decimos que al clickear eliminar, le pase el id, que se generó también como clase CSS en los <p> del popup y lo muestre
    	var id = $(this).attr('id');
    	
                   $(".nombre-plan").text($(this).closest('td').prev('td').prev('td').text());
                	$("#id_profesionales").val(id);
		$('.line.' + id).show();
    	$('#del').modal('show');
    	$('.eliminar').click(function() { 
    		
    		
    	
    	});
    });
    
    $(document).on('click','.btn-editrow', function (e) {
		
		e.preventDefault();
        $('#modalEditar').modal('show');
        var id = "id_profesionales="+$(this).attr('id');
		$('#formulario').html('<img src="<?php echo base_url();?>static/img/ajax-loader-1.gif">');
		$.ajax({
			type : "POST",
			url : "<?php echo site_url("administracion/altaprofesionales?uid="); ?>"+Math.random(),
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
	
	
	$(document).on('click','.altaprofesionales', function (e) {
		
		e.preventDefault();
        $('#modalEditar').modal('show');
        var id=0;
		$('#formulario').html('<img src="<?php echo base_url();?>static/img/ajax-loader-1.gif">');
		$.ajax({
			type : "POST",
			url : "<?php echo site_url("administracion/altaprofesionales?uid="); ?>"+Math.random(),
			data: id,
			success: function(data){
				
			 	$('#formulario').html(data);
			 	 $("#ui-datepicker-div").css("z-index", "9999");   
				
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
