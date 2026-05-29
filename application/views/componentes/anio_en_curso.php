<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$anio_actual = date('Y');
?>
<label class="control-label" for="anio">Año</label>
<div class="input-group input-group-lg">
	<span class="input-group-addon"><i class="glyphicon glyphicon-calendar green"></i></span>
	<input type="text" class="form-control" id="anio" value="<?php echo (int) $anio_actual; ?>" readonly="readonly">
	<input type="hidden" name="anio" value="<?php echo (int) $anio_actual; ?>">
</div>
