<?php

class Mail {
	public $result;
	
	public static function isSend(){
		return $this->result;
	}
	public static function getHtmlHeaders($from){
		// Always set content-type when sending HTML email
		/*$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: www.reporteoperatorio.com <'.$from.'>' . "\r\n".
		'Cc: myboss@reporteoperatorio.com' . "\r\n";*/

// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <webmaster@reporteoperatorio.com>' . "\r\n";
		$headers .= 'Cc: myboss@reporteoperatorio.com' . "\r\n";

		return $headers;
	}
	public static function htmlBody($body){
		return $body;
	}

	public static function avisoNuevoReporte($to, $nombreUsuario, $departamento, $institucion, $usuario_registro, $enlace_a_pdf){
		$headers = self::getHtmlHeaders("webmaster@reporteoperatorio.com");
		$html = "Estimado $nombreUsuario <br>
				$departamento <br>
				$institucion <br>

				Reporte Operatorio le comunica que el usuario $usuario_registro ha generado un nuevo reporte a través de nuestro sistema. <br>

				El enlace de acceso es:  <a href=\"$enlace_a_pdf\">Enlace PDF</a>. <br>

				Requeriría ingresar con su usuario y contraseña para acceder al documento en mención. <br>

				Gracias,<br>

				El equipo de Reporte Operatorio.<br>

				E. mail: system@reporteoperatorio.com<br>
				Website: www.reporteoperatorio.com<br>";

		return mail($to,"Se ha generado un nuevo reporte en Reporte Operatorio.",self::htmlBody($html),$headers);
	}
	public static function avisoNuevoReporte2($to, $nombreUsuario, $departamento, $institucion, $usuario_registro, $enlace_a_pdf){

		Load::lib("PHPMailer-master/PHPMailerAutoload");

		//$headers = self::getHtmlHeaders("webmaster@reporteoperatorio.com");
		$html = "Estimado $nombreUsuario <br>
				$departamento <br>
				$institucion <br>

				Reporte Operatorio le comunica que el usuario $usuario_registro ha generado un nuevo reporte a través de nuestro sistema. <br>

				El enlace de acceso es:  <a href='http://reporteoperatorio.com".$enlace_a_pdf."'>Enlace PDF</a>. <br>

				Requeriría ingresar con su usuario y contraseña para acceder al documento en mención. <br>

				Gracias,<br>

				El equipo de Reporte Operatorio.<br>

				E. mail: system@reporteoperatorio.com<br>
				Website: www.reporteoperatorio.com<br>";


		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.reporteoperatorio.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'reporteoperatori';                 // SMTP username
		$mail->Password = 'reportes2015';                           // SMTP password
		//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 26;                                    // TCP port to connect to

		$mail->setFrom('system@reporteoperatorio.com', 'Reporte Operatorio');
		$mail->addAddress($to, $nombreUsuario);     // Add a recipient
		//$mail->addAddress('ellen@example.com');               // Name is optional
		/*$mail->addReplyTo('info@example.com', 'Information');
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');*/

		/*$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		$mail->addAttachment('/tmp/image.jpg', 'new.jpg');*/    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = "Se ha generado un nuevo reporte en Reporte Operatorio.";
		$mail->Body    = $html;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()) {
		    /*echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;*/
		    return false;
		} else {
			return true;
		    //echo 'Message has been sent';
		}
	}

	/*public static function send($to, $nombreUsuario, $departamento, $institucion, $usuario_registro, $enlace_a_pdf){
		Load::lib("PHPMailer-master/PHPMailerAutoload");

		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'localhost';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'reporteoperatori@reporteoperatorio.com';                 // SMTP username
		$mail->Password = 'reportes2015';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		$mail->setFrom('system@reporteoperatorio.com', 'System');
		$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		//$mail->addAddress('ellen@example.com');               // Name is optional
		/*$mail->addReplyTo('info@example.com', 'Information');
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');*/

		/*$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		$mail->addAttachment('/tmp/image.jpg', 'new.jpg');*/    // Optional name
		/*$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = 'Here is the subject';
		$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()) {
		    echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
		    echo 'Message has been sent';
		}
	}*/
}
?>
