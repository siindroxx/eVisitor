<?php
//FUNCION UTILIZADA PARA EL ENVIO DE EMAILS PARA LOS VISITANTES
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sendemailuser($nombre, $date, $clave , $password, $email, $tipo)
{
    require '../../librerias/phpmailer/Exception.php';
    require '../../librerias/phpmailer/PHPMailer.php';
    require '../../librerias/phpmailer/SMTP.php';
    $mail = new PHPMailer(true);
    //DEFINIR TEMPLATE: 1 NUEVO USUARIO || 2 RESET USUARIO
    if($tipo == 1){
        $message = file_get_contents("emailtemplateuser.html", FILE_USE_INCLUDE_PATH);
        $altBody = "Nuevo Usuario eVisitor";
    }
    else{
        if ($tipo == 2){
            $message = file_get_contents("emailtemplateresetuser.html", FILE_USE_INCLUDE_PATH);
            $altBody = "Reset Usuario eVisitor";
        }
    }
    $message = str_replace("{{ Name }}", $nombre, $message);
    $message = str_replace("{{ fecha }}", $date, $message);
    $message = str_replace("{{ usuario }}", $clave, $message);
    $message = str_replace("{{ password }}", $password, $message);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.zoho.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'website@sollertia.mx';                     //SMTP username
        $mail->Password = 'Grup0S0ltecVentas1_';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('website@sollertia.mx', 'Sollertia');
        $mail->addAddress($email, $nombre);     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('../QR/RTH78L.png', 'QR.jpg');    //Optional name

        //Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);                                  //Set email format to HTML
        //$mail->addEmbeddedImage('../QR/RTH78L.png', 'qr');
        //$mail->addEmbeddedImage($qrpath, 'qr');
        $mail->Subject = $altBody;
        $mail->Body = $message;
        $mail->AltBody = $altBody;

        $mail->send();
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
