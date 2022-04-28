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
/*Obtener fecha y hora actuales. No se puede editar una visita que sea mayor a estos valores*/
$fecha= date('Y/m/d');
$hora= date('h:i:sa');
//OBTENER VISITAS EDITABLES
$sql_visitas = "SELECT v.*, p.Nombre as Protocolo from visita v 
                inner join protocolo p on v.IDProtocolo = p.ID
                where IDUsuario = '" . $_SESSION['login_id'] . "' AND ('$fecha' <= FechaVisita || (Recurrente = 1 AND '$fecha' <= FechaFin)) AND v.Activo = 1  ORDER BY FechaVisita ASC, HoraInicio ASC";
$result_visitas = mysqli_query($conn, $sql_visitas);
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
                <h4>Editar Visita</h4>
            </div>
            <div style="float: right" class="logout">
                <div style="float: left">
                    <h3><a href="nuevaVisita.php"><i class="fas fa-plus-square"></i>Nuevo</a></h3>
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
        <table id="example" class="display">
            <thead>
            <tr>
                <th scope="col">Titulo</th>
                <th scope="col">Protocolo</th>
                <th scope="col">Fecha Visita</th>
                <th scope="col">Hora Visita</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while($row = mysqli_fetch_array($result_visitas)){
                ?>
                <tr>
                    <td > <?php echo $row['Titulo'] ?> </td>
                    <td><?php echo $row['Protocolo']?></td>
                    <td><?php echo $row['FechaVisita'] ?></td>
                    <td><?php echo $row['HoraInicio'] ?></td>
                    <td>
                        <a href="editaVisita.php?id=<?php echo $row['ID']?>"><button type="button" class="button button2"><i class="fas fa-edit"></i></button></a>
                        <a href="nuevoVisitante.php?id=<?php echo $row['ID']?>"><button type="button" class="button button2"><i class="fas fa-plus-square"></i></button></a>
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