<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_GET['id_user'];

$sql = $connection->query("SELECT * FROM usuario ORDER BY nome ASC;");
$count = $sql->fetchAll();
$usuarios = [];
foreach ($count as $row) {
    $usuario = new UserClass();
    $usuario->setId($row['id']);
    $usuario->setNome($row['nome']);
    $usuario->setEmail($row['email']);
    array_push($usuarios, $usuario);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo $details; ?></h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $name; ?></th>
                    <th><?php echo $email; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($usuarios); $i++) {
                ?>
                    <tr>
                        <td><?php echo $usuarios[$i]->getId(); ?></td>
                        <td><?php echo $usuarios[$i]->getNome(); ?></td>
                        <td><?php echo $usuarios[$i]->getEmail(); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-verify-about.php<?php echo '?id_user=' . $usuarios[$i]->getId(); ?>"><i class="fas fa fa-eye"></i></a> <?php echo $view; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>