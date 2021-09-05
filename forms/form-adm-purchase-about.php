<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_purchase = $_GET["id_purchase"];

$sql = $connection->query("SELECT * FROM itens WHERE id_comp = $id_purchase");
$count = $sql->fetchAll();
$itens = [];
foreach ($count as $row) {
    $item = new ItemsClass();
    $item->setId_comp($row['id_comp']);
    $item->setId_prodserv($row['id_prodserv']);
    $item->setQuantidade($row['quantidade']);
    $item->setValor($row['valor']);
    array_push($itens, $item);
}

$sql = $connection->query("SELECT * FROM prodserv");
$count_e = $sql->fetchAll();
$prodservs = [];
foreach ($count_e as $row_sp) {
    $prodserv = new ProdServClass();
    $prodserv->setId($row_sp['id']);
    $prodserv->setNome($row_sp['nome']);
    $prodserv->setValor($row_sp['valor']);
    $prodserv->setTipo($row_sp['tipo']);
    array_push($prodservs, $prodserv);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <?php
            echo $details;
            ?>
        </h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $product . $or . $service; ?></th>
                    <th><?php echo $amount; ?></th>
                    <th><?php echo $value; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($itens); $i++) {
                ?>
                    <tr>
                        <td><?php echo $itens[$i]->getId_prodserv(); ?></td>
                        <td>
                            <?php
                            for ($h = 0; $h < count($prodservs); $h++) {
                                if ($itens[$i]->getId_prodserv() == $prodservs[$h]->getId()) {
                                    echo $prodservs[$h]->getNome();
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $itens[$i]->getQuantidade(); ?></td>
                        <td><?php echo $itens[$i]->getValor(); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>