<?php
include('../sql/conexion.php');
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
}
else{
    //UPDATE DE VISITA - ACTUALIZA TODOS LOS VALORES DE LA TABLA VISITA
    if (isset($_POST['submit'])) {
        $id = $_POST['idvisita'];
        $asunto = $_POST['asunto']; //ASUNTO DE LA VISITA
        $titulo = $_POST['titulo']; //TITULO DE LA VISITA
        $protocolo = $_POST['protocolo']; //PROTOCOLO ELEGIDO
        $instrucciones = $_POST['instrucciones']; //INSTRUCCIONES DE VISITA
        $fechainicio = $_POST['fechainicio']; //FECHA DE LA VISITA
        $horainicio = $_POST['horainicio']; //HORA DE INICIO DE LA VISITA
        $recurrente = $_POST['recurrente']; //DEFINE SI LA VISITA ES RECURRENTE O UNICA. 1 = RECURRENTE || 0 = UNICA
        if($fechainicio == date('Y/m/d') && $horainicio < date('h:i:sa')) //VALIDA QUE LA FECHA Y HORA DE INICIO SEAN MAYORES A LA FECHA Y HRA ACTUALES
        {
            ?>
            <script>
                alert ("No se puede actualizar la visita cuando la hora es menor a la hora actual")
            </script>
            <?php
        }
        else{
            if($recurrente == 1){ //SI LA VISITA ES RECURRENTE RECIBE LOS VALORES DE FECHA Y HORA FINALES
                $fechafin = $_POST['fechafin'];
                $horafin = $_POST['horafin'];
            }
            else{ //EN CASO DE NO SER RECURRENTE LOS DEJA NULOS
                $fechafin = null;
                $horafin = null;
            }
            if ($fechafin <= $fechainicio && !is_null($fechafin) && !is_null($horafin)){ //VALIDA QUE LA FECHA Y HORA FIN SEAN MAYORES A LA FECHA Y HORA INICIALES
                ?>
                <script>
                    alert ("No se puede actualizar la visita cuando es recurrente donde la fecha fin sea menor a la fecha inicio")
                </script>
                <?php
            }
            else{ //INSERTA LA VISITA EN LA BASE DE DATOS
                if(isset($_SESSION['message'])){
                    unset($_SESSION['message']);
                }
                if(isset($_SESSION['messagecontent'])){
                    unset($_SESSION['messagecontent']);
                }
                $sql_visita = "UPDATE visita 
                                SET Asunto ='$asunto', Titulo = '$titulo', IDProtocolo = '$protocolo', 
                                Instrucciones = '$instrucciones', FechaVisita ='$fechainicio', HoraInicio ='$horainicio', 
                                FechaFin ='$fechafin', HoraFin = '$horafin', 
                                Recurrente = '$recurrente'
                                WHERE ID = '$id'";
                if(!mysqli_query($conn, $sql_visita)){
                    $_SESSION['message'] = 'error';
                    $_SESSION['messagecontent'] = 'Error al actualizar visita';
                    ?>
                    <script>
                        window.location.href = "editarVisita.php?msg=error"
                    </script>
                    <?php
                }
                else{
                    $_SESSION['message'] = 'success';
                    $_SESSION['messagecontent'] = 'Visitante actualizada exitosamente';
                    ?>
                    <script>
                        window.location.href = "editarVisita.php?msg=success"
                    </script>
                    <?php
                }
            }
        }
    }
    else{
        ?>
        <script>
            alert ("Error al obtener id de visita")
            window.location.href = "editarVisita.php"
        </script>
        <?php
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
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="nuevaVisita.php"><i class="fas fa-plus-square"></i></i>Nueva Visita</a></li>
            <li><a href="editarVisita.php"><i class="fas fa-edit"></i>Editar Visita</a></li>

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
            <h4 class="top">Editar Visita</h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="registro">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="editaVisita.php" method="post">
                <input type="hidden" value="<?php echo $idVisita?>" name="idvisita">

                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" placeholder="Asunto" maxlength="30" value="<?php echo $row['Asunto'];?>" required>

                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título" maxlength="30" value="<?php echo $row['Titulo'];?>"  required>

                 <!--FALTA CREAR TABLA DE PROTOCOLOS, POR MIENTRAS SE QUEDA ASI-->
                <label for="protocolo">Protocolo</label>
                <select id="protocolo" name="protocolo">
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
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255"><?php echo $row['Instrucciones'];?></textarea>

                <label for="fechainicio">Fecha Inicio</label>
                <input type="date" id="fechainicio" name="fechainicio" value="<?php echo $row['FechaVisita'];?>" required>

                <label for="horainicio">Hora Inicio</label>
                <input type="time" id="horainicio" name="horainicio" value="<?php echo $row['HoraInicio'];?>"  required>

                <label for="recurrente">Visita Recurrente</label>
                <select id="recurrente" name="recurrente" onchange="yesnoCheck(this);"> <!--Si se selecciona "sí" se muestran los campos de fechafin y horafin-->
                    <!--<option value="0">No</option>
                    <option value="1">Si</option>-->
                    <option value="<?php echo $row['Recurrente']?>"><?php if($row['Recurrente'] == 1){echo "Si";}else{ echo "No";} ?></option>
                    <option value="<?php if($row['Recurrente'] == 1){echo 0;}else{echo 1;}?>"><?php if($row['Recurrente'] == 1){echo "No";}else{ echo "Si";} ?></option>
                </select>
                <!--Falta agregar validacion para requerir fechafin y horafin en caso de ser visita recurrente-->
                <div id="fecharecurrente" style=" <?php if($row['Recurrente'] == 0) {echo "display: none;";} ?>">

                <label>Fecha Fin</label>
                <input type="date" id="fechafin" name="fechafin" value="<?php echo $row['FechaFin'];?>">

                <label for="horafin">Hora Fin</label>
                <input type="time" id="horafin" name="horafin" value="<?php if($row['Recurrente']==0){$row['HoraFin']=null;} echo $row['HoraFin'];?>"> <!--SI NO ES VISITA RECURRENTE PONE LA HORA FIN COMO NULA, PORQQUE EN LA VISTA LO INTERPRETABA COMO 12:00 AM-->
                </div>

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Actualizar">
            </form>
        </div>
    </div>
</div>
<script>
    //funcion para mostrar/ocultar campos de fechafin y horafin dependiendo del select de visitareccurente
    function yesnoCheck(that) {
        if (that.value == "1") {
            document.getElementById("fecharecurrente").style.display = "block";
        } else {
            document.getElementById("fecharecurrente").style.display = "none";
        }
    }
    //Para bloquear las fechas menores a la fecha actual
    var today = new Date().toISOString().split('T')[0];
    document.getElementsByName("fechainicio")[0].setAttribute('min', today);
    document.getElementsByName("fechafin")[0].setAttribute('min', today);
</script>
</body>
</html>