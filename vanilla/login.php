<?php
include('lang/translator.php');
?>

<!DOCTYPE html>
<html>
<?php
include('views/head.php');
?>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="index.php"><b><?php echo $title; ?></b></a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg"><?php echo $titleLogin; ?></p>

                <?php
                include('forms/form-login.php');
                ?>

                <p class="mt-2 mb-1">
                    <a href="forgot.php"><?php echo $forgot; ?></a>
                </p>
                <p class="mb-0">
                    <a href="register-user.php" class="text-center"><?php echo $noAccount; ?></a>
                </p>
            </div>
        </div>
    </div>

    <?php
    include('views/script.php');
    ?>

</body>

</html>