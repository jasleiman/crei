	<div class="navbar navbar-default" role="navigation">
        <div class="navbar-inner">
            <button type="button" class="navbar-toggle pull-left animated flip menu-celular">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="header-union" href="<?php echo base_url(); ?>index.php/home">
                <img alt="<?php echo $this->lang->line('profiler_empresa'); ?>" src="<?php echo base_url(); ?>static/img/logo.png" class="logo-empresa hidden-xs"></a>
            <a class="header-union header-escondido" href="<?php echo base_url(); ?>index.php/home" >
                <img alt="<?php echo $this->lang->line('profiler_empresa'); ?>" src="<?php echo base_url(); ?>static/img/logo.png" class="logo-empresa logo-escondido" ></a>

            <!-- user dropdown starts -->
            <div class="btn-group pull-right">
                <button class="btn btn-default dropdown-toggle menu-celular" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-user"></i><span class="hidden-sm hidden-xs"> <?php $login = $this->session->userdata("logged_in"); echo $login['persona']; ?></span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">

                    <li><a href="<?php echo site_url("login/logout");?>">Salir</a></li>
                </ul>
            </div>
            <!-- user dropdown ends -->
        </div>
    </div>