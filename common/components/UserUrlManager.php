<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 14.11.2018
 * Time: 11:32
 */

namespace common\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UrlManager;

class UserUrlManager extends UrlManager
{

    const UPLOADS = '/uploads/global';
    const USER_UPLOADS = '/users';

    public $root = '/htdocs';

    public $hideRoot = true;

    public function createUrl($params)
    {
        $url = parent::createUrl($params);
        if($this->hideRoot) {
            $url = $this->removeRoot($url);
        }
        return $url;
    }

    public function parseRequest($request)
    {
        if ($this->enablePrettyUrl) {
            /* @var $rule UrlRule */
            foreach ($this->rules as $rule) {
                $result = $rule->parseRequest($this, $request);
                if (YII_DEBUG) {
                    Yii::debug([
                        'rule' => method_exists($rule, '__toString') ? $rule->__toString() : get_class($rule),
                        'match' => $result !== false,
                        'parent' => null,
                    ], __METHOD__);
                }
                if ($result !== false) {
                    return $result;
                }
            }

            if ($this->enableStrictParsing) {
                return false;
            }

            Yii::debug('No matching URL rules. Using default URL parsing logic.', __METHOD__);

            $suffix = (string) $this->suffix;

            $pathInfo = $this->resolveRequestUri($request);

            $app = basename(Yii::getAlias('@app')).'/';
            $pos = stripos($pathInfo,$app);
            $basePath = substr($pathInfo,$pos + strlen($app));
            $params_pos = stripos($basePath, '?');
            if($params_pos){
                $pathInfo = substr($basePath,0,$params_pos);
            } else {
                $pathInfo = $basePath;
            }
            $normalized = false;
            if ($this->normalizer !== false) {
                $pathInfo = $this->normalizer->normalizePathInfo($pathInfo, $suffix, $normalized);
            }
            if ($suffix !== '' && $pathInfo !== '') {
                $n = strlen($this->suffix);
                if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
                    $pathInfo = substr($pathInfo, 0, -$n);
                    if ($pathInfo === '') {

                        // suffix alone is not allowed
                        return false;
                    }
                } else {

                    // suffix doesn't match
                    return false;
                }
            }

            if ($normalized) {
                // pathInfo was changed by normalizer - we need also normalize route

                return $this->normalizer->normalizeRoute([$pathInfo, []]);
            }
            return [$pathInfo, []];
        }

        Yii::debug('Pretty URL not enabled. Using default URL parsing logic.', __METHOD__);
        $route = $request->getQueryParam($this->routeParam, '');
        if (is_array($route)) {
            $route = '';
        }
        return [(string) $route, []];
    }

    protected function resolveRequestUri($request)
    {
        if ($request->headers->has('X-Rewrite-Url')) { // IIS
            $requestUri = $request->headers->get('X-Rewrite-Url');
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            throw new InvalidConfigException('Unable to determine the request URI.');
        }
        return $requestUri;
    }


    //
    public static function getProtocol(){
        if (Yii::$app) {
            $params = Yii::$app->params;
        } else {
            $params = [];
        }

        $protocol = ArrayHelper::getValue($params, '__PROTOCOL__');
        if ($protocol) {
            return $protocol;
        }

        $protocol_param = ArrayHelper::getValue($params, 'fixedProtocol');
        $protocol = ($protocol_param ? $protocol_param : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http'));
        ArrayHelper::setValue($params, '__PROTOCOL__', $protocol); // cache
        return $protocol;
    }


    //
    public static function getDomainUrl($app = null, $prefix = true, $suffix = false )
    {
        // Get the value from the Cache
        if (Yii::$app) {
            $url = ArrayHelper::getValue(Yii::$app->params, '_domain_url_');
        }

        if (!isset($url)) {
            if (!$app) {
                $app = '@app';
            }
            $pathInfo = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (Yii::$app) {
                $root = ArrayHelper::getValue(Yii::$app->params, 'rootDomain');
            }
            $root_pos = isset($root) ? stripos($pathInfo, $root) : false;
            if ($root_pos !== false) {
                $url = substr($pathInfo, 0, $root_pos) . substr($pathInfo, $root_pos + strlen($root));
            } else {
                $url = $pathInfo;
            }


            $app = basename(Yii::getAlias($app)) . '/';

            $pos = stripos($pathInfo, $app);
            $basePath = substr($url, 0, $pos);
            $params_pos = strpos($basePath, '?');
            if ($params_pos) {
                $url = substr($basePath, 0, $params_pos);
            } else {
                $url = $basePath;
            }

            $url = rtrim($url, '/');

            if ($prefix == true) {
//                $url = 'https://' . $url;

                $protocol = self::getProtocol();

                if (Yii::$app) {
                    $protocol_param = ArrayHelper::getValue(Yii::$app->params, 'fixedProtocol');
                }

                $url = ($protocol_param ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http')) . '://' . $url;
            }

            if (Yii::$app) {
                Yii::$app->params['_domain_url_'] = $url; // Put the value from the Cache
            }
        }

        if( $suffix ){
            $url .= $suffix;
        }

        return $url;
    }

    //
    public static function setAbsoluteUploadsPath( $data, $fields = null, $prefix = null )
    {
        if( !$data ) return $data;

        $prefix = self::getDomainUrl( null, true, $prefix ? $prefix : self::UPLOADS );

        // When a string data passed
        if( is_string($data)){
            return $prefix.$data;
        }

        function _traverse( $data, $prefix, $fields ){
            foreach($data as $i => $item) {
                if( is_array( $item ) ) {
                    $data[$i] = _traverse( $item, $prefix, $fields );
                }else if( $item ){
                    foreach ( $fields as $field ) {
                        if( $i == $field ) $data[$i] = $prefix . $item;
                    }
                }
            }
            return $data;
        }

        return _traverse( $data, $prefix, $fields );

    }


    //
    private function removeRoot($url){
        $root_pos = stripos($url, $this->root);
        //убираем папку root из URL
        if ($root_pos !== false) {
            $url = substr($url, 0, $root_pos) . substr($url, $root_pos + strlen($this->root));
        }
        return $url;
    }

    //
    public function getUploadsPath()
    {
        $imgPath = Url::base();
        $app = basename(Yii::getAlias('@app'));
        $pos = stripos($imgPath, $app);
        $imgPath = substr($imgPath,0,$pos-1);
        $imgPath .= Yii::getAlias('@images').'/';
        return $imgPath;
    }
}