<?php

use admin\modules\rbac\models\RoleAssign;

?>

<div>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <td>ID</td>
            <td>Имя пользователя</td>
            <td>Роль</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <?php $userRole = RoleAssign::find()->where(['user_id' => $user->id])->one()?>
            <tr>
                <td>
                    <?= $user->id?>
                </td>
                <td>
                    <?= $user->username?>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><?= $userRole->role->name ?>
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <?php foreach ($roles as $role):?>
                            <li><a onclick="changeRole(<?= $user->id?>,<?= $role->id?>)"><?= $role->name?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>