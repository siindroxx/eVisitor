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
if(isset($_GET['id'])) {
    //OBTIENE LOS DATOS DEL PROTOCOLO
    $idtiempo = $_GET['id'];
    $desc = $_SESSION['tiempo'][$idtiempo]['Descripcion'];
    $dias = $_SESSION['tiempo'][$idtiempo]['Dias'];
    $horas = $_SESSION['tiempo'][$idtiempo]['Horas'];
    $min = $_SESSION['tiempo'][$idtiempo]['Min'];
}
else{
    if (isset($_POST['submit'])) {
        if(isset($_SESSION['message'])){
            unset($_SESSION['message']);
        }
        if(isset($_SESSION['messagecontent'])){
            unset($_SESSION['messagecontent']);
        }
        $id = $_POST['idtiempo'];
        $dias = $_POST['dias'];
        $horas = $_POST['horas'];
        $min = $_POST['min'];
        $sql_tiempo = "UPDATE tiempo_gracia
                           SET Dias = '$dias', Horas ='$horas', Minutos = '$min'
                           WHERE ID = '$id'";
        if(!mysqli_query($conn, $sql_tiempo)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al actualizar tiempo';
            ?>
            <script>
                window.location.href = "horaLimite.php?msg=error"
            </script>
            <?php
        }
        else{
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Tiempo actualizado exitosamente';
            ?>
            <script>
                window.location.href = "horaLimite.php?msg=success"
            </script>
            <?php
        }
    }
    else{
        $_SESSION['message'] = 'error';
        $_SESSION['messagecontent'] = 'Error al obtener id de tiempo';
        ?>
        <script>
            window.location.href = "horaLimite.php?msg=error"
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
                <h4 class="top">Editar: <strong> <?php echo $desc ?></strong></h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="registro">
            <form action="editarHoraLimite.php" method="post">

                <input type="hidden" value="<?php echo $idtiempo?>" name="idtiempo">

                <label for="dias">Dias</label>
                <input type="number" id="dias" name="dias" value="<?php echo $dias; ?>"required>

                <label for="dias">Horas</label>
                <input type="number" id="horas" name="horas" value="<?php echo $horas; ?>"required>

                <label for="dias">Minutos</label>
                <input type="number" id="min" name="min" value="<?php echo $min; ?>"required>


                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Registrar">
            </form>
        </div>
    </div>
</div>
</body>
</html>
