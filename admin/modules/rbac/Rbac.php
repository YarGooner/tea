<?php

namespace admin\modules\rbac;

/**
 * rbac module definition class
 */
class Rbac extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */

    /**
     * namespace of this module's controllers
     * @var string
     */
    public $controllerNamespace = 'admin\modules\rbac\controllers';

    /**
     * @var string
     * model of user
     */
    public $userModel = '';

    /**
     * actions, that this module won't block
     * Every element should be an array with name of controller as key and list of action's name's as value
     * ['post' => ['create','delete']]
     * @var array
     */
    public $unauthorizedActions = [];

    /**
     * path to root from controller's folder
     * @var string
     */
    public $rootPath = '/../../../../';

    /**
     * path from root to folder with controller of user's module
     * @var string
     */
    public $userControllerFolder = 'api\modules\v1\controllers';

    /**
     * namespace of user's module's controller
     * @var string
     */
    public $userControllerNamespace = 'api\modules\v1\controllers';

    /**
     * path from root to user's module's models
     * @var string
     */
    public $modelFolder = 'api\modules\v1\models';

    /**
     * namespace of user's module's models
     * @var string
     */
    public $modelNamespace = 'api\modules\v1\models';

    /**
     * user's module's id
     * @var string
     */
    public $moduleId = 'api';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
