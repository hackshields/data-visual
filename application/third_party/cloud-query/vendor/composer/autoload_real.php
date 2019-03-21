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
// autoload_real.php @generated by Composer
class ComposerAutoloaderInitcaaeb95adee1a370bd132bbe1c8a3eb3
{
    private static $loader;
    public static function loadClassLoader($class)
    {
        if ('Composer\\Autoload\\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }
        spl_autoload_register(array('ComposerAutoloaderInitcaaeb95adee1a370bd132bbe1c8a3eb3', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInitcaaeb95adee1a370bd132bbe1c8a3eb3', 'loadClassLoader'));
        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__ . '/autoload_static.php';
            call_user_func(\Composer\Autoload\ComposerStaticInitcaaeb95adee1a370bd132bbe1c8a3eb3::getInitializer($loader));
        } else {
            $map = (require __DIR__ . '/autoload_namespaces.php');
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }
            $map = (require __DIR__ . '/autoload_psr4.php');
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }
            $classMap = (require __DIR__ . '/autoload_classmap.php');
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }
        $loader->register(true);
        if ($useStaticLoader) {
            $includeFiles = Composer\Autoload\ComposerStaticInitcaaeb95adee1a370bd132bbe1c8a3eb3::$files;
        } else {
            $includeFiles = (require __DIR__ . '/autoload_files.php');
        }
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequirecaaeb95adee1a370bd132bbe1c8a3eb3($fileIdentifier, $file);
        }
        return $loader;
    }
}
function composerRequirecaaeb95adee1a370bd132bbe1c8a3eb3($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        require $file;
        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
    }
}

?>