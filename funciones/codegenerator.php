<?php
//FUNCION PARA GENERAR UN CODIGO DE VISITA UNICO DE 6 CARACTERES
function generateRandomString($length, $conn) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    do {
        for ($i = 0; $i <= $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        //checar que el codigo no este usado
        $sql_check = "SELECT * FROM visita WHERE CodigoVisita = '$randomString'";
        $result_check = mysqli_query($conn, $sql_check);
    }while (mysqli_num_rows($result_check)!=0);
    return $randomString;
}
