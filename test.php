<?php
/*EJEMPLO PARA HACER TABLAS CON BOTON DE EXPORTAR A CSV Y QUE TENGA PAGINACION*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eVisitor</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="scripts/fontawesome.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="css/buttons.dataTables.css">
    <script type="text/javascript" charset="utf8" src="JS/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="JS/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="JS/dataTables.buttons.js"></script>
    <script type="text/javascript" charset="utf8" src="JS/buttons.html5.js"></script>
</head>
<body>
<div class="main_content">
    <div class="alert hide">
        <i class="fas fa-exclamation-circle"></i>
        <span class="msg">TEST DE CAMBIO NUMERO 2</span>
        <div class="close-btn">
            <i class="fas fa-times"></i>
        </div>
    </div>
<table id="example" class="display nowrap" style="width:100%">
    <thead>
    <tr>
        <th>NOMBRE</th>
        <th>POSISCION</th>
        <th>OFICINA</th>
        <th>EDAD</th>
        <th>FECHA INICIAL</th>
        <th>SALARIO</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Tiger Nixon</td>
        <td>System Architect</td>
        <td>Edinburgh</td>
        <td>61</td>
        <td>2011/04/25</td>
        <td>$320,800</td>
    </tr>
    <tr>
        <td>Garrett Winters</td>
        <td>Accountant</td>
        <td>Tokyo</td>
        <td>63</td>
        <td>2011/07/25</td>
        <td>$170,750</td>
    </tr>
    <tr>
        <td>Ashton Cox</td>
        <td>Junior Technical Author</td>
        <td>San Francisco</td>
        <td>66</td>
        <td>2009/01/12</td>
        <td>$86,000</td>
    </tr>
    <tr>
        <td>Cedric Kelly</td>
        <td>Senior Javascript Developer</td>
        <td>Edinburgh</td>
        <td>22</td>
        <td>2012/03/29</td>
        <td>$433,060</td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th>Name</th>
        <th>Position</th>
        <th>Office</th>
        <th>Age</th>
        <th>Start date</th>
        <th>Salary</th>
    </tr>
    </tfoot>
</table>
</div>
</body>
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
        $('.alert').addClass("oculto");
    });
</script>
</html>