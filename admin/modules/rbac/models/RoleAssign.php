<?php

namespace admin\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "role_assign".
 *
 * @property int $id
 * @property int $role_id
 * @property int $user_id
 *
 * @property Role $role
 * @property User $user
 */
class RoleAssign extends RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_assign';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $userModel = Yii::$app->getModule('rbac')->userModel;

        return [
            [['role_id', 'user_id'], 'integer'],
            [['user_id'], 'required'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => $userModel, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
