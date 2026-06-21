<?php
include("../connection.php");
include("../../lang/translator.php");

$email_d = $_POST["email"];

try {
    $connection->beginTransaction();
    $sql = "SELECT * FROM usuario WHERE email = '$email_d'";
    $stmt = $connection->prepare($sql);
    $executa = $stmt->execute();
    $retorn = $stmt->fetchAll();

    foreach ($retorn as $row) {
        $id      = $row['id'];
        $email_b = $row['email'];
        $name    = $row['nome'];
    }

    if ($email_b == $email_d) {
        $confirmacao = md5($id);
        $sequencia = "UPDATE usuario SET confirmacao = '$confirmacao' WHERE id = '$id';";

        $stmt = $connection->prepare($sequencia);
        $stmt->execute();

        require '../../PHPMailer/PHPMailerAutoload.php';

        $mail = new PHPMailer();

        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output 0=disable
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = "smtp-mail.outlook.com";                // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'running-app@outlook.com';          // SMTP username
        $mail->Password = 'a6l5-t4a3-i2r1';
        $mail->SMTPSecure = 'tsl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('running-app@outlook.com', utf8_decode('Running'));
        $mail->addAddress($email_b, utf8_decode($name));        // Add a recipient
        $mail->addReplyTo('running-app@outlook.com', utf8_decode('Running'));
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('running-app@outlook.com');

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');      // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML

        $hostname = getenv('HTTP_HOST');

        $mail->Subject = utf8_decode($emailTitleRecovery);
        $mail->Body    = utf8_decode($emailText1 . $name . ',<br>' . $emailText2 . '<br><br><a href="' . $hostname . '/running/change-pass.php?confirm=' . $confirmacao . '">' . $clickHere . '</a><br><br>' . $title . '<br>');

        // $mail->AltBody = 'Isto é apenas um teste em texto plano.';

        $mail->send();
?>
        <script type="text/javascript">
            alert("<?php echo $emailSucess; ?>")
        </script>
    <?php

        $connection->commit();

        header("Refresh: 1, ../../login.php");
    } else {
    ?>
        <script type="text/javascript">
            alert("<?php echo $emailNoRegistered; ?>")
        </script>
<?php
        header("Refresh: 1, ../../login.php");
    }
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;

    header("Refresh: 1, ../../login.php");
}
