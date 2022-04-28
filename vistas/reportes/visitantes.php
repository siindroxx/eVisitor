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

//OBTENER VISITAS EDITABLES
$sql_visitante = "select v.ID, v.Nombre as Visitante, v.Email as EmailVisitante,
                    u.nombre as Anfitrion, u.Correo as EmailAnfitrion,
                    case when EstatusCaseta = 1 then 'Si' else 'No' end EntradaCaseta,
                    case when EstatusCaseta = 1 then c.FechaHora else 'N/A' end FechaCaseta,
                    case when EstatusRecepcion = 1 then 'Si' else 'No' end EntradaRecepcion,
                    case when EstatusRecepcion = 1 then d.FechaHora else 'N/A' end FechaRecepcion
                    from visitante v
                    inner join visita v2 on v.IDVisita = v2.ID
                    inner join usuario u on u.ID = v2.IDUsuario
                    left join (
                        select v.ID, vd.FechaHora from visitante v
                        inner join visitante_detalle vd on v.ID = vd.IDVisitante
                        INNER JOIN(
                            select IDVisitante, max(vd1.id) as maxid
                            from visitante_detalle vd1
                            inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                            where IDTipoOrigen =  1 and pa.IDOrigen = 1
                            GROUP BY IDVisitante
                        ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                        where v.EstatusCaseta = 1 and Activo = 1
                        ) c on c.ID = v.ID
                    left join (
                        select v.ID, vd.FechaHora from visitante v
                            inner join visitante_detalle vd on v.ID = vd.IDVisitante
                            INNER JOIN(
                            select IDVisitante, max(vd1.id) as maxid
                            from visitante_detalle vd1
                            inner join punto_acceso pa on pa.ID = vd1.IDPuntoAcceso
                            where IDTipoOrigen =  1 and pa.IDOrigen = 2
                            GROUP BY IDVisitante
                        ) c on v.ID = c.IDVisitante and c.maxid = vd.ID
                        where v.EstatusRecepcion = 1 and Activo = 1
                    ) d on d.ID = v.ID
                    where EstatusCaseta = 1 or EstatusRecepcion = 1 and v.Activo = 1";
$result_visitante = mysqli_query($conn, $sql_visitante);
while($row_visitante = mysqli_fetch_assoc($result_visitante)){
    $array = array(
        'visitante' => $row_visitante['Visitante'],
        'emailvisitante' => $row_visitante['EmailVisitante'],
        'anfitrion' => $row_visitante['Anfitrion'],
        'emailanfitrion' => $row_visitante['EmailAnfitrion'],
        'caseta' => $row_visitante['EntradaCaseta'],
        'fechacaseta' => $row_visitante['FechaCaseta'],
        'recepcion' => $row_visitante['EntradaRecepcion'],
        'fecharecepcion' => $row_visitante['FechaRecepcion']
    );
    $array_visitante[$row_visitante['ID']] = $array;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../scripts/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="../../css/buttons.dataTables.css">
    <link rel="stylesheet" type="text/css" href="../../css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../../JS/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/dataTables.buttons.js"></script>
    <script type="text/javascript" charset="utf8" src="../../JS/buttons.html5.js"></script>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <a href="../../main.php"><h2>eVisitor</h2></a>
        <ul>
            <li><a href="../../index.php"><i class="fas fa-long-arrow-alt-left"></i>Regresar</a></li>
            <li><a href="reporteVisitas.php"><i class="fas fa-calendar"></i>Visitas</a></li>
            <li><a href="visitantes.php"><i class="fas fa-users"></i>Visitantes</a></li>
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
                <h4>Visitantes</h4>
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
        <?php } ?>
        <!--FIN DE MENSAJES-->
        <table id="example" class="display">
            <thead>
            <tr>
                <th scope="col">Visitante</th>
                <th scope="col">Email Visitante</th>
                <th scope="col">Anfitrion</th>
                <th scope="col">Email Anfitrion</th>
                <th scope="col">Entrada Caseta</th>
                <th scope="col">Hora Caseta</th>
                <th scope="col">Entrada Recepcion</th>
                <th scope="col">Hora Recepcion</th>
            </tr>
            </thead>
            <tbody>
            <?php
                if(!empty($array_visitante)){
                    foreach ($array_visitante as $row){
                        ?>
                        <tr>
                            <td > <?php echo $row['visitante'] ?> </td>
                            <td><?php echo $row['emailvisitante']?></td>
                            <td><?php echo $row['anfitrion'] ?></td>
                            <td><?php echo $row['emailanfitrion'] ?></td>
                            <td><?php echo $row['caseta'] ?></td>
                            <td><?php echo $row['fechacaseta'] ?></td>
                            <td><?php echo $row['recepcion'] ?></td>
                            <td><?php echo $row['fecharecepcion'] ?></td>
                        </tr>
                        <?php
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#example').DataTable( {
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 filas', '25 filas', '50 filas', 'Mostrar todo' ]
            ],
            buttons: [
                'pageLength',
                'csv'
            ]
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
<?php }
}?>
</body>
</html>
