<?php

namespace api\modules\v1\controllers;

use common\models\Collection;
use common\models\Feedback;
use common\models\News;
use common\models\Tea;
use Yii;
use yii\filters\auth\HttpBearerAuth;


class TestController extends AppController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authentificator' => [
                'class' => HttpBearerAuth::className(),
                'except' => ['collection', 'tea', 'news', 'feedback']
            ],
        ]);
    }

    public function actionCollection()
    {
        $collection = Collection::find()->all();
        return $this->returnSuccess([
            'collections' => $collection
        ]);
    }

    public function actionTea(int $collection_id)
    {
        $tea = Tea::findall(['collection_id' => $collection_id]);
        return $this->returnSuccess(['tea' => $tea]);
    }

    public function actionNews()
    {
        $news = News::find()->where(['status' => 1])->orderBy(['output_priority' => SORT_DESC])->all();
        return $this->returnSuccess(['news' => $news]);
    }

    public function actionFeedback()
    {

        $model = new Feedback();

        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            return $this->returnSuccess('Успех');
        }

        return $this->returnError($model->errors);
    }
}