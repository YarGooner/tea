<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.09.2018
 * Time: 17:17
 */

namespace admin\controllers;


use yii\web\Controller;
use api\modules\v1\models\User;
use Yii;
use yii\filters\AccessControl;

class AdminController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'controllers' => ['*'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    public function getImgPath()
    {
        $imgPath = Yii::$app->getHomeUrl();
        $pos = stripos($imgPath, '/admin');
        $imgPath = substr($imgPath,0,$pos);
        $imgPath .= Yii::getAlias('@images').'/';
        return $imgPath;
    }

    public function imgNameCheck($image, $attributes = []) {
        if ($image->load(Yii::$app->request->post())) {
            foreach ($attributes as $attribute) {
                $pos = strrchr($image->$attribute, '/');
                if($pos) {
                    $image->$attribute = substr($pos, 1, strlen($pos) - 1);
                }
            }
            if($image->save()){
                return $this->redirect(['view', 'id' => $image->id]);
            }
        }
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('site/login');
        }
        if (!parent::beforeAction($action)) {
            return false;
        }
        return true;
    }
}