<?php
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../index.php"); // Redirecting To Home Page
    }
}
if(isset($_SESSION['admision'])){
    unset($_SESSION['admision']);
}
if(isset($_SESSION['visitante'])){
    unset($_SESSION['visitante']);
}
if(isset($_SESSION['QRVisita'])) {
    unset($_SESSION['QRVisita']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../scripts/fontawesome.js"></script>
    <script type="text/javascript" src="../scripts/instascan.min.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h2 href="../main.php">eVisitor</h2>
        <ul>
            <li><a href="../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <!-- <li><a href="#"><i class="fas fa-project-diagram"></i>portfolio</a></li>
             <li><a href="#"><i class="fas fa-blog"></i>Blogs</a></li>
             <li><a href="#"><i class="fas fa-address-book"></i>Contact</a></li>
             <li><a href="logout.php"><i class="fas fa-map-pin"></i>Map</a></li>-->
        </ul>
        <div class="social_media">
            <!--<a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>-->
        </div>
    </div>
    <div class="main_content">
        <div class="header">
            <div style="float: left">
            <h4>Admitir Visita</h4>
            </div>
            <div style="float: right" class="logout">
                <h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
            </div>
        </div>
        <?php if(!isset($_GET['action'])){?>
        <div id="registro">
            <a href="admitirVisita.php?action=leerqr"><button type="button" class="button button3"><i class="fas fa-qrcode"></i>  Leer QR</button></a>
        </div>
        <div style="height: 50px"></div>
        <div id="registro">
            <a href="admitirVisita.php?action=entqr"><button type="button" class="button button3"><i class="fas fa-pen"></i> Ingresar código manualmente</button></a>
        </div>
        <?php }
        else {
            if($_GET['action']=="leerqr"){ ?>
                <div id="registro">
                    <h2>Escanear QR</h2><br>
                    <video id="preview"></video>
                    <script>
                        let scanner = new Instascan.Scanner(
                            {
                                video: document.getElementById('preview')
                            }
                        );
                        scanner.addListener('scan', function(content) {
                            //alert('Contenido Escaneado: ' + content);
                            window.location.href= 'admisionVisita.php?QR=' + content
                        });
                        Instascan.Camera.getCameras().then(cameras =>
                        {
                            if(cameras.length > 0){
                                scanner.start(cameras[0]);
                            } else {
                                console.error("No se puede acceder a la camara");
                            }
                        });
                    </script>
                </div>
            <?php
            }
            if($_GET['action']=="entqr"){ ?>
        <div id="registro">
            <form action="admisionVisita.php" method="get" >

                <label for="QR">Código</label>
                <input type="text" id="QR" name="QR"  maxlength="6" required>

                <input type="submit"  value="Buscar">
            </form>
            <?php }
        }?>

    </div>
</div>
</body>
</html>