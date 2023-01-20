<?php

namespace admin\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "action".
 *
 * @property int $id
 * @property int $controller_id
 * @property string $name
 * @property string $description
 *
 * @property Controller $controller
 * @property RolePermission[] $rolePermissions
 */
class Action extends RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['controller_id', 'name'], 'required'],
            [['controller_id'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['controller_id'], 'exist', 'skipOnError' => true, 'targetClass' => Controller::className(), 'targetAttribute' => ['controller_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'controller_id' => Yii::t('app', 'Controller ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getController()
    {
        return $this->hasOne(Controller::className(), ['id' => 'controller_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolePermissions()
    {
        return $this->hasMany(RolePermission::className(), ['action_id' => 'id']);
    }
}
