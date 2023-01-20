<?php

namespace admin\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "role_permission".
 *
 * @property int $id
 * @property int $role_id
 * @property int $action_id
 *
 * @property Action $action
 * @property Role $role
 */
class RolePermission extends RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id'], 'required'],
            [['role_id', 'action_id'], 'integer'],
            [['action_id'], 'exist', 'skipOnError' => true, 'targetClass' => Action::className(), 'targetAttribute' => ['action_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'action_id' => Yii::t('app', 'Action ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(Action::className(), ['id' => 'action_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
}
