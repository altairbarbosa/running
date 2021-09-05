<form action="crud/insert/insert-forgot.php" method="post">
    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="<?php echo $email; ?>" name="email">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block"><?php echo $submit; ?></button>
        </div>
    </div>
</form>