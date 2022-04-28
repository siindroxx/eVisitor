<?php
include ("../../sql/conexion.php");
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../../index.php"); // Redirecting To Home Page
    }
}
if(isset($_GET['id'])){
    //DATOS DE USUARIO
    $idusuario = $_GET['id'];
    $sql_usuario = "SELECT * from usuario where ID = '$idusuario'";
    $result_usuario = mysqli_query($conn, $sql_usuario);
    $row_usuario = mysqli_fetch_array($result_usuario);

    //OBTIENE PUNTO DE ACCESO
    $sql_puntoacceso = "SELECT * FROM punto_acceso WHERE Activo = 1";
    $result_puntoacceso = mysqli_query($conn, $sql_puntoacceso);
    //ARREGLO DE PUNTOS DE ACCESO
    while($row_puntoacceso = mysqli_fetch_assoc($result_puntoacceso)){
        $array = array (
                'ID' => $row_puntoacceso['ID'],
                'Nombre' => $row_puntoacceso['Nombre']
        );
        $array_puntoacceso[$row_puntoacceso['ID']] = $array;
    }
}
else{
    if (isset($_POST['submit'])) {
        if(isset($_SESSION['message'])){
            unset($_SESSION['message']);
        }
        if(isset($_SESSION['messagecontent'])){
            unset($_SESSION['messagecontent']);
        }
        $iduser = $_POST['idusuario'];
        $nombre = $_POST['nombre'];//NOMBRE USUARIO
        $email = $_POST['email']; //EMAIL USUARIO
        $tipo = $_POST['tipo']; //TIPO DE USUARIO
        $puntoacceso = $_POST['puntoacceso'];
        $sql_update= "UPDATE usuario SET IDPuntoAcceso = '$puntoacceso', Nombre = '$nombre', Correo = '$email', Sistema = '$tipo' WHERE ID = '$iduser' "; //ACTUALIZA  EL USUARIO EN BD
        if(!mysqli_query($conn, $sql_update)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al actualizar usuario';
            ?>
            <script>
                window.location.href = "editarUsuario.php?msg=error"
            </script>
            <?php
        }
        else{
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Usuario actualizado exitosamente';
            ?>
            <script>
                window.location.href = "editarUsuario.php?msg=success"
            </script>
            <?php
        }
    }
    else{
        $_SESSION['message'] = 'error';
        $_SESSION['messagecontent'] = 'Error al obtener id de usuario';
        ?>
        <script>
            window.location.href = "editarUsuario.php?msg=error"
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
            <h4>Editar Usuario</h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <div id="registro">
            <form action="editaUsuario.php" method="post">
                <input type="hidden" value="<?php echo $row_usuario['ID'] ?>" name="idusuario">

                <label for="clave">Usuario</label>
                <input type="text" id="clave" placeholder="Usuario" maxlength="15" value="<?php echo $row_usuario['Clave'] ?>" disabled>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" maxlength="50" value="<?php echo $row_usuario['Nombre'] ?>" required>

                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" maxlength="50" value="<?php echo $row_usuario['Correo']?>" required>

                <label for="tipo">Tipo</label> <!--DEFINE EN DONDE SE APLICA - 1 Oficina | 2 Caseta-->
                <select id="tipo" name="tipo">

                    <option value="<?php echo $row_usuario['Sistema']?>>"><?php if($row_usuario['Sistema'] == 1) {echo "Oficina";}else{echo "Caseta";}?></option>
                    <option value="<?php if($row_usuario['Sistema'] == 1) {echo 2;}else{echo 1;}?>"><?php if($row_usuario['Sistema'] == 1) {echo "Caseta";}else{echo "Oficina";}?></option>
                </select>

                <label for="puntoacceso">Punto Acceso</label>
                <select id="puntoacceso" name="puntoacceso">
                   <?php foreach ($array_puntoacceso as $values){ //MUESTRA EL SELECT DE PUNTO DE ACCESO, PRIMERO MUESTRA EL PA DEL USUARIO Y DESPUES LOS DEMAS
                       if ($values['ID'] == $row_usuario['IDPuntoAcceso']){?>
                           <option value="<?php echo $row_usuario['IDPuntoAcceso']?>"> <?php echo $values['Nombre'] ?></option>
                   <?php
                       }
                   }
                   foreach ($array_puntoacceso as $values){
                    if ($values['ID'] == $row_usuario['IDPuntoAcceso']){
                        continue;
                    }
                    else{?>
                        <option value="<?php echo $values['ID']?>"> <?php echo $values['Nombre'] ?></option>
                    <?php }
                   }
                   ?>

                </select>

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Actualizar">
            </form>
        </div>
    </div>
</div>
</body>
</html>