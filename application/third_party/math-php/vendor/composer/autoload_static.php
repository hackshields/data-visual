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

class ComposerStaticInitb8d52a58fcc7aacf128d9f6cf5f2eb93
{
    public static $prefixLengthsPsr4 = array('M' => array('MathPHP\\' => 8));
    public static $prefixDirsPsr4 = array('MathPHP\\' => array(0 => __DIR__ . '/../..' . '/src'));
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb8d52a58fcc7aacf128d9f6cf5f2eb93::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb8d52a58fcc7aacf128d9f6cf5f2eb93::$prefixDirsPsr4;
        }, null, ClassLoader::class);
    }
}

?>