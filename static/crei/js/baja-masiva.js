/**
 * Selección múltiple y eliminación masiva en listados de administración.
 */
(function ($) {
	'use strict';

	var entidadActual = null;
	var $tablaActual = null;

	function tablaDesdeTrigger($el) {
		var selector = $el.closest('.baja-masiva-toolbar').data('tabla');
		if (!selector) {
			selector = '#DataTables_Table_0';
		}
		return $(selector).first();
	}

	function filasCheckboxes($tabla) {
		return $tabla.find('tbody .baja-masiva-fila');
	}

	function actualizarToolbar($tabla) {
		var $toolbar = $('.baja-masiva-toolbar[data-tabla="' + $tabla.attr('id') + '"]');
		if (!$toolbar.length) {
			$toolbar = $('.baja-masiva-toolbar').first();
		}
		var $checks = filasCheckboxes($tabla).filter(':checked');
		var n = $checks.length;
		$toolbar.find('.baja-masiva-cantidad').text(n);
		$toolbar.find('.btn-baja-masiva-ejecutar').prop('disabled', n === 0);

		var total = filasCheckboxes($tabla).length;
		var th = $tabla.find('thead .baja-masiva-th-todos');
		th.prop('checked', total > 0 && n === total);
		th.prop('indeterminate', n > 0 && n < total);
	}

	$(document).on('change', '.baja-masiva-th-todos', function () {
		var $tabla = $(this).closest('table');
		var checked = $(this).is(':checked');
		filasCheckboxes($tabla).prop('checked', checked);
		actualizarToolbar($tabla);
	});

	$(document).on('change', '.baja-masiva-fila', function () {
		actualizarToolbar($(this).closest('table'));
	});

	$(document).on('click', '.btn-baja-masiva-ejecutar', function () {
		var $btn = $(this);
		$tablaActual = tablaDesdeTrigger($btn);
		entidadActual = $btn.data('entidad');
		var n = filasCheckboxes($tablaActual).filter(':checked').length;

		if (n === 0) {
			return;
		}

		$('#modal-baja-masiva .baja-masiva-cantidad-modal').text(n);
		$('#modal-baja-masiva').modal('show');
	});

	$(document).on('click', '.btn-baja-masiva-confirmar', function () {
		if (!$tablaActual || !entidadActual) {
			return;
		}

		var $form = $('#form-baja-masiva');
		$form.find('input[name="ids[]"]').remove();

		filasCheckboxes($tablaActual).filter(':checked').each(function () {
			$('<input>', { type: 'hidden', name: 'ids[]', value: $(this).val() }).appendTo($form);
		});

		$form.submit();
	});

	$(document).ready(function () {
		$('table.tabla-baja-masiva').each(function () {
			actualizarToolbar($(this));
		});
	});
})(jQuery);
