    <!-- topbar ends -->
  
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
                    <h2><i class="glyphicon glyphicon-edit"></i>Administración de Obras Sociales</h2>
            
                    <div class="box-icon">
                       
                        <a href="#" class="btn btn-minimize btn-round btn-default"><i
                                class="glyphicon glyphicon-chevron-up"></i></a>
                       
                    </div>
                </div>
            	<div class="box-content">
                	
                   
                    <div class="control-group">
                        <label class="control-label" for="selectError">Datos de la Obra Social</label>
    
                        <div class="controls">
                            
                        </div>
                    </div>
                    <br/>
                   <div class="form-group">
                               <?php 
	$attributes = array('class' => 'form-horizontal');
	echo form_open( 'administracion/amobrassociales', $attributes ); ?>
    
    <?php echo form_fieldset( ); ?>

        <div class="input-group input-group-lg">
        	<span class="input-group-addon"><i class="glyphicon glyphicon-briefcase blue"></i></span>
            <?php 
			$data = array(
              'name'        => 'descripcion',
              'id'          => 'descripcion',
              'maxlength'   => '100',
              'size'        => '50',
			  'placeholder'	=> 'Razón Social',
			  'class'		=> 'form-control',
            );
			echo form_input( $data ); ?>

        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user blue"></i></span>
            <?php 
            $data = array(
              'name'        => 'contacto',
              'id'          => 'contacto',
              'maxlength'   => '100',
              'size'        => '50',
              'placeholder' => 'Contacto',
              'class'       => 'form-control',
            );
            echo form_input( $data ); ?>

        </div>
        <formspan></formspan>
		<div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>
            <?php 
				$data = array(
				  'name'        => 'email',
				  'id'          => 'email',
				  'maxlength'   => '100',
				  'size'        => '50',
                  'type'        => 'email',
				  'placeholder'	=> 'Correo',
				  'class'		=> 'form-control',
            	);
				echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-earphone blue"></i></span>
            <?php 
                $data = array(
                  'name'        => 'telefono',
                  'id'          => 'telefono',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Teléfono',
                  'class'       => 'form-control',
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
            <?php 
                $data = array(
                  'name'        => 'direccion',
                  'id'          => 'direccion',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Dirección',
                  'class'       => 'form-control',
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
            <?php 
                $data = array(
                  'name'        => 'localidad',
                  'id'          => 'localidad',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Localidad',
                  'class'       => 'form-control',
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
            <?php 
                $data = array(
                  'name'        => 'partido',
                  'id'          => 'partido',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Partido',
                  'class'       => 'form-control',
                );
                echo form_input( $data ); ?>
        </div>
        <formspan class="fuck"></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>
            <?php 
                $data = array(
                  'name'        => 'codigo_postal',
                  'id'          => 'codigo_postal',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Código Postal',
                  'class'       => 'form-control',
                );
                echo form_input( $data ); ?>
        </div>
        <formspan></formspan>
        <div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-globe blue"></i></span>
            <?php 
                $data = array(
                  'name'        => 'provincia',
                  'id'          => 'provincia',
                  'maxlength'   => '100',
                  'size'        => '50',
                  'placeholder' => 'Provincia',
                  'class'       => 'form-control',
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
				  'type'		=> 'submit',
				  'value'	    => 'Guardar',
				  'class'		=> 'btn btn-primary disabled',
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
    <script src="<?php echo base_url();?>/static/crei/js/crei.js"></script>
    