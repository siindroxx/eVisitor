<?php
//GENERA EL CODIGO QR DE LA VISITA
include '../librerias/phpqrcode/qrlib.php';


function genqr($codigo){

    $path = '../QR/';

    if(!file_exists($path)){
        mkdir($path);
    }

    $text = $codigo;

    $file = $path.$text.".png";

    $tam = 10;
    $level = 'M';
    $frameSize = 3;
    $contenido = $text;

    QRcode::png($contenido, $file, $level, $tam, $frameSize);

    return $file;
}





