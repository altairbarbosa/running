<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_SESSION['id'];

try {
    $connection->beginTransaction();
    $sql = "SELECT * FROM usuario WHERE id = '$id_user'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    foreach ($result as $row) {
        $id            = $row["id"];
        $name_b        = $row["nome"];
        $email_b       = $row["email"];
        $phone_b       = $row["telefone"];
        $age_b         = $row["idade"];
        $address_b     = $row["endereco"];
    }

    $connection->commit();
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;
}
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $enterInformation; ?></h3>
    </div>
    <form action="crud/update/update-profile.php" method="POST" role="form">
        <div class="card-body">
            <input hidden type="text" class="form-control" value="<?php echo $id; ?>" id="id" name="id">
            <div class="form-group">
                <label for="name"><?php echo $name; ?></label>
                <input type="text" class="form-control" value="<?php echo $name_b; ?>" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="email"><?php echo $email; ?></label>
                <input type="text" class="form-control" value="<?php echo $email_b; ?>" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="address"><?php echo $address; ?></label>
                <input type="text" class="form-control" value="<?php echo $address_b; ?>" id="address" name="address">
            </div>
            <div class="form-group">
                <label for="phone"><?php echo $phone; ?></label>
                <input type="text" class="form-control" value="<?php echo $phone_b; ?>" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="age"><?php echo $age; ?></label>
                <input type="text" class="form-control" value="<?php echo $age_b; ?>" id="age" name="age">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?php echo $submit; ?></button>
        </div>
    </form>
</div>