<?php
require_once('crud/session-adm.php');
require_once('lang/translator.php');
require_once('crud/connection.php');
require_once('models/DebtClass.php');
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
                                <?php echo $payment; ?>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="adm-home.php"><?php echo $home; ?></a></li>
                                <li class="breadcrumb-item active"><?php echo $payment; ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            include('forms/form-adm-payment-view.php')
                            ?>
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

    <script>
        $(function() {
            $("#table1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $('#table2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
</body>

</html>