<?php
$id_user = $_SESSION['id'];

try {
    $connection->beginTransaction();
    $sql = "SELECT * FROM usuario WHERE id = '$id_user'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    foreach ($result as $row_p) {
        $id            = $row_p["id"];
        $name_b        = $row_p["nome"];
        $email_b       = $row_p["email"];
        $phone_b       = $row_p["telefone"];
        $age_b         = $row_p["idade"];
        $address_b     = $row_p["endereco"];
    }

    $connection->commit();
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;
}
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fas fa-globe"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">
                <a href="?lang=en" class="dropdown-item">
                    <i class="flag-icon flag-icon-us mr-2"></i> <?php echo $english; ?>
                </a>
                <a href="?lang=es" class="dropdown-item">
                    <i class="flag-icon flag-icon-es mr-2"></i> <?php echo $spanish; ?>
                </a>
                <a href="?lang=pt" class="dropdown-item">
                    <i class="flag-icon flag-icon-br mr-2"></i> <?php echo $portuguese; ?>
                </a>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">
                <a class="dropdown-item" type="button" data-toggle="modal" data-target="#profileModal">
                    <i class="fas fa-user"></i>
                    <?php echo $_SESSION['nome']; ?>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="?exit">
                    <?php echo $exit ?>
                </a>
            </div>
        </li>
    </ul>
</nav>

<div class="modal fade" id="profileModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="crud/update/update-profile.php" method="POST" role="form">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $profile; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input hidden type="text" class="form-control" value="<?php echo $id; ?>" id="id" name="id">
                    <div class="form-group">
                        <label for="name"><?php echo $name; ?></label>
                        <input type="text" class="form-control" value="<?php echo $name_b; ?>" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="email"><?php echo $email; ?></label>
                        <input type="text" class="form-control" value="<?php echo $email_b; ?>" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="address"><?php echo $address; ?></label>
                        <input type="text" class="form-control" value="<?php echo $address_b; ?>" id="address" name="address">
                    </div>
                    <div class="form-group">
                        <label for="phone"><?php echo $phone; ?></label>
                        <input type="text" class="form-control" value="<?php echo $phone_b; ?>" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="age"><?php echo $age; ?></label>
                        <input type="text" class="form-control" value="<?php echo $age_b; ?>" id="age" name="age">
                    </div>
                </div>
                <div class="modal-footer right-content-between">
                    <button type="submit" class="btn btn-primary"><?php echo $update; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="adm-home.php" class="brand-link">
        <img src="assets/img/logo/logo-color.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $title; ?></span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="adm-home.php" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p><?php echo $home; ?></p>
                    </a>
                </li>

                <li class="nav-header"><?php echo $register; ?></li>

                <li class="nav-item">
                    <a href="adm-training.php" class="nav-link">
                        <i class="fas fa-stream nav-icon"></i>
                        <p><?php echo $training; ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="adm-purchase.php" class="nav-link">
                        <i class="fas fa-shopping-cart nav-icon"></i>
                        <p><?php echo $purchase; ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="adm-payment.php" class="nav-link">
                        <i class="fas fa-credit-card nav-icon"></i>
                        <p><?php echo $payment; ?></p>
                    </a>
                </li>

                <li class="nav-header"><?php echo $debts; ?></li>

                <li class="nav-item">
                    <a href="adm-verify.php" class="nav-link">
                        <i class="nav-icon fas fa-search"></i>
                        <p><?php echo $verify; ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="adm-report.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p><?php echo $report; ?></p>
                    </a>
                </li>

                <li class="nav-header"><?php echo $dataBase; ?></li>

                <li class="nav-item">
                    <a href="adm-product.php" class="nav-link">
                        <i class="fas fa-tshirt nav-icon"></i>
                        <p><?php echo $product; ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="adm-service.php" class="nav-link">
                        <i class="fas fa-bookmark nav-icon"></i>
                        <p><?php echo $service; ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="adm-exercise.php" class="nav-link">
                        <i class="nav-icon fas fa-dumbbell"></i>
                        <p><?php echo $exercise; ?></p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>