<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$entidad = isset($entidad) ? $entidad : '';
$tabla = isset($tabla) ? $tabla : 'tabla-papelera';
?>
<div class="papelera-toolbar well well-sm" style="margin-bottom: 12px;" data-entidad="<?php echo htmlspecialchars($entidad, ENT_QUOTES, 'UTF-8'); ?>" data-tabla="#<?php echo htmlspecialchars($tabla, ENT_QUOTES, 'UTF-8'); ?>">
	<button type="button" class="btn btn-success btn-sm btn-papelera-restaurar" disabled>
		<i class="glyphicon glyphicon-repeat"></i>
		Restaurar (<span class="papelera-cantidad">0</span>)
	</button>
	<button type="button" class="btn btn-danger btn-sm btn-papelera-eliminar" disabled>
		<i class="glyphicon glyphicon-remove"></i>
		Eliminar definitivamente (<span class="papelera-cantidad">0</span>)
	</button>
</div>

<div class="modal fade" id="modal-papelera-restaurar" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
				<h4 class="modal-title">Restaurar registros</h4>
			</div>
			<div class="modal-body">
				<p>¿Restaurar <strong><span class="papelera-cantidad-modal">0</span></strong> registro(s) al listado activo?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success btn-papelera-confirmar" data-accion="restaurar">Restaurar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-papelera-eliminar" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
				<h4 class="modal-title">Eliminar definitivamente</h4>
			</div>
			<div class="modal-body">
				<p>¿Eliminar <strong><span class="papelera-cantidad-modal">0</span></strong> registro(s) de forma permanente? Esta acción no se puede deshacer.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger btn-papelera-confirmar" data-accion="eliminar_definitivo">Eliminar definitivamente</button>
			</div>
		</div>
	</div>
</div>

<?php echo form_open(site_url('administracion/papelera_accion/' . $entidad), array('id' => 'form-papelera', 'style' => 'display:none;', 'method' => 'post')); ?>
<input type="hidden" name="accion" id="papelera-accion-input" value="">
<?php echo form_close(); ?>

<script src="<?php echo base_url(); ?>static/crei/js/papelera.js"></script>
