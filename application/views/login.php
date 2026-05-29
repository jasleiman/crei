<body>

<div class="ch-container">
<div class="row">
	<div class="row">
        <div class="well col-md-5 center login-box">
        	<?php if (isset($_SESSION["error"])) { ?>
        	<div class="alert alert-danger">
                <?php echo $_SESSION["error"]; 
					unset($_SESSION["error"]);
                
                ?>
            </div>	
        	<?php } ?>
         	<div class="alert alert-info">
                Por favor ingrese con su usuario y contraseña
            </div>
           

<?php 
	$attributes = array('class' => 'form-horizontal');
	echo form_open( 'login/verificarlogin', $attributes ); ?>
    
    <?php echo form_fieldset( ); ?>

        <div class="input-group input-group-lg">
        	<span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
            
            
            <?php 
			$data = array(
              'name'        => 'username',
              'id'          => 'username',
              'maxlength'   => '100',
              'size'        => '50',
			  'placeholder'	=> 'Usuario',
			  'class'		=> 'form-control',
            );
			echo form_input( $data ); ?>
        </div>
		<div class="clearfix"></div><br>
        <div class="input-group input-group-lg">
        <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
            <?php 
				$data = array(
				  'name'        => 'clave',
				  'id'          => 'clave',
				  'maxlength'   => '100',
				  'size'        => '50',
				  'type'		=>	'password',
				  'placeholder'	=> 'Contraseña',
				  'class'		=> 'form-control',
            	);
				echo form_input( $data ); ?>
        </div>

        <p class="center col-md-5">
            <?php $data = array(
				  'name'        => 'enviar',
				  'id'          => 'enviar',
				  'maxlength'   => '100',
				  'size'        => '50',
				  'type'		=>	'submit',
				  'value'	=> 'Login',
				  'class'		=> 'btn btn-primary',
            	);
				echo form_input( $data ); ?>
        </div>

    <?php echo form_fieldset_close(); ?>
<?php echo form_close(); ?>
    </div><!--/row-->
</div><!--/fluid-row-->
</div><!--/.fluid-container--> 