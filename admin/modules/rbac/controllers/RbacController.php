<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 21.01.2019
 * Time: 16:34
 */

namespace admin\modules\rbac\controllers;

use admin\modules\rbac\models\RoleAssign;
use yii\web\Controller as BaseController;
use admin\modules\rbac\models\Role;
use admin\modules\rbac\models\Action;
use admin\modules\rbac\models\Controller;
use Yii;
use yii\filters\AccessControl;

class RbacController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'controllers' => ['*'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $roles = Role::find()->all();
        $controllers = Controller::find()->all();
        $actions = Action::find()->all();

        $roleModel = new Role();

        $userModel = Yii::$app->getModule('rbac')->userModel;
        $users = $userModel::find()->all();

        $rolesAssign = RoleAssign::find()->all();

        return $this->render('index', [
            'roles' => $roles,
            'controllers' => $controllers,
            'actions' => $actions,
            'roleModel' => $roleModel,
            'users' => $users,
            'rolesAssign' => $rolesAssign
        ]);
    }

    public function actionSaveRole(){
        $model = \Yii::$app->request->post('Role');
        $new_model = new Role($model);
        if(!$new_model->save()){
            var_dump($new_model->errors);
        } else {
            $roleModel = new Role();
            return $this->renderPartial('_role_create_ajax',[
                'roleModel' => $roleModel
            ]);
        }
    }

    public function actionChangeAssign(){
        $userId = Yii::$app->request->post('user_id');
        $roleId = Yii::$app->request->post('role_id');
        $roleAssign = RoleAssign::find()->where(['user_id' => $userId])->one();
        if(!$roleAssign){
            $roleAssign = new RoleAssign();
            $roleAssign->user_id = $userId;
        }
        $roleAssign->role_id = $roleId;
        if(!$roleAssign->save()){
            var_dump($roleAssign->errors);
        }

        $userModel = Yii::$app->getModule('rbac')->userModel;
        $users = $userModel::find()->all();

        $rolesAssign = RoleAssign::find()->all();
        $roles = Role::find()->all();

        return $this->renderPartial('_user_ajax',[
            'users' => $users,
            'rolesAssign' => $rolesAssign,
            'roles' => $roles
        ]);
    }

    public function actionDeleteRole()
    {

        $id = Yii::$app->request->post('id');
        $tab = Yii::$app->request->post('tab');
//        $this->findModel($id)->delete();
        $roles = Role::find()->all();
        if($tab == 'controller'){
            $controllers = Controller::find()->all();
            $actions = Action::find()->all();
            return $this->renderPartial('_permission_ajax',[
                'roles' => $roles,
                'controllers' => $controllers,
                'actions' => $actions
            ]);
        } elseif ($tab == 'model'){

        }
//
//        return $this->renderPartial(['index']);
    }

}