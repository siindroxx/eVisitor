<?php
include('../sql/conexion.php');
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../index.php"); // Redirecting To Home Page
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

$sql_prot = "SELECT * FROM protocolo WHERE Activo = 1";
$result_prot =  mysqli_query($conn, $sql_prot);
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
            <li><a href="../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="nuevoProt.php"><i class="fas fa-plus-square"></i></i>Nuevo Protocolo</a></li>
            <li><a href="editarProt.php"><i class="fas fa-edit"></i>Editar Protocolo</a></li>

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
            <h4 class="top">Editar Protocolo</h4>
            </div>
            <div style="float: right" class="logout">
                <div style="float: left">
                    <h3><a href="nuevoProt.php"><i class="fas fa-plus-square"></i>Nuevo</a></h3>
                </div>
                <!--<div style="float: right">
                    <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
                </div>-->
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
                <!--<th scope="col">Instrucciones</th>-->
                <!--<th scope="col">Tipo</th>-->
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while($row = mysqli_fetch_array($result_prot)){
                ?>
                <tr>
                    <td > <?php echo $row['Nombre'] ?> </td>
                   <!-- <td><?php if($row['Tipo'] == 1){echo "Visita";}else{echo "Mensajería";} ?></td>-->
                    <td>
                        <a href="editaProt.php?id=<?php echo $row['ID']?>"><button type="button" class="button button2"><i class="fas fa-edit"></i></button></a>
                        <a href="nuevoNot.php?id=<?php echo $row['ID']?>"><button type="button" class="button button2"><i class="fas fa-plus-square"></i></button></a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="../JS/table.js"></script>
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