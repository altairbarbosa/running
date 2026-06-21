<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_GET["id_user"];

$sql = $connection->query("SELECT * FROM treino WHERE id_usuario like '%$id_user%'");
$count = $sql->fetchAll();
$treinos = [];
foreach ($count as $row) {
    $treino = new TrainingClass();
    $treino->setId($row['id']);
    $treino->setId_usuario($row['id_usuario']);
    $treino->setNome($row['nome']);
    $treino->setData_inicio($row['data_inicio']);
    $treino->setData_fim($row['data_fim']);
    array_push($treinos, $treino);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo $training; ?></h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $name; ?></th>
                    <th><?php echo $dateStart; ?></th>
                    <th><?php echo $dateEnd; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($treinos); $i++) {
                ?>
                    <tr>
                        <td><?php echo $treinos[$i]->getId(); ?></td>
                        <td><?php echo $treinos[$i]->getNome(); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($treinos[$i]->getData_inicio())); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($treinos[$i]->getData_fim())); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-training-about.php<?php echo '?id_training=' . $treinos[$i]->getId(); ?>"><i class="fas fa fa-eye"></i></a> <?php echo $view; ?>
                                </div>
                                <div class="col-sm">
                                    <a href="adm-training-edit.php<?php echo '?id_training=' . $treinos[$i]->getId(); ?>"><i class="fas fa fa-edit"></i></a> <?php echo $edit; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>