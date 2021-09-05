<?php
include('../connection.php');
include('../../lang/translator.php');

$id_user = $_POST['id_user'];
$name_training = $_POST['name_training'];
$date_start = $_POST['date_start'];
$date_end = $_POST['date_end'];

try {
	$connection->beginTransaction();
	$sql = "INSERT INTO treino (id_usuario, nome, data_inicio, data_fim) VALUES ('$id_user', '$name_training', '$date_start', '$date_end');";
	$stmt = $connection->prepare($sql);
    $stmt->execute();

    $sequencia = "SELECT LAST_INSERT_ID() FROM treino";

    $stmt = $connection->prepare($sequencia);
    $stmt->execute();

    $id = $stmt->fetch();
    $id_training = $id[0];

    $connection->commit();
?>
	<script type="text/javascript">
		alert("<?php echo $optionRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-training-exercise.php?id_training=" . $id_training . "&id_user=" . $id_user);
} catch (\PDOException $e) {
	$connection->rollBack();
	throw $e;

?>
	<script type="text/javascript">
		alert("<?php echo $optionNoRegistered; ?>")
	</script>
<?php

	header("Refresh: 1, ../../adm-training.php?id_user=" . $id_user);
}
?>