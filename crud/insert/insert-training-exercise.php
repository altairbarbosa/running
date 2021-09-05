<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../connection.php');
include('../../lang/translator.php');

$id_training     = $_POST["id_training"];
$id_user         = $_POST['id_user'];

$order           = $_POST['order'];
$id_exercise     = $_POST['id_exercise'];
$exe_weight      = $_POST['exe_weight'];
$exe_repetition  = $_POST['exe_repetition'];
$exe_series      = $_POST['exe_series'];
$exe_description = $_POST['exe_description'];

try {

    for ($x = 0; $x < count($id_exercise); $x++) {
        $row[] = "('{$id_training}','{$id_exercise[$x]}', '{$order[$x]}', '{$exe_series[$x]}', '{$exe_weight[$x]}', '{$exe_repetition[$x]}', '{$exe_description[$x]}')";
    }
    $array = implode(',', $row);

    $connection->beginTransaction();
    $sql = "INSERT INTO treino_exercicio (id_treino, id_exercicio, ordem, serie, peso, repeticao, descricao) VALUES {$array};";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    $connection->commit();
?>
    <script type="text/javascript">
        alert("<?php echo $optionRegistered; ?>")
    </script>
<?php

    header("Refresh: 1, ../../adm-training-add.php?id_user=" . $id_user);
} catch (\PDOException $e) {
    $connection->rollBack();
    throw $e;

?>
    <script type="text/javascript">
        alert("<?php echo $optionNoRegistered; ?>")
    </script>
<?php

    header("Refresh: 1, ../../adm-training-exercise.php?id_training=" . $id_training . "&id_user=" . $id_user);
}
