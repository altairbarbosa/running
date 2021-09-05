<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sql = $connection->query("SELECT * FROM usuario ORDER BY nome ASC;");
$count = $sql->fetchAll();
$usuarios = [];
foreach ($count as $row) {
    $usuario = new UserClass();
    $usuario->setId($row['id']);
    $usuario->setNome($row['nome']);
    $usuario->setEmail($row['email']);
    $usuario->setSenha($row['senha']);
    $usuario->setNivel($row['nivel']);
    $usuario->setConfirmacao($row['confirmacao']);
    $usuario->setEndereco($row['endereco']);
    $usuario->setTelefone($row['telefone']);
    $usuario->setIdade($row['idade']);
    array_push($usuarios, $usuario);
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?php echo $details; ?></h3>
    </div>
    <div class="card-body">
        <table id="table1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo $identifier; ?></th>
                    <th><?php echo $name; ?></th>
                    <th><?php echo $email; ?></th>
                    <th><?php echo $profile; ?></th>
                    <th><?php echo $confirmation; ?></th>
                    <th><?php echo $address; ?></th>
                    <th><?php echo $phone; ?></th>
                    <th><?php echo $age; ?></th>
                    <th><?php echo $action; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i = 0; $i < count($usuarios); $i++) {
                ?>
                    <tr>
                        <td><?php echo $usuarios[$i]->getId(); ?></td>
                        <td><?php echo $usuarios[$i]->getNome(); ?></td>
                        <td><?php echo $usuarios[$i]->getEmail(); ?></td>
                        <td><?php echo $usuarios[$i]->getNivel(); ?></td>
                        <td><?php echo $usuarios[$i]->getConfirmacao(); ?></td>
                        <td><?php echo $usuarios[$i]->getEndereco(); ?></td>
                        <td><?php echo $usuarios[$i]->getTelefone(); ?></td>
                        <td><?php echo $usuarios[$i]->getNivel(); ?></td>
                        <td>
                            <div class="row">
                                <div class="col-sm">
                                    <a href="adm-user-edit.php<?php echo '?id_user=' . $usuarios[$i]->getId(); ?>"><i class="fas fa fa-edit"></i></a> <?php echo $edit; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>