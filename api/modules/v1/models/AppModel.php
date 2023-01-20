<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 24.09.2018
 * Time: 14:52
 */

namespace api\modules\v1\models;

use yii\db\ActiveRecord;
use Yii;
use admin\modules\rbac\behaviors\RbacFieldBehavior;

class AppModel extends ActiveRecord
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return array_merge($behaviors,[
            [
                'class' => RbacFieldBehavior::className(),
                'tableName' => $this->tableName(),
            ]
        ]);
    }

    public function rules()
    {
        return $this->getWritableFields();
    }

    public function fields()
    {
        return $this->getReadableFields();
    }
}