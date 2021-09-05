<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sql = $connection->query("SELECT * FROM prodserv WHERE tipo = 's';");
$count = $sql->fetchAll();
$prodservs = [];
foreach ($count as $row) {
    $prodserv = new ProdServClass();
    $prodserv->setId($row['id']);
    $prodserv->setNome($row['nome']);
    $prodserv->setValor($row['valor']);
    $prodserv->setTipo($row['tipo']);
    array_push($prodservs, $prodserv);
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
                    <th><?php echo $value; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($prodservs); $i++) {
                ?>
                    <tr>
                        <td><?php echo $prodservs[$i]->getId(); ?></td>
                        <td><?php echo $prodservs[$i]->getNome(); ?></td>
                        <td><?php echo $prodservs[$i]->getValor(); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-service-edit.php<?php echo '?id_service=' . $prodservs[$i]->getId(); ?>"><i class="fas fa fa-edit"></i></a> <?php echo $edit; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>