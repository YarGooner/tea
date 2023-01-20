<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 26.11.2018
 * Time: 12:51
 */
namespace common\modules\auth\social;

interface SocInterface
{
    public function getLoginUrl();

    public function getAccessTokenUrl();

    public function getUserUrl();

    public function getAuthArgs();

    public function getUserArgs($response);

    public function getUserIdFromResponse($response);

    public function getUserDataFromResponse($response);

    public function getAccessTokenFromResponse($response);
}