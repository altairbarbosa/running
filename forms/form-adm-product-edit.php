<?php
$id_product = $_GET['id_product'];

try {
    $connection->beginTransaction();
    $sql = "SELECT * FROM prodserv WHERE id = '$id_product'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    foreach ($result as $row_p) {
        $id            = $row_p["id"];
        $name_b        = $row_p["nome"];
        $valor_b       = $row_p["valor"];
        $tipo_b        = $row_p["tipo"];
    }

    $connection->commit();
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;
}
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $changeData; ?></h3>
    </div>
    <form action="adm-purchase-search.php" method="post" role="form">
        <div class="card-body">
            <input hidden type="text" class="form-control" value="<?php echo $id; ?>" id="id" name="id">
            <div class="form-group">
                <label for="nameProduct"><?php echo $name; ?></label>
                <input type="text" class="form-control" value="<?php echo $name_b; ?>" id="nameProduct" name="nameProduct">
            </div>
            <div class="form-group">
                <label for="valueProduct"><?php echo $value; ?></label>
                <input type="text" class="form-control" value="<?php echo $valor_b; ?>" id="valueProduct" name="valueProduct">
            </div>
            <input hidden type="text" class="form-control" value="<?php echo $tipo_b; ?>" id="type" name="type">
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?php echo $update; ?></button>
        </div>
    </form>
</div>