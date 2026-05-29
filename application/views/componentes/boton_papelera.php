<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$entidad = isset($entidad) ? $entidad : '';
?>
<a href="<?php echo site_url('administracion/papelera/' . $entidad); ?>" class="btn btn-default btn-sm" style="margin-left: 8px;">
	<i class="glyphicon glyphicon-trash"></i> Ver papelera
</a>
