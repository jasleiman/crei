<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$tipo = isset($tipo) ? $tipo : '';
$titulo = isset($titulo) ? $titulo : 'Importar desde Excel';
?>
<div class="well" style="margin-bottom: 15px; text-align: left;">
	<strong><?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></strong>
	<p class="text-muted" style="margin: 8px 0 12px;">
		Descargue la plantilla, complete los datos y súbala para revisar antes de confirmar.
	</p>
	<a class="btn btn-info btn-sm" href="<?php echo site_url('importaciones/plantilla/' . $tipo); ?>">
		<i class="glyphicon glyphicon-download-alt"></i> Descargar plantilla
	</a>
	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-import-<?php echo $tipo; ?>">
		<i class="glyphicon glyphicon-upload"></i> Importar Excel
	</button>
</div>

<div class="modal fade" id="modal-import-<?php echo $tipo; ?>" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Importar <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?></h4>
			</div>
			<?php echo form_open_multipart('importaciones/procesar/' . $tipo); ?>
			<div class="modal-body">
				<p>Archivo <strong>.xlsx</strong> o <strong>.xls</strong> (máx. 500 filas). Use la plantilla para los nombres de columnas.</p>
				<?php if ($tipo === 'maestras') { ?>
				<p class="text-muted"><small><strong>perfil</strong> = permisos de menú (Administrador, Equipo, Maestra integradora…). <strong>tipo_profesional</strong> = rol en el organigrama (Maestra integradora, Psicopedagoga, Directivo…); si queda vacío se asume Maestra integradora. Si el email ya existe como usuario activo, la fila <strong>actualiza</strong> persona, usuario, superiores y datos de contacto (la clave solo cambia si la indica en el Excel).</small></p>
				<p class="text-muted"><small><strong>Varios coordinadores:</strong> en <strong>coordinador</strong> sepárelos con <strong>;</strong> (emails o nombres de profesionales de <strong>clase Equipo</strong>: psicopedagoga, directivo, fonoaudiólogo, trabajadora social, etc.). Columnas <strong>coordinador2</strong> / <strong>coordinador3</strong> opcionales.</small></p>
				<?php } elseif ($tipo === 'alumnos') { ?>
				<p class="text-muted"><small>Columnas <strong>coordinador</strong> y <strong>maestra_integradora</strong>: un solo valor por fila (nombre o email válido del coordinador asignado al alumno).</small></p>
				<?php } ?>
				<input type="file" name="archivo" accept=".xlsx,.xls" required class="form-control">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary">Vista previa</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
