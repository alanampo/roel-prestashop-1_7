<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc0384943dbf76ae0f39a0a2a396da012
{
    public static $classMap = array (
        'Ps_Emailsubscription' => __DIR__ . '/../..' . '/ps_emailsubscription.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitc0384943dbf76ae0f39a0a2a396da012::$classMap;

        }, null, ClassLoader::class);
    }
}
