<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home"></i>
                        <?php echo $home; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        <?php echo $login; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">
                        <i class="fas fa-info-circle"></i>
                        <?php echo $about; ?>
                    </a>
                </li>
            </ul>
        </div>

        <ul class="order-1 order-md-1 navbar-nav navbar-no-expand ml-auto">
            <a href="index.php" class="navbar-brand">
                <img src="assets/img/logo/logo-color.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light"><?php echo $title; ?></span>
            </a>
        </ul>

        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
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
        </ul>
    </div>
</nav>