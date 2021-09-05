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
                <p class="login-box-msg"><?php echo $textForgot1; ?></p>

                <?php
                include('forms/form-forgot.php');
                ?>

                <p class="mt-3 mb-1">
                    <a href="login.php" class="text-center"><?php echo $returnLogin; ?></a>
                </p>
            </div>
        </div>
    </div>

    <?php
    include('views/script.php');
    ?>

</body>

</html>