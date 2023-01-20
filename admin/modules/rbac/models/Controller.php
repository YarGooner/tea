<?php

namespace admin\modules\rbac\models;

use admin\modules\rbac\Rbac;
use Yii;

/**
 * This is the model class for table "controller".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @property Action[] $actions
 */
class Controller extends RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'controller';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActions()
    {
        return $this->hasMany(Action::className(), ['controller_id' => 'id']);
    }
}
