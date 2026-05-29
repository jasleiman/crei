/**
 * Papelera: restaurar o eliminar definitivamente registros con baja lógica.
 */
(function ($) {
	'use strict';

	var $tablaActual = null;
	var entidadActual = null;
	var accionModal = null;

	function filasCheckboxes($tabla) {
		return $tabla.find('tbody .papelera-fila');
	}

	function actualizarToolbar($tabla) {
		var $toolbar = $('.papelera-toolbar').first();
		var n = filasCheckboxes($tabla).filter(':checked').length;
		$toolbar.find('.papelera-cantidad').text(n);
		$toolbar.find('.btn-papelera-restaurar, .btn-papelera-eliminar').prop('disabled', n === 0);

		var total = filasCheckboxes($tabla).length;
		var th = $tabla.find('thead .papelera-th-todos');
		th.prop('checked', total > 0 && n === total);
		th.prop('indeterminate', n > 0 && n < total);
	}

	$(document).on('change', '.papelera-th-todos', function () {
		var $tabla = $(this).closest('table');
		filasCheckboxes($tabla).prop('checked', $(this).is(':checked'));
		actualizarToolbar($tabla);
	});

	$(document).on('change', '.papelera-fila', function () {
		actualizarToolbar($(this).closest('table'));
	});

	$(document).on('click', '.btn-papelera-restaurar', function () {
		$tablaActual = $($(this).closest('.papelera-toolbar').data('tabla')).first();
		entidadActual = $(this).closest('.papelera-toolbar').data('entidad');
		accionModal = 'restaurar';
		var n = filasCheckboxes($tablaActual).filter(':checked').length;
		if (n === 0) {
			return;
		}
		$('#modal-papelera-restaurar .papelera-cantidad-modal').text(n);
		$('#modal-papelera-restaurar').modal('show');
	});

	$(document).on('click', '.btn-papelera-eliminar', function () {
		$tablaActual = $($(this).closest('.papelera-toolbar').data('tabla')).first();
		entidadActual = $(this).closest('.papelera-toolbar').data('entidad');
		accionModal = 'eliminar_definitivo';
		var n = filasCheckboxes($tablaActual).filter(':checked').length;
		if (n === 0) {
			return;
		}
		$('#modal-papelera-eliminar .papelera-cantidad-modal').text(n);
		$('#modal-papelera-eliminar').modal('show');
	});

	$(document).on('click', '.btn-papelera-confirmar', function () {
		if (!$tablaActual) {
			return;
		}
		var accion = $(this).data('accion') || accionModal;
		var $form = $('#form-papelera');
		$form.find('input[name="ids[]"]').remove();
		$('#papelera-accion-input').val(accion);

		filasCheckboxes($tablaActual).filter(':checked').each(function () {
			$('<input>', { type: 'hidden', name: 'ids[]', value: $(this).val() }).appendTo($form);
		});

		$form.submit();
	});

	$(document).ready(function () {
		$('table.tabla-papelera').each(function () {
			actualizarToolbar($(this));
		});
	});
})(jQuery);
