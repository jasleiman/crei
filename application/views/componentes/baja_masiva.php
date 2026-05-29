<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$entidad = isset($entidad) ? $entidad : '';
$tabla = isset($tabla) ? $tabla : 'DataTables_Table_0';
?>
<div class="baja-masiva-toolbar well well-sm" style="margin-bottom: 12px;" data-entidad="<?php echo htmlspecialchars($entidad, ENT_QUOTES, 'UTF-8'); ?>" data-tabla="#<?php echo htmlspecialchars($tabla, ENT_QUOTES, 'UTF-8'); ?>">
	<button type="button" class="btn btn-danger btn-sm btn-baja-masiva-ejecutar" data-entidad="<?php echo htmlspecialchars($entidad, ENT_QUOTES, 'UTF-8'); ?>" disabled>
		<i class="glyphicon glyphicon-trash icon-white"></i>
		Eliminar seleccionados (<span class="baja-masiva-cantidad">0</span>)
	</button>
	<span class="text-muted" style="margin-left: 10px;">Marcá las filas con la casilla de la izquierda.</span>
</div>

<div class="modal fade" id="modal-baja-masiva" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Eliminar registros</h4>
			</div>
			<div class="modal-body">
				<p>¿Dar de baja <strong><span class="baja-masiva-cantidad-modal">0</span></strong> registro(s) seleccionado(s)? Pasarán a la papelera y podrán restaurarse o eliminarse definitivamente desde allí.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger btn-baja-masiva-confirmar">Eliminar</button>
			</div>
		</div>
	</div>
</div>

<?php echo form_open(site_url('administracion/bajamasiva/' . $entidad), array('id' => 'form-baja-masiva', 'style' => 'display:none;', 'method' => 'post')); ?>
<?php echo form_close(); ?>

<script src="<?php echo base_url(); ?>static/crei/js/baja-masiva.js"></script>
