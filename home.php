<?php
require_once('crud/session.php');
require_once('lang/translator.php');
require_once('crud/connection.php');

$select = $connection->query("SELECT COUNT(*) FROM treino;");
$count_treino = $select->fetch();
?>

<!DOCTYPE html>
<html>
<?php
include('views/head.php');
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">

        <?php
        include('views/nav-home.php');
        ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">
                                <?php
                                include('views/greeting.php');
                                ?>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home.php"><?php echo $home; ?></a></li>
                                <li class="breadcrumb-item active"><?php echo $home; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_treino[0]; ?>
                                    </h3>
                                    <p>
                                        <?php echo $training; ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-stream"></i>
                                </div>
                                <a href="training.php" class="small-box-footer"><?php echo $view; ?> <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php
        include('views/footer.php')
        ?>

    </div>

    <?php
    include('views/script.php');
    ?>
</body>

</html>