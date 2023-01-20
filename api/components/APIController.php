<?php
namespace api\modules\v1\components;

use api\modules\v1\models\APIUser;
use common\components\recaptcha\Recaptcha;
use common\components\Utils;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\filters\Cors;
//use yii\rest\ActiveController;
use yii\rest\Controller;
//use yii\web\Response;

/**
 * API Base Controller
 * All controllers within API app must extend this controller!
 */
class APIController extends Controller
{

    const USER_NOT_FOUND = 'user_not_found';
    const USER_NOT_ACTIVE = 'user_not_active';

    const AUTHORIZED_ONLY = 'authorized_only';
    const DEV_ONLY = 'dev_only';
    const EMAIL_CONFIRM_REQUIRED = 'email_confirm_required';

    public $REQUEST;
    public $POST;
    public $GET;
    public $HAS_DEV_TOKEN;

    const MODULE_PREFIX = "/v1/site/";

    // try for Corrs
//    public $enableCsrfValidation = false;

//    public static function allowedDomains() {
//        return [
//             '*',                        // star allows all domains
////            'http://alfaphobia.loc',
////            'http://test2.example.com',
//        ];
//    }
// try for Corrs

/*
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        if( Yii::$app->params['api.authWithToken'] ) {
            // add CORS filter
            $behaviors['corsFilter'] = [
                'class' => Cors::className(),
//                'cors'  => [
//                    // restrict access to domains:
//                    'Origin'                           => static::allowedDomains(),
//                    'Access-Control-Request-Method'    => ['POST'],
//                    'Access-Control-Allow-Credentials' => true,
//                    'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
//                ],
            ];

            // add QueryParamAuth for authentication
            $behaviors['authenticator'] = [
                'class' => QueryParamAuth::className(),
            ];

            // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
            $behaviors['authenticator']['except'] = ['options'];
        }

        return $behaviors;
    }
*/


    public function beforeAction($action)
    {
        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl

        if (!parent::beforeAction($action)) {
            return false;
        }

        $locals = $this->getLocals();

        // Output format
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Shortcuts
        $this->REQUEST = \Yii::$app->request;
        $this->POST = $this->REQUEST->post();
        $this->GET = $this->REQUEST->get();

        // GET DEV TOKEN
        $dev_token = isset($this->POST['dev_token']) ? $this->POST['dev_token'] : null;
        $this->HAS_DEV_TOKEN = $dev_token && $dev_token === Yii::$app->params['api.info.dev_token'];

        // IF DEV TOKEN REQUIRED !

        if( !$this->HAS_DEV_TOKEN && $this->getActionInfoParam(self::DEV_ONLY ) ){
//            $locals = $this->getLocals();
            if( Yii::$app->params['api.devTokenWarn'] )
                Yii::$app->response->data = $this->returnErrorDevTokenRequired();
            else
                Yii::$app->response->data = $this->returnErrorBadRequest();
            return false;
        }

        // Check USER AUTH

        $user = $this->getUser();

        // ACCESS TOKEN
        $access_token = isset( $this->POST['access_token'] ) ? $this->POST['access_token'] : null;
//        $dev_access_token_expire = $this->POST['dev_access_token_expire'];

//        $user = User::findIdentityByAccessToken($access_token);
//        return $this->returnErrors(["access_token" => $user]);
//        $this->addError( 'action', Yii::$app->controller->action->id );
//        $this->addError( 'method data', $authorized_only );

        // if user is not authorized and access_token is passed
//        if ( !$user && $access_token ) $user = Yii::$app->user->loginByAccessToken($access_token );
        if( !$user && $access_token ) $user = APIUser::findIdentityByAccessToken( $access_token );

        // if authorization required
        if( $this->getActionInfoParam(self::AUTHORIZED_ONLY ) ) {

            if( !$user  ) { // and user is not authorized with access_token - throw an error
                Yii::$app->response->data = $this->returnErrorUserIsNotLoggedIn();
                return false;
            }

            // if access_tokon is expired - throw error
            $access_token_time = APIUser::checkUserAccessToken( $user );
//            $this->addError( 'time', $access_token_time );
//            $this->addError( 'user', $user);
            if( $access_token_time <= 0 ) {
                Yii::$app->response->data = $this->returnErrors(['access_token:expired' => $locals['access_token:expired']]); // access_token is expired
                return false;
            }

            // else logging user in
            if( $access_token ) Yii::$app->user->login( $user );

        }

        // EMAIL CONFIRM REQUIRED
        if( $this->getActionInfoParam( self::EMAIL_CONFIRM_REQUIRED ) && (!$user || !$user->email_confirmed) ){
            Yii::$app->response->data = $this->returnErrorEmailConfirmRequired();
            return false;
        }

        return true; // or false to not run the action
    }

    // >>>>>>>>>>>>>>>>>>   SUPPORT   >>>>>>>>>>>>>>>>>>

    //
    public function getTableColumnNames( $table_schema = null ){
        $class = $this->tablePath;
        if( $table_schema == null ) $table_schema = $class::getTableSchema();
        $columns = $table_schema->columns;
        if( !$columns ) return null;
        $column_names = [];
        foreach( $columns as $key => $column ){
            array_push( $column_names, $key );
        };
        return $column_names;
    }


    //
    public function getActionInfoParam($param_name ){
		$method_info = $this->getMethodsInfo();
		$action_id = Yii::$app->controller->action->id;
		$method_info_data = isset( $method_info[$action_id] ) ? $method_info[$action_id] : null;
		if( $method_info_data == null ) return null;
        $param = isset( $method_info_data[ $param_name ] ) ? $method_info_data[ $param_name ] : null;
        if( $param_name == "index" && !$param ){
            $param = $this->getMethodsInfo()[Yii::$app->controller->action->id][ "" ];
        }
        return $param;
    }


    //
    public function getUser(){

        $user = Yii::$app->user->identity;
        if( $user ) return $user;
        return null;

    }


    //
    public function isUserActive( $user ){

        $locals = $this->getLocals();

        if( !$user ){
//             $this->returnErrors(['not_found:user' => "user with email: " . $post['email'] . " is not found"]);
            $this->returnErrorUserNotFound();
            return false;
        }

        if( $user->status != APIUser::STATUS_ACTIVE ){
            $this->addErrors([ self::USER_NOT_ACTIVE => $locals["user:not_active"] ]);
            return false;
        }

        return true;
    }


    //
    public function getLocalText( $id, $return_array = false ){
//        $app_params = Yii::$app->params;
        $locals = $this->getLocals();
        $text = $locals[ $id ];
        if( $text ) return $return_array === true ? [ $id => $text ] : $text;
//        return [ $id => $text ];
        return null;
    }


    //
    public function getLocals(){
        return Yii::$app->params['locals'];
    }

    //
    public function addFilePrefixes($data, $fields, $prefix = null )
    {
        if( !$prefix ) $prefix = Utils::getUploadFolderUrl();

        function _traverse( $data, $prefix, $fields ){
            foreach($data as $i => $item) {
                if( is_array( $item ) ) {
                    $data[$i] = _traverse( $item, $prefix, $fields );
                }else if( $item ){
                    foreach ( $fields as $field ) {
                        if( $i == $field ) $data[$i] = $prefix .'/'. ltrim( $item, '/');
                    }
                }
            }
            return $data;
        }

        return _traverse( $data, $prefix, $fields );

    }

    //
    public function getTablePath(){
        $name = str_replace( '-', '', ucwords ($this->controllerName, '-' ) );
//        $name = ucwords ($this->controllerName, '-' );

        return "backend\models\\" . $name;
//        return "backend\models\Collections";
    }

    //
    public function getControllerName(){
        return Yii::$app->controller->id;
    }

    //
    public function getAllowedAnswer(){
        return [];
    }

    public function filterAnswer( $answer, $allowed = null ){
//        $my_array = ['foo' => 1, 'hello' => 'world'];
//        $allowed  = ['foo', 'bar'];
        if( $allowed == null ) $allowed = $this->getAllowedAnswer();

        return array_filter(
            $answer,
            function ($key) use ($allowed) {
                return in_array($key, $allowed);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    // <<<<<<<<<<<<<<<<<<   SUPPORT   <<<<<<<<<<<<<<<<<<

}