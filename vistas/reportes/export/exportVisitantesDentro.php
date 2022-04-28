<?php

include('../../../sql/conexion.php');
if (!isset($_SERVER['HTTP_REFERER'])) {
    // REDIRIGIR A VENTA EN CASO QUE INGRESEN URL DIRECTAMENTE
    header('location: ../../,,.index.php');
    exit;
}
if (isset($_GET["export"])) {
    $fecha = date("d-m-Y h:i:s A");
    $sql_query = "select v.ID, v.Nombre as Visitante, v.Email as EmailVisitante,
                    u.nombre as Anfitrion, u.Correo as EmailAnfitrion,
                    case when EstatusCaseta = 1 then 'Si' else 'No' end EntradaCaseta,
                    case when EstatusCaseta = 1 then c.FechaHora else 'N/A' end FechaCaseta,
                    case when EstatusRecepcion = 1 then 'Si' else 'No' end EntradaRecepcion,
                    case when EstatusRecepcion = 1 then d.FechaHora else 'N/A' end FechaRecepcion
                    from visitante v
                    inner join visita v2 on v.IDVisita = v2.ID
                    inner join usuario u on u.ID = v2.IDUsuario
                    left join (
                        select v.ID, vd.FechaHora from visitante v
                        inner join visitante_detalle vd on v.ID = vd.IDVisitante
                        INNER JOIN(
                            select IDVisitante, max(vd1.id) as maxid
                            from visitante_detalle vd1
                            inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                            where IDTipoOrigen =  1 and pa.IDOrigen = 1
                            GROUP BY IDVisitante
                        ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                        where v.EstatusCaseta = 1 and Activo = 1
                        ) c on c.ID = v.ID
                    left join (
                        select v.ID, vd.FechaHora from visitante v
                            inner join visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN(
                            select IDVisitante, max(vd1.id) as maxid
                            from visitante_detalle vd1
                            inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                            where IDTipoOrigen =  1 and pa.IDOrigen = 2
                            GROUP BY IDVisitante
                        ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                        where v.EstatusRecepcion = 1 and Activo = 1
                    ) d on d.ID = v.ID
                    where EstatusCaseta = 1 or EstatusRecepcion = 1 and v.Activo = 1";
    $result = mysqli_query($conn, $sql_query);
    if (mysqli_num_rows($result) == 0) {
        echo 'error';
    } else {
        header('Content-Type: text/csv; charset=utf-8');
        $archivo = 'Content-Disposition: attachment; filename=VisitantesDentro_'.$fecha.'.csv';
        header($archivo);
        $output = fopen("php://output", "w");
        fputcsv($output, array('Visiante', 'EmailVisitante', 'Anfitrion', 'EmailAnfitrion', 'EntradaCaseta', 'HoraCaseta', 'EntradaRecepcion', 'HoraRecepcion'));
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        fclose($output);
    }
}