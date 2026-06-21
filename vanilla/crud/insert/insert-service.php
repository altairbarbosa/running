<?php
include('../connection.php');
include('../../lang/translator.php');

$nameService = $_POST['nameService'];
$valueService = $_POST['valueService'];
$type = 's';

try {

	$connection->beginTransaction();
	$sql = "INSERT INTO prodserv (nome, valor, tipo) VALUES ('$nameService', '$valueService', '$type');";
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