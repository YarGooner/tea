<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.09.2018
 * Time: 17:18
 */

namespace admin\models;


use yii\db\ActiveRecord;

class AdminModel extends ActiveRecord
{
    public function afterFind()
    {
        parent::afterFind();
        $actionId = \Yii::$app->controller->action->id;
        if($actionId == 'index' || $actionId == 'view') {
            foreach ($this->attributes as $key => $attribute) {
                $this->$key = $this->prepareText($this->$key);
            }
        }
    }

    function prepareText($string, $up_first_string = false, $html = false)
    {
        $string=preg_replace("/[\r\n]+/", "\n", $string);
        $string=preg_replace("/[ \t]+/", " ", $string);

        if ($html == false) {
            $string = strip_tags($string);
            $string = htmlspecialchars($string);
        } else {
            $string=str_replace( "\n", '<br />', $string);
            $string = \yii\helpers\HtmlPurifier::process($string, [
                'HTML.Allowed' => 'p,br',
            ]);
        }
        if ($up_first_string) {
            $string = ucfirst($string);
        }
        return $string;
    }
}