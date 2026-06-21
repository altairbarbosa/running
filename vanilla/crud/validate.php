<?php
require_once('connection.php');
require_once('../lang/translator.php');
?>

<!DOCTYPE html>
<html>
<?php
include('../views/head.php');
?>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                </div>
            </section>

            <section class="content">
                <?php
                if (isset($_POST['submit'])) {
                    $email_session = trim(strip_tags($_POST['email']));
                    $senha_session = trim(strip_tags($_POST['senha']));

                    $sql = "SELECT * FROM usuario WHERE email=:email AND senha=:senha AND confirmacao IS NULL";
                    $senha = sha1($senha_session);
                    try {
                        $result = $connection->prepare($sql);
                        $result->bindParam(':email', $email_session, PDO::PARAM_STR);
                        $result->bindParam(':senha', $senha, PDO::PARAM_STR);
                        $result->execute();
                        $consult = $result->fetchAll();

                        $contar = $result->rowCount();
                        foreach ($consult as $row) {
                            $id_user  = $row['id'];
                            $name_user = $row['nome'];
                        }

                        if ($contar == 1) {
                            $email_session     = $_POST['email'];
                            $senha_session     = $_POST['senha'];

                            $_SESSION['id']    = $id_user;
                            $_SESSION['nome']  = $name_user;
                            $_SESSION['email'] = $email_session;

                            $sql = $connection->query("SELECT nivel FROM usuario WHERE id = '$id_user'");
                            $profile = $sql->fetchAll();

                            foreach ($profile as $row_p) {
                                switch ($row_p['nivel']) {
                                    case 1:
                                        header("Location: ../home.php");
                                        $_SESSION['nivel'] = 1;
                                        break;
                                    case 2:
                                        header("Location: ../adm-home.php");
                                        $_SESSION['nivel'] = 2;
                                        break;
                                    case 3:
                                        header("Location: ../developer.php");
                                        $_SESSION['nivel'] = 3;
                                        break;
                                }
                            }
                        } else {
                ?>
                            <script type="text/javascript">
                                alert("<?php echo $loginError; ?>")
                            </script>
                    <?php
                            header("Refresh:1, ../login.php");
                        }
                    } catch (PDOException $e) {
                        echo $e;
                    }
                } else {
                    ?>
                    <script type="text/javascript">
                        alert("<?php echo $loginError; ?>")
                    </script>
                <?php
                    header("Refresh:1, ../login.php");
                }
                ?>
            </section>
        </div>

        <div class="content">
            <div class="container">
                <div class="col-12">
                </div>
            </div>
        </div>

    </div>

    <?php
    include('../views/script.php');
    ?>
</body>

</html>