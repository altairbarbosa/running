<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_SESSION['id'];

$sql = $connection->query("SELECT * FROM treino WHERE id_usuario = '$id_user';");
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
                        <td><?php echo $treinos[$i]->getNome(); ?></td>
                        <td>
                            <?php
                            $data_inicio = $treinos[$i]->getData_inicio();
                            if ($data_inicio == NULL) {
                                echo NULL;
                            } else {
                                echo date('d/m/Y', strtotime($data_inicio));
                            };
                            ?>
                        </td>
                        <td>
                            <?php
                            $data_fim = $treinos[$i]->getData_fim();
                            if ($data_fim == NULL) {
                                echo NULL;
                            } else {
                                echo date('d/m/Y', strtotime($data_fim));
                            };
                            ?>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="training-view.php<?php echo '?id_training=' . $treinos[$i]->getId(); ?>"><i class="fas fa fa-eye"></i></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>