<?php
require_once('lang/translator.php');
?>

<!DOCTYPE html>
<html>

<?php
include('views/head.php');
?>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <?php
        include('views/nav-index.php');
        ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                </div>
            </section>

            <section class="content">
                <div class="error-page">
                    <h2 class="headline text-warning"> 401</h2>

                    <div class="error-content">
                        <h3><i class="fas fa-exclamation-triangle text-warning"></i> <?php echo $error401; ?> </h3>
                        <p>
                            <?php echo $noPermission; ?>
                        </p>
                        <div class="row">
                            <div class="col text-left">
                                <a href="javascript:history.back()"><i class="fas fa-arrow-left mr-1"></i><?php echo $back; ?></a>
                            </div>
                            <div class="col text-right">
                                <a href="login.php"><i class="fas fa-sign-in-alt mr-1"></i><?php echo $returnLogin; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="content">
            <div class="container">
                <div class="col-12">
                    <?php
                    include('views/footer.php');
                    ?>
                </div>
            </div>
        </div>

    </div>

    <?php
    include('views/script.php');
    ?>
</body>

</html>