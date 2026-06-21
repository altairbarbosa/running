<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$select = $connection->query("SELECT id, nome FROM usuario");
$usuarios = $select->fetchAll();

$sql = $connection->query("SELECT * FROM compra");
$count = $sql->fetchAll();
$compras = [];
foreach ($count as $row) {
    $compra = new PurchaseClass();
    $compra->setId($row['id']);
    $compra->setId_usuario($row['id_usuario']);
    $compra->setTotal($row['total']);
    $compra->setData_comp($row['data_comp']);
    array_push($compras, $compra);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo $searchClient; ?></h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $name; ?></th>
                    <th><?php echo $total; ?></th>
                    <th><?php echo $datePurchase; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($compras); $i++) {
                ?>
                    <tr>
                        <td><?php echo $compras[$i]->getId(); ?></td>
                        <td>
                            <?php
                            foreach ($usuarios as $row_u) {
                                if ($row_u['id'] == $compras[$i]->getId_usuario()){
                                    echo $row_u['nome'];
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $compras[$i]->getTotal(); ?></td>
                        <td><?php echo $compras[$i]->getData_comp(); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-payment-view.php<?php echo '?id_purchase=' . $compras[$i]->getId(); ?>"><i class="fas fa fa-eye"></i></a> <?php echo $view; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>