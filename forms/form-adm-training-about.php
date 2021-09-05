<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_training = $_GET["id_training"];

$sql = $connection->query("SELECT * FROM treino WHERE id = $id_training");
$count_t = $sql->fetchAll();
$treinos = [];
foreach ($count_t as $row_t) {
    $treino = new TrainingClass();
    $treino->setId($row_t['id']);
    $treino->setNome($row_t['nome']);
    array_push($treinos, $treino);
}

$sql = $connection->query("SELECT * FROM treino_exercicio WHERE id_treino = $id_training ORDER BY ordem ASC");
$count = $sql->fetchAll();
$treinos_e = [];
foreach ($count as $row) {
    $treino_e = new TrainingViewClass();
    $treino_e->setId_treino($row['id_treino']);
    $treino_e->setId_Exercicio($row['id_exercicio']);
    $treino_e->setOrdem($row['ordem']);
    $treino_e->setPeso($row['peso']);
    $treino_e->setSerie($row['serie']);
    $treino_e->setRepeticao($row['repeticao']);
    $treino_e->setDescricao($row['descricao']);
    array_push($treinos_e, $treino_e);
}

$sql = $connection->query("SELECT * FROM exercicio");
$count_e = $sql->fetchAll();
$exercicios = [];
foreach ($count_e as $row_e) {
    $exercicio = new ExerciseClass();
    $exercicio->setId($row_e['id']);
    $exercicio->setNome($row_e['nome']);
    array_push($exercicios, $exercicio);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <?php
            for ($t = 0; $t < count($treinos); $t++) {
                echo $treinos[$t]->getNome();
            }
            ?>
        </h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $name; ?></th>
                    <th><?php echo $weight; ?></th>
                    <th><?php echo $series; ?></th>
                    <th><?php echo $repetition; ?></th>
                    <th><?php echo $description; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($treinos_e); $i++) {
                ?>
                    <tr>
                        <td><?php echo $treinos_e[$i]->getOrdem(); ?></td>
                        <td>
                            <?php
                            for ($h = 0; $h < count($exercicios); $h++) {
                                if ($treinos_e[$i]->getId_exercicio() == $exercicios[$h]->getId()) {
                                    echo $exercicios[$h]->getNome();
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $treinos_e[$i]->getPeso(); ?></td>
                        <td><?php echo $treinos_e[$i]->getSerie(); ?></td>
                        <td><?php echo $treinos_e[$i]->getRepeticao(); ?></td>
                        <td><?php echo $treinos_e[$i]->getDescricao(); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>