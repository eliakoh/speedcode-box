<form action="{base_uri}login" method="post" name="frm_login" role="form">
    <div class="form-group">
        <label for="txt_login">{t:Nom d'utilisateur}</label>
        <input type="text" class="form-control" id="txt_login" name="login" placeholder="{t:Nom d'utilisateur}">
    </div>
    <div class="form-group">
        <label for="txt_pwd">{t:Mot de passe}</label>
        <input class="form-control" type="password" id="txt_pwd" name="password" placeholder="{t:Mot de passe}">
    </div>
    <button type="submit" class="btn btn-default">{t:Connexion}</button>
</form>