<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb9d94f783fc8962463149f0ef2425b20
{
    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'MipsEqLogicTrait' => 
            array (
                0 => __DIR__ . '/..' . '/mips/jeedom-tools/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'MipsEqLogicTrait' => __DIR__ . '/..' . '/mips/jeedom-tools/src/MipsEqLogicTrait.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitb9d94f783fc8962463149f0ef2425b20::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitb9d94f783fc8962463149f0ef2425b20::$classMap;

        }, null, ClassLoader::class);
    }
}
