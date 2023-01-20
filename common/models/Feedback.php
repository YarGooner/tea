<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "feedback".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $message
 * @property string $created_at
 * @property string $updated_at
 * @property int $moderation_status
 * @property string $comment
 *
 */
class Feedback extends \yii\db\ActiveRecord
{
    public const MODERATION_NEW = 0;
    public const MODERATION_ACCEPTED = 10;
    public const MODERATION_REJECTED = 20;

    public $name;
    public $email;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            ['moderation_status', 'in', 'range' => [self::MODERATION_NEW, self::MODERATION_ACCEPTED, self::MODERATION_REJECTED]],
            [['name', 'email', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'Email',
            'message' => 'Сообщение',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'moderation_status' => 'Статус модерации',
            'comment' => 'Комментарий',
        ];
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}
