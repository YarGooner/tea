<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 23.01.2019
 * Time: 11:25
 */

namespace admin\modules\rbac\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\FileHelper;
use admin\modules\rbac\models\Model;
use admin\modules\rbac\models\Field;



class GetModelRoutesController extends RbacController
{
    public function actionIndex(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $path = __DIR__ . Yii::$app->getModule('rbac')->rootPath . Yii::$app->getModule('rbac')->modelFolder;
        $files = FileHelper::findFiles($path, [
            'only' => [
                '*.php'
            ]
        ]);
        $this->saveRoutesToDb($files);

        return ['success' => true, 'message' => 'Models have been successfully added to database'];
    }

    public function saveRoutesToDb($files){
        foreach ($files as $file){
            $modelId = $this->saveModelToDb($file);
            if($modelId){
                $this->saveFieldsToDb($file, $modelId);
            }
        }
    }

    public function saveModelToDb($modelFile){
        $fileInfo = pathinfo($modelFile);
        $file = $fileInfo['basename'];
        $modelName = $this->getModelName($file);
        if(!$modelName ){
            return false;
        }
        $model = Model::find()->where(['name' => $modelName])->one();
        if($model == false) {
            $model = new Model();
            $model->name = $modelName;
            $model->save();
        }
        return $model->id;
    }

    public function getModelName($modelFile){
        $model = Yii::$app->getModule('rbac')->modelNamespace.'\\'.substr($modelFile,0,-4);
        $modelName = $model::tableName();
        if ($modelName && Yii::$app->db->schema->getTableSchema($modelName)) {
            return $modelName;
        }
        else return false;
    }
    
    public function saveFieldsToDb($modelFile, $modelId){
        $fileInfo = pathinfo($modelFile);
        $file = $fileInfo['filename'];
        $modelPath = Yii::$app->getModule('rbac')->modelNamespace . '\\' . $file;
        $result = [];
        $tableSchema = Yii::$app->db->schema->getTableSchema($modelPath::tableName());
        if($tableSchema){
            $model = new \ReflectionMethod($modelPath,'getAttributes');
            $fields = $model->invoke(new $modelPath());
            foreach ($fields as $id => $value) {
                $result[] = $id;
            }
        }
        foreach ($result as $item) {
            $this->saveFieldToDb($item, $modelId);
        }
    }

    public function saveFieldToDb($fieldName, $modelId){
        if(Field::find()->where(['model_id' => $modelId, 'name' =>$fieldName])->one()){
            return false;
        }
        $field = new Field();
        $field->name = $fieldName;
        $field->model_id = $modelId;
        $field->save();
    }

}