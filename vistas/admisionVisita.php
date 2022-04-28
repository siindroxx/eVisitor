<?php
include ('../sql/conexion.php');
include ('../funciones/email.php');
include ('../funciones/calculatiempo.php');
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../index.php"); // Redirecting To Home Page
    }
}
if(!isset($_SERVER['HTTP_REFERER'])){
    // REDIRIGIR A VENTA EN CASO QUE INGRESEN URL DIRECTAMENTE
    header('location: ../main.php');
    exit;
}
if(isset($_GET['QR'])){
    /*RESETEO DE VARIABLES*/
    if(isset($_SESSION['QRVisita'])) {
        unset($_SESSION['QRVisita']);
    }
    if(isset($_SESSION['visitante'])) {
        unset($_SESSION['visitante']);
    }
    if(isset($_SESSION['detallec'])){
        unset($_SESSION['detallec']);
    }
    if(isset($_SESSION['detaller'])){
        unset($_SESSION['detaller']);
    }
    if(isset($_SESSION['salida'])){
        unset($_SESSION['salida']);
    }
    if(isset($_SESSION['admision'])) {
        unset($_SESSION['admision']);
    }
    if(isset($_SESSION['notificado'])){
        unset($_SESSION['notificado']);
    }
    $_SESSION['QRVisita'] = $_GET['QR']; //CODIGO QR DE LA VISITA
}
if(!isset($_GET['msg'])) {
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
}
if(isset($_SESSION['QRVisita'])){
    $QRVisita = $_SESSION['QRVisita'];
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
        $row_visita = mysqli_fetch_array($result_visita); //RESULTADO DE QUERY DE VISITA
        $horallegada = calculatiempo($row_visita['FechaVisita'], $row_visita['HoraInicio'], $row_visita['FechaFin'], $row_visita['HoraFin'], $row_visita['Recurrente'], $conn); //REVISA QUE HORA DE VISITA CUMPLA CONDICIONES
        if($horallegada == 1) {
            $IDVisita = $row_visita['ID']; //ID DE LA VISITA
            $IDProtocolo = $row_visita['IDProtocolo']; //ID DEL PROTOCOLO DE LA VISITA
            $IDUsuario = $row_visita['IDUsuario']; //ID DEL USUARIO CREADOR DE LA VISITA
            $recurrente = $row_visita['Recurrente'];
            //OBTIENE DATOS DE PROTOCOLO
            $sql_prot = "SELECT * FROM protocolo WHERE ID = '$IDProtocolo' ";
            $result_prot = mysqli_query($conn, $sql_prot);
            $row_prot = mysqli_fetch_array($result_prot); //DATOS DE PROTOCOLO

            //OBTENER DETALLE VISITANTES ADMITIDOS EN CASETA
            if(!isset($_SESSION['detallec'])) {
                //INICIO PARA CUANDO NO ES RECURRENTE
                if($recurrente == 0){
                    $sql_detallec = "SELECT v.id as IDVisitante, vd.id as IDDetalle, vd.FechaHora FROM visitante v
                            INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN(
                              select IDVisitante, max(vd1.id) as maxid
                              from visitante_detalle vd1
                              inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                              where IDTipoOrigen =  1 and pa.IDOrigen = 1
                              GROUP BY IDVisitante
                              ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                              WHERE v.IDVisita = '$IDVisita' and v.Activo = 1";
                    $result_detallec = mysqli_query($conn, $sql_detallec);
                    if (mysqli_num_rows($result_detallec) == 0) {
                        $detallec = 0; //VERIFICA SI HAY DATOS EN DETALLEC
                    } else {
                        while ($row_detallec = mysqli_fetch_array($result_detallec)) {
                            $array_detallec = array(
                                'IDVisitante' => $row_detallec['IDVisitante'],
                                'IDDetalle' => $row_detallec['IDDetalle'],
                                'FechaHora' => $row_detallec['FechaHora']
                            );
                            $_SESSION['detallec'][$row_detallec['IDVisitante']] = $array_detallec;
                        }
                        $detallec = 1;
                    }
                }//FIN PARA CUANDO NO ES RECURRENTE
                else {//INICIO PARA CUANDO ES RECURRENTE
                    $sql_detallec = "SELECT v.id as IDVisitante, vd.id as IDDetalle, vd.FechaHora FROM visitante v
                            INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN(
                              select IDVisitante, max(vd1.id) as maxid
                              from visitante_detalle vd1
                              inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                              where IDTipoOrigen =  1 and pa.IDOrigen = 1
                              GROUP BY IDVisitante
                              ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                              WHERE v.IDVisita = '$IDVisita' and v.EstatusCaseta = 1 and v.Activo = 1";
                    $result_detallec = mysqli_query($conn, $sql_detallec);
                    if (mysqli_num_rows($result_detallec) == 0) {
                        $detallec = 0; //VERIFICA SI HAY DATOS EN DETALLEC
                    } else {
                        while ($row_detallec = mysqli_fetch_array($result_detallec)) {
                            $array_detallec = array(
                                'IDVisitante' => $row_detallec['IDVisitante'],
                                'IDDetalle' => $row_detallec['IDDetalle'],
                                'FechaHora' => $row_detallec['FechaHora']
                            );
                            $_SESSION['detallec'][$row_detallec['IDVisitante']] = $array_detallec;
                        }
                        $detallec = 1;
                    }
                }//FIN DE CUANDO ES RECURRENTE
            }
            //OBTENER DETALLE VISITANTES ADMITIDOS EN RECEPCION
            if(!isset($_SESSION['detaller'])) {
                if($recurrente == 0){ //INICIO PARA CUANDO NO ES RECURRENTE
                    $sql_detaller = "SELECT v.id as IDVisitante, vd.id as IDDetalle, vd.FechaHora FROM visitante v
                            INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN(
                              select IDVisitante, max(vd1.id) as maxid
                              from visitante_detalle vd1
                              inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                              where IDTipoOrigen =  1 and pa.IDOrigen = 2
                              GROUP BY IDVisitante
                              ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                              WHERE v.IDVisita = '$IDVisita' and v.Activo = 1";
                    $result_detaller = mysqli_query($conn, $sql_detaller);
                    if(mysqli_num_rows($result_detaller) == 0){
                        $detaller = 0; //VERIFICA SI HAY DATOS EN DETALLER
                    }else {
                        while ($row_detaller = mysqli_fetch_array($result_detaller)) {
                            $array_detaller = array(
                                'IDVisitante' => $row_detaller['IDVisitante'],
                                'IDDetalle' => $row_detaller['IDDetalle'],
                                'FechaHora' => $row_detaller['FechaHora']
                            );
                            $_SESSION['detaller'][$row_detaller['IDVisitante']] = $array_detaller;
                        }
                        $detaller = 1;
                    }
                } // FIN PARA CUANDO NO ES RECURRENTE
                else {//INICIO DE CUANDO ES RECURRENTE
                    $sql_detaller = "SELECT v.id as IDVisitante, vd.id as IDDetalle, vd.FechaHora FROM visitante v
                            INNER JOIN visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN(
                              select IDVisitante, max(vd1.id) as maxid
                              from visitante_detalle vd1
                              inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                              where IDTipoOrigen =  1 and pa.IDOrigen = 2
                              GROUP BY IDVisitante
                              ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                              WHERE v.IDVisita = '$IDVisita' and v.EstatusRecepcion = 1 and v.Activo = 1";
                    $result_detaller = mysqli_query($conn, $sql_detaller);
                    if (mysqli_num_rows($result_detaller) == 0) {
                        $detaller = 0; //VERIFICA SI HAY DATOS EN DETALLER
                    } else {
                        while ($row_detaller = mysqli_fetch_array($result_detaller)) {
                            $array_detaller = array(
                                'IDVisitante' => $row_detaller['IDVisitante'],
                                'IDDetalle' => $row_detaller['IDDetalle'],
                                'FechaHora' => $row_detaller['FechaHora']
                            );
                            $_SESSION['detaller'][$row_detaller['IDVisitante']] = $array_detaller;
                        }
                        $detaller = 1;
                    }
                }//FIN DE CUANDO ES RECURRENTE
            }

            //ARREGLO PARA SABER SI VISITANTE YA TIENE REGISTRADA LA SALIDA
            if(!isset($_SESSION['salida'])){
                $sql_salida = "select IDVisitante from visitante_detalle vd
                               inner join visitante vs on vs.ID = vd.IDVisitante
                               where vd.IDTipoOrigen = 2 and vs.IDVisita = '$IDVisita'";
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

            //OBTENER VISITANTES
            if(!isset($_SESSION['visitante'])) {
                $sql_visitante = "SELECT * FROM visitante WHERE IDVisita = '$IDVisita' and Activo = 1";
                $result_visitante = mysqli_query($conn, $sql_visitante);
                if(mysqli_num_rows($result_visitante) == 0){
                    $result_visitante = "SIN VISITANTES";
                }
                else {
                    while ($row_visitante = mysqli_fetch_array($result_visitante)) {
                        $array_visitante = array(
                            'ID' => $row_visitante['ID'],
                            'Nombre' => $row_visitante['Nombre'],
                            'Email' => $row_visitante['Email'],
                            'EstatusCaseta' => $row_visitante['EstatusCaseta'],
                            'EstatusRecepcion' => $row_visitante['EstatusRecepcion'],
                            'CodigoNFC' => $row_visitante['CodigoNFC']
                        );
                        $_SESSION['visitante'][$row_visitante['ID']] = $array_visitante; //ARREGLO DE VISITANTES
                    }
                }
            }

            //AGREGA AL ARRAY DE ADMISION LOS VISITANTES YA ADMITIDOS
            if(isset($_SESSION['visitante']) && isset($_SESSION['detaller'])) {
                $item_array_id = array_column($_SESSION['detaller'], "IDVisitante");
                foreach ($_SESSION['visitante'] as $values){
                    if($values['EstatusRecepcion'] == 1 || ($recurrente == 0)){
                        if(in_array($values['ID'], $item_array_id)){
                            $item_array = array(
                                'admision_id' => $values['ID'],
                                'codigoacceso' => 0,
                                'date' => $_SESSION['detaller'][$values['ID']]['FechaHora']
                            );
                            $_SESSION['admision'][$values['ID']] = $item_array;
                        }
                    }
                }
            }
        } //FIN DE VALIDACION DE HORA DE LLEGADA
        else{ //SI NO SE CUMPLE LA CONDICION DE CALCULATIEMPO
            if($horallegada == 0){ //FALTAN DATOS DE HORA DE GRACIA / HORA LLEGADA
                ?>
                <script>
                    alert ("Por Favor Registre el tiempo de gracia antes de recibir una visita")
                    window.location.href = "admitirVisita.php"
                </script>
                <?php
            }
            else{
                ?>
                <script>
                    alert ("No se puede admitir visita: <?php echo $horallegada ?>")
                    window.location.href = "admitirVisita.php"
                </script>
                <?php
            }
        }
    }
}
if (isset($_POST['submit'])){
    $fecha = date('Y/m/d H:i:s', time());
    $detallec = $_POST['detallec'];
    /*INSERTAR VISITANTE EN ARREGLO*/
    if(isset($_SESSION['admision'])){
        $item_array_id = array_column($_SESSION['admision'],"admision_id");
        if(!in_array($_POST['visitante'], $item_array_id)){
            $item_array = array(
                'admision_id' => $_POST['visitante'],
                'codigoacceso' => $_POST['codigoacceso'],
                'date' => $fecha
            );
            $_SESSION['admision'][$_POST['visitante']] = $item_array;
        }
    }
    else{
        $item_array = array(
            'admision_id' => $_POST['visitante'],
            'codigoacceso' => $_POST['codigoacceso'],
            'date' => $fecha
        );
        $_SESSION['admision'][$_POST['visitante']] = $item_array;
    }
}
//GUARDAR VISITA - ACTUALIZA BASE DE DATOS Y ENVIA EMAIL A NOTIFICADOS
if(isset($_GET['action'])){
    if($_GET['action'] == 'guardar'){
        $IDProtocolo = $_GET['ProtocoloID']; //ID DE PROTOCOLO DE VISITA
        $IDUsuario = $_GET['IDu']; //ID DE USUARIO QUE ADMITE LA VISITA
        $titulo = $_GET['titulo']; //TITULO DE LA VISITA

        $puntoacceso = $_SESSION['puntoacceso']; //ID DE PUNTO DE ACCESO DONDE SE REGISTRA ENTRADA
        $check = 0; //VARIABLE AUXILIAR
        if(isset($_SESSION['admision'])){
            foreach ($_SESSION['admision'] as $values) {
                $CodigoNFC = $values['codigoacceso'];
                if($CodigoNFC != 0){ //SOLO GUARDA LOS NUEVOS VISITANTES
                    $check = 1;
                    $EntradaRecepcion = $values['date'];
                    $idvisitante = $values['admision_id'];
                    $sql_insertv = "INSERT INTO visitante_detalle (IDVisitante, IDPuntoAcceso, IDTipoOrigen, FechaHora) VALUES ('$idvisitante', '$puntoacceso', 1, '$EntradaRecepcion')";
                    if (!mysqli_query($conn, $sql_insertv)) {
                        $_SESSION['message'] = 'error';
                        $_SESSION['messagecontent'] = 'Error al registrar detalle de visitante';
                        ?>
                        <script>
                            window.location.href = "admitirVisita.php?QR=<?php echo $_SESSION['QRVisita']?>&msg=error"
                        </script>
                        <?php
                    }
                    else{
                        $sql_updatev = "UPDATE visitante SET EstatusRecepcion = 1, CodigoNFC = '$CodigoNFC' WHERE ID = '$idvisitante'";
                        if(!mysqli_query($conn, $sql_updatev)){
                            $_SESSION['message'] = 'error';
                            $_SESSION['messagecontent'] = 'Error al registrar visitante';
                            ?>
                            <script>
                                window.location.href = "admitirVisita.php?QR=<?php echo $_SESSION['QRVisita']?>&msg=error"
                            </script>
                            <?php
                        }
                    }
                }
            }
        }
        if($check == 1){
            //OBTIENE DATOS DE NOTIFICADOS
            $sql_notificado = "SELECT u.ID, u.Nombre, u.Correo FROM notificado n 
           INNER JOIN usuario u ON n.IDUsuario = u.ID
           WHERE n.IDProt = '$IDProtocolo' and n.Activo = 1";
            $result_notificado = mysqli_query($conn, $sql_notificado); //DATOS DE NOTIFICADOS
            if (mysqli_num_rows($result_notificado) != 0) {
                while ($row_notificado = mysqli_fetch_assoc($result_notificado)) {
                    $array_notificado = array(
                        'notificado_id' => $row_notificado['ID'],
                        'Nombre' => $row_notificado['Nombre'],
                        'Email' => $row_notificado['Correo']
                    );
                    $_SESSION['notificado'][$row_notificado['ID']] = $array_notificado;
                }
            }
            //OBTIENE DATOS DE USUARIO CREADOR DE VISITA
            $sql_usuario = "SELECT Nombre, Correo FROM usuario WHERE ID = '$IDUsuario'";
            $result_usuario = mysqli_query($conn, $sql_usuario);
            $row_usuario = mysqli_fetch_array($result_usuario); //DATOS DE USUARIO CREADOR DE VISITA
            $NombreUsuarioCreador = $row_usuario['Nombre'];
            $EmailUsuarioCreador = $row_usuario['Correo'];
            $nombres = ''; //NOMBRES VISITANTES ADMITIDOS
            $count = 0;
            //CREA CADENA DE USUARIOS ADMITIDOS - SOLO PONE EN LA CADENA A LOS USUARIOS QUE SU CODIGO DE ACCESO SEA DIFERENTE DE 0, OSEA A LAS ADMISIONES NUEVAS
            foreach ($_SESSION['admision'] as $value){
                if($value['codigoacceso'] != 0) {
                    if ($count == 0) {
                        $nombres = $_SESSION['visitante'][$value['admision_id']]['Nombre'];
                        $count = $count + 1;
                    }
                    else {
                        $nombres = $nombres . ", " . $_SESSION['visitante'][$value['admision_id']]['Nombre'];
                    }
                }
            }
            if($count != 0) { //SOLO ENVIA EMAIL SI SE ACTUALIZO AL MENOS UN VISITANTE
                sendemail($NombreUsuarioCreador, '', $titulo, $nombres, $EmailUsuarioCreador, 2);
                $_SESSION['message'] = 'success';
                $_SESSION['messagecontent'] = 'Visitantes registrados exitosamente';
                ?>
                <script> window.location = "admisionVisita.php?QR=<?php echo $_SESSION['QRVisita']?>&msg=success" </script>
                <?php
            }
            else{
            ?>
            <script> window.location = "admisionVisita.php?QR=<?php echo $_SESSION['QRVisita']?>" </script>
            <?php
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../scripts/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../JS/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="../JS/jquery.dataTables.min.js"></script>

</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="admitirVisita.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
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
                <div style="float: left">
                <h3><a href="admisionVisita.php?action=guardar&ProtocoloID=<?php echo $IDProtocolo?>&IDu=<?php echo $IDUsuario?>&titulo=<?php echo $row_visita['Titulo'] ?>"><i class="fas fa-save"></i>Guardar</a></h3>
                </div>
                <div style="float: right">
                <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
                </div>
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
        <div id="admision" style="float: left; width: 50%">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="admisionVisita.php" method="post">
                <input type="hidden" value="<?php echo $IDVisita?>" name="idvisita">

                <label for="fechainicio">Fecha Inicio</label>
                <input type="date" id="fechainicio" name="fechainicio" value="<?php echo $row_visita['FechaVisita'];?>" disabled>

                <label for="horainicio">Hora Inicio</label>
                <input type="time" id="horainicio" name="horainicio" value="<?php echo $row_visita['HoraInicio'];?>"  disabled>

                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto"  value="<?php echo $row_visita['Asunto'];?>" disabled>

                <!--<label for="protocolo">Protocolo</label>
                <input type="text" id="protocolo" name="protocolo" value="<?php echo $row_prot['Nombre'];?>" disabled>-->

                <label for="instrucciones">Instrucciones Visita</label>
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255" disabled><?php echo $row_visita['Instrucciones'];?></textarea>

                <label for="protocolo">Protocolo</label>
                <input type="text" id="protocolo" name="protocolo" value="<?php echo $row_prot['Nombre'];?>" disabled>

                <label for="instruccionesprot">Instrucciones Protocolo</label>
                <textarea id="instruccionesprot" name="instruccionesprot" rows="4" cols="190" maxlength="255" disabled><?php echo $row_prot['Instrucciones'];?></textarea>

                <div class="logout">
                    <h3>Abrir PDF de protocolo: <a href="../<?php echo $row_prot['Archivo'] ?>" target="_blank"><i class="fas fa-book-open"></i> Abrir</a></h3>
                </div>

            </form>
        </div>
        <div id="admision" style="float: right; width: 50%">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="admisionVisita.php" method="post">
                <input type="hidden" name="codigo" value="<?php echo $QRVisita?>">
                <input type="hidden" name="detallec" value="<?php echo $detallec?>">


                <label for="visitante">Visitante</label>
                <select id="visitante" name="visitante">
                    <?php
                    $control = 0; //PARA REVISAR SI HAY RESULTADOS CON LOS CREITERIOS DE EntradaCaseta is not null && EntradaRecepcion is null
                    if($salida == 1){
                        $item_array_salida = array_column($_SESSION['salida'], 'IDVisitante');
                    }
                    foreach ($_SESSION['visitante'] as $values){ //LLAMA AL ARRAY DE VISITANTES
                        if(isset($_SESSION['admision'])){
                                $item_array_id = array_column($_SESSION['admision'],"admision_id");
                                 if(!in_array($values['ID'], $item_array_id)){
                                     if($salida == 1 && $recurrente == 0){
                                         if(!in_array($values['ID'], $item_array_salida)){
                                             $control = $control + 1;
                                             ?> <option value="<?php echo $values['ID']; ?>"> <?php echo $values['Nombre']?> </option> <?php
                                         }
                                     }
                                     else{
                                         $control = $control + 1;
                                    ?>
                                        <option value="<?php echo $values['ID']; ?>"> <?php echo $values['Nombre']?> </option>
                                    <?php
                                     }
                                }
                        }
                        else{
                            if($salida == 1 && $recurrente == 0){
                                if(!in_array($values['ID'], $item_array_salida)){
                                    $control = $control + 1;
                                    ?>
                                    <option value="<?php echo $values['ID']; ?>"> <?php echo $values['Nombre']?> </option>
                                    <?php
                                }
                            }
                            else{
                                $control = $control + 1;
                                ?> <option value="<?php echo $values['ID']; ?>"> <?php echo $values['Nombre']?> </option> <?php
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

                <label for="codigoacceso">Acceso</label>
                <input type="password" id="codigoacceso" name="codigoacceso" required <?php if($control == 0){ echo 'disabled'; } ?>> <!--SE DESHABILITA SI NO HAY VISITANTES-->

                <input type="submit"  value="Registrar" name="submit" <?php if($control == 0){ echo 'disabled'; } ?>><!--SE DESHABILITA SI NO HAY VISITANTES-->
            </form>
        </div>
        <div style="height: 700px"></div>
        <div id="admision" style=" float: left; width: 50%">
            <h3>Admitidos Caseta</h3>
            <table id="example" class="display" style="width: 100%">
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Fecha/Hora</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if($detallec == 1){
                    $array_detaller_id = array_column($_SESSION['detallec'], 'IDVisitante'); //ID DE VISITANTE EN ARREGLO DE DETALLE CASETA
                    foreach ($_SESSION['visitante'] as $values){
                        if($values['EstatusCaseta'] == 1 || $recurrente == 0){
                            if(in_array($values['ID'], $array_detaller_id)){
                                $date = $_SESSION['detallec'][$values['ID']]['FechaHora'];//VARIABLE AUXILIAR DE FECHA
                                //$printdate = date_format($date, "d-m-Y h:i:s A"); //VARIABLE PARA IMPRIMIR FECHA CON FORMATO d-m-YYYY h:m:s a.m/p.m.
                                ?>
                                <tr>
                                    <td > <?php echo $values['Nombre'] ?> </td>
                                    <td><?php echo $date ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div id="admision" style=" float: right; width: 50%">
            <h3>Admitidos Recepcion</h3>
            <table id="example2" class="display" style="width: 100%">
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Fecha/Hora</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(isset($_SESSION['admision'])){
                    foreach ($_SESSION['admision'] as $values){
                        $dateaux = date_create($values['date']);//VARIABLE AUXILIAR DE FECHA
                        $printdate = date_format($dateaux, "d-m-Y h:i:s A"); //VARIABLE PARA IMPRIMIR FECHA CON FORMATO d-m-YYYY h:m:s a.m/p.m.
                        ?>
                        <tr <?php if($values['codigoacceso'] != 0){?> style="color: orange"<?php } ?>>
                            <td> <?php echo $_SESSION['visitante'][$values['admision_id']]['Nombre'] ?> </td>
                            <td><?php echo $printdate ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../JS/table.js"></script>
<script src="../JS/table2.js"></script>
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
        setTimeout(function(){
            $('.alert').removeClass("showAlert");
            $('.alert').removeClass("hide");
        },6000);
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
<?php } ?>
    <script>
        setTimeout(function(){
            $('.alert').removeClass("showAlert");
            $('.alert').removeClass("hide");
            $('.alertsuccess').removeClass("showAlert");
            $('.alertsuccess').removeClass("hide");
        },6000);
    </script>
<?php }?>
</body>
</html>