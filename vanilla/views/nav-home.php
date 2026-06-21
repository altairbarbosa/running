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
                <a class="dropdown-item" href="profile.php">
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

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="home.php" class="brand-link">
        <img src="assets/img/logo/logo-color.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $title; ?></span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="home.php" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p><?php echo $home; ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="training.php" class="nav-link">
                        <i class="nav-icon fas fa-dumbbell"></i>
                        <p><?php echo $training; ?></p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>