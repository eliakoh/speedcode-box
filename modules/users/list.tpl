<table class="table table-striped">
    <thead>
        <tr>
            <th>{t:Nom}</th>
            <th>{t:E-mail}</th>
            <th>{t:Login}</th>
            <th>{t:Profil}</th>
            <th>Langue</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    {items}
        <tr>
            <td>{lastname} {firstname}</td>
            <td>{email}</td>
            <td><em>{login}</em></td>
            <td>{profile}</td>
            <td>{language}</td>
            <td><p class="text-center"><span class="glyphicon glyphicon-{status}-circle"></span></p></td>
            <td>
                <a class="btn btn-default btn-xs" href="#" role="button"><span class="glyphicon glyphicon-edit"></span></a>
                <a class="btn btn-default btn-xs" href="#" role="button"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
        </tr>
    {/items}
    </tbody>
</table>
<a href="users/add" class="btn btn-primary">{t:Ajouter un utilisateur}</a>