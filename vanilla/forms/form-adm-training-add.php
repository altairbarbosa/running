<?php
$id_client = $_GET["id_user"];

$select = $connection->query("SELECT nome FROM usuario WHERE id = $id_client");
$cliente = $select->fetchAll();

foreach ($cliente as $row) {
    $nome = $row['nome'];
}
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $enterInformation; ?></h3>
    </div>
    <form action="crud/insert/insert-training.php" method="post" role="form">
        <div class="card-body">
            <input hidden type="text" class="form-control" value="<?php echo $id_client; ?>" id="id_user" name="id_user">
            <div class="form-group">
                <label for="name_Client"><?php echo $client; ?></label>
                <input type="text" class="form-control" placeholder="<?php echo $row['nome']; ?>" id="name_Client" name="name_client" disabled>
            </div>
            <div class="form-group">
                <label for="name_Training"><?php echo $nameTraining; ?></label>
                <input type="text" class="form-control" id="name_Training" name="name_training">
            </div>
            <div class="form-group">
                <label for="date_Start"><?php echo $dateStart; ?></label>
                <input type="date" class="form-control" id="date_Start" placeholder="" value="" name="date_start">
            </div>
            <div class="form-group">
                <label for="date_End"><?php echo $dateEnd; ?></label>
                <input type="date" class="form-control" id="date_End" placeholder="" value="" name="date_end">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?php echo $register; ?></button>
        </div>
    </form>
</div>