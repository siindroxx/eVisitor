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
//OBTENER ID DE LA VISITA Y CODIGO DE VISITA
if(isset($_GET['id'])){
    $idProt = $_GET['id']; //ID DEL PROTOCOLO
    //para eliminar notificados
    if(isset($_GET['action'])){
        if($_GET['action'] == 'delete'){
            $idNot = $_GET['idnot']; //ID DEL NOTIFICADO
            $sql_deletenot = "UPDATE notificado SET Activo = 0 WHERE ID = '$idNot'";
            if(!mysqli_query($conn, $sql_deletenot)){
                ?>
                <script>
                    alert ("No se pudo registrar el notificado")
                    window.location.href = "editarProt.php"
                </script>
                <?php
            }
            else{
                ?>
                <script>
                    //alert("Registrado exitosamente")
                    window.location.href = "nuevoNot.php?id=<?php echo $idProt?>"
                </script>
                <?php
            }
        }
        if($_GET['action'] == 'add'){
            $IDUsuario = $_GET['idnot']; //ID DEL USUARIO AGREGADO
            $sql_checknot = "SELECT * FROM notificado WHERE IDUsuario = '$IDUsuario' and IDProt = '$idProt' AND Activo = 1";
            $result_checknot = mysqli_query($conn, $sql_checknot);
            if(mysqli_num_rows($result_checknot) == 0){
                $sql_notificado = "INSERT INTO notificado (IDProt, IDUsuario, Activo) 
                                    VALUES('$idProt', '$IDUsuario' , 1)"; //INSERTA EL NOTIFICADO EN LA BD
                if(!mysqli_query($conn, $sql_notificado)){
                    ?>
                    <script>
                        alert ("No se pudo registrar el notificado")
                        window.location.href = "nuevoNot.php?id=<?php echo $idProt?>"
                    </script>
                    <?php
                }
                else{
                    ?>
                    <script>
                        //alert("Registrado exitosamente")
                        window.location.href = "nuevoNot.php?id=<?php echo $idProt?>"
                    </script>
                    <?php
                }
            }
            else {
                ?>
                <script>
                    alert("Usuario ya esta registrado como notificado")
                    window.location.href = "nuevoNot.php?id=<?php echo $idProt?>"
                </script>
                <?php
            }
        }
    }
    else {
        if(isset($_SESSION['nombreprot'])){
            unset($_SESSION['nombreprot']);
        }
        $sql_prot = "SELECT * from protocolo where ID = '$idProt' and Activo = 1";
        $result_prot = mysqli_query($conn, $sql_prot);
        $row = mysqli_fetch_array($result_prot);
        $_SESSION['nombreprot'] = $row['Nombre'];

        //OBTIENE LOS DATOS DE LOS NOTIFICADOS
        $sql_not = "SELECT n.ID, Clave, Nombre, Correo FROM usuario u INNER JOIN notificado n on (u.ID = n.IDUsuario) WHERE n.IDProt = '$idProt' and u.Activo = 1 and n.Activo = 1";
        $result_not = mysqli_query($conn, $sql_not);
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
            <h4 class="top">Notificados - Protocolo: <?php echo $_SESSION['nombreprot']?></h4>
        </div>
        <?php if(!isset($_POST['submit'])){ ?>
        <div id="registro">
            <form action="nuevoNot.php" method="post">
                <label for="nombre">Ingrese clave, nombre o correo electrónico del usuario</label>
                <input type="text" id="nombre" name="nombre" placeholder="Clave o Correo de Notificado" maxlength="50" required>

                <input type="hidden" name="idprot" value="<?php echo $idProt?>"> <!--ID DE PROTOCOLO-->

                <input type="hidden" name="submit" value="true">
                <input type="submit"  value="Buscar">
            </form>
        </div>
        <div style="height: 50px" id="registro">
            <h3>Notificados del protocolo</h3>
            <table id="example" class="display">
                <thead>
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Eliminar</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while($row_not = mysqli_fetch_array($result_not)){
                    ?>
                    <tr>
                        <td > <?php echo $row_not['Clave'] ?> </td>
                        <td > <?php echo $row_not['Nombre'] ?> </td>
                        <td><?php echo $row_not['Correo'] ?></td>
                        <td>
                            <a href="nuevoNot.php?action=delete&id=<?php echo $idProt?>&idnot=<?php echo $row_not['ID']?>"><button type="button" class="button-danger button2"><i class="fas fa-trash-alt"></i></button></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php }
            else {
                $idProt = $_POST['idprot']; //ID DE LA VISITA
                $nombre = $_POST['nombre']; //NOMBRE DEL NOTIFICADO
                $nombrel = '%'.$nombre.'%';
                //OBTENER DATOS DE USUARIO
                $sql_usuario = "SELECT * from usuario where Clave like '$nombrel' or Correo like '$nombrel' or Nombre like '$nombrel'
                                and ID not in (SELECT IDUsuario FROM notificado WHERE IDProt = '$idProt' and Activo = 1) and Activo = 1";
                $result_usuario = mysqli_query($conn, $sql_usuario);
                if(mysqli_num_rows($result_usuario) == 0){
                ?>
                    <script>
                        alert ("No se encontro ningun usuario con esos datos")
                        window.location.href = "nuevoNot.php?id=<?php echo $idProt?>"
                    </script>
                    <?php
                }
                else{ //MOSTRAR RESULTADOS DE BUSQUEDA  ?>
                    <table id="example" class="display">
                        <div id="registro">
                            <h3>Resultados de busqueda</h3>
                        </div>
                        <thead>
                        <tr>
                            <th scope="col">Clave</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Agregar</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        while($row_usuario = mysqli_fetch_array($result_usuario)){
                            ?>
                            <tr>
                                <td > <?php echo $row_usuario['Clave'] ?> </td>
                                <td > <?php echo $row_usuario['Nombre'] ?> </td>
                                <td><?php echo $row_usuario['Correo'] ?></td>
                                <td>
                                    <a href="nuevoNot.php?action=add&id=<?php echo $idProt?>&idnot=<?php echo $row_usuario['ID']?>"><button type="button" class="button button2"><i class="fas fa-plus-square"></i></button></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                <?php }
            } ?>
    </div>
</div>
<script src="../JS/table.js"></script>
</body>
</html>
