<?php

namespace admin\modules\rbac\controllers;

use admin\modules\rbac\models\Action;
use admin\modules\rbac\models\Controller;
use admin\modules\rbac\models\Role;
use Yii;
use admin\modules\rbac\models\RolePermission;
use admin\modules\rbac\models\RolePermissionSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RolePermissionController implements the CRUD actions for RolePermission model.
 */
class RolePermissionController extends RbacController
{
    /**
     * Lists all RolePermission models.
     * @return mixed
     */
//    public function actionIndex()
//    {
//        $searchModel = new RolePermissionSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    public function actionIndex()
    {
        $roles = Role::find()->all();
        $controllers = Controller::find()->all();
        $actions = Action::find()->all();


        return $this->render('index', [
            'roles' => $roles,
            'controllers' => $controllers,
            'actions' => $actions,
        ]);
    }

    /**
     * Displays a single RolePermission model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RolePermission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RolePermission();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RolePermission model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RolePermission model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAdd(){
        $roleId = Yii::$app->request->post('role_id');
        $actionId = Yii::$app->request->post('action_id');
        $old_perm = RolePermission::find()->where(['role_id' => $roleId, 'action_id' => $actionId])->one();
        if($old_perm){
            return false;
        }
        $new_perm = new RolePermission();
        $new_perm->role_id = $roleId;
        $new_perm->action_id = $actionId;
        if(!$new_perm->save()){
            var_dump($new_perm->errors);
        }
        return true;
    }

    public function actionRemove(){
        $roleId = Yii::$app->request->post('role_id');
        $actionId = Yii::$app->request->post('action_id');
        $old_perm = RolePermission::find()->where(['role_id' => $roleId, 'action_id' => $actionId])->one();
        if($old_perm){
            $old_perm->delete();
            return true;
        }
        return false;
    }

    /**
     * Finds the RolePermission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RolePermission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RolePermission::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
