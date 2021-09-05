<?php
include("../connection.php");
include("../../lang/translator.php");

$id_confirm  = $_POST["id_confirm"];

if ($_POST['password'] != $_POST['confirm_password']) {
?>
    <script type="text/javascript">
        alert("<?php echo $passNoCorrespond; ?>")
    </script>
<?php header("Refresh: 1, ../../change_pass.php?confirm=' . $id_confirm . '");
    exit();
} else {
    $password   = sha1($_POST["password"]);
}

try {
    $connection->beginTransaction();
    $sql = "SELECT * FROM usuario WHERE confirmacao = '$id_confirm'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $retorn = $stmt->fetchAll();

    foreach ($retorn as $row) {
        $id_user  = $row['id'];
    }

    $sql = "UPDATE usuario SET senha = '$password', confirmacao = NULL WHERE id = '$id_user';";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

?>
    <script type="text/javascript">
        alert("<?php echo $passSucess; ?>")
    </script>
<?php
    header("Refresh: 1, ../../login.php");

    $connection->commit();
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;

?>
    <script type="text/javascript">
        alert("<?php echo $optionNoRegistered; ?>")
    </script>
<?php
    header("Refresh: 1, ../../login.php");
}
