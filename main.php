<?php
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: index.php"); // Redirecting To Home Page
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="scripts/fontawesome.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h2>eVisitor</h2>
        <ul>
            <?php if($_SESSION['tipo'] == 1){ ?>
                <li><a href="vistas/admitirVisita.php"><i class="fas fa-address-card"></i>Admitir Visita</a></li>
                <li><a href="vistas/registroSalida.php"><i class="fas fa-door-open"></i> Registrar Salida</a></li>
                <li><a href="vistas/registroVisita.php"><i class="fas fa-user-friends"></i>Visita</a></li>
                <li><a href="vistas/registroProt.php"><i class="fas fa-book"></i>Protocolos</a></li>
                <li><a href="vistas/catalogos/catalogos.php"><i class="fas fa-book"></i>Catálogos</a></li>
                <li><a href="vistas/reportes/reportes.php"><i class="fas fa-book"></i>Reportes</a></li>
                <li><a href="vistas/configuracion/configuracion.php"><i class="fas fa-cogs"></i>Configuración</a></li>
            <?php } ?>
            <?php if($_SESSION['tipo'] == 2){ ?>
                <li><a href="vistas/caseta/admitirVisita.php"><i class="fas fa-address-card"></i>Admitir Visita</a></li>
                <li><a href="vistas/caseta/registroSalida.php"><i class="fas fa-door-open"></i> Registrar Salida</a></li>
            <?php } ?>
           <!-- <li><a href="#"><i class="fas fa-project-diagram"></i>portfolio</a></li>
            <li><a href="#"><i class="fas fa-blog"></i>Blogs</a></li>
            <li><a href="#"><i class="fas fa-address-book"></i>Contact</a></li>
            <li><a href="logout.php"><i class="fas fa-map-pin"></i>Map</a></li>-->
        </ul>
        <div class="social_media">
            <!--<a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>-->
            <!--<li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a></li>-->
        </div>
    </div>
    <div class="main_content">
        <div class="header">
            <div style="float: left">
                <h4>Bienvenido <?php echo $_SESSION['login_user']; ?></h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
    </div>
</div>
</body>
</html>
