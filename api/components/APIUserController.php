<?php
namespace api\modules\v1\components;

use api\modules\v1\models\APIUser;
use backend\models\Auth;
use backend\models\Params;
use backend\models\SiteUser;
use common\components\DateHelper;
use http\Url;
use Yii;
//use api\modules\v1\models\APIUser;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * User Controller
 */
class APIUserController extends APIController
{

    public $modelClass = 'api\modules\v1\models\APIUser';

    // keys for user attributes filtration
    public $allowed_user_attributes = [
        'id',
        'username',
        'first_name',
        'last_name',
        'email',
        'status',
        'email_confirmed',
        'soc_id',
        'soc_user_id',
        'soc_user_image',
        'gender',

//        'personal_data_agreement',
    ];

    public $allowed_to_change_user_attributes = [
        'username' => 'unique',
        'email' => 'unique',
        'first_name' => '',
        'last_name' => '',
        'password' => 'password',
    ];

    public function getUserRegValidationRules()
    {

        $app_params = Yii::$app->params;
        $locals = $this->getLocals();

        return [
            [['username', 'email', 'first_name', 'last_name'], 'string',
                'min' => 3, 'max' => 50, 'tooLong' => $locals['input_string:too_long'], 'tooShort' => $locals['input_string:too_short']],
            ['email', 'email', 'message' => $locals['input_email:wrong']],
            [['password'], 'string',
                'min' => $app_params['api.passwordMinLength'], 'max' => $app_params['api.passwordMaxLength'], 'tooLong' => $locals['input_string:too_long'], 'tooShort' => $locals['input_string:too_short']],
        ];
    }

    public function actions()
    {
        $actions = parent::actions(); //

        // disable the "delete" and "create" actions
        unset(
            $actions['delete'],
            $actions['create']
        );

        return $actions;
    }


    public function behaviors()
    {
        $behaviors = parent::behaviors();

//        if( Yii::$app->params['api.authWithToken'] ) {
//
//            $behaviors['authenticator']['except'] = array_merge($behaviors['authenticator']['except'], [
//                'reg',
//                'confirm-email-send',
//                'confirm-email',
//                'login',
//                'auth',
//                'edit',
//                'logout',
//                'recover-password',
//                'reset-password',
//            ]);
//        }

        return $behaviors;
    }





//
//    // >>>>>>>>>>>>>>>>>>   INDEX   >>>>>>>>>>>>>>>>>>
//    public function actionIndex(){
////        return null;
//        return $this->returnErrorBadRequest();
//    }
//    // <<<<<<<<<<<<<<<<<<   INDEX   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>> DEV-RESET >>>>>>>>>>>>
    public function actionDevReset()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
        //
        $test_username = $app_params['api.tester.username'];
        $test_user = APIUser::findByUsername($test_username);
        if ($test_user) $test_user->delete();

        $test_username_new = $app_params['api.tester.username_new'];
        $test_user_new = APIUser::findByUsername($test_username_new);
        if ($test_user_new) $test_user_new->delete();
//        return $this->returnErrors();

        return $this->returnSuccess();
    }
    // <<<<<<<<<<<< DEV-RESET <<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   REG   >>>>>>>>>>>>>>>>>>
    public function actionReg()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
        //

        $model = DynamicModel::validateData(
            array_merge(
                $this->_getUserSafePostAttributes($post),
                [
                    'personal_data_agreement' => $post['personal_data_agreement'],
                ]
            ), array_merge($this->getUserRegValidationRules(), [
                    [['username'], 'required', 'message' => $locals['input_empty:username']],
                    [['email'], 'required', 'message' => $locals['input_empty:email']],
                    [['password'], 'required', 'message' => $locals['input_empty:password']],
                    [['first_name'], 'required', 'message' => $locals['input_empty:first_name']],
                    [['last_name'], 'required', 'message' => $locals['input_empty:last_name']],
//                ['personal_data_agreement', 'required', 'message' => $locals['input_empty:personal_data_agreement']],
//                ['personal_data_agreement', 'boolean' ],
                ]
            )
        );

        if ($model->hasErrors()) return $this->returnErrors($model->errors);

        // is the name unique
        if (APIUser::findOne(["username" => $model->username])) {
            return $this->returnErrors(['username' => $locals['username:used']]);
        }

        // is personal data agreement checked
        if ($app_params['api.personalDataAgreementRequired'] && !$model->personal_data_agreement) {
            return $this->returnErrors(['personal_data_agreement' => $locals['input:personal_data_agreement']]);
        }

        // is the email unique
        if (APIUser::findOne(["email" => $model->email])) {
            return $this->returnErrors(['email' => $locals['email:used']]);
        }

        // Create new User
        $user = new APIUser();
        $user->username = $model->username;
        $user->email = $model->email;
        $user->first_name = $model->first_name;
        $user->last_name = $model->last_name;
        $user->personal_data_agreement = $model->personal_data_agreement;
        $user->is_tester = $post['is_tester'] ? 1 : 0;
        $user->setPassword($model->password);
        $user->generateAuthKey();

        return $this->completeUserDataChange($user, true);

    }
    // <<<<<<<<<<<<<<<<<<   REG   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   EDIT   >>>>>>>>>>>>>>>>>>
    public function actionEdit()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
        //

        // USER
        if (!($user = $this->getUser())) return $this->returnErrors();

        $model = DynamicModel::validateData($this->_getUserSafePostAttributes($post),
            $this->getUserRegValidationRules());

        if ($model->hasErrors()) return $this->returnErrors($model->errors);


        // check fields
        $this->_checkEditedFields($user, $model, $locals, $this->allowed_to_change_user_attributes);
        if (count($this->errors)) return $this->returnErrors();

//        return $user;

        // apply changes
        if (!$this->_applyEditedFields($user, $model)) {
            return $this->returnErrors(['message' => $locals['request.no_changes']]);
        }

        return $this->completeUserDataChange($user, false);

    }


    // -------------------------
    private function _getUserSafePostAttributes($post)
    {
        $attrs = [];
        foreach ($this->allowed_to_change_user_attributes as $key => $value) {
            $attrs[$key] = $post[$key];
        }
        return $attrs;
    }


    // -------------------------
    private function _checkEditedFields($user, $model, $locals, $fields)
    {
//        $response = [];
        foreach ($fields as $key => $type) {

            // is unique
            if ($type == 'unique' && APIUser::find()->where([$key => $model->$key])->andWhere(['!=', 'id', $user->id])->one()) {
                $this->addErrors(['username' => $locals['username:used']]);
            }

            // is not changed
            if ($type == 'password') {
                if ($model->$key && Yii::$app->getSecurity()->validatePassword($model->$key, $user['password_hash'])) {
                    $model->$key = '';
                }
            } else if ($user->$key == $model->$key) {
//                $this->addErrors([$key => $locals['input_not_changed'] ]);
                $model->$key = '';
            }
//            array_push( $response, $key ." : ". $field );
        }
//        return $response;
    }


    // -------------------------
    private function _applyEditedFields($user, $model)
    {

        $needsUpdate = false;

        foreach ($model->attributes as $key => $value) {

            if (!$value) continue;

            $needsUpdate = true;

            if ($key == 'password') {
                $user->password_reset_token = null;
                $user->setPassword($value);
                $user->generateAuthKey();
            } else {
                $user[$key] = $value;
            }
        }
        return $needsUpdate;
    }


    // -------------------------
    private function completeUserDataChange($user, $is_reg)
    {

        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();

        // Response
        $response = [];

        $changed_attrs = $user->getDirtyAttributes();
//        $response['prev'] = $prev_attrs;

        // Setup email confirmation
        if ($changed_attrs['email'] && $app_params['api.emailConfirmRequired']) {
            $user->email_confirm_token = Yii::$app->security->generateRandomString() . '_' . time();
        }

        // Save
        try {
            $user->save(false);
        } catch (Exception $e) {
            return $this->returnErrors(['db_error' => $e->errorInfo]);
        }


        $user->refresh();

        array_merge($response, ['user' => $this->cleanupUserData($user->attributes)]);

        if ($this->HAS_DEV_TOKEN) // IF DEV TOKEN
            $response = array_merge($response, ["email_confirm_token" => $user->email_confirm_token]);

        // Send Mail
        if ($app_params['mail.sendOnRegister'] || $app_params['api.emailConfirmRequired']) {

            if ($is_reg) {

                $this->sendMail(
                    $user->email,
                    'Registration on ' . Yii::$app->name,
                    ['html' => 'userRegistered-API-html', 'text' => 'userRegistered-API-text'],
                    ['user' => $user]
                );

            } else {

                $this->sendMail(
                    $user->email,
                    'Profile data changed on ' . Yii::$app->name,
                    ['html' => 'userProfileChanged-API-html', 'text' => 'userProfileChanged-API-text'],
                    ['user' => $user]
                );

            }

            if ($changed_attrs['email'])
                $response = array_merge($response, ["message" => str_replace('{email}', $user->email, $locals['mail:confirm_email_sent'])]);

        }

        // Auth User // TODO: implement this
        return $this->returnSuccess($response);

    }
    // <<<<<<<<<<<<<<<<<<   EDIT   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   CONFIRM EMAIL SEND   >>>>>>>>>>>>>>>>>>
    public function actionConfirmEmailSend()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
        //

        // USER
        if (!($user = $this->getUser())) return $this->returnErrors();

        // is confirmation required
        if (!$app_params['api.emailConfirmRequired']) {
            return $this->returnErrors(['email_confirm_is_not_required' => $locals['email:confirm_not_required']]);
        }

        // is user already confirm his email
        if ($user->email_confirmed) {
            return $this->returnErrors(['email_is_confirmed' => $locals['email:is_confirmed_already']]);
        }

        // token send timeout
        $resend_timeout = $this->canSendToken($user->email_confirm_token);
        if ($resend_timeout > 0) {
            if ($this->HAS_DEV_TOKEN) $this->addError("timeout", $resend_timeout);
            return $this->returnErrors(['token_send_timeout' => $locals['token:send_timeout']]);
        }

        // Setup email confirmation
        $user->generateEmailConfirmToken();

        // Save
        try {
            $user->save(false);
        } catch (Exception $e) {
            return $this->returnErrors($this->getDBError($e->errorInfo));
        }

        $user->refresh();

        $response = [
            'token_send_timeout' => $app_params['api.tokenSendTimeout'],
        ];

        // Send Mail
        if ($app_params['mail.sendOnRegister'] || $app_params['api.emailConfirmRequired']) {

            if ($this->sendMail(
                $user->email,
                'Registration on ' . Yii::$app->name,
                ['html' => 'userRegistered-API-html', 'text' => 'userRegistered-API-text'],
                ['user' => $user]
            )
            ) {

                if ($this->HAS_DEV_TOKEN) // IF DEV TOKEN
                    $response = array_merge($response, ["email_confirm_token" => $user->email_confirm_token]);

                return $this->returnSuccess(array_merge($response, ['message' => str_replace('{email}', $user->email, $locals['mail:confirm_email_sent'])]));
            }

            return $this->returnErrors(['mail_send_error' => $locals['mail:send_error']]);
        }

        return $this->returnSuccess($response);

    }
    // <<<<<<<<<<<<<<<<<<   CONFIRM EMAIL SEND   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   CONFIRM EMAIL    >>>>>>>>>>>>>>>>>>
    public function actionConfirmEmail()
    {

        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();

        if (!$post['token']) $this->addError('token', $locals['input_empty:token']);

        if (count($this->errors)) return $this->returnErrors();

        $user = APIUser::findOne([
            "email_confirm_token" => $post['token'], // users with email confirm token
            'status' => APIUser::STATUS_ACTIVE, // active users only
            'email_confirmed' => false, // without confirmed email
        ]);

        if (!$user) {
            return $this->returnErrorUserNotFound();
        }

        if (!$this->isTokenNotExpired($post['token'])) {
            return $this->returnErrors(['token_expired' => $locals['token:expired']]);
        }

        $user->email_confirm_token = null;
        $user->email_confirmed = true;

        if (!$user->save()) {
            return $this->returnErrors($this->getDBError($e->errorInfo));
        }

        // send mail
//        $this->senMail(
//            $user->email,
//            'Password is resetted for ' . Yii::$app->name,
//            ['html' => 'passwordResetted-API-html', 'text' => 'passwordResetted-API-text'],
//            ['user' => $user]
//        );

//        return $this->returnSuccess(['user' => array($user->attributes)]);// !!! DEBUG ONLY
        return $this->returnSuccess();
    }
    // <<<<<<<<<<<<<<<<<<   CONFIRM EMAIL   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   LOGIN   >>>>>>>>>>>>>>>>>>
    public function actionLogin()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
        //

        $model = DynamicModel::validateData([
            'username' => $post['username'],
            'password' => $post['password'],
            'remember_me' => $post['remember_me'],
        ], [
            [['username'], 'required', 'message' => $locals['input_empty:username']],
            [['password'], 'required', 'message' => $locals['input_empty:password']],
            [['username', 'password'], 'string'],
            ['remember_me', 'boolean'],
        ]);

        if ($model->hasErrors()) return $this->returnErrors($model->errors);

        $user = APIUser::findByUsername($model->username);// only active useres will be found
//        $user = User::findOne(['username' => $model->username ]);

        if (!$user) {
//            return $this->returnErrors([ APIController::USER_NOT_FOUND => $locals['user:not_found'] ]);
            return $this->returnErrors(['wrong_login_or_password' => $locals['input:wrong_auth']]);
        }

        // Check password
        if (!Yii::$app->getSecurity()->validatePassword($model->password, $user['password_hash'])) {
//        if ( $user->validatePassword( $model['password'] ) ){
            return $this->returnErrors(['wrong_login_or_password' => $locals['input:wrong_auth']]);
        }

        // Check user status
//        if($user->status !== User::STATUS_ACTIVE){
//            return $this->returnErrors([ APIController::USER_NOT_ACTIVE => $locals['user:not_active'] ]);
//        }

        // Remember me
        $remember_me = $model->remember_me && $app_params['api.loginRememberMeOn'];

        return $this->_loginUser( $user, $remember_me );

    }

    public function _loginUser( $user, $remember_me = null ){

        $app_params = Yii::$app->params;

        $remember_me_duration = $app_params['api.loginRememberMeDuration'];
        Yii::$app->user->login($user, $remember_me ? $remember_me_duration : 0);



//        $user->generateAccessTokens(true);

        $response = ['user' => $this->getCurrentUserData()];

//        $response = array_merge($response, [
//            'access_token' => $user->access_token,
//            'renew_access_token' => $user->renew_access_token,
//        ]);

//        if ($this->HAS_DEV_TOKEN) $response = array_merge($response, [
//            'remember_me' => $remember_me,
//            'remember_me_duration' => $remember_me_duration,
////            'auth_key' => $user->auth_key
////            'auth_key' => $cookies->getValue('_auth_key'),
////            'auth_key' => Yii::$app->session->get("auth_key"),
//        ]);

        return $this->returnSuccess($response);
//        return Yii::$app->user->identity;
    }
    // <<<<<<<<<<<<<<<<<<   LOGIN   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   AUTH   >>>>>>>>>>>>>>>>>>
    public function actionAuth()
    {
//        $post = $this->POST;
//        $app_params = Yii::$app->params;
//        $post = $this->POST;
//        $locals = $this->getLocals();
//        $auth_key = Yii::$app->session->get("auth_key" );
//        $cookies = Yii::$app->request->cookies;
//        $auth_key = $cookies->getValue('_auth_key');

        if (!($user = $this->getUser())) return $this->returnErrors();
        $user_data = $this->getCurrentUserData();
//        toLog('api', 'USER: ' . implode( '; ', $user_data ) );
        return $this->returnSuccess(['user' => $user_data ]);
    }
    // <<<<<<<<<<<<<<<<<<   AUTH   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   RENEW ACCESS TOKEN   >>>>>>>>>>>>>>>>>>
    public function actionRenewAccessToken()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();

        $user = APIUser::findByRenewAccessToken($post['renew_access_token']);
        if (!$user) {
            return $this->returnErrorUserNotFound();
        }

        $user->generateAccessTokens(true);

        return $this->returnSuccess([
            "access_token" => $user->access_token,
            "renew_access_token" => $user->renew_access_token,
        ]);

    }
    // <<<<<<<<<<<<<<<<<<   RENEW ACCESS TOKEN   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   LOGOUT   >>>>>>>>>>>>>>>>>>
    public function actionLogout()
    {
//        $app_params = Yii::$app->params;
//        $post = $this->POST;
//        $locals = $this->getLocals();

        if (!($user = $this->getUser())) return $this->returnErrors();

        Yii::$app->user->logout();
        $user->removeAccessTokens();
        return $this->returnSuccess();
    }
    // <<<<<<<<<<<<<<<<<<   LOGOUT   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   RECOVER PASSWORD   >>>>>>>>>>>>>>>>>>
    public function actionRecoverPassword()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
        //

        $model = DynamicModel::validateData([
            'email' => $post['email'],
        ], [
            [['email'], 'required', 'message' => $locals['input_empty:email']],
            ['email', 'email'],
        ]);

        if ($model->hasErrors()) return $this->returnErrors($model->errors);

        $user = APIUser::findOne([
//            'email' => $post['email']
            'email' => $model->email,
        ]);

        if (!$this->isUserActive($user)) return $this->returnErrors();

        // token send timeout
        if ($this->canSendToken($user->password_reset_token) > 0) {
            return $this->returnErrors(['token_send_timeout' => $locals['token:send_timeout']]);
        }

        // reset token

//        if ( !User::isPasswordResetTokenValid($user->password_reset_token)) {
        $user->generatePasswordResetToken();
        if (!$user->save()) {
            return $this->returnErrors($this->getDBError($e->errorInfo));
        }
//        }

        $user->refresh();

        // send mail
        if ($this->sendMail(
            $model->email,
            'Password reset for ' . Yii::$app->name,
            ['html' => 'passwordResetToken-API-html', 'text' => 'passwordResetToken-API-text'],
            ['user' => $user]
        )
        ) {
//            return $this->returnSuccess(["user" => array($user->attributes)]); // !!! DEBUG ONLY
            $response = ["message" => str_replace('{email}', $user->email, $locals['mail:confirm_email_sent'])];

            if ($this->HAS_DEV_TOKEN) // IF DEV TOKEN
                $response = array_merge($response, ["password_confirm_token" => $user->password_reset_token]);

            return $this->returnSuccess($response);
        }

        return $this->returnErrors(["sendmail:error" => 'mailer error']);

    }
    // <<<<<<<<<<<<<<<<<<   RECOVER PASSWORD   <<<<<<<<<<<<<<<<<<


    // >>>>>>>>>>>>>>>>>>   PASSWORD RESET   >>>>>>>>>>>>>>>>>>
    public function actionResetPassword()
    {
        $app_params = Yii::$app->params;
        $post = $this->POST;
        $locals = $this->getLocals();
//
//        if( !$post['password'] ) $this->addError( 'required:password', 'password is required' );
//        if( !$post['token'] ) $this->addError( 'required:token', 'token is required' );
//
//        if( count($this->errors) ) return $this->returnErrors();


        $model = DynamicModel::validateData([
            'token' => $post['token'],
            'password' => $post['password'],
        ], [
            [['token'], 'required', 'message' => $locals['input_empty:token']],
            [['password'], 'required', 'message' => $locals['input_empty:password']],
            [['token', 'password'], 'string'],
        ]);

        if ($model->hasErrors()) return $this->returnErrors($model->errors);

        $user = APIUser::findOne([
            "password_reset_token" => $model->token,
        ]);

        if (!$this->isUserActive($user)) return $this->returnErrors();

        if (!$this->isTokenNotExpired($model->token)) {
            return $this->returnErrors(['token_expired' => $locals['token:expired']]);
        }

        $user->password_reset_token = null;
        $user->setPassword($model->password);
        $user->generateAuthKey();
        if (!$user->save()) {
            return $this->returnErrors($this->getDBError($e->errorInfo));
        }

        $response = [];

        // send mail
        if (
        $this->sendMail(
            $user->email,
            'Password is resetted for ' . Yii::$app->name,
            ['html' => 'passwordResetted-API-html', 'text' => 'passwordResetted-API-text'],
            ['user' => $user]
        )
        ) {
//            $response = array_merge( $response, [ 'message' => $locals[''] ] );
            $response = ['message' => $locals['mail:password_changed_sent']];
        }

//        return $this->returnSuccess(['user' => array($user->attributes)]);// !!! DEBUG ONLY
        return $this->returnSuccess($response);

    }
    // <<<<<<<<<<<<<<<<<<   PASSWORD RESET   <<<<<<<<<<<<<<<<<<





    // >>>>>>>>>>>>>>>>>>   VK   >>>>>>>>>>>>>>>>>>
//    public function actionVk()
//    {
//
//        $app_params = Yii::$app->params;
//        $locals = $this->getLocals();
//
//        $soc_name = 'vk';
//        $redirect_url = \yii\helpers\Url::toRoute('', true );
//        $code = ArrayHelper::getValue( $this->GET, 'code');
//        $scope = '&scope=friends';
//        $fields = 'uid,first_name,last_name,photo_big,sex,about,bdate';
//        $ver = 5.85;
//        $app_id = $app_params[$soc_name.'.app_id'];
//        // Show Login Form
//        if( $code == null ){
//            $this->redirect( 'https://oauth.vk.com/authorize?client_id=6738713&display=popup&redirect_uri='.$redirect_url.'&response_type=code&v='.$ver.$scope );
//            return;
//        }
//
//        // Auth User
//        $client = new Client();
//
//        $auth_response = $client->createRequest()
//            ->setMethod('GET')
//            ->setUrl('https://oauth.vk.com/access_token')
//            ->setData([
////                'client_id' => $app_id,
////                'client_secret' => $app_params[$soc_name.'.app_secret'],
//                //TODO: вынести хранение кодов в отдельный файл
//                'client_id' => 6738713,
//                'client_secret' => 'uNvNHzm23bZ4PYGWkkB8',
//                'redirect_uri' => $redirect_url,
//                'code' => $code,
//                'v' => $ver,
//            ])
//            ->send();
//
//        if ( !$auth_response->isOk ) {
//            return $this->returnErrors(['auth:error' => $soc_name.': Auth error' ]);
//        }
//
//        $access_token = $auth_response->data['access_token'];
//        if( !$access_token ) return $this->returnErrors(['auth:error' => $soc_name.': access_token recieving fail']);
//        $user_id = $auth_response->data['user_id'];
//
////        return $access_token;
//        // GET USER DATA
//        $user_response = $client->createRequest()
//            ->setMethod('GET')
//            ->setUrl('https://api.vk.com/method/users.get')
//            ->setData([
//                'user_id' => $user_id,
//                'access_token' => $access_token,
//                'fields' => $fields,
//                'v' => $ver,
//            ])
//            ->send();
//
//        if ( !$auth_response->isOk ) {
//            return $this->returnErrors(['auth:error' => $soc_name.' get User data error' ]);
//        }
//
////        return $user_response->data;
////        {"id":74549344,"first_name":"Habbib","last_name":"Jabbah","sex":2,"photo_big":"https://pp.userapi.com/c9588/u74549344/a_14e36703.jpg?ava=1","about":"дык всёж выше"}
//        $user_data = $user_response->data['response'][0];
//        $user_data['birthday'] = DateHelper::convertViewToDB( $user_data['bdate'], '.' );
//        $user_data['avatar'] = $user_data['photo_big'];
//
//        return $this->socAuthComplete( $soc_name, $user_data );
//
//    }
    // <<<<<<<<<<<<<<<<<<   VK   <<<<<<<<<<<<<<<<<<


    /// !!! !!!!!!!!!
    public function actionReset(){

//        Params::deleteAll(['test'=> true ]);

        clearLog('api');

        $param = Params::findOne(['key' => 'counter']);
        $param->value = '0';
        $param->save();

    }
    /// !!! !!!!!!!!!

    // >>>>>>>>>>>>>>>>>>   FB   >>>>>>>>>>>>>>>>>>
    public function actionFb()
    {

        Yii::$app->cache->flush();

        $app_params = Yii::$app->params;
        $locals = $this->getLocals();

        $soc_name = 'fb';

        $redirect_url = \yii\helpers\Url::toRoute('', true );
        $code = ArrayHelper::getValue( $this->GET, 'code');

        $ver = '3.1';
//        $scope = '&scope=email,user_birthday,user_gender';
        $scope = '';
        $fields = 'id,name,age_range,first_name,last_name';
        $app_id = $app_params[$soc_name.'.app_id'];

        $counter = '0';
        /*
        // !!! DEBUG !!!
        $param = Params::findOne(['key' => 'counter']);
        $param->value = strval( intval( $param->value ) + 1 );
        $param->save();
        $param->refresh();
        $counter = $param->value;

//        toLog( 'api', $counter .' > increment > code:'. $code );
        // !!! DEBUG !!!
        */

        // Show Login Form
        if( $code === null ){
            $redirect_url_ = 'https://www.facebook.com/v'.$ver.'/dialog/oauth?client_id='.$app_id.'&display=popup&redirect_uri='.$redirect_url.'&response_type=code'.$scope;
//            toLog( 'api', $counter .' > REDIRECT: to fb > ' . $redirect_url_ );
            return $this->redirect( $redirect_url_ )->send();
        }

//        toLog( 'api', $counter .' > from FB > ' . $code );

        // Auth User
        $client = new Client();

        $auth_response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://graph.facebook.com/v'.$ver.'/oauth/access_token')
            ->setData([
                'client_id' => $app_id,
                'client_secret' => $app_params[$soc_name.'.app_secret'],
                'redirect_uri' => $redirect_url,
                'code' => $code,
            ])
            ->send();

//        toLog( 'api', $counter .' > INTERNAL: start receive access_token > isOK=[' . $auth_response->isOk .']' );

        if ( !$auth_response->isOk ) { // Auth with Code Failed.

            // >>> Patch to cases when the redirect_uri calls more than once >>>
            $user = Auth::getUserByAuthCode( $soc_name, $code );
            if( $user ){
//                toLog( 'api', $counter .' > INTERNAL: receive access_token FAIL, but user was found by AUTH CODE!' );
                return $this->getSocResponse( $soc_name, $user, true );
            }
            // <<< Patch to cases when the redirect_uri calls more than once <<<

//            toLog( 'api', $counter .' > INTERNAL: receive access_token FAIL.' );

            return $this->returnErrors([
                'auth:error' => $soc_name.': Auth error.',
                'code' => $code,
            ]);

        }

//        toLog( 'api', $counter .' > INTERNAL: access_token RECEIVED.' );

        $access_token = $auth_response->data['access_token'];
        if( !$access_token ) return $this->returnErrors(['auth:error' => $soc_name.': access_token recieving fail']);
//        return $access_token;

        // GET USER DATA
        $user_response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://graph.facebook.com/v'.$ver.'/me')
            ->setData([
                'access_token' => $access_token,
                'fields' => $fields,
                'locale' => 'ru_RU',
            ])
            ->send();

//        $this->addError('state', 'get user data complete' );

        if ( !$user_response->isOk ) {
            return $this->returnErrors(['auth:error' => $soc_name.' get User data error' ]);
        }

//        return $this->returnErrors([ '_data' => $user_response->data ]); // !!!

        $user_data = $user_response->data;
        $user_data['avatar'] = 'https://graph.facebook.com/v'.$ver.'/'.$user_data['id'].'/picture?type=large';
        $birthday = ArrayHelper::getValue( $user_data, 'birthday' );
        $user_data['birthday'] = $birthday ? $birthday : null;
        $user_data['sex'] = $this->getGenderParamValue( ArrayHelper::getValue( $user_data, 'gender') );
        $user_data['auth_code'] = $code;

//        $this->addError('state', 'start to save');
//        return $this->returnErrors(['user_data' => $user_data]);
//        toLog( 'api', $counter .' > INTERNAL: USER DATA RECEIVED.' );
        return $this->socAuthComplete( $soc_name, $user_data );

    }
    // <<<<<<<<<<<<<<<<<<   FB   <<<<<<<<<<<<<<<<<<



    // >>>>>>>>>>>>>>>>>>   OK   >>>>>>>>>>>>>>>>>>
    public function actionOk()
    {

        // https://apiok.ru/dev/methods/#{%22application_id%22:%221271899904%22,%22application_key%22:%22CBAIKQMMEBABABABA%22,%22application_secret_key%22:%22FCD5F6D9E91CF7DB33C20007%22,%22permissions%22:%22VALUABLE_ACCESS;LONG_ACCESS_TOKEN%22,%22access_token%22:%22-s-07R4OmMJMVLkPDu0QSQ2oo37HVP3msL7-R-8bEMTJSLuM1%22,%22session_key%22:%22ass%22,%22session_secret_key%22:%22asss%22,%22method%22:%22users.getCurrentUser%22,%22session%22:%22web%22,%22oauth%22:%22server%22,%22method_params%22:{%22format%22:%22json%22,%22access_token%22:%22\t-s-07R4OmMJMVLkPDu0QSQ2oo37HVP3msL7-R-8bEMTJSLuM1%22}}

        $app_params = Yii::$app->params;
        $locals = $this->getLocals();

        $soc_name = 'ok';
        $redirect_url = \yii\helpers\Url::toRoute('', true );
        $code = ArrayHelper::getValue( $this->GET, 'code');
        $ver = 0;
        $scope_values = 'VALUABLE_ACCESS,GET_EMAIL,LONG_ACCESS_TOKEN';
        $scope = '&scope='.$scope_values;
        $fields = 'id,first_name,last_name,photo_big,gender,bdate';
        $app_id = $app_params[$soc_name.'.app_id'];
        $app_secret = $app_params['ok.app_secret'];

        // Show Login Form
        if( $code == null ){
//            https://connect.ok.ru/oauth/authorize?client_id={clientId}&scope={scope}&response_type={{response_type}}&redirect_uri={redirectUri}&layout={layout}&state={state}
//              layout   w – (по умолчанию) стандартное окно для полной версии сайта;
//                       m – окно для мобильной авторизации;
//                       a – упрощённое окно для мобильной авторизации без шапки.
            $redirect_url_ = 'https://connect.ok.ru/oauth/authorize?client_id='.$app_id.'&layout=w&redirect_uri='.$redirect_url.'&response_type=code'.$scope;
//                return $redirect_url_;
            $this->redirect( $redirect_url_ );
            return;
        }

        // Auth User
        $client = new Client();

        // https://api.ok.ru/oauth/token.do?code={code}&client_id={client_id}&client_secret={client_secret}&redirect_uri={redirect_uri}&grant_type={grant_type}
        $auth_data = [
            'client_id' => $app_id,
            'client_secret' => $app_params[$soc_name.'.app_secret'],
            'redirect_uri' => $redirect_url,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];

        $auth_response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.ok.ru/oauth/token.do')
            ->setData( $auth_data )
            ->send();

        if ( !$auth_response->isOk ) {
            debug( $auth_response );
            return $this->returnErrors(['auth:error' => $soc_name.': Auth error' ]);
        }

//        echo('auth fail!!!');
//        debug( $this->GET );
//        debug($auth_data);
//        debug( $auth_response->data );
//        return; // !!!

        $access_token = $auth_response->data['access_token'];
        if( !$access_token ) return $this->returnErrors(['auth:error' => $soc_name.': access_token recieving fail']);
//        $user_id = $auth_response->data['user_id'];

//        return $access_token;
        // GET USER DATA
        $user_request_data = [
            'format' => 'json',
            'application_key' => $app_params['ok.app_public'],
            'method' => 'users.getCurrentUser',
//            '__online' => false,
        ];
        ksort( $user_request_data );

        // SIG
        $rq_secret_key = md5($access_token . $app_secret );
        $str_ = http_build_query( $user_request_data, null, '' ) . $rq_secret_key;
        $sig = md5( $str_ );

        $user_request_data = array_merge( $user_request_data, [
            'sig' => $sig,
            'access_token' => $access_token,
        ]);
//            'access_token' => $access_token,
//        \Yii::$app->response->format = Response::FORMAT_HTML;
//        $arr = ['b'=>1,'a'=>2,'c'=>'3' ];
//        return http_build_query( ksort($arr), null, '' );
//        return '<p>$str_: '. $str_ .'</p><br/><p>$sig: '.$sig.'</p><br/><p>'.$access_token .' + ' . $app_secret .' = ' . $rq_secret_key.'</p><br/>';

        $user_response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.ok.ru/fb.do')
            ->setData( $user_request_data )
            ->send();

        if ( !$auth_response->isOk ) {
            return $this->returnErrors(['auth:error' => $soc_name.' get User data error' ]);
        }

        $user_data = $user_response->data;

        $user_data['id'] = $user_data['uid'];

        $user_data['sex'] = $this->getGenderParamValue( ArrayHelper::getValue( $user_data, 'gender') );
        $user_data['avatar'] = $user_data['pic_3'];

//        return $this->returnErrors( ['data' => $user_data ]);
        return $this->socAuthComplete( $soc_name, $user_data );

    }
    // <<<<<<<<<<<<<<<<<<   OK   <<<<<<<<<<<<<<<<<<



    public function getGenderParamValue( $gender_ ){
        if( $gender_ == 'male' ) $gender_ = 2;
        else if( $gender_ == 'female' ) $gender_ = 1;
        else $gender_ = 0;
        return $gender_;
    }


    public function socAuthComplete( $soc_id, $user_data ){

        //
        function _updateUserData( $user_, $user_data, $soc_id ) {
            $user_->first_name = $user_data['first_name'];
            $user_->last_name = $user_data['last_name'];
            $user_->birthday = $user_data['birthday'];
            $user_->gender = $user_data['sex'];
            $user_->soc_id = $soc_id;
            $user_->soc_user_image = $user_data['avatar'];
        }
        //

        $success = false;
        $soc_user_id = strval( $user_data['id']);
        $user = Auth::getUserBySocId( $soc_id, $soc_user_id );
        $auth_code = ArrayHelper::getValue( $user_data, 'auth_code' );
        // Brand new User
        if( $user == null ) {

            $user = new SiteUser();
            _updateUSerData( $user, $user_data, $soc_id );
            $user->soc_user_id =  $soc_user_id;
            $user->username = $soc_id.'.'.$soc_user_id;
            $user->setPassword( Yii::$app->security->generateRandomString() );
            $user->generateAuthKey();

//            return $this->returnErrors( ['user created' => $user->toArray(), 'user_data' => $user_data ] ); // !!!

            if( $user->save() ){

//                return $this->returnErrors( ['bbb' => $user->toArray() ] ); // !!!

                $auth = new Auth();
                $auth->source = $soc_id;
                $auth->source_id = $soc_user_id;
                $auth->user_id = $user->id;
                $auth->auth_code = $auth_code;

                if( $auth->save() ){
                    $success = true;
//                    return $this->returnErrors( ['usr2' => $user->toArray() ] ); // !!!
                }else{
//                    return $this->returnErrors( ['usr3' => $user->toArray() ] ); // !!!
                }
            }

        }else{ // User exists
            $success = true;

            _updateUserData( $user, $user_data, $soc_id );

            // TODO: save access token to Auth
            // update Auth Code
            if( $auth_code ) {
                $auth = Auth::findOne(['source' => $soc_id, 'user_id' => $user->id ]);
//                toLog( 'api', 'update user AUTH_CODE > '. $soc_id .', '. $user->id .', '. $auth_code );
                if ( $auth ) {
                    $auth->auth_code = $auth_code;
                    $auth->save();
                }

            }

        }


        return $this->getSocResponse( $soc_id, $user, $success );

    }


    public function getSocResponse( $soc_id, $user, $success ){

        if( $user && $success ){
            $response = $this->_loginUser( $user );
//            $response = array_merge( [ 'oauth_client' => $soc_id ], $response );
        }else{
            $response = [
                'success' => $success,
                'user' => $user->toArray(),
                'user_data' => $user_data,
                'user_errors' => $user != null ? $user->getErrors() : null,
                'auth' => $auth,
                'auth_errors' => $auth != null ? $auth->getErrors() : null,
            ];
        }



        \Yii::$app->response->format = Response::FORMAT_HTML;
//        return "<script>window.opener.$(window.opener).trigger('oauth:complete', ".json_encode($response).");window.close();</script>";
        return json_encode(array_merge(['success' => 'true'], ["access_token" => $user->auth_key]));
    }








    public function getMethodsInfo()
    {
        $app_params = Yii::$app->params;
        $methods = parent::getMethodsInfo();
        $methods = array_merge( $methods, [
            //            '' => [
            //                'description' => 'Манипуляция пользователями',
            //                'request' => [
            //
            //                ],
            //                'response' => []
            //            ],
            
            'dev-reset' => [
                self::DEV_ONLY => true,
                'request' => [
                    'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                ],
                "response" => [],
                "description" => "Dev only action. Deletes the test user if it exists",
            ],

            'reg' => [
                'request' => [
					'username' => 'string :: required',
                    'email' => 'string :: required',
                    'password' => 'string :: required',
                    'first_name' => 'string :: required',
                    'last_name' => 'string :: required',
                    'is_tester' => 'boolean :: optional // Sets the tester state for identifying creation case of a User.',
//                    'personal_data_agreement' => 'boolean :: required',
                    'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                ],
                "response" => $this->allowed_user_attributes,
            ],

            'confirm-email-send' => [
                self::AUTHORIZED_ONLY => true,
                'request' => [
                    'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                    'access_token' => 'string :: required',
                ],
                "response" => [
                    'dev!: email_confirm_token(string)',
                ],
            ],

            'confirm-email' => [
                'request' => [
                    'token' => 'string :: required',
                ],
                "response" => []
            ],

            'login' => [
//                self::EMAIL_CONFIRM_REQUIRED=> true,
                'request' => [
                    'username' => 'string :: required',
                    'password' => 'string :: required',
                    'remember_me' => 'boolean :: optional // works if remember_me is enabled on the server',
                    'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                ],
                "response" => $this->allowed_user_attributes,
            ],

            'auth' => [
                self::AUTHORIZED_ONLY => true,
                'request' => [
                    'access_token' => 'string :: required',
					'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                ],
                "response" => $this->allowed_user_attributes,
            ],

            'renew-access-token' => [
//                self::EMAIL_CONFIRM_REQUIRED=> true,
                'request' => [
                    'renew_access_token' => 'string :: required',
                    'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                ],
                "response" => [
                    "access_token",
                    "renew_access_token",
                ],
            ],

            'edit' => [
                self::AUTHORIZED_ONLY => true,
                'request' => [
                    'access_token' => 'string :: required',
                    'username' => 'string :: optional',
                    'email' => 'string :: optional',
                    'password' => 'string :: optional',
                    'first_name' => 'string :: optional',
                    'last_name' => 'string :: optional',
                ],
                "response" => $this->allowed_user_attributes,
            ],
            'logout' => [
                self::AUTHORIZED_ONLY => true,
                'request' => [
                    'access_token' => 'string :: required',
                ],
                "response" => []
            ],
            'recover-password' => [
                'request' => [
                    'email' => 'string :: required',
                    'dev_token' => 'string :: optional :: dev // works if dev_token is enabled on the server',
                ],
                "response" => [
                    'dev!: password_confirm_token(string)',
                ]
            ],
            'reset-password' => [
                'request' => [
//                    'access_token' => 'string :: required',
                    'password' => 'string :: required',
                    'token' => 'string :: required',
                ],
                "response" => []
            ],

            // SOCIALS
            'vk' => [
                'description' => 'Look to Soc tab. After complete dispatches event <code>oauth:complete</code> to window.',
                'request' => [
                ],
                "response" => []
            ],
            'fb' => [
                'description' => 'Look to Soc tab. After complete dispatches event <code>oauth:complete</code> to window.',
                'request' => [
                ],
                "response" => []
            ],
            'ok' => [
                'description' => 'Look to Soc tab. After complete dispatches event <code>oauth:complete</code> to window.',
                'request' => [
                ],
                "response" => []
            ],
    //            '[id]' => [
    //                'description' => '',
    //                'request' => [
    //
    //                ],
    //                'response' => []
    //            ],
        ]);
        return $methods;
    }


    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> DEV VARS >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function getDevVars()
    {
        $app_params = Yii::$app->params;
        //

        $devvars = parent::getDevVars();
        $devvars = array_merge( $devvars, [
            'test-user-username' => $app_params['api.tester.username'],
            'new-test-user-username' => $app_params['api.tester.username_new'],
            'test-user-email' => $app_params['api.tester.email'],
            'token-send-timeout' => $app_params['api.tokenSendTimeout'],
        ]);
        return $devvars;
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< DEV VARS <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




    // >>>>>>>>>>>>>>>>>>   SUPPORT METHODS   >>>>>>>>>>>>>>>>>>
    public function canSendToken($token)
    {
        if (empty($token)) {
            return 0;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['api.tokenSendTimeout'];
        $delta = max($timestamp + $expire - time(), 0 ); // max()
        return $delta;
    }

    public function isTokenNotExpired($token)
    {

        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['api.tokenResetExpire'];
        return $timestamp + $expire >= time();
    }

    public function getCurrentUserData(){
        if( Yii::$app->user->identity ) {
            $user = (array)Yii::$app->user->identity->attributes;
            $user = $this->cleanupUserData( $user );
            return $user;
        }
        return null;
    }

    public function cleanupUserData( $userdata ){
        if( !$userdata ) return null;
        if( $this->allowed_user_attributes ) {
            $user_data_cleared = Utils::array_filter_key( $userdata, $this->allowed_user_attributes );
            if( $this->HAS_DEV_TOKEN ) $user_data_cleared['is_tester'] = $userdata['is_tester'];
            return $user_data_cleared;
        }
        return $userdata;
    }

    // SEND MAIL
    public function sendMail($to, $subject, $template, $data ){
        return Yii::$app
            ->mailer
            ->compose( $template, $data )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo( $to )
            ->setSubject( $subject )
            ->send();
    }

    //
    public function uploadFile( $model, $attr_url, $attr_file ){

        $model->$attr_file = UploadedFile::getInstance( $model, $attr_file );

        if( !empty( $model->$attr_file ) ){

            if ($model->upload( $attr_url, $attr_file )) {
                return true;
            }
        }

        return false;
    }


}