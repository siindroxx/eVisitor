<?php
include('../sql/conexion.php');
include('../funciones/email.php');
include('../funciones/formatofecha.php');
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../index.php"); // Redirecting To Home Page
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
//OBTENER ID DE LA VISITA Y CODIGO DE VISITA
if(isset($_GET['id'])){
    $idVisita = $_GET['id'];
    $sql_visita = "SELECT * from visita where ID = '$idVisita'";
    $result_visita = mysqli_query($conn, $sql_visita);
    $row_visita = mysqli_fetch_array($result_visita);
    $sql_visitantes = "SELECT * FROM visitante WHERE IDVisita = '$idVisita' and Activo = 1";
    $result_visitantes = mysqli_query($conn, $sql_visitantes);
}
else{
    if (isset($_POST['submit'])) {
        $id = $_POST['idvisita']; //ID DE LA VISITA
        $codigo = $_POST['codigovisita']; //CODIGO DE LA VISITA
        $fechavisita = $_POST['fechavisita'];//FECHA DE LA VISITA
        $horainicio = $_POST['horainicio']; //HORA INICIO DE VISITA
        $asunto = $_POST['asunto'];//ASUNTO DE LA VISITA, VIENE DE LA TABLA VISITA Y SE USA COMO ASUNTO DEL CORREO ELECTRONICO
        $nombre = $_POST['nombre']; //NOMBRE DEL VISITANTE
        $email = $_POST['email']; //EMAIL VISITANTE
        $compania = $_POST['compania']; //COMPANIA VISITANTE
        $sql_visitante = "INSERT INTO visitante (IDVisita, Nombre, Email, Compania) VALUES('$id', '$nombre', '$email', '$compania')"; //INSERTA EL VISITANTE EN LA BD
        if(!mysqli_query($conn, $sql_visitante)){
            if(isset($_SESSION['message'])){
                unset($_SESSION['message']);
            }
            if(isset($_SESSION['messagecontent'])){
                unset($_SESSION['messagecontent']);
            }
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al registrar visitante';
            ?>
            <script>
                window.location.href = "nuevoVisitante.php?id=<?php echo $id?>&msg=error"
            </script>
            <?php
        }
        else{
            //ENVIAR EMAIL DE VISITA
            $fechalarga = formatofecha($fechavisita, $horainicio); //CONVIERTE LA FECHA EN NUMERO A FECHA LARGA CON NOMBRE DE DIA Y MES EJ: (2021/04/05 -> LUNES 05 DE ABRIL DEL 2021)
            $tipo = 1; //TIPO DE EMAIL - 1 EMAIL DE NUEVA VISITA
            sendemail($nombre, $fechalarga, $asunto, $codigo, $email, $tipo); //FUNCION PARA ENVIAR EL EMAIL AL VISITANTE
            if(isset($_SESSION['message'])){
                unset($_SESSION['message']);
            }
            if(isset($_SESSION['messagecontent'])){
                unset($_SESSION['messagecontent']);
            }
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Visitante registrado exitosamente';
            ?>
            <script>
                window.location.href = "nuevoVisitante.php?id=<?php echo $id?>&msg=success"
            </script>
            <?php
        }
    }
    else{
        ?>
        <script>
            alert ("Error al obtener id de visita")
            window.location.href = "../index.php"
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
            <h4 class="top">Nuevo Visitante - Visita: <?php echo $row_visita['Asunto']?></h4>
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
        <div id="registro">
            <form action="nuevoVisitante.php" method="post">
                <label for="nombre">Nombre Visitante</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre Visitante" maxlength="50" required>

                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" maxlength="50" required>

                <label for="compania">Compañia</label>
                <input type="text" id="compania" name="compania" placeholder="Compañia"  maxlength="30" required>

                <input type="hidden" name="idvisita" value="<?php echo $idVisita?>"> <!--ID DE LA VISITA-->
                <input type="hidden" name="codigovisita" value="<?php echo $row_visita['CodigoVisita']?>"><!--CODIGO DE LA VISITA-->
                <input type="hidden" name="fechavisita" value="<?php echo $row_visita['FechaVisita']?>"><!--FECHA INICIO DE VISITA-->
                <input type="hidden" name="horainicio" value="<?php echo $row_visita['HoraInicio']?>"><!--HORA INICIO DE VISITA-->
                <input type="hidden" name="asunto" value="<?php echo $row_visita['Asunto']?>"><!--ASUNTO DE LA VISITA-->

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Registrar">
            </form>
        </div>
        <div style="height: 50px" id="registro">
            <h3>Visitantes Registrados</h3>
            <table id="example" class="display">
                <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Compañia</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while($row_visitantes = mysqli_fetch_array($result_visitantes)){
                    ?>
                    <tr>
                        <td > <?php echo $row_visitantes['Nombre'] ?> </td>
                        <td > <?php echo $row_visitantes['Email'] ?> </td>
                        <td><?php echo $row_visitantes['Compania'] ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
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