<?php

//DATOS DE FECHA
function calculatiempo ($fechainicio, $horainicio, $fechafin, $horafin, $recur, $conn)
{
    //OBTIENE VALORES DE TIEMPO DE GRACIA
    $sql_tiempo = "SELECT * FROM tiempo_gracia";
    $result_tiempo = mysqli_query($conn, $sql_tiempo);
    if (mysqli_num_rows($result_tiempo) != 0) {
        while ($row_tiempo = mysqli_fetch_assoc($result_tiempo)) {
            $array = array(
                "ID" => $row_tiempo['ID'],
                "Dias" => $row_tiempo['Dias'],
                "Horas" => $row_tiempo['Horas'],
                "Minutos" => $row_tiempo['Minutos']
            );
            $array_tiempo[$row_tiempo['ID']] = $array;
        }
        $fechahora = $fechainicio . " " . $horainicio; //CONCATENA FECHA Y HORA INICIO
        $fechahorafin = $fechafin. " " . $horafin; //CONCATENA FECHA Y HORA FIN
        $date = date_create($fechahora); //VARIABLE FECHA 1 USADA PARA TIEMPO GRACIA
        $date2 = date_create($fechahora); //VARIABLE FECHA 2 USADA PARA TIEMPO LLEGADA
        $date3= date_create($fechahorafin); //VARIABLE FECHA 3 USADA PARA TIEMPO FIN - SOLO SE USA CUANDO ES VISITA RECURRENTE
        $check = 0;
        foreach ($array_tiempo as $values) {
            if ($values['ID'] == 1) { //VALORES DE TIEMPO DE GRACIA
                $dias = $values['Dias'] . " days";
                $horas = $values['Horas'] . " hours";
                $min = $values['Minutos'] . " minutes";
                $check = $check + 1; //REVISA QUE PASE POR AQUI
            } else {
                if ($values['ID'] == 2) {//VALORES DE TIEMPO DE LLEGADA
                    $dias2 = $values['Dias'] . " days";
                    $horas2 = $values['Horas'] . " hours";
                    $min2 = $values['Minutos'] . " minutes";
                    $check = $check + 1; //REVISA QUE PASE POR AQUI
                }
            }
        }
        if ($check == 2) {
            //AGREGAR TIEMPO DE GRACIA
            $date = date_add($date, date_interval_create_from_date_string($dias));
            $date = date_add($date, date_interval_create_from_date_string($horas));
            $date = date_add($date, date_interval_create_from_date_string($min));

            //RESTAR TIEMPO DE LLEGADA
            $date2 = date_add($date2, date_interval_create_from_date_string($dias2));
            $date2 = date_add($date2, date_interval_create_from_date_string($horas2));
            $date2 = date_sub($date2, date_interval_create_from_date_string($min2));

            //FECHA ACTUAL
            $fecha = date("d-m-Y h:i:s A", time());
            $fecha2 = date_create($fecha);


            //CHECAR CONDICIONES: LA FECHA ACTUAL ES: (MAYOR O IGUAL AL TIEMPO DE LLEGADA) Y (MENOR O IGUAL AL TIEMPO DE GRACIA)
            if($recur == 0) {
                if ($fecha2 >= $date2 && $fecha2 <= $date) {
                    $result = 1;
                } else {
                    $printdate = date_format($date, "d-m-Y h:i:s A"); //HORA DE GRACIA
                    $printdate2 = date_format($date2, "d-m-Y h:i:s A"); //HORA DE LLEGADA
                    $tiempollegada = "Tiempo de llegada entre: " . $printdate2 . " y " . $printdate;
                    return $tiempollegada;
                }
            }
            if($recur == 1){
                if($fecha2 >= $date2){ //SI LA FECHAHORA ACTUAL ES MAYOR A LA FECHA DE LLEGADA
                    if($fecha2 < $date3) { //SI LA FECHAHORA ACTUAL ES MENOR A LA FECHAFIN
                        return 1;
                    }
                    else{
                        $printdate3 = date_format($date3, "d-m-Y h:i:s A"); //HORA FIN
                        $tiempollegada = "Visita finalizada. Fecha Fin: ". $printdate3;
                        return $tiempollegada;
                    }
                }
                else{
                    $printdate3 = date_format($date2, "d-m-Y h:i:s A"); //HORA FIN
                    $tiempollegada = "Tiempo de llegada despues de: ". $printdate3;
                    return $tiempollegada;

                }
            }
        }
        else {
            $result = 0; //NO HAY REGISTRO DE TIEMPO DE GRACIA O TIEMPO DE LLEGADA
        }
    }
    else{
        $result = 0; //NO HAY REGISTRO DE TIEMPO DE GRACIA O TIEMPO DE LLEGADA
    }

    return $result;
}