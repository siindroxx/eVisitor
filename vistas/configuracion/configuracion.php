<?php
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../../index.php"); // Redirecting To Home Page
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
            <h4>Configuración</h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
    </div>
</div>
</body>
</html>
