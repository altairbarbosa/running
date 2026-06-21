<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sql = $connection->query("SELECT * FROM exercicio ORDER BY id ASC;");
$count = $sql->fetchAll();
$exercicios = [];
foreach ($count as $row) {
    $exercicio = new ExerciseClass();
    $exercicio->setId($row['id']);
    $exercicio->setNome($row['nome']);
    array_push($exercicios, $exercicio);
}
?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h4 class="my-1"><?php echo $details; ?></h4>
            </div>
            <div class="col">
                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target=".button-modal">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $name; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($exercicios); $i++) {
                ?>
                    <tr>
                        <td><?php echo $exercicios[$i]->getId(); ?></td>
                        <td><?php echo $exercicios[$i]->getNome(); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-exercise-edit.php<?php echo '?id_exercise=' . $exercicios[$i]->getId(); ?>"><i class="fas fa fa-edit"></i></a> <?php echo $edit; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<form action="crud/insert/insert-exercise.php" method="post">
    <div class="modal fade button-modal" tabindex="-1" role="dialog" aria-labelledby="button-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo $exercise; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nameExercise"><?php echo $name; ?></label>
                        <input type="text" class="form-control" id="nameExercise" name="nameExercise">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="submit"><?php echo $register; ?></button>
                </div>
            </div>
        </div>
    </div>
</form>