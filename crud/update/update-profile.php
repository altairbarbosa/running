<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../connection.php");
include("../../lang/translator.php");

$id      = $_POST["id"];
$name    = $_POST["name"];
$email   = $_POST["email"];
$address = $_POST["address"];
$phone   = $_POST["phone"];
$age     = $_POST["age"];

try {

    $connection->beginTransaction();
    $sql = "UPDATE usuario SET nome='$name', email='$email', endereco='$address', telefone='$phone', idade='$age' WHERE id = $id";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    $connection->commit();

?>
    <script type="text/javascript">
        alert("<?php echo $optionUpdateSucess; ?>")
        history.go(-1)
    </script>
<?php
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;

?>
    <script type="text/javascript">
        alert("<?php echo $optionNoRegistered; ?>")
        history.go(-1)
    </script>
<?php
}
