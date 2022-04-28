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
if (isset($_POST['submit'])) {
    if(isset($_SESSION['message'])){
        unset($_SESSION['message']);
    }
    if(isset($_SESSION['messagecontent'])){
        unset($_SESSION['messagecontent']);
    }
    $nombre = $_POST['nombre'];
    $instrucciones = $_POST['instrucciones'];
    $tipo = $_POST['tipo'];
    //VARIABLES DE ARCHIVO ADJUNTO
    $archivo = $_FILES['doc'];
    $NombreArchivo = $_FILES['doc']['name'];
    $ArchivoNombreTemp = $_FILES['doc']['tmp_name'];
    $TamanioArchivo = $_FILES['doc']['size'];
    $ArchivoError = $_FILES['doc']['error'];
    $ArchivoExt = explode('.', $NombreArchivo);
    $ArchvioExtReal = strtolower(end($ArchivoExt));
    //ARCHIVOS PERMITIDOS
    $permitidos = array('pdf');
    if($TamanioArchivo > 0){
        if(in_array($ArchvioExtReal, $permitidos)){
            if($ArchivoError == 0){
                if($TamanioArchivo > 100000){
                    $path = 'protocolos/';
                    $realpath = '../'.$path;
                    if(!file_exists($realpath)){
                        mkdir($realpath);
                    }
                    $Destino = $path.$NombreArchivo;
                    $DestinoCheck = '../'.$Destino;
                    if(file_exists($DestinoCheck))
                    {
                        ?>
                        <script>
                            alert ("No se pudo registrar el protocolo: El archivo adjunto ya existe")
                            window.location.href = "nuevoProt.php"
                        </script>
                        <?php
                    }
                    else{
                        move_uploaded_file($ArchivoNombreTemp, $DestinoCheck);
                        $sql_protocolo = "INSERT INTO protocolo (Nombre, Instrucciones, Tipo, Archivo, Activo)
                                VALUES ('$nombre','$instrucciones','$tipo', '$Destino' , 1 )";
                        if(!mysqli_query($conn, $sql_protocolo)){
                            $_SESSION['message'] = 'error';
                            $_SESSION['messagecontent'] = 'Error al registrar protocolo';
                            ?>
                            <script>
                                window.location.href = "editarProt.php?msg=error"
                            </script>
                            <?php
                        }
                        else{
                            $_SESSION['message'] = 'success';
                            $_SESSION['messagecontent'] = 'Protocolo registrado correctamente';
                            ?>
                            <script>
                                window.location.href = "editarProt.php?msg=success"
                            </script>
                            <?php
                        }
                    }
                }
                else{ //CUANDO EXCEDE DEL TAMANIO
                    ?>
                    <script>
                        alert ("El archivo excede el tamaño permitido")
                        window.location.href = "nuevoProt.php"
                    </script>
                    <?php
                }
            }
        }
        else{ //PARA CUANDO EL ARCHIVO NO ES PDF O DOCX
            ?>
            <script>
                alert ("Solo se admiten archivos PDF")
                window.location.href = "nuevoProt.php"
            </script>
            <?php
        }
    }
    else{ //PARA CUANDO NO INSERTA ARCHIVO
        $sql_protocolo = "INSERT INTO protocolo (Nombre, Instrucciones, Tipo, Activo)
                                VALUES ('$nombre','$instrucciones','$tipo', 1 )";
        if(!mysqli_query($conn, $sql_protocolo)){
            $_SESSION['message'] = 'error';
            $_SESSION['messagecontent'] = 'Error al registrar protocolo';
            ?>
            <script>
                window.location.href = "editarProt.php?msg=error"
            </script>
            <?php
        }
        else{
            $_SESSION['message'] = 'success';
            $_SESSION['messagecontent'] = 'Protocolo registrado correctamente';
            ?>
            <script>
                window.location.href = "editarProt.php?msg=success"
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
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../scripts/fontawesome.js"></script>
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
            <h4 class="top">Nuevo Protocolo</h4>
            </div>
            <div style="float: right" class="logout">
                <!--<h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>-->
            </div>
        </div>
        <div id="registro">
            <form action="nuevoProt.php" method="post" enctype="multipart/form-data">
                <label for="asunto">Nombre</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del protocolo" maxlength="30" required>

                <label for="instrucciones">Instrucciones</label>
                <!--<input type="text" id="instrucciones" name="instrucciones" placeholder="Instrucciones de Visita" size="255" maxlength="255">-->
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255"></textarea>

                <!--DEFINE EN DONDE SE APLICA - 1 VISITA | 2 MENSAJERIA-->
                <input type="hidden" name="tipo" value="1">
                <!--<label for="tipo">Tipo</label>
                <select id="tipo" name="tipo">
                    <option value="1">Visita</option>
                    <option value="2">Mensajería</option>
                </select> -->

                <label for="doc">Documento</label>
                <input type="file" id="doc" name="doc">

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Registrar">
            </form>
        </div>
    </div>
</div>
</body>
</html>
