<?php

namespace admin\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "role_model_permission".
 *
 * @property int $id
 * @property int $role_id
 * @property int $field_id
 * @property int $type
 *
 * @property Field $field
 * @property Role $role
 */
class RoleModelPermission extends \admin\modules\rbac\models\RbacModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_model_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'field_id', 'type'], 'required'],
            [['role_id', 'field_id', 'type'], 'integer'],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
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
            'field_id' => Yii::t('app', 'Field ID'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
}
