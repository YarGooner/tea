<?php

namespace admin\controllers;

use common\models\Settings;
use yii\data\ActiveDataProvider;
use yii2mod\editable\EditableAction;

class SettingsController extends AdminController
{

    public function actionIndex(){
        $dataProvider = new ActiveDataProvider([
            'query' => Settings::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actions()
    {
        return [
            'change-value' => [
                'class' => EditableAction::class,
                'modelClass' => Settings::class,
            ],
            'change-description' => [
                'class' => EditableAction::class,
                'modelClass' => Settings::class,
            ],
        ];
    }
}
