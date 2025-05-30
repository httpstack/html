<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit075f06b3c5aa4cd2d658476f0a8b2fbc
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit075f06b3c5aa4cd2d658476f0a8b2fbc', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit075f06b3c5aa4cd2d658476f0a8b2fbc', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit075f06b3c5aa4cd2d658476f0a8b2fbc::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
