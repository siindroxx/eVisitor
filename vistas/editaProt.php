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
if(isset($_GET['id'])) {
    //OBTIENE LOS DATOS DEL PROTOCOLO
    $idProt = $_GET['id'];
    $sql_prot = "SELECT * FROM protocolo WHERE Activo = 1 and ID = '$idProt'";
    $result_prot = mysqli_query($conn, $sql_prot);
    $row = mysqli_fetch_array($result_prot);
    if($row['Archivo'] != '') {
        $Arch = $row['Archivo'];
        $ArchivoNombre = str_replace('protocolos/', '', $Arch);
    }
    else{
        $ArchivoNombre = 'N/A';
    }
}
else{
    if (isset($_POST['submit'])) {
        $id = $_POST['idprot'];
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
        $permitidos = array('docx', 'pdf');
        if($TamanioArchivo > 0){
            if(in_array($ArchvioExtReal, $permitidos)) {
                if ($ArchivoError == 0) {
                    if ($TamanioArchivo > 10000) {
                        $path = 'protocolos/';
                        $realpath = '../'.$path;
                        if (!file_exists($realpath)) {
                            mkdir($realpath);
                        }
                        $Destino = $path . $NombreArchivo;
                        $DestinoCheck = '../' . $Destino;
                        move_uploaded_file($ArchivoNombreTemp, $DestinoCheck);
                        $sql_protocolo = "UPDATE protocolo 
                               SET Nombre = '$nombre', Instrucciones ='$instrucciones', Tipo = '$tipo', Archivo = '$Destino'
                               WHERE ID = '$id'";
                        if(!mysqli_query($conn, $sql_protocolo)){
                            $_SESSION['message'] = 'error';
                            $_SESSION['messagecontent'] = 'Error al actualizar protocolo';
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
                    else{ //CUANDO EXCEDE DEL TAMANIO
                        ?>
                        <script>
                            alert ("El archivo excede el tamaño permitido")
                            window.location.href = "editaProt.php?id=<?php echo $id?>"
                        </script>
                        <?php
                    }
                }
            }
            else{ //PARA CUANDO EL ARCHIVO NO ES PDF O DOCX
                ?>
                <script>
                    alert ("Solo se admiten archivos PDF")
                    window.location.href = "editaProt.php?id=<?php echo $id?>"
                </script>
                <?php
            }
        }
        else{ //PARA CUANDO NO SE ACTUALIZA ARCHIVO
            $sql_protocolo = "UPDATE protocolo 
                               SET Nombre = '$nombre', Instrucciones ='$instrucciones', Tipo = '$tipo'
                               WHERE ID = '$id'";
            if(!mysqli_query($conn, $sql_protocolo)){
                $_SESSION['message'] = 'error';
                $_SESSION['messagecontent'] = 'Error al actualizar protocolo';
                ?>
                <script>
                    window.location.href = "editarProt.php?msg=error"
                </script>
                <?php
            }
            else{
                $_SESSION['message'] = 'success';
                $_SESSION['messagecontent'] = 'Protocolo actualizado correctamente';
                ?>
                <script>
                    window.location.href = "editarProt.php?msg=success"
                </script>
                <?php
            }
        }
    }//FIN DE SUBMIT
    else{
        ?>
        <script>
            alert ("Error al obtener id de protocolo")
            window.location.href = "editarProt.php"
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
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="nuevoProt.php"><i class="fas fa-plus-square"></i>Nuevo Protocolo</a></li>
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
            <h4 class="top">Editar Protocolo</h4>
            </div>
            <div style="float: right" class="logout">
                <!--<h3 ><a href="../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>-->
            </div>
        </div>
        <div id="registro">
            <form action="editaProt.php" method="post" enctype="multipart/form-data">

                <input type="hidden" value="<?php echo $idProt?>" name="idprot">

                <label for="asunto">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $row['Nombre']; ?>" maxlength="30" required>

                <label for="instrucciones">Instrucciones</label>
                <!--<input type="text" id="instrucciones" name="instrucciones" placeholder="Instrucciones de Visita" size="255" maxlength="255">-->
                <textarea id="instrucciones" name="instrucciones" rows="4" cols="190" maxlength="255"><?php echo $row['Instrucciones']; ?></textarea>

                <!--DEFINE EN DONDE SE APLICA - 1 VISITA | 2 MENSAJERIA-->
                <!--<label for="tipo">Tipo</label> -->
                <!--<select id="tipo" name="tipo">
                    <option value="<?php echo $row['Tipo']; ?>"><?php if($row['Tipo']==1){echo "Visita";}else{echo "Mensajería";} ?></option>
                    <option value="<?php if($row['Tipo'] == 1){echo 2;}else{echo 1;}?>"><?php if($row['Tipo']==1){echo "Mensajería";}else{echo "Visita";} ?></option>
                </select> -->
                <input type="hidden" name="tipo" value="1">

                <label for="doc">Documento (Actual: <?php echo $ArchivoNombre ?>)</label>
                <input type="file" id="doc" name="doc">

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Actualizar">
            </form>
        </div>
    </div>
</div>
</body>
</html>
