<?php
include("../connection.php");
include("../../lang/translator.php");

$id_user        = $_POST["id_user"];
$name_client    = $_POST["name_client"];
$address_client = $_POST["address_client"];
$email_client   = $_POST["email_client"];
$phone_client   = $_POST["phone_client"];
$age_client     = $_POST["age_client"];
$id_nivel       = $_POST["id_nivel"];

try {

    $connection->beginTransaction();
    $sql = "UPDATE usuario SET nome='$name_client', email='$email_client', nivel='$id_nivel', endereco='$address_client', telefone='$phone_client', idade='$age_client' WHERE id = $id_user";
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
