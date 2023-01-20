<?php

use admin\modules\rbac\models\RoleModelPermission;

?>

<div class="role-permission-index">

    <?php foreach($roles as $role):?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 data-toggle="collapse" href="#collapse_m_<?= $role->id?>">
                    <?=$role->name;?>
                </h4>
                <div class="glyphicon glyphicon-remove role-remove" onclick="removeRole('model',<?= $role->id ?>)"></div>
            </div>

            <div id="collapse_m_<?= $role->id?>" class="panel-collapse collapse">
                <?php foreach ($models as $model):?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#car_accordion" href="#collapse_m_<?=$role->id . "_" . $model->id?>">
                                    <?=$model->name?>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_m_<?=$role->id . "_" . $model->id?>" class="panel-collapse collapse">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <td>
                                        id
                                    </td>
                                    <td>
                                        Имя экшена
                                    </td>
                                    <td>
                                        Чтение
                                    </td>
                                    <td>
                                        Изменение
                                    </td>
                                </tr>
                                </thead>
                                <tbody>

                                <?php foreach ($fields as $field):?>
                                    <?php if($field->model_id != $model->id) continue; ?>
                                    <tr>
                                        <td>
                                            <?= $field->id?>
                                        </td>
                                        <td>
                                            <?= $field->name?>
                                        </td>
                                        <td>
                                            <?php
                                            if (RoleModelPermission::find()->where(['role_id' => $role->id, 'field_id' => $field->id, 'type' => 0])->one()){
                                                $checked = 'checked';
                                            } else $checked = '';
                                            ?>
                                            <div class="checkbox">
                                                <label><input id="checkbox_<?=$role->id . "_" . $field->id?>_0" type="checkbox" onclick="refreshModelPermission(<?=$role->id?>,<?=$field->id?>,0)" value="" <?= $checked?>></label>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            if (RoleModelPermission::find()->where(['role_id' => $role->id, 'field_id' => $field->id, 'type' => 1])->one()){
                                                $checked = 'checked';
                                            } else $checked = '';
                                            ?>
                                            <div class="checkbox">
                                                <label><input id="checkbox_<?=$role->id . "_" . $field->id?>_1" type="checkbox" onclick="refreshModelPermission(<?=$role->id?>,<?=$field->id?>,1)" value="" <?= $checked?>></label>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


    <?php endforeach; ?>
</div>