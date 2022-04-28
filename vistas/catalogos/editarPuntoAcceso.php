<?php
include('../../sql/conexion.php');
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../../index.php"); // Redirecting To Home Page
    }
}

if(isset($_GET['action'])){
    //OBTENER INFO DE TABLA ORIGEN
    $sql_origen = "SELECT * FROM cat_origen WHERE Activo = 1"; //INFORMACION DE ORIGEN
    $result_origen = mysqli_query($conn, $sql_origen);
    while($row_origen = mysqli_fetch_assoc($result_origen)){
        $array = array(
            'ID' => $row_origen['ID'],
            'Desc' => $row_origen['Descripcion'],
        );
        $array_origen[$row_origen['ID']]  = $array;
    }
    //SI ES UN PUNTO ACCESO NUEVO
    if($_GET['action'] == 'nuevo'){
        $action = 'nuevo';
        $ID = '';
        $IDOrigen = 0;
        $puntoacceso = '';
        $desc = '';
    }
    //SI SE EDITA UN PUNTO ACCESO
    if($_GET['action'] == 'editar'){
        $action = 'editar';
        $ID = $_GET['ID']; //ID DE PUNTO DE ACCESO
        $IDOrigen = $_SESSION['puntoacceso'][$ID]['IDOrigen']; //ID DE ORIGEN CASETA O RECEPCION
        $puntoacceso = $_SESSION['puntoacceso'][$ID]['PuntoAcceso']; //NOMBRE DE PUNTO DE ACCESO
        $desc = $_SESSION['puntoacceso'][$ID]['Descripcion'];//DESCRUIPCION DE PUNTO DE ACCESO
    }
}
else{
    if(isset($_POST['submit'])){
        if(isset($_SESSION['message'])){
            unset($_SESSION['message']);
        }
        if(isset($_SESSION['messagecontent'])){
            unset($_SESSION['messagecontent']);
        }
        //PARA PUNTOS DE ACCESO NUEVOS
        if($_POST['action'] == 'nuevo'){
            $puntoacceso = $_POST['puntoacceso'];
            $desc = $_POST['desc'];
            $IDOrigen = $_POST['tipo'];
            $sql_insert = "INSERT INTO punto_acceso (IDOrigen, Nombre, Descripcion, Activo) VALUES ('$IDOrigen', '$puntoacceso', '$desc', 1)";
            if(!mysqli_query($conn, $sql_insert)){
                $_SESSION['message'] = 'error';
                $_SESSION['messagecontent'] = 'Error al registrar punto de acceso';
                ?>
                <script>
                    window.location.href = "puntoAcceso.php?msg=error"
                </script>
                <?php
            }
            else{
                $_SESSION['message'] = 'success';
                $_SESSION['messagecontent'] = 'Punto de acceso registrado exitosamente';
                ?>
                <script>
                    window.location.href = "puntoAcceso.php?msg=success"
                </script>
                <?php
            }
        }
        //PARA EDITAR UN PUNTO DE ACCESO
        if($_POST['action'] == 'editar'){
            $ID = $_POST['ID']; //ID DEL PUNTO DE ACCESO A EDITAR
            $puntoacceso = $_POST['puntoacceso'];
            $desc = $_POST['desc'];
            $IDOrigen = $_POST['tipo'];
            $fecha = $fecha = date('Y/m/d H:i:s', time()); //FECHA EN QUE SE HACE MODIFICACION
            $IDUsuario =  $_SESSION['login_id']; //ID DE USUARIO QUE MODIFICA
            $sql_update = "UPDATE punto_acceso SET IDOrigen = '$IDOrigen', Nombre = '$puntoacceso', Descripcion = '$desc',
                           IDUsuarioModificacion = '$IDUsuario', FechaModificacion = '$fecha'
                           WHERE ID = '$ID' ";
            if(!mysqli_query($conn, $sql_update)){
                $_SESSION['message'] = 'error';
                $_SESSION['messagecontent'] = 'Error al actualizar punto de acceso';
                ?>
                <script>
                    window.location.href = "puntoAcceso.php?msg=error"
                </script>
                <?php
            }
            else{
                $_SESSION['message'] = 'success';
                $_SESSION['messagecontent'] = 'Punto de acceso actualizado exitosamente';
                ?>
                <script>
                    window.location.href = "puntoAcceso.php?msg=success"
                </script>
                <?php
            }
        }
    }//SI NO SE CUMPLE NINGUNA CONDICION
    else{
        ?>
        <script>
            alert ("Error")
            window.location.href = "puntoAcceso.php"
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
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../scripts/fontawesome.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="puntoAcceso.php"><i class="fas fa-door-open"></i>Punto Acceso</a></li>
            <li><a href="horaLimite.php"><i class="fas fa-clock"></i>Tiempo Llegada</a></li>
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
                <h4 class="top">Punto de Acceso: <?php echo $action?></h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="registro">
            <form action="editarPuntoAcceso.php" method="post">

                <input type="hidden" value="<?php echo $ID?>" name="ID">
                <input type="hidden" value="<?php echo $action?>" name="action">

                <label for="puntoacceso">Nombre</label>
                <input type="text" id="puntoacceso" name="puntoacceso" value="<?php echo $puntoacceso?>"required maxlength="25">

                <label for="desc">Descripcion</label>
                <input type="text" id="desc" name="desc" value="<?php echo $desc?>"required maxlength="50">

                <label for="tipo">Origen</label>
                <select id="tipo" name="tipo">
                    <?php if($action == 'editar'){ ?>
                    <option value="<?php echo $IDOrigen; ?>"><?php echo $array_origen[$IDOrigen]['Desc']?></option>
                    <?php
                    }
                    foreach ($array_origen as $values) {
                        if($values['ID'] != $IDOrigen){?>
                            <option value="<?php echo $values['ID']; ?>"><?php echo $values['Desc'] ?></option>
                        <?php }
                    }
                    ?>
                </select>

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Registrar">
            </form>
        </div>
    </div>
</div>
</body>
</html>
