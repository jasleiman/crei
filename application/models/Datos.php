<?php
class Datos extends CI_Model {
		function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    
	function fechaphp2pg($fecha, $input_separator = 'auto') {
			if ($fecha=="") {return "";}
			else {
			if ($input_separator == 'auto') {
			if  ( substr_count($fecha, '-') )
				$input_separator = '-';
			else
				$input_separator = '/';
			}
			list ($dia, $mes, $anio) = explode ($input_separator, $fecha);
			return ( "$anio-$mes-$dia");
		}
	}
		
	function fechapg2php($fecha, $input_separator = 'auto') {
		if ($fecha=="") {return "";}
		else {
		if ($input_separator == 'auto') {
		if  ( substr_count($fecha, '-') )
			$input_separator = '-';
		else
			$input_separator = '/';
		}
		list ($anio, $mes, $dia) = explode ($input_separator, $fecha);
		return ( "$dia-$mes-$anio");
	}
	}
	
	function sumarHoras($initialHour, $finalHour) {
		if ($initialHour === null || $initialHour === '') {
			$initialHour = '0:0';
		}
		if ($finalHour === null || $finalHour === '') {
			$finalHour = '0:0';
		}
	    $day1 = explode(":", (string) $initialHour);
		$day2 = explode(":", (string) $finalHour);
		
		$totalmins = 0;
		$totalmins += $day1[0] * 60;
		if (isset($day1[1]))
		$totalmins += $day1[1];
		$totalmins += $day2[0] * 60;
		if (isset($day2[1]))
		$totalmins += $day2[1];
		
		$hours = intval($totalmins / 60);
		$minutes = $totalmins % 60;
		
		$totalhours = "$hours:$minutes";
		return $totalhours;
 	}
	
	function restarHoras($initialHour, $finalHour) {
	    $day1 = explode(":", $initialHour);
		if ($day1[0] == $initialHour) $day1[1]=0;
		$day2 = explode(":", $finalHour);
		
		$totalmins = 0;
		$totalmins2 = 0;
		$totalmins += $day1[0] * 60;
		if (isset($day1[1]))
		$totalmins += $day1[1];
		$totalmins2 += $day2[0] * 60;
		if (isset($day2[1]))
		$totalmins2 += $day2[1];
		
		$hours = intval(($totalmins2-$totalmins) / 60);
		$minutes = ($totalmins2-$totalmins) % 60;
		
		$totalhours = "$hours:$minutes";
		return $totalhours;
 	}

	function sumarHoras2($initialHour, $finalHour) {
	    $h = date('H', strtotime($finalHour));
	    $m = date('i', strtotime($finalHour));
	    
	    $tmp = $h." hour ".$m." min ";
	    $sumHour = $initialHour." + ".$tmp;
	    $newTime = date('H:i', strtotime($sumHour));
	    return $newTime;
 	}
	
	function getChildren($tabla,$id){
		$fin=false;
		$resul=Array();
		$ida=array($id);
		while ($fin == false){
			$idp='id_'.$tabla;
			$this -> db -> SELECT($idp);
			$this -> db -> FROM($tabla);
			$this -> db -> where_in('id_padre', $ida); 
			$this -> db -> where('habilitado', '1'); 
			$query = $this -> db -> get();
			
			if ($query -> num_rows() > 0) {
				$data=$query->result();
				unset($ida);
				$ida=array();
				foreach ($data as $ids) {
					$ida[]=$ids->$idp;
					$resul[]=$ids->$idp;
				}
			} else {
				$fin=true;
			}
		}
		
		if (sizeof($resul)==0) return false;
		else
		return $resul;
	}
	
	function getChildrenAlternativo($tabla,$id){
		$this -> db -> reset_query();
		$fin=false;
		$resul=Array();
		$ida=array($id);		
		$idp='id_'.$tabla;
		$i=0;
		while ($fin == false){
			$this -> db -> SELECT($tabla.'.'.$idp);
			$this -> db -> FROM($tabla);
			$this -> db -> JOIN ($tabla.'_padre',$tabla.'.id_'.$tabla.'='.$tabla.'_padre.id_'.$tabla,'left');
			$this -> db -> where_in($tabla.'_padre.id_padre', $ida); 
			$this -> db -> where('habilitado', '1'); 
			$query = $this -> db -> get();
			log_message('info', $this->db->last_query());
			if ($query -> num_rows() > 0) {
				$data=$query->result();
				unset($ida);
				$ida=array();
				foreach ($data as $ids) {
					$ida[]=$ids->$idp;
					$resul[]=$ids->$idp;
					
					
				}
			
			} else {
				$fin=true;
			}
			$i++;
			//if ($i==4) exit;
			
		}
		$resul[]=$id;
		if (sizeof($resul)==0) {return false;}
		else
		return $resul;
	}
}