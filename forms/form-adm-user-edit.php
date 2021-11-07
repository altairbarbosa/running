<?php
$id_client = $_GET["id_user"];

$select = $connection->query("SELECT * FROM usuario WHERE id = $id_client");
$cliente = $select->fetchAll();
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $updateInformation; ?></h3>
    </div>
    <form action="crud/update/update-adm-user.php" method="post" role="form">
        <?php
        foreach ($cliente as $row) {
        ?>
            <div class="card-body">
                <input hidden type="text" class="form-control" value="<?php echo $row['id']; ?>" id="id_user" name="id_user">
                <div class="form-group">
                    <label for="name_Client"><?php echo $name; ?></label>
                    <input type="text" class="form-control" value="<?php echo $row['nome']; ?>" id="name_client" name="name_client">
                </div>
                <div class="form-group">
                    <label for="address_Client"><?php echo $address; ?></label>
                    <input type="text" class="form-control" value="<?php echo $row['endereco']; ?>" id="address_client" name="address_client">
                </div>
                <div class="form-group">
                    <label for="email_Client"><?php echo $email; ?></label>
                    <input type="text" class="form-control" value="<?php echo $row['email']; ?>" id="email_client" name="email_client">
                </div>
                <div class="form-group">
                    <label for="phone_Client"><?php echo $phone; ?></label>
                    <input type="text" class="form-control" value="<?php echo $row['telefone']; ?>" id="phone_client" name="phone_client">
                </div>
                <div class="form-group">
                    <label for="age_Client"><?php echo $age; ?></label>
                    <input type="text" class="form-control" value="<?php echo $row['idade']; ?>" id="age_client" name="age_client">
                </div>
                <div class="form-group">
                    <label><?php echo $level; ?></label>
                    <select class="form-control" style="width: 100%;" name="id_nivel">
                        <option value="<?php echo $row['nivel']; ?>">
                            <?php
                            if ($row['nivel'] == 1) {
                                echo $client;
                            } else {
                                echo $administrator;
                            }
                            ?>
                        </option>
                        <option value="1"><?php echo $client; ?></option>
                        <option value="2"><?php echo $administrator; ?></option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><?php echo $register; ?></button>
            </div>
        <?php
        }
        ?>
    </form>
</div>