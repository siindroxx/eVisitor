<?php
include "../../sql/conexion.php";
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 2){
        header("location: ../../index.php"); // Redirecting To Home Page
    }
}
if(!isset($_GET['msg'])) {
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
}
$sql_visitante = "SELECT vd.ID as ID, vd.IDVisitante as IDVisitante, v.Nombre as Nombre, pa.Descripcion as PuntoAcceso, co.Descripcion as Origen, cto.Descripcion as Tipo,
                        vd.FechaHora as FechaHora
                        FROM visitante v
                        INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                        INNER JOIN punto_acceso pa on vd.IDPuntoAcceso = pa.ID 
                        INNER JOIN (
                          select IDVisitante, max(vd1.ID) maxid from visitante_detalle vd1  
                          INNER JOIN punto_acceso pa1 on vd1.IDPuntoAcceso = pa1.ID 
                          where IDTipoOrigen =  1 and pa1.IDOrigen = 1
                          GROUP by IDVisitante
                        ) c on v.ID = c.IDVisitante and c.maxid = vd.ID and  pa.IDOrigen = 1
                        INNER JOIN cat_origen co on pa.IDOrigen = co.ID
                        INNER JOIN cat_tipo_origen cto on vd.IDTipoOrigen = cto.ID
                        WHERE  v.EstatusCaseta = 1 and v.Activo = 1
                        UNION
                        SELECT vd.ID as ID, vd.IDVisitante as IDVisitante, v.Nombre as Nombre, pa.Descripcion as PuntoAcceso, co.Descripcion as Origen, cto.Descripcion as Tipo,
                        vd.FechaHora as FechaHora
                        FROM visitante v
                        INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                        INNER JOIN punto_acceso pa on vd.IDPuntoAcceso = pa.ID 
                        INNER JOIN (
                          select IDVisitante, max(vd1.ID) maxid from visitante_detalle vd1  
                          INNER JOIN punto_acceso pa1 on vd1.IDPuntoAcceso = pa1.ID 
                          where IDTipoOrigen =  1 and pa1.IDOrigen = 2
                          GROUP by IDVisitante
                        ) c on v.ID = c.IDVisitante and c.maxid = vd.ID and  pa.IDOrigen = 2
                        INNER JOIN cat_origen co on pa.IDOrigen = co.ID
                        INNER JOIN cat_tipo_origen cto on vd.IDTipoOrigen = cto.ID
                        WHERE v.EstatusCaseta = 0 AND v.EstatusRecepcion = 1 and v.Activo = 1"; //DATOS DE VISITANTES ADMITIDOS
$result_visitante = mysqli_query($conn, $sql_visitante);
if(mysqli_num_rows($result_visitante) == 0){
    $visitante = 0;
}else {
    while ($row_visitante = mysqli_fetch_assoc($result_visitante)) {
        $array_visitante = array(
            'ID' => $row_visitante['ID'],
            'IDVisitante' => $row_visitante['IDVisitante'],
            'Nombre' => $row_visitante['Nombre'],
            'PuntoAcceso' => $row_visitante['PuntoAcceso'],
            'Origen' => $row_visitante['Origen'],
            'FechaHora' => $row_visitante['FechaHora']
        );
        $visitante[$row_visitante['ID']] = $array_visitante;
    }
}

if(isset($_GET['id'])){
    if(isset($_SESSION['message'])){
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
    $IDVisitante = $_GET['id'];
    $puntoacceso = $_SESSION['puntoacceso'];
    $fecha = date('Y/m/d H:i:s', time());
    $sql_insert = "INSERT INTO visitante_detalle (IDVIsitante, IDPuntoAcceso, IDTipoOrigen, FechaHora) VALUES ('$IDVisitante', '$puntoacceso', 2, '$fecha')";
    if(!mysqli_query($conn, $sql_insert)){
        $_SESSION['message'] = 'error';
        $_SESSION['messagecontent'] = 'Error al actualizar visitante';
        ?>
        <script>
            window.location.href = "registroSalida.php?msg=error"
        </script>
        <?php
    }
    else{
        $sql_update = "UPDATE visitante SET EstatusCaseta = 0, EstatusRecepcion = 0, CodigoNFC = 0 WHERE ID = '$IDVisitante'"; //ACTUALIZA EL VISITANTE, REGISTRA SALIDA
        if(!mysqli_query($conn, $sql_update)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al actualizar visitante';
            ?>
            <script>
                window.location.href = "registroSalida.php?msg=error"
            </script>
            <?php
        }
        else{
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Salida registrada exitosamente';
            ?>
            <script>
                window.location.href = "registroSalida.php?msg=success"
            </script>
            <?php
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../scripts/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../../css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../../JS/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>

        </ul>
        <!--<div class="social_media">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>-->
    </div>
    <div class="main_content">
        <div class="header">
            <div style="float: left">
                <h4>Registrar Salida</h4>
            </div>
            <div style="float: right" class="logout">
                <h3><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <!--MENSAJES-->
        <?php if(isset($_SESSION['messagecontent'])){ ?>
            <div class="alert hide">
                <i class="fas fa-exclamation-circle"></i>
                <span class="msg"><?php echo $_SESSION['messagecontent'] ?></span>
                <div class="close-btn">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="alertsuccess hide">
                <i class="fas fa-check-circle"></i>
                <span class="msg"><?php echo $_SESSION['messagecontent'] ?></span>
                <div class="close-btn">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        <?php } ?>
        <!--FIN DE MENSAJES-->
        <table id="example" class="display">
            <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Fecha/Hora Entrada</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
                if($visitante != 0){
                    $col = array_column($visitante, 'ID');
                    array_multisort($col, SORT_ASC, $visitante);
                    foreach ($visitante as $row){
                        $fecha = date_create($row['FechaHora']);
                        $printdate = date_format($fecha, "d-m-Y h:i:s A");
                        ?>
                        <tr>
                            <td > <?php echo $row['Nombre'] ?> </td>
                            <td><?php echo $printdate?></td>
                            <td>
                                <a href="registroSalida.php?id=<?php echo $row['IDVisitante']?>"><button type="button" class="button button2"><i class="fas fa-door-open"></i></button></a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../../JS/table.js"></script>
<?php
if(isset($_SESSION['message'])){
if($_SESSION['message'] == 'error'){ ?>
    <script>
        $('.alert').addClass("show");
        $('.alert').removeClass("hide");
        $('.alert').addClass("showAlert");
        setTimeout(function(){
            $('.alert').removeClass("show");
            $('.alert').addClass("hide");
        },5000);
        $('.close-btn').click(function(){
            $('.alert').removeClass("show");
            $('.alert').addClass("hide");
        });
    </script>
<?php }
if($_SESSION['message'] == 'success'){
?>
    <script>
        $('.alertsuccess').addClass("show");
        $('.alertsuccess').removeClass("hide");
        $('.alertsuccess').addClass("showAlert");
        setTimeout(function(){
            $('.alertsuccess').removeClass("show");
            $('.alertsuccess').addClass("hide");
        },5000);
        $('.close-btn').click(function(){
            $('.alertsuccess').removeClass("show");
            $('.alertsuccess').addClass("hide");
        });
    </script>
<?php }
}?>
</body>
</html>