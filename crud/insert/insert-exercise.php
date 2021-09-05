<?php
include('../connection.php');
include('../../lang/translator.php');

$nameExercise = $_POST['nameExercise'];

try {

	$connection->beginTransaction();
	$sql = "INSERT INTO exercicio (nome) VALUES ('$nameExercise');";
	$stmt = $connection->prepare($sql);
	$stmt->execute();

	$connection->commit();
?>
	<script type="text/javascript">
		alert("<?php echo $optionRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-exercise.php");
} catch (\PDOException $e) {
	$connection->rollBack();
	throw $e;

?>
	<script type="text/javascript">
		alert("<?php echo $optionNoRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-exercise.php");
}
?>