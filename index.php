<?php
include('login.php');
if(isset($_SESSION['login_user'])){
    header("location: main.php"); // Redirecting To Profile Page
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>eVisitor</title>
    <link rel="stylesheet" href="css/stylelogin.css">
</head>
<body>
<div class="wrapper fadeInDown">
    <div id="formContent">
        <!-- Tabs Titles -->
        <!--<h2 class="inactive underlineHover">Sign Up </h2>-->

        <!-- Icon -->
        <!--<div class="fadeIn first">
            <img src="http://danielzawadzki.com/codepen/01/icon.svg" id="icon" alt="User Icon" />
        </div>-->

        <!-- Login Form FORM ORDINARIA DE INICIO DE SESION -->
        <?php if(!isset($_SESSION['login_id'])){ ?>
            <h2 class="active"> Iniciar Sesión </h2>
        <form action="" method="post">
            <input type="text" id="name" class="fadeIn second" name="username" placeholder="Usuario" required>
            <input type="password" id="password" name="password" class="fadeIn third" placeholder="contraseña" required>
            <input type="submit" class="fadeIn fourth" name="submit" value="Inciar Sesión">
        </form>
        <?php } else{ //SOLO SE MUESTRA LA SIGUIENTE FORMA EN CASO DE QUE EL USUARIO TENGA CONTRASEÑA TEMPORAL?>
            <h2 class="active"> Cambiar Contraseña Temporal </h2>
            <form action="" method="post">
                <input type="hidden" name="action" value="passwordtemp">
                <input type="password" id="passwordtemp" class="fadeIn second" name="passwordtemp" placeholder="Contraseña Temporal" required>
                <input type="password" id="password" name="password" class="fadeIn third" placeholder="Nueva Contraseña" required>
                <input type="password" id="confirm_password" name="confirm_password" class="fadeIn third" placeholder="Confirmar Nueva Contraseña" required>
                <input type="submit" class="fadeIn fourth" name="submit" value="Registrar">
            </form>
        <?php }?>

        <!-- Remind Passowrd -->
        <!--<div id="formFooter">
            <a class="underlineHover" href="#">Forgot Password?</a>
        </div>-->

    </div>
</div>

<img  class="center">
<script src="scripts/passwords.js"></script>
</body>
</html>