<?php
require_once('crud/connection.php');
require_once('lang/translator.php');

$id = $_GET['confirm'];

try {
    $connection->beginTransaction();
    $sql = "SELECT confirmacao FROM usuario WHERE confirmacao = '$id'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $retorn = $stmt->fetchAll();

    foreach ($retorn as $row) {
        $confirmacao  = $row['confirmacao'];
    }

    if ($id == $confirmacao) {
        $sequencia = "UPDATE usuario SET confirmacao = NULL WHERE confirmacao = '$id';";
        $stmt = $connection->prepare($sequencia);
        $stmt->execute();

?>
        <script type="text/javascript">
            alert("<?php echo $emailChecked; ?>")
        </script>
    <?php
        header("Refresh:1, login.php");
    } else {
    ?>
        <script type="text/javascript">
            alert("<?php echo $optionNoRegistered; ?>")
        </script>
<?php
        header("Refresh:1, register-user.php");
    }
    $connection->commit();
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;
}
?>