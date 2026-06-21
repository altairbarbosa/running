<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_purchase = $_GET["id_purchase"];

$sql = $connection->query("SELECT * FROM debito WHERE id_comp = $id_purchase");
$count = $sql->fetchAll();
$debitos = [];
foreach ($count as $row) {
    $debito = new DebtClass();
    $debito->setId($row['id']);
    $debito->setId_pag($row['id_pag']);
    $debito->setId_comp($row['id_comp']);
    $debito->setVencimento($row['vencimento']);
    $debito->setParcela($row['parcela']);
    $debito->setValor($row['valor']);
    $debito->setValor_pag($row['valor_pag']);
    $debito->setData_pag($row['data_pag']);
    array_push($debitos, $debito);
}

$select = $connection->query("SELECT nome FROM usuario WHERE id = $id_user");
$usuario = $select->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo $payment; ?></h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $identifier . ' ' . $payment; ?></th>
                    <th><?php echo $identifier . ' ' . $purchase; ?></th>
                    <th><?php echo $expiration; ?></th>
                    <th><?php echo $portion; ?></th>
                    <th><?php echo $value; ?></th>
                    <th><?php echo $value . ' ' . $payment; ?></th>
                    <th><?php echo $date; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($debitos); $i++) {
                ?>
                    <tr>
                        <td><?php echo $debitos[$i]->getId(); ?></td>
                        <td><?php echo $debitos[$i]->getId_pag(); ?></td>
                        <td><?php echo $debitos[$i]->getId_comp(); ?></td>
                        <td><?php echo $debitos[$i]->getVencimento(); ?></td>
                        <td><?php echo $debitos[$i]->getParcela(); ?></td>
                        <td><?php echo $debitos[$i]->getValor(); ?></td>
                        <td><?php echo $debitos[$i]->getValor_pag(); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($debitos[$i]->getData_pag())); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-purchase-about.php<?php echo '?id_purchase=' . $debitos[$i]->getId(); ?>"><i class="fas fa fa-eye"></i></a> <?php echo $details; ?>
                                </div>
                                <div class="col-sm">
                                    <a href="adm-payment-add.php<?php echo '?id_purchase=' . $debitos[$i]->getId(); ?>"><i class="fas fa fa-plus-square"></i></a> <?php echo $add; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>