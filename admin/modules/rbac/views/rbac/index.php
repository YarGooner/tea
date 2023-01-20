<?php

use yii\helpers\Html;
use yii\grid\GridView;
use admin\modules\rbac\models\RolePermission;
use admin\modules\rbac\assets\RbacAssets;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $searchModel admin\models\RolePermissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Role Permissions');
$this->params['breadcrumbs'][] = $this->title;
RbacAssets::register($this);
?>

<?php
echo Tabs::widget([
    'items' => [
        [
            'label' => 'Пользователи',
            'content' => '<div id="user-ajax">'.$this->render('_user_ajax', [
                'users' => $users,
                'rolesAssign' => $rolesAssign,
                'roles' => $roles
            ]). '</div>',
            'active' => true
        ],
        [
            'label' => 'Разрешения',
            'content' => $this->render('_permission_ajax', [
                'roles' => $roles,
                'controllers' => $controllers,
                'actions' => $actions,
            ]),
        ],
        [
            'label' => 'Добавить роль',
            'content' => $this->render('_role_create_ajax', [
                'roleModel' => $roleModel,
            ]),
            'headerOptions' => [
                'id' => 'someId'
            ]
        ],
    ]
]);
?>

