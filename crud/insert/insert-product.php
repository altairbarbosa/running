<?php
include('../connection.php');
include('../../lang/translator.php');

$nameProduct = $_POST['nameProduct'];
$valueProduct = $_POST['valueProduct'];
$type = 'p';

try {

	$connection->beginTransaction();
	$sql = "INSERT INTO prodserv (nome, valor, tipo) VALUES ('$nameProduct', '$valueProduct', '$type');";
	$stmt = $connection->prepare($sql);
	$stmt->execute();

	$connection->commit();
?>
	<script type="text/javascript">
		alert("<?php echo $optionRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-product.php");
} catch (\PDOException $e) {
	$connection->rollBack();
	throw $e;

?>
	<script type="text/javascript">
		alert("<?php echo $optionNoRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-product.php");
}
?>