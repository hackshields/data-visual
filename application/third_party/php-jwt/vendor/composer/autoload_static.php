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
// autoload_static.php @generated by Composer
namespace Composer\Autoload;

class ComposerStaticInit701cdceb537acdc15b488ca81549e39a
{
    public static $prefixLengthsPsr4 = array('F' => array('Firebase\\JWT\\' => 13));
    public static $prefixDirsPsr4 = array('Firebase\\JWT\\' => array(0 => __DIR__ . '/..' . '/firebase/php-jwt/src'));
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit701cdceb537acdc15b488ca81549e39a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit701cdceb537acdc15b488ca81549e39a::$prefixDirsPsr4;
        }, null, ClassLoader::class);
    }
}

?>