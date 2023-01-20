<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property int $output_priority
 * @property string $date
 * @property string $description
 * @property string $text
 * @property string $image
 * @property int $status
 */
class News extends \yii\db\ActiveRecord
{
    public const STATUS_PUBLISHED_NO = 0;
    public const STATUS_PUBLISHED_YES = 1;

    public $image;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'date'], 'required'],
            [['output_priority'], 'integer'],
            [['status'], 'boolean'],
            [['date'], 'safe'],
            [['description', 'text'], 'string'],
            [['title'], 'string', 'max' => 255],
            ['status', 'in', 'range' => [self::STATUS_PUBLISHED_NO, self::STATUS_PUBLISHED_YES]],
           // [['image'], 'img'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'output_priority' => 'Приоритет для вывода в API',
            'date' => 'Дата публикации',
            'description' => 'Описание',
            'text' => 'Текст новости',
            'image' => 'Изображение',
            'status' => 'Статус публикации',
        ];
    }
}
