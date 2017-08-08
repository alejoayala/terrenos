<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function restafechas($fecha_i,$fecha_f){
	$dias = (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias = abs($dias); $dias = floor($dias);
	if($dias < 1) {
		$dias = 1;
	}
	return $dias;
}
function agregardiasfecha($fecha_i,$dias){
	$nuevafecha = strtotime ( '+'.$dias.' day' , strtotime ( $fecha_i ) ) ;
	$nuevafecha = date ( 'd-m-Y' , $nuevafecha );
	return $nuevafecha;
}
function minutos_transcurridos($fecha_i,$fecha_f) {
	$minutos = (strtotime($fecha_i)-strtotime($fecha_f))/60;
	$minutos = abs($minutos); $minutos = floor($minutos);
	return $minutos;
}
function get_rangofechas($start, $end,$onlyDate = FALSE) {
	//var_dump($end);
    $range = array();
    if (is_string($start) === true) $start = strtotime($start);
    if (is_string($end) === true ) $end = strtotime($end);
    //if ($start > $end) return createDateRangeArray($end, $start);
    do {
    	if($onlyDate) {
    		$range[] = date('Y-m-d', $start);
        	$start = strtotime("+ 1 day", $start);
    	}else{
    		$range[] = date('Y-m-d H:i:s', $start);
        	$start = strtotime("+ 1 day", $start);
    	}
        
    } while($start <= $end);
    if(count($range) < 1) {
    	if($onlyDate) { $range[] = date('Y-m-d'); }
    	else{ $range[] = date('Y-m-d H:i:s'); }
    }
    return $range;
}
function get_dias_transcurridos($start, $end) {
	//var_dump($start,$end); exit();
	$diasTranscurridos = 0;
    if (is_string($start) === true) $start = strtotime($start);
    if (is_string($end) === true ) $end = strtotime($end);
    //if ($start > $end) return createDateRangeArray($end, $start);
    do {
    	$diasTranscurridos++;
    	$start = strtotime("+ 1 day", $start);
    } while($start <= $end);
    if($diasTranscurridos < 1) {
    	return false;
    }
    return $diasTranscurridos;
}
function get_rangohoras($start, $end) {
	//var_dump($end);
    $range = array();
    if (is_string($start) === true) {
    	$start = strtotime($start);
    }
    if (is_string($end) === true ) {
    	$end = strtotime($end);
    }
    //if ($start > $end) return createDateRangeArray($end, $start);
    do {
        $range[] = date('H:i:s', $start);
        $start = strtotime("+ 1 hour", $start);
    } while($start <= $end);
    return $range;
}
function get_rangomeses($start, $end, $format = 1) {
    $range = array();
    if (is_string($start) === true) $start = strtotime($start);
    if (is_string($end) === true ) $end = strtotime($end);
    do {
    	if($format == 1){
    		$range[] = darFormatoMesAno(date('Y-m', $start));
    	}else{
    		$range[] = date('Y-m', $start);
    	}
        $start = strtotime("+ 1 month", $start);
    } while($start <= $end);
    return $range;
}
function darFormatoDMY($fecha)
{
	$fechaUT = strtotime($fecha); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT);
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$result = $d."-".$m."-".$y;
	return $result;
}
function darFormatoHora($fecha)
{
	$fechaUT = strtotime($fecha); // obtengo una fecha UNIX ( integer )
	$hr	= date('h', $fechaUT);
	$min= date('i', $fechaUT);
	$a= date('a', $fechaUT);
	$result = $hr.":".$min." ".$a;
	return $result;
}
function darFormatoFecha($fechaSQL){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT);
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$result = $d." de ".$longMonthArray[$m]." de ".$y;
	return $result;
}
function darFormatoMesAno($fechaSQL)
{
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$result = $longMonthArray[$m]."-".$y;
	return $result;
}
function darFormatoFechaYHora($fechaSQL){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT);
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$hr	= date('h', $fechaUT);
	$min= date('i', $fechaUT);
	$a= date('a', $fechaUT);
	$result = $d." de ".$longMonthArray[$m]." a las ".$hr.":".$min." ".$a;
	return $result; // 01 de Junio a las 12:00 am 
}
function formatoFechaReporte($fechaSQL){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	if ($fechaSQL == 0) return "";
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT); //obtiene los dias en formato 1 - 31
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$hr	= date('h', $fechaUT);
	$min= date('i', $fechaUT);
	$a= date('a', $fechaUT);
		$result = $d." ".$shortMonthArray[$m]." ".$y." - ".$hr.":".$min." ".$a;
	return $result;
}
function formatoFechaReporte2($fechaSQL){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	if ($fechaSQL == 0) return "";
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT); //obtiene los dias en formato 1 - 31
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$hr	= date('h', $fechaUT);
	$min= date('i', $fechaUT);
	$a= date('a', $fechaUT);
	$result = $d.$shortMonthArray[$m]." ".$hr.":".$min.$a;
	return $result; // 01Jun 12:00am 
}
function devolverEdad($fechaNacimiento){
	$startDate = $fechaNacimiento;
	$endDate = date('Y-m-d');
	list($year, $month, $day) = explode('-', $startDate);
	$startDate = mktime(0, 0, 0, $month, $day, $year);
	list($year, $month, $day) = explode('-', $endDate);
	$endDate = mktime(0, 0, 0, $month, $day, $year);
	$edad = (int)(($endDate - $startDate)/(60 * 60 * 24 * 365));
	return $edad;
}
function devolverEdadDetalle($fechaNacimiento)
{
    $localtime = getdate();
    $today = $localtime['mday']."-".$localtime['mon']."-".$localtime['year'];
    $dob_a = explode("-", date('d-m-Y',strtotime("$fechaNacimiento")));
    $today_a = explode("-", $today);
    $dob_d = $dob_a[0];$dob_m = $dob_a[1];$dob_y = $dob_a[2];
    $today_d = $today_a[0];$today_m = $today_a[1];$today_y = $today_a[2];
    $years = $today_y - $dob_y;
    $months = $today_m - $dob_m;
    if ($today_m.$today_d < $dob_m.$dob_d) 
    {
        $years--;
        $months = 12 + $today_m - $dob_m;
    }

    if ($today_d < $dob_d) 
    {
        $months--;
    }

    $firstMonths=array(1,3,5,7,8,10,12);
    $secondMonths=array(4,6,9,11);
    $thirdMonths=array(2);

    if($today_m - $dob_m == 1) 
    {
        if(in_array($dob_m, $firstMonths)) 
        {
            array_push($firstMonths, 0);
        }
        elseif(in_array($dob_m, $secondMonths)) 
        {
            array_push($secondMonths, 0);
        }elseif(in_array($dob_m, $thirdMonths)) 
        {
            array_push($thirdMonths, 0);
        }
    }
    return "$years años, $months meses.";
}
function darFechaCumple($fechaNacimiento){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	$fechaUT = strtotime($fechaNacimiento); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT);
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$result = $d." de ".$longMonthArray[$m];
	return $result; // 04 de Junio 
}
function formatoSimpleFecha($fechaSQL){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	if ($fechaSQL == 0) return "";
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$d	= date('d', $fechaUT);
	$m	= (int)date('m', $fechaUT);
	$y	= date('Y', $fechaUT);
	$result = $d." ".$shortMonthArray[$m];
	return $result; // 01 Jun 
}
function formatoConDia($fechaSQL){
	$longMonthArray = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
	$shortMonthArray = array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
	$shortDayArray = array("","Lun","Mar","Mie","Jue","Vie","Sab","Dom");
	if ($fechaSQL == 0) return "";
	$fechaUT = strtotime($fechaSQL); // obtengo una fecha UNIX ( integer )
	$D	= date('j', $fechaUT); //obtiene los dias en formato 1 - 7
	$d	= date('N', $fechaUT); //obtiene los dias en formato 1 - 31
	$m	= (int)date('m', $fechaUT);
	$day = $shortDayArray[$d];
	$month = $shortMonthArray[$m];
	$result = $day." ".$D." ".$month;
	return $result; // Jue 4 Jun 
}
?>