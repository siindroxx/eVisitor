<?php
include ('../../sql/conexion.php');
include ('../../funciones/calculatiempo.php');

session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 2){
        header("location: ../../index.php"); // Redirecting To Home Page
    }
}
if(!isset($_SERVER['HTTP_REFERER'])){
    // REDIRIGIR A VENTA EN CASO QUE INGRESEN URL DIRECTAMENTE
    header('location: ../../main.php');
    exit;
}
if(!isset($_GET['msg'])) {
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
}
if(isset($_GET['QR'])){
    unset($_SESSION['visitante']);
    unset($_SESSION['detalle']);
    unset($_SESSION['salida']);
    $QRVisita = $_GET['QR']; //CODIGO DE LA VISITA
    //OBTENER DATOS DE LA VISITA
    $sql_visita = "SELECT * from visita where CodigoVisita = '$QRVisita' and Activo = 1"; //TABLA VISITA
    $result_visita = mysqli_query($conn, $sql_visita);
    if(mysqli_num_rows($result_visita) == 0){
        ?>
        <script>
            alert ("No se encontro ninguna visita con código <?php echo $QRVisita?>")
            window.location.href = "admitirVisita.php"
        </script>
        <?php
    }
    else {
        $row_visita = mysqli_fetch_array($result_visita);
        $revision = calculatiempo($row_visita['FechaVisita'], $row_visita['HoraInicio'], $row_visita['FechaFin'], $row_visita['HoraFin'], $row_visita['Recurrente'], $conn);
        if($revision == 1) {
            $IDVisita = $row_visita['ID']; //ID DE LA VISITA
            $IDProtocolo = $row_visita['IDProtocolo']; //ID DEL PROTOCOLO DE LA VISITA
            $IDUsuario = $row_visita['IDUsuario']; //ID DEL USUARIO CREADOR DE LA VISITA
            $recurrente = $row_visita['Recurrente'];
            //OBTIENE DATOS DE LOS VISITANTES
            $sql_visitante = "SELECT v.*, 'N/A' as IDDetalle FROM visitante v WHERE v.IDVisita = '$IDVisita'and v.EstatusCaseta = 0 and v.EstatusRecepcion = 0 and v.Activo = 1
                          UNION
                          SELECT v.*, vd.ID as IDDetalle FROM visitante v 
                          INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante                        
                          INNER JOIN ( select IDVisitante, max(vd1.ID) maxid 
                          FROM visitante_detalle vd1
                          INNER JOIN punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                          WHERE IDTipoOrigen = 1 and pa.IDOrigen = 1 GROUP by IDVisitante ) c on v.ID = c.IDVisitante and c.maxid = vd.ID 
                          WHERE v.IDVisita = '$IDVisita' and v.EstatusCaseta = 1 and v.Activo = 1
                          UNION
                          SELECT v.*, vd.ID as IDDetalle FROM visitante v 
                          INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante                        
                          INNER JOIN ( select IDVisitante, max(vd1.ID) maxid 
                          FROM visitante_detalle vd1
                          INNER JOIN punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                          WHERE IDTipoOrigen = 1 and pa.IDOrigen = 2 GROUP by IDVisitante ) c on v.ID = c.IDVisitante and c.maxid = vd.ID 
                          WHERE v.IDVisita = '$IDVisita' and v.EstatusCaseta = 0 and v.EstatusRecepcion = 1 and v.Activo = 1";
            $result_visitante = mysqli_query($conn, $sql_visitante); //DATOS DE VISITANTES SIN ADMITIR
            while ($row_visitante = mysqli_fetch_assoc($result_visitante)) {
                $array_visitante = array(
                    'ID' => $row_visitante["ID"],
                    'Nombre' => $row_visitante['Nombre'],
                    'Email' => $row_visitante['Email'],
                    'Compania' => $row_visitante ['Compania'],
                    'EstatusCaseta' => $row_visitante['EstatusCaseta'], //DEFINE SI VISITANTE ESTA ACTIVO CON ENTRADA EN CASETA
                    'EstatusRecepcion' => $row_visitante['EstatusRecepcion'], //DEFINE SI VISITANTE ESTA ACTIVO CON ENTRADA EN RECEPCION
                    'IDDetalle' => $row_visitante['IDDetalle']
                );
                $_SESSION['visitante'][$row_visitante["ID"]] = $array_visitante; //ARREGLO DATOS VISITANTES
            }
            //OBTIENE DETALLE DE VISITANTE
            if($recurrente == 1){ //PARA CUANDO ES VISITA RECURRENTE
                $sql_detalle = "SELECT vd.ID as ID, vd.IDVisitante as IDVisitante, pa.Descripcion as PuntoAcceso, co.Descripcion as Origen, cto.Descripcion as Tipo,
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
                            WHERE v.IDVisita = '$IDVisita' and v.EstatusCaseta = 1 and v.Activo = 1
                            UNION
                            SELECT vd.ID as ID, vd.IDVisitante as IDVisitante, pa.Descripcion as PuntoAcceso, co.Descripcion as Origen, cto.Descripcion as Tipo,
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
                            WHERE v.IDVisita = '$IDVisita' and v.EstatusCaseta = 0 AND v.EstatusRecepcion = 1 and v.Activo = 1";
                $result_detalle = mysqli_query($conn, $sql_detalle);
                if (mysqli_num_rows($result_detalle) != 0) {
                    while ($row_detalle = mysqli_fetch_assoc($result_detalle)) {
                        $array_detalle = array(
                            'ID' => $row_detalle['ID'],
                            'IDVisitante' => $row_detalle['IDVisitante'],
                            'PuntoAcceso' => $row_detalle['PuntoAcceso'], //PUNTO DE ACCESO DONDE FUE ENTRADA/SALIDA
                            'Origen' => $row_detalle['Origen'], //DEFINE SI ES CASETA O RECEPCION
                            'Tipo' => $row_detalle['Tipo'], //DEFINE SI FUE ENTRADA O SALIDA
                            'FechaHora' => $row_detalle['FechaHora'] //FECHA Y HORA ENTRADA O SALIDA
                        );
                        $_SESSION['detalle'][$row_detalle['ID']] = $array_detalle; //ARREGLO DETALLE VISITANTE
                    }
                    $detalle = 1;
                }
                else{
                    $detalle = 0;
                }
            }
            else{ //PARA CUANDO NO ES RECURRENTE
                //QUERY PARA FECHA CASETA
                $sql_detalle = "
                            SELECT vd.ID as ID, vd.IDVisitante as IDVisitante, pa.Descripcion as PuntoAcceso, co.Descripcion as Origen, cto.Descripcion as Tipo,
                            vd.FechaHora as FechaHora
                            FROM visitante v
                            INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN punto_acceso pa on vd.IDPuntoAcceso = pa.ID 
                            INNER JOIN (
                              select IDVisitante, max(vd1.ID) maxid from visitante_detalle vd1  
                              INNER JOIN punto_acceso pa1 on vd1.IDPuntoAcceso = pa1.ID 
                              where IDTipoOrigen =  1 and pa1.IDOrigen = 1
                              GROUP by IDVisitante
                            ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                            INNER JOIN cat_origen co on pa.IDOrigen = co.ID
                            INNER JOIN cat_tipo_origen cto on vd.IDTipoOrigen = cto.ID
                            WHERE v.IDVisita = '$IDVisita' and v.Activo = 1";
                $result_detalle = mysqli_query($conn, $sql_detalle);
                if (mysqli_num_rows($result_detalle) != 0) {
                    while ($row_detalle = mysqli_fetch_assoc($result_detalle)) {
                        $array_detalle = array(
                            'ID' => $row_detalle['ID'],
                            'IDVisitante' => $row_detalle['IDVisitante'],
                            'PuntoAcceso' => $row_detalle['PuntoAcceso'], //PUNTO DE ACCESO DONDE FUE ENTRADA/SALIDA
                            'Origen' => $row_detalle['Origen'], //DEFINE SI ES CASETA O RECEPCION
                            'Tipo' => $row_detalle['Tipo'], //DEFINE SI FUE ENTRADA O SALIDA
                            'FechaHora' => $row_detalle['FechaHora'] //FECHA Y HORA ENTRADA O SALIDA
                        );
                        $_SESSION['detalle'][$row_detalle['ID']] = $array_detalle; //ARREGLO DETALLE VISITANTE
                        $_SESSION['visitante'][$row_detalle['IDVisitante']]['IDDetalle'] = $row_detalle['ID'];
                    }
                    $item_array_detalle = array_column($_SESSION['detalle'], 'IDVisitante');
                    $detalle = 1;
                }
                else{
                    $detalle = 0;
                }
                //QUERY PARA FECHA RECEPCION
                $sql_detalle = "
                            SELECT vd.ID as ID, vd.IDVisitante as IDVisitante, pa.Descripcion as PuntoAcceso, co.Descripcion as Origen, cto.Descripcion as Tipo,
                            vd.FechaHora as FechaHora
                            FROM visitante v
                            INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN punto_acceso pa on vd.IDPuntoAcceso = pa.ID 
                            INNER JOIN (
                              select IDVisitante, max(vd1.ID) maxid from visitante_detalle vd1  
                              INNER JOIN punto_acceso pa1 on vd1.IDPuntoAcceso = pa1.ID 
                              where IDTipoOrigen =  1 and pa1.IDOrigen = 2
                              GROUP by IDVisitante
                            ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                            INNER JOIN cat_origen co on pa.IDOrigen = co.ID
                            INNER JOIN cat_tipo_origen cto on vd.IDTipoOrigen = cto.ID
                            WHERE v.IDVisita = '$IDVisita' and v.Activo = 1";
                $result_detalle = mysqli_query($conn, $sql_detalle);
                if (mysqli_num_rows($result_detalle) != 0) {
                    while ($row_detalle = mysqli_fetch_assoc($result_detalle)) {
                        if($detalle == 0){
                            $array_detalle = array(
                                'ID' => $row_detalle['ID'],
                                'IDVisitante' => $row_detalle['IDVisitante'],
                                'PuntoAcceso' => $row_detalle['PuntoAcceso'], //PUNTO DE ACCESO DONDE FUE ENTRADA/SALIDA
                                'Origen' => $row_detalle['Origen'], //DEFINE SI ES CASETA O RECEPCION
                                'Tipo' => $row_detalle['Tipo'], //DEFINE SI FUE ENTRADA O SALIDA
                                'FechaHora' => $row_detalle['FechaHora'] //FECHA Y HORA ENTRADA O SALIDA
                            );
                            $_SESSION['detalle'][$row_detalle['ID']] = $array_detalle; //ARREGLO DETALLE VISITANTE
                            $_SESSION['visitante'][$row_detalle['IDVisitante']]['IDDetalle'] = $row_detalle['ID'];
                        }else {
                            if (!in_array($row_detalle['IDVisitante'], $item_array_detalle)) { //AGREGA AL ARREGLO DE DETALLE LOS VISITANTES QUE NO TENGAN ENTRADA EN CASETA PERO SI EN RECEPCION
                                $array_detalle = array(
                                    'ID' => $row_detalle['ID'],
                                    'IDVisitante' => $row_detalle['IDVisitante'],
                                    'PuntoAcceso' => $row_detalle['PuntoAcceso'], //PUNTO DE ACCESO DONDE FUE ENTRADA/SALIDA
                                    'Origen' => $row_detalle['Origen'], //DEFINE SI ES CASETA O RECEPCION
                                    'Tipo' => $row_detalle['Tipo'], //DEFINE SI FUE ENTRADA O SALIDA
                                    'FechaHora' => $row_detalle['FechaHora'] //FECHA Y HORA ENTRADA O SALIDA
                                );
                                $_SESSION['detalle'][$row_detalle['ID']] = $array_detalle; //ARREGLO DETALLE VISITANTE
                                $_SESSION['visitante'][$row_detalle['IDVisitante']]['IDDetalle'] = $row_detalle['ID'];
                            }
                        }
                    }
                    if($detalle == 0){
                        $detalle = 1;
                    }
                }
            }
            //ARREGLO PARA SABER SI VISITANTE YA TIENE REGISTRADA LA SALIDA
            if(!isset($_SESSION['salida'])){
                $sql_salida = "select IDVisitante from visitante_detalle vd
                               inner join visitante vs on vs.ID = vd.IDVisitante
                               inner join punto_acceso pa on vd.IDPuntoAcceso = pa.ID
                               where (vd.IDTipoOrigen = 2 or (vd.IDTipoOrigen = 1 and pa.IDOrigen = 2))  and vs.IDVisita = '$IDVisita'";
                $result_salida = mysqli_query($conn, $sql_salida);
                if(mysqli_num_rows($result_salida)==0){
                    $salida = 0;
                }
                else{
                    while($row_salida = mysqli_fetch_assoc($result_salida)){
                        $array_salida = array (
                            'IDVisitante' => $row_salida['IDVisitante']
                        );
                        $_SESSION['salida'][$row_salida['IDVisitante']] = $array_salida;
                    }
                    $salida = 1;
                }
            }
        }
        else{
            if($revision == 0){
                ?>
                <script>
                    alert ("No se puede admitir visita: <?php echo $revision?> ")
                    window.location.href = "admitirVisita.php"
                </script>
                <?php
            }
            ?>
            <script>
                alert ("No se puede admitir visita: <?php echo $revision?> ")
                window.location.href = "admitirVisita.php"
            </script>
            <?php
        }
    }
}
if (isset($_POST['submit'])){
    if(isset($_SESSION['message'])){
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
    $QRVisita = $_POST['codigo']; //CODIGO DE LA VISITA
    $idvisitante = $_POST['visitante']; //ID DEL VISITATNE
    $puntoacceso = $_SESSION['puntoacceso']; //PUNTO ACCESO DE USUARIO QUE REGISTRA ACCESO
    $fecha = date('Y/m/d H:i:s', time());
    $sql_updateVis = "UPDATE visitante SET EstatusCaseta = 1 WHERE ID = '$idvisitante'";
        if(!mysqli_query($conn, $sql_updateVis)){ //CAMBIA EL ESTATUSCASETA A 1
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al registrar visitante';
            ?>
            <script>
                window.location.href = "admisionVisita.php?QR=<?php echo $QRVisita?>&msg=error"
            </script>
            <?php
        }
        else{
            $sql_insert = "INSERT INTO visitante_detalle (IDVisitante, IDPuntoAcceso, IDTipoOrigen, FechaHora) VALUES ('$idvisitante', '$puntoacceso', 1, '$fecha')"; //INSERTA DETALLE VISITANTE - EL TIPO ORIGEN POR DEFAULT ES ENTRADA
            if(!mysqli_query($conn, $sql_insert)){
                $sql_reverse = "UPDATE visitante SET EstatusCaseta = 0 WHERE ID = '$idvisitante'"; //EN CASO DE ERROR REGRESA EL VISITANTE CON ESTATUSCASETA = 0
                mysqli_query($conn, $sql_reverse);
                $_SESSION['message'] = 'error';
                $_SESSION['messagecontent'] = 'Error al registrar visitante';
                ?>
                <script>
                    window.location.href = "admisionVisita.php?QR=<?php echo $QRVisita?>&msg=error"
                </script>
                <?php
            }
            else{
                $_SESSION['message'] = 'success';
                $_SESSION['messagecontent'] = 'Visitante registrado exitosamente';
                ?>
                <script>
                    window.location.href = "admisionVisita.php?QR=<?php echo $QRVisita?>&msg=success"
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
            <!--<li><a href="nuevaVisita.php"><i class="fas fa-plus-square"></i></i>Nueva Visita</a></li>
            <li><a href="editarVisita.php"><i class="fas fa-edit"></i>Editar Visita</a></li>-->
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
                <h4 class="top">Visita - <?php echo $row_visita['Titulo'] ?></h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
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
        <div id="registro">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="admisionVisita.php" method="post">
                <input type="hidden" name="codigo" value="<?php echo $QRVisita?>">

                <label for="visitante">Visitante</label>
                <select id="visitante" name="visitante">
                    <?php
                    $control = 0; //PARA REVISAR SI HAY RESULTADOS CON LOS CRITERIOS DE EntradaCaseta is null
                    if($recurrente == 1){
                        foreach ($_SESSION['visitante'] as $vis){
                            if($vis['EstatusCaseta'] == 0 && $vis['EstatusRecepcion'] == 0){
                                $control = $control + 1;
                            ?>
                                <option value="<?php echo $vis['ID']; ?>"> <?php echo $vis['Nombre']?> </option>
                            <?php
                            }
                        }
                    }
                    else{
                        if($salida == 1){ //SI HAY VALORES EN ARREGLO DE SALIDA - APLICA PARA CUANDO ALGUN VISITANTE YA SE LE REGISTRO SALIDA
                            $item_array_salida = array_column($_SESSION['salida'], 'IDVisitante');
                            foreach ($_SESSION['visitante'] as $vis){
                                if(!in_array($vis['ID'], $item_array_salida)){
                                    if($vis['EstatusCaseta'] == 0 && $vis['EstatusRecepcion'] == 0){
                                        $control = $control + 1;
                                        ?> <option value="<?php echo $vis['ID']; ?>"> <?php echo $vis['Nombre']?> </option> <?php
                                    }
                                }
                            }
                        }
                        else{
                            foreach ($_SESSION['visitante'] as $vis){
                                if($vis['EstatusCaseta'] == 0 && $vis['EstatusRecepcion'] == 0){
                                ?> <option value="<?php echo $vis['ID']; ?>"> <?php echo $vis['Nombre']?> </option> <?php
                                    $control = $control + 1;
                                }
                            }
                        }
                    }
                    if($control == 0){
                        ?>
                        <option value="">Sin Visitantes Disponibles</option>
                        <?php
                    }
                    ?>
                </select>

                <input type="submit"  value="Registrar Entrada" name="submit" <?php if($control == 0){ echo 'disabled'; } ?>>
            </form>
        </div>
        <div style="height: 50px"></div>
        <table id="example" class="display">
            <div id="registro">
                <h3>Visitantes Admitidos</h3>
            </div>
            <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Fecha/Hora</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($detalle != 0){
                $col = array_column($_SESSION['visitante'], 'IDDetalle');
                array_multisort($col, SORT_ASC, $_SESSION['visitante']);
                $item_array_id = array_column($_SESSION['detalle'], "IDVisitante");
                foreach ($_SESSION['visitante'] as $vis){
                    if (in_array($vis['ID'], $item_array_id)) {
                        $date = date_create($_SESSION['detalle'][$vis['IDDetalle']]['FechaHora']);
                        $printdate = date_format($date, "d-m-Y h:i:s A"); //VARIABLE PARA IMPRIMIR FECHA CON FORMATO d-m-YYYY h:m:s a.m/p.m.
                        ?>
                        <tr>
                            <td > <?php echo $vis['Nombre'] ?> </td>
                            <td><?php echo $printdate ?></td>
                        </tr>
                        <?php
                    }
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
