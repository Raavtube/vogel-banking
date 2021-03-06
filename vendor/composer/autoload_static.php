<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit843f587a8c79beb6a7dd542b854f5254
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Socketlabs\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Socketlabs\\' => 
        array (
            0 => __DIR__ . '/..' . '/socketlabs/email-delivery/InjectionApi/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit843f587a8c79beb6a7dd542b854f5254::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit843f587a8c79beb6a7dd542b854f5254::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
