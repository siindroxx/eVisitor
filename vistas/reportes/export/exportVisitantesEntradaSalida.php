<?php

include('../../../sql/conexion.php');
if (!isset($_SERVER['HTTP_REFERER'])) {
    // REDIRIGIR A VENTA EN CASO QUE INGRESEN URL DIRECTAMENTE
    header('location: ../../,,.index.php');
    exit;
}
if (isset($_GET["export"])) {
    $idVisita = $_GET['idVisita'];
    $titulo = $_GET['titulo'];
    $sql_query = "select v.Nombre as visitante, pa.Descripcion as PuntoAcceso, co.Descripcion as TipoPuntoAcceso,
                        cto.Descripcion as tipo, vd.FechaHora
                        from visitante_detalle vd
                        inner join visitante v on v.ID = vd.IDVisitante
                        inner join punto_acceso pa on pa.ID = vd.IDPuntoAcceso
                        inner join cat_tipo_origen cto on cto.ID = vd.IDTipoOrigen
                        inner join cat_origen co on pa.IDOrigen = co.ID
                        WHERE v.activo = 1 and v.IDVisita = '$idVisita'";
    $result = mysqli_query($conn, $sql_query);
    if (mysqli_num_rows($result) == 0) {
        echo 'error';
    } else {
        header('Content-Type: text/csv; charset=utf-8');
        $archivo = 'Content-Disposition: attachment; filename=EntradaSalidaVisita_'. $titulo . '.csv';
        header($archivo);
        $output = fopen("php://output", "w");
        fputcsv($output, array('Visitante', 'PuntoAcceso', 'TipoPuntoAcceso', 'Tipo', 'FechaHora'));
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
    }
}