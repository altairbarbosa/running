<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo $enterInformation; ?></h3>
    </div>
    <form action="adm-verify-search.php" method="post" role="form">
        <div class="card-body">
            <div class="form-group">
                <label for="userName"><?php echo $client; ?></label>
                <input type="text" class="form-control" id="userName" name="userName">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><?php echo $search; ?></button>
        </div>
    </form>
</div>