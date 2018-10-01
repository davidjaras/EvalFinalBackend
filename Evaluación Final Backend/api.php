<?php

/*Api para consultas de bienes raíces*/

$ciudad 	= $_GET["ciudad"];
$tipo 		= $_GET["tipo"];
$precio		= $_GET["precio"];
$mostrar 	= $_GET["mostrar"];

//Lectura de archivo .json con información de inmuebles
$jsonData = file_get_contents("data-1.json");

/*
 *condicional que determina que tipos de datos entregar según tipo de acción y consulta
 */

//Para mostrar todos los elementos 
if ($mostrar == "true") {
	echo $jsonData;
}
//Para completar los elementos select en la página
elseif ($mostrar == "select") {

	$ciudades = array();
	$tipos = array();

	$data = json_decode($jsonData);

	foreach ($data as $key => $value) {
		array_push($ciudades, $value->Ciudad);
		array_push($tipos, $value->Tipo);
	}//foreach

	$ciudades = array_unique($ciudades);
	$tipos = array_unique($tipos);

	echo json_encode(array('ciudades'=>$ciudades, 'tipos'=>$tipos), true);

}
//Entrega resultados según tipo de filtro
else{
	echo json_encode(resultadoFiltro($ciudad, $tipo, $precio));
}


//Función que define que elementos entregar de acuerdo a lo especificado en búsqueda personalizada
function resultadoFiltro($city, $type, $price){
	$resPriceFilter = priceFilter($price);
	$resCityFilter	= cityFilter($city, $resPriceFilter);
	$resTypeFilter	= typeFilter($type, $resCityFilter);
	return $resTypeFilter;
}//resultadoFiltro

//Función que filtra según tipo de inmueble especificado
function typeFilter($type, $arrayData){
	if ($type == "") {
		return $arrayData;
	} else {
		$resultado = array();

		foreach ($arrayData as $key => $value) {
			if ($type == $value->Tipo) {
				array_push($resultado, $value);
			}
		}//foreach
		return $resultado;
	}
}//typeFilter

//Función que filtra según ciudad especificada
function cityFilter($city, $arrayData){
	if ($city == "") {
		return $arrayData;
	} else {
		$resultado = array();

		foreach ($arrayData as $key => $value) {
			if ($city == $value->Ciudad) {
				array_push($resultado, $value);
			}
		}//foreach
		return $resultado;
	}
}//cityFilter

//Función que filtra según el precio del inmueble
function priceFilter($rango){

	global $jsonData;
	$data = json_decode($jsonData);
	$resultado = array();

	list($minPrice, $maxPrice) = explode(";",$rango);

	foreach ($data as $key => $value) {
		$precioAux = str_replace(",", "", substr($value->Precio, 1));
		if (($precioAux>=$minPrice)and($precioAux<=$maxPrice)) {
			array_push($resultado, $value);
		}
		
	}//foreach
	return $resultado;
}//priceFilter
