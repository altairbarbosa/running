<?php
include('../connection.php');
include('../../lang/translator.php');

$userProdServ = $_POST['userProdServ'];
$prodServ = $_POST['prodServ'];
$valueProdServ = $_POST['valueProdServ'];
$dateProdServ = $_POST['dateProdServ'];

try {

	$connection->beginTransaction();
	$sql = "INSERT INTO compra (id_usuario, total, data_comp) VALUES ('$userProdServ', '$valueProdServ', '$dateProdServ');";
	$stmt = $connection->prepare($sql);
	$stmt->execute();

	$connection->commit();
?>
	<script type="text/javascript">
		alert("<?php echo $optionRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-service.php");
} catch (\PDOException $e) {
	$connection->rollBack();
	throw $e;

?>
	<script type="text/javascript">
		alert("<?php echo $optionNoRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-service.php");
}
?>