<?php

namespace admin\modules\rbac\controllers;

use Yii;
use admin\modules\rbac\models\RoleModelPermission;
use admin\modules\rbac\models\RoleModelPermissionSearch;
use admin\modules\rbac\controllers\RbacController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RoleModelPermissionController implements the CRUD actions for RoleModelPermission model.
 */
class RoleModelPermissionController extends RbacController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RoleModelPermission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RoleModelPermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RoleModelPermission model.
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
     * Creates a new RoleModelPermission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RoleModelPermission();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RoleModelPermission model.
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
     * Deletes an existing RoleModelPermission model.
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
        $fieldId = Yii::$app->request->post('field_id');
        $type = Yii::$app->request->post('type');
        $old_perm = RoleModelPermission::find()->where(['role_id' => $roleId, 'field_id' => $fieldId, 'type' => $type])->one();
        if($old_perm){
            return false;
        }
        $new_perm = new RoleModelPermission();
        $new_perm->role_id = $roleId;
        $new_perm->field_id = $fieldId;
        $new_perm->type = $type;
        if(!$new_perm->save()){
            var_dump($new_perm->errors);
        }
        return true;
    }

    public function actionRemove(){
        $roleId = Yii::$app->request->post('role_id');
        $fieldId = Yii::$app->request->post('field_id');
        $type = Yii::$app->request->post('type');
        $old_perm = RoleModelPermission::find()->where(['role_id' => $roleId, 'field_id' => $fieldId, 'type' => $type])->one();
        if($old_perm){
            $old_perm->delete();
            return true;
        }
        return false;
    }


    /**
     * Finds the RoleModelPermission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RoleModelPermission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RoleModelPermission::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
