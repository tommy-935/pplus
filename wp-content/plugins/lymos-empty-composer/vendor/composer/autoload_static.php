<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit37e7c43995e92fc87859c4234bbbc13d
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Lymos\\Lwc\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Lymos\\Lwc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit37e7c43995e92fc87859c4234bbbc13d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit37e7c43995e92fc87859c4234bbbc13d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit37e7c43995e92fc87859c4234bbbc13d::$classMap;

        }, null, ClassLoader::class);
    }
}
