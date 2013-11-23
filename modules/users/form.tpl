<form class="form-horizontal" role="form" method="post" id="form-users-add">
    <div class="form-group">
        <label for="form-lastname" class="col-sm-2 control-label">{t:Nom}</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="lastname" id="form-lastname">
        </div>
    </div>
    <div class="form-group">
        <label for="form-firstname" class="col-sm-2 control-label">{t:Prénom}</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="firstname" id="form-firstname">
        </div>
    </div>
    <div class="form-group">
        <label for="form-email" class="col-sm-2 control-label">{t:E-mail}</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" name="email" id="form-email">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <div class="checkbox">
                <label>
                    <input type="checkbox"> Remember me
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Enregistrer</button>
        </div>
    </div>
</form>