<?php
//FUNCION UTILIZADA PARA EL ENVIO DE EMAILS PARA LOS VISITANTES
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sendemail($name, $fecha, $titulo, $codigo, $email, $tipo)
{
    require '../librerias/phpmailer/Exception.php';
    require '../librerias/phpmailer/PHPMailer.php';
    require '../librerias/phpmailer/SMTP.php';
    require 'genqr.php';
    $mail = new PHPMailer(true);
    if($tipo == 1) {
        $qrpath = '../QR/' . $codigo . '.png';
        if (!file_exists($qrpath)) {
            genqr($codigo);
        }
        $message = file_get_contents("templates/emailtemplate.html", FILE_USE_INCLUDE_PATH);
        $message = str_replace("{{ titulo }}", $titulo, $message);
        $message = str_replace("{{ fechainicio }}", $fecha, $message);
        $message = str_replace("{{ Name }}", $name, $message);
        $message = str_replace("{{ codigo }}", $codigo, $message);
        $message = str_replace("{{ imagenqr }}", 'cid:qr', $message);
        $subject = 'Nueva Visita Programada Sollertia';
        $AltBody = 'Nueva Visita';
    }
    if($tipo == 2) {

        $date= date("d-m-Y");
        $time= date("H:i:s");
        $message = file_get_contents("templates/emailtemplateadmision.html", FILE_USE_INCLUDE_PATH);
        $message = str_replace("{{ Name }}", $name, $message);
        $message = str_replace("{{ titulo }}", $titulo, $message);
        $message = str_replace("{{ fecha }}", $date, $message);
        $message = str_replace("{{ hora }}", $time, $message);
        $message = str_replace("{{ NombresVisitantes }}", $codigo, $message);

        $subject = 'Admisión Sollertia';
        $AltBody = 'Admisión Sollertia';
    }

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
        $mail->addAddress($email, $name);     //Add a recipient
        if($tipo == 2){
            if(isset($_SESSION['notificado'])) {
                foreach ($_SESSION['notificado'] as $values) {
                    $mail->addAddress($values['Email']);
                }
            }
        }
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
        if($tipo == 1){
            $mail->addEmbeddedImage($qrpath, 'qr');
        }
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = $AltBody;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


