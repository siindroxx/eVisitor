<?php
    include('../sql/conexion.php');
    include('../funciones/codegenerator.php');
    include('../funciones/genqr.php');
    session_start();
    if(!isset($_SESSION['login_user'])){
        header("location: ../../index.php"); // Redirecting To Home Page
    }
    else{
        if($_SESSION['tipo'] != 1){
            header("location: ../index.php"); // Redirecting To Home Page
        }
    }
    //OBTIENE PROTOCOLOS DISPONIBLES
    $sql_prot = "SELECT * FROM protocolo WHERE Activo = 1";
    $result_prot = mysqli_query($conn, $sql_prot);
    //GUARDAR UNA NUEVA VISITA
    if (isset($_POST['submit'])) {
        $idusuario = $_SESSION['login_id']; //ID DEL USUARIO QUE CREA LA VISITA
        $asunto = $_POST['asunto']; //ASUNTO DE LA VISITA
        $titulo = $_POST['titulo']; //TITULO DE LA VISITA
        $protocolo = $_POST['protocolo']; //PROTOCOLO ELEGIDO
        $instrucciones = $_POST['instrucciones']; //INSTRUCCIONES DE VISITA
        $fechainicio = $_POST['fechainicio']; //FECHA DE LA VISITA
        $horainicio = $_POST['horainicio']; //HORA DE INICIO DE LA VISITA
        $recurrente = $_POST['recurrente']; //DEFINE SI LA VISITA ES RECURRENTE O UNICA. 1 = RECURRENTE || 0 = UNICA
        $codigo = generateRandomString(5, $conn); //GENERA UN CODIGO DE 6 CARACTERES
        if($fechainicio == date('Y/m/d') && $horainicio < date('h:i:sa')) //VALIDA QUE LA FECHA Y HORA DE INICIO SEAN MAYORES A LA FECHA Y HRA ACTUALES
        {
            ?>
            <script>
                alert ("No se puede registrar visita en hora menor a la hora actual")
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
                    alert ("No se puede registrar visita recurrente donde la fecha fin sea menor a la fecha inicio")
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
                $sql_visita = "INSERT INTO visita (IDUsuario, Asunto, Titulo, IDProtocolo, Instrucciones, FechaVisita, HoraInicio, FechaFin, HoraFin, Recurrente, CodigoVisita , Activo)
                                VALUES ('$idusuario','$asunto', '$titulo', '$protocolo', '$instrucciones', '$fechainicio', '$horainicio', '$fechafin', '$horafin', '$recurrente', '$codigo' , 1 )";
                if(!mysqli_query($conn, $sql_visita)){
                    $_SESSION['message'] = 'error';
                    $_SESSION['messagecontent'] = 'Error al registrar visita';
                    ?>
                    <script>
                        window.location.href = "editarVisita.php?msg=error"
                    </script>
                    <?php
                }
                else{
                    //CREAR CODIGO QR CON EL CODIGO DE 6 DIGITOS
                    genqr($codigo);
                    $_SESSION['message'] = 'success';
                    $_SESSION['messagecontent'] = 'Visita registrada exitosamente';
                    ?>
                    <script>
                    window.location.href = "editarVisita.php?msg=success"
                    </script>
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
    </div>
    <div class="main_content">
        <div class="header">
            <div style="float: left">
            <h4 class="top">Nueva Visita</h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="registro">
            <form action="nuevaVisita.php" method="post" class="form-disable"> <!--CLASE FORM-DISABLE PERMITE DESHABILITAR EL SUBMIT DESPUES DE DARLE CLICK-->
                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" placeholder="Asunto" maxlength="30" required>

                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título" maxlength="30" required>

                 <!--FALTA CREAR TABLA DE PROTOCOLOS, POR MIENTRAS SE QUEDA ASI-->
                <label for="protocolo">Protocolo</label>
                <select id="protocolo" name="protocolo">
                        <?php
                        while($row_prot = mysqli_fetch_array($result_prot)){
                            ?>
                            <option value="<?php echo $row_prot['ID']; ?>"><?php echo $row_prot['Nombre']?></option>
                            <?php
                        }
                        ?>
                </select>
                <label for="instrucciones">Instrucciones</label>
                <!--<input type="text" id="instrucciones" name="instrucciones" placeholder="Instrucciones de Visita" size="255" maxlength="255">-->
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255"></textarea>

                <label for="fechainicio">Fecha Inicio</label>
                <input type="date" id="fechainicio" name="fechainicio" required>

                <label for="horainicio">Hora Inicio</label>
                <input type="time" id="horainicio" name="horainicio" required>

                <label for="recurrente">Visita Recurrente</label>
                <select id="recurrente" name="recurrente" onchange="yesnoCheck(this);"> <!--Si se selecciona "sí" se muestran los campos de fechafin y horafin-->
                    <option value="0">No</option>
                    <option value="1">Si</option>
                </select>

                <div id="fecharecurrente" style="display: none;">

                <label>Fecha Fin</label>
                <input type="date" id="fechafin" name="fechafin">

                <label for="horafin">Hora Fin</label>
                <input type="time" id="horafin" name="horafin">
                </div>

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Registrar" data-submit-value="Procesando">
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
<script src="../scripts/jquery.min.js"></script>
<script src="../scripts/Global.js"></script>
</body>
</html>

