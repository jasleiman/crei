<?php setlocale(LC_ALL,"es_AR"); 
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$meses_corto = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");?>
<div class="container">
    <div class="page-header">
        <h1 id="timeline"><?php echo $actividades[0]->alumno .' - '.$meses[date('n',strtotime($actividades[0]->fecha_inicio))-1] .' '.date('Y',strtotime($actividades[0]->fecha_inicio))  ?></h1>
    </div>
    <ul class="timeline">
    	<?php $i=0; foreach ($actividades as $actividad) {
    		
    		if ($i%2==0) {
				echo '<li >';
			}else{
				echo '<li class="timeline-inverted">';
			}
    	?>
        
          <div class="timeline-badge">
	        <span class="timeline-balloon-date-day"><?php echo date('d',strtotime($actividad->fecha_inicio));  ?></span>
	        <span class="timeline-balloon-date-month"><?php echo $meses_corto[date('n',strtotime($actividad->fecha_inicio))-1];  ?></span>
	      </div>
          <div class="timeline-panel">
            <div class="timeline-heading">
              <h4 class="timeline-title"><?php echo $actividad->persona .' - ' .$actividad -> tipo_actividades ?></h4>
              <p><small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?php echo $actividad -> horas; ?> Horas</small></p>
            </div>
            <div class="timeline-body">
              <p><?php echo $actividad->observaciones; ?></p>
            </div>
          </div>
        </li>
        <?php 
			$i++;
		} ?>
    
      
    </ul>
</div>