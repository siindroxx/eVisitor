<?php
include ('../sql/conexion.php');
include ('../funciones/email.php');
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
        $IDVisita = $row_visita['ID']; //ID DE LA VISITA
        $IDProtocolo = $row_visita['IDProtocolo']; //ID DEL PROTOCOLO DE LA VISITA
        $IDUsuario = $row_visita['IDUsuario']; //ID DEL USUARIO CREADOR DE LA VISITA
        //OBTIENE DATOS DE PROTOCOLO
        $sql_prot = "SELECT * FROM protocolo WHERE ID = '$IDProtocolo' ";
        $result_prot = mysqli_query($conn, $sql_prot);
        $row_prot = mysqli_fetch_array($result_prot); //DATOS DE PROTOCOLO
        //OBTIENE DATOS DE LOS VISITANTES
        $sql_visitante = "SELECT ID, Nombre, Email, Compania FROM visitante WHERE IDVisita = '$IDVisita' and EntradaCaseta is not null and CodigoNFC is null and EntradaRecepcion is null and SalidaRecepcion is null and Activo = 1";
        $result_visitante = mysqli_query($conn, $sql_visitante); //DATOS DE VISITANTES SIN ADMITIR
        $sql_visadmincaseta = "SELECT ID, Nombre, Email, Compania, EntradaCaseta FROM visitante WHERE IDVisita = '$IDVisita' and EntradaCaseta is not null and Activo = 1";
        $aux_visitante = mysqli_query($conn, $sql_visadmincaseta); //DATOS DE VISITANTES ADMITIDOS CASETA
        $sql_visadmin = "SELECT ID, Nombre, Email, Compania, EntradaRecepcion FROM visitante WHERE IDVisita = '$IDVisita' and EntradaCaseta is not null and EntradaRecepcion is not null and Activo = 1";
        $aux2_visitante = mysqli_query($conn, $sql_visadmin); //DATOS DE VISITANTES ADMITIDOS RECEPCION
        //OBTIENE DATOS DE NOTIFICADOS
        $sql_notificado = "SELECT u.* FROM notificado n 
                       INNER JOIN usuario u ON n.IDUsuario = u.ID
                       WHERE n.IDProt =  = '$IDProtocolo' and n.Activo = 1";
        $result_notificado = mysqli_query($conn, $sql_notificado); //DATOS DE NOTIFICADOS
        //OBTIENE DATOS DE USUARIO CREADOR DE VISITA
        $sql_usuario = "SELECT Nombre, Correo FROM usuario WHERE ID = '$IDUsuario'";
        $result_usuario = mysqli_query($conn, $sql_usuario);
        $row_usuario = mysqli_fetch_array($result_usuario); //DATOS DE USUARIO CREADOR DE VISITA
    }
}
if (isset($_POST['submit'])){
    $QRVisita = $_POST['codigo']; //CODIGO DE LA VISITA
    $idvisitante = $_POST['visitante']; //ID DEL VISITATNE
    $codigoacceso = $_POST['codigoacceso'];//CODIGO DE ACCESO NFC
    $fecha = date('Y/m/d h:i:s', time());
    $sql_visitante2 = "SELECT ID, Nombre, Email, Compania FROM visitante WHERE ID = '$idvisitante'";
    $sql_updateVis = "UPDATE visitante SET CodigoNFC = '$codigoacceso', EntradaRecepcion = '$fecha' WHERE ID = '$idvisitante'";
    if(!mysqli_query($conn, $sql_updateVis)){
        ?>
        <script>
            alert ("No se pudo actualizar el visitante")
            window.location.href = "admisionVisita.php?QR=<?php echo $QRVisita?>"
        </script>
        <?php
    }
    else{
        //ENVIAR EMAIL DE ADMISIO
        $result_visitante2 = mysqli_query($conn, $sql_visitante2); //DATOS DE VISITANTE SIENDO ADMITIDO
        $row_visitante3 = mysqli_fetch_array($result_visitante2);
        $name = $row_visitante3['Nombre'];
        $email = $row_visitante3['Email'];
        $tipo = 2; //DEFINE EL TIPO DE EMAIL || TIPO 2 ES PARA ADMISION DE VISITA
        $titulo = "";
        $codigo ="";
        sendemail($name, $fecha, $titulo, $codigo, $email, $tipo);
        ?>
        <script>
            window.location.href = "admisionVisita.php?QR=<?php echo $QRVisita?>"
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
                <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="admision" style="float: left; width: 850px;">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="admisionVisita.php" method="post">
                <input type="hidden" value="<?php echo $IDVisita?>" name="idvisita">

                <label for="fechainicio">Fecha Inicio</label>
                <input type="date" id="fechainicio" name="fechainicio" value="<?php echo $row_visita['FechaVisita'];?>" disabled>

                <label for="horainicio">Hora Inicio</label>
                <input type="time" id="horainicio" name="horainicio" value="<?php echo $row_visita['HoraInicio'];?>"  disabled>

                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto"  value="<?php echo $row_visita['Asunto'];?>" disabled>

                <label for="protocolo">Protocolo</label>
                <input type="text" id="protocolo" name="protocolo" value="<?php echo $row_prot['Nombre'];?>" disabled>

                <label for="instrucciones">Instrucciones Visita</label>
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255" disabled><?php echo $row_visita['Instrucciones'];?></textarea>

                <label for="instruccionesprot">Instrucciones Protocolo</label>
                <textarea id="instruccionesprot" name="instruccionesprot" rows="4" cols="190" maxlength="255" disabled><?php echo $row_prot['Instrucciones'];?></textarea>
            </form>
        </div>
        <div id="admision" style="float: right; width: 850px;">
            <!--EN LA FORM SE MUESTRAN LOS VALORES OBTENIDOS DEL QUERY CON EL ID DE LA VISITA-->
            <form action="admisionVisita.php" method="post">
                <input type="hidden" name="codigo" value="<?php echo $QRVisita?>">

                <label for="visitante">Visitante</label>
                <select id="visitante" name="visitante">
                    <?php
                    while($row_visitante = mysqli_fetch_array($result_visitante)){
                        ?>
                        <option value="<?php echo $row_visitante['ID']; ?>"> <?php echo $row_visitante['Nombre']?> </option>
                        <?php
                    }
                    ?>
                </select>

                <label for="codigoacceso">Acceso</label>
                <input type="password" id="codigoacceso" name="codigoacceso" required>

                <input type="submit"  value="Registrar" name="submit">
            </form>
        </div>
        <div style="height: 700px"></div>
        <div id="admision" style=" float: left; width: 850px">
            <h3>Admitidos Caseta</h3>
            <table>
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Fecha/Hora</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while($row_visitante2 = mysqli_fetch_array($aux_visitante)){
                    $date = date_create($row_visitante2['EntradaCaseta']);//VARIABLE AUXILIAR DE FECHA
                    $printdate = date_format($date, "d-m-Y h:i:00 A"); //VARIABLE PARA IMPRIMIR FECHA CON FORMATO d-m-YYYY h:m:s a.m/p.m.
                    ?>
                    <tr>
                        <td > <?php echo $row_visitante2['Nombre'] ?> </td>
                        <td><?php echo $printdate ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <div id="admision" style=" float: right; width: 850px">
            <h3>Admitidos Recepcion</h3>
            <table>
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Fecha/Hora</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while($row_visitante3 = mysqli_fetch_array($aux2_visitante)){
                    $date = date_create($row_visitante3['EntradaRecepcion']); //VARIABLE AUXILIAR DE FECHA
                    $printdate = date_format($date, "d-m-Y h:i:00 A"); //VARIABLE PARA IMPRIMIR FECHA CON FORMATO d-m-YYYY h:m:s a.m/p.m.
                    ?>
                    <tr>
                        <td > <?php echo $row_visitante3['Nombre'] ?> </td>
                        <td><?php echo $printdate ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
