<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfb01d3e1620055ff96db6149cddadab5
{
    public static $classMap = array (
        'Ps_Crossselling' => __DIR__ . '/../..' . '/ps_crossselling.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitfb01d3e1620055ff96db6149cddadab5::$classMap;

        }, null, ClassLoader::class);
    }
}
