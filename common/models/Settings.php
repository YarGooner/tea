<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "email_settings".
 *
 * @property int $id
 * @property string $parameter Название параметра
 * @property string $value Значение
 * @property string $description Описание параметра
 */
class Settings extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parameter', 'value', 'description'], 'required'],
            [['parameter', 'value', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parameter' => Yii::t('app', 'Parameter'),
            'value' => Yii::t('app', 'Value'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
}
