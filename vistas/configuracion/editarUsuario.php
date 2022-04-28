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
if(!isset($_GET['msg'])) {
    if (isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
}
if(isset($_POST['submit'])){
    if(isset($_SESSION['message'])){
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
    if($_POST['submit']=="Eliminar"){
        $idusuario = $_POST['id'];
        $sql_delete = "UPDATE usuario SET Activo = 0 WHERE ID = '$idusuario'";
        if(!mysqli_query($conn, $sql_delete)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al eliminar usuario';
            ?>
            <script>
                window.location.href = "editarUsuario.php?msg=error"
            </script>
            <?php
        }
        else{
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Usuario eliminado exitosamente';
            ?>
            <script>
                window.location.href = "editarUsuario.php?msg=success"
            </script>
            <?php
        }
    }
    if($_POST['submit']=="Reset"){
        $idusuario = $_POST['id'];
        $password = generateRandomString(9, $conn);//contraseña temporal
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //CONTRASEÑA HASHED
        $sql_reset = "UPDATE usuario SET PasswordTemp = 1, Password = '$hashed_password' where ID = '$idusuario'";
        if(!mysqli_query($conn, $sql_reset)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al resetear contraseña de usuario';
            ?>
            <script>
                window.location.href = "editarUsuario.php?msg=error"
            </script>
            <?php
        }
        else{
            //OBTENER DATOS DE USUARIO
            $date = date('Y/m/d'); //FECHA ACTUAL
            $sql_u = "SELECT * FROM usuario WHERE ID = '$idusuario' and Activo = 1";
            $result_u= mysqli_query($conn, $sql_u);
            $row_u = mysqli_fetch_array($result_u);
            sendemailuser($row_u['Nombre'], $date, $row_u['Clave'] , $password, $row_u['Correo'], 2);
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Reseteo de contraseña de usuario exitoso';
            ?>
            <script>
                window.location.href = "editarUsuario.php?msg=success"
            </script>
            <?php
        }
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
            <h4>Editar Usuario</h4>
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
            <h1>Buscar Usuario</h1>
            <div style="height: 50px"></div>
            <form action="editarUsuario.php" method="post">

                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" maxlength="50" required>

                <input type="submit"  value="Buscar" name="submitsearch">
            </form>
        </div>
        <div style="height: 50px"></div>
    <?php
    //TABLA QUE MUESTRA LOS DATOS DEL USUARIO
    if(isset($_POST['submitsearch'])){
        $email = $_POST['email'];
        $sql_usuario = "SELECT * FROM usuario WHERE Correo = '$email' and Activo = 1";
        $result_usuario = mysqli_query($conn, $sql_usuario);
        if(mysqli_num_rows($result_usuario)==0){
        ?>
        <script>
            alert ("Buesqueda sin resultados")
            window.location.href = "editarUsuario.php"
        </script>
    <?php
        }
        else{
        $row_usuario = mysqli_fetch_array($result_usuario);
    ?>
        <table>
            <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Correo</th>
                <th scope="col">Reset Contraseña</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td > <?php echo $row_usuario['Nombre'] ?> </td>
                    <td><?php echo $row_usuario['Correo'] ?></td>
                    <td>
                        <a href="editarUsuario.php?id=<?php echo $row_usuario['ID']?>&action=reset&clave=<?php echo $row_usuario['Clave']?>"><button type="button" class="button button2" title="Reset Contraseña"><i class="fas fa-retweet"></i></i></button></a>
                    </td>
                    <td>
                        <a href="editaUsuario.php?id=<?php echo $row_usuario['ID']?>"><button type="button" class="button button2" title="Editar"><i class="fas fa-edit"></i></button></a>
                        <a href="editarUsuario.php?id=<?php echo $row_usuario['ID']?>&action=delete&clave=<?php echo $row_usuario['Clave']?>"><button type="button" class="button-danger button2"><i class="fas fa-trash-alt"></i></button></a>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php }
    }
    if(isset($_GET['action'])){
        //CONFIRMACION DE ELIMINACION DE USUARIO
        if($_GET['action'] == "delete"){
            $id = $_GET['id'];
            $clave = $_GET['clave'];
            ?>
                <div id="registro">
                    <form action="editarUsuario.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $id?>">
                        <label>¿Seguro que desea eliminar el usuario <strong><?php echo $clave ?></strong>?</label>
                        <div class="delete">
                        <input class="delete" type="submit"  value="Eliminar" name="submit">
                        </div>
                    </form>
                </div>
        <?php
        }
        //CONFIRMACION DE RESET USUARIO
        if($_GET['action'] == "reset"){
            $id = $_GET['id'];
            $clave = $_GET['clave'];
            ?>
            <div id="registro">
                <form action="editarUsuario.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id?>">
                    <label>¿Seguro que desea resetear contraseña del el usuario <strong><?php echo $clave ?></strong>?</label>
                    <label>El usuario recibira en su correo una nueva contraseña temporal.</label>
                    <div class="reset">
                        <input type="submit"  value="Reset" name="submit">
                    </div>
                </form>
            </div>
        <?php
        }
    }
    ?>
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
