<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit30de68758f4a5cbcdab6aefd15e18e7b
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Synaptic4UParser\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Synaptic4UParser\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit30de68758f4a5cbcdab6aefd15e18e7b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit30de68758f4a5cbcdab6aefd15e18e7b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit30de68758f4a5cbcdab6aefd15e18e7b::$classMap;

        }, null, ClassLoader::class);
    }
}
