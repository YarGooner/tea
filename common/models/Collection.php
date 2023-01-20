<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "collection".
 *
 * @property int $id
 * @property string $title
 * @property string $subtitle
 * @property string $hovercolor
 * @property string $image
 *
 * @property Tea[] $teas
 */
class Collection extends \yii\db\ActiveRecord
{
    public $image;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'collection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'subtitle', 'hovercolor'], 'string', 'max' => 255],
            //[['image'], 'file'],
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
            'subtitle' => 'Подзаголовок',
            'hovercolor' => 'Цвет ховера',
            'image' => 'Фоновое изображение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeas()
    {
        return $this->hasMany(Tea::className(), ['collection_id' => 'id']);
    }
}
