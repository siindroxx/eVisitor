<?php
//GENERA EL FORMATO DE FECHA LARGO EN ESPAÑOL
function formatofecha($fechavisita, $horainicio)
{

    $date = date_create($fechavisita);
    $dia = date_format($date, "d");
    $mes = date_format($date, "m");
    $anio = date_format($date, "Y");
    $diasem = date_format($date, "N");
    $hora = date_create($horainicio);
    $formhora = date_format($hora, "h:i:00 A");


    if ($mes == '01') {
        $meslargo = 'Enero';
    }
    if ($mes == '02') {
        $meslargo = 'Febrero';
    }
    if ($mes == '03') {
        $meslargo = 'Marzo';
    }
    if ($mes == '04') {
        $meslargo = 'Abril';
    }
    if ($mes == '05') {
        $meslargo = 'Mayo';
    }
    if ($mes == '06') {
        $meslargo = 'Junio';
    }
    if ($mes == '07') {
        $meslargo = 'Julio';
    }
    if ($mes == '08') {
        $meslargo = 'Agosto';
    }
    if ($mes == '09') {
        $meslargo = 'Septiembre';
    }
    if ($mes == '10') {
        $meslargo = 'Octubre';
    }
    if ($mes == '11') {
        $meslargo = 'Noviembre';
    }
    if ($mes == '12') {
        $meslargo = 'Diciembre';
    }

    if ($diasem == '1') {
        $diasemlargo = 'Lunes';
    }
    if ($diasem == '2') {
        $diasemlargo = 'Martes';
    }
    if ($diasem == '3') {
        $diasemlargo = 'Miércoles';
    }
    if ($diasem == '4') {
        $diasemlargo = 'Jueves';
    }
    if ($diasem == '5') {
        $diasemlargo = 'Viernes';
    }
    if ($diasem == '6') {
        $diasemlargo = 'Sábado';
    }
    if ($diasem == '7') {
        $diasemlargo = 'Domingo';
    }

    return "Fecha: " . $diasemlargo . " " . $dia . " de " . $meslargo . " del " . $anio . " Hora: " . $formhora;

}