<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sql = $connection->query("SELECT * FROM usuario ORDER BY nome ASC");
$count = $sql->fetchAll();
$usuarios = [];
foreach ($count as $row) {
    $usuario = new UserClass();
    $usuario->setId($row['id']);
    $usuario->setNome($row['nome']);
    array_push($usuarios, $usuario);
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
    <form action="crud/insert/insert-purchase.php" method="POST" role="form">
        <div class="card-body">
            <div class="form-group">
                <label><?php echo $client; ?></label>
                <select class="form-control select2bs4" style="width: 100%;">
                    <option selected="selected"><?php echo $option; ?></option>
                    <?php
                    for ($i = 0; $i < count($usuarios); $i++) {
                    ?>
                        <option value="<?php echo $usuarios[$i]->getId(); ?>"><?php echo $usuarios[$i]->getNome(); ?></option>
                    <?php } ?>
                </select>
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
                <label for="valueProdServ"><?php echo $value; ?></label>
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