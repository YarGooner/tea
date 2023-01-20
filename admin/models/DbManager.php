<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 13.12.2018
 * Time: 10:13
 */

namespace admin\models;

use yii\base\Model;
use Yii;

class DbManager extends Model
{
    public $action;

    public $path;

    public $fields;

    public function rules()
    {
        return [
            [['action'], 'required'],
            [['path'], 'string'],
            [['fields'], 'safe'],
//            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'action' => Yii::t('app', 'Действие'),
            'path' => Yii::t('app', 'Путь'),
            'fields' => Yii::t('app', 'Поля'),
        ];
    }

}