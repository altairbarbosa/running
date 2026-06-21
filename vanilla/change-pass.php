<?php
include('lang/translator.php');
?>

<!DOCTYPE html>
<html>

<?php
include('views/head.php');
?>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <a href="index.php"><b><?php echo $title; ?></b></a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg"><?php echo $titleChangePass; ?></p>
                <?php
                include('forms/form-change-pass.php');
                ?>
            </div>
        </div>
    </div>

    <?php
    include('views/script.php');
    ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
        $('#password, #confirm_password').on('keyup', function() {
            if ($('#password').val() == $('#confirm_password').val()) {
                $('#message').html('<?php echo $passCorrespond; ?>').css('color', 'green');
            } else
                $('#message').html('<?php echo $passNoCorrespond; ?>').css('color', 'red');
        });
    </script>
</body>

</html>