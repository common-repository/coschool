<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfabdf9de748f747f23e227d8f5cf6ac7
{
    public static $files = array (
        '3643df700757a247351b378bc7c3ac76' => __DIR__ . '/../..' . '/inc/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Codexpert\\CoSchool\\Third_Party\\' => 31,
            'Codexpert\\CoSchool\\App\\' => 23,
            'Codexpert\\CoSchool\\Abstracts\\' => 29,
            'Codexpert\\CoSchool\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Codexpert\\CoSchool\\Third_Party\\' => 
        array (
            0 => __DIR__ . '/../..' . '/thirdparty',
        ),
        'Codexpert\\CoSchool\\App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
        'Codexpert\\CoSchool\\Abstracts\\' => 
        array (
            0 => __DIR__ . '/../..' . '/abstracts',
        ),
        'Codexpert\\CoSchool\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Codexpert\\Plugin\\Base' => __DIR__ . '/..' . '/codexpert/plugin/src/Base.php',
        'Codexpert\\Plugin\\Fields' => __DIR__ . '/..' . '/codexpert/plugin/src/Fields.php',
        'Codexpert\\Plugin\\Metabox' => __DIR__ . '/..' . '/codexpert/plugin/src/Metabox.php',
        'Codexpert\\Plugin\\Notice' => __DIR__ . '/..' . '/codexpert/plugin/src/Notice.php',
        'Codexpert\\Plugin\\Settings' => __DIR__ . '/..' . '/codexpert/plugin/src/Settings.php',
        'Codexpert\\Plugin\\Setup' => __DIR__ . '/..' . '/codexpert/plugin/src/Setup.php',
        'Codexpert\\Plugin\\Table' => __DIR__ . '/..' . '/codexpert/plugin/src/Table.php',
        'Codexpert\\Plugin\\Widget' => __DIR__ . '/..' . '/codexpert/plugin/src/Widget.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfabdf9de748f747f23e227d8f5cf6ac7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfabdf9de748f747f23e227d8f5cf6ac7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfabdf9de748f747f23e227d8f5cf6ac7::$classMap;

        }, null, ClassLoader::class);
    }
}
