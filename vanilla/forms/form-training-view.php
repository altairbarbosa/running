<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_training = $_GET['id_training'];

$select = $connection->query("SELECT * FROM exercicio;");
$exercicios_b = $select->fetchAll();

$select = $connection->query("SELECT * FROM treino WHERE id = '$id_training';");
$treinos_b = $select->fetchAll();

$sql = $connection->query("SELECT * FROM treino_exercicio WHERE id_treino = '$id_training';");
$count = $sql->fetchAll();
$exercicios = [];
foreach ($count as $row) {
    $exercicio = new TrainingViewClass();
    $exercicio->setId_treino($row['id_treino']);
    $exercicio->setId_exercicio($row['id_exercicio']);
    $exercicio->setPeso($row['peso']);
    $exercicio->setSerie($row['serie']);
    $exercicio->setRepeticao($row['repeticao']);
    $exercicio->setDescricao($row['descricao']);
    array_push($exercicios, $exercicio);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <?php
            foreach ($treinos_b as $row_t) {
                echo $training . ': ' . $row_t['nome'];
            }
            ?>
        </h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $exercise; ?></th>
                    <th><?php echo $weight; ?></th>
                    <th><?php echo $series; ?></th>
                    <th><?php echo $repetition; ?></th>
                    <th><?php echo $description; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($exercicios); $i++) {
                ?>
                    <tr>
                        <td>
                            <?php
                            foreach ($exercicios_b as $row_e) {
                                if ($exercicios[$i]->getId_exercicio() == $row_e['id']) {
                                    echo $row_e['nome'];
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $exercicios[$i]->getPeso(); ?></td>
                        <td><?php echo $exercicios[$i]->getSerie(); ?></td>
                        <td><?php echo $exercicios[$i]->getRepeticao(); ?></td>
                        <td><?php echo $exercicios[$i]->getDescricao(); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>