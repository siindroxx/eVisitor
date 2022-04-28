<?php
include('../../../sql/conexion.php');
if(!isset($_SERVER['HTTP_REFERER'])){
    // REDIRIGIR A VENTA EN CASO QUE INGRESEN URL DIRECTAMENTE
    header('location: ../../,,.index.php');
    exit;
}
if(isset($_GET["export"])) {
    $sql_query = "SELECT pa.ID, Nombre, pa.Descripcion, co.Descripcion as Tipo FROM punto_acceso pa inner join cat_origen co on (co.ID = pa.IDOrigen) WHERE pa.Activo = 1";
    $result = mysqli_query($conn, $sql_query);
    if(mysqli_num_rows($result) == 0){
        echo 'error';
    }
    else {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=punto_acceso.csv');
        $output = fopen("php://output", "w");
        fputcsv($output, array('ID','Nombre', 'Descripcion', 'Tipo'));
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
    }
}
