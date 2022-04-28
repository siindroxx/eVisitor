<?php
include('../../sql/conexion.php');
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../index.php"); // Redirecting To Home Page
    }
}
//OBTIENE DATOS DE VISITA
if(isset($_GET['id'])){
    $idVisita = $_GET['id'];
    $sql_visita = "SELECT * from visita where ID = '$idVisita' and Activo = 1";
    $result_visita = mysqli_query($conn, $sql_visita);
    $row = mysqli_fetch_array($result_visita);
    $titulo = $row['Titulo'];
    //OBTIENE DATOS DE PROTOCOLOS
    $sql_prot = "SELECT * FROM protocolo WHERE Activo = 1";
    $result_prot = mysqli_query($conn, $sql_prot);
    $result_prot_aux = mysqli_query($conn, $sql_prot);
   //OBTIENE NOMBRE DE PROTOCOLO DE LA VISITA
    while($row_protnom = mysqli_fetch_array($result_prot)) {
        if($row_protnom['ID']==$row['IDProtocolo']){
            $nombreprotocolo = $row_protnom['Nombre']; //NOMBRE CORRESPONDIENTE A IDProtocolo DE TABLA VISITA
        }
    }
    $sql_visitantes = "select v.Nombre as visitante, pa.Descripcion as PuntoAcceso, co.Descripcion as TipoPuntoAcceso,
                        cto.Descripcion as tipo, vd.FechaHora
                        from visitante_detalle vd
                        inner join visitante v on v.ID = vd.IDVisitante
                        inner join punto_acceso pa on pa.ID = vd.IDPuntoAcceso
                        inner join cat_tipo_origen cto on cto.ID = vd.IDTipoOrigen
                        inner join cat_origen co on pa.IDOrigen = co.ID
                        WHERE v.activo = 1 and v.IDVisita = '$idVisita'";
    $result_visitantes = mysqli_query($conn, $sql_visitantes);
}
else{
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
    $_SESSION['message'] = 'error';
    $_SESSION['messagecontent'] = 'Error al obtener ID de visita';
    ?>
    <script>
        window.location.href = "reporteVisitas.php?msg=error"
    </script>
    <?php
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../scripts/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../../css/buttons.dataTables.css">
    <link rel="stylesheet" type="text/css" href="../../css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../../JS/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/dataTables.buttons.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/buttons.html5.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="reporteVisitas.php"><i class="fas fa-calendar"></i>Visitas</a></li>
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
            <h4 class="top">Datos de visita</h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="registro">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="visita.php" method="post">
                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" placeholder="Asunto" maxlength="30" value="<?php echo $row['Asunto'];?>" disabled>

                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título" maxlength="30" value="<?php echo $row['Titulo'];?>"  disabled>

                 <!--FALTA CREAR TABLA DE PROTOCOLOS, POR MIENTRAS SE QUEDA ASI-->
                <label for="protocolo">Protocolo</label>
                <select id="protocolo" name="protocolo" disabled>
                    <option value="<?php echo $row['IDProtocolo']?>"><?php echo $nombreprotocolo?> </option>
                    <?php while($row_prot = mysqli_fetch_array($result_prot_aux)){
                                if($row_prot['ID']==$row['IDProtocolo']){
                                    continue;
                                }
                                else{
                                ?>
                                    <option value="<?php echo $row_prot['ID']?>"> <?php echo $row_prot['Nombre']?></option>
                                <?php
                                }
                            } ?>
                </select>

                <label for="instrucciones">Instrucciones</label>
                <!--<input type="text" id="instrucciones" name="instrucciones" placeholder="Instrucciones de Visita" size="255" value=""  maxlength="255">-->
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255" disabled><?php echo $row['Instrucciones'];?></textarea>

                <label for="fechainicio">Fecha Inicio</label>
                <input type="date" id="fechainicio" name="fechainicio" value="<?php echo $row['FechaVisita'];?>" disabled>

                <label for="horainicio">Hora Inicio</label>
                <input type="time" id="horainicio" name="horainicio" value="<?php echo $row['HoraInicio'];?>"  disabled>

                <label for="recurrente">Visita Recurrente</label>
                <select id="recurrente" name="recurrente" onchange="yesnoCheck(this);" disabled> <!--Si se selecciona "sí" se muestran los campos de fechafin y horafin-->
                    <!--<option value="0">No</option>
                    <option value="1">Si</option>-->
                    <option value="<?php echo $row['Recurrente']?>"><?php if($row['Recurrente'] == 1){echo "Si";}else{ echo "No";} ?></option>
                    <option value="<?php if($row['Recurrente'] == 1){echo 0;}else{echo 1;}?>"><?php if($row['Recurrente'] == 1){echo "No";}else{ echo "Si";} ?></option>
                </select>
                <!--Falta agregar validacion para requerir fechafin y horafin en caso de ser visita recurrente-->
                <div id="fecharecurrente" style=" <?php if($row['Recurrente'] == 0) {echo "display: none;";} ?>">

                <label>Fecha Fin</label>
                <input type="date" id="fechafin" name="fechafin" value="<?php echo $row['FechaFin'];?>" disabled>

                <label for="horafin">Hora Fin</label>
                <input type="time" id="horafin" name="horafin" value="<?php if($row['Recurrente']==0){$row['HoraFin']=null;} echo $row['HoraFin'];?>" disabled> <!--SI NO ES VISITA RECURRENTE PONE LA HORA FIN COMO NULA, PORQQUE EN LA VISTA LO INTERPRETABA COMO 12:00 AM-->
                </div>
            </form>
            <br>
            <h3>Visitantes Entrada/Salida</h3>
            <!--<div class="logout">
                <p><a href="../reportes/export/exportVisitantesEntradaSalida.php?export=1&idVisita=<?php echo $idVisita ?>&titulo=<?php echo $titulo?>"><i class="fas fa-file-csv"></i></i> Exportar CSV</a></p>
            </div>-->
            <table id="example" class="display">
                <thead>
                <tr>
                    <th scope="col">Visitante</th>
                    <th scope="col">Punto de Acceso</th>
                    <th scope="col">Tipo Punto Acceso</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Fecha y Hora</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while($row_visitantes = mysqli_fetch_array($result_visitantes)){
                    ?>
                    <tr>
                        <td > <?php echo $row_visitantes['visitante'] ?> </td>
                        <td > <?php echo $row_visitantes['PuntoAcceso'] ?> </td>
                        <td><?php echo $row_visitantes['TipoPuntoAcceso'] ?></td>
                        <td><?php echo $row_visitantes['tipo'] ?></td>
                        <td><?php echo $row_visitantes['FechaHora'] ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#example').DataTable( {
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 filas', '25 filas', '50 filas', 'Mostrar todo' ]
            ],
            buttons: [
                'pageLength',
                {
                    extend: 'csv',
                    filename: '<?php echo 'Visitantes_' . $titulo.'_'. $row['FechaVisita'] ?>'
                }
            ]
        } );
    } );
</script>
</body>
</html>