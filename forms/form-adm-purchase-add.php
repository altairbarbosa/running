<?php
$id_client = $_GET["id_user"];

$select = $connection->query("SELECT nome FROM usuario WHERE id = $id_client");
$cliente = $select->fetchAll();

foreach ($cliente as $row_client) {
    $nome = $row_client['nome'];
}

$sql = $connection->query("SELECT * FROM prodserv ORDER BY nome ASC");
$count = $sql->fetchAll();
$prodservs = [];
foreach ($count as $row) {
    $prodserv = new ProdServClass();
    $prodserv->setId($row['id']);
    $prodserv->setNome($row['nome']);
    array_push($prodservs, $prodserv);
}
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $enterInformation; ?></h3>
    </div>
    <form action="crud/insert/insert-purchase.php" method="post" role="form">
        <div class="card-body">
            <div class="form-group">
                <label for="nameClient"><?php echo $client; ?></label>
                <input type="text" class="form-control" placeholder="<?php echo $row_client['nome']; ?>" id="nameClient" name="nameClient" disabled>
            </div>
            <div class="form-group">
                <label><?php echo $product . $or . $service; ?></label>
                <select class="form-control select2bs4" style="width: 100%;">
                    <option selected="selected"><?php echo $option; ?></option>
                    <?php
                    for ($i = 0; $i < count($prodservs); $i++) {
                    ?>
                        <option value="<?php echo $prodservs[$i]->getId(); ?>"><?php echo $prodservs[$i]->getNome(); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="valueAmount"><?php echo $amount; ?></label>
                <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" class="form-control" id="valueAmount" name="valueAmount">
            </div>
            <div class="form-group">
                <label for="valueProdServ"><?php echo $value . ' ' . $total; ?></label>
                <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" class="form-control" id="valueProdServ" name="valueProdServ">
            </div>
            <div class="form-group">
                <label for="dateProdServ"><?php echo $date; ?></label>
                <input type="date" class="form-control" id="dateProdServ" name="dateProdServ">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?php echo $submit; ?></button>
        </div>
    </form>
</div>