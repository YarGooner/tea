<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tea".
 *
 * @property int $id
 * @property string $title
 * @property int $collection_id
 * @property string $subtitle
 * @property string $description
 * @property string $image_fon
 * @property string $image_pack
 * @property string $weight
 * @property string $temperature_brewing
 * @property string $time_brewing
 * @property int $buy_button_flag
 * @property string $url
 * @property int $output_priority
 *
 * @property Collection $collection
 */
class Tea extends \yii\db\ActiveRecord
{
    public $image_fon;
    public $image_pack;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tea';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'collection_id'], 'required'],
            [['collection_id', 'buy_button_flag', 'output_priority'], 'integer'],
            [['subtitle', 'description', 'weight', 'temperature_brewing', 'time_brewing', 'url'], 'string'],
            [['title'], 'string', 'max' => 255],
            //[['image_fon', 'image_pack'], 'img'],
            [['collection_id'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['collection_id' => 'id']],
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
            'collection_id' => 'ID Коллекции',
            'subtitle' => 'Подзаголовок',
            'description' => 'Описание',
            'image_fon' => 'Фоновое изображение',
            'image_pack' => 'Изображение пачки',
            'weight' => 'Вес',
            'temperature_brewing' => 'Температура заваривания',
            'time_brewing' => 'Время заваривания',
            'buy_button_flag' => 'Доступность покупки',
            'url' => 'Ссылка для покупки',
            'output_priority' => 'Приоритет для вывода в API',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCollection()
    {
        return $this->hasOne(Collection::className(), ['id' => 'collection_id']);
    }
}
