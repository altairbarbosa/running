<?php
require_once("../connection.php");
require_once("../../lang/translator.php");

$name   = $_POST["name"];
$email  = $_POST["email"];
$level  = 1;

if ($_POST['password'] != $_POST['confirm_password']) {
?>
    <script type="text/javascript">
        alert("<?php echo $passNoCorrespond; ?>")
    </script>
    <?php
    header("Refresh: 1, ../../register-user.php");
    exit();
} else {
    $password = sha1($_POST["password"]);
}

try {
    $connection->beginTransaction();
    $sql = "SELECT * FROM usuario WHERE email = '$email'";
    $stmt = $connection->prepare($sql);
    $executa = $stmt->execute();
    $retorn_email = $stmt->fetch();

    if ($retorn_email == false) {
        $sql = "INSERT INTO usuario (nome, email, senha, nivel) VALUES (:name, :email, :password, :level);";
        $stmt = $connection->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':level', $level);

        $executa = $stmt->execute();

        $sql = "SELECT id FROM usuario WHERE nome= '$name';";
        $stmt = $connection->prepare($sql);
        $executa = $stmt->execute();

        $retorn_id = $stmt->fetchAll();

        foreach ($retorn_id as $row) {
            $id  = $row['id'];
        }

        $confirmacao = md5($id);
        $sequencia = "UPDATE usuario SET confirmacao = '$confirmacao' WHERE id = '$id';";

        $stmt = $connection->prepare($sequencia);
        $stmt->execute();

        $hostname = getenv('HTTP_HOST');

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
        $mail->addAddress($email, utf8_decode($name));        // Add a recipient
        $mail->addReplyTo('running-app@outlook.com', utf8_decode('Running'));
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('running-app@outlook.com');

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');      // Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = utf8_decode($emailTitleConfirm);
        $mail->Body    = utf8_decode($emailText1 . $name . ',<br>' . $emailText2 . '<br><br><a href="' . $hostname . '/running/confirm-pass.php?confirm=' . $confirmacao . '">' . $clickHere . '</a><br><br>' . $title . '<br>');

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
            alert("<?php echo $emailRegistered; ?>")
        </script>
<?php
        header("Refresh: 1, ../../login.php");
    }
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;

    header("Refresh: 1, ../../login.php");
}
