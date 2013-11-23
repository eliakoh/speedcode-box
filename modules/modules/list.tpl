<table class="table table-striped">
    <thead>
        <tr>
            <th>{t:Nom}</th>
            <th>{t:Nom système}</th>
            <th>Status</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    {items}
        <tr>
            <td>{label}</td>
            <td>{name}</td>
            <td><p class="text-center"><span class="glyphicon glyphicon-{status}-circle"></span></p></td>
            <td>
                <a class="btn btn-default btn-xs" href="#" role="button"><span class="glyphicon glyphicon-edit"></span></a>
                <a class="btn btn-default btn-xs" href="#" role="button"><span class="glyphicon glyphicon-remove"></span></a>
            </td>
        </tr>
    {/items}
    </tbody>
</table>