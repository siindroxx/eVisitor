<?php
include('../../sql/conexion.php');
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: ../../index.php"); // Redirecting To Home Page
}
else{
    if($_SESSION['tipo'] != 1){
        header("location: ../index.php"); // Redirecting To Home Page
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../scripts/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../../css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../../JS/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="reporteVisitas.php"><i class="fas fa-calendar"></i>Visitas</a></li>
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
                <h4>Visitas</h4>
            </div>
            <div style="float: right" class="logout">
                <div style="float: left">
                </div>
                <div style="float: right">
                    <h3 ><a href="../../logout.php"><i class="fas fa-key"></i> Cerrar Sesión</a></h3>
                </div>
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
            <!--FIN DE MENSAJES-->
        <?php }
            if(!isset($_POST['busqueda'])){ ?>
                <div id="registro">
                    <form action="reporteVisitas.php" method="post">

                        <label for="fechainicio">Rango de fechas</label>

                        <label for="fechainicio">Fecha Inicial</label>
                        <input type="date" id="fechainicio" name="fechainicio" required>

                        <label for="fechainicio">Fecha Final</label>
                        <input type="date" id="fechafin" name="fechafin" required>

                        <input type="hidden" name="busqueda" value="true">
                        <input type="submit"  value="Buscar">
                    </form>
                </div>
            <?php }
            else{
                $fechainicio = $_POST['fechainicio'];
                $fechafin = $_POST['fechafin'];
                //OBTENER VISITAS
                $sql_visitas = "SELECT v.*, p.Nombre as Protocolo from visita v 
                inner join protocolo p on v.IDProtocolo = p.ID
                where IDUsuario = '" . $_SESSION['login_id'] . "' AND v.Activo = 1 AND v.FechaVisita between '$fechainicio' AND '$fechafin'";
                $result_visitas = mysqli_query($conn, $sql_visitas);
                while($row_visitas = mysqli_fetch_assoc($result_visitas)){
                    $array = array(
                        'ID' => $row_visitas['ID'],
                        'Titulo' => $row_visitas['Titulo'],
                        'Protocolo' => $row_visitas['Protocolo'],
                        'FechaVisita' => $row_visitas['FechaVisita'],
                        'HoraInicio' => $row_visitas['HoraInicio'],
                        'Recurrente' => $row_visitas['Recurrente']
                    );
                    $array_visitas[$row_visitas['ID']] = $array;
                }
                ?>
                <table id="example" class="display">
                    <thead>
                    <tr>
                        <th scope="col">Titulo</th>
                        <th scope="col">Protocolo</th>
                        <th scope="col">Fecha Visita</th>
                        <th scope="col">Hora Visita</th>
                        <th scope="col">Ver</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($array_visitas)){
                        foreach ($array_visitas as $row){
                            ?>
                            <tr>
                                <td > <?php echo $row['Titulo'] ?> </td>
                                <td><?php echo $row['Protocolo']?></td>
                                <td><?php echo $row['FechaVisita'] ?></td>
                                <td><?php echo $row['HoraInicio'] ?></td>
                                <td>
                                    <a href="visita.php?id=<?php echo $row['ID']?>" target="_blank"><button type="button" class="button button2"><i class="fas fa-book-reader"></i></button></a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            <?php }   ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#example').DataTable( {
            "order": [[ 2, "desc" ]]
        } );
    } );
</script>

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