<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
namespace google\appengine\api\app_identity;

class AppIdentityService
{
    public static $scope;
    public static $accessToken = array('access_token' => 'xyz', 'expiration_time' => '2147483646');
    public static function getAccessToken($scope)
    {
        self::$scope = $scope;
        return self::$accessToken;
    }
}

?>