<?php

namespace admin\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "field".
 *
 * @property int $id
 * @property int $model_id
 * @property string $name
 * @property string $description
 *
 * @property Model $model
 * @property RoleModelPermission[] $roleModelPermissions
 */
class Field extends \admin\modules\rbac\models\RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id', 'name'], 'required'],
            [['model_id'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => Model::className(), 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model_id' => Yii::t('app', 'Model ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(Model::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleModelPermissions()
    {
        return $this->hasMany(RoleModelPermission::className(), ['field_id' => 'id']);
    }
}
