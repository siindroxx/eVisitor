<?php
include('sql/conexion.php');
session_start(); // Starting Session
$error = 'Usuario o contraseña incorrecta'; // Variable To Store Error Message
if (isset($_POST['submit'])) {
    if(isset($_POST['action'])){ //RECIBE LOS DATOS DE INDEX CUANDO SE ACTUALIZA LA CONTRASEÑA
        $idusuario = $_SESSION['login_id']; //ID DEL USUARIO
        $passwordtemp = $_POST['passwordtemp']; //CONTRASEÑA TEMPORAL
        $passwordnew = $_POST['password']; //NUEVA CONTRASEÑA
        $sql_checker = "SELECT * FROM usuario WHERE ID = '$idusuario'"; //SELECCIONA LOS DATOS DEL USUARIO
        $result_checker = mysqli_query($conn, $sql_checker);
        $row_checker = mysqli_fetch_array($result_checker);
        if(password_verify($passwordtemp, $row_checker['Password'])){ //REVISA QUE LA CONTRASEÑA TEMPORAL INGRESADA SEA IGUAL A LA DE LA BD
            $hashed_password = password_hash($passwordnew, PASSWORD_DEFAULT); //CONTRASEÑA HASHED - HASHEA LA NUEVA CONTRASEÑA
            $sql_update = "UPDATE usuario SET Password = '$hashed_password', PasswordTemp = 0 WHERE ID = '$idusuario'"; //HACE EL UPDATE DE LA NUEVA CONTRASEÑA
            if(!mysqli_query($conn, $sql_update)){
                ?>
                <script>
                    alert ("No se pudo actualizar el usuario")
                    window.location.href = "index.php"
                </script>
                <?php
            }
            else{ //EN CASO DE SER EXITOSO DESTRUYE LA SESION Y REGRESA AL INDEX, YA SE PUEDE INGRESAR CON LA NUEVA CONTRASEÑA
                session_destroy();
                ?>
                <script>
                    alert ("Usuario actualizado")
                    window.location.href = "index.php"
                </script>
                <?php
            }
        }
        else{ //EN CASO QUE LA CONTRASEÑA TEMPORAL SEA INCORRECTA DESTRUYE LA SESION Y REGRESA AL INDEX
            session_destroy();
            ?>
            <script>
                alert ("Contraseña temporal incorrecta")
                window.location.href = "index.php"
            </script>
            <?php
        }
    }
    else{ //LA PRIMERA VEZ QUE SE INGRESA USUARIO Y CONTRASEÑA ENTRA AQUI. SE DEFINE SI TIENE CONTRASEÑA TEMPORAL O NO
        if (empty($_POST['username']) || empty($_POST['password'])) { //EN CASO DE QUE LOS CAMPOS INGRESADOS ESTEN VACIOS CAUSA ERROR
            $error = "Usuario o contraseña incorrecta";
        }
        else{
            // Define $username and $password
            $username = $_POST['username']; //USUARIO INGRESADO EN INDEX
            $password = $_POST['password']; //PASSWORD INGRESADA EN INDEX
            // OBTENER DATOS DE USUARIO
            $sql_usuario = "SELECT ID, IDPuntoAcceso, Password, PasswordTemp, Sistema FROM usuario where Clave = '$username' LIMIT 1";
            if(!mysqli_query($conn, $sql_usuario)){
                ?>
                <script>
                    alert ("<?php echo $error?>")
                </script>
                <?php
            }
            else{
                $result = mysqli_query($conn, $sql_usuario);
                if(mysqli_num_rows($result)==0){ //SI EL QUERY DE USUARIO NO REGRESA RESULTADOS CAUSA ERROR
                    session_destroy();
                    ?>
                    <script>
                        alert ("<?php echo $error?>")
                    </script>
                    <?php
                }
                else{ //EN CASO QUE QUERY DE USUARIO REGRESE RESULTADOS
                    $row = mysqli_fetch_array($result);
                    $fetchPasswordTemp = $row['PasswordTemp'];
                    if ($fetchPasswordTemp == 1){ //SE REVISA SI EL USUARIO TIENE CONTRASEÑA TEMPORAL
                        $_SESSION['login_id'] = $row['ID']; //SI TIENE CONTRASEÑA TEMPORAL SE REGRESA AL INDEX, EL INDEX RECIBE EL ID DE USUARIO Y MUESTRA FORM DE CAMBIO DE CONTRASEÑA
                        header("location: index.php");
                    }
                    else { //SI NO TIENE CONTRASEÑA TEMPORAL
                        $fetchPassword =  $row['Password']; //CONTRASEÑA OBTENIDA CON NOMBRE DE USUARIO DE LA BD
                        if(password_verify($password, $fetchPassword)){ //VERIFICA QUE LA CONTRASEÑA INGRESADA SEA IGUAL QUE EN LA BD
                            $_SESSION['login_user'] = $username; // Inicializa sesion
                            $_SESSION['login_id'] = $row['ID']; //ID DE USUARIO
                            $_SESSION['tipo'] = $row['Sistema']; //TIPO DE USUARIO
                            $_SESSION['puntoacceso'] = $row['IDPuntoAcceso']; //PUNTO DE ACCESO DE USUARIO
                            header("location: main.php");// Redirije a la pagina principal
                        }
                        else{
                            ?>
                            <script>
                                alert ("<?php echo $error?>") //SI NO SE CUMPLE NINGUNA CONDICION ANTERIOR SE MARCA ERROR
                            </script>
                            <?php
                        }
                    }
                }
            }
        }
    }
}
?>