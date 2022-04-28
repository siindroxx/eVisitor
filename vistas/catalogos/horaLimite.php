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

//OBTENER PUNTOS DE ACCESO
if(isset($_SESSION['tiempo'])){
    unset ($_SESSION['tiempo']);
}

if(!isset($_GET['msg'])) {
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
}

if(!isset($_SESSION['tiempo'])) {
    $sql_tiempo = "SELECT * FROM tiempo_gracia";
    $result_tiempo = mysqli_query($conn, $sql_tiempo);
    while ($row = mysqli_fetch_assoc($result_tiempo)) {
        $array = array(
            'ID' => $row['ID'],
            'Descripcion' => $row['Descripcion'],
            'Dias' => $row['Dias'],
            'Horas' => $row['Horas'],
            'Min' => $row['Minutos']
        );
        $_SESSION['tiempo'][$row['ID']] = $array;
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
    <script type="text/javascript" charset="utf8" src="../../JS/jquery-3.5.1.js"></script>
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
                <h4>Tiempo de llegada</h4>
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
        <table>
            <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Dias</th>
                <th scope="col">Horas</th>
                <th scope="col">Minutos</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($_SESSION['tiempo'] as $row){
                ?>
                <tr>
                    <td > <?php echo $row['Descripcion'] ?> </td>
                    <td><?php echo $row['Dias']?></td>
                    <td><?php echo $row['Horas'] ?></td>
                    <td><?php echo $row['Min'] ?></td>
                    <td>
                        <a href="editarHoraLimite.php?id=<?php echo $row['ID']?>"><button type="button" class="button button2"><i class="fas fa-edit"></i></button></a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
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
