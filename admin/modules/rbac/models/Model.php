<?php

namespace admin\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "model".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @property Field[] $fields
 */
class Model extends \admin\modules\rbac\models\RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'model';
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
    public function getFields()
    {
        return $this->hasMany(Field::className(), ['model_id' => 'id']);
    }
}
