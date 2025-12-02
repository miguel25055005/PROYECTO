<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../PHPMailer/Exception.php';
    require '../PHPMailer/PHPMailer.php';
    require '../PHPMailer/SMTP.php';

    class Usuario{
        public function insertarUsuario($nombres,$apellidos,$email,$telefono,$identificacion,$clave,$rol,$estrato){
            // INSTANCEAMOS LA CONEXION
            $objetoConexion = new Conexion();
            $conexion = $objetoConexion -> get_conexion();

            // VERIFICAMOS QUE EL USUARIO NO ESTE REGISTRADO
            $consultar = "SELECT * FROM usuario WHERE correo='$email'";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultdo = $conexion -> prepare($consultar);
            $resultdo -> execute();
            $f = $resultdo -> fetch();

            if($f){
                echo"<script>alert('El usuario ya se encunegtra registrado, porfavor registre un correo nuevo')</script>";
                echo"<script>location.href='../views/extras/login.php'</script>";
            }else{
                // DEFINIMOS EN UNA VARIBALE LA CONSULTA DE SQL SEGUN SEA EL CASO
                $registrar = "INSERT INTO usuario(nombres,apellidos,correo,telefono,identificacion,password,rol,estrato) VALUES('$nombres','$apellidos','$email','$telefono','$identificacion','$clave','$rol','$estrato')";

                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultdo = $conexion -> prepare($registrar);
                $resultdo -> execute();

                echo"<script>alert('Cliente registrado con exito')</script>";
                echo"<script>location.href='../views/extras/login.php'</script>";
            }
        }

        public function iniciarCliente($email,$clave){
            // INSTANCEAMOS LA CONEXION
            $objetoConexion = new Conexion();
            $conexion = $objetoConexion -> get_conexion();

            // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
            $consultar = "SELECT * FROM usuario WHERE correo='$email'";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultdo = $conexion -> prepare($consultar);
            $resultdo -> execute();
            $f = $resultdo -> fetch();

            if($f){
                $_SESSION['id']=$f['id'];
                $_SESSION['estrato']=$f['estrato'];
                $_SESSION['autenticado']='si';
                $_SESSION['rol']='cliente';

                if($clave==$f['password']){
                    echo"<script>alert('Bienvenido cliente ".$f['nombres']."')</script>";
                    echo"<script>location.href='../views/cliente/dashboardCliente.php'</script>";
                }else{
                    echo"<script>alert('Contraseña incorrecta')</script>";
                    echo"<script>location.href='../views/extras/login.php'</script>";
                }
            }else{
                echo"<script>alert('Usuario no encontrado, porfavor ingrese un correo valido')</script>";
                echo"<script>location.href='../views/extras/login.php'</script>";
            }
        }

        public function iniciarAdministrador($email,$clave){
            // INSTANCEAMOS LA CONEXION
            $objetoConexion = new Conexion();
            $conexion = $objetoConexion -> get_conexion();

            // DEFINIMOS EN UNA VARIABLE LA CONSULTA DE SQL SEGUN SEA EL CASO
            $consultar = "SELECT * FROM usuario WHERE correo='$email' AND rol='Administrador'";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultdo = $conexion -> prepare($consultar);
            $resultdo -> execute();
            $f = $resultdo -> fetch();
            if($f){

                $_SESSION['autenticado']='si';
                $_SESSION['rol']='Administrador';
                

                if($clave==$f['password']){
                    echo"<script>alert('Bienvenid@ adminstrad@r ".$f['nombres']."')</script>";
                    echo"<script>location.href='../views/admin/dashboardAdmin.php'</script>";
                }else{
                    echo"<script>alert('Contraseña incorrecta')</script>";
                    echo"<script>location.href='../views/extras/login.php'</script>";
                }
            }else{
                echo"<script>alert('Usuario no encontrado, porfavor ingrese un correo valido')</script>";
                echo"<script>location.href='../views/extras/login.php'</script>";
            }
        }


        public function recuperarClave($identificacion,$email){
            // INSTANCEAMOS LA CONEXION
            $objetoConexion = new Conexion();
            $conexion = $objetoConexion -> get_conexion();

            // VERIFICAMOS QUE EL CORREO Y LA IDENTIFICACION EXITAS
            $consultar = "SELECT * FROM usuario WHERE correo ='$email' AND identificacion = '$identificacion'";

            // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
            $resultdo = $conexion -> prepare($consultar);
            $resultdo -> execute();
            $f = $resultdo -> fetch();

            if($f){
                $clave_nueva = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,10);

                $clave_encriptada = md5($clave_nueva);

                $actualizar_clave = "UPDATE usuario SET password='$clave_encriptada' WHERE correo = '$email'";
                // PREPARAMOS LA ACCION A EJECUTAR Y LA EJECUTAMOS
                $resultado = $conexion -> prepare($actualizar_clave);
                $resultado -> execute();

                 $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //ESTE VALOR CAMBIA SEGUN EL CORREO QUE SE ESTE USANDO COMO BASE EJ: GMAIL,HOTMAIL, ETC.
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'soportesenasoft@gmail.com';                     //SMTP username
                $mail->Password   = 'puhhcmfyhekhzlta';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('soportesenasoft@gmail.com', 'Soporte Tecnico Bicicletas');         //REMITENTE O QUIEN ENVIA EL MENSAJE
                $mail->addAddress($email);     //RECEPTOR DEL MENSAJE
                // $mail->addAddress('ellen@example.com');               //Name is optional
                // $mail->addReplyTo('info@example.com', 'Information');
                // $mail->addCC('cc@example.com');
                // $mail->addBCC('bcc@example.com');

                //Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Has Recibido Un Mensaje Del Formulario De Contacto - Alquiler-Bicicletas';
                $mail->Body    = '
                
                
                            <!DOCTYPE html>
                                    <html lang="es">
                                    <head>
                                    <meta charset="UTF-8">
                                    <style>
                                        body {
                                            font-family: "Segoe UI", Arial, sans-serif;
                                            background-color: #f3f3f3;
                                            margin: 0;
                                            padding: 0;
                                            color: #333;
                                        }

                                        .container {
                                            max-width: 650px;
                                            margin: 40px auto;
                                            background-color: #fff;
                                            border-radius: 10px;
                                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                            overflow: hidden;
                                        }

                                        header {
                                            background-color: #39ff14; /* Verde neón */
                                            color: #000;
                                            text-align: center;
                                            padding: 25px 20px;
                                        }

                                        header img {
                                            width: 90px;
                                            height: 90px;
                                            border-radius: 50%;
                                            object-fit: cover;
                                            margin-bottom: 10px;
                                        }

                                        header h1 {
                                            margin: 5px 0;
                                            font-size: 24px;
                                            letter-spacing: 1px;
                                            text-transform: uppercase;
                                        }

                                        header p {
                                            font-size: 14px;
                                            margin: 0;
                                        }

                                        main {
                                            padding: 30px 40px;
                                        }

                                        main h2 {
                                            color: #39ff14;
                                            font-size: 20px;
                                            margin-bottom: 20px;
                                            border-bottom: 2px solid #39ff14;
                                            display: inline-block;
                                            padding-bottom: 4px;
                                        }

                                        main p {
                                            font-size: 15px;
                                            line-height: 1.6;
                                            margin: 10px 0;
                                        }

                                        main strong {
                                            color: #111;
                                        }

                                        footer {
                                            background-color: #111;
                                            color: #bbb;
                                            text-align: center;
                                            padding: 15px;
                                            font-size: 13px;
                                        }

                                        footer a {
                                            color: #39ff14;
                                            text-decoration: none;
                                        }
                                    </style>
                                    </head>
                                    <body>
                                        <div class="container">
                                            <header>
                                                <!-- 👇 Aquí colocas la URL de tu logo -->
                                                <img src="https://i.pinimg.com/236x/71/ff/68/71ff682bd76bdbffba7678191f59e5c5.jpg" alt="Logo Alquiler de Bicicletas">
                                                <h1>Alquiler de Bicicletas</h1>
                                                <p>Nuevo mensaje desde el formulario de recuperar clave</p>
                                            </header>

                                            <main>
                                                <h2>Se ha recibido un nuevo correo desdel formulario de recuperar clave de la plataforma de alquiler de bicicletas</h2>
                                                <p><strong>👤 Nueva contraseña:</strong> '.$clave_nueva.'</p>
                                             

                                            <footer>
                                                <p>© '.date('Y').' <a href="#">Alquiler de Bicicletas</a> | Todos los derechos reservados.</p>
                                            </footer>
                                        </div>
                                    </body>
                                    </html>
                ';
                // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }else{
                echo"<script>alert('Los datos no son validos')</script>";
                echo"<script>location.href='../views/extras/ressetPasword.php'</script>";
            }
            
        }
    }

?>