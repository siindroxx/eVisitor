<?php
include ("../../sql/conexion.php");
include ("../../funciones/codegenerator.php");
include ("emailusuario.php");
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../../index.php"); // Redirecting To Home Page
    }
}
//OBTIENE PUNTO DE ACCESO
$sql_puntoacceso = "SELECT * FROM punto_acceso WHERE Activo = 1";
$result_puntoacceso = mysqli_query($conn, $sql_puntoacceso);

if(!isset($_GET['msg'])) {
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
}

if (isset($_POST['submit'])) {
    if(isset($_SESSION['message'])){
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
    $clave = $_POST['clave']; //CLAVE USUARIO
    $nombre = $_POST['nombre'];//NOMBRE USUARIO
    $email = $_POST['email']; //EMAIL USUARIO
    $tipo = $_POST['tipo']; //TIPO DE USUARIO
    $puntoacceso = $_POST['puntoacceso']; //PUNTO DE ACCESO
    $password = generateRandomString(9, $conn);//contraseña temporal
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); //CONTRASEÑA HASHED
    $idusuario = $_SESSION['login_id']; //id de usuario que da de alta nuevo usuario
    $date = date('Y/m/d'); //FECHA ACTUAL
    //CHECAR QUE NOMBRE DE USUARIO NO EXISTA
    $sql_check = "SELECT Clave, Correo FROM usuario WHERE Clave = '$clave' or Correo = '$email' and Activo = 1";
    $result_check= mysqli_query($conn, $sql_check);
    if(mysqli_num_rows($result_check)==0){
        $sql_usuario = "INSERT INTO usuario (IDPuntoAcceso, Clave, Nombre, Correo, Password, PasswordTemp, FechaAlta, UsuarioAltaID, Activo, Sistema) 
                        VALUES('$puntoacceso','$clave', '$nombre', '$email', '$hashed_password', 1, '$date', '$idusuario', 1, '$tipo')"; //INSERTA EL USUARIO EN BD
        if(!mysqli_query($conn, $sql_usuario)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al registrar usuario';
        ?>
        <script>
            window.location.href = "nuevoUsuario.php?msg=error"
        </script>
        <?php
        }
        else{
            sendemailuser($nombre, $date, $clave , $password, $email, 1);
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Usuario registrado exitosamente';
            ?>
            <script>
                window.location.href = "nuevoUsuario.php?msg=success"
            </script>
            <?php
        }
    }
    else{ //SI USUARIO EXISTE CANCELA
        $_SESSION['message'] = 'error';
        $_SESSION['messagecontent'] = 'Error: Clave de usuario o correo electronico ya existe.';
        ?>
        <script>
            window.location.href = "nuevoUsuario.php?msg=error"
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
    <script type="text/javascript" charset="utf8" src="../../JS/jquery-3.5.1.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h2>eVisitor</h2>
        <ul>
            <li><a href="../../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="nuevoUsuario.php"><i class="fas fa-plus-square"></i>Registro Usuario</a></li>
            <li><a href="editarUsuario.php"><i class="fas fa-user-edit"></i>Editar Usuario</a></li>
            <!--<li><a href="#"><i class="fas fa-address-card"></i>About</a></li>
            <li><a href="#"><i class="fas fa-project-diagram"></i>portfolio</a></li>
            <li><a href="#"><i class="fas fa-blog"></i>Blogs</a></li>
            <li><a href="#"><i class="fas fa-address-book"></i>Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-map-pin"></i>Map</a></li>-->
        </ul>
        <!--<div class="social_media">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a></li>
        </div>-->
    </div>
    <div class="main_content">
        <div class="header">
            <div style="float: left">
            <h4>Nuevo Usuario</h4>
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
        <div id="registro">
            <form action="nuevoUsuario.php" method="post">
                <label for="clave">Usuario</label>
                <input type="text" id="clave" name="clave" placeholder="Usuario" maxlength="25" required>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" maxlength="50" required>

                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" maxlength="50xx" required>

                <label for="tipo">Tipo</label> <!--DEFINE EN DONDE SE APLICA - 1 Oficina | 2 Caseta-->
                <select id="tipo" name="tipo">
                    <option value="1">Oficina</option>
                    <option value="2">Caseta</option>
                </select>

                <label for="puntoacceso">Punto Acceso</label> <!--DEFINE EN DONDE SE APLICA - 1 Oficina | 2 Caseta-->
                <select id="puntoacceso" name="puntoacceso">
                   <?php while($row_puntoacceso = mysqli_fetch_assoc($result_puntoacceso)){?>
                       <option value="<?php echo $row_puntoacceso['ID'] ?>"> <?php echo $row_puntoacceso['Nombre'] ?> </option>
                   <?php } ?>
                </select>

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Registrar">
            </form>
        </div>
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
