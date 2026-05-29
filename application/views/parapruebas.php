<?php
$alumnosconos=0
$cd = "8";
foreach ($alumnos as $alumno) {
            if ($alumno->id_obras_sociales == $cd){
                    $alumnosconos++;
            }
var_dump($alumnosconos);


?>