<?php
$id_training = $_GET["id_training"];
$id_user     = $_GET["id_user"];

$select = $connection->query("SELECT * FROM treino WHERE id = $id_training");
$treino = $select->fetchAll();

foreach ($treino as $row_t) {
    $nome = $row_t['nome'];
}

$select = $connection->query("SELECT * FROM exercicio");
$exercicio = $select->fetchAll();
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $enterInformation; ?></h3>
    </div>
    <form action="crud/insert/insert-training-exercise.php" method="post" role="form">
        <div class="card-body">
            <input hidden type="text" class="form-control" value="<?php echo $id_user; ?>" id="id_user" name="id_user">
            <input hidden type="text" class="form-control" value="<?php echo $id_training; ?>" id="id_training" name="id_training">
            <div class="form-group">
                <label for="name_Client"><?php echo $nameTraining; ?></label>
                <input type="text" class="form-control" placeholder="<?php echo $row_t['nome']; ?>" id="name_Client" name="name_client" disabled>
            </div>
            <div class="row" id="source">
                <div class="col-md-1">
                    <div class="form-group">
                        <label><?php echo $order; ?></label>
                        <select class="form-control" style="width: 100%;" id="order" name="order[]">
                            <option value="1"> 1 </option>
                            <option value="2"> 2 </option>
                            <option value="3"> 3 </option>
                            <option value="4"> 4 </option>
                            <option value="5"> 5 </option>
                            <option value="6"> 6 </option>
                            <option value="7"> 7 </option>
                            <option value="8"> 8 </option>
                            <option value="9"> 9 </option>
                            <option value="10"> 10 </option>
                            <option value="11"> 11 </option>
                            <option value="12"> 12 </option>
                            <option value="13"> 13 </option>
                            <option value="14"> 14 </option>
                            <option value="15"> 15 </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo $exercise; ?></label>
                        <select class="form-control" style="width: 100%;" id="id_exercise" name="id_exercise[]">
                            <option value=""><?php echo $option; ?></option>
                            <?php
                            foreach ($exercicio as $row_e) { ?>
                                <option value="<?php echo $row_e['id']; ?>"> <?php echo $row_e['nome']; ?> </option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="exe_weight"><?php echo $weight; ?></label>
                        <input type="text" class="form-control" id="exe_weight" name="exe_weight[]">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="exe_repetition"><?php echo $repetition; ?></label>
                        <input type="text" class="form-control" id="exe_repetition" name="exe_repetition[]">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="exe_series"><?php echo $series; ?></label>
                        <input type="text" class="form-control" id="exe_series" name="exe_series[]">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="exe_description"><?php echo $description; ?></label>
                        <input type="text" class="form-control" id="exe_description" name="exe_description[]">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="exe_description"><?php echo $add; ?></label>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="form-control btn btn-primary" onclick="cloneInput();"> + </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="form-control btn btn-primary" onclick="removeInput(this);"> - </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="destiny"></div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?php echo $register; ?></button>
        </div>
    </form>
</div>

<script>
    function cloneInput() {
        var clone = document.getElementById('source').cloneNode(true);
        var destiny = document.getElementById('destiny');
        destiny.appendChild(clone);

        var camposClonados = clone.getElementsByTagName('input');

        for (i = 0; i < camposClonados.length; i++) {
            camposClonados[i].value = '';
        }
    }

    function removeInput(id) {
        var node1 = document.getElementById('destiny');
        node1.removeChild(node1.childNodes[0]);
    }
</script>