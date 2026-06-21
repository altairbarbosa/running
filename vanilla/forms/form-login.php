<form action="crud/validate.php" method="post">
    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="<?php echo $email; ?>" name="email" require="">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" class="form-control" placeholder="<?php echo $password; ?>" name="senha" require="">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block" name="submit"><?php echo $login; ?></button>
        </div>
    </div>
</form>