<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita15e42908a030ac7f23292fc850d2973
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
            $loader->prefixesPsr0 = ComposerStaticInita15e42908a030ac7f23292fc850d2973::$prefixesPsr0;
            $loader->classMap = ComposerStaticInita15e42908a030ac7f23292fc850d2973::$classMap;

        }, null, ClassLoader::class);
    }
}
