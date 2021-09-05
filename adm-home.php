<?php
require_once('crud/session-adm.php');
require_once('lang/translator.php');
require_once('crud/connection.php');

$select = $connection->query("SELECT COUNT(*) FROM usuario;");
$count_client = $select->fetch();

$select = $connection->query("SELECT COUNT(*) FROM prodserv WHERE tipo = 'p';");
$count_product = $select->fetch();

$select = $connection->query("SELECT COUNT(*) FROM prodserv WHERE tipo = 's';");
$count_service = $select->fetch();

$select = $connection->query("SELECT COUNT(*) FROM exercicio;");
$count_exercise = $select->fetch();
?>

<!DOCTYPE html>
<html>
<?php
include('views/head.php');
?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">

        <?php
        include('views/nav-adm.php');
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
                                <li class="breadcrumb-item"><a href="adm-home.php"><?php echo $home; ?></a></li>
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
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_client[0]; ?>
                                    </h3>
                                    <p>
                                        <?php echo $registered_users; ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <a href="adm-user-view.php" class="small-box-footer"><?php echo $view; ?> <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_product[0]; ?>
                                    </h3>
                                    <p>
                                        <?php echo $registered_product; ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-stream"></i>
                                </div>
                                <a href="adm-product-view.php" class="small-box-footer"><?php echo $view; ?> <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_service[0]; ?>
                                    </h3>
                                    <p>
                                        <?php echo $registered_service; ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="adm-service-view.php" class="small-box-footer"><?php echo $view; ?> <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>
                                        <?php echo $count_exercise[0]; ?>
                                    </h3>
                                    <p>
                                        <?php echo $registered_exercise; ?>
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="adm-exercise-view.php" class="small-box-footer"><?php echo $view; ?> <i class="fas fa-arrow-circle-right"></i></a>
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