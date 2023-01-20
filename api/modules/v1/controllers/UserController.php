<?php

namespace api\modules\v1\controllers;

use common\models\User;
use common\models\UserExt;
use common\components\UserUrlManager;
use common\components\Emailer;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use yii\web\Cookie;
use yii\helpers\ArrayHelper;
use common\modules\auth\controllers\SocAuthController;

class UserController extends AppController
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'authentificator' => [
                'class' => HttpBearerAuth::className(),
                'except' => ['login', 'signup', 'password-restore', 'reset-password', 'email-confirm']
            ],
        ]);
    }

    // SIGNUP
    public function actionSignup($soc = null, $code = null){
        $enabled_clients = ArrayHelper::getValue( Yii::$app->params, 'signup.enabled_clients' );
        if($soc || $code){
            return $this->socAuth('signup');
        }
        if( $enabled_clients['email-password'] ) return $this->emailSignup();
        return $this->returnError( Yii::t('app', 'Registration disabled') );
    }

    // LOGIN
    public function actionLogin($soc = null, $code = null){
        if($soc || $code){
            return $this->socAuth('login');
        }
        return $this->emailLogin();
    }

    // UPDATE
    public function actionUpdate(){

        $post = Yii::$app->request->post();
        if( !$post ) return $this->returnError( Yii::t('app', 'Data required'));

        $user = User::findIdentity(\Yii::$app->user->id);
        if( !$user ) return $this->returnError( Yii::t( 'app', 'User is not found'));

        $user_update_result = $user->updateData( $user, $post );
        if( isset( $user_update_result['error']) ) return $this->returnError( $user_update_result['error'] );

        return $this->getProfile($user);
    }


    //Выход из системы
    public function actionLogout(){

        $user = User::findIdentity( \Yii::$app->user->id );

        $user->auth_key = null;
        $user->updated_at = time();

        if( $user->save( false ) ){
            \Yii::$app->user->logout();
            return $this->returnSuccess( Yii::t('app', 'You have successfully logged out.') );
        }

        return $this->returnError( $user->errors );
    }


    //Получение профиля
    public function actionProfile(){
        $user = User::findIdentity( \Yii::$app->user->id, true );
//        return ['user' => \Yii::$app->user->id];
//        $user = User::find()->where(['id'=>\Yii::$app->user->id])->with('email')->one();
//        return $user;
        return $this->getProfile($user);
    }


    //Подтверждение почты
    public function actionEmailConfirm($token){

        $redirect_url = UserUrlManager::getDomainUrl().'?confirm_status=';
        $result = UserExt::confirmEmail( $token );
        if( isset($result['error'])) return Yii::$app->request-$this->redirect( $redirect_url.$result['error'] );
        return Yii::$app->request-$this->redirect( $redirect_url.'success' );

    }


    //Авторизация по E-mail
    public function emailLogin(){

        $email = $this->getParameterFromRequest('email');
        $password = $this->getParameterFromRequest('password');

        if( !$email || !$password ) return $this->returnError(['email' => 'Wrong Email or Password']);

        $user = User::getUserByEmail($email);

        if( !$user ) return $this->returnError(['email' => 'Wrong Email or Password']);
        if( !$user->validatePassword( $password ) )  return $this->returnError(['email' => 'Wrong Email or Password']);

        $user->auth_source = 'e-mail';
        $user->last_login_at = time();

        return $this->getProfile($user);

    }


    //Регистрация пользователя по E-Mail
    public function emailSignup(){

        $params = Yii::$app->params;
        $email = $this->getParameterFromRequest('email');
        $password = $this->getParameterFromRequest('password');
        $username = $this->getParameterFromRequest('username');
        $rules_accepted = $this->getParameterFromRequest('rules_accepted');

//        return $this->returnError(['rules_accepted' => ArrayHelper::getValue( $params, 'signup.require.rules_accepted') ]); // !!!

        if( ArrayHelper::getValue( $params, 'signup.require.rules_accepted') === true && !$rules_accepted  )
            return $this->returnError(['rules_accepted' => Yii::t('app', 'Must agree to the rules')]);

//        return $this->returnError(['user' => User::getUserByEmail($email) ]); // !!!
//        return $this->returnError(['user' => UserExt::getByEmail($email) ]); // !!!

        if( $email && ArrayHelper::getValue( $params, 'signup.unique.email') === true && UserExt::getByEmail($email) )
            return $this->returnError(['rules_accepted' => Yii::t('app', 'Such Email is already registered')]);
//        if (Email::find()->where(['value' => $email])->one())
//            return $this->returnError(['email' => 'Такой E-mail уже зарегистрирован.']);

        $user = User::createUser( compact( 'email', 'password', 'username', 'rules_accepted') );
        if( isset($user['error']) ) return $user['error'];

        // USER CREATED SUCCESSFULLY!
        // >>> APP ACITIONS >>>

        // <<< APP ACITIONS <<<

        return $this->getProfile($user);

    }



    //Обновление E-mail
    public function updateEmail($email, $user){
        $db_email = Email::find()->where(['user_id' => $user->id])->one();
        if($db_email->value != $email) {
            $db_email->value = $email;
            $db_email->is_verified = 0;
        }
        if(!$db_email->save()){
            return $db_email->errors;
        }
        return true;
    }


    //Получение профиля пользователя
    public function getProfile( $user = null ){

        if($user == null){
            return false;
        }

        if( $user->auth_key == null ){
            $user->auth_key = \Yii::$app->security->generateRandomString();
            $user->updated_at = time();
            $user->save( false );
        }

        $profile = [

            'id' => $user->id,
            'access_token' => $user->auth_key,

            'username' => $user->username,

            'first_name' => $user->userExt['first_name'],
            'middle_name' => $user->userExt['middle_name'],
            'last_name' => $user->userExt['last_name'],
            'phone' => $user->userExt['phone'],

            'email' => $user->userExt['email'],
//            'unconfirmed_email' => $user->userExt['unconfirmed_email'],
//            'userExt' => $user->userExt,

        ];

        return $this->returnSuccess($profile, 'profile');
    }



    //
    protected function socAuth( $type ){

        // получаем тип соц.сети ('fb' или 'vk')
        $soc_id = $this->getParameterFromRequest('soc');

        //сохраняем куки для повторного захода в метод после редиректа
        if(!$soc_id){
            $soc_id = Yii::$app->request->cookies->get('soc')->value;
            Yii::$app->response->cookies->remove('soc');
        } else {
            Yii::$app->response->cookies->add(new Cookie([
                'name' => 'soc',
                'value' => $soc_id
            ]));
        }
        if( !$soc_id ) return false; // TODO: handler for FALSE value

        $access_token = $this->getParameterFromRequest('access_token');

        if($access_token){
            Yii::$app->response->cookies->add(new Cookie([
                'name' => 'access_token',
                'value' => $access_token
            ]));
        }

        //получаем нужную модель
        $socName = 'common\modules\auth\social\models\\'.ucfirst($soc_id);
        $get_params = Yii::$app->request->get();
        $code = ArrayHelper::getValue($get_params , 'code');
        $soc = new $socName();

        // Первый заход в метод - перенаправляем в социалку с redirect_url
        if ($code == null) {
            $url = $soc->getLoginUrl();
            return $this->redirect( $url );
        }

        //При втором заходе в метод реализуем логику взаимодействия с БД
        $socAuth = new SocAuthController();
        $response = $socAuth->auth( $soc, $type );

        if($response) {
            if(is_array($response) && array_key_exists('error', $response)){
                $result = $response;
            } else { // Если успешно авторизовались через социалку
                $user = User::findIdentityByAccessToken( $response );
                $result =  [
                    'success' => true,
//                    'user' => $user,
                    'data' => $this->getProfile($user),
//                    '$response' => $response,
                ];
                $soc_user_id = $response;
            }
        } else {
            $result = $this->returnError(['soc' => 'You have not assigned this social network!']);
        }

        $result['oauth_client'] = $soc_id;
//        echo '<pre>';
//        var_dump($result);
//        echo '</pre>';
//        return 1;
        return $this->returnOpenerResponse($result);

    }




    // >>> PASSWORD RESTORE >>>

    /*
     * Изменение пароля авторизованного пользователя или по токену
     */
    public function actionResetPassword( $password = null, $token = null ){

        $password = $this->getParameterFromRequest( 'password' );
        if( !$password ) return $this->returnError(['password:wrong' => 'Введите пароль'],500);

        $current_user = $this->getIdentity();

        if( !$current_user ) {

            $token = $this->getParameterFromRequest( 'token' );
            if( !$token ) return $this->returnError(['token:wrong' => 'Введите токен'], 500);

            $user = User::findByPasswordResetToken($token);
            if (!$user) {
                return $this->returnError([
                    'token:wrong' => 'Некорректный токен',
                    'token' => $token,
                ], 500);
            }

        }else{
            $user = $current_user;
        }

//        $user->setScenario(User::SCENARIO_PASSWORD ); // TODO: Переключить сценарии чтобы можно было валидировать
//        return $this->returnError([ 'scenario' => $user->scenario, 'rules' => $user->rules() ]);

        $user->setPassword($password);
        $user->removePasswordResetToken();
        if( !$user->save( false) ) return $this->returnErrors([ 'validation-error' => $user->errors ]);

        // Authorize user
        Yii::$app->user->login( $user );

        return $this->getProfile();

    }

    // Запрос на Восстановление пароля неавторизованного
    public function actionPasswordRestore(){

        $email = $this->getParameterFromRequest('email');
        $user = User::getUserByEmail( $email );
        if(!$user){
            return $this->returnError(['email' => Yii::t('app', 'User is not found')]);
        }

        $pass_restore_type = ArrayHelper::getValue( Yii::$app->params, 'passwordRestoreType' );
        if( $pass_restore_type == 'generate' ) {
            $result = $this->_restorePasswordDirectlyToEmail( $user, $email );
        }else {
            $result = $this->_restorePasswordViaToken($user, $email);
        }

        return $result === true ?  $this->returnSuccess( Yii::t('app', 'Password recovery email sent') ) : $this->returnError( $result );
    }


    private function _restorePasswordDirectlyToEmail( $user, $email ){

        // создаём новый пароль
        $new_password = \Yii::$app->security->generateRandomString(8);
        $user->setPassword($new_password);
        if( !$user->save( false ) ) return $user->errors;

        //Отправляем письмо с паролем
//        $message['html_layout'] = 'passwordSend-html.php';
//        $message['text_layout'] = 'passwordSend-text.php';
        $data['password'] = $new_password;

//        return [
//            '$user' => $user,
//            '$email' => $email,
//            '$message' => $message,
//            'passs' => $data['password'],
//        ];

        $send_result = Emailer::sendMail(
            $email,
            Yii::t('app', 'Letter Subject: Password Recovery'),
            ArrayHelper::getValue( Yii::$app->params, 'email_on.send_password', 'passwordSend' ),
            $user,
            $data
        );

        return $send_result;

    }

    private function _restorePasswordViaToken( $user, $email ){

        $user->generatePasswordResetToken();
//        return ['>> ', $user->password_reset_token ];
        if(!$user->save( false )){
            return $user->errors;
        }

//        return [
//            '$user' => $user,
//            '$email' => $email,
//            '$message' => $message,
//            '$user->password_reset_token' => $user->password_reset_token,
//        ];

//        $send_result = Emailer::sendMail( $email, 'Восстановление пароля', 'passwordResetToken', $user );
        $send_result = Emailer::sendMail(
            $email,
            Yii::t('app', 'Letter Subject: Password Recovery'),
            ArrayHelper::getValue( Yii::$app->params, 'email_on.send_password_restore_token', 'passwordResetToken' ),
            $user
        );

        return $send_result;

        //////
        ///

    }

    // <<< PASSWORD RESTORE <<<
}
