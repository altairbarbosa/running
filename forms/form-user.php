<form action="crud/insert/insert-user.php" class="mb-3" method="post">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="<?php echo $name; ?>" name="name">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="email" class="form-control" placeholder="<?php echo $email; ?>" name="email" required="">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" id="password" class="form-control" placeholder="<?php echo $password; ?>" name="password" required="">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <input type="password" id="confirm_password" class="form-control" placeholder="<?php echo $confirmPassword; ?>" name="confirm_password" required="">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>
    <div class="input-group mb-3">
        <label class="small" for="confirm_password">
            <span id='message'></span>
        </label>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block"><?php echo $register; ?></button>
        </div>
    </div>
</form>